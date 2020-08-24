<!-- Row -->
<?php if(isset($data->open_row) && $data->open_row) : ?>
<div class="row with-forms">
<?php endif;

if(isset($_GET[$data->name])) {
	$value = sanitize_text_field($_GET[$data->name]);
} else {
	$value = '';
} ?>

	<div class="search-form-<?php echo esc_attr($data->name);?> <?php echo esc_attr($data->class);?> <?php if(isset($data->css_class)) { echo esc_attr($data->css_class); }?>">
		<!-- Select Input -->
		<div class="select-input disabled-first-option">
			<input type="text" 
			name="<?php echo esc_attr($data->name);?>" 
			id="<?php echo esc_attr($data->name); ?>"  
			placeholder="<?php echo esc_attr($data->placeholder); ?>" 
			<?php if(isset($data->unit)) { ?> data-unit="<?php echo esc_attr($data->unit) ?>" <?php } ?> 
			value="<?php if(isset($value)){ echo $value;  } ?>"/>
			<select>
				<option value=""><?php echo esc_attr($data->placeholder);?></option>

				<?php echo get_realteo_intervals_dropdown($data->min,$data->max,$data->step,$data->name); ?>
			</select>
		</div>
		<!-- Select Input / End -->
	</div>

<?php if(isset($data->close_row) && $data->close_row) : ?>
</div>
<?php endif; ?><!-- Row / End -->