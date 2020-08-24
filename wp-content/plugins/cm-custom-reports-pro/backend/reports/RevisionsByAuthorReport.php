<?php
new CMCR_Revisions_By_Author_Report();

class CMCR_Revisions_By_Author_Report extends CMCR_Report_Base
{

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
            <label>Show author: <?php echo $this->showUsersDropdown() ?></label>
            <input type="submit" value="Filter">
        </form>
        <?php
        $graphControlsOutput = ob_get_clean();
        $output = $graphControlsOutput . $output;
        return $output;
    }

    public function getReportSlug()
    {
        return 'revisions-by-author';
    }

    public function getReportDescription()
    {
        return CM_Custom_Reports::__('Report displays amount of revisions by author');
    }

    public function getReportName()
    {
        return CM_Custom_Reports::__('Revisions by Author');
    }

    public function getGroups()
    {
        return array('revisions' => CM_Custom_Reports::__('Revisions'));
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

    public function showUsersDropdown()
    {
        $postArray = filter_input_array(INPUT_POST);

        $args = array(
            'name'             => 'author',
            'selected'         => ($postArray['author'] ? $postArray['author'] : 'all'),
            'include_selected' => TRUE,
            'echo'             => FALSE,
            'show_option_all'  => 'Default',
        );

        $result = wp_dropdown_users($args);
        $result = preg_replace('/'.preg_quote("<option value='0'>Default</option>",'/').'/', '<option value="all">Default</option>', $result);
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
                'bars'  => array(
                    'show' => TRUE,
                    'barWidth' => 24*60*60*1000,
                    'align' => 'center'
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
        $postArray = filter_input_array(INPUT_POST);
        if (!isset($postArray["author"]) || $postArray["author"] == "all") {
            return 'all';
        }
        $usersQuery = new WP_User_Query(array('number' => 1));
        if( !empty($usersQuery->results[0]) )
        {
            $result = $usersQuery->results[0]->ID;
        }
        else
        {
            $result = NULL;
        }
        

        if( !empty($postArray['author']) )
        {
            $result = $postArray['author'];
        }
        return (int) $result;
    }

    public function getData($dataArgs = array('json' => FALSE))
    {
        static $savedData = array();

        $result = array();
        $postByDate = array();
        $postByAuthor = array();

        $args = array(
            'post_type'      => 'revision',
            'post_status'    => 'inherit',
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
            $dataArgs['author'] = self::addTopFilter();
        }
        else
        {
            $dataArgs['author'] = $dataArgs['author'];
        }

        if( !empty($dataArgs['author']) )
        {
            $args['author'] = $dataArgs['author'];
        }
        if (isset($args['author']) && $args['author'] == 'all') {
            unset($args['author']);
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
         * Posts
         */
        $query = new WP_Query($args);
        $posts = $query->get_posts();
        if( !empty($posts) )
        {
            foreach($posts as $post)
            {
                $time = strtotime($post->post_date);
                $author = $post->post_author;
                $realDate = CM_Custom_Reports_Backend::getDate($time);
                $realTime = strtotime($realDate);

                if( isset($postByDate[$author][$realTime]) )
                {
                    $postByDate[$author][$realTime] ++;
                }
                else
                {
                    $postByDate[$author][$realTime] = 1;
                }

                /*
                 * Sum the posts by author
                 */
                if( isset($postByAuthor[$author]) )
                {
                    $postByAuthor[$author] ++;
                }
                else
                {
                    $postByAuthor[$author] = 1;
                }
            }

            if( !empty($postByDate) )
            {
                foreach($postByDate as $authorId => $postsData)
                {
                    /*
                     * Not one of Top X authors
                     */
                    if( !array_key_exists($authorId, $postByAuthor) )
                    {
                        continue;
                    }

                    $dataPosts = array();
                    $authorName = get_the_author_meta('display_name', $authorId);
                    $authorNicename = get_the_author_meta('user_nicename', $authorId);

                    if( empty($authorName) )
                    {
                        $author = CMCR_Labels::getLocalized('unknown_author') . ' - ' . $postByAuthor[$authorId];
                    }
                    else
                    {
                        $author = $authorNicename . ' (' . $authorName . ' - ' . $postByAuthor[$authorId] . ')';
                    }

                    ksort($postsData);

                    reset($postsData);
                    $first_key = key($postsData);
                    self::updateDataDateFrom(CM_Custom_Reports_Backend::getDate($first_key));

                    foreach($postsData as $key => $value)
                    {
                        $dataPosts[] = array((int) $key * 1000, $value);
                    }

                    $result[] = array(
                        'label' => $author,
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
