<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Realteo_Search class.
 */
class Realteo_Search {

	/**
	 * Constructor
	 */
	public function __construct() {


		add_action( 'pre_get_posts', array( $this, 'pre_get_posts_properties' ), 0 );
		//add_action( 'parse_tax_query', array( $this, 'parse_tax_query_properties' ), 1 );
		add_shortcode( 'realteo_search_form', array($this, 'output_search_form'));
		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );

		if(realteo_get_option_with_name('realteo_general_options','realteo_search_name_autocomplete')) {
			add_action( 'wp_print_footer_scripts', array( __CLASS__, 'wp_print_footer_scripts' ), 11 );
	        add_action( 'wp_ajax_realteo_incremental_property_suggest', array( __CLASS__, 'wp_ajax_realteo_incremental_property_suggest' ) );
	        add_action( 'wp_ajax_nopriv_realteo_incremental_property_suggest', array( __CLASS__, 'wp_ajax_realteo_incremental_property_suggest' ) );
	    }
	}

	static function wp_print_footer_scripts() {  
		?>
    <script type="text/javascript">
        (function($){
        $(document).ready(function(){

            $( '#keyword_search.title-autocomplete' ).autocomplete({
                
                source: function(req, response){
                    $.getJSON('<?php echo admin_url( 'admin-ajax.php' ); ?>'+'?callback=?&action=realteo_incremental_property_suggest', req, response);
                },
                select: function(event, ui) {
                    window.location.href=ui.item.link;
                },
                minLength: 3,
            }); 
         });

        })(this.jQuery);

           
    </script><?php
    }

    static function wp_ajax_realteo_incremental_property_suggest() {
    
        $suggestions = array();
        $posts = get_posts( array(
            's' => $_REQUEST['term'],
            'post_type'     => 'property',
        ) );
        global $post;
        $results = array();
        foreach ($posts as $post) {
            setup_postdata($post);
            $suggestion = array();
            $suggestion['label'] =  html_entity_decode($post->post_title, ENT_QUOTES, 'UTF-8');
            $suggestion['link'] = get_permalink($post->ID);
            
            $suggestions[] = $suggestion;
        }
        // JSON encode and echo
            $response = $_GET["callback"] . "(" . json_encode($suggestions) . ")";
            echo $response;
             // Don't forget to exit!
            exit;

    }

	public function add_query_vars($vars) {
		
		$new_vars = $this->build_available_query_vars();
	    $vars = array_merge( $new_vars, $vars );
		return $vars;

	}

	public static function build_available_query_vars(){
		$query_vars = array();
		$taxonomy_objects = get_object_taxonomies( 'property', 'objects' );
        foreach ($taxonomy_objects as $tax) {
        	array_push($query_vars, 'tax-'.$tax->name);
        }
        $price_fields = Realteo_Meta_Boxes::meta_boxes_price();
        foreach ($price_fields['fields'] as $key => $field) {
        	array_push($query_vars, $field['id']);
        	array_push($query_vars, $field['id'].'_min');
        	array_push($query_vars, $field['id'].'_max');
        }
        $main_details = Realteo_Meta_Boxes::meta_boxes_main_details();
        foreach ($main_details['fields'] as $key => $field) {
            	array_push($query_vars, $field['id']);
            	array_push($query_vars, $field['id'].'_min');
        		array_push($query_vars, $field['id'].'_max');
        }
        $details = Realteo_Meta_Boxes::meta_boxes_details();
            foreach ($details['fields'] as $key => $field) {
               	array_push($query_vars, $field['id']);
               	array_push($query_vars, $field['id'].'_min');
        		array_push($query_vars, $field['id'].'_max');
        } 
        $location = Realteo_Meta_Boxes::meta_boxes_location();
            foreach ($location['fields'] as $key => $field) {
              	array_push($query_vars, $field['id']);
              	array_push($query_vars, $field['id'].'_min');
        		array_push($query_vars, $field['id'].'_max');
        } 
        
		return $query_vars;

	}

	public function pre_get_posts_properties( $query ) {

		if ( is_admin() || ! $query->is_main_query() ){
			return;

		}
	
		if ( is_post_type_archive( 'property' ) || is_author() || is_tax()) {
			


			$ordering_args = Realteo_Property::get_properties_ordering_args( );
			
			if(isset($ordering_args['meta_key']) && $ordering_args['meta_key'] != '_featured' ){
				$query->set('meta_key', $ordering_args['meta_key']);
			} 



			$query->set('orderby', $ordering_args['orderby']);
        	$query->set('order', $ordering_args['order'] );



			$keyword = get_query_var( 'keyword_search' );

	        if ( $keyword  ) {
	        	$api_key = realteo_get_option( 'realteo_maps_api_server' );
	        	$radius = get_query_var('search_radius');
	        	if(empty($radius)){
	        		$radius =  realteo_get_option('realteo_maps_default_radius');
	        	}
				$radius_type = realteo_get_option('radius_unit','km');
				if(!empty($keyword) && !empty($radius)) {
					//search by google
					$post_ids = array(0);
					
					if($api_key) {
						$latlng = realteo_geocode($keyword);

						$nearbyposts = realteo_get_nearby_properties($latlng[0], $latlng[1], $radius, $radius_type ); 

						realteo_array_sort_by_column($nearbyposts,'distance');
						$post_ids = array_unique(array_column($nearbyposts, 'post_id'));

					}
					
					if(empty($post_ids)) {
						
						$post_ids = array(0);
					}

					if( realteo_get_option('include_text_search') ){
						global $wpdb;
						// Trim and explode keywords
						$keywords = array_map( 'trim', explode( ',', $keyword  ) );
					
						// Setup SQL
						$posts_keywords_sql    = array();
						$postmeta_keywords_sql = array();
						// Loop through keywords and create SQL snippets
						
							// Create post meta SQL
							$postmeta_keywords_sql[] = " meta_value LIKE '%" . esc_sql( $keywords[0] ) . "%' ";
							// Create post title and content SQL
							$posts_keywords_sql[]    = " post_title LIKE '%" . esc_sql( $keywords[0] ) . "%' OR post_content LIKE '%" . esc_sql(  $keywords[0] ) . "%' ";
						

						// Get post IDs from post meta search
						$text_search_post_ids = $wpdb->get_col( "
						    SELECT DISTINCT post_id FROM {$wpdb->postmeta}
						    WHERE " . implode( ' OR ', $postmeta_keywords_sql ) . "
						" );
			
						// Merge with post IDs from post title and content search

						$text_search_post_ids = array_merge( $text_search_post_ids, $wpdb->get_col( "
						    SELECT ID FROM {$wpdb->posts}
						    WHERE ( " . implode( ' OR ', $posts_keywords_sql ) . " )
						    AND post_type = 'property'
						   
						" ), array( 0 ) );

						$post_ids = array_merge($text_search_post_ids,$post_ids);
					}
				} else {
					//search by text
					global $wpdb;
					// Trim and explode keywords
					$keywords = array_map( 'trim', explode( ',', $keyword  ) );
				
					// Setup SQL
					$posts_keywords_sql    = array();
					$postmeta_keywords_sql = array();
					// Loop through keywords and create SQL snippets
					
						// Create post meta SQL
						$postmeta_keywords_sql[] = " meta_value LIKE '%" . esc_sql( $keywords[0] ) . "%' ";
						// Create post title and content SQL
						$posts_keywords_sql[]    = " post_title LIKE '%" . esc_sql( $keywords[0] ) . "%' OR post_content LIKE '%" . esc_sql(  $keywords[0] ) . "%' ";
					

					// Get post IDs from post meta search
					$post_ids = $wpdb->get_col( "
					    SELECT DISTINCT post_id FROM {$wpdb->postmeta}
					    WHERE " . implode( ' OR ', $postmeta_keywords_sql ) . "
					" );
		
					// Merge with post IDs from post title and content search

					$post_ids = array_merge( $post_ids, $wpdb->get_col( "
					    SELECT ID FROM {$wpdb->posts}
					    WHERE ( " . implode( ' OR ', $posts_keywords_sql ) . " )
					    AND post_type = 'property'
					   
					" ), array( 0 ) );

				}


	        
			} /*eof keywoard*/

			if ( ! empty( $post_ids ) ) {
		        $query->set( 'post__in', $post_ids );
		    }

			$query->set('post_type', 'property');
	 		$args = array();
			$tax_query = array();
			
			$tax_query = array(
		        'relation' => 'AND',
		    );
			$taxonomy_objects = get_object_taxonomies( 'property', 'objects' );
            foreach ($taxonomy_objects as $tax) {
            	$get_tax = get_query_var( 'tax-'.$tax->name  );
            	if(is_array($get_tax)){
            		$tax_query[$tax->name] = array('relation'=> 'OR');

            		foreach ($get_tax as $key => $value) {
			    		array_push($tax_query[$tax->name], array(
				           'taxonomy' =>   $tax->name,
				           'field'    =>   'slug',
				           'terms'    =>   $value,
				           
				        ));
				        
			    	}
			    	
            	} else {
	            	if( $get_tax ){
				    	$term = get_term_by('slug', $get_tax, $tax->name);
				    	if($term){
					    	array_push($tax_query, array(
					           'taxonomy' =>  $tax->name,
					           'field'    =>  'slug',
					           'terms'    =>  $term->slug,
					           'operator' =>  'IN'
					        ));
						}
				    }
			 	}
            }
            

			$query->set('tax_query', $tax_query);	

			$available_query_vars = $this->build_available_query_vars();

			$meta_queries = array();
			foreach ($available_query_vars as $key => $meta_key) {
				if( substr($meta_key,0, 4) == "tax-") {
					continue;
				}
				if (substr($meta_key, -4) == "_min") {
					if(in_array($meta_key, array('_price_min','_price_max','_price'))){
						$value = get_query_var( $meta_key );
						//$meta_min = (int)preg_replace('/[^0-9]/', '', $value);
						$meta_min = (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
					} else {
						$meta_min = get_query_var( $meta_key );	
					}
					

				}
				if (substr($meta_key, -4) == "_max") {
					if(in_array($meta_key, array('_price_min','_price_max','_price'))){
						$value = get_query_var( $meta_key );
						
						//$meta_max = (int)preg_replace('/[^0-9]/', '', $value);
						$meta_max =  (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
					} else {
						$meta_max = get_query_var( $meta_key );	
					}
					

				}

				if(!empty($meta_min) && !empty($meta_max) ) {
			
					$meta_queries[] = array(
			            'key' =>  substr($meta_key,0, -4),
			            'value' => array($meta_min, $meta_max),
			            'compare' => 'BETWEEN',
			            'type' => 'NUMERIC'
			        );
			        $meta_max = false;
			        $meta_min = false;

				} else if(!empty($meta_min) && empty($meta_max) ) {
					$meta_queries[] = array(
			            'key' =>  substr($meta_key,0, -4),
			            'value' => $meta_min,
			            'compare' => '>=',
			            'type' => 'NUMERIC'
			        );
			        $meta_max = false;
			        $meta_min = false;
				} else if(empty($meta_min) && !empty($meta_max) ) {
					$meta_queries[] = array(
			            'key' =>  substr($meta_key,0, -4),
			            'value' => $meta_max,
			            'compare' => '<=',
			            'type' => 'NUMERIC'
			        );
			        $meta_max = false;
			        $meta_min = false;
				}
				
				if (substr($meta_key, -4) == "_min" || substr($meta_key, -4) == "_max") { continue; }
				$meta = get_query_var( $meta_key );

				if ( $meta ) {

					if(is_array($meta)){

						$meta_queries[] = array(
			                'key'     => $meta_key,
			                'value'   => $meta, 
			                'compare' => 'IN',
			            );	
				
					} else {
						$meta_queries[] = array(
			                'key'     => $meta_key,
			                'value'   => $meta, 
			            );	
					}
					
				}
				
			}

			
			
			if( isset($ordering_args['meta_key']) && $ordering_args['meta_key'] == '_featured' ){

				$meta_queries[] = array(
					'relation' => 'OR',
					'featured_clause' => array(
						'key'     => '_featured',
						'value'   => 'on',
						'compare' => '='
					),
					'notexistfeatured_clause' => array(
						'key'     => '_featured',
						'compare' => 'NOT EXISTS'
					),
					'notfeatured_clause' => array(
						'key'     => '_featured',
						'value'   => 'on',
						'compare' => '!='
					),
				);
				$query->set('orderby', array( 'featured_clause' => 'DESC'));
			}
		
			if(!empty($meta_queries)){
				$query->set('meta_query', array(
		            'relation' => 'AND',
		            $meta_queries 
		        ) );	

	        }
	    
			
	    } 

	    return $query;
	} /*eof function*/

	public static function get_search_fields(){
		$scale = realteo_get_option( 'scale', 'sq ft' );
		$search_fields = array(
			'order' => array(
				'placeholder'	=> __( 'Hidden order', 'realteo' ),
				'key'			=> 'realteo_order',
				'class'			=> 'col-md-12',
				'open_row'		=> true,
				'close_row'		=> true,
				'name'			=> 'realteo_order',
		    	'priority'		=> 1,
		    	'place'			=> 'main',
				'type' 			=> 'hidden',
			),	
			'keyword_search' => array(
				
				'placeholder'	=> __( 'Address e.g. steet or city', 'realteo' ),
				'key'			=> 'keyword_search',
				'class'			=> 'col-md-12',
				'open_row'		=> true,
				'close_row'		=> true,
				'name'			=> 'keyword_search',
		    	'priority'		=> 2,
		    	'place'			=> 'main',
				'type' 			=> 'location',
			),	
			'offer_type' => array(
				
				'placeholder'	=> __( 'Any Offer type', 'realteo' ),
				'key'			=> '_offer_type',
				'class'			=> 'col-md-12',
				'open_row'		=> true,
				'close_row'		=> true,
				'name'			=> '_offer_type',
		    	'priority'		=> 4,
		    	'place'			=> 'main',
				'type' 			=> 'select',
				'options_source'=> 'predefined',
				'options_cb' 	=> 'realteo_get_offer_types',
			),					
			'property_type' => array(
				
				'placeholder'	=> __( 'Any Property type', 'realteo' ),
				'key'			=> '_property_type',
				'class'			=> 'col-md-12',
				'open_row'		=> true,
				'close_row'		=> true,
				'name'			=> '_property_type',
		    	'priority'		=> 3,
		    	'place'			=> 'main',
		    	'type' 			=> 'select',
				'options_source'=> 'predefined',
				'options_cb' 	=> 'realteo_get_property_types',
			),				
			'region' => array(
				
				'placeholder'	=> __( 'Any Region', 'realteo' ),
				'key'			=> '_region',
				'class'			=> 'col-md-12',
				'open_row'		=> true,
				'close_row'		=> true,
				'name'			=> 'tax-region',
		    	'priority'		=> 5,
		    	'place'			=> 'main',
				'type' 			=> 'select-taxonomy',
				'taxonomy' 		=> 'region',
			),				
			'bedrooms' => array(
				
				'placeholder'	=> __( 'Beds', 'realteo' ),
				'key'			=> '_bedrooms',
				'class'			=> 'col-md-6',
				'open_row'		=> true,
				'close_row'		=> false,
				'name'			=> '_bedrooms',
		    	'priority'		=> 6,
				'type' 			=> 'input-select',
				'place'			=> 'main',
				'max' 			=> 10,
				'min' 			=> 1,
				'step'			=> 1,
			),				
			'baths' => array(
				
				'placeholder'	=> __( 'Baths', 'realteo' ),
				'key'			=> '_bathrooms',
				'class'			=> 'col-md-6',
				'open_row'		=> false,
				'close_row'		=> true,
				'name'			=> '_bathrooms',
		    	'priority'		=> 7,
		    	'place'			=> 'main',
				'type' 			=> 'input-select',
				'place'			=> 'main',
				'max' 			=> 10,
				'min' 			=> 1,
				'step'			=> 1,
			),			
			'area_range' => array(
				
				'placeholder' 	=> __( 'Area range', 'realteo' ),
				'key'			=> '_area',
				'class'			=> 'col-md-12',
				'css_class'		=> 'margin-top-25',
				'open_row'		=> true,
				'close_row'		=> true,
				'name'			=> '_area',
		    	'priority'		=> 8,
		    	'place'			=> 'main',
				'type' 			=> 'slider',
				'max' 			=> 'auto',
				'min' 			=> 'auto',
				'unit' 			=> apply_filters('realteo_scale',$scale),
			),				
			'price_range' => array(
				
				'placeholder' 	=> __( 'Price range', 'realteo' ),
				'key'			=> '_price',
				'class'			=> 'col-md-12',
				'css_class'		=> 'margin-bottom-15',
				'open_row'		=> true,
				'close_row'		=> true,
				'name'			=> '_price',
		    	'priority'		=> 9,
		    	'place'			=> 'main',
				'type' 			=> 'slider',
				'max' 			=> 'auto',
				'min' 			=> 'auto',
				'unit' 			=> realteo_get_option( 'currency' ),
			),			
			'features' => array(
				
				'placeholder' 	=> __( 'Features', 'realteo' ),
				'key'			=> '_features',
				'class'			=> 'col-md-12',
				'open_row'		=> true,
				'close_row'		=> true,
				'name'			=> 'tax-property_feature',
		    	'priority'		=> 10,
		    	'options'		=> array(),
		    	'place'			=> 'adv',
				'type' 			=> 'multi-checkbox',
				'taxonomy' 		=> 'property_feature',
			),
		);

		$fields = realteo_sort_by_priority( apply_filters( 'realteo_search_fields', $search_fields ) );

		return $fields;
	}

	public static function get_search_fields_fw(){
		$scale = realteo_get_option( 'scale', 'sq ft' );
		$search_fields = array(
			'order' => array(
				'placeholder'	=> __( 'Hidden order', 'realteo' ),
				'key'			=> 'realteo_order',
				'class'			=> 'col-md-12',
				'open_row'		=> true,
				'close_row'		=> true,
				'name'			=> 'realteo_order',
		    	'priority'		=> 1,
		    	'place'			=> 'main',
				'type' 			=> 'hidden',
			),	
			'offer_type' => array(
				
				'placeholder'	=> __( 'Any Offer type', 'realteo' ),
				'key'			=> '_offer_type',
				'class'			=> 'col-md-3',
				'open_row'		=> true,
				'close_row'		=> false,
				'name'			=> '_offer_type',
		    	'priority'		=> 1,
		    	'place'			=> 'main',
				'type' 			=> 'select',
				'options_source'=> 'predefined',
				'options_cb' 	=> 'realteo_get_offer_types',
			),					
			'property_type' => array(
				
				'placeholder'	=> __( 'Any type', 'realteo' ),
				'key'			=> '_property_type',
				'class'			=> 'col-md-3',
				'open_row'		=> false,
				'close_row'		=> false,
				'name'			=> '_property_type',
		    	'priority'		=> 1,
		    	'place'			=> 'main',
				'type' 			=> 'select',
				'options_source'=> 'predefined',
				'options_cb' 	=> 'realteo_get_property_types',
			),					
			'keyword_search' => array(
				
				'placeholder'	=> __( 'Enter address e.g. steet, city or state', 'realteo' ),
				'key'			=> '_keyword_search',
				'class'			=> 'col-md-4',
				'open_row'		=> false,
				'close_row'		=> false,
				'name'			=> 'keyword_search',
		    	'priority'		=> 1,
		    	'place'			=> 'main',
				'type' 			=> 'location',
			),				
			'submit' => array(
				'class'			=> 'col-md-2',
				'open_row'		=> false,
				'close_row'		=> true,
				'name'			=> 'submit',
				'place'			=> 'main',
				'type' 			=> 'submit',
				'placeholder' 		=> __( 'Search', 'realteo' ),
			),				
				
			'area_range' => array(
				
				'placeholder' 	=> __( 'Area range', 'realteo' ),
				'key'			=> '_area',
				'class'			=> 'col-md-3',
				'open_row'		=> true,
				'close_row'		=> false,
				'name'			=> '_area',
		    	'priority'		=> 1,
		    	'place'			=> 'main',
				'type' 			=> 'double-input',
				'max' 			=> 1500,
				'min' 			=> 100,
				'step'			=> 100,
				'unit' 			=> apply_filters('realteo_scale',$scale),
			),				
			'price_range' => array(
				
				'placeholder' 	=> __( 'Price range', 'realteo' ),
				'key'			=> '_price',
				'class'			=> 'col-md-3',
				'open_row'		=> false,
				'close_row'		=> true,
				'name'			=> '_price',
		    	'priority'		=> 1,
		    	'place'			=> 'main',
				'type' 			=> 'double-input',
				'max' 			=> 150000,
				'min' 			=> 10000,
				'step'			=> 10000,
				'unit' 			=> realteo_get_option( 'currency' ),
			),			
			
			'age' => array(
				
				'placeholder'	=> __( 'Age of Home', 'realteo' ),
				'key'			=> '_age',
				'class'			=> 'col-md-3',
				'open_row'		=> true,
				'close_row'		=> false,
				'name'			=> '_age',
		    	'priority'		=> 1,
				'type' 			=> 'select',
				'place'			=> 'adv',
				'options_source'=> 'custom',
				'options' 		=> array(
					'0 - 1 Years' => __( '0 - 1 Years', 'realteo' ),
					'2 - 5 Years' => __( '2 - 5 Years', 'realteo' ),
					'6 - 10 Years' => __( '6 - 10 Years', 'realteo' ),
					'11 - 20 Years' => __( '11 - 20 Years', 'realteo' ),
					'21 - 50 Years' => __( '21 - 50 Years', 'realteo' ) 
					),
			),			
			'rooms' => array(
				
				'placeholder'	=> __( 'Rooms', 'realteo' ),
				'key'			=> '_rooms',
				'class'			=> 'col-md-3',
				'open_row'		=> false,
				'close_row'		=> false,
				'name'			=> '_rooms',
		    	'priority'		=> 1,
				'type' 			=> 'select',
				'place'			=> 'adv',
				'options_source'=> 'custom',
				'options' 		=> array('1','2','3','4','5','6','7','8','9','10'),
			),				
			'bedrooms' => array(
				
				'placeholder'	=> __( 'Beds', 'realteo' ),
				'key'			=> '_bedrooms',
				'class'			=> 'col-md-3',
				'open_row'		=> false,
				'close_row'		=> false,
				'name'			=> '_bedrooms',
		    	'priority'		=> 1,
				'type' 			=> 'select',
				'place'			=> 'adv',
				'options_source'=> 'custom',
				'options' 		=> array('1','2','3','4','5','6','7','8','9','10'),
			),				
			'baths' => array(
				
				'placeholder'	=> __( 'Baths', 'realteo' ),
				'key'			=> '_bathrooms',
				'class'			=> 'col-md-3',
				'open_row'		=> false,
				'close_row'		=> true,
				'name'			=> '_bathrooms',
		    	'priority'		=> 1,
		    	'place'			=> 'adv',
				'type' 			=> 'select',
				'options_source'=> 'custom',
				'options' 		=> array('1','2','3','4','5','6','7','8','9','10'),
			),
			'features' => array(
				
				'placeholder' 	=> __( 'Features', 'realteo' ),
				'key'			=> '_features',
				'class'			=> 'col-md-12',
				'open_row'		=> true,
				'close_row'		=> true,
				'options'		=> array(),
				'name'			=> 'tax-property_feature',
				'taxonomy' 		=> 'property_feature',
		    	'priority'		=> 1,
		    	'place'			=> 'adv',
				'type' 			=> 'multi-checkbox-row',
			),		
		);

		return apply_filters('realteo_search_fields_fw',$search_fields);
	}

	public static function get_search_fields_half(){
		$scale = realteo_get_option( 'scale', 'sq ft' );
		$search_fields = array(
			'order' => array(
				'placeholder'	=> __( 'Hidden order', 'realteo' ),
				'key'			=> 'realteo_order',
				'class'			=> 'col-md-12',
				'open_row'		=> true,
				'close_row'		=> true,
				'name'			=> 'realteo_order',
		    	'priority'		=> 1,
		    	'place'			=> 'main',
				'type' 			=> 'hidden',
			),	
			'keyword_search' => array(
				
				'placeholder'	=> __( 'Enter address e.g. steet, city or state', 'realteo' ),
				'key'			=> 'keyword_search',
				'class'			=> 'col-fs-6',
				'open_row'		=> true,
				'close_row'		=> false,
				'name'			=> 'keyword_search',
		    	'priority'		=> 1,
		    	'place'			=> 'main',
				'type' 			=> 'location',
			),	
			'offer_type' => array(
				
				'placeholder'	=> __( 'Any Offer type', 'realteo' ),
				'key'			=> '_offer_type',
				'class'			=> 'col-fs-3',
				'open_row'		=> false,
				'close_row'		=> false,
				'name'			=> '_offer_type',
		    	'priority'		=> 1,
		    	'place'			=> 'main',
				'type' 			=> 'select',
				'options_source'=> 'predefined',
				'options_cb' 	=> 'realteo_get_offer_types',
			),					
			'property_type' => array(
				
				'placeholder'	=> __( 'Any type', 'realteo' ),
				'key'			=> '_property_type',
				'class'			=> 'col-fs-3',
				'open_row'		=> false,
				'close_row'		=> true,
				'name'			=> '_property_type',
		    	'priority'		=> 1,
		    	'place'			=> 'main',
				'type' 			=> 'select',
				'options_source'=> 'predefined',
				'options_cb' 	=> 'realteo_get_property_types',
			),	
			'area_range' => array(
				
				'placeholder' 	=> __( 'Area range', 'realteo' ),
				'key'			=> '_area',
				'class'			=> 'col-fs-3',
				'open_row'		=> true,
				'close_row'		=> false,
				'name'			=> '_area',
		    	'priority'		=> 1,
		    	'place'			=> 'main',
				'type' 			=> 'double-input',
				'max' 			=> 1500,
				'min' 			=> 100,
				'step'			=> 100,
				'unit' 			=> apply_filters('realteo_scale',$scale),
			),			

			'price_range' => array(
				
				'placeholder' 	=> __( 'Price range', 'realteo' ),
				'key'			=> '_price',
				'class'			=> 'col-fs-3',
				'open_row'		=> false,
				'close_row'		=> true,
				'name'			=> '_price',
		    	'priority'		=> 1,
		    	'place'			=> 'main',
				'type' 			=> 'double-input',
				'max' 			=> 150000,
				'min' 			=> 10000,
				'step'			=> 10000,
				'unit' 			=> realteo_get_option( 'currency' ),
			),				
						
			'submit' => array(
				'class'			=> 'button fs-map-btn',
				'open_row'		=> false,
				'close_row'		=> false,
				'place'			=> 'main',
				'name' 			=> 'submit',
				'type' 			=> 'submit',
				'placeholder'	=> __( 'Search', 'realteo' ),
			),			
				

			'age' => array(
				
				'placeholder'	=> __( 'Age of Home', 'realteo' ),
				'key'			=> '_age',
				'class'			=> 'col-fs-3',
				'open_row'		=> true,
				'close_row'		=> false,
				'name'			=> '_age',
		    	'priority'		=> 1,
				'type' 			=> 'select',
				'place'			=> 'adv',
				'options_source'=> 'custom',
				'options' 		=> array(
					'0 - 1 Years' => __( '0 - 1 Years', 'realteo' ),
					'2 - 5 Years' => __( '2 - 5 Years', 'realteo' ),
					'6 - 10 Years' => __( '6 - 10 Years', 'realteo' ),
					'11 - 20 Years' => __( '11 - 20 Years', 'realteo' ),
					'21 - 50 Years' => __( '21 - 50 Years', 'realteo' ) 
					),
			),		
			'bedrooms' => array(
				
				'placeholder'	=> __( 'Beds', 'realteo' ),
				'key'			=> '_bedrooms',
				'class'			=> 'col-fs-3',
				'open_row'		=> false,
				'close_row'		=> false,
				'name'			=> '_bedrooms',
		    	'priority'		=> 1,
				'type' 			=> 'select',
				'place'			=> 'adv',
				'options_source'=> 'custom',
				'options' 		=> array('1','2','3','4','5','6','7','8','9','10'),
			),		
			'rooms' => array(
				
				'placeholder'	=> __( 'Rooms', 'realteo' ),
				'key'			=> '_rooms',
				'class'			=> 'col-fs-3',
				'open_row'		=> false,
				'close_row'		=> false,
				'name'			=> '_rooms',
		    	'priority'		=> 1,
				'type' 			=> 'select',
				'place'			=> 'adv',
				'options_source'=> 'custom',
				'options' 		=> array('1','2','3','4','5','6','7','8','9','10'),
			),				
			'baths' => array(
				
				'placeholder'	=> __( 'Baths', 'realteo' ),
				'key'			=> '_bathrooms',
				'class'			=> 'col-fs-3',
				'open_row'		=> false,
				'close_row'		=> true,
				'name'			=> '_bathrooms',
		    	'priority'		=> 1,
		    	'place'			=> 'adv',
				'type' 			=> 'select',
				'options_source'=> 'custom',
				'options' 		=> array('1','2','3','4','5','6','7','8','9','10'),
			),
						
			'features' => array(
				
				'placeholder' 	=> __( 'Features', 'realteo' ),
				'key'			=> '_features',
				'class'			=> 'col-md-12',
				'open_row'		=> true,
				'close_row'		=> true,
				'options'		=> array(),
				'name'			=> 'tax-property_feature',
				'taxonomy' 		=> 'property_feature',
		    	'priority'		=> 1,
		    	'place'			=> 'adv',
				'type' 			=> 'multi-checkbox-row',
			),		
		);

		return apply_filters('realteo_search_fields_half',$search_fields);
	}

	public static function get_search_fields_home(){
		$scale = realteo_get_option( 'scale', 'sq ft' );
		$search_fields = array(
			'order' => array(
				'placeholder'	=> __( 'Hidden order', 'realteo' ),
				'key'			=> 'realteo_order',
				'class'			=> 'col-md-12',
				'open_row'		=> true,
				'close_row'		=> true,
				'name'			=> 'realteo_order',
		    	'priority'		=> 1,
		    	'place'			=> 'main',
				'type' 			=> 'hidden',
			),	
			'property_type' => array(
				
				'placeholder'	=> __( 'Any type', 'realteo' ),
				'key'			=> '_property_type',
				'class'			=> 'col-md-4',
				'open_row'		=> true,
				'close_row'		=> false,
				'name'			=> '_property_type',
		    	'priority'		=> 1,
		    	'place'			=> 'main',
				'type' 			=> 'select',
				'options_source'=> 'predefined',
				'options_cb' 	=> 'realteo_get_property_types',
			),	
			'price_range' => array(
				
				'placeholder' 	=> __( 'Price', 'realteo' ),
				'key'			=> '_price',
				'class'			=> 'col-md-4',
				'open_row'		=> false,
				'close_row'		=> true,
				'name'			=> '_price',
		    	'priority'		=> 1,
		    	'place'			=> 'main',
				'type' 			=> 'double-input',
				'max' 			=> 150000,
				'min' 			=> 10000,
				'step'			=> 10000,
				'unit' 			=> realteo_get_option( 'currency' ),
			),	

			'area_range' => array(
				
				'placeholder' 	=> __( 'Area range', 'realteo' ),
				'key'			=> '_area',
				'class'			=> 'col-md-6',
				'open_row'		=> true,
				'close_row'		=> true,
				'name'			=> '_area',
		    	'priority'		=> 1,
		    	'place'			=> 'adv',
				'type' 			=> 'double-input',
				'max' 			=> 1500,
				'min' 			=> 100,
				'step'			=> 100,
				'unit' 			=> apply_filters('realteo_scale',$scale),
			),
			'bedrooms' => array(
				
				'placeholder'	=> __( 'Beds', 'realteo' ),
				'key'			=> '_bedrooms',
				'class'			=> 'col-md-6',
				'open_row'		=> true,
				'close_row'		=> false,
				'name'			=> '_bedrooms',
		    	'priority'		=> 1,
				'place'			=> 'adv',
				'type' 			=> 'input-select',
				'max' 			=> 10,
				'min' 			=> 1,
				'step'			=> 1,
			),				
			'baths' => array(
				
				'placeholder'	=> __( 'Baths', 'realteo' ),
				'key'			=> '_bathrooms',
				'class'			=> 'col-md-6',
				'open_row'		=> false,
				'close_row'		=> true,
				'name'			=> '_bathrooms',
		    	'priority'		=> 1,
				'place'			=> 'adv',
				'type' 			=> 'input-select',
				'max' 			=> 10,
				'min' 			=> 1,
				'step'			=> 1,
			),
			'features' => array(
				
				'placeholder' 	=> __( 'Features', 'realteo' ),
				'key'			=> '_features',
				'class'			=> 'col-md-12',
				'open_row'		=> true,
				'close_row'		=> true,
				'options'		=> array(),
				'name'			=> 'tax-property_feature',
				'taxonomy' 		=> 'property_feature',
		    	'priority'		=> 1,
		    	'place'			=> 'adv',
				'type' 			=> 'multi-checkbox-row',
			),			
		);

		return apply_filters('realteo_search_fields_home',$search_fields);
	}

	public static function get_search_fields_home_alt(){
		$scale = realteo_get_option( 'scale', 'sq ft' );
		$search_fields = array(
			'order' => array(
				'placeholder'	=> __( 'Hidden order', 'realteo' ),
				'key'			=> 'realteo_order',
				'class'			=> 'col-md-12',
				'open_row'		=> true,
				'close_row'		=> true,
				'name'			=> 'realteo_order',
		    	'priority'		=> 1,
		    	'place'			=> 'main',
				'type' 			=> 'hidden',
			),	
			'property_type' => array(
				
				'placeholder'	=> __( 'Any type', 'realteo' ),
				'key'			=> '_property_type',
				'class'			=> 'col-md-3',
				'open_row'		=> true,
				'close_row'		=> false,
				'name'			=> '_property_type',
		    	'priority'		=> 1,
		    	'place'			=> 'main',
				'type' 			=> 'select',
				'options_source'=> 'predefined',
				'options_cb' 	=> 'realteo_get_property_types',
			),	
			'offer_type' => array(
				
				'placeholder'	=> __( 'Any Status', 'realteo' ),
				'key'			=> '_offer_type',
				'class'			=> 'col-md-3',
				'open_row'		=> false,
				'close_row'		=> false,
				'name'			=> '_offer_type',
		    	'priority'		=> 1,
		    	'place'			=> 'main',
				'type' 			=> 'select',
				'options_source'=> 'predefined',
				'options_cb' 	=> 'realteo_get_offer_types',
			),	
			'keyword_search' => array(
				'placeholder'	=> __( 'Enter address e.g. steet, city or state', 'realteo' ),
				'key'			=> '_keyword_search',
				'class'			=> 'col-md-5',
				'open_row'		=> false,
				'close_row'		=> false,
				'name'			=> 'keyword_search',
		    	'priority'		=> 1,
		    	'place'			=> 'main',
				'type' 			=> 'location',
			),
			'submit' => array(
				'class'			=> 'col-md-1',
				'open_row'		=> false,
				'close_row'		=> true,
				'place'			=> 'main',
				'placeholder'	=> '<i class="fa fa-search"></i>',
				'type' 			=> 'submit',
			),		
		
		);

		return apply_filters('realteo_search_fields_home_alt',$search_fields);
	}

	public function output_search_form( $atts = array() ){
		extract( $atts = shortcode_atts( apply_filters( 'realteo_output_defaults', array(
			'source'			=> 'sidebar', //full-width
			'wrap_with_form'	=> 'yes',
			'more_trigger'		=> 'yes',
			'more_text_open'	=> __('Additional Features','realteo'),
			'more_text_close'	=> __('Additional Features','realteo'),
			'more_custom_class' => ' margin-bottom-10 margin-top-30',
			'more_trigger_style' => 'relative',
			'action'			=> '',

		) ), $atts ) );

		switch ($source) {
			case 'home':
				$search_fields = $this->get_search_fields_home();
				break;

			case 'home-alt':
				$search_fields = $this->get_search_fields_home_alt();
				break;

			case 'sidebar':
				$search_fields = $this->get_search_fields();
				break;

			case 'fullwidth':
				$search_fields = $this->get_search_fields_fw();
				break;

			case 'half':
				$search_fields = $this->get_search_fields_half();
				break;
			
			default:
				$search_fields = $this->get_search_fields_fw();	
				break;
		}

		
		$template_loader = new Realteo_Template_Loader;

		//$action = get_post_type_archive_link( 'property' );
		
		if(is_author()) {
			$author = get_queried_object();
    		$author_id = $author->ID;
			$action = get_author_posts_url($author_id);
		}
		ob_start();	
		if($wrap_with_form == 'yes') { ?>
		<form action="<?php echo $action; ?>" id="realteo-search-form" method="GET">
		<?php } ?>
			<?php if(is_archive() && is_author()) : 
			$author = get_queried_object();
    		$author_id = $author->ID;?>
			<!-- <input type="hidden" name="author" value="<?php echo esc_attr($author_id); ?>"> -->
			<?php endif; ?>
			<?php 
				$more_trigger = false;
				foreach ($search_fields as $key => $value) {
					if(isset($value['place']) && $value['place'] == 'adv') {
						$more_trigger = 'yes';
					}
				}
			?>
			<?php 
			foreach ($search_fields as $key => $value) {
				if(isset($value['place']) && $value['place'] == 'main') {
					$template_loader->set_template_data( $value )->get_template_part( 'search-form/'.$value['type']);
				}
			} ?>	
			<?php if($more_trigger == 'yes') : ?>				
				<!-- More Search Options -->
				<a href="#" class="more-search-options-trigger <?php echo esc_attr($more_custom_class) ?>" data-open-title="<?php echo esc_attr($more_text_open) ?>" data-close-title="<?php echo esc_attr($more_text_close) ?>"></a>
				<?php if($more_trigger_style == "over") : ?>
				<div class="more-search-options ">
					<div class="more-search-options-container">
				<?php else: ?>
					<div class="more-search-options relative">
				<?php endif; ?>
						<?php foreach ($search_fields as $key => $value) {
						if($value['place'] == 'adv') {
							$template_loader->set_template_data( $value )->get_template_part( 'search-form/'.$value['type']);
						}
						} ?>
					<?php if($more_trigger_style == "over") : ?>
					</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			
			<!-- More Search Options / End -->
			<?php if($source == 'sidebar') {	?>
				<button class="button fullwidth margin-top-30"><?php esc_html_e('Search','realteo') ?></button>
			<?php }
		if($wrap_with_form == 'yes') { ?>
		</form>
		<?php }
 		return ob_get_clean();

	}

	public static function get_min_meta_value($meta_key = '',$type = '') {

		global $wpdb;
		$result = false;
		if($type == 'sale') {
			$type_query = 'AND ( m1.meta_key = "_offer_type" AND m1.meta_value = "'.$type.'")';
		} else {
			$type_query = false;
		}
		if($meta_key):
	
			$result = $wpdb->get_var(
		    $wpdb->prepare("
		            SELECT min(m2.meta_value + 0)
		            FROM $wpdb->posts AS p
		            INNER JOIN $wpdb->postmeta AS m1 ON ( p.ID = m1.post_id )
					INNER JOIN $wpdb->postmeta AS m2  ON ( p.ID = m2.post_id )
					WHERE
					p.post_type = 'property'
					AND p.post_status = 'publish'
					$type_query
					AND ( m2.meta_key IN (%s)  ) AND m2.meta_value != ''
		        ", $meta_key )
		    ) ;
		endif;

	    return $result;
	}	

	public static function get_max_meta_value($meta_key = '',$type = '' ) {
		global $wpdb;
		$result = false;
		if($type == 'sale') {
			$type_query = 'AND ( m1.meta_key = "_offer_type" AND m1.meta_value = "'.$type.'")';
		} else {
			$type_query = false;
		}
		if($meta_key):
		
			$result = $wpdb->get_var(
		    $wpdb->prepare("
		            SELECT max(m2.meta_value + 0)
		            FROM $wpdb->posts AS p
		            INNER JOIN $wpdb->postmeta AS m1 ON ( p.ID = m1.post_id )
					INNER JOIN $wpdb->postmeta AS m2  ON ( p.ID = m2.post_id )
					WHERE
					p.post_type = 'property'
					AND p.post_status = 'publish'
					$type_query
					AND ( m2.meta_key IN (%s)  ) AND m2.meta_value != ''
		        ", $meta_key )
		    );
		  
	    endif;
	   

	    return $result;
	}


}