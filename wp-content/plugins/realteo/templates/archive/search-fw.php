
<!-- Search
================================================== -->
<section class="search margin-bottom-50">
	<div class="container">
		<div class="row">
			<div class="col-md-12">

				<!-- Title -->
				<h3 class="search-title"><?php esc_html_e('Search','findeo'); ?>
				<?php if(isset($_GET['keyword_search'])) : ?>	<a id="realteo_reset_filters" href="#"><?php esc_html_e('Reset Filters','findeo'); ?></a> <?php endif; ?>
				</h3>
	<!-- Form -->
				<div class="main-search-box no-shadow">
					<?php echo do_shortcode('[realteo_search_form action='.get_post_type_archive_link( 'property' ).'  source="fw"]'); ?>
		
				</div>
				<!-- Box / End -->
			</div>
		</div>
	</div>
</section>