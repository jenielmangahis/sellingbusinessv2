<?php
CMCR_Cron_Module::init();

class CMCR_Cron_Module {

    const MODULE_NAME     = 'Schedule';
    const CRON_EVENT_BASE = 'cmcr_cron_event-';

    public static function init() {
        add_action( 'cron_schedules', array( __CLASS__, 'cronSchedules' ) );

        $reports = CM_Custom_Reports_Backend::loadReports();
        foreach ( $reports as $reportSlug => $reportOptions ) {
            $action = self::CRON_EVENT_BASE . $reportSlug;
            add_action( $action, array( __CLASS__, 'scheduledReport' ) );

            $report = new $reportOptions[ 'class' ];

            $additionalActions = $report->getAdditionalCronActions();
            if ( !empty( $additionalActions ) && is_array( $additionalActions ) ) {
                foreach ( $additionalActions as $additionalAction ) {
                    add_action( $additionalAction, array( __CLASS__, 'scheduledReport' ) );
                }
            }
        }

        /*
         * TODO: Uncomment if need to test schedules
         */
//		add_action( 'shutdown', array( __CLASS__, 'testSchedules' ) );
    }

    /**
     * Function sends emails with reports
     */
    public static function testSchedules() {
        $reports = CM_Custom_Reports_Backend::loadReports();
        foreach ( $reports as $reportSlug => $reportOptions ) {
            $action = self::CRON_EVENT_BASE . $reportSlug;
            do_action( $action );

            $report = new $reportOptions[ 'class' ];

            $additionalActions = $report->getAdditionalCronActions();
            if ( !empty( $additionalActions ) && is_array( $additionalActions ) ) {
                foreach ( $additionalActions as $additionalAction ) {
                    do_action( $additionalAction );
                }
            }
        }
    }

    /**
     * Function sends emails with reports
     */
    public static function cronSchedules( $schedules ) {
        /*
         * add a 'weekly' interval
         */
        $schedules[ 'weekly' ]  = array(
            'interval' => 604800,
            'display'  => __( 'Once Weekly' )
        );
        /*
         * Add a 'monthly' interval
         */
        $schedules[ 'monthly' ] = array(
            'interval' => 2635200,
            'display'  => __( 'Once a month' )
        );
        return $schedules;
    }

    /**
     * Function sends emails with reports
     */
    public static function scheduledReport() {
        $currentFilter = current_filter();

        $pattern        = '/' . self::CRON_EVENT_BASE . '([^|]*)((?:\|\|)(.*)|$)/';
        preg_match( $pattern, $currentFilter, $matches );
        $reportSlug     = isset( $matches[ 1 ] ) ? $matches[ 1 ] : '';
        $additionalData = isset( $matches[ 3 ] ) ? $matches[ 3 ] : '';

        $reportOptions = CM_Custom_Reports_Backend::getReportOptions( $reportSlug );
        if ( empty( $reportOptions ) ) {
            $reportSlug    = str_replace( self::CRON_EVENT_BASE, '', $currentFilter );
            $reportOptions = CM_Custom_Reports_Backend::getReportOptions( $reportSlug );
        }

        $schedulesList = wp_get_schedules();
        if ( empty( $reportOptions[ 'db' ] ) ) {
            return;
        }
        $interval        = $reportOptions[ 'db' ][ 'cmcr_cron_interval' ];
        $intervalDetails = $schedulesList[ $interval ];

        $report          = new $reportOptions[ 'class' ];
        $currentTime     = time();
        $reportStartTime = $currentTime - $intervalDetails[ 'interval' ];

        $dataArgs[ 'date_query' ] = array(
            'before' => date( 'd-m-Y', $currentTime ),
            'after'  => date( 'd-m-Y', $reportStartTime )
        );

        $data[ 'report' ]          = $reportSlug;
        $data[ 'emails' ]          = $reportOptions[ 'db' ][ 'cmcr_cron_emails' ];
        $data[ 'data' ]            = maybe_serialize( $report->getData( $dataArgs ) );
        $data[ 'data_args' ]       = maybe_serialize( $dataArgs );

        if ( apply_filters( 'cmrc_cron_standard_action', 1, $data, $dataArgs ) ) {
            $result = CMCR_Schedule_Log_List_Table::addLogEntry( $data );

            if ( !empty( $result[ 'result' ] ) ) {
                $data[ 'save_result' ] = $result;
                $data[ 'db' ]          = $reportOptions[ 'db' ];
                self::sendEmails( $data );
            }
        }
        $data[ 'additional_data' ] = $additionalData;
        do_action( 'cmcr_cron_custom_action', $data, $dataArgs, $reportSlug );
        do_action( 'cmcr_cron_custom_action_' . $reportSlug, $data, $dataArgs, $reportSlug );
    }

