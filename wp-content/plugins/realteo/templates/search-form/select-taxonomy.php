<!-- Row -->

<?php if($data->open_row) : ?>
<div class="row with-forms">
<?php endif; 
$multi = false;

if(isset($data->multi) && $data->multi) {
	$multi = true;
}
if(isset($_GET[$data->name])) {
	if(is_array($_GET[$data->name])){
		$selected = $_GET[$data->name];
	} else {
		$selected = sanitize_text_field($_GET[$data->name]);	
	}
} else if(get_query_var($data->taxonomy)) {
	$selected = urldecode( get_query_var( $data->taxonomy ));
} else {
	$selected = '';
} 

?>
	<div class="search-form-<?php echo esc_attr($data->name);?> <?php echo esc_attr($data->class);?> <?php if(isset($data->css_class)) { echo esc_attr($data->css_class); }?>">
		<select 
		<?php if($multi) : ?> 
			multiple name="<?php echo esc_attr($data->name);?>[]"
		<?php else : ?>
			name="<?php echo esc_attr($data->name);?>"
		<?php endif; ?>
		 data-placeholder="<?php echo esc_attr($data->placeholder);?>" class="chosen-select" >
			<option value=""><?php echo esc_attr($data->placeholder);?></option>
			<?php 

			$terms = get_terms($data->taxonomy, array('hide_empty' => false));
			$options = realteo_get_options_array_hierarchical($terms,$selected);
			echo $options;
			//$options = realteo_get_options_array('taxonomy',$data->taxonomy);
			//echo get_realteo_options_dropdown($options,$selected) ?>
		</select>
	</div>

<?php if($data->close_row) : ?>
</div>
<?php endif; ?>

<!-- Row / End -->