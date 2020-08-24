<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$field = $data->field;
$key = $data->key;
// Get selected value
if ( isset( $field['value'] ) ) {
	$selected = $field['value'];
} elseif ( isset( $field['default']) && is_int( $field['default'] ) ) {
	$selected = $field['default'];
} elseif ( ! empty( $field['default'] ) && ( $term = get_term_by( 'slug', $field['default'], $field['taxonomy'] ) ) ) {
	$selected = $term->term_id;
} else {
	$selected = '';
}

// Select only supports 1 value
if ( is_array( $selected ) ) {
	$selected = current( $selected );
}

wp_dropdown_categories( apply_filters( 'realteo_term_select_field_wp_dropdown_categories_args', array(
	'taxonomy'         => $field['taxonomy'],
	'hierarchical'     => 1,
	'show_option_all'  => false,
	//'show_option_none' => isset($field['required']) ? '' : '-',
	'name'             => (isset( $field['name'] ) ? $field['name'] : $key),
	'orderby'          => 'name',
	'selected'         => $selected,
	'class'			   => 'chosen-select-no-single',
	'hide_empty'       => false
), $key, $field ) );
