<!-- Row -->
<?php if(isset($data->open_row) && $data->open_row) : ?>
<div class="row with-forms">
<?php endif; ?>

<?php 
$param = null;
if(isset($_GET['_offer_type']) && $_GET['_offer_type'] == 'rent') {
	$param = 'rent';
}
if(isset($_GET['_offer_type']) && $_GET['_offer_type'] == 'sale') {
	$param = 'sale';
}


if(isset($_GET[$data->name.'_min']) && !empty($_GET[$data->name.'_min']) && $_GET[$data->name.'_min'] != 'NaN') {
	$min = sanitize_text_field($_GET[''.$data->name.'_min']);
	$min = (int)preg_replace('/[^0-9]/', '', $min);

	if($data->name == '_price'){
		$data->min = Realteo_Search::get_min_meta_value($data->name,$param);
	} else {
		$data->min = Realteo_Search::get_min_meta_value($data->name);
	}
} else {

	if($data->min == 'auto') {
		if($data->name == '_price'){
			$min = Realteo_Search::get_min_meta_value($data->name,$param);
		} else {
			$min = Realteo_Search::get_min_meta_value($data->name);
			$data->min = Realteo_Search::get_min_meta_value($data->name);
		}
		
	} else {
		$min = $data->min;	
	}
} 

if(isset($_GET[$data->name.'_max']) && !empty($_GET[$data->name.'_max']) && $_GET[$data->name.'_max'] != 'NaN') {

	$max = sanitize_text_field($_GET[$data->name.'_max']);

	$max = (int)preg_replace('/[^0-9]/', '', $max);
	if($data->name == '_price'){
		$data->max = Realteo_Search::get_max_meta_value($data->name,$param);
		if(!$data->max) {
			$data->max = Realteo_Search::get_max_meta_value($data->name);
		}
	} else {
		$data->max = Realteo_Search::get_max_meta_value($data->name);
	}
} else {
	if($data->max == 'auto') {
		if($data->name == '_price'){
			$max = Realteo_Search::get_max_meta_value($data->name,$param);
			if(!$max){
				$max = Realteo_Search::get_max_meta_value($data->name);
			}

		} else {
			$max = Realteo_Search::get_max_meta_value($data->name);
			$data->max = Realteo_Search::get_max_meta_value($data->name);
		}
	} else {
		$max = $data->max;	
	}
	
} 

?>

<div class="search-form-<?php echo esc_attr($data->name);?> <?php echo esc_attr($data->class);?> <?php if(isset($data->css_class)) { echo esc_attr($data->css_class); }?>" >
	<div class="range-slider">
		<label><?php echo esc_html($data->placeholder) ?> <?php if(isset($data->unit) && !empty($data->unit)) { echo '<span>('.esc_attr($data->unit).')</span>'; } ?></label>
		<div class="range-slider-element" id="<?php echo esc_attr($data->name); ?>" 
		data-min="<?php echo esc_attr($data->min); ?>" 
		data-max="<?php echo esc_attr($data->max); ?>" 
		data-value-min="<?php echo esc_attr($min); ?>" 
		data-value-max="<?php echo esc_attr($max); ?>" 
		data-unit=""></div>
		<div class="clearfix"></div>
	</div>
</div>
<?php if(isset($data->close_row) && $data->close_row) : ?>
</div>
<?php endif; ?>

<!-- Row / End -->