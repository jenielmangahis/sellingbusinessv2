<?php $template_loader = new Realteo_Template_Loader; ?>
<!-- Listing Item -->
<div class="listing-item" 
	data-title="<?php the_title(); ?>"
	data-friendly-address="<?php echo esc_attr(get_post_meta( $post->ID, '_friendly_address', true )); ?>" 
	data-address="<?php the_property_address(); ?>" 
	data-price="<?php echo esc_html(get_the_property_price( $post )); echo esc_html('<i class="price_per_scale">').esc_html(get_the_property_price_per_scale()).'</i>'; ?>"
	data-image="<?php echo realteo_get_property_image( $post->ID ); ?>" 
	data-longitude="<?php echo esc_attr(get_post_meta( $post->ID, '_geolocation_lat', true )); ?>" 
	data-latitude="<?php echo esc_attr(get_post_meta( $post->ID, '_geolocation_long', true )); ?>">

	<a href="<?php the_permalink(); ?>" class="listing-img-container">
		<div class="listing-badges">
			<?php if(realteo_is_featured( $post->ID )) : ?><span class="featured"><?php esc_html_e('Featured','realteo'); ?></span><?php endif; ?>
			<?php
			 the_property_offer_type(); ?>
		</div>

		<div class="listing-img-content">
			<span class="listing-price"><?php the_property_price(); ?> <i class="price_per_scale"> <?php the_property_price_per_scale(); ?> </i></span>
			<?php 
			if(!empty(realteo_get_option( 'bookmarks_page' ))) {
				$nonce = wp_create_nonce("realteo_bookmark_this_nonce");

				if( realteo_check_if_bookmarked($post->ID) ) { ?>

					<span class="like-icon liked"></span>

				<?php } else { 
					if(is_user_logged_in()){ ?>

					<span class="save realteo-bookmark-it like-icon with-tip" 
					data-post_id="<?php echo esc_attr($post->ID); ?>" 
					data-tip-content="<?php esc_html_e('Bookmark This Property','realteo'); ?>"
					data-tip-content-bookmarking="<?php esc_html_e('Adding To Bookmarks..','realteo'); ?>"
					data-tip-content-bookmarked="<?php esc_html_e('Bookmarked!','realteo'); ?>"  
					data-nonce="<?php echo esc_attr($nonce); ?>" ></span>
					
					<?php } else { ?>
					
					<span class="save right like-icon with-tip"  data-tip-content="<?php esc_html_e('Login To Bookmark Items','realteo'); ?>"  ></span>

					<?php } ?>
					
				<?php }	
			}	
				if(!empty(realteo_get_option( 'compare_page' ))) {
					$nonce = wp_create_nonce("realteo_compare_this_nonce");
					$compareObj = Realteo_Compare::instance();
					
					$compare_post_ids = $compareObj->get_compare_posts();  ?>
					<span class="compare-button with-tip <?php if(in_array($post->ID,$compare_post_ids)) { echo "already-added"; } ?>"  data-post_id="<?php echo esc_attr($post->ID); ?>" 
						data-nonce="<?php echo esc_attr($nonce); ?>" 
						data-tip-content="<?php esc_html_e('Add To Compare','realteo'); ?>"  
						data-tip-adding-content="<?php esc_html_e('Adding To Compare','realteo'); ?> <?php echo esc_attr('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i>'); ?>" 
						data-tip-added-content="<?php esc_html_e('Added To Compare!','realteo'); ?>"></span>
				<?php } ?>
		</div>
	
		<?php 
		$template_loader->get_template_part( 'content-property-image');  ?>

	</a>
	
	<div class="listing-content">

		<div class="listing-title">
			<h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
			<?php the_property_location_link($post->ID); ?>
			
			<a href="<?php the_permalink(); ?>" class="details button border"><?php esc_html_e('Details','realteo'); ?></a>
		</div>
		<?php 
		$data = array( 'class' => 'listing-details' );
		$template_loader->set_template_data( $data )->get_template_part( 'single-partials/single-property','main-details' );  ?>
		
		<?php if(realteo_get_option( 'realteo_hide_listing_footer', false ) == false){ ?>
		<div class="listing-footer">
	       <?php echo  '<a class="author-link" href="'.esc_url(get_author_posts_url(get_the_author_meta('ID' ))).'"><i class="fa fa-user"></i> '; realteo_agent_name(); echo'</a>'; ?>
			<span><i class="fa fa-calendar-o"></i>
			<?php printf( esc_html__( '%s ago', 'realteo' ), human_time_diff( get_post_time( 'U' ), current_time( 'timestamp' ) ) ); ?>
			</span>
		</div>
		<?php } ?>

	</div>

</div>
<!-- Listing Item / End -->