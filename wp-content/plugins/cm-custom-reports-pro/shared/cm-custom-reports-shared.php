<?php
if( !defined('ABSPATH') )
{
    exit;
}

class CM_Custom_Reports_Shared
{
    protected static $instance = NULL;

    public static function instance()
    {
        $class = __CLASS__;
        if( !isset(self::$instance) && !( self::$instance instanceof $class ) )
        {
            self::$instance = new $class;
        }
        return self::$instance;
    }

    public function __construct()
    {
        self::setupConstants();
        self::setupOptions();
        self::loadClasses();
        self::registerActions();
    }

    /**
     * Register the plugin's shared actions (both backend and frontend)
     */
    private static function registerActions()
    {
        return;
    }

    /**
     * Setup plugin constants
     *
     * @access private
     * @since 1.1
     * @return void
     */
    private static function setupConstants()
    {
        return;
    }

    /**
     * Setup plugin constants
     *
     * @access private
     * @since 1.1
     * @return void
     */
    private static function setupOptions()
    {
        /*
         * Adding additional options
         */
        do_action('cmcr_setup_options');
    }

    /**
     * Create taxonomies
     */
    public static function cmcr_create_taxonomies()
    {
        return;
    }

    /**
     * Load plugin's required classes
     *
     * @access private
     * @since 1.1
     * @return void
     */
    private static function loadClasses()
    {
        /*
         * Load the file with shared global functions
         */
        include_once CMCR_PLUGIN_DIR . "shared/functions.php";
    }

    public function registerShortcodes()
    {
        return;
    }

    public function registerFilters()
    {
        return;
    }

    public static function initSession()
    {
        if( !session_id() )
        {
            session_start();
        }
    }

    /**
     * Create custom post type
     */
    public static function registerPostTypeAndTaxonomies()
    {
        return;
    }

}