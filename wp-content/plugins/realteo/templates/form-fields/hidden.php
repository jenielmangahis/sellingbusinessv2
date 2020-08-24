<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$field = $data->field;
$key = $data->key;
?>
<input type="hidden"
	name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ); ?>"
	<?php if ( isset( $field['autocomplete'] ) && false === $field['autocomplete'] ) { echo ' autocomplete="off"'; } ?> 
	id="<?php echo esc_attr( $key ); ?>" 
	
	value="<?php echo isset( $field['value'] ) ? esc_attr( $field['value'] ) : ''; ?>" 
	maxlength="<?php echo ! empty( $field['maxlength'] ) ? $field['maxlength'] : ''; ?>" 


	/>
