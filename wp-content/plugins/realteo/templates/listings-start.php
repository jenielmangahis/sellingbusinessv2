<!-- Listings -->
<?php 

if(isset($data)) :
	$style 			= (isset($data->style)) ? $data->style : 'list-layout' ;
	$custom_class 	= (isset($data->class)) ? $data->class : '' ;
	$in_rows	 	= (isset($data->in_rows)) ? $data->in_rows : '' ;

endif; ?>
<div data-counter="<?php echo esc_attr($data->counter); ?>" class="listings-container <?php echo esc_attr($style); ?> <?php echo esc_attr($custom_class); ?>">
<?php if(!empty($in_rows)): ?>
	<div class="row">
<?php endif; ?>