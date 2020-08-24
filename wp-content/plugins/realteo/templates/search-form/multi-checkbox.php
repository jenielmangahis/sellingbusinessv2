<div class="search-form-<?php echo esc_attr($data->name);?> checkboxes one-in-row <?php if(isset($data->css_class)) { echo esc_attr($data->css_class); }?>">


<?php 

if(isset($_GET[$data->name])) {
	$selected = $_GET[$data->name];
} else {
	$selected = array();
} 

if(isset($data->taxonomy) && !empty($data->taxonomy)) {
	$data->options = realteo_get_options_array('taxonomy',$data->taxonomy);
	if(is_tax($data->taxonomy)){
		$selected[get_query_var($data->taxonomy)] = 'on';
	}	
	foreach ($data->options as $key => $value) { ?>

		<input <?php if ( array_key_exists ($value['slug'], $selected) ) { echo 'checked="checked"'; } ?> id="<?php echo esc_html($value['slug']) ?>" value="<?php echo esc_html($value['slug']) ?>" type="checkbox" name="<?php echo $data->name.'['.esc_html($value['slug']).']'; ?>">
		<label for="<?php echo esc_html($value['slug']) ?>"><?php echo esc_html($value['name']) ?></label>
	
<?php } 
}

if(isset($data->options_source) && empty($data->taxonomy) ) {
	if(isset($data->options_cb) && !empty($data->options_cb) ){
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
	if($data->options_source == 'custom') {
		$data->options = array_flip($data->options);
	}
	foreach ($data->options as $key => $value) { ?>

		<input <?php if ( array_key_exists ($key, $selected) ) { echo 'checked="checked"'; } ?> id="<?php echo esc_html($key) ?>" type="checkbox" name="<?php echo $data->name.'['.esc_html($key).']'; ?>">
		<label for="<?php echo esc_html($key) ?>"><?php echo esc_html($value) ?></label>
	
<?php } 
}
?>


</div>