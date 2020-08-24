<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Realteo_Property class
 */
class Realteo_Property {
	
	private static $_instance = null;

	public function __construct () {
			add_filter( 'query_vars', array( $this, 'add_query_vars' ) );
	}


	
	/**
	 * add_query_vars()
	 *
	 * Adds query vars for search and display.
	 *
	 * @param integer $vars Post ID
	 *
	 * @since 1.0.0
	 */
	public function add_query_vars($vars) {
		
		$new_vars = array();

        array_push($new_vars, 'keyword_search','realteo_order','search_radius','radius_type','agency');
	
	    $vars = array_merge( $new_vars, $vars );
		return $vars;

	}

	public static function get_real_properties($args ) {

		global $wpdb;

		global $paged;

		$ordering_args = Realteo_Property::get_properties_ordering_args( );
		
		if ( get_query_var( 'paged' ) ) { $paged = get_query_var( 'paged' ); }
		elseif ( get_query_var( 'page' ) ) { $paged = get_query_var( 'page' ); }
		else { $paged = 1; }

		
	
		$search_radius_var = get_query_var( 'search_radius' );

		if(!empty($search_radius_var)) {	$args['search_radius'] = $search_radius_var;	}

		$radius_type_var = get_query_var( 'radius_type' );
		if(!empty($radius_type_var)) {	$args['radius_type'] = $radius_type_var;	}
				
		$keyword_var = get_query_var( 'keyword_search' );
		if(!empty($keyword_var)) {	$args['keyword'] = $keyword_var;	}
			
		$agency_var = get_query_var( 'agency' );
		if(!empty($agency_var)) {	$args['agency'] = $agency_var;	}

		$query_args = array(
			'post_type'              => 'property',
			'post_status'            => 'publish',
			'ignore_sticky_posts'    => 1,
			'paged' 		 		 => $paged,
			'posts_per_page'         => intval( $args['posts_per_page'] ),
			'orderby'                => $args['orderby'],
			'order'                  => $args['order'],
			'tax_query'              => array(),
			'meta_query'             => array(),
		);

	    if(isset($ordering_args['meta_key']) && $ordering_args['meta_key'] != '_featured' ){
			$query_args['meta_key'] = $ordering_args['meta_key'];
		}

		if ( isset($args['keyword']) && !empty($args['keyword']) ) {

			$radius = $args['search_radius'];
			if(empty($radius)){
        		$radius =  realteo_get_option('realteo_maps_default_radius');
        	}
			$radius_type = realteo_get_option('radius_unit','km');

			if(!empty($keyword_var) && !empty($radius)) {
				//search by google
				
				$latlng = realteo_geocode($keyword_var);

				$nearbyposts = realteo_get_nearby_properties($latlng[0], $latlng[1], $radius, $radius_type ); 

				realteo_array_sort_by_column($nearbyposts,'distance');
				$post_ids = array_unique(array_column($nearbyposts, 'post_id'));

				if(empty($post_ids)) {
					
					$post_ids = array(0);
				}
			} 
			if( realteo_get_option('include_text_search') ){
				// Trim and explode keywords
				$keywords = array_map( 'trim', explode( ',', $args['keyword'] ) );

				// Setup SQL

				$posts_keywords_sql    = array();
				$postmeta_keywords_sql = array();

				$postmeta_keywords_sql[] = " meta_value LIKE '%" . esc_sql( $keywords[0] ) . "%' ";
				// Create post title and content SQL
				$posts_keywords_sql[]    = " post_title LIKE '%" . esc_sql( $keywords[0] ) . "%' OR post_content LIKE '%" . esc_sql(  $keywords[0] ) . "%' ";
					
				// Get post IDs from post meta search

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
		}

		if(!empty($args['agency'])) {

			if(is_numeric($args['agency'])){
				$authors_id = get_post_meta($args['agency'],'agency_agent_of',true);
			} else {
				$agency_object = get_page_by_path( $args['agency'], OBJECT, 'agency' ) ;

				$authors_id = get_post_meta($agency_object->ID,'realteo-agents',true);
			}
			$query_args['author__in'] =  $authors_id;
		}
		
		if ( ! empty( $post_ids ) ) {
			$query_args['post__in'] = $post_ids;
		}
		$query_args['tax_query'] = array(
	        'relation' => 'AND',
	    );

		$taxonomy_objects = get_object_taxonomies( 'property', 'objects' );

        foreach ($taxonomy_objects as $tax) {
        	
        	$get_tax = (isset($_GET['tax-'.$tax->name])) ? $_GET['tax-'.$tax->name] : $args['tax-'.$tax->name] ;
			if(!is_array($get_tax)){
				if(strpos($get_tax, ',') !== false) {
					$get_tax = explode(',', $get_tax);
				}
			}
        	if(is_array($get_tax)){
        		$query_args['tax_query'][$tax->name] = array('relation'=> 'OR');
        		foreach ($get_tax as $key => $value) {
		    		array_push($query_args['tax_query'][$tax->name], array(
			           'taxonomy' =>   $tax->name,
			           'field'    =>   'slug',
			           'terms'    =>   $value,
			           
			        ));
		    	}
        	} else {

            	if( $get_tax ){

			    	$term = get_term_by('slug', $get_tax, $tax->name);
			    	if($term){
				    	array_push($query_args['tax_query'], array(
				           'taxonomy' =>  $tax->name,
				           'field'    =>  'slug',
				           'terms'    =>  $term->slug,
				           'operator' =>  'IN'
				        ));	
			    	}
			    	
			    }
		 	}
        }
     
		$available_query_vars = Realteo_Search::build_available_query_vars();


		$meta_queries = array();
		foreach ($available_query_vars as $key => $meta_key) {

			if( substr($meta_key,0, 4) == "tax-") {
				continue;
			}

			if (substr($meta_key, -4) == "_min") {
				if(in_array($meta_key, array('_price_min','_price_max','_price'))) {
					$meta_min = (int) filter_var(get_query_var( $meta_key ), FILTER_SANITIZE_NUMBER_INT);
				} else {
					$meta_min = get_query_var( $meta_key );
					$meta_min = (!empty(get_query_var( $meta_key ))) ? get_query_var( $meta_key ) : $args[$meta_key] ;
				}
			}
			if (substr($meta_key, -4) == "_max") {
				if(in_array($meta_key, array('_price_min','_price_max','_price'))){
					$meta_max = (int) filter_var(get_query_var( $meta_key ), FILTER_SANITIZE_NUMBER_INT);
				} else {
					$meta_max = get_query_var( $meta_key );
					$meta_max = (!empty(get_query_var( $meta_key ))) ? get_query_var( $meta_key ) : $args[$meta_key] ;
				}
				
			}

			if(!empty($meta_min) && !empty($meta_max) ) {
		
				$query_args['meta_query'][$meta_key] = array(
		            'key' =>  substr($meta_key,0, -4),
		            'value' => array($meta_min, $meta_max),
		            'compare' => 'BETWEEN',
		            'type' => 'NUMERIC'
		        );
		        $meta_max = false;
		        $meta_min = false;

			} else if(!empty($meta_min) && empty($meta_max) ) {

				$query_args['meta_query'][$meta_key] = array(
		            'key' =>  substr($meta_key,0, -4),
		            'value' => $meta_min,
		            'compare' => '>=',
		            'type' => 'NUMERIC'
		        );
		        $meta_max = false;
		        $meta_min = false;
			} else if(empty($meta_min) && !empty($meta_max) ) {
				$query_args['meta_query'][$meta_key] = array(
		            'key' =>  substr($meta_key,0, -4),
		            'value' => $meta_max,
		            'compare' => '<=',
		            'type' => 'NUMERIC'
		        );
		        $meta_max = false;
		        $meta_min = false;
			}
			if (substr($meta_key, -4) == "_min" || substr($meta_key, -4) == "_max") { continue; }
			$meta = (!empty(get_query_var( $meta_key ))) ? get_query_var( $meta_key ) : $args[$meta_key] ;

			if ( $meta ) {

				if(is_array($meta)){
					$query_args['meta_query'][$meta_key] = array(
		                'key'     => $meta_key,
		                'value'   => $meta, 
		                'compare' => 'IN',
		            );
				}else {
					$query_args['meta_query'][] = array(
		                'key'     => $meta_key,
		                'value'   => $meta, 
		            );
				}
			}
		
		}
		  
;
    	if( isset($ordering_args['meta_key']) && $ordering_args['meta_key'] == '_featured' ){

			$query_args['meta_query'][] = array(
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
			$query_args['orderby'] = array( 'featured_clause' => 'DESC');
		}

		if ( empty( $query_args['meta_query'] ) )
		 	unset( $query_args['meta_query'] );

		$query_args = apply_filters( 'realto_get_listings', $query_args, $args );
		
		if ( ! empty( $post_ids ) )
			$query_args['post__in'] = $post_ids;

		$result = new WP_Query( $query_args );
		
		return $result;

	}


	/**
	 * get_listing_price_raw()
	 *
	 * Return listings price without formatting.
	 *
	 * @param integer $post_id Post ID
	 * @uses get_the_ID()
	 * @uses get_post_meta()
	 * @return string Listing price meta value
	 *
	 * @since 1.0.0
	 */
	public static function get_property_price( $post ) {

		// Use global post ID if not defined

		if ( ! $post ) {
			$post = get_the_ID();
		} else {
			$post = $post->ID;
		}

		$price = get_post_meta( $post, '_price', true );
		if (is_numeric($price)) {
		    $price_raw = number_format_i18n($price);
		} else {
		    return $price;
		}

		$price_output = '';
		if ( !empty( $price_raw ) ) :
			
 			$currency_abbr = realteo_get_option( 'currency' );
			$currency_postion = realteo_get_option( 'currency_postion' );
			$currency_symbol = Realteo_Property::get_currency_symbol($currency_abbr);

			if($currency_postion == 'after') {
				$price_output = $price_raw . $currency_symbol;
			} else {
				$price_output = $currency_symbol.$price_raw;
			}

		endif;
		// Return listing price
		return apply_filters( 'get_property_price', $price_output, $post );

	}

	/**
	 *
	 * @since 1.0.0
	 */
	public static function get_property_price_per_scale( $post ) {
		if ( ! $post )
			$post = get_the_ID();

		$price_raw 		= get_post_meta( $post, '_price', true );
		if(empty($price_raw) || !is_numeric($price_raw)){
			return;
		}
		$area 			= get_post_meta( $post, '_area', true );

		$price_per_raw 	= get_post_meta( $post, '_price_per', true );
		$output 		= '';
		$currency_abbr = realteo_get_option( 'currency' );
		$currency_postion = realteo_get_option( 'currency_postion' );
		$currency_symbol = Realteo_Property::get_currency_symbol($currency_abbr);

		if(empty($price_per_raw) && !empty($area)){
			$output = intval($price_raw/$area,10);
		} else {
			if(empty($price_per_raw)) {
				$output = '';
				
			} else {
				$output = $price_per_raw;

			}
			
		}

		

		if(realteo_get_option( 'realteo_hide_price_per_scale' )) {
			$output = '';
		} else {
			if($output): //have price
				$scale = realteo_get_option( 'scale', 'sq ft' );
				if($currency_postion == 'after') {
					$output = $output . $currency_symbol;
				} else {
					$output = $currency_symbol . $output;
				}
				$output .= ' / '.apply_filters('realteo_scale',$scale);
			endif;		
		}
		$show_rental_period = false;
		$offer_type = get_the_property_offer_type();
		$available_for_rental = get_available_for_rental_period();
		if(is_array($offer_type)){
			foreach ($offer_type as $offer) {
				if(in_array($offer,$available_for_rental)){
					$show_rental_period = true;
				}
			}
		}
		if(is_string($offer_type) && in_array($offer_type,$available_for_rental)) {
			$show_rental_period = true;
		}
		if($show_rental_period) {
			$periods = realteo_get_rental_period();
			
			$current_selection = get_post_meta( $post, '_rental_period', true );
			if(is_array($current_selection)){
				foreach ($current_selection as $current) {
					if(isset($periods[$current])) {
						$output = $periods[$current];	
					
					} else {
						$output = '';
					}
				}
			} else {
				if(!empty($current_selection) && isset($periods[$current_selection])) {
						$output = $periods[$current_selection];	
					
				} else {
					$output = '';
				}
			}
			
			
		} 
		
		


		return apply_filters( 'get_property_price', $output, $post );

	}


	
	public static function get_currency_symbol( $currency = '' ) {
		if ( ! $currency ) {
			$currency = realteo_get_option( 'currency' );
		}

		switch ( $currency ) {
			case 'BHD' :
				$currency_symbol = '.د.ب';
				break;
			case 'AED' :
				$currency_symbol = 'د.إ';
				break;
			case 'AUD' :
			case 'ARS' :
			case 'CAD' :
			case 'CLP' :
			case 'COP' :
			case 'HKD' :
			case 'MXN' :
			case 'NZD' :
			case 'SGD' :
			case 'USD' :
				$currency_symbol = '&#36;';
				break;
			case 'BDT':
				$currency_symbol = '&#2547;&nbsp;';
				break;
			case 'LKR':
				$currency_symbol = '&#3515;&#3540;&nbsp;';
				break;
			case 'BGN' :
				$currency_symbol = '&#1083;&#1074;.';
				break;
			case 'BRL' :
				$currency_symbol = '&#82;&#36;';
				break;
			case 'CHF' :
				$currency_symbol = '&#67;&#72;&#70;';
				break;
			case 'CNY' :
			case 'JPY' :
			case 'RMB' :
				$currency_symbol = '&yen;';
				break;
			case 'CZK' :
				$currency_symbol = '&#75;&#269;';
				break;
			case 'DKK' :
				$currency_symbol = 'DKK';
				break;
			case 'DOP' :
				$currency_symbol = 'RD&#36;';
				break;
			case 'EGP' :
				$currency_symbol = 'EGP';
				break;
			case 'EUR' :
				$currency_symbol = '&euro;';
				break;
			case 'GBP' :
				$currency_symbol = '&pound;';
				break;
			case 'GHS' :
				$currency_symbol = 'GH₵';
				break;
			case 'HRK' :
				$currency_symbol = 'Kn';
				break;
			case 'HUF' :
				$currency_symbol = '&#70;&#116;';
				break;
			case 'IDR' :
				$currency_symbol = 'Rp';
				break;
			case 'ILS' :
				$currency_symbol = '&#8362;';
				break;
			case 'INR' :
				$currency_symbol = 'Rs.';
				break;
			case 'JOD' :
				$currency_symbol = 'JOD';
				break;
			case 'ISK' :
				$currency_symbol = 'Kr.';
				break;	
			case 'KZT' :
				$currency_symbol = '₸';
				break;
			case 'KIP' :
				$currency_symbol = '&#8365;';
				break;
			case 'KRW' :
				$currency_symbol = '&#8361;';
				break;
			case 'MYR' :
				$currency_symbol = '&#82;&#77;';
				break;
			case 'NGN' :
				$currency_symbol = '&#8358;';
				break;
			case 'NOK' :
				$currency_symbol = '&#107;&#114;';
				break;
			case 'NPR' :
				$currency_symbol = 'Rs.';
				break;
			case 'MAD' :
				$currency_symbol = 'DH';
				break;
			case 'PHP' :
				$currency_symbol = '&#8369;';
				break;
			case 'PLN' :
				$currency_symbol = '&#122;&#322;';
				break;
			case 'PYG' :
				$currency_symbol = '&#8370;';
				break;
			case 'RON' :
				$currency_symbol = 'lei';
				break;
			case 'RUB' :
				$currency_symbol = '&#1088;&#1091;&#1073;.';
				break;
			case 'SEK' :
				$currency_symbol = '&#107;&#114;';
				break;
			case 'THB' :
				$currency_symbol = '&#3647;';
				break;
			case 'TRY' :
				$currency_symbol = '&#8378;';
				break;
			case 'TWD' :
				$currency_symbol = '&#78;&#84;&#36;';
				break;
			case 'UAH' :
				$currency_symbol = '&#8372;';
				break;
			case 'VND' :
				$currency_symbol = '&#8363;';
				break;
			case 'ZAR' :
				$currency_symbol = '&#82;';
				break;
			case 'ZMK' :
				$currency_symbol = 'ZK';
				break;
			default :
				$currency_symbol = '';
				break;
		}

		return apply_filters( 'realteo_currency_symbol', $currency_symbol, $currency );
	}

	public static function get_properties_ordering_args( $orderby = '', $order = '' ) {
		// Get ordering from query string unless defined
		if ( ! $orderby ) {	
			$orderby_value = isset( $_GET['realteo_order'] ) ? (string) $_GET['realteo_order']  : realteo_get_option( 'realteo_sort_by','date' );

			// Get order + orderby args from string
			$orderby_value = explode( '-', $orderby_value );
			$orderby       = esc_attr( $orderby_value[0] );
			$order         = ! empty( $orderby_value[1] ) ? $orderby_value[1] : $order;

		}

		$orderby = strtolower( $orderby );
		$order   = strtoupper( $order );
		$args    = array();

		// default - menu_order
		$args['orderby']  = 'date ID'; //featured
		$args['order']    = ( 'DESC' === $order ) ? 'DESC' : 'ASC';
		$args['meta_key'] = '';

		switch ( $orderby ) {
			case 'rand' :
				$args['orderby']  = 'rand';
				break;
			case 'featured' :
				$args['orderby']  = 'meta_value_num date';
				$args['meta_key']  = '_featured';
				break;
			case 'date' :

				$args['orderby']  = 'date ID';
				$args['order']    = ( 'ASC' === $order ) ? 'ASC' : 'DESC';
				break;
			case 'price' :
				if ( 'DESC' === $order ) {
					//add_filter( 'posts_clauses', array( $this, 'order_by_price_desc_post_clauses' ) );
					$args['orderby']  = 'meta_value_num';
					$args['meta_key']  = '_price';
				} else {
					$args['orderby']  = 'meta_value_num';
					$args['meta_key']  = '_price';
					//add_filter( 'posts_clauses', array( $this, 'order_by_price_asc_post_clauses' ) );
				}
				break;
			
			case 'title' :
				$args['orderby'] = 'title';
				$args['order']   = ( 'DESC' === $order ) ? 'DESC' : 'ASC';
				break;
			default:
				$args['orderby']  = 'date ID';
				$args['order']    = ( 'ASC' === $order ) ? 'ASC' : 'DESC';
				break;
		}
	
		return apply_filters( 'realteo_get_properties_ordering_args', $args );
	}

	/**
	 * Handle numeric price sorting.
	 *
	 * @access public
	 * @param array $args
	 * @return array
	 */
	public function order_by_price_asc_post_clauses( $args ) {
		global $wpdb;
		$args['join']    .= " INNER JOIN ( SELECT post_id, min( meta_value+0 ) price FROM $wpdb->postmeta WHERE meta_key='_price' GROUP BY post_id ) as price_query ON $wpdb->posts.ID = price_query.post_id ";
		$args['orderby'] = " price_query.price ASC ";
		return $args;
	}

	/**
	 * Handle numeric price sorting.
	 *
	 * @access public
	 * @param array $args
	 * @return array
	 */
	public function order_by_price_desc_post_clauses( $args ) {
		global $wpdb;
		$args['join']    .= " INNER JOIN ( SELECT post_id, max( meta_value+0 ) price FROM $wpdb->postmeta WHERE meta_key='_price' GROUP BY post_id ) as price_query ON $wpdb->posts.ID = price_query.post_id ";
		$args['orderby'] = " price_query.price DESC ";
		return $args;
	}

}
?>