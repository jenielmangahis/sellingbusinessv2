<div class="notification closeable notice"><p><strong><?php esc_html_e('Notice!','realteo');?></strong> <?php esc_html_e("This is a preview of the business you've submitted, please confirm or edit your submission using buttons at the end of that page.",'realteo'); ?></p><a class="close" href="#"></a></div>
<form method="post" id="property_preview" >

<?php 

$template_loader = new Realteo_Template_Loader; ?>
	<div  class="property-titlebar margin-top-55">

		<div class="row">
			<div class="col-md-12">
				
				<div class="property-title">
					<h2><?php $post = get_post();
 
    				$title = isset( $post->post_title ) ? $post->post_title : ''; echo $title;?> <?php the_property_offer_type(); ?> <?php the_property_type(); ?> </h2>
					<?php if(get_the_property_address()): ?>
					<span>
						<a href="#location" class="listing-address">
							<i class="fa fa-map-marker"></i>
							<?php the_property_address(); ?>
						</a>
					</span>
					<?php endif; ?>
				</div>

				<div class="property-pricing">
					<div><?php the_property_price(); ?></div>
					<div class="sub-price"><?php the_property_price_per_scale(); ?></div>
				</div>


			</div>
		</div>
	</div>
	
		<?php 
			$layout = get_post_meta( $post->ID, '_layout', true ); 
			if(empty($layout)) { $layout == 'style-1'; }
			switch ($layout) {
				case 'style-1':
					$template_loader->get_template_part( 'single-partials/single-property','gallery' );  
					break;

				case 'style-2':
					$template_loader->get_template_part( 'single-partials/single-property','gallery-contact' );  
					break;
				
				case 'style-3':
					$template_loader->get_template_part( 'single-partials/single-property','gallery-fullwidth' );  
					break;
				
				default:
					$template_loader->get_template_part( 'single-partials/single-property','gallery' );  
					break;
			}
		?>
 	<div class="row margin-bottom-50">
		<!-- Property Description -->
		<div class="col-lg-8 col-md-7">

			<div class="property-description">

				<?php $template_loader->get_template_part( 'single-partials/single-property','main-details' );  ?>

				<?php  $count = strlen(strip_tags(do_shortcode($post->post_content))); ?>
				<h3 class="desc-headline"><?php esc_html_e('Description', 'realteo') ?> </h3>
				 <?php if ($count > 850 ) : ?>
				<div class="show-more">
					<?php the_content(); ?>

					<a href="#" class="show-more-button"><?php esc_html_e('Show More', 'realteo') ?> <i class="fa fa-angle-down"></i></a>
				</div>
				<?php else : ?>
					<?php the_content(); ?>
				<?php endif; ?>
				
				<!-- Details -->
				<?php $template_loader->get_template_part( 'single-partials/single-property','details' );  ?>
				<?php $template_loader->get_template_part( 'single-partials/single-property','features' );  ?>
				<?php $template_loader->get_template_part( 'single-partials/single-property','floorplans' );  ?>
				<?php $template_loader->get_template_part( 'single-partials/single-property','location' );  ?>

			</div>

		</div>
		<!-- Property Description / End -->
	</div>

	<div class="row margin-bottom-30">
		<div class="col-md-12">
			<div class="input-with-icon"><i class="sl sl-icon-check"></i><input type="submit" name="continue" id="property_preview_submit_button" class="button realteo-button-submit-listing" value="<?php echo apply_filters( 'submit_property_step_preview_submit_text', __( 'Submit Listing', 'realteo' ) ); ?>" /></div>
			<div class="input-with-icon grey"><i class="sl sl-icon-note"></i><input type="submit" name="edit_property" class="button realteo-button-edit-listing" value="<?php esc_attr_e( 'Edit listing', 'realteo' ); ?>" /></div>
			<input type="hidden" 	name="property_id" value="<?php echo esc_attr( $data->property_id ); ?>" />
			<input type="hidden" 	name="step" value="<?php echo esc_attr( $data->step ); ?>" />
			<input type="hidden" 	name="realteo_form" value="<?php echo $data->form; ?>" />
		</div>
	</div>
</form>
