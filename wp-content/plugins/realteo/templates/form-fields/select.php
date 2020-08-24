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

if(isset( $field['options_cb'] ) && !empty($field['options_cb'])){
	switch ($field['options_cb']) {
		case 'realteo_get_offer_types_flat':
			$field['options'] = realteo_get_offer_types_flat(false);
			break;

		case 'realteo_get_property_types':
			$field['options'] = realteo_get_property_types();
			break;

		case 'realteo_get_rental_period':
			$field['options'] = realteo_get_rental_period();
			break;
		
		default:
			# code...
			break;
	}	
}
?>
<select 
<?php if($multi) : ?> 
	multiple name="<?php echo esc_attr($field['name']);?>[]"
<?php else : ?>
	name="<?php echo esc_attr($field['name']);?>"
<?php endif; ?>
	class="<?php echo esc_attr( isset( $field['class'] ) ? $field['class'] : $key ); ?>" name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ); ?>" id="<?php echo esc_attr( $key ); ?>" <?php if ( ! empty( $field['required'] ) ) echo 'required'; ?>><?php if(isset($field['placeholder']) && !empty($field['placeholder'])) : ?>
		<option value=""><?php echo esc_attr($field['placeholder']);?></option>
	<?php endif ?>
	<?php foreach ( $field['options'] as $key => $value ) : ?>	

	<option value="<?php echo esc_attr( $key ); ?>" <?php 
		if ( isset( $field['value'] ) || isset( $field['default'] ) ) 
			if(isset( $field['value']) && is_array($field['value'])){
				if( in_array($key,$field['value']) ) {
					echo "selected='selected'";
				}
			} else {
				selected( isset( $field['value'] ) ? $field['value'] : $field['default'], $key );
			}
			 ?> >
		<?php echo esc_html( $value ); ?></option>
	<?php endforeach; ?>
</select>