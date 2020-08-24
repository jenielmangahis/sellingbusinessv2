<?php 		
if(has_post_thumbnail()){ 
	the_post_thumbnail('findeo-property-grid'); 
} else {
	$gallery = (array) get_post_meta( $id, '_gallery', true );
		$ids = array_keys($gallery);
		if(!empty($ids[0]) && $ids[0] !== 0){ 
			echo  wp_get_attachment_image($ids[0],'findeo-property-grid'); 
		} else { ?>
			<img src="<?php echo get_realteo_placeholder_image(); ?>" alt="">
	<?php } 
} 
?>