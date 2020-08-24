<!-- Row --><?php if(isset($data->open_row) && $data->open_row) : ?>
<div class="row with-forms">
<?php endif;

if(isset($data->options_cb) && !empty($data->options_cb)){
	switch ($data->options_cb) {
		case 'realteo_get_offer_types':
			$data->options = realteo_get_offer_types_flat(false);
			break;

		case 'realteo_get_property_types':
			$data->options = realteo_get_property_types();
			break;

		case 'realteo_get_rental_period':
			$data->options = realteo_get_rental_period();
			break;
		
		default:
			# code...
			break;
	}	
}


if(isset($_GET[$data->name])) {
	if(is_array($_GET[$data->name])){
		$selected = $_GET[$data->name];
	} else {
		$selected = sanitize_text_field($_GET[$data->name]);	
	}
} else {
	$selected = '';
} 
?>
	<div class="search-form-<?php echo esc_attr($data->name);?> <?php echo esc_attr($data->class);?> <?php if(isset($data->css_class)) { echo esc_attr($data->css_class); }?>">
			<select multiple name="<?php echo esc_attr($data->name);?>[]" id="<?php echo esc_attr($data->name);?>"  data-placeholder="<?php echo esc_attr($data->placeholder);?>" class="chosen-select" >
				<option value=""><?php echo esc_attr($data->placeholder);?></option>
				<?php 
				if( is_array( $data->options ) ) :
					foreach ($data->options as $key => $value) { 
						$is_selected = in_array( $key, $selected ) ? ' selected="selected" ' : '';
						?>
						<option <?php echo $is_selected; ?> value="<?php echo esc_html($key);?>"><?php echo esc_html($value);?></option>
					<?php }
				endif;
				?>
			</select>
	</div>
<?php if(isset($data->close_row) && $data->close_row) : ?>
</div>
<?php endif; ?><!-- Row / End -->