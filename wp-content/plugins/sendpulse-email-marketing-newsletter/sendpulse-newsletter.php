<?php
/*
	Plugin Name: SendPulse Email Marketing Newsletter
	Plugin URI: https://wordpress.org/plugins/sendpulse-email-marketing-newsletter/
	Description: Add e-mail subscription form, send marketing newsletters and create autoresponders.
	Version: 2.1.0
	Author: SendPulse
	Author URI: https://sendpulse.com
	License:     GPL2
	License URI: https://www.gnu.org/licenses/gpl-2.0.html
	Text Domain: sendpulse-email-marketing-newsletter
	Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


include_once( 'inc/class-senpulse-newsletter-requirement.php' );

$requirement = new Send_Pulse_Newsletter_Requirement();

if ( $requirement->is_success() ) {

	include_once( 'inc/class-senpulse-newsletter-loader.php' );

	new Send_Pulse_Newsletter_Loader(
		plugins_url( '/', __FILE__ ),
		basename( dirname( __FILE__ ) ) . '/languages/'
	);

}