<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
$template_loader = new Realteo_Template_Loader;
get_header(get_option('header_bar_style','standard') );
$layout = get_post_meta( $post->ID, '_layout', true ); 
if(empty($layout)) { $layout = realteo_get_option('default_gallery');}?>


<?php if ( have_posts() ) : ?>

<!-- Titlebar
================================================== -->
<div id="titlebar" class="property-titlebar margin-bottom-0  print-only">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				
				<a href="javascript:history.back();" class="back-to-listings"></a>
				<div class="property-title">
					<h2><?php the_title(); ?> <?php the_property_offer_type(); ?> <?php the_property_type(); ?></h2>
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
</div>

<?php 

switch ($layout) {
	case 'style-1':
		$template_loader->get_template_part( 'single-partials/single-property','gallery' );  
		break;

	case 'style-2':
		$template_loader->get_template_part( 'single-partials/single-property','gallery-contact' );  
		break;
	
	case 'style-3':
		$gallery = get_post_meta( $post->ID, '_gallery', true );
		if(sizeof($gallery)==1) {
			$template_loader->get_template_part( 'single-partials/single-property','gallery' ); 
		} else {
			$template_loader->get_template_part( 'single-partials/single-property','gallery-fullwidth' );  
		};
		break;
	
	default:
		$template_loader->get_template_part( 'single-partials/single-property','gallery' );  
		break;
}

?>

<div class="container">
	<div class="row">
		
		<!-- Property Description -->
		<div class="col-lg-8 col-md-7">
			<?php while ( have_posts() ) : the_post();  ?>
			
			<div class="property-description print-only">
				<?php $template_loader->get_template_part( 'single-partials/single-property','print-image' );  ?>
				<?php $template_loader->get_template_part( 'single-partials/single-property','main-details' );  ?>
				
				<?php  $count = strlen(strip_tags(do_shortcode($post->post_content))); ?>
			
				<?php if($count>0) { ?><h3 class="desc-headline"><?php esc_html_e('Description', 'realteo') ?> </h3> <?php }?>
				 <?php if ($count > 850 && realteo_get_option('realteo_single_property_show_more') ) : ?>
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
				<?php $template_loader->get_template_part( 'single-partials/single-property','video' );  ?>
				<?php if(realteo_get_option('realteo_single_property_walkscore_id')) { $template_loader->get_template_part( 'single-partials/single-property','walkscore' ); } ?>
				<?php $template_loader->get_template_part( 'single-partials/single-property','location' );  ?>
				<?php
				if(empty(realteo_get_option('realteo_single_property_similar'))) : 
					$template_loader->get_template_part( 'single-partials/single-property','related' );  
				endif; ?>
				<?php // If comments are open or we have at least one comment, load up the comment template.
				$enable_comments = realteo_get_option( 'realteo_single_property_comments' );
				if($enable_comments) {
					if ( comments_open() || get_comments_number() ) :
						comments_template();  
					endif;	
				}
					?>
			</div>

			<?php endwhile; // End of the loop. ?>
		</div>
		<!-- Property Description / End -->

		
		<!-- Sidebar -->
		<div class="col-lg-4 col-md-5">
			<div class="sidebar sticky right">
				<?php get_sidebar('listing'); ?>
			</div>
		</div>
		<!-- Sidebar / End -->
		
	</div>
</div>

<?php else : ?>

<?php get_template_part( 'content', 'none' ); ?>

<?php endif; ?>


<!-- Footer
================================================== -->
<div class="margin-top-55"></div>

<?php get_footer(); ?>