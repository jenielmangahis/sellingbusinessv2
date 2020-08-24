<?php
/*
Plugin Name: CM Ad Changer - Server Pro
Plugin URI: https://www.cminds.com/store/adchanger/
Description: Ad Changer Pro Server. Manage, Track and Report Advertising Campaigns Across Sites
Author: CreativeMindsSolutions
Author URI: https://www.cminds.com/
Version: 1.9.0
*/

/**
 * CM Ad Changer
 *
 * @author CreativeMinds (http://ad-changer.cminds.com)
 * @copyright Copyright (c) 2013, CreativeMinds
 */
define( 'ACS_PLUGIN_PATH', WP_PLUGIN_DIR . '/' . basename( dirname( __FILE__ ) ) );
define( 'ACS_PLUGIN_URL', plugins_url( '', __FILE__ ) );
define( 'ACS_PLUGIN_FILE', __FILE__ );

/**
 * Define Plugin prefix
 *
 * @since 1.2.5
 */
if ( !defined( 'CMAC_PREFIX' ) ) {
    define( 'CMAC_PREFIX', 'cmac_' );
}

/**
 * Define Plugin Version
 *
 * @since 1.0
 */
if ( !defined( 'CMAC_VERSION' ) ) {
    define( 'CMAC_VERSION', '1.8.15' );
}

/**
 * Define Plugin Directory
 *
 * @since 1.0
 */
