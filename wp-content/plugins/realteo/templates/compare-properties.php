<?php 
$properties = '';
if(isset($data)) :
	$properties	 	= (isset($data->properties)) ? $data->properties : '' ;
	$properties_top	 	= (isset($data->properties_top)) ? $data->properties_top : '' ;
endif; 

?>

<div class="nothing-compares-2u" <?php if(!empty($properties)){ ?> style="display:none;" <?php } ?>>
	<section id="properties-not-found" class="margin-bottom-50">
		<h2><?php esc_html_e('No properties to compare','realteo'); ?> </h2>
		<p><?php _e( 'Looks like you haven\'t add anything to compare yet!', 'realteo' ); ?></p>
	</section>
	<?php 
	$template_loader = new Realteo_Template_Loader;
	  $wp_query = new WP_Query( array(
	      'post_type' => 'property',
	      'posts_per_page' => 6,
	      'ignore_sticky_posts' => 1,
	      'orderby' => 'rand',
	   ) );

    if($wp_query->have_posts()) { ?>
	    <h3 class="desc-headline no-border margin-bottom-35 margin-top-60 print-no"><?php esc_html_e('Check Other Properties','realteo'); ?></h3>
	     <div class="carousel">
			<?php 
	        if ( $wp_query->have_posts() ) {
			        while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
	                <div class="carousel-item">
		               <?php
	                        $template_loader->get_template_part( 'content-property' );  
	                    ?>
			        </div>
			 <?php endwhile; // end of the loop. 
			} else {
				
			} ?>
	        </div>
			<?php wp_reset_postdata();
	        wp_reset_query();
	     }
	?>
</div>
	
<?php if(!empty($properties)){?>

<div class="compare-list-container">
	<ul id="compare-list">
		<li class="compare-list-properties">
			<div class="blank-div"></div>
			<?php  
			$nonce = wp_create_nonce("realteo_uncompare_this_nonce");

			foreach ($properties_top as $id => $value) {
				if($id == 0) continue;


					?>
						<div>
							<a href="<?php echo esc_attr($value['url']); ?>">
								<div class="clp-img">
									<?php if($value['image']) { ?>
										<img src="<?php echo esc_attr($value['image']); ?>" alt="">
									<?php } else { ?>
										<img src="<?php echo get_realteo_placeholder_image(); ?>" alt="">
									<?php } ?>
									<span data-post_id="<?php echo esc_attr($value['id']); ?>" data-nonce="<?php echo esc_attr($nonce); ?>" class="remove-from-compare"><i class="fa fa-close"></i></span>
								</div>

								<div class="clp-title">
									<h4><?php echo esc_attr($value['title']); ?></h4>
									<span><?php echo esc_attr($value['price']); ?></span>
								</div>
							</a>
						</div>

					<?php 
				
			}?>
			
		</li>
		<?php 
		 foreach ($properties as $id => $value) {
		 	if( in_array($id,array('title','url','image','price'))) {
		 		continue;
		 	}
		 	if(count(array_filter($value)) == 1){
		 		continue;
		 	};
		 	echo '<li>';
		 	foreach ($value as $key => $_value) {?>
		 		<div><?php echo (!empty($_value)) ?  $_value : '<span class="fa fa-minus"></span>';?></div>
			<?php }
			echo '</li>';
		 	}
		?>

	</ul>
</div>
<?php } ?>

<!-- Compare List / End -->