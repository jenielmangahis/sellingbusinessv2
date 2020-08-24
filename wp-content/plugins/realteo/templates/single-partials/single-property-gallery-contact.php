<!-- Content
================================================== -->
<?php $gallery = get_post_meta( $post->ID, '_gallery', true );
if(!empty($gallery)) : ?>
<div class="container">
	<div class="row margin-bottom-50">
		<div class="col-md-12">

			<!-- Slider Container -->
			<div class="property-slider-container">
				
				<?php
				$agentID = get_post_field( 'post_author', $post->ID );
				
				if($agentID) :	?>
					<!-- Agent Widget -->
					<div class="agent-widget">
						<div class="agent-title">
							<div class="agent-photo"><?php echo get_avatar( $agentID, 72 );  ?></div>
							<div class="agent-details">
								<?php  $agent_data = get_userdata( $agentID ); ?>
								<h4><a href="<?php echo esc_url(get_author_posts_url( $agentID )); ?>"><?php echo $agent_data->first_name; ?> <?php echo $agent_data->last_name; ?></a></h4>
								<?php if(isset($agent_data->phone)): ?><span><i class="sl sl-icon-call-in"></i><a href="tel:<?php echo esc_html($agent_info->phone); ?>"><?php echo esc_html($agent_info->phone); ?></a><?php endif; ?></span>
							</div>
							<div class="clearfix"></div>
						</div>

						<?php  echo do_shortcode( sprintf( '[contact-form-7 id="%s" ]', realteo_get_option( 'agency_form' )) );?>
					</div>
				<!-- Agent Widget / End -->
				<?php endif; ?>
				<!-- Slider -->

				<?php 
				$gallery = get_post_meta( $post->ID, '_gallery', true );
				echo '<div class="property-slider no-arrows">';
				foreach ( (array) $gallery as $attachment_id => $attachment_url ) {
					$image = wp_get_attachment_image_src( $attachment_id, 'findeo-gallery' );
					echo '<a href="'.esc_url($image[0]).'" data-background-image="'.esc_attr($image[0]).'" class="item mfp-gallery"></a>';
				}
				echo '</div>';
				?>
				</div>
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