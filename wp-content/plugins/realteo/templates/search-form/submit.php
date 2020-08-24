<!-- Min Price -->
<?php if(isset($data->open_row) && $data->open_row) : ?>
<div class="row with-forms">
<?php endif; ?>
	<div class="<?php echo esc_attr($data->class);?> <?php if(isset($data->css_class)) { echo esc_attr($data->css_class); }?>">
	
		<button class="button fullwidth"><?php echo (isset($data->placeholder)) ? $data->placeholder : __('Submit','realteo'); ?></button>
	</div>
<?php if(isset($data->close_row) && $data->close_row) : ?>
</div>
<?php endif; ?>