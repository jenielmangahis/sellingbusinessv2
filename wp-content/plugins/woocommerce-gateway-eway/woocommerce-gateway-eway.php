<?php
/**
 * Plugin Name: WooCommerce eWAY Payment Gateway
 * Description: WooCommerce eWAY Rapid 3.1 payment gateway integration supporting all countries. Support for WooCommerce Subscriptions.
 * Plugin URI: https://woocommerce.com/products/eway/
 * Author: WooCommerce
 * Author URI: https://woocommerce.com/
 * Version: 3.1.19
 * Text Domain: wc-eway
 * Domain Path: /languages
 * Requires at least: 4.4
 * Tested up to: 5.0
 * WC tested up to: 3.5
 * WC requires at least: 2.6
 * Copyright: © 2014-2018 WooCommerce.
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_action( 'plugins_loaded', 'woocommerce_eway_init', 0 );

function woocommerce_eway_init() {
	if ( ! class_exists( 'WC_Payment_Gateway' ) ) {
		return;
	}

	load_plugin_textdomain( 'wc-eway', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

	define( 'WC_EWAY_TEMPLATE_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) . '/templates/' );

	require_once 'includes/class-wc-gateway-eway.php';
	require_once 'includes/class-wc-gateway-eway-privacy.php';
	include_once 'includes/class-wc-gateway-eway-saved-cards.php';

	// Load subscriptions class if active
	if ( class_exists( 'WC_Subscriptions_Order' ) ) {
		if ( ! function_exists( 'wcs_create_renewal_order' ) ) { // Subscriptions < 2.0
			require_once 'includes/class-wc-gateway-eway-subscriptions-deprecated.php';
		} else {
			require_once 'includes/class-wc-gateway-eway-subscriptions.php';
		}
	}

	// Add classes to WC Payment Methods
	add_filter( 'woocommerce_payment_gateways', 'woocommerce_eway_add_gateway' );

	// Add links next to plugin 'Activate' button.
	add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'woocommerce_eway_plugin_action_links' );
}

function woocommerce_eway_add_gateway( $available_gateways ) {
	if ( class_exists( 'WC_Subscriptions_Order' ) ) {
		if ( ! function_exists( 'wcs_create_renewal_order' ) ) { // Subscriptions < 2.0
			$available_gateways[] = 'WC_Gateway_EWAY_Subscriptions_Deprecated';
		} else {
			$available_gateways[] = 'WC_Gateway_EWAY_Subscriptions';
		}
	} else {
		$available_gateways[] = 'WC_Gateway_EWAY';
	}
	return $available_gateways;
}

/**
 * Adds plugin action links.
 *
 * @since 3.1.18
 *
 * @param array $links Plugin action links
 *
 * @return array Plugin action links
 */
function woocommerce_eway_plugin_action_links( $links ) {
	$setting_link = admin_url( 'admin.php?page=wc-settings&tab=checkout&section=eway' );

	$plugin_links = array(
		'<a href="' . $setting_link . '">' . __( 'Settings', 'wc_eway' ) . '</a>',
	);

	return array_merge( $plugin_links, $links );
}
