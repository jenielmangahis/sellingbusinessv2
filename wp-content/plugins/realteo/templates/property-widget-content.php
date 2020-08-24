<?php $template_loader = new Realteo_Template_Loader; ?>
<div class="item">
	<div class="listing-item compact">

		<a href="<?php the_permalink(); ?>" class="listing-img-container">

			<div class="listing-badges">
				<span class="featured"><?php esc_html_e('Featured','realteo'); ?></span>
				<?php the_property_offer_type(); ?>
			</div>

			<div class="listing-img-content">
				<span class="listing-compact-title"><?php the_title(); ?><i><?php the_property_price(); ?></i></span>
				<?php 
				$data = array( 'class' => 'listing-hidden-content' );
				$template_loader->set_template_data( $data )->get_template_part( 'single-partials/single-property','main-details' );  ?>
				
			</div>

			<?php $type = get_option('findeo_properties_gallery_on_list');
$gallery = get_post_meta( $post->ID, '_gallery', true );

if($type == 'image') {
	if(has_post_thumbnail()){ ?>
		<div class="listing-carousel">
			<div><?php the_post_thumbnail('findeo-property-grid'); ?></div>
		</div>
	<?php } else { ?>
		<div class="listing-carousel">
			<div><img src="<?php echo get_realteo_placeholder_image(); ?>" alt=""></div>
		</div>
	<?php
	}
} else {

	if(!empty($gallery)) {

		echo '<div class="listing-carousel">';
		$count = 0;
			foreach ( (array) $gallery as $attachment_id => $attachment_url ) {
				$count++;
				$image = wp_get_attachment_image_src( $attachment_id, 'findeo-property-grid' );
				echo '<div><img src="'.esc_url($image[0]).'" alt=""></div>';
				if($count == 3) {
					break;
				}
			}
		echo '</div>'; 
	} else if(has_post_thumbnail()){ ?>
		<div class="listing-carousel">
			<div><?php the_post_thumbnail('findeo-property-grid'); ?></div>
		</div>
	<?php } else { ?>
		<div class="listing-carousel">
			<div><img src="<?php echo get_realteo_placeholder_image(); ?>" alt=""></div>
		</div>
		
	<?php } 

}?>
		</a>

	</div>
</div>
<!-- Item / End -->
