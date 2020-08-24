<?php

class Advanced_Ads_Pro_Module_Flash_Admin {

    public function __construct() {
        add_action( 'advanced-ads-settings-init', array( $this, 'settings_init'), 10, 1 );
	add_action( 'admin_enqueue_scripts', array( $this, 'load_admin_scripts' ), 10 );
    }

    /**
     * load admin scripts needed for flash files
     */
    public function load_admin_scripts(){

	    //call media manager only on ad edit pages
	    $screen = get_current_screen();
	    if( isset( $screen->id ) && Advanced_Ads::POST_TYPE_SLUG === $screen->id ) {
		    wp_enqueue_media();
	    }
    }

    public function settings_init($hook) {
       $admin = Advanced_Ads_Admin::get_instance();

        // add new section
        add_settings_field(
            'module-flash',
            __('Flash files', 'advanced-ads-pro'),
            array($this, 'render_settings'),
            Advanced_Ads_Pro::OPTION_KEY . '-settings',
            Advanced_Ads_Pro::OPTION_KEY . '_modules-enable'
        );
    }

    public function render_settings() {
        include dirname( __FILE__ ) . '/views/settings.php';
    }
}
