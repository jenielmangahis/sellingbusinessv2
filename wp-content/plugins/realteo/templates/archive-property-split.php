<?php 
$template_loader = new Realteo_Template_Loader; 
  ?>
<!-- Content
================================================== -->
<div class="fs-container">

	<div class="fs-inner-container">

		<!-- Map -->
		<div id="map-container">
		    <div id="map" data-map-zoom="4" data-map-scroll="true">
		        <!-- map goes here -->
		    </div>

		    <!-- Map Navigation -->
			
			<ul id="mapnav-buttons" class="top">
			    <li><a href="#" id="prevpoint" title="<?php esc_attr_e('Previous point on map','realteo'); ?>"><?php esc_html_e('Prev','realteo'); ?></a></li>
			    <li><a href="#" id="nextpoint" title="<?php esc_attr_e('Next point on map','realteo'); ?>"><?php esc_html_e('Next','realteo'); ?></a></li>
			</ul>
		</div>

	</div>

	<div class="fs-inner-container">
		<div class="fs-content">

			<!-- Search -->
			<section class="search margin-bottom-30">

				<div class="row">
					<div class="col-md-12">

						<!-- Title -->
						<h4 class="search-title"><?php esc_html_e('Find Your Home','findeo'); ?>
							<?php if(isset($_GET['keyword_search'])) : ?>	<a id="realteo_reset_filters" href="#"><?php esc_html_e('Reset Filters','findeo'); ?></a> <?php endif; ?>
						</h4>

						<!-- Form -->
						<div class="main-search-box no-shadow">

							<?php echo do_shortcode('[realteo_search_form source="half" more_custom_class="margin-bottom-30"]'); ?>
								<!-- Box / End -->
					</div>
				</div>

			</section>
			<!-- Search / End -->
				<!-- Listings Container -->
			<div class="row fs-listings">
				<div class="row margin-bottom-15">
					<?php do_action( 'realto_before_archive' ); ?>
				</div>
				<?php 
				$content_layout = get_option('pp_properties_layout','list');
				switch ($content_layout) {
					case 'list':
					case 'grid':
						$container_class = $content_layout.'-layout'; 
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
		</div>
	</div>
</div>
<div class="clearfix"></div>

<?php get_footer('empty'); ?>