    /**
     * Sends the e-mails with report link
     * @param type $data
     * @return type
     */
    public static function sendEmails( $data ) {
        $reportKey = !empty( $data[ 'save_result' ][ 'id' ] ) ? $data[ 'save_result' ][ 'id' ] : NULL;
        $emails    = !empty( $data[ 'db' ][ 'cmcr_cron_emails' ] ) ? $data[ 'db' ][ 'cmcr_cron_emails' ] : NULL;
        if ( is_null( $reportKey ) || is_null( $emails ) ) {
            return;
        }

        $emailTemplateId = !empty( $data[ 'db' ][ 'cmcr_cron_template' ] ) ? $data[ 'db' ][ 'cmcr_cron_template' ] : 0;
        $emailTemplate   = CMCR_Email_Templates_List_Table::getTemplate( $emailTemplateId );
        /*
         * Error
         */
        if ( !is_object( $emailTemplate ) ) {
            $emailTemplate = CMCR_Email_Templates_List_Table::getDefaultTemplate();
        }

        wp_mail( $emails, $emailTemplate->subject, self::getParsedEmailContent( $emailTemplate->content, $reportKey ) );
    }

    public static function getParsedEmailContent( $content, $reportKey ) {
        $reportUrl     = CMCR_Schedule_Log_List_Table::getReportUrl( $reportKey );
        $parsedContent = str_replace( '[report_link]', $reportUrl, $content );
        return $parsedContent;
    }

    public static function saveOptions() {
        $post = filter_input_array( INPUT_POST );

        if ( !empty( $post ) ) {
            if ( isset( $post[ '_cmcr-nonce' ] ) && wp_verify_nonce( $post[ '_cmcr-nonce' ], 'cmcr-cron-options' ) ) {
                $report       = CM_Custom_Reports_Backend::getCurrentReport();
                $savedOptions = $report->getSavedReportOptions();
                $saveArr      = array();
                foreach ( $post as $key => $value ) {
                    if ( strpos( $key, 'cmcr_cron' ) !== FALSE ) {
                        $saveArr[ $key ] = $value;
                    }
                }

                if ( !empty( $saveArr ) ) {
                    $saveArr = array_merge( $savedOptions, $saveArr );
                    $report->setSavedReportOptions( $saveArr );

                    $possibleIntervals = array_keys( wp_get_schedules() );

                    $newScheduleHour     = $saveArr[ 'cmcr_cron_hour' ];
                    $newScheduleDay      = $saveArr[ 'cmcr_cron_day' ];
                    $newScheduleInterval = $saveArr[ 'cmcr_cron_interval' ];

                    if ( $newScheduleHour !== NULL && $newScheduleInterval !== NULL ) {
                        $reportSlug = $report->getReportSlug();
                        $hook       = self::CRON_EVENT_BASE . $reportSlug;

                        wp_clear_scheduled_hook( $hook );

                        if ( $newScheduleInterval == 'none' ) {
                            return;
                        }

                        if ( empty( $newScheduleDay ) ) {
                            $newScheduleDay = 'monday';
                        }

                        if ( !in_array( $newScheduleInterval, $possibleIntervals ) ) {
                            $newScheduleInterval = 'daily';
                        }

                        /*
                         * eg next friday 19:30
                         */
                        $time = strtotime( 'next ' . $newScheduleDay . ' ' . $newScheduleHour );
                        if ( $time === FALSE ) {
                            $time = current_time( 'timestamp' );
                        }
                        wp_schedule_event( $time, $newScheduleInterval, $hook );

                        if ( !empty( $saveArr[ 'cmcr_cron_run' ] ) ) {
                            do_action( $hook );
                        }
                    }
                }
            }
        }
    }

