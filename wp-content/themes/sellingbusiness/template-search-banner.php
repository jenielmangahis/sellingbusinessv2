<?php
/**
 * Template Name: Home Page with Search Banner
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
<div class="parallax" <?php  echo findeo_get_search_header(); ?> >
<?php } else { ?>
	<div class="main-video-container dark-overlay">
<?php } ?>

	<div class="container">
		<div class="row">
			<div class="col-md-12">
				
				<div class="search-container abc">

					<!-- Form -->
					<h2><?php echo get_option('pp_home_title','Find Your Dream Home'); ?></h2>
						
					<!-- Row With Forms -->
					<?php echo do_shortcode('[realteo_search_form action='.get_post_type_archive_link( 'property' ).' source="home-alt" more_trigger="false"]') ?>
					<!-- Row With Forms / End -->

					<!-- Browse Jobs -->
					<div class="adv-search-btn">
						<?php echo esc_html__( 'Need more search options?', 'findeo'); ?> <a href="<?php echo esc_url(get_post_type_archive_link('property')); ?>"><?php echo esc_html__( 'Advanced Search', 'findeo'); ?></a>
					</div>
					
					<!-- Announce -->
					<div class="announce">
					 <?php $count_jobs = wp_count_posts( 'property', 'readable' ); 
                    	printf( esc_html__( 'We have  %s properties for you!', 'findeo' ), '<strong>' . $count_jobs->publish . '</strong>' ) ?>
						
					</div>

				</div>

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


<?php while ( have_posts() ) : the_post(); ?>
	
	<!-- 960 Container -->
	<div class="container page-container home-page-container">
	    <article <?php post_class(); ?>>
	        <?php the_content(); ?>
	    </article>
	</div>

<?php endwhile; // end of the loop.

get_footer(); ?>