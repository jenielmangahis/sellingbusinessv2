<?php

function findeo_recent_properties( $atts, $content ) {
 		extract(shortcode_atts(array(
            'limit'=>'6',
            'layout'=>'standard',
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

        $meta_query = array();

       if(!class_exists('Realteo')){ return ;  }
		$args = array(
            'post_type' => 'property',
            'post_status' => 'publish',
            'posts_per_page' => $limit,
            'orderby' => $orderby,
            'order' => $order,
            'tax_query'              => array(),
            );

        if($featured){
            $args['meta_key'] = '_featured';
            $args['meta_value'] = 'on';
 
        }
 
        if(!empty($exclude_posts)) {
            $exl = is_array( $exclude_posts ) ? $exclude_posts : array_filter( array_map( 'trim', explode( ',', $exclude_posts ) ) );
            $args['post__not_in'] = $exl;
        }

        if(!empty($include_posts)) {
            $inc = is_array( $include_posts ) ? $include_posts : array_filter( array_map( 'trim', explode( ',', $include_posts ) ) );
            $args['post__in'] = $inc;
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
        <div class="carousel">
		<?php 
        if ( $wp_query->have_posts() ) {
		        while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
                <div class="carousel-item">
	               <?php

                   if($layout == 'compact') {

                        $template_loader->get_template_part( 'content-property-compact-shortcode' );  
                   } else {
                        $template_loader->get_template_part( 'content-property' );  
                   }
                    ?>
		        </div>
		 <?php endwhile; // end of the loop. 
		} else {
			
		} ?>
        </div>
		<?php wp_reset_postdata();
        wp_reset_query();

		return ob_get_clean();
	}
