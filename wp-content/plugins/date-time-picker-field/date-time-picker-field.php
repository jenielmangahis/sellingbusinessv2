<?php
/**
 * Plugin Name:     Date Time Picker Field
 * Plugin URI:      https://cmoreira.net/date-and-time-picker-for-wordpress/
 * Description:     Allows you to enable date and time picker fields in your website using css selectors.
 * Author:          Carlos Moreira
 * Author URI:      https://cmoreira.net
 * Text Domain:     date-time-picker-field
 * Domain Path:     /lang
 * Version:         1.7.4.1
 *
 * @package date-time-picker-field
 */

/**
 * Version Log
 *  * v.1.7.4.1 - 08.04.2019
 * - fixed get_plugin_data() error
 *
 *  * v.1.7.4 - 06.04.2019
 * - language files
 * - add version to loaded scrips and styles
 * - remove unused files
 * - fixed bug on AM/PM time format
 *
 *  * v.1.7.3 - 03.04.2019
 * - Fixed data format issue in some languages
 * - Removed moment library in favour of custom formatter
 *
 * v.1.7.2 - 03.04.2019
 * - Fix IE11 issue
 *
 * v.1.7.1 - 02.04.2019
 * - Added advanced options to better control time options for individual days
 *
 *  * v.1.6 - 16.01.2019
 * - Start of the week now follows general settings option
 * - Added new Day.Month.Year format
 *
 * v.1.5 - 04.10.2018
 * - Option to add minimum and maximum time entries
 * - Option to disable past dates
 *
 * v.1.4 - 05.09.2018
 * - Option to add script also in admin
 *
 * v.1.3 - 24.07.2018
 * - PHP Error "missing file" solved
 *
 * v.1.2.2 - 16.07.2018
 * - Included option to prevent keyboard edit
 *
 * v.1.2.1 - 16.07.2018
 * - Added option to allow original placeholder to be kept
 *
 * v.1.2 - 26.06.2018
 * - Solved bug on date and hour format
 *
 * V.1.1 - 26.06.2018
 * - Improved options handling
 * - Added direct link to settings page from plugins screen
 *
 * v.1.0
 * - Initial Release
 */

function dtp_load_plugin_textdomain() {
	load_plugin_textdomain( 'date-time-picker-field', "", basename( dirname( __FILE__ ) ) . '/lang/' );
}
add_action( 'plugins_loaded', 'dtp_load_plugin_textdomain' );

// Add Settings Page.
require_once dirname( __FILE__ ) . '/includes/class.settings-api.php';
require_once dirname( __FILE__ ) . '/admin/class-dtp-settings-page.php';

// Creates Settings Page.
new DTP_Settings_Page();

/**
 * Function to load necessary files
 *
 * @return void
 */
