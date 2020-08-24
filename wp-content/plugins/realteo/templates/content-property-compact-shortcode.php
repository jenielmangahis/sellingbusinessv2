<?php $template_loader = new Realteo_Template_Loader; 
$offer_type = get_the_property_offer_type($post);
?>
<!-- Listing Item -->

<div class="listing-item compact" 
	data-title="<?php the_title(); ?>"
	data-friendly-address="<?php echo esc_attr(get_post_meta( $post->ID, '_friendly_address', true )); ?>" 
	data-address="<?php the_property_address(); ?>" 
	data-price="<?php echo get_the_property_price( $post ); echo esc_html('<i class="price_per_scale">').get_the_property_price_per_scale().'</i>'; ?>" 
	data-image="<?php echo realteo_get_property_image( $post->ID ); ?>" 
	data-longitude="<?php echo esc_attr(get_post_meta( $post->ID, '_geolocation_lat', true )); ?>" 
	data-latitude="<?php echo esc_attr(get_post_meta( $post->ID, '_geolocation_long', true )); ?>">
	<a href="<?php the_permalink(); ?>" class="listing-img-container">

		<div class="listing-badges">
			<?php if(realteo_is_featured( $post->ID )) : ?><span class="featured"><?php esc_html_e('Featured','realteo'); ?></span><?php endif; ?>
			<?php the_property_offer_type(); ?>
		</div>

		<div class="listing-img-content">
			<span class="listing-compact-title"><?php the_title(); ?> <i class="price_per_scale"><?php the_property_price(); ?><?php if($offer_type == 'rent') {
			echo ' / '.get_post_meta( $post->ID, '_rental_period', true ); } ?></i></span>
			<?php 
			$data = array( 'class' => 'listing-hidden-content' );
			$template_loader->set_template_data( $data )->get_template_part( 'single-partials/single-property','main-details' );  ?>
			
		</div>
		<?php 
		$template_loader->get_template_part( 'content-property-compact-image');  ?>
	</a>
</div>

<!-- Listing Item / End -->