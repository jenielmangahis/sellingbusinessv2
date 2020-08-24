<?php 
function findeo_fullwidth_property_slider($atts) {
		 extract(shortcode_atts(array(
		 	'limit'=>'6',
            'orderby'=> 'date',
            'order'=> 'DESC',
            'categories' => '',
            'exclude_posts' => '',
            'include_posts' => '',
            'feature' => '',
            'region' => '',
            '_property_type' => '',
            '_offer_type' => '',
            'featured' => '',
            'from_vs' => 'no',
	        ), $atts));
	 	 
	
	 	$output = '';
        $randID = rand(1, 99); // Get unique ID for carousel

		$args = array(
            'post_type' => 'property',
            'posts_per_page' => $limit,
            'orderby' => $orderby,
            'order' => $order,
            'meta_query' => array( 

            	'relation' => 'OR',
			        array(
			            'key' => '_thumbnail_id',
			            'compare' => 'EXISTS',
			        ),
			        array(
			            'key' => 'findeo_slider_property_image',
			            'compare' => 'EXISTS',
			        ), 

                ),
            );

        if(!empty($exclude_posts)) {
            $exl = is_array( $exclude_posts ) ? $exclude_posts : array_filter( array_map( 'trim', explode( ',', $exclude_posts ) ) );
            $args['post__not_in'] = $exl;
        }

        if($from_vs === 'yes'){
            if(!empty($categories)) {
                $categories         = is_array( $categories ) ? $categories : array_filter( array_map( 'trim', explode( ',', $categories ) ) );
                $args['category__and'] = $categories;
            }
        } else {
            if(!empty($categories)) {
                
                $args['category_name'] = $categories;
            }

        }

        if($featured){
            $args['meta_key'] = '_featured';
            $args['meta_value'] = 'on';
 
        }

        if($feature){
            $feature = is_array( $feature ) ? $feature : array_filter( array_map( 'trim', explode( ',', $feature ) ) );
            foreach ($feature as $key) {
                array_push($args['tax_query'] , array(
                   'taxonomy' =>   'property_feature',
                   'field'    =>   'slug',
                   'terms'    =>   $key,
                   
                ));
            }
        }

        if($region){
            
                array_push($args['tax_query'] , array(
                   'taxonomy' =>   'region',
                   'field'    =>   'slug',
                   'terms'    =>   $region,
                   'operator' =>   'IN'
                   
                ));
            
        }

       if ( $_property_type ) {
                    
            $args['meta_query']['_property_type'] = array(
                'key'     => '_property_type',
                'value'   => $_property_type, 
            );
        }
        if ( $_offer_type ) {
                    
            $args['meta_query']['_offer_type'] = array(
                'key'     => '_offer_type',
                'value'   => $_offer_type, 
            );
        }

        if(!empty($tags)) {
            $tags         = is_array( $tags ) ? $tags : array_filter( array_map( 'trim', explode( ',', $tags ) ) );
            $args['tag__in'] = $tags;
        }
        $i = 0;

        $wp_query = new WP_Query( $args );
      
        $template_loader = new Realteo_Template_Loader;
		ob_start();
        ?>
       
		<div class="fullwidth-home-slider margin-bottom-0">

		<?php 
        if ( $wp_query->have_posts() ) {
		        while ( $wp_query->have_posts() ) : $wp_query->the_post();
		        
		        if(get_post_meta( $wp_query->post->ID, 'findeo_slider_property_image', true )) {
					$image[0] = get_post_meta( $wp_query->post->ID, 'findeo_slider_property_image', true );
		        } else {
		        	$image = wp_get_attachment_image_src(get_post_thumbnail_id($wp_query->post->ID), 'full', true );
		        }
		        
		         ?>
              <!-- Slide -->
				<div data-background-image="<?php echo esc_url($image[0]); ?>" style="background-image: url(<?php echo esc_url($image[0]); ?>)" class="item">
					<div class="container">
						<div class="row">
							<div class="col-md-12">
								<div class="home-slider-container">

									<!-- Slide Title -->
									<div class="home-slider-desc">
										
										<div class="home-slider-price">
											<?php the_property_price(); ?>
										</div>

										<div class="home-slider-title">
											<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
											<?php the_property_location_link($wp_query->post->ID); ?>
										</div>

										<a href="<?php the_permalink(); ?>" class="read-more"><?php esc_html_e('View Details','findeo-shortcodes'); ?> <i class="fa fa-angle-right"></i></a>

									</div>
									<!-- Slide Title / End -->

								</div>
							</div>
						</div>
					</div>
				</div>
		 <?php endwhile; // end of the loop. 
		} else {
			//do_action( "woocommerce_shortcode_{$loop_name}_loop_no_results" );
		} ?>
        </div>
		<?php wp_reset_postdata();
        wp_reset_query();

		return ob_get_clean();

	} ?>