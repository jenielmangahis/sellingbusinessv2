<?php 

	/**
	* Headline shortcode
	* Usage: [iconbox title="Service Title" url="#" icon="37"] test [/headline]
	*/
	function findeo_posts_carousel( $atts, $content ) {
 		extract(shortcode_atts(array(
            'limit'=>'6',
            'orderby'=> 'date',
            'order'=> 'DESC',
            'categories' => '',
            'exclude_posts' => '',
            'include_posts' => '',
            'ignore_sticky_posts' => 1,
            'limit_words' => 15,
            'from_vs' => 'no'
            ), $atts));

        $output = '';
        $randID = rand(1, 99); // Get unique ID for carousel

		$args = array(
            'post_type' => 'post',
            'posts_per_page' => $limit,
            'orderby' => $orderby,
            'order' => $order,
            );

        if(!empty($exclude_posts)) {
            $exl = is_array( $exclude_posts ) ? $exclude_posts : array_filter( array_map( 'trim', explode( ',', $exclude_posts ) ) );
            $args['post__not_in'] = $exl;
        }

        
            if(!empty($categories)) {
                
                $args['category_name'] = $categories;
            }


        if(!empty($tags)) {
            $tags         = is_array( $tags ) ? $tags : array_filter( array_map( 'trim', explode( ',', $tags ) ) );
            $args['tag__in'] = $tags;
        }
        $i = 0;

        $wp_query = new WP_Query( $args );
      

		ob_start();


		if ( $wp_query->have_posts() ) {
			?>
        <div class="findeo-post-grid-wrapper">


				<?php while ( $wp_query->have_posts() ) : $wp_query->the_post();
				$i++;
                $id = $wp_query->post->ID;
                $thumb = get_post_thumbnail_id();
                $img_url = wp_get_attachment_url( $thumb,'full');
                $image_size = get_post_meta($wp_query->post->ID, 'sphene_pf_sizet', true); 
                $image = aq_resize( $img_url, 620, 450, true, false, true ); //resize & crop the image 
                        ?>
			
            <div class="col-md-4">
            <!-- Blog Post -->
                <div class="blog-post">
                    
                    <!-- Img -->
                    <a href="<?php the_permalink(); ?>" class="post-img">
                        <?php 
                        if($image) { ?>
                            <img src="<?php echo $image[0]; ?>" alt="">
                        <?php } else { 
                            the_post_thumbnail(); 
                        } ?>
                    </a>
                    
                    <!-- Content -->
                    <div class="post-content">
                        <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                        <p><?php 
                        $excerpt = get_the_excerpt();
                        echo findeo_string_limit_words($excerpt,$limit_words); ?></p>

                        <a href="<?php the_permalink(); ?>" class="read-more"><?php esc_html_e('Read More','findeo-shortcodes') ?> <i class="fa fa-angle-right"></i></a>
                    </div>

                </div>
            </div>
		
			<?php 
			 endwhile; // end of the loop. 
		} else {
			//do_action( "woocommerce_shortcode_{$loop_name}_loop_no_results" );
		}
        ?>
        </div>
        <?php 
		wp_reset_postdata();

		return ob_get_clean();
	}

?>