<?php 

	function findeo_testimonials($atts) { 
	    extract(shortcode_atts(array(
	        'title'                  	=>'We collect reviews from our customers so you can get an honest opinion of what an apartment is really like!',
	        'per_page'                  => '6',
	        'orderby'                   => 'date',
	        'order'                     => 'DESC',
	        'exclude_posts'             => '',
	        'include_posts'             => '',
	        'background_color'          => '#222c42',
	        'background'				=> '',
	        'opacity' 					=> '0.6',
	        'from_vs'                   => 'no',
	        'textcolor'                 => 'light',
	        ), $atts));

	    $randID = rand(1, 99);

	    $args = array(
	        'post_type' => array('testimonial'),
	        'showposts' => $per_page,
	        'orderby' => $orderby,
	        'order' => $order,
	    );
	    if(!empty($exclude_posts)) {
	        $exl = explode(",", $exclude_posts);
	        $args['post__not_in'] = $exl;
	    }
	    if(!empty($include_posts)) {
	        $inc = explode(",", $include_posts);
	        $args['post__in'] = $inc;
	    }
	    $wp_query = new WP_Query( $args );
	 
 	    if($from_vs=='yes') {
	    	$background = wp_get_attachment_url( $background );
		}

$output ='';

	       
	    
    		if ( $wp_query->have_posts() ):

    			if(!empty($title)) {
    				$output .= '<div class="col-md-12"><div class="testimonials-subtitle">'.$title.'</div></div>';	
    			}
    			 $output .= '<div class="testimonial-carousel dot-navigation">';
            	while( $wp_query->have_posts() ) : $wp_query->the_post(); 

                    $id = $wp_query->post->ID;
                    $company = get_post_meta($id, 'findeo_pp_company', true);
                    
                    $output .= '
                	<div class="carousel-item">
						<div class="testimonial-box">
							<div class="testimonial">'.get_the_content().'</div>
							<div class="testimonial-author">
								'.get_the_post_thumbnail().'
								<h4>'.get_the_title($id).' <span>'.$company.'</span></h4>
							</div>
						</div>
					</div>';

            	endwhile;  // close the Loop
            	 $output .= '</div>';
    		endif;


    	 wp_reset_postdata();
    	
    	return $output;
	}

?>