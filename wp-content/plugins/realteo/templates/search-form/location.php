<!-- Row -->
<?php if(isset($data->open_row) && $data->open_row) : ?>
<div class="row with-forms">
<?php endif; 
if(isset($_GET[$data->name])) {
	$value = sanitize_text_field($_GET[$data->name]);
} else {
	$value = '';
} ?>

	<div class="input-with-icon-2 location <?php echo esc_attr($data->class);?> <?php if(isset($data->css_class)) { echo esc_attr($data->css_class); }?>" >
			<input name="<?php echo esc_attr($data->name);?>" id="<?php echo esc_attr($data->name);?>" class="<?php if(realteo_get_option_with_name('realteo_general_options','realteo_search_name_autocomplete')) { echo 'title-autocomplete'; } ?> <?php echo esc_attr($data->name);?>" type="text" placeholder="<?php echo esc_attr($data->placeholder);?>" value="<?php if(isset($value)){ echo $value;  } ?>"/>
			<a href="#" class="geoLocation"><i class="fa fa-dot-circle-o"></i></a>
	</div>

<?php if(isset($data->close_row) && $data->close_row) : ?>
</div>
<?php endif; ?>

<!-- Row / End -->