if ( !defined( 'CMAC_PLUGIN_DIR' ) ) {
    define( 'CMAC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

/**
 * Define Plugin URL
 *
 * @since 1.0
 */
if ( !defined( 'CMAC_PLUGIN_URL' ) ) {
    define( 'CMAC_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}

/**
 * Define Plugin File Name
 *
 * @since 1.0
 */
if ( !defined( 'CMAC_PLUGIN_FILE' ) ) {
    define( 'CMAC_PLUGIN_FILE', __FILE__ );
}

/**
 * Define Plugin Slug name
 *
 * @since 1.0
 */
if ( !defined( 'CMAC_SLUG_NAME' ) ) {
    define( 'CMAC_SLUG_NAME', 'cm-ad-changer' );
}

/**
 * Define Plugin name
 *
 * @since 1.0
 */
if ( !defined( 'CMAC_NAME' ) ) {
    define( 'CMAC_NAME', 'CM Ad Changer' );
}

/**
 * Define Plugin name
 *
 * @since 1.0
 */
if ( !defined( 'CMAC_LICENSE_NAME' ) ) {
    define( 'CMAC_LICENSE_NAME', 'Ad Changer' );
}

/**
 * Define Plugin basename
 *
 * @since 1.0
 */
if ( !defined( 'CMAC_PLUGIN' ) ) {
    define( 'CMAC_PLUGIN', plugin_basename( __FILE__ ) );
}

/**
 * Define Plugin release notes url
 *
 * @since 1.0
 */
if ( !defined( 'CMAC_RELEASE_NOTES' ) ) {
    define( 'CMAC_RELEASE_NOTES', 'https://ad-changer.cminds.com/release-notes/' );
}

require_once ACS_PLUGIN_PATH . '/config.php';
require_once ACS_PLUGIN_PATH . '/functions.php';
require_once ACS_PLUGIN_PATH . '/classes/ac_advert.php';
require_once ACS_PLUGIN_PATH . '/classes/ac_data.php';
require_once ACS_PLUGIN_PATH . '/classes/ac_requests.php';
require_once ACS_PLUGIN_PATH . '/classes/ac_client.php';
require_once ACS_PLUGIN_PATH . '/package/cminds-pro.php';

global $cmac_isLicenseOk;
//$licensingApi	 = new CMAC_Cminds_Licensing_API( 'CM Ad Changer Pro', 'ac_server', 'Ad Changer', ACS_PLUGIN_FILE, array( 'release-notes' => 'http://ad-changer.cminds.com/release-notes/' ), '', array( 'CM Ad Changer Pro', 'CM Ad Changer Pro Special' ) );
//$isLicenseOk	 = $licensingApi->isLicenseOk();
/*
 * Single banner widget
 */
if ( !class_exists( 'CMAC_AdChangerWidget' ) ) {
    require_once ACS_PLUGIN_PATH . '/widget.php';
}
/*
 * Banner group widget
 */
if ( !class_exists( 'CMACGWidget' ) ) {
    require_once ACS_PLUGIN_PATH . '/groupswidget.php';
}

/*
 * Load external libraries
 */
if ( !class_exists( 'Image' ) ) {
    require_once ACS_PLUGIN_PATH . '/libs/image.php';
}

if ( !class_exists( 'ip2location_lite' ) ) {
    require_once ACS_PLUGIN_PATH . '/libs/ip2locationlite.php';
}

register_activation_hook( __FILE__, 'ac_activate' );
register_deactivation_hook( __FILE__, 'ac_deactivate' );

/*
 * Activate the shortcode
 */

if ( $cmac_isLicenseOk ) {

    if ( !shortcode_exists( 'cm_ad_changer' ) ) {
        add_shortcode( 'cm_ad_changer', array( 'AC_Client', 'banners' ) );
    }

    add_action( 'wp_ajax_nopriv_acc_get_banners', array( 'AC_Client', 'get_banners' ) );
    add_action( 'wp_ajax_acc_get_banners', array( 'AC_Client', 'get_banners' ) );
    add_action( 'wp_ajax_acc_trigger_click_event', array( 'AC_Client', 'trigger_click_event' ) );
    add_action( 'wp_ajax_nopriv_acc_trigger_click_event', array( 'AC_Client', 'trigger_click_event' ) );
    add_action( 'wp_ajax_acc_trigger_impression_event', array( 'AC_Client', 'trigger_impression_event' ) );
    add_action( 'wp_ajax_nopriv_acc_trigger_impression_event', array( 'AC_Client', 'trigger_impression_event' ) );
}

add_action( 'admin_init', 'cmac_init' );

function cmac_init() {

    add_action('cmac_campaign_errors', array( 'AC_Advert', 'validateBanners' ), 10, 2 );
    wp_register_style( 'ac_adChangerStylesheet', plugins_url( 'assets/css/style.css', __FILE__ ) );
    wp_register_style( 'ac_datePickerUIStylesheet', plugins_url( 'assets/js/datepicker/smoothness.datepick.css', __FILE__ ) );
    wp_register_style( 'ac_jqueryUIStylesheet', plugins_url( 'assets/css/jquery-ui/smoothness/jquery-ui-1.10.3.custom.min.css', __FILE__ ) );
    wp_register_style( 'ac_speechBubblesStylesheet', plugins_url( 'assets/js/speechbubbles/speechbubbles.css', __FILE__ ) );

    cmac_enable_cors_for_ajax();
}

function cmac_enable_cors_for_ajax() {
    $action = filter_input( INPUT_GET, 'action' );
    if ( $action && 'acc_get_banners' === $action ) {
        header( 'Access-Control-Allow-Origin: *' );
        header( 'Access-Control-Allow-Credentials: true' );
        header( 'Access-Control-Allow-Methods: POST, GET' );
        header( 'Cache-Control: no-cache' );
        header( 'Pragma: no-cache' );
    }
}

add_action( 'admin_notices', 'cmac_display_notice' );

function cmac_display_notice() {
    $serverActive = get_option( 'acs_active', 1 );
    if ( isset( $_GET[ 'page' ] ) && $_GET[ 'page' ] == 'ac_server' && !empty( $_POST ) ) {
        $serverActive = ((isset( $_POST[ 'acs_active' ] )) ? ($_POST[ 'acs_active' ]) : (null));
    }

    if ( !$serverActive ) {
        $message = sprintf( __( '%s has detected that the Server is not Active.' ), 'CM Ad Changer Pro' );
        cminds_show_message( $message, true );
    }
    /*
     * fetch all campaigns with type select and with unselected banners
     */
    $unselectedBanners = AC_Data::get_select_campaigns_with_no_banner_selected();
    if ( !empty( $unselectedBanners ) ) {
        $noticeString = translate( 'Following campaigns has Display Method "Selected Banner" and does not have banner selected:' );
        foreach ( $unselectedBanners as $oneCampaign ) {
            $noticeString .= '<br>';
            $noticeString .= $oneCampaign->campaign_id . ' - ' . $oneCampaign->title . '&nbsp;&nbsp;&nbsp;';
            $noticeString .= '<a href="' . $url = admin_url( 'admin.php?page=ac_server_campaigns&action=edit&saved=1&campaign_id=' . $oneCampaign->campaign_id ) . '">' . translate( 'go to settings' ) . '</a>';
        }
        cminds_show_message( $noticeString, true );
    }
}

add_action( 'admin_menu', 'cmac_menu' );

function cmac_menu() {
    global $submenu;
    $current_user = wp_get_current_user();
    if ( !user_can( $current_user, 'manage_options' ) ) {
        return;
    }
    $settings_page     = add_menu_page( 'Ad Changer ', 'Ad Changer', 'manage_options', 'ac_server', 'ac_load_page', plugin_dir_url( __FILE__ ) . '/assets/images/cm-ad-changer-icon.png' );
    $campaigns_subpage = add_submenu_page( 'ac_server', 'Campaigns', 'Campaigns', 'manage_options', 'ac_server_campaigns', 'ac_load_page' );
    $groups_subpage    = add_submenu_page( 'ac_server', 'Campaign Groups', 'Campaign Groups', 'manage_options', 'ac_server_groups', 'ac_load_page' );
    $history_subpage   = add_submenu_page( 'ac_server', 'Statistics', 'Statistics', 'manage_options', 'ac_server_statistics', 'ac_load_page' );
    $testing_subpage   = add_submenu_page( 'ac_server', 'Testing', 'Testing', 'manage_options', 'ac_server_testing', 'ac_load_page' );

    add_action( 'admin_print_styles-' . $settings_page, 'ac_admin_styles' );
    add_action( 'admin_print_styles-' . $campaigns_subpage, 'ac_admin_styles' );
    add_action( 'admin_print_styles-' . $groups_subpage, 'ac_admin_styles' );
    add_action( 'admin_print_styles-' . $history_subpage, 'ac_admin_styles' );
    add_action( 'admin_print_styles-' . $testing_subpage, 'ac_admin_styles' );

    $submenu[ 'ac_server' ][ 0 ][ 0 ] = 'Settings';
}

/**
 * Load the styles and scripts on the admin side
 */
function ac_admin_styles() {
    wp_enqueue_style( 'ac_adChangerStylesheet' );
    wp_enqueue_style( 'ac_datePickerUIStylesheet' );
    wp_enqueue_style( 'ac_jqueryUIStylesheet' );

    if ( $_GET[ 'page' ] == 'ac_server_campaigns' ) {
        wp_enqueue_style( 'ac_speechBubblesStylesheet' );
        wp_enqueue_script( 'jquery-ui-datepicker' );

        wp_enqueue_script( 'plupload-full-js', plugins_url( 'assets/js/plupload/plupload.full.js', __FILE__ ), array(), '1.0.0', false );
        wp_enqueue_script( 'plupload-queue-js', plugins_url( 'assets/js/plupload/jquery.plupload.queue.js', __FILE__ ), array(), '1.0.0', false );
//        wp_enqueue_script('datepicker', plugins_url('assets/js/datepicker/jquery.datepick.js', __FILE__), array('jquery'), '1.0.0', false);
        wp_enqueue_script( 'speechBubbles', plugins_url( 'assets/js/speechbubbles/speechbubbles.js', __FILE__ ), array(), '1.0.0', false );
        wp_enqueue_script( 'cm-ad-changer-scripts', plugins_url( 'assets/js/scripts.js', __FILE__ ), array(), '1.0.0', false );
        /*
         * Metaboxes
         */
        wp_enqueue_style( 'cm-adchanger-wpalchemy-metabox', plugins_url( 'assets/css/meta.css', __FILE__ ) );

//        wp_enqueue_style('cmac-ad-designer', plugins_url('assets/css/addesigner.css', __FILE__));
//        wp_enqueue_script('jquery-ui-slider');
//        wp_enqueue_script('cmac-ad-designer-bootstrap', plugins_url('assets/js/bootstrap.min.js', __FILE__));
//        wp_enqueue_script('cmac-ad-designer-bootstrap-colorpicker', plugins_url('assets/js/bootstrap-colorpicker.min.js', __FILE__), array('cmac-ad-designer-bootstrap'));
//        wp_enqueue_script('cmac-ad-designer-js', plugins_url('assets/js/addesigner.js', __FILE__), array('cmac-ad-designer-bootstrap-colorpicker'));


        /*
         * make sure we enqueue some scripts just in case ( only needed for repeating metaboxes )
         */
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-widget' );
        wp_enqueue_script( 'jquery-ui-mouse' );
        wp_enqueue_script( 'jquery-ui-sortable' );
        wp_enqueue_script( 'jquery-ui-dialog' );

        wp_enqueue_script( 'word-count' );

        wp_enqueue_script( 'editor' );

        wp_enqueue_script( 'quicktags' );
        wp_enqueue_style( 'buttons' );

        wp_enqueue_script( 'wplink' );

        wp_enqueue_script( 'wp-fullscreen' );
        wp_enqueue_script( 'media-upload' );

        /*
         *  special script for dealing with repeating textareas- needs to run AFTER all the tinyMCE init scripts, so make 'editor' a requirement
         */
        wp_enqueue_script( 'cmac-textareas', plugins_url( 'assets/js/cmac-textareas.js', __FILE__ ), array( 'jquery', 'word-count', 'editor', 'quicktags', 'wplink', 'media-upload', ), '1.1', true );

        $scriptData[ 'pluginurl' ] = plugins_url( '', __FILE__ );
        wp_localize_script( 'cm-ad-changer-scripts', 'cmac_data', $scriptData );
    }

    if ( $_GET[ 'page' ] == 'ac_server_groups' ) {
        wp_enqueue_style( 'ac_speechBubblesStylesheet' );
//        wp_enqueue_script('datepicker', plugins_url('assets/js/datepicker/jquery.datepick.js', __FILE__), array('jquery'), '1.0.0', false);
        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_script( 'speechBubbles', plugins_url( 'assets/js/speechbubbles/speechbubbles.js', __FILE__ ), array(), '1.0.0', false );
        wp_enqueue_script( 'cm-ad-changer-scripts', plugins_url( 'assets/js/scripts.js', __FILE__ ), array(), '1.0.0', false );
        $scriptData[ 'pluginurl' ] = plugins_url( '', __FILE__ );
        wp_localize_script( 'cm-ad-changer-scripts', 'cmac_data', $scriptData );
    }

    if ( $_GET[ 'page' ] == 'ac_server_testing' ) {
        wp_enqueue_script( 'tcycle', plugins_url( 'assets/js/jquery.tcycle.js', __FILE__ ), array( 'jquery' ) );
        wp_enqueue_script( 'ouibounce', plugins_url( 'assets/js/ouibounce.js', __FILE__ ), array(), '1.0.0', false );
        wp_enqueue_style( 'cm-ad-ouibounce', plugins_url( 'assets/css/ouibounce.css', __FILE__ ) );
        wp_enqueue_script( 'jquery-ui-datepicker' );
    }

    if ( $_GET[ 'page' ] == 'ac_server_statistics' ) {
        wp_enqueue_style( 'cm-ad-preloader', plugins_url( 'assets/css/preloader.css', __FILE__ ) );
        wp_enqueue_script( 'flot', plugins_url( 'assets/js/flot/jquery.flot.js', __FILE__ ), array(), '1.0.0', false );
        wp_enqueue_script( 'flotCategories', plugins_url( 'assets/js/flot/jquery.flot.categories.js', __FILE__ ), array(), '1.0.0', false );
        wp_enqueue_script( 'jquery-ui-datepicker' );
    }

	if ( $_GET[ 'page' ] == 'ac_server' ) {
		 wp_enqueue_script( 'jquery-ui-datepicker' );
	}

    $int_version = (int) str_replace( '.', '', get_bloginfo( 'version' ) );
    if ( $int_version < 100 ) {
        $int_version *= 10; // will be 340 or 341 or 350 etc
    }

    if ( $int_version > 320 ) {
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-widget' );
        wp_enqueue_script( 'jqueryUIPosition' );
        wp_enqueue_script( 'jquery-ui-tabs' );
        wp_enqueue_script( 'jquery-ui-button' );
    } else {
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script( 'jquery-ui-widget' );
        wp_enqueue_script( 'jqueryUIPosition' );
        wp_enqueue_script( 'jquery-ui-tabs' );
        wp_enqueue_script( 'jquery-ui-button' );
    }

    if ( $int_version >= 350 ) {
        wp_enqueue_script( 'jquery-ui-spinner' );
        wp_enqueue_script( 'jquery-ui-tooltip' );
        wp_enqueue_script( 'jquery-ui-datepicker	' );
    }

    wp_enqueue_script( 'mouseWheel', plugins_url( 'assets/js/jquery-ui/jquery.mousewheel.js', __FILE__ ), array( 'jquery' ), '1.0.0', false );

    if ( $int_version < 350 ) {
        wp_enqueue_script( 'jQueryMissingUI', plugins_url( 'assets/js/jquery-ui/missing_ui.js', __FILE__ ), array(), '1.0.0', true );
    }
}

/**
 * pages dispatcher
 * @param String   $ac_page  Page slug
 */
function ac_load_page( $ac_page = null ) {
    global $label_descriptions;

    if ( empty( $ac_page ) ) {
        $ac_page = $_GET[ 'page' ];
    }

    $plugin_data = get_plugin_data( ACS_PLUGIN_FILE );

    switch ( $ac_page ) {
        case 'ac_server':
            if ( !empty( $_POST ) ) {
                $data = AC_Data::ac_handle_settings_post( $_POST );
                if ( !empty( $data ) ) {
                    $fields_data = $data[ 'fields_data' ];
                    if ( isset( $data[ 'errors' ] ) && !empty( $data[ 'errors' ] ) )
                        $errors      = $data[ 'errors' ];
                }else {
                    $fields_data[ 'acs_active' ]                    = ((isset( $_POST[ 'acs_active' ] )) ? ($_POST[ 'acs_active' ]) : (null));
                    $fields_data[ 'acs_disable_history_table' ]     = ((isset( $_POST[ 'acs_disable_history_table' ] )) ? ($_POST[ 'acs_disable_history_table' ]) : (null));
                    $fields_data[ 'acs_notification_email_tpl' ]    = stripslashes( $_POST[ 'acs_notification_email_tpl' ] );
                    $fields_data[ 'acs_inject_scripts' ]            = ((isset( $_POST[ 'acs_inject_scripts' ] )) ? ($_POST[ 'acs_inject_scripts' ]) : (''));
                    $fields_data[ 'acs_div_wrapper' ]               = ((isset( $_POST[ 'acs_div_wrapper' ] )) ? ($_POST[ 'acs_div_wrapper' ]) : (''));
                    $fields_data[ 'acs_class_name' ]                = ((isset( $_POST[ 'acs_class_name' ] )) ? ($_POST[ 'acs_class_name' ]) : (''));
                    $fields_data[ 'acs_custom_css' ]                = $_POST[ 'acs_custom_css' ];
                    $fields_data[ 'acs_geolocation_api_key' ]       = $_POST[ 'acs_geolocation_api_key' ];
                    $fields_data[ 'acs_use_banner_variations' ]     = $_POST[ 'acs_use_banner_variations' ];
                    $fields_data[ 'acs_banner_area' ]               = $_POST[ 'acs_banner_area' ];
                    $fields_data[ 'acs_resize_banner' ]             = $_POST[ 'acs_resize_banner' ];
                    $fields_data[ 'acs_slideshow_effect' ]          = $_POST[ 'acs_slideshow_effect' ];
                    $fields_data[ 'acs_slideshow_interval' ]        = $_POST[ 'acs_slideshow_interval' ];
                    $fields_data[ 'acs_slideshow_transition_time' ] = $_POST[ 'acs_slideshow_transition_time' ];
                    $fields_data[ 'acs_script_in_footer' ]          = ((isset( $_POST[ 'acs_script_in_footer' ] )) ? ($_POST[ 'acs_script_in_footer' ]) : (0));
                    $fields_data[ 'acs_auto_deactivate_campaigns' ] = isset( $_POST[ 'acs_auto_deactivate_campaigns' ] ) ? $_POST[ 'acs_auto_deactivate_campaigns' ] : 0;
                    $success                                        = 'Settings were successfully stored!';
                }
            } else {
                $fields_data[ 'acs_active' ]                    = get_option( 'acs_active', 1 );
                $fields_data[ 'acs_disable_history_table' ]     = get_option( 'acs_disable_history_table', null );
                $fields_data[ 'acs_notification_email_tpl' ]    = get_option( 'acs_notification_email_tpl', "Hi,\n\nCampaign '%campaign_name%'(Campaign ID: %campaign_id%) stopped working\nReason: %reason%\n\nBest Regards,\nCampaign Manager" );
                $fields_data[ 'acs_inject_scripts' ]            = get_option( 'acs_inject_scripts', '1' );
                $fields_data[ 'acs_div_wrapper' ]               = get_option( 'acs_div_wrapper', '0' );
                $fields_data[ 'acs_class_name' ]                = get_option( 'acs_class_name', '' );
                $fields_data[ 'acs_geolocation_api_key' ]       = get_option( 'acs_geolocation_api_key', '' );
                $fields_data[ 'acs_use_banner_variations' ]     = get_option( 'acs_use_banner_variations', '1' );
                $fields_data[ 'acs_banner_area' ]               = get_option( 'acs_banner_area', 'screen' );
                $fields_data[ 'acs_resize_banner' ]             = get_option( 'acs_resize_banner', '1' );
                $fields_data[ 'acs_slideshow_effect' ]          = get_option( 'acs_slideshow_effect', 'fade' );
                $fields_data[ 'acs_slideshow_interval' ]        = get_option( 'acs_slideshow_interval', '5000' );
                $fields_data[ 'acs_slideshow_transition_time' ] = get_option( 'acs_slideshow_transition_time', '400' );
                $fields_data[ 'acs_custom_css' ]                = get_option( 'acs_custom_css', '' );
                $fields_data[ 'acs_script_in_footer' ]          = get_option( 'acs_script_in_footer', '0' );
                $fields_data[ 'acs_auto_deactivate_campaigns' ] = get_option( 'acs_auto_deactivate_campaigns', '0' );
            }
            require_once ACS_PLUGIN_PATH . '/views/settings.php';
            break;

        case 'ac_server_campaigns':

            global $wpdb;
            $data        = array();
            $fields_data = array();
            $success     = null;

            if ( !empty( $_POST ) ) {
                $data = AC_Data::ac_handle_campaigns_post( $_POST );
                if ( !empty( $data ) ) {
                    $fields_data = $data[ 'fields_data' ];
                    if ( isset( $data[ 'errors' ] ) && !empty( $data[ 'errors' ] ) ) {
                        $errors = $data[ 'errors' ];
                    }
                } else {
//                    $fields_data['category_ids'] = isset($_POST['category_ids']) ? $_POST['category_ids'] : NULL;
//                    $fields_data['category_title'] = isset($_POST['category_title']) ? $_POST['category_title'] : NULL;
//                    $fields_data['title'] = isset($_POST['title']) ? $_POST['title'] : NULL;

                    $fields_data = AC_Data::get_campaign( $_GET[ 'campaign_id' ] );
                    $success     = 'Campaign was successfully stored!';
                }
            } else {
                if ( isset( $_GET[ 'action' ] ) ) {
                    if ( isset( $_GET[ 'campaign_id' ] ) && is_numeric( $_GET[ 'campaign_id' ] ) ) {
                        switch ( $_GET[ 'action' ] ) {
                            case 'edit':

                                $fields_data = AC_Data::get_campaign( $_GET[ 'campaign_id' ], true );

                                if ( isset( $_GET[ 'saved' ] ) && $_GET[ 'saved' ] == '1' ) {
                                    $success = 'Campaign was successfully stored!';
                                }
                                break;
                            case 'delete':
                                AC_Data::ac_remove_campaign( $_GET[ 'campaign_id' ] );
                                $success = 'Campaign was removed from database!';
                                break;
                            case 'duplicate':
                                $res     = AC_Data::duplicate_campaign( $_GET[ 'campaign_id' ] );

                                if ( $res === true ) {
                                    $success = 'Campaign copy created!';
                                } else {
                                    $errors = array( 0 => $res[ 'error' ] );
                                }
                                break;
                        }
                    } else {
                        $errors = array( 0 => 'Campaign ID not given' );
                    }
                }
            }

            $campaigns       = AC_Data::get_campaigns();
            $advertisers     = AC_Data::get_advertisers();
            $campaign_groups = AC_Data::get_groups();

            if ( $advertisers ) {
                $fields_data[ 'advertisers' ] = AC_Data::get_advertisers();
            }
            require_once ACS_PLUGIN_PATH . '/views/campaigns.php';
            break;

        case 'ac_server_groups':

            global $wpdb;
            $data        = array();
            $fields_data = array();
            $success     = null;
            $addCampain  = false;
            if ( !empty( $_POST ) || (!empty( $_GET[ 'group_name' ] ) && !empty( $_GET[ 'group_order' ] )) ) {
                //ugly workaround (real shame)
                if ( !empty( $_GET[ 'group_name' ] ) && !empty( $_GET[ 'group_order' ] ) ) {
                    $_POST[ 'description' ] = $_GET[ 'group_name' ];
                    $_POST[ 'group_order' ] = $_GET[ 'group_order' ];
                    $addCampain             = true;
                }
                $data = AC_Data::ac_handle_groups_post( $_POST );
                if ( !empty( $data[ 'errors' ] ) ) {
                    $fields_data = $data[ 'fields_data' ];
                    if ( isset( $data[ 'errors' ] ) && !empty( $data[ 'errors' ] ) ) {
                        $errors = $data[ 'errors' ];
                    }
                } else {
                    $success = 'Group was successfully stored!';
                    //ugly workaround continues
                    if ( $addCampain ) {
                        AC_Data::add_campaign_to_group( $_GET[ 'campaign_id' ], $data[ 'group_id' ] );
                        $success .= '<br>Campaign with ID:' . $campaignId . ' added to the group!';
                    }
                }
            } else {
                if ( isset( $_GET[ 'action' ] ) ) {
                    if ( isset( $_GET[ 'group_id' ] ) && is_numeric( $_GET[ 'group_id' ] ) ) {
                        switch ( $_GET[ 'action' ] ) {
                            case 'add_campaign':
                            case 'remove_campaign':
                                $campaignId = isset( $_GET[ 'campaign_id' ] ) ? $_GET[ 'campaign_id' ] : '';
                                if ( !empty( $campaignId ) ) {
                                    if ( $_GET[ 'action' ] == 'add_campaign' ) {
                                        AC_Data::add_campaign_to_group( $campaignId, $_GET[ 'group_id' ] );
                                        $success = 'Campaign with ID:' . $campaignId . ' added to the group!';
                                    } else {
                                        AC_Data::remove_campaign_from_group( $campaignId );
                                        $success = 'Campaign with ID:' . $campaignId . ' removed from the group!';
                                    }
                                }
                            case 'edit':
                                $fields_data        = AC_Data::get_group( $_GET[ 'group_id' ] );
                                $campaignsForSelect = isset( $fields_data[ 'group_id' ] ) ? AC_Data::get_non_group_campaigns( $fields_data[ 'group_id' ] ) : AC_Data::get_campaigns();
                                $groupCampaigns     = AC_Data::get_group_campaigns( $_GET[ 'group_id' ] );
                                break;
                            case 'delete':
                                AC_Data::ac_remove_group( $_GET[ 'group_id' ] );
                                $success            = 'Group was removed from database!';
                                break;
                            case 'duplicate':
                                $res                = AC_Data::duplicate_group( $_GET[ 'group_id' ] );

                                if ( $res === true ) {
                                    $success = 'Group copy created!';
                                } else {
                                    $errors = array( 0 => $res[ 'error' ] );
                                }
                                break;
                        }
                    } else {
                        $errors = array( 0 => 'Group ID not given' );
                    }
                }
            }
            $campaigns = AC_Data::get_campaigns();
            $groups    = AC_Data::get_groups();

            require_once ACS_PLUGIN_PATH . '/views/groups.php';
            break;

        case 'ac_server_testing':
            if ( !empty( $_POST ) ) {
                $fields_data[ 'acs_campaign_id' ] = $_POST[ 'acs_campaign_id' ];
                $fields_data[ 'type' ]            = $_POST[ 'type' ];
            }
            require_once ACS_PLUGIN_PATH . '/views/testing.php';
            break;

        case 'ac_server_about':
            require_once ACS_PLUGIN_PATH . '/views/about.php';
            break;

        case 'ac_server_statistics':
            if ( isset( $_GET[ 'acs_page' ] ) && is_numeric( $_GET[ 'acs_page' ] ) && $_GET[ 'acs_page' ] > 0 ) {
                $history       = AC_Data::get_history( $_GET[ 'acs_page' ] );
                $ac_pagination = ac_pagination( $_GET[ 'acs_page' ] );
            } else {
                $history       = AC_Data::get_history();
                $ac_pagination = ac_pagination();
            }
            require_once ACS_PLUGIN_PATH . '/views/statistics.php';
            break;

        case 'ac_server_month_report':
            $campaigns     = AC_Data::get_campaigns();
            $months        = AC_Data::get_history_months();
            if ( isset( $_GET[ 'action2' ] ) && $_GET[ 'action2' ] == 'get_month_details' )
                $month_details = AC_Data::get_history_month( $_GET[ 'month' ], $_GET[ 'campaign_id' ] );
            require_once ACS_PLUGIN_PATH . '/views/ajax/month_report.php';
            break;

        case 'ac_server_day_report':
            $campaigns = AC_Data::get_campaigns();
            if ( isset( $_GET[ 'action2' ] ) && $_GET[ 'action2' ] == 'get_days_details' )
                $result    = AC_Data::get_history_days_data( $_GET[ 'date_from' ], $_GET[ 'date_to' ], $_GET[ 'campaign_id' ] );
            require_once ACS_PLUGIN_PATH . '/views/ajax/day_report.php';
            break;
        case 'ac_server_group_report':
            $groups    = AC_Data::getGroupsForStatisticsDropdown();
            $months    = AC_Data::get_history_months();
            if ( isset( $_GET[ 'action2' ] ) && $_GET[ 'action2' ] == 'get_group_details' ) {
                $group_details = AC_Data::getHistoryGroups( $_GET[ 'month' ], $_GET[ 'group_id' ] );
            }
            require_once ACS_PLUGIN_PATH . '/views/ajax/group_report.php';
            break;

        case 'ac_server_clients_logs':
            $clients_logs = AC_Data::get_clients_logs();
            require_once ACS_PLUGIN_PATH . '/views/ajax/clients_logs.php';
            break;

        case 'ac_server_history':
            $where     = array();
            $condition = ' 1 ';
            if ( isset( $_REQUEST[ 'events_filter' ] ) && $_REQUEST[ 'events_filter' ] != 'all' ) {
                $where[] = 'h.event_type="' . $_REQUEST[ 'events_filter' ] . '"';
            }

            if ( isset( $_REQUEST[ 'campaign_name' ] ) && !empty( $_REQUEST[ 'campaign_name' ] ) ) {
                $where[] = 'c.title LIKE "%' . $_REQUEST[ 'campaign_name' ] . '%" ';
            }

            if ( isset( $_REQUEST[ 'advertiser_id' ] ) && !empty( $_REQUEST[ 'advertiser_id' ] ) && (int) $_REQUEST[ 'advertiser_id' ] != 0 ) {
                $where[] = 't.term_id="' . $_REQUEST[ 'advertiser_id' ] . '" ';
            }

            if ( !empty( $where ) ) {
                $condition = ' ' . implode( ' AND ', $where ) . ' ';
            }

            $history       = AC_Data::get_history( isset( $_GET[ 'acs_page' ] ) && is_numeric( $_GET[ 'acs_page' ] ) ? $_GET[ 'acs_page' ] : 1  );
            $ac_pagination = ac_pagination( isset( $_REQUEST[ 'acs_page' ] ) ? $_REQUEST[ 'acs_page' ] : 1, $condition );

            $advertisers = AC_Data::get_advertisers();
            require_once ACS_PLUGIN_PATH . '/views/ajax/history.php';
            break;

        case 'ac_server_load':
            $campaigns = AC_Data::get_campaigns();
            if ( isset( $_GET[ 'time_range' ] ) ) {
                $data = AC_Data::get_server_load( $_GET[ 'time_range' ], $_GET[ 'campaign_id' ] );
            } else {
                $data = AC_Data::get_server_load( 'hour' );
            }
            require_once ACS_PLUGIN_PATH . '/views/ajax/server_load.php';
            break;
    }
}

add_action( 'wp_enqueue_scripts', 'cmac_enqueue_head_check', 1 );
add_action( 'wp_enqueue_scripts', 'cmac_enqueue_js' );
add_action( 'wp_print_styles', 'cmac_enqueue_css' );

/**
 * Add tooltip stylesheet & javascript to page first
 */
function cmac_enqueue_js() {
    if ( defined( 'CMAC_HEAD_ENQUEUED' ) ) {
        wp_enqueue_script( 'jquery-ui-datepicker' );
        wp_enqueue_script( 'cm-ad-changer-scripts-frontend', plugins_url( 'assets/js/front-scripts.js', __FILE__ ), array( 'jquery' ) );
        wp_enqueue_script( 'jquery-tcycle', plugins_url( 'assets/js/jquery.tcycle.js', __FILE__ ), array( 'jquery' ) );
        wp_enqueue_script( 'jquery-ouibounce', plugins_url( 'assets/js/ouibounce.js', __FILE__ ), array( 'jquery' ) );
        wp_enqueue_script( 'jquery-flyingBottom', plugins_url( 'assets/js/flyingBottom.js', __FILE__ ), array( 'jquery' ) );
        wp_enqueue_style( 'cm-ad-ouibounce', plugins_url( 'assets/css/ouibounce.css', __FILE__ ) );

        $scriptData[ 'ajaxurl' ]   = admin_url( 'admin-ajax.php' );
        $scriptData[ 'pluginurl' ] = plugins_url( '', __FILE__ );
        wp_localize_script( 'cm-ad-changer-scripts-frontend', 'cmac_data', $scriptData );

        do_action( 'cmac_enqueue_js' );
    }
}

/**
 * Outputs the frontend CSS
 */
function cmac_enqueue_css() {
    /*
     * It's WP 3.3+ function
     */
    if ( function_exists( 'wp_add_inline_style' ) && defined( 'CMAC_HEAD_ENQUEUED' ) ) {
        wp_enqueue_style( 'cm-ad-changer-frontend', plugins_url( 'assets/css/style.css', __FILE__ ) );

        $custom_style = '';
        $custom_css   = get_option( 'acs_custom_css', '' );

        if ( !empty( $custom_css ) ) {
            $custom_style = "\n/*ACC Custom CSS*/\n";
            $custom_style .= $custom_css;
            $custom_style .= "\n/*ACC Custom CSS: END*/\n";
        }

        wp_add_inline_style( 'cm-ad-changer-frontend', $custom_style );

        cmac_log( 'Inline styles added' );
    }

    do_action( 'cmac_enqueue_css' );
}

/**
 * Function checks if the custom CSS and Ad Changer Scripts should be enqueued
 */
function cmac_enqueue_head_check() {
    if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
        return;
    }
    $widgetActive      = is_active_widget( false, false, 'cmac_adchangerwidget' );
    $groupWidgetActive = is_active_widget( false, false, 'cmacg_adchangerwidget' );
    if ( !empty( $widgetActive ) ) {
        $widgetActive = true;
    }
    if ( !empty( $groupWidgetActive ) ) {
        $groupWidgetActive = true;
    }
    $addCustomCssConditionsBase = array(
        'widgetDisplayed'      => $widgetActive,
        'groupWidgetDisplayed' => $groupWidgetActive,
        'inectScripts'         => get_option( 'acs_inject_scripts', '0' ) == '1'
    );
    $addCustomCssConditions     = apply_filters( 'cmac_enqueue_head_conditions', $addCustomCssConditionsBase );
    foreach ( $addCustomCssConditions as $key => $value ) {
        if ( $value == TRUE ) {
            if ( !defined( 'CMAC_HEAD_ENQUEUED' ) ) {
                define( 'CMAC_HEAD_ENQUEUED', '1' );
                break;
            }
        }
    }
    if ( !defined( 'CMAC_HEAD_ENQUEUED' ) ) {
        while ( have_posts() ): the_post();
            $the_content = get_the_content();
            if ( has_shortcode( $the_content, 'cm_ad_changer' ) ) {
                define( 'CMAC_HEAD_ENQUEUED', '1' );
            }
        endwhile;
    }
}

/* * ***************************** */
/* Handling API Requests */
/* * ***************************** */

add_action( 'wp_loaded', array( 'AC_Requests', 'handle_api_requests' ) );