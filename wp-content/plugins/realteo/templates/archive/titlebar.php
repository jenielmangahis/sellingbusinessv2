<!-- Titlebar
================================================== -->
<div class="parallax titlebar"
	data-background="<?php echo get_option('pp_properties_header_upload'); ?>"
	data-color="#333333"
	data-color-opacity="0.7"
	data-img-width="800"
	data-img-height="505">

	<div id="titlebar">
		<div class="container">
			<div class="row">
				<div class="col-md-12">

					<?php
					the_archive_title( '<h1 class="page-title">', '</h1>' );
					$subtitle = get_option('findeo_properties_archive_subtitle');
					if(isset($_GET['keyword_search'])) {
						?>
						<span>
						<?php
						$count = $GLOBALS['wp_query']->found_posts;
						printf(_n(  'We\'ve found <em class="count_properties">%s</em> <em class="count_text">property</em> for you', 'We\'ve found <em class="count_properties">%s</em> <em class="count_text">properties</em> for you' , $count, 'realteo' ), $count); 
						?>
						</span>
						<?php
					} else {
						if($subtitle) {
							'<span>'.$subtitle.'</span>';
						}
					}
					

				?>
					
					<!-- Breadcrumbs -->
					<?php if(function_exists('bcn_display')) { ?>
			        <nav id="breadcrumbs" xmlns:v="http://rdf.data-vocabulary.org/#">
						<ul>
				        	<?php bcn_display_list(); ?>
				        </ul>
					</nav>
					<?php } ?>

				</div>
			</div>
		</div>
	</div>
</div>