function dtpicker_scripts() {

	$version = dtp_get_version();

	wp_enqueue_style( 'dtpicker', plugins_url( 'vendor/datetimepicker/jquery.datetimepicker.min.css', __FILE__ ), array(), $version, 'all' );
	//wp_enqueue_script( 'dtp-moment', plugins_url( 'vendor/moment/moment.js', __FILE__ ), array( 'jquery' ), $version, true );
	wp_enqueue_script( 'dtpicker', plugins_url( 'vendor/datetimepicker/jquery.datetimepicker.full.min.js', __FILE__ ), array( 'jquery' ), $version, true );
	wp_enqueue_script( 'dtpicker-build', plugins_url( 'assets/js/dtpicker.js', __FILE__ ), array( 'dtpicker' ), $version, true );

	$opts    = get_option( 'dtpicker' );
	$optsadv = get_option( 'dtpicker_advanced' );
	// merge advanced options
	if ( is_array( $opts ) && is_array( $optsadv ) ) {
		$opts = array_merge( $opts, $optsadv );
	}

	$format = '';
	$value  = '';

	// fix format
	$opts['hourformat'] = dtp_format( $opts['hourformat'] );
	$opts['dateformat'] = dtp_format( $opts['dateformat'] );

	if ( isset( $opts['datepicker'] ) && 'on' === $opts['datepicker'] ) {
		$format .= $opts['dateformat'];
		$value  .= date( $format );
	}

	if ( isset( $opts['timepicker'] ) && 'on' === $opts['timepicker'] ) {
		$hformat = $opts['hourformat'];
		$format .= ' ' . $hformat;
		$value  .= ' ' . date( $hformat );
	}

	$opts['format'] = $format;
	$opts['value']  = $value;

	if ( isset( $opts['placeholder'] ) && 'on' === $opts['placeholder'] ) {
		$opts['value'] = '';
	}

	// day of start of week
	$opts['dayOfWeekStart'] = get_option( 'start_of_week' );

	// sanitize disabled days
	$opts['disabled_days']   = isset( $opts['disabled_days'] ) && is_array( $opts['disabled_days'] ) ? array_values( array_map( 'intval', $opts['disabled_days'] ) ) : '';
	$opts['allowed_times']   = isset( $opts['allowed_times'] ) && '' !== $opts['allowed_times'] ? explode( ',', $opts['allowed_times'] ) : '';
	$opts['sunday_times']    = isset( $opts['sunday_times'] ) && '' !== $opts['sunday_times'] ? explode( ',', $opts['sunday_times'] ) : '';
	$opts['monday_times']    = isset( $opts['monday_times'] ) && '' !== $opts['monday_times'] ? explode( ',', $opts['monday_times'] ) : '';
	$opts['tuesday_times']   = isset( $opts['tuesday_times'] ) && '' !== $opts['tuesday_times'] ? explode( ',', $opts['tuesday_times'] ) : '';
	$opts['wednesday_times'] = isset( $opts['wednesday_times'] ) && '' !== $opts['wednesday_times'] ? explode( ',', $opts['wednesday_times'] ) : '';
	$opts['thursday_times']  = isset( $opts['thursday_times'] ) && '' !== $opts['thursday_times'] ? explode( ',', $opts['thursday_times'] ) : '';
	$opts['friday_times']    = isset( $opts['friday_times'] ) && '' !== $opts['friday_times'] ? explode( ',', $opts['friday_times'] ) : '';
	$opts['saturday_times']  = isset( $opts['saturday_times'] ) && '' !== $opts['saturday_times'] ? explode( ',', $opts['saturday_times'] ) : '';

	wp_localize_script( 'dtpicker-build', 'datepickeropts', $opts );
}

// Enqueue scripts according to options
add_action( 'init', 'dtp_enqueue_scripts' );
function dtp_enqueue_scripts() {
	$opts = get_option( 'dtpicker' );
	if ( isset( $opts['load'] ) && 'full' === $opts['load'] ) {
		add_action( 'wp_enqueue_scripts', 'dtpicker_scripts' );
	} elseif ( isset( $opts['load'] ) && 'admin' === $opts['load'] ) {
		add_action( 'admin_enqueue_scripts', 'dtpicker_scripts' );
	} elseif ( isset( $opts['load'] ) && 'fulladmin' === $opts['load'] ) {
		add_action( 'admin_enqueue_scripts', 'dtpicker_scripts' );
		add_action( 'wp_enqueue_scripts', 'dtpicker_scripts' );
	} else {
		add_shortcode( 'datetimepicker', 'dtpicker_scripts' );
	}
}

// Adds link to settings page
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'dtp_add_action_links' );

function dtp_add_action_links( $links ) {
	$mylinks = array(
		'<a href="' . admin_url( 'options-general.php?page=dtp_settings' ) . '">' . __( 'Settings', 'dtpicker' ) . '</a>',
	);

	return array_merge( $mylinks, $links );
}

function dtp_get_version() {

	$plugin_version = '1.7.4';

	if ( function_exists( 'get_file_data' ) ) {

		$plugin_data = get_file_data( __FILE__ , array(
			'Version' => 'Version'
		) );

		if( $plugin_data ){
			$plugin_version = $plugin_data[ 'Version' ];
		}
	}

	return $plugin_version;
}

function dtp_format( $string ) {
	$replace = array(
		'hh',
		'HH',
		'mm',
		'A',
		'YYYY',
		'MM',
		'DD'
	);
	$replaceby = array(
		'h',
		'H',
		'i',
		'A',
		'Y',
		'm',
		'd'
	);

	return str_replace( $replace, $replaceby, $string );
}