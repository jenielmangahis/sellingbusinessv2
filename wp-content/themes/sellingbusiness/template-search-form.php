<?php
/**
 * Template Name: Home Page with Search Form
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WPVoyager
 */

get_header(get_option('header_bar_style','standard') ); 
$video = get_option('findeo_search_video_mp4'); 
?>
<!-- Banner
================================================== -->
<?php if(!$video) { ?>
<div class="parallax dark-overlay" <?php  echo findeo_get_search_header(); ?> >
	<div class="main-video-container dark-overlay"><?php putRevSlider( 'homebanner' ); ?></div>
	<div class="parallax-content">
<?php } else { ?>
	<div class="main-video-container dark-overlay">
	<div class="parallax-content">
<?php } ?>
		<div class="container">
			<div class="row">
				<div class="col-md-12">

					<!-- Main Search Container -->
					<div class="main-search-container">
						<h2><?php echo get_option( 'pp_home_title' , 'Find Your Dream Home' ); ?></h2>
						
						<!-- Main Search -->
						<form action="<?php echo get_post_type_archive_link( 'property' ); ?>" class="main-search-form">
							<input type="hidden" name="keyword_search" />
							<?php 
							$tabs = get_option('realteo_home_offer_types_tabs'); 
							$first_row = get_option('realteo_home_search_first_row');?>
							
							<?php if(empty($tabs)) : ?>
							<!-- Type -->
							<div class="search-type">
								<label class="active">
								<input class="first-tab" value="" name="_offer_type" checked="checked" type="radio"><?php esc_html_e('Any Status','findeo'); ?></label>
								<?php $offer_types = realteo_get_offer_types_flat(); 
								foreach ($offer_types as $key => $value) { ?>
									<label><input name="_offer_type" value="<?php echo esc_attr($key); ?>" type="radio"><?php echo stripslashes(esc_html($value)); ?></label>
								<?php } ?>

								
								
								<div class="search-type-arrow"></div>
							</div>
							<?php endif; ?>

							
							<!-- Box -->
							<div class="main-search-box">
								
								<?php if(empty($first_row)) : ?>
								<!-- Main Search Input -->
								<div class="main-search-input larger-input">
									<div class="input-with-icon-2 location" >
										<input type="text" name="keyword_search" id="keyword_search" class="ico-01 <?php if(realteo_get_option_with_name('realteo_general_options','realteo_search_name_autocomplete')) { echo 'title-autocomplete'; } ?>" placeholder="<?php esc_html_e('Enter address e.g. street, city and state or zip','findeo');?>" value=""/>
										<a href="#" class="geoLocation"><i class="fa fa-dot-circle-o"></i></a>
									</div>
									<button class="button"><?php esc_html_e('Search','findeo');?></button>
								</div>
								<?php endif; ?>

								<!-- Row -->
								<?php echo do_shortcode('[realteo_search_form wrap_with_form="false" source="home" more_trigger_style="over" more_custom_class=" " more_text_open="'.esc_html__('More Options','findeo').'" more_text_close="'.esc_html__('Less Options','findeo').'"]') ?>
								
							<div class="clearfix"></div></div>
							<!-- Box / End -->

						</form>
						<!-- Main Search -->

					</div>
					<!-- Main Search Container / End -->

				</div>
			</div>
		</div>
	<?php if($video) { ?>
	<!-- Video -->
	<div class="video-container">
		<video poster="<?php echo get_option('findeo_search_video_poster'); ?>" loop autoplay muted>
			<source src="<?php echo get_option('findeo_search_video_mp4'); ?>" type="video/mp4">
			<!-- <source src="https://archive.org/download/WebmVp8Vorbis/webmvp8.webm" type="video/webm"> -->
		</video>
	</div>
	<?php } ?>
	</div>
</div>


<?php while ( have_posts() ) : the_post(); ?>
	
	<!-- 960 Container -->
	<div class="container page-container home-page-container">
	    <article <?php post_class(); ?>>
	        <?php the_content(); ?>
	    </article>
	</div>

<?php endwhile; // end of the loop.

get_footer(); ?>