<?php
if(isset($_GET[$data->name])) {
	$value = sanitize_text_field($_GET[$data->name]);
} else {
	$value = '';
} 
if($data->name === 'realteo_order' ) {
	$value = isset( $_GET['realteo_order'] ) ? (string) $_GET['realteo_order']  : realteo_get_option('realteo_sort_by'); 	
}
?>
<input id="<?php echo esc_attr($data->name);?>" name="<?php echo esc_attr($data->name);?>" type="hidden" placeholder="<?php echo esc_attr($data->placeholder);?>" value="<?php if(isset($value)){ echo $value;  } ?>"/>
