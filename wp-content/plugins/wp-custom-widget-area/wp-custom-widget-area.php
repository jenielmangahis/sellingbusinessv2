<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://example.com
 * @since             cF
 * @package           Custom widget area
 *
 * @wordpress-plugin
 * Plugin Name:       WP Custom Widget area
 * Plugin URI:        http://kishorkhambu.com.np/plugins/
 * Description:       A wordpress plugin to create custom dynamic widget area.
 * Version:           1.2.5 
 * Author:            Kishor Khambu
 * Author URI:        http://kishorkhambu.com.np
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-custom-widget-area
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}
$plugin_url = plugins_url('', __FILE__);

$purl = plugin_dir_url( __FILE__ );

require_once plugin_dir_path( __FILE__ ) . 'includes/config.php';
/**
 * The code that runs during plugin activation.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-custom-widget-area-activator.php';

/**
 * The code that runs during plugin deactivation.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-wp-custom-widget-area-deactivator.php';



/** This action is documented in includes/class-wp-custom-widget-area-activator.php */
register_activation_hook( __FILE__, array( 'Custom_Widget_Area_Activator', 'activate' ) );

/** This action is documented in includes/class-wp-custom-widget-area-deactivator.php */
register_deactivation_hook( __FILE__, array( 'Custom_Widget_Area_Deactivator', 'deactivate' ) );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-custom-widget-area.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.1.5
 */
function run_plugin_name() {
	$plugin = new Custom_Widget_Area();
	$plugin->run();

}


function cb(){
	echo "welcome to first metabox showcase";
}



function myplugin_update_db_check() {
    global $kz_db_version, $wpdb;
    $table_name = TABLE_NAME;
    $current_version = get_site_option( 'kz_db_version' );
    //update_site_option('kz_db_version', '1.0.4'); exit;
   // var_dump($table_name); exit;
    if ( get_site_option( 'kz_db_version' ) != $kz_db_version ) {
        Custom_Widget_Area_Activator::activate();
       if(!!$current_version && $current_version < '1.1.0'){
       		$row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$table_name' AND column_name = 'cwa_type'"  );
       		if(empty($row)){
       			$x = $wpdb->query("ALTER TABLE $table_name ADD cwa_type varchar (10) ");
		       $updaterow = $wpdb->get_results(  "SELECT id FROM $table_name");
  			   foreach ($updaterow as $data) {
  				   	# code...
  				   	$up = $wpdb->update($table_name, array('cwa_type'=> 'widget'), array('id'=>$data->id));
  				  }
       		}
       		
       }

      //new updates
      if(!!$current_version && $current_version < '1.2.5'){
        $row = $wpdb->get_results(  "SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '$table_name' AND column_name = 'cwa_type'"  );
        if(empty($row)){
          $x = $wpdb->query("ALTER TABLE $table_name MODIFY COLUMN cwa_widget_wrapper text, MODIFY COLUMN cwa_widget_header_wrapper text ");
        }
        
      }
    }


    run_plugin_name();
}
add_action( 'plugins_loaded', 'myplugin_update_db_check' );


