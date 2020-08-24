<?php
if( !class_exists('WP_List_Table') )
{
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class CMCR_Schedule_Settings_List_Table extends WP_List_Table
{
    protected $orderBy = 'DESC';
    protected $order = '';
    protected $perpage = '';

    function __construct()
    {
        parent::__construct(array(
            'singular' => 'cmcr_schedule_setting',
            'plural'   => 'cmcr_schedule_settings',
            'ajax'     => true
        ));
    }

    function get_columns()
    {

        $columns = array(
            'cmcr_schedule_setting_report'   => __('Report Name'),
            'cmcr_schedule_setting_on'       => __('Scheduled?'),
            'cmcr_schedule_setting_interval' => __('Interval'),
            'cmcr_schedule_setting_time'     => __('Time'),
            'cmcr_schedule_setting_emails'   => __('E-mails'),
            'cmcr_schedule_setting_template' => __('Template'),
            'cmcr_schedule_setting_show'     => __('Options')
        );

        return $columns;
    }

    public function get_columns_fields()
    {
        $columnsFields = array(
            'cmcr_schedule_setting_report'   => 'report',
            'cmcr_schedule_setting_on'       => 'on',
            'cmcr_schedule_setting_interval' => 'interval',
            'cmcr_schedule_setting_time'     => 'time',
            'cmcr_schedule_setting_emails'   => 'emails',
            'cmcr_schedule_setting_show'     => 'id',
        );

        return $columnsFields;
    }

    public function get_sortable_columns()
    {
        return $sortable = array(
//            'cmcr_schedule_setting_interval' => array('interval', 'DESC'),
//            'cmcr_schedule_setting_time'     => array('time', 'DESC'),
//            'cmcr_schedule_setting_report'   => array('report', 'DESC'),
        );
    }

    function get_column_info()
    {
        return array(
            $this->get_columns(),
            array(),
            $this->get_sortable_columns(),
            $this->get_primary_column_name(),
        );
    }

    function prepare_items()
    {
        global $_wp_column_headers;

        $screen = get_current_screen();

        $this->items = CM_Custom_Reports_Backend::loadReports();
        $totalitems = count($this->items);

        $this->set_pagination_args(array(
            "total_items" => $totalitems,
            "total_pages" => 1,
            "per_page"    => -1,
        ));

        $columns = $this->get_columns();
        if( $screen )
        {
            $_wp_column_headers[$screen->id] = $columns;
        }
    }

    /**
     * Display the rows of records in the table
     * @return string, echo the markup of the rows
     */
    function display_rows()
    {
        /*
         * Get the records registered in the prepare_items method
         */
        $records = $this->items;

        /*
         * Get the columns registered in the get_columns and get_sortable_columns methods
         */
        list( $columns, $hidden ) = $this->get_column_info();

        /*
         * Loop for each record
         */
        if( !empty($records) )
        {
            foreach($records as $item)
            {

                /*
                 * Open the line
                 */
                echo '<tr id="record_' . $item['slug'] . '">';
                foreach($columns as $column_name => $column_display_name)
                {
                    /*
                     * Style attributes for each col
                     */
                    $class = "class='$column_name column-$column_name'";
                    $style = '';
                    if( in_array($column_name, $hidden) )
                    {
                        $style = ' style="display:none;"';
                    }
                    $attributes = $class . $style;

                    /*
                     * Display the cell
                     */
                    switch($column_name)
                    {
                        case "cmcr_schedule_setting_report": echo '<td ' . $attributes . '>' . $item['name'] . '</td>';
                            break;
                        case "cmcr_schedule_setting_on":
                            {
                                $on = (!empty($item['db']['cmcr_cron_on'])) ? CM_Custom_Reports::__('Yes') : CM_Custom_Reports::__('No');
                                echo '<td ' . $attributes . '>' . $on . '</td>';
                            }
                            break;
                        case "cmcr_schedule_setting_interval":
                            {
                                $interval = (!empty($item['db']['cmcr_cron_interval'])) ? $item['db']['cmcr_cron_interval'] : CM_Custom_Reports::__('--');
                                $hook = CMCR_Cron_Module::CRON_EVENT_BASE . $item['slug'];
                                $nextReportTime = wp_next_scheduled($hook);
                                if( $nextReportTime !== false )
                                {
                                    $nextReportDate = date_i18n('d-m-Y H:i:s', $nextReportTime);
                                    $nextReport = sprintf(CM_Custom_Reports::__(' (next - %s)'), $nextReportDate);
                                }
                                else
                                {
                                    $nextReport = '';
                                }
                                echo '<td ' . $attributes . '>' . $interval . $nextReport . '</td>';
                            }
                            break;
                        case "cmcr_schedule_setting_time":
                            {
                                $time = (!empty($item['db']['cmcr_cron_hour'])) ? $item['db']['cmcr_cron_hour'] : CM_Custom_Reports::__('--');
                                echo '<td ' . $attributes . '>' . $time . '</td>';
                            }
                            break;
                        case "cmcr_schedule_setting_emails":
                            {
                                $emails = (!empty($item['db']['cmcr_cron_emails'])) ? $item['db']['cmcr_cron_emails'] : CM_Custom_Reports::__('--');
                                echo '<td ' . $attributes . '>' . $emails . '</td>';
                            }
                            break;
                        case "cmcr_schedule_setting_template":
                            {
                                $template = (!empty($item['db']['cmcr_cron_template'])) ? $item['db']['cmcr_cron_template'] : '0';
                                if( $template !== '0' )
                                {
                                    $template = CMCR_Email_Templates_List_Table::getTemplate($template);
                                    if( is_object($template) )
                                    {
                                        $link = CMCR_Email_Templates_List_Table::getTemplateUrl($template->id);
                                        $template = '<a href="' . $link . '" class="button-secondary" target="_blank">' . $template->name . '</a>';
                                    }
                                }
                                else
                                {
                                    $template = CM_Custom_Reports::__('Default');
                                }
                                echo '<td ' . $attributes . '>' . $template . '</td>';
                            }
                            break;
                        case "cmcr_schedule_setting_show":
                            {
                                $url = esc_url(add_query_arg(array('page' => 'cm-custom-reports', 'cmcr_report' => $item['slug']), admin_url('admin.php')));
                                $url .= '#tabs_2';
                                echo '<td ' . $attributes . '><a class="button-primary" href="' . $url . '" target="_blank">' . CM_Custom_Reports::__('Edit Settings') . '</a></td>';
                            }
                            break;
                    }
                }

                /*
                 * Close the line
                 */
                echo'</tr>';
            }
        }
    }

}