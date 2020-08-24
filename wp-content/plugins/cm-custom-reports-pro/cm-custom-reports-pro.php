<?php
/*
Plugin Name: CM Custom Reports Pro
Plugin URI: https://www.cminds.com/store/purchase-cm-custom-reports-plugin-for-wordpress
Description: Pro! Plugin displays, exports, schedules and e-mails a multitude of useful reports about your Wordpress installation.
Version: 1.2.0
Author: CreativeMindsSolutions
Author URI: https://www.cminds.com/
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main plugin class file.
 * What it does:
 * - checks which part of the plugin should be affected by the query frontend or backend and passes the control to the right controller
 * - manages installation
 * - manages uninstallation
 * - defines the things that should be global in the plugin scope (settings etc.)
 * @author CreativeMindsSolutions - Marcin Dudek
 */
class CM_Custom_Reports {

    public static $calledClassName;
    protected static $instance = NULL;
    public static $isLicenseOK = NULL;

    /**
     * Main Instance
     *
     * Insures that only one instance of class exists in memory at any one
     * time. Also prevents needing to define globals all over the place.
     *
     * @since 1.0
     * @static
     * @staticvar array $instance
     * @return The one true AKRSubscribeNotifications
     */
    public static function instance() {
        $class = __CLASS__;
        if ( !isset( self::$instance ) && !( self::$instance instanceof $class ) ) {
            self::$instance = new $class;
        }
        return self::$instance;
    }

    public function __construct() {
        if ( empty( self::$calledClassName ) ) {
            self::$calledClassName = __CLASS__;
        }

        self::setupConstants();

        /*
         * Shared
         */

        include_once CMCR_PLUGIN_DIR . '/shared/classes/Labels.php';
        include_once CMCR_PLUGIN_DIR . '/backend/classes/Settings.php';
        include_once CMCR_PLUGIN_DIR . '/shared/cm-custom-reports-shared.php';

        CM_Custom_Reports_Shared::instance();

        include_once CMCR_PLUGIN_DIR . '/package/cminds-pro.php';

        global $cmcr_isLicenseOk;
        self::$isLicenseOK = $cmcr_isLicenseOk;

        $isLogin = in_array( $GLOBALS[ 'pagenow' ], array( 'wp-login.php', 'wp-register.php' ) );
        //if( is_admin() || defined('DOING_CRON') || $isLogin )
        //{
        /*
         * Backend
         */
        include_once CMCR_PLUGIN_DIR . '/backend/cm-custom-reports-backend.php';
        CM_Custom_Reports_Backend::instance();
        //}
    }

    /**
     * Setup plugin constants
     *
     * @access private
     * @since 1.1
     * @return void
     */
    private static function setupConstants() {
        /**
         * Define Plugin Version
         *
         * @since 1.0
         */
        if ( !defined( 'CMCR_VERSION' ) ) {
            define( 'CMCR_VERSION', '1.2.0' );
        }

        /**
         * Define Plugin Directory
         *
         * @since 1.0
         */
        if ( !defined( 'CMCR_PLUGIN_DIR' ) ) {
            define( 'CMCR_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
        }

        /**
         * Define Plugin URL
         *
         * @since 1.0
         */
        if ( !defined( 'CMCR_PLUGIN_URL' ) ) {
            define( 'CMCR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
        }

        /**
         * Define Plugin File Name
         *
         * @since 1.0
         */
        if ( !defined( 'CMCR_PLUGIN_FILE' ) ) {
            define( 'CMCR_PLUGIN_FILE', __FILE__ );
        }

        /**
         * Define Plugin Slug name
         *
         * @since 1.0
         */
        if ( !defined( 'CMCR_SLUG_NAME' ) ) {
            define( 'CMCR_SLUG_NAME', 'cm-custom-reports' );
        }

        /**
         * Define Plugin name
         *
         * @since 1.0
         */
        if ( !defined( 'CMCR_NAME' ) ) {
            define( 'CMCR_NAME', 'CM Custom Reports Pro' );
        }

        /**
         * Define Plugin basename
         *
         * @since 1.0
         */
        if ( !defined( 'CMCR_PLUGIN' ) ) {
            define( 'CMCR_PLUGIN', plugin_basename( __FILE__ ) );
        }

        /**
         * Define Plugin code
         *
         * @since 1.0
         */
        if ( !defined( 'CMCR_CODE' ) ) {
            define( 'CMCR_CODE', 'cmr' );
        }

        /**
         * Define Plugin code
         *
         * @since 1.0
         */
        if ( !defined( 'CMCR_RELEASE_NOTES' ) ) {
            define( 'CMCR_RELEASE_NOTES', 'https://www.cminds.com/store/purchase-cm-custom-reports-plugin-for-wordpress/#changelog' );
        }
    }

    public static function _install() {
        include_once CMCR_PLUGIN_DIR . '/backend/classes/ScheduleLogListTable.php';
        CMCR_Schedule_Log_List_Table::_install();
        include_once CMCR_PLUGIN_DIR . '/backend/classes/EmailTemplatesListTable.php';
        CMCR_Email_Templates_List_Table::_install();
        include_once CMCR_PLUGIN_DIR . '/backend/classes/EventLogListTable.php';
        CMCR_Event_Log_List_Table::_install();
        return;
    }

    public static function _uninstall() {
        return;
    }

    public function registerAjaxFunctions() {
        return;
    }

    /**
     * Get localized string.
     *
     * @param string $msg
     * @return string
     */
    public static function __( $msg ) {
        return __( $msg, CMCR_SLUG_NAME );
    }

    /**
     * Get item meta
     *
     * @param string $msg
     * @return string
     */
    public static function meta( $id, $key, $default = null ) {
        $result = get_post_meta( $id, $key, true );
        if ( $default !== null ) {
            $result = !empty( $result ) ? $result : $default;
        }
        return $result;
    }

}

/**
 * The main function responsible for returning the one true plugin class
 * Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $marcinPluginPrototype = MarcinPluginPrototypePlugin(); ?>
 *
 * @since 1.0
 * @return object The one true CM_Micropayment_Platform Instance
 */
function CM_Custom_ReportsInit() {
    return CM_Custom_Reports::instance();
}

$CM_Custom_Reports = CM_Custom_ReportsInit();

register_activation_hook( __FILE__, array( 'CM_Custom_Reports', '_install' ) );
register_deactivation_hook( __FILE__, array( 'CM_Custom_Reports', '_uninstall' ) );