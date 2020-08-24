<?php
new CMCR_Comments_By_Type_Report();

class CMCR_Comments_By_Type_Report extends CMCR_Report_Base
{
    public static $commentTypes = array();

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
        return 'comments-by-type';
    }

    public function getReportDescription()
    {
        return CM_Custom_Reports::__('Report displays amount of comments by type');
    }

    public function getReportName()
    {
        return CM_Custom_Reports::__('Comments by Type');
    }

    public function getGroups()
    {
        return array('comments' => CM_Custom_Reports::__('Comments'));
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
        $selectedType = (isset($postArray['type']) ? $postArray['type'] : 'all');

        $types = self::$commentTypes;

        ob_start();
        ?>
        <select name="type">
            <option value="all" <?php selected('all', $selectedType); ?> ><?php echo CM_Custom_Reports::__('All') ?></option>
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
        $result = NULL;
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
        $commentByDate = array();
        $commentsByType = array();

        $args = array(
            'posts_per_page' => -1,
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
            $dataArgs['type'] = $dataArgs['comment_type'];
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
        $query = new WP_Comment_Query();
        $comments = $query->query($args);
        if( !empty($comments) )
        {
            foreach($comments as $comment)
            {
                $time = strtotime($comment->comment_date);
                $type = $comment->comment_type;

                if(!in_array($type, self::$commentTypes))
                {
                    self::$commentTypes[] = $type;
                }

                if(isset($dataArgs['type']) && $dataArgs['type'] !== NULL && $dataArgs['type'] !== 'all' && $type !== $dataArgs['type'])
                {
                    continue;
                }
                $realDate = CM_Custom_Reports_Backend::getDate($time);
                $realTime = strtotime($realDate);

                if( isset($commentByDate[$type][$realTime]) )
                {
                    $commentByDate[$type][$realTime] ++;
                }
                else
                {
                    $commentByDate[$type][$realTime] = 1;
                }

                /*
                 * Sum the posts by type
                 */
                if( isset($commentsByType[$type]) )
                {
                    $commentsByType[$type] ++;
                }
                else
                {
                    $commentsByType[$type] = 1;
                }
            }

            if( !empty($commentByDate) )
            {
                foreach($commentByDate as $type => $commentData)
                {
                    /*
                     * No comments of given type
                     */
                    if( !array_key_exists($type, $commentsByType) )
                    {
                        continue;
                    }

                    $dataPosts = array();
                    $commentTypeName = $type;

                    if( empty($commentTypeName) )
                    {
                        $type = CMCR_Labels::getLocalized('empty_comment_type');
                    }

                    ksort($commentData);

                    reset($commentData);
                    $first_key = key($commentData);
                    self::updateDataDateFrom(CM_Custom_Reports_Backend::getDate($first_key));

                    foreach($commentData as $key => $value)
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
