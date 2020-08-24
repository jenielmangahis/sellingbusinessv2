<?php
new CMCR_Post_Revisions_Report();

class CMCR_Post_Revisions_Report extends CMCR_Report_Base
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
            <input type="submit" value="Filter">
        </form>
        <?php
        $graphControlsOutput = ob_get_clean();
        $output = $graphControlsOutput . $output;
        return $output;
    }

    public function getReportSlug()
    {
        return 'post-revisions';
    }

    public function getReportDescription()
    {
        return CM_Custom_Reports::__('Report about number of revisions created');
    }

    public function getReportName()
    {
        return CM_Custom_Reports::__('Revisions');
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
            if( !in_array($key, array('bars', 'points')) )
            {
                unset($possibleGraphTypes[$key]);
            }
        }
        return $possibleGraphTypes;
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

    public function getData($dataArgs = array('json' => FALSE))
    {
        static $savedData = array();

        $postByDate = array();
        $dataPosts = array();
        $dataPages = array();

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
            foreach($posts as $page)
            {
                $time = strtotime($page->post_date);
                $realDate = CM_Custom_Reports_Backend::getDate($time);
                $realTime = strtotime($realDate);

                if( isset($postByDate[$realTime]) )
                {
                    $postByDate[$realTime] ++;
                }
                else
                {
                    $postByDate[$realTime] = 1;
                }
            }
            ksort($postByDate);

            reset($postByDate);
            $first_key = key($postByDate);
            self::updateDataDateFrom(CM_Custom_Reports_Backend::getDate($first_key));

            foreach($postByDate as $key => $value)
            {
                $dataPosts[] = array((int) $key * 1000, $value);
            }
        }

        $pageByDate = array();

        $result = array(
            array(
                'label' => __('Revisions'),
                'data'  => $dataPosts
            ),
        );

        if( $json )
        {
            $result = json_encode($result);
        }
        $savedData[$argsKey] = $result;
        return $result;
    }

}
