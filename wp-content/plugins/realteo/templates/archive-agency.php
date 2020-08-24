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
?>

<div id="titlebar">
	<div class="container">
		<div class="row">
			<div class="col-md-12">
				
				<?php
					
					$title = get_option('findeo_agencies_archive_title');
					if($title){ ?>
						<h1 class="page-title"><?php echo $title; ?></h1>
					<?php } else {
						the_archive_title( '<h1 class="page-title">', '</h1>' );
					}
					
					$subtitle = get_option('findeo_agencies_archive_subtitle');
					if($subtitle) {
						echo '<span>'.$subtitle.'</span>';
					}
				?>

	
				<!-- Breadcrumbs -->
				<?php if(function_exists('bcn_display')) { ?>
                    <nav id="breadcrumbs">
                        <ul>
                            <?php bcn_display_list(); ?>
                        </ul>
                    </nav>
                <?php } ?>

			</div>
		</div>
	</div>
</div>

<!-- Content
================================================== -->
<div class="container">
	<div class="row">
			<!-- Main Search Input -->
			<form action="<?php echo get_post_type_archive_link('agency'); ?>" method="GET">
				<div class="col-md-12">
					<div class="main-search-input">
					<?php 
					if ( ! empty( $_GET['s'] ) ) {
						$keywords = sanitize_text_field( $_GET['s'] );
					} else {
						$keywords = '';
					} ?>
						<input type="text" class="ico-01" name="s" placeholder="<?php esc_html_e( 'Type agency name' , 'findeo' ); ?>" 
						value="<?php echo esc_attr( $keywords ); ?>"/>
						<input type="hidden" name="post_type" value="agency">
						<button class="button"><?php esc_html_e( 'Search' , 'findeo' ); ?></button>
					</div>
				</div>
			</form>
		</div>
	<div class="row">
		<div class="col-md-12">
			
			<?php
			if ( have_posts() ) :

				/* Start the Loop */
				while ( have_posts() ) : the_post(); 
					$template_loader->get_template_part( 'archive-agency/content-agency' ); ?>
					
				<?php endwhile;

				the_posts_navigation();

			else :

				get_template_part( 'template-parts/content', 'none' );

			endif; ?>



		</div>
	</div>
</div>

<div class="margin-top-40"></div>
<?php


get_footer(); 