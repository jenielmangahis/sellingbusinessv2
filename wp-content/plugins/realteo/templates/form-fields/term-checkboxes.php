<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$field = $data->field;
$key = $data->key;
// Get selected value
if ( isset( $field['value'] ) ) {
	$selected = array($field['value']);

} elseif ( isset( $field['default']) && is_int( $field['default'] ) ) {
	$selected = $field['default'];

} elseif ( ! empty( $field['default'] ) && ( $term = get_term_by( 'slug', $field['default'], $field['taxonomy'] ) ) ) {

	$selected = array($term->term_id);
} else {
	$selected = array(array());
}

?>
<ul class="realteo-term-checklist realteo-term-checklist-<?php echo $key ?>">
<?php
	require_once( ABSPATH . '/wp-admin/includes/template.php' );

	if ( empty( $field['default'] ) ) {
		$field['default'] = '';
	}

	$taxonomy = $field['taxonomy'];
	$terms = get_terms( $taxonomy, array(
	    'hide_empty' => false,
	) );
	foreach ($terms as $key => $category) {
		echo '<input value="' . $category->term_id . '" type="checkbox" name="tax_input['.$taxonomy.'][]" id="in-'.$taxonomy.'-' . $category->term_id . '"' .
            checked( in_array( $category->term_id, $selected[0] ), true, false ) . ' /> ' .
            '<label for="in-'.$taxonomy.'-' . $category->term_id . '">'. esc_html( apply_filters( 'the_category', $category->name ) ) . '</label>';
	}
?>
</ul>