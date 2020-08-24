<!-- Row -->
<?php if(isset($data->open_row) && $data->open_row) : ?>
<div class="row with-forms">
<?php endif; ?>
<h4 class="<?php echo esc_attr($data->class);?> <?php if(isset($data->css_class)) { echo esc_attr($data->css_class); }?>"><?php echo esc_attr($data->placeholder);?></h4>

<?php if(isset($data->close_row) && $data->close_row) : ?>
</div>
<?php endif; ?><!-- Row / End -->