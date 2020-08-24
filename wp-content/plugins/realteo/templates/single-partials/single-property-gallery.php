<!-- Content
================================================== -->
<?php $gallery = get_post_meta( $post->ID, '_gallery', true );

if(!empty($gallery)) : ?>
<div class="container">
	<div class="row margin-bottom-50">
		<div class="col-md-12">
			<!-- Slider -->
			<?php 
			$gallery = get_post_meta( $post->ID, '_gallery', true );
			echo '<div class="property-slider default ">';
			$count = 0;
			foreach ( (array) $gallery as $attachment_id => $attachment_url ) {
				$image = wp_get_attachment_image_src( $attachment_id, 'findeo-gallery' );
				echo '<a href="'.esc_url($image[0]).'" data-background-image="'.esc_attr($image[0]).'" class="item mfp-gallery"></a>';
			}
			echo '</div>';
			?>
			<!-- Slider Thumbs-->
			<?php 
			$gallery = get_post_meta( $post->ID, '_gallery', true );
			if(sizeof($gallery)>1) {
				echo '<div class="property-slider-nav">';
				foreach ( (array) $gallery as $attachment_id => $attachment_url ) {

					$image = wp_get_attachment_image_src( $attachment_id, 'findeo-gallery' );
					echo '<div class="item"><img src="'.esc_url($image[0]).'" alt=""></div>';
				}
				echo '</div>';
			}
			?>
	

		</div>
	</div>
</div>
<?php endif; ?>