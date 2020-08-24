<?php
/**
 * Advanced Ads – Selling Ads
 *
 * Plugin Name:       Advanced Ads – Selling Ads
 * Plugin URI:        https://wpadvancedads.com/
 * Description:       Let users purchase ads directly in the frontend of your site.
 * Version:           1.2.4
 * Author:            Thomas Maier
 * Author URI:        https://wpadvancedads.com/
 * Text Domain:       advanced-ads-selling
 * Domain Path:       /languages
 * 
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// only load if not already existing (maybe within another plugin I created)
// important before anything breaks
register_activation_hook(__FILE__, 'advanced_ads_selling_activate');

if( !class_exists('Advanced_Ads_Selling') && version_compare(PHP_VERSION, '5.3.0') === 1 ) {
    
	// load basic path to the plugin
	define( 'AASA_BASE_PATH', plugin_dir_path( __FILE__ ) );
	define( 'AASA_BASE_URL', plugin_dir_url( __FILE__ ) );
	define( 'AASA_BASE_DIR', dirname( plugin_basename( __FILE__ ) ) ); // directory of the plugin without any paths
	// general and global slug, e.g. to store options in WP, textdomain
	define( 'AASA_SLUG', 'advanced-ads-selling' );
	define( 'AASA_PLUGIN_NAME', 'Selling Ads' );
	define( 'AASA_URL', 'https://wpadvancedads.com/' );
	define( 'AASA_VERSION', '1.2.4' );

	// load public functions (might be used by modules, other plugins or theme)
	include_once( AASA_BASE_PATH . 'classes/plugin.php' );
	include_once( AASA_BASE_PATH . 'classes/notifications.php' );
	new Advanced_Ads_Selling_Notifications();
	include_once( AASA_BASE_PATH . 'classes/order.php' );
	new Advanced_Ads_Selling_Order();

	// handle ajac requests
	if( defined('DOING_AJAX') && DOING_AJAX ){
	    include_once( AASA_BASE_PATH . 'public/includes/ajax.php' );
	    new Advanced_Ads_Selling_Ajax();
	}
	
	if ( is_admin() ) {
	    include_once( AASA_BASE_PATH . 'admin/admin.php' );
	    new Advanced_Ads_Selling_Admin();
	    include_once( AASA_BASE_PATH . 'admin/includes/ad-product.php' );
	    new Advanced_Ads_Selling_Admin_Ad_Product();
	    include_once( AASA_BASE_PATH . 'admin/includes/ad-order-meta-box.php' );
	    new Advanced_Ads_Selling_Admin_Ad_Order_Meta_Box();
	    include_once( AASA_BASE_PATH . 'admin/includes/order-page.php' );
	    new Advanced_Ads_Selling_Admin_Order_Page();
	    include_once( AASA_BASE_PATH . 'admin/includes/placements.php' );
	    new Advanced_Ads_Selling_Admin_Placements();
	} else {
	    include_once( AASA_BASE_PATH . 'public/public.php' );
	    new Advanced_Ads_Selling();
	    include_once( AASA_BASE_PATH . 'public/includes/order.php' );
	    new Advanced_Ads_Selling_Public_Order();
	}
}

function advanced_ads_selling_activate() {
    if(version_compare(PHP_VERSION, '5.3.0', '<') == -1) { 
	deactivate_plugins(plugin_basename('advanced-ads-selling/advanced-ads-selling.php'));
	wp_die('<em>Advanced Ads – Selling Ads</em> requires PHP 5.3 or higher. Your server is using ' . PHP_VERSION . '. Please contact your server administrator for a PHP update. <a href="'. admin_url( 'plugins.php') .'">Back to Plugins</a>'); 
    } 
}