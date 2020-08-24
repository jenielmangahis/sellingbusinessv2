<?php
new CMCR_Posts_By_Type_Report();

class CMCR_Posts_By_Type_Report extends CMCR_Report_Base
{
    public static $postTypes = array();

    public function init()
    {
        add_filter('cmcr_graph_tab_controls_output-' . $this->getReportSlug(), array($this, 'addGraphControls'));
        add_filter('cmcr_report_name_filter', array('CMCR_Report_Base', 'addReportNameContent'), 10, 2);
    }

    public function addGraphControls($output)
    {
        $postArray = filter_input_array(INPUT_POST);
        ob_start();
        ?>
        <form method="post" action="">
            <input type="text" name="date_from" value="<?php echo!empty($postArray['date_from']) ? $postArray['date_from'] : '' ?>" class="datepicker" />
            <input type="text" name="date_to" value="<?php echo!empty($postArray['date_to']) ? $postArray['date_to'] : '' ?>" class="datepicker" />
            <label>Show type: <?php echo $this->showTypesDropdown() ?></label>
            <input type="submit" value="Filter">
        </form>
        <?php
        $graphControlsOutput = ob_get_clean();
        $output = $graphControlsOutput . $output;
        return $output;
    }

    public function getReportSlug()
    {
        return 'posts-by-type';
    }

    public function getReportDescription()
    {
        return CM_Custom_Reports::__('Report displays amount of posts by type');
    }

    public function getReportName()
    {
        return CM_Custom_Reports::__('Posts by Type');
    }

    public function getGroups()
    {
        return array('posts' => CM_Custom_Reports::__('Posts'));
    }

    /**
     * Return the list of possible Graph Types
     * @param type $possibleGraphTypes
     * @return type
     */
    public function getPossibleGraphTypes($possibleGraphTypes)
    {
        foreach($possibleGraphTypes as $key => $value)
        {
            if( !in_array($key, array('bars', 'points', 'pie')) )
            {
                unset($possibleGraphTypes[$key]);
            }
        }
        return $possibleGraphTypes;
    }

    public function showTypesDropdown()
    {
        $postArray = filter_input_array(INPUT_POST);
        $selectedType = (isset($postArray['type']) ? $postArray['type'] : 'any');

        $types = self::$postTypes;

        ob_start();
        ?>
        <select name="type">
            <option value="any" <?php selected('any', $selectedType); ?> ><?php echo CM_Custom_Reports::__('All') ?></option>
            <?php foreach($types as $value) : ?>
                <?php
                $label = !empty($value) ? $value : CMCR_Labels::getLocalized('empty_comment_type');
                ?>
                <option value="<?php echo $value; ?>" <?php selected($value, $selectedType); ?> ><?php echo $label; ?></option>
            <?php endforeach; ?>
        </select>
        <?php
        $result = ob_get_clean();
        return $result;
    }

    public function getReportExtraOptions()
    {
        $graphOptions = array(
            'axisLabels' => array(
                'show' => true
            ),
            'xaxis'      => array(
                'axisLabel'   => 'Day',
                'mode'        => 'time',
                'timeformat'  => CM_Custom_Reports_Backend::getDateFormat('flot'),
                'minTickSize' => array(1, "day")
            ),
            'yaxis'      => array(
                'axisLabel'    => 'Amount',
                'min'          => 0,
                'minTickSize'  => 1,
                'tickDecimals' => 0
            ),
            'series'     => array(
                'bars' => array(
                    'show'     => TRUE,
                    'barWidth' => 24 * 60 * 60 * 1000,
                    'align'    => 'center'
                )
            ),
            'grid'       => array(
                'hoverable'     => TRUE,
                'clickable'     => TRUE,
                'autoHighlight' => TRUE,
            )
        );

        $reportOptions = array(
            'cron'             => TRUE,
            'graph'            => $graphOptions,
            'graph_datepicker' => array(
                'showOn'      => 'both',
                'showAnim'    => 'fadeIn',
                'dateFormat'  => CM_Custom_Reports_Backend::getDateFormat('datepicker'),
                'buttonImage' => CM_Custom_Reports_Backend::$imagesPath . 'calendar.gif',
            )
        );

        return $reportOptions;
    }

