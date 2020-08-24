<?php
/**
 * Load widgets

 * @since 4.0.3
 */

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

/* Widgets list */
$findeo_widgets = array(
	'inc/widgets/list.php',
	'inc/widgets/header.php',

);
$findeo_widgets = apply_filters( 'findeo_widgets', $findeo_widgets );
foreach ( $findeo_widgets as $findeo_widget ) {
	include_once locate_template( $findeo_widget );
}