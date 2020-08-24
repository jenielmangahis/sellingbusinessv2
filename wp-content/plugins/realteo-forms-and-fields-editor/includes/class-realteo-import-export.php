<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Realteo_Forms_Import_Export {
	/**
     * Stores static instance of class.
     *
     * @access protected
     * @var Realteo_Submit The single instance of the class
     */
    protected static $_instance = null;

    /**
     * Returns static instance of class.
     *
     * @return self
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

	public function __construct($version = '1.0.0') {
  
       add_action( 'admin_menu', array( $this, 'add_options_page' ) ); //create tab pages
       add_action( 'admin_init', array( $this,'realteo_process_settings_export' ));
       add_action( 'admin_init', array( $this,'realteo_process_settings_import' ));

    }

    /**
     * Add menu options page
     * @since 0.1.0
     */
    public function add_options_page() {        
         add_submenu_page( 'realteo-fields-and-form', 'Import/Export', 'Import/Export', 'manage_options', 'realteo-import-export', array( $this, 'output' )); 

         //reset
    
    }

    public function output(){
    	
        if ( ! empty( $_GET['import'] ) ) {
                echo '<div class="updated"><p>' . __( 'The file was imported successfully.', 'realteo' ) . '</p></div>';
            }?>
        <div class="metabox-holder">
            <div class="postbox">
                <h3><span><?php _e( 'Export Settings' ); ?></span></h3>
                <div class="inside">
                    <p><?php _e( 'Export fields and forms settings for this site as a .json file. This allows you to easily import the configuration into another site or make a backup.' ); ?></p>
                    <form method="post">
                        <p><input type="hidden" name="realteo_action" value="export_settings" /></p>
                        <p>
                            <?php wp_nonce_field( 'realteo_export_nonce', 'realteo_export_nonce' ); ?>
                            <?php submit_button( __( 'Export' ), 'secondary', 'submit', false ); ?>
                        </p>
                    </form>
                </div><!-- .inside -->
            </div><!-- .postbox -->

            <div class="postbox">
                <h3><span><?php _e( 'Import Settings' ); ?></span></h3>
                <div class="inside">
                    <p><?php _e( 'Import the plugin settings from a .json file. This file can be obtained by exporting the settings on another site using the form above.' ); ?></p>
                    <form method="post" enctype="multipart/form-data">
                        <p>
                            <input type="file" name="import_file"/>
                        </p>
                        <p>
                            <input type="hidden" name="realteo_action" value="import_settings" />
                            <?php wp_nonce_field( 'realteo_import_nonce', 'realteo_import_nonce' ); ?>
                            <?php submit_button( __( 'Import' ), 'secondary', 'submit', false ); ?>
                        </p>
                    </form>
                </div><!-- .inside -->
            </div><!-- .postbox -->
        </div><!-- .metabox-holder -->
        <?php
    }


        /**
         * Process a settings export that generates a .json file of the shop settings
         */
        function realteo_process_settings_export() {

            if( empty( $_POST['realteo_action'] ) || 'export_settings' != $_POST['realteo_action'] )
                return;

            if( ! wp_verify_nonce( $_POST['realteo_export_nonce'], 'realteo_export_nonce' ) )
                return;

            if( ! current_user_can( 'manage_options' ) )
                return;

            $settings = array();
            $settings['property_types']         = get_option('realteo_property_types_fields');
            $settings['property_rental']        = get_option('realteo_rental_periods_fields');
            $settings['property_offer_types']   = get_option('realteo_offer_types_fields');

            $settings['submit']                 = get_option('realteo_submit_form_fields');
            
            $settings['price_tab']              = get_option('realteo_price_tab_fields');
            $settings['main_details_tab']       = get_option('realteo_main_details_tab_fields');
            $settings['details_tab']            = get_option('realteo_details_tab_fields');
            $settings['location_tab']           = get_option('realteo_locations_tab_fields');

            $settings['sidebar_search']         = get_option('realteo_sidebar_search_form_fields');
            $settings['full_width_search']      = get_option('realteo_full_width_search_form_fields');
            $settings['half_map_search']        = get_option('realteo_search_on_half_map_form_fields');
            $settings['home_page_search']       = get_option('realteo_search_on_home_page_form_fields');
            $settings['home_page_alt_search']   = get_option('realteo_search_on_home_page_alt_form_fields');

            ignore_user_abort( true );

            nocache_headers();
            header( 'Content-Type: application/json; charset=utf-8' );
            header( 'Content-Disposition: attachment; filename=realteo-settings-export-' . date( 'm-d-Y' ) . '.json' );
            header( "Expires: 0" );

            echo json_encode( $settings );
            exit;
        }

    /**
     * Process a settings import from a json file
     */
    function realteo_process_settings_import() {

        if( empty( $_POST['realteo_action'] ) || 'import_settings' != $_POST['realteo_action'] )
            return;

        if( ! wp_verify_nonce( $_POST['realteo_import_nonce'], 'realteo_import_nonce' ) )
            return;

        if( ! current_user_can( 'manage_options' ) )
            return;

        $extension = end( explode( '.', $_FILES['import_file']['name'] ) );

        if( $extension != 'json' ) {
            wp_die( __( 'Please upload a valid .json file' ) );
        }

        $import_file = $_FILES['import_file']['tmp_name'];

        if( empty( $import_file ) ) {
            wp_die( __( 'Please upload a file to import' ) );
        }

        // Retrieve the settings from the file and convert the json object to an array.
        $settings = json_decode( file_get_contents( $import_file ), true );

        update_option('realteo_property_types_fields'   ,$settings['property_types']);
        update_option('realteo_rental_periods_fields'   ,$settings['property_rental']);
        update_option('realteo_offer_types_fields'      ,$settings['property_offer_types']);

        update_option('realteo_submit_form_fields'      ,$settings['submit']);

        update_option('realteo_price_tab_fields'        ,$settings['price_tab']);
        update_option('realteo_main_details_tab_fields' ,$settings['main_details_tab']);
        update_option('realteo_details_tab_fields'      ,$settings['details_tab']);
        update_option('realteo_locations_tab_fields'    ,$settings['location_tab']);

        update_option('realteo_sidebar_search_form_fields',$settings['sidebar_search']);
        update_option('realteo_full_width_search_form_fields',$settings['full_width_search']);
        update_option('realteo_search_on_half_map_form_fields',$settings['half_map_search']);
        update_option('realteo_search_on_home_page_form_fields',$settings['home_page_search']);
        update_option('realteo_search_on_home_page_alt_form_fields',$settings['home_page_alt_search']);

       
        wp_safe_redirect( admin_url( 'admin.php?page=realteo-import-export&import=success' ) ); exit;

    }

}