<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$field = $data->field;
$key = $data->key;
$multi = false;

if(isset($field['multi']) && $field['multi']) {
	$multi = true;
}

?>
<?php if($multi) : ?> 
	<div class="checkboxes in-row margin-bottom-20">

	<?php foreach ( $field['options'] as $slug => $name ) : ?>

		<input type="checkbox" name="<?php echo $key ?>[]" id="<?php echo esc_html($slug) ?>" value="<?php echo esc_attr($slug); ?>" <?php if(isset( $field['value']) && is_array($field['value'])){
			if( in_array($slug,$field['value']) ) {
				echo "checked";
			}
		}  ?>>
		
		<label for="<?php echo esc_html($slug) ?>"><?php echo esc_html($name) ?></label>
	<?php endforeach; ?>

</div>
<?php else : ?>
	

<!-- Rounded switch -->
<div class="switch_box box_1">
	<input type="checkbox" 
	class="input-checkbox switch_1" 

	name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ); ?>"
	
	id="<?php echo esc_attr( $key ); ?>" 
	placeholder="<?php echo esc_attr( $field['placeholder'] ); ?>" 
	value="check_on"
	<?php isset( $field['value'] ) ? checked($field['value'],'check_on') : ''; ?> 
	maxlength="<?php echo ! empty( $field['maxlength'] ) ? $field['maxlength'] : ''; ?>" 
	<?php if ( ! empty( $field['required'] ) ) echo 'required'; ?> 
	<?php if ( isset( $field['unit'] ) ) echo 'data-unit="'.$field['unit'].'"'; ?> 

	/>
</div>

<label class="realteo-switch"></label>
<?php endif; ?>