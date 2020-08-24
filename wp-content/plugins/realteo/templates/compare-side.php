<?php 
$compare_posts = array();
if(is_user_logged_in()){
	 	global $current_user;
	    wp_get_current_user();
	    $user_id =  $current_user->ID;
	    $compare_posts = get_user_meta($user_id, 'realteo-compare-posts', true);
   
} else {
	if(isset( $_COOKIE['realteo-compareposts'] )) {
        $compare_posts = $_COOKIE['realteo-compareposts'];
        $compare_posts = explode(',', $compare_posts);
    }
}?>
<!-- Compare Properties Widget
================================================== -->
<div class="compare-slide-menu" <?php if(empty($compare_posts)) { echo 'style="display:none;"'; } ?>>

	<div class="csm-trigger"></div>

	<div class="csm-content">
		<h4><?php esc_html_e('Compare Properties','realteo') ?> <div class="csm-mobile-trigger"></div></h4>
		<div class="notification closeable warning" style="display: none; margin-bottom: 0; ">
			<?php esc_html_e('You can compare only 4 properties','realteo'); ?>
		</div>
		<div class="csm-properties">
			

			<?php
			if(!empty($compare_posts)) :
				$query = new WP_Query( array( 'post_type' => 'property', 'post__in' => $compare_posts ) );
				if (  $query->have_posts() ) {
 					
				    while (  $query->have_posts() ) {
				 		$nonce = wp_create_nonce("realteo_uncompare_this_nonce");

				        $query->the_post();
				        $post_id = $query->post->ID; ?>
				 		<div class="listing-item compact">
								<a href="<?php echo get_permalink($post_id); ?>" class="listing-img-container">
								<div data-post_id="<?php echo esc_attr($post_id); ?>" data-nonce="<?php echo esc_attr($nonce); ?>" class="remove-from-compare"><i class="fa fa-close"></i></div>
								<div class="listing-badges"><?php the_property_offer_type();?></div>
								<div class="listing-img-content">
									<span class="listing-compact-title"><?php the_title(); ?> <i><?php echo get_the_property_price(  ); ?></i></span>
								</div>
								<?php 		
									if(has_post_thumbnail()){ 
										the_post_thumbnail($post_id,'findeo-property-grid'); 
									} else {
										$gallery = (array) get_post_meta( $post_id, '_gallery', true );
											$ids = array_keys($gallery);
											if(!empty($ids[0]) && $ids[0] !== 0){ 
												echo  wp_get_attachment_image($ids[0],'findeo-property-grid'); 
											} else { ?>
												<img src="<?php echo get_realteo_placeholder_image(); ?>" alt="">
										<?php } 
									} 
								?>
							</a>
						</div>
						<?php
				    }

				 
				}
				// Reset the `$post` data to the current post in main query.
				wp_reset_postdata();
 				wp_reset_query(); 
			endif; ?>
			

		</div>

		<div class="csm-buttons">
			<a href="<?php echo get_permalink(realteo_get_option( 'compare_page' ))?>" class="button"><?php esc_html_e('Compare','realteo'); ?></a>
			<?php $nonce = wp_create_nonce("realteo_uncompare_all_nonce"); ?>
			<a href="#" data-nonce="<?php echo esc_attr($nonce); ?>" class="button reset reset-compare"><?php esc_html_e('Reset','realteo'); ?></a>
		</div>
	</div>

</div>
<!-- Compare Properties Widget / End -->