    public static function addDataFilter()
    {
        $dateQuery = array();
        $postArray = filter_input_array(INPUT_POST);

        if( !empty($postArray['date_from']) )
        {
            $dateQuery['after'] = $postArray['date_from'];
        }
        if( !empty($postArray['date_to']) )
        {
            $dateQuery['before'] = $postArray['date_to'];
        }
        else
        {
            $dateQuery['before'] = CM_Custom_Reports_Backend::getDate();
        }

        return $dateQuery;
    }

    public static function addTopFilter()
    {
        $result = 'any';
        $postArray = filter_input_array(INPUT_POST);
        if( isset($postArray['type']) )
        {
            $result = $postArray['type'];
        }
        return $result;
    }

    public function getData($dataArgs = array('json' => FALSE))
    {
        static $savedData = array();

        $result = array();
        $postsByDate = array();
        $postsByType = array();

        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'any',
            'fields' => 'ids',
        );

        $json = !empty($dataArgs['json']) ? $dataArgs['json'] : false;

        if( empty($dataArgs['date_query']) )
        {
            $args['date_query'] = self::addDataFilter();
        }
        else
        {
            $args['date_query'] = $dataArgs['date_query'];
        }

        /*
         * Additional filter
         */
        if( empty($dataArgs['author']) )
        {
            $filterResult = self::addTopFilter();

            if( $filterResult !== NULL )
            {
                $dataArgs['type'] = $filterResult;
            }
        }
        else
        {
            $dataArgs['type'] = $dataArgs['post_type'];
        }

        if( !empty($dataArgs['author']) )
        {
            $args['user_id'] = $dataArgs['author'];
        }

        if( !empty($args['date_query']) )
        {
            $args['date_query']['inclusive'] = true;
            $args[ 'date_query' ][ 'before' ] .= '23:59:59';
        }

        $argsKey = sha1(maybe_serialize($args));
        if( !empty($savedData[$argsKey]) )
        {
            return $savedData[$argsKey];
        }

        /*
         * Comments
         */
        $query = new WP_Query($args);
        $posts = $query->get_posts();
        if( !empty($posts) )
        {
            foreach($posts as $postId)
            {
				$post = get_post($postId);
                $time = strtotime($post->post_date);
                $type = $post->post_type;

                if(!in_array($type, self::$postTypes))
                {
                    self::$postTypes[] = $type;
                }

                if(isset($dataArgs['type']) && $dataArgs['type'] !== NULL && $dataArgs['type'] !== 'any' && $type !== $dataArgs['type'])
                {
                    continue;
                }
                $realDate = CM_Custom_Reports_Backend::getDate($time);
                $realTime = strtotime($realDate);

                if( isset($postsByDate[$type][$realTime]) )
                {
                    $postsByDate[$type][$realTime] ++;
                }
                else
                {
                    $postsByDate[$type][$realTime] = 1;
                }

                /*
                 * Sum the posts by type
                 */
                if( isset($postsByType[$type]) )
                {
                    $postsByType[$type] ++;
                }
                else
                {
                    $postsByType[$type] = 1;
                }
            }

            if( !empty($postsByDate) )
            {
                foreach($postsByDate as $type => $postData)
                {
                    /*
                     * No comments of given type
                     */
                    if( !array_key_exists($type, $postsByType) )
                    {
                        continue;
                    }

                    $dataPosts = array();
                    $postTypeName = $type;

                    if( empty($postTypeName) )
                    {
                        $type = CMCR_Labels::getLocalized('empty_post_type');
                    }

                    ksort($postData);

                    reset($postData);
                    $first_key = key($postData);
                    self::updateDataDateFrom(CM_Custom_Reports_Backend::getDate($first_key));

                    foreach($postData as $key => $value)
                    {
                        $dataPosts[] = array((int) $key * 1000, $value);
                    }

                    $result[] = array(
                        'label' => $type,
                        'data'  => $dataPosts
                    );
                }
            }
        }

        if( $json )
        {
            $result = json_encode($result);
        }
        $savedData[$argsKey] = $result;
        return $result;
    }

}
