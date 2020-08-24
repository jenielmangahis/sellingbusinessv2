<?php
/*
 * Plugin Name: Realteo - Real Estate Plugin by Purethemes
 * Version: 1.0.15
 * Plugin URI: http://www.purethemes.net/
 * Description: Real Estate Plugin from Purethemes.net
 * Author: Purethemes.net
 * Author URI: http://www.purethemes.net/
 * Requires at least: 4.7
 * Tested up to: 4.8.2
 *
 * Text Domain: realteo
 * Domain Path: /languages/
 *
 * @package WordPress
 * @author Lukasz Girek
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

define( 'REALTEO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
/* load CMB2 for meta boxes*/
if ( file_exists( dirname( __FILE__ ) . '/lib/cmb2/init.php' ) ) {
	require_once dirname( __FILE__ ) . '/lib/cmb2/init.php';
	require_once dirname( __FILE__ ) . '/lib/cmb2-tabs/plugin.php';
} else {
	add_action( 'admin_notices', 'realteo_missing_cmb2' );
}
// Load plugin class files
require_once( 'includes/class-realteo-cmb2-admin.php' );
require_once( 'includes/class-realteo.php' );


// Load plugin libraries
require_once( 'includes/lib/class-realteo-admin-api.php' );


/**
 * Returns the main instance of realteo to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object realteo
 */
function Realteo () {
	$instance = Realteo::instance( __FILE__, '1.0.0' );

	/*if ( is_null( $instance->settings ) ) {
		$instance->settings =  Realteo_Settings::instance( $instance );
	}*/

	return $instance;
}
$GLOBALS['realteo'] = Realteo();


/* load template engine*/
if ( ! class_exists( 'Gamajo_Template_Loader' ) ) {
	require_once dirname( __FILE__ ) . '/lib/class-gamajo-template-loader.php';
}
include( dirname( __FILE__ ) . '/includes/class-realteo-templates.php' );

include( dirname( __FILE__ ) . '/includes/paid-properties/class-realteo-paid-properties.php' );
include( dirname( __FILE__ ) . '/includes/paid-properties/class-wc-product-property-package.php' );


function realteo_pricing_install() {
	global $wpdb;

	//$wpdb->hide_errors();

	$collate = '';
	if ( $wpdb->has_cap( 'collation' ) ) {
		if ( ! empty( $wpdb->charset ) ) {
			$collate .= "DEFAULT CHARACTER SET $wpdb->charset";
		}
		if ( ! empty( $wpdb->collate ) ) {
			$collate .= " COLLATE $wpdb->collate";
		}
	}

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

	/**
	 * Table for user packages
	 */
	$sql = "
	CREATE TABLE {$wpdb->prefix}realteo_user_packages (
	  id bigint(20) NOT NULL auto_increment,
	  user_id bigint(20) NOT NULL,
	  product_id bigint(20) NOT NULL,
	  order_id bigint(20) NOT NULL default 0,
	  package_featured int(1) NULL,
	  package_duration bigint(20) NULL,
	  package_limit bigint(20) NOT NULL,
	  package_count bigint(20) NOT NULL,
	  PRIMARY KEY  (id)
	) $collate;
	";
	
	dbDelta( $sql );

}

register_activation_hook( __FILE__, 'realteo_pricing_install' );

function realteo_missing_cmb2() { ?>
	<div class="error">
		<p><?php _e( 'CMB2 Plugin is missing CMB2!', 'realteo' ); ?></p>
	</div>
<?php }

Realteo();
