<!-- Content
================================================== -->
<?php $gallery = get_post_meta( $post->ID, '_gallery', true );

if(!empty($gallery)) : ?>
<!-- Slider -->
<div class="fullwidth-property-slider margin-bottom-50">
	<?php 

		foreach ( (array) $gallery as $attachment_id => $attachment_url ) {
			$image = wp_get_attachment_image_src( $attachment_id, 'findeo-gallery' );
			echo '<a href="'.esc_url($image[0]).'" data-background-image="'.esc_attr($image[0]).'" class="item mfp-gallery"></a>';
		}
		
	?>
</div>
<?php endif; ?>