    public static function displayCron() {
        self::saveOptions();

        $report = CM_Custom_Reports_Backend::getCurrentReport();
        do_action( 'cmcr_before_display_cron', $report );

        ob_start();
        ?>
        <form method="post">
            <?php
            echo wp_nonce_field( 'cmcr-cron-options', '_cmcr-nonce' );
            ?>
            <div class="block">
                <h3><?php _e( self::MODULE_NAME ); ?></h3>
                <table class="floated-form-table form-table">
                    <tr valign="top">
                        <th scope="row">Enabled:</th>
                        <td>
                            <input type="hidden" name="cmcr_cron_on" value="0" />
                            <input type="checkbox" name="cmcr_cron_on" value="1" <?php checked( TRUE, $report->getOption( 'cmcr_cron_on', 0 ) ); ?> />
                        </td>
                        <td colspan="2" class="cmcr_field_help_container">When this option is enabled reports will be sent periodically to given emails.</td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">E-mails:</th>
                        <td><input type="text" placeholder="" name="cmcr_cron_emails" value="<?php echo $report->getOption( 'cmcr_cron_emails', get_option( 'admin_email' ) ); ?>" /></td>
                        <td colspan="2" class="cmcr_field_help_container">Which e-mails should the report be sent to. For multiple use comma delimetered list.</td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Interval:</th>
                        <td>
                            <select name="cmcr_cron_interval" >
                                <?php
                                $types            = wp_get_schedules();
                                $selectedInterval = $report->getOption( 'cmcr_cron_interval', 'none' );
                                ?>
                                <?php foreach ( $types as $typeName => $type ): ?>
                                    <option value="<?php echo $typeName; ?>" <?php selected( $typeName, $selectedInterval ) ?>><?php echo $type[ 'display' ]; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td colspan="2" class="cmcr_field_help_container">Choose how often the reports are being sent. Choose 'none' to disable sending reports.</td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Template:</th>
                        <td>
                            <select name="cmcr_cron_template" >
                                <?php
                                $templates        = CMCR_Email_Templates_List_Table::getTemplates();
                                $selectedTemplate = $report->getOption( 'cmcr_cron_template', '0' );
                                ?>
                                <option value="0" <?php selected( '0', $selectedTemplate ) ?>><?php _e( 'Default' ) ?></option>
                                <?php foreach ( $templates as $templateId => $template ): ?>
                                    <option value="<?php echo $templateId; ?>" <?php selected( $templateId, $selectedTemplate ) ?>><?php echo $template->name; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <a class="button-secondary" target="_blank" href="<?php echo CMCR_Email_Templates_List_Table::getTemplateUrl( $selectedTemplate ) ?>">Edit Template</a>
                        </td>
                        <td colspan="2" class="cmcr_field_help_container">Choose the e-mail template for the report. </td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Hour:</th>
                        <td><input type="time" placeholder="00:00" size="5" name="cmcr_cron_hour" value="<?php echo $report->getOption( 'cmcr_cron_hour' ); ?>" /></td>
                        <td colspan="2" class="cmcr_field_help_container">Choose the hour when the report should be sent. The hour should be properly formatted string eg. 23:00 or 1 AM</td>
                    </tr>
                    <tr valign="top">
                        <th scope="row">Day:</th>
                        <td>
                            <select name="cmcr_cron_day" >
                                <?php
                                $types    = array( 'monday'    => 'Monday',
                                    'tuesday'   => 'Tuesday',
                                    'wednesday' => 'Wednesday',
                                    'thursday'  => 'Thursday',
                                    'friday'    => 'Friday',
                                    'saturday'  => 'Saturday',
                                    'sunday'    => 'Sunday' );
                                $selected = $report->getOption( 'cmcr_cron_day', 'monday' );
                                ?>
                                <?php foreach ( $types as $typeName => $type ): ?>
                                    <option value="<?php echo $typeName; ?>" <?php selected( $typeName, $selected ) ?>><?php echo $type; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td colspan="2" class="cmcr_field_help_container">Choose the day of the week when the report should be sent.</td>
                    </tr>
                    <?php echo $cronTabAdditionalFields = apply_filters( 'cmcr_cron_tab_add_fields', '' ); ?>
                </table>
            </div>
            <input type="submit" class="button button-primary" value="Save" />
        </form>
        <?php
        $output                  = ob_get_clean();
        $cronTabOutput           = apply_filters( 'cmcr_cron_tab_output', $output );
        return $cronTabOutput;
    }

    public static function addCronTab( $tabs ) {
        $tabs[ '2' ] = __( self::MODULE_NAME );
        return $tabs;
    }

}
