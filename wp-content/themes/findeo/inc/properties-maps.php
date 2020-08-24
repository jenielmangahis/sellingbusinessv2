<?php 
/**
* 
*/
class FindeoMaps 
{
	
	protected $plugin_slug = 'findeo-map';

	function __construct() {

		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		
		wp_register_script( $this->plugin_slug . '-script',  get_template_directory_uri() . '/js/findeo.big.map.min.js', array( 'jquery' ),'1.0', true );
	}

	public function show_map($height=450){
		
			
			$type = 'property';
		
			

	
		$query_args = array( 
			 	'post_type'              => 'property',
        		'post_status'            => 'publish',
        		'posts_per_page'		 => -1,
			);

		
		$markers = array();
		// The Loop
		 $wp_query = new WP_Query( $query_args );
   		if ( $wp_query->have_posts() ):
			$i = 0;
			while( $wp_query->have_posts() ) : 
				$wp_query->the_post(); 
				
				$lat = $wp_query->post->_geolocation_lat;
				$id = $wp_query->post->ID;
					if (!empty($lat)) {
					    
						$title = get_the_title();
						$ibcontet = '';
						ob_start(); ?>
						<a href="<?php the_permalink(); ?>" class="listing-img-container">
							<div class="infoBox-close"><i class="fa fa-times"></i></div>
							<div class="listing-img-content">
								<span class="listing-price"><?php the_property_price(); ?><i><?php the_property_price_per_scale(); ?></i></span>
							</div>
							<?php
							if(has_post_thumbnail()){ 
								the_post_thumbnail('findeo-property-grid'); 
							} else {
								$gallery = get_post_meta( $id, '_gallery', true );
								if(!empty($gallery)){
									$ids = array_keys($gallery);
									$image = wp_get_attachment_image_src( $ids[0], 'findeo-property-grid' );	
									echo '<img src="'.esc_url($image[0]).'" alt="">';
								} else {
									echo '<img src="'.get_realteo_placeholder_image().'" alt="">';
								}
								
							}?>
						</a>
						<div class="listing-content">
							<div class="listing-title"><h4><a href="<?php the_permalink(); ?>"><?php the_title(); ?> </a></h4>
							<p><?php 
								$friendly_address = get_post_meta( $id, '_friendly_address', true );
								$address = get_post_meta( $id, '_address', true );
								echo (!empty($friendly_address)) ? $friendly_address : $address ;
								?>
							</p>
							</div>
						</div>
						<?php 
						
					$ibcontet =  ob_get_clean();
					$ibdata = $ibcontet.'<div class="infoBox-close"><i class="fa fa-times"></i></div>';
					
					$mappoint = array(
						'lat' =>  $lat,
						'lng' =>  $wp_query->post->_geolocation_long,
						'id' => $i,
						'ibcontent' => $ibdata,
					);

					// check if such element exists in the array
				
				    $markers[] = $mappoint;
				    $i++;
				
				}

			 endwhile;
	    
	    endif; 
    	wp_reset_postdata();

		wp_enqueue_script( $this->plugin_slug . '-script' );
		wp_localize_script( $this->plugin_slug . '-script', 'findeo_big_map', $markers );

		$output = '';
		$output .= '	<div id="findeo-map" style="height:'.esc_attr($height).'px;" >
					        <!-- map goes here -->
					    </div>';

		return $output;
		;
	}


	private function find_matching_location($haystack, $needle) {

	    foreach ($haystack as $index => $a) {

	        if ($a['lat'] == $needle['lat']
	                && $a['lng'] == $needle['lng']
	              ) {
	            return $index;
	        }
	    }
	    return null;
	}

}
new FindeoMaps();
?>