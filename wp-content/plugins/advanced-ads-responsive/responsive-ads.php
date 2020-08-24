<?php
/**
 * Advanced Ads – Responsive Ads
 *
 * Plugin Name:       Advanced Ads – Responsive Ads
 * Plugin URI:        https://wpadvancedads.com/add-ons/responsive-ads/
 * Description:       Display ads based on browser size.
 * Version:           1.8.4
 * Author:            Thomas Maier
 * Author URI:        https://wpadvancedads.com
 * Text Domain:       advanced-ads-responsive
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// only load if not already existing (maybe within another plugin I created)
if(!class_exists('Advanced_Ads_Responsive')) {

// load basic path and url to the plugin
define('AAR_BASE_PATH', plugin_dir_path(__FILE__));
define('AAR_BASE_URL', plugin_dir_url(__FILE__));
define('AAR_BASE_DIR', dirname( plugin_basename( __FILE__ ) ) ); // directory of the plugin without any paths

// plugin slug and textdoamin
define('AAR_SLUG', 'responsive-ads');

define( 'AAR_VERSION', '1.8.4' );
define( 'AAR_PLUGIN_URL', 'https://wpadvancedads.com' );
define( 'AAR_PLUGIN_NAME', 'Responsive Ads' );

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

include_once( plugin_dir_path( __FILE__ ) . 'classes/plugin.php' );
include_once( plugin_dir_path( __FILE__ ) . 'public/public.php' );
    new Advanced_Ads_Responsive();

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {
    include_once( plugin_dir_path( __FILE__ ) . 'admin/admin.php' );
    new Advanced_Ads_Responsive_Admin();
}

/*----------------------------------------------------------------------------*
 * Loads Modules
 *----------------------------------------------------------------------------*/
include_once(AAR_BASE_PATH . 'modules/gadsense/main.php');

include_once(AAR_BASE_PATH . 'classes/Mobile-Detect/Mobile_Detect.php');
$advads_mobile_detect = new Advanced_Ads_Mobile_Detect;

include_once(AAR_BASE_PATH . 'modules/amp/main.php');
}



