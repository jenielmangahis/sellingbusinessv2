<!-- Min Price -->
<?php 
if(isset($_GET[$data->name.'_min']) && !empty($_GET[$data->name.'_min']) && $_GET[$data->name.'_min'] != 'NaN') {
	$min = sanitize_text_field($_GET[''.$data->name.'_min']);
	$min = str_replace( ',', '', $min );
} 
if(isset($_GET[$data->name.'_max']) && !empty($_GET[$data->name.'_max']) && $_GET[$data->name.'_max'] != 'NaN') {
	$max = sanitize_text_field($_GET[''.$data->name.'_max']);
	$max = str_replace( ',', '', $max );
} 

?>

<?php if(isset($data->open_row) && $data->open_row) : ?>
<div class="row with-forms">
<?php endif; ?>

	<div class="search-form-<?php echo esc_attr($data->name);?> <?php echo esc_attr($data->class);?> <?php if(isset($data->css_class)) { echo esc_attr($data->css_class); }?>">
		
		<!-- Select Input -->
		<div class="select-input disabled-first-option">
			<input type="text" value="<?php if(isset($min)) { echo esc_attr($min); } ?>" name="<?php echo esc_attr($data->name);?>_min" id="<?php echo esc_attr($data->name); ?>-min"  placeholder="<?php esc_html_e('Min','realteo'); ?> <?php echo esc_attr($data->placeholder); ?>" data-unit="<?php echo esc_attr($data->unit) ?>">
			<select>	
				<option><?php esc_html_e('Min','realteo'); ?>  <?php echo esc_attr($data->placeholder); ?></option>
				<?php echo get_realteo_intervals_dropdown($data->min,$data->max,$data->step,$data->name) ?>
			</select>
		</div>
		<!-- Select Input / End -->

	</div>

	<!-- Max Price -->
	<div class="<?php echo esc_attr($data->class);?>">
		
		<!-- Select Input -->
		<div class="select-input disabled-first-option">
			<input type="text" value="<?php if(isset($max)) { echo esc_attr($max); } ?>" name="<?php echo esc_attr($data->name);?>_max" id="<?php echo esc_attr($data->name); ?>-max"  placeholder="<?php esc_html_e('Max','realteo'); ?> <?php echo esc_attr($data->placeholder); ?>" data-unit="<?php echo esc_attr($data->unit) ?>">
			<select>	
				<option><?php esc_html_e('Max','realteo'); ?>  <?php echo esc_attr($data->placeholder); ?></option>
				<?php echo get_realteo_intervals_dropdown($data->min,$data->max,$data->step,$data->name) ?>
			</select>
		</div>
		<!-- Select Input / End -->

	</div>

<?php if(isset($data->close_row) && $data->close_row) : ?>
</div>
<?php endif; ?>