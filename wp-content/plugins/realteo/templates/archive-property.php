<?php
/**
 * The template for displaying properties
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package findeo
 */

get_header(get_option('header_bar_style','standard') );
$template_loader = new Realteo_Template_Loader; 
 
$top_layout = get_option('pp_properties_top_layout','map');

$content_layout = get_option('pp_properties_layout','list');

switch ($top_layout) {
	case 'map':
		$template_loader->get_template_part( 'archive/map' ); 
		break;
	
	case 'searchfw':
		$template_loader->get_template_part( 'archive/search-fw' ); 
		break;
	case 'half':
	case 'disable':
		/*empty*/
		break;

	default:
		$template_loader->get_template_part( 'archive/titlebar' ); 
		break;
}

if($top_layout == 'half') {
	$template_loader->get_template_part( 'archive-property-split' );
} else { ?>



<?php 
$sidebar_side = get_option('pp_properties_sidebar_layout'); 
?>
<!-- Content
================================================== -->
<div class="container <?php echo esc_attr($sidebar_side) ?>">
	<div class="row sticky-wrapper">

		<?php echo ($sidebar_side == 'full-width') ? '<div class="col-md-12">' : '<div class="col-md-8 properties-column-content">' ; ?>
			

			<div class="row margin-bottom-15">
				<?php do_action( 'realto_before_archive' ); ?>
			</div>
				<?php 
				
				switch ($content_layout) {
					case 'list':
					case 'grid':
						$container_class = $content_layout.'-layout'; 
						break;
					
					case 'grid-three':
						$container_class = 'grid-layout-three'; 
						break;

					case 'compact':
						$container_class = $content_layout; 
						break;

					default:
						$container_class = 'list-layout'; 
						break;
				} ?>
				<!-- Listings -->
				<div class="listings-container <?php echo esc_attr($container_class) ?>">
					<?php if($content_layout == 'compact'): ?>
						<div class="row">
					<?php endif; ?>
					<?php
					if ( have_posts() ) : 

						/* Start the Loop */
						while ( have_posts() ) : the_post();

							switch ($content_layout) {
								case 'list':
								case 'grid':
									$template_loader->get_template_part( 'content-property' ); 
									break;
								
								case 'compact':
									$template_loader->get_template_part( 'content-property-compact' );  
									break;

								default:
									$template_loader->get_template_part( 'content-property' );
									break;
							}

						endwhile;

						?>
						<div class="pagination-container margin-top-45 margin-bottom-60">
							<nav class="pagination">
							<?php
								if(function_exists('wp_pagenavi')) { 
									wp_pagenavi(array(
										'next_text' => '<i class="fa fa-chevron-right"></i>',
										'prev_text' => '<i class="fa fa-chevron-left"></i>',
										'use_pagenavi_css' => false,
										));
								} else {
									the_posts_navigation();	
								}?>
							</nav>
						</div>
						<?php
						

					else :

						$template_loader->get_template_part( 'archive/no-found' ); 

					endif; ?>
					<?php if($content_layout == 'compact'): ?>
						</div>
					<?php endif; ?>	
				</div>
		</div>
		<?php if($sidebar_side != 'full-width') : ?>
			<!-- Sidebar
			================================================== -->
			<div class="col-md-4 properties-sidebar-content">
				<?php $template_loader->get_template_part( 'sidebar-realteo' );?>
			</div>
			<!-- Sidebar / End -->
		<?php endif; ?>
	</div>
</div>

<?php get_footer(); ?>
<?php } //eof split ?>
