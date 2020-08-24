<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	$field = $data->field;
	$key = $data->key;

if(isset($field['description'])){
	echo '<p class="description" id="'.$key.'-description">'.$field['description'].'</p>';
}
?>

	<div class="realteo-form-upload-images" >
		<?php if(isset($field['value']) && is_array(($field['value']))){ 
			foreach($field['value'] as $key => $value) { ?>
				<div class="realteo-submit-image-preview" data-thumb="<?php echo esc_attr($key); ?>" data-id="_gallery<?php echo esc_attr($key); ?>"><img src="<?php echo esc_attr($value); ?>" ><a class="remove-submit-image"><i class="fa fa-times"></i></a></div>
                <input id="_gallery<?php echo esc_attr($key); ?>" type="hidden" name="_gallery[<?php echo esc_attr($key); ?>]"  value="<?php echo esc_attr($value); ?>">
			<?php } 
		} ?>

	</div>
	<button class="realteo-form-upload button" ><i class="sl sl-icon-plus"></i> <?php echo esc_attr( $field['placeholder'] ); ?></button>


