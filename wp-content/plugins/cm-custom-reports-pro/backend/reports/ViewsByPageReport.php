<?php
new CMCR_Views_By_Page_Report();

class CMCR_Views_By_Page_Report extends CMCR_Report_Base
{

    public function init()
    {
        add_filter('cmcr_graph_tab_controls_output-' . $this->getReportSlug(), array($this, 'addGraphControls'));
        add_filter('cmcr_report_name_filter', array('CMCR_Report_Base', 'addReportNameContent'), 10, 2);
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

    public function addGraphControls($output)
    {
        $postArray = filter_input_array(INPUT_POST);
        $page_query = new WP_Query(array(
            'posts_per_page' => -1,
            'post_type' => 'page',
        ));
        ob_start();
        ?>
        <form method="post" action="">
            <input type="text" name="date_from" value="<?php echo!empty($postArray['date_from']) ? $postArray['date_from'] : '' ?>" class="datepicker" />
            <input type="text" name="date_to" value="<?php echo!empty($postArray['date_to']) ? $postArray['date_to'] : '' ?>" class="datepicker" />
            <select name="page_id" class="cmcr-select-page">
                <option disabled selected>Select a page</option>
            <?php
                if( $page_query->have_posts() )
                {
                    while ($page_query->have_posts())
                    {
                        $page_query->the_post();
                        echo '<option value="'. get_the_ID() . '"'
                            . (isset($postArray['page_id']) ? selected($postArray['page_id'], get_the_ID()) : '') 
                            .'>' . get_the_title() . '</option>';
                    }
                }
                wp_reset_postdata();

            ?>
                
            </select>
            <input type="submit" value="Filter">
        </form>
        <?php
        $graphControlsOutput = ob_get_clean();
        $output = $graphControlsOutput . $output;
        return $output;
    }

    public function getReportSlug()
    {
        return 'views-by-page';
    }

    public function getReportDescription()
    {
        return _('Report about page views');
    }

    public function getReportName()
    {
        return _('Views by page');
    }

    public function getGroups()
    {
        return array('pages' => 'Pages');
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
        if (!empty($postArray['page_id'])) 
        {
            $dateQuery['page_id'] = $postArray['page_id'];
        } 

        return $dateQuery;
    }

    public function getData($dataArgs = array('json' => FALSE))
    {
        static $savedData = array();

        $viewsByPage = array();
        $dataPosts = array();

        $json = !empty($dataArgs['json']) ? $dataArgs['json'] : false;

        if( empty($dataArgs['date_query']) )
        {
            $by_date = self::addDataFilter();
        }
        else
        {
            $by_date = $dataArgs['date_query'];
        }

        if (empty($by_date['page_id'])) {
            return array();
        }

        $argsKey = sha1(maybe_serialize($by_date));
        if( !empty($savedData[$argsKey]) )
        {
            return $savedData[$argsKey];
        }

        /*
         * Get views
         */
        $views = get_post_meta($by_date['page_id'], '_cmcr_views', true);
        foreach ($views as $view_date => $count) {
            if (isset($by_date['before']) && strtotime($by_date['before']) < strtotime($view_date))
                continue;
            if (isset($by_date['after']) && strtotime($by_date['after']) > strtotime($view_date))
                continue;

            $viewsByPage[strtotime($view_date)] = $count;
        }

        ksort($viewsByPage);
        reset($viewsByPage);
        $first_key = key($viewsByPage);
        self::updateDataDateFrom(CM_Custom_Reports_Backend::getDate($first_key)); 

        foreach($viewsByPage as $key => $value)
        {
            $dataPosts[] = array((int) $key * 1000, $value); 
        }

        $result = array(
            array(
                'label' => __('Pages'),
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
