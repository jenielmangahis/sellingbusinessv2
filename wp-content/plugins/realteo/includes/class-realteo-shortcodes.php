<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Realteo_Shortcodes class.
 */
class Realteo_Shortcodes {

	/**
	 * Constructor
	 */
	public function __construct() {
		
		add_shortcode( 'properties', array( $this, 'show_properties' ) );
		add_filter('realteo_output_defaults',array( $this, 'add_custom_property_atts' ) );
		add_filter('realteo_output_defaults',array( $this, 'add_custom_property_atts' ) );
		
	}


	function add_custom_property_atts($atts) {
	  # this filter should only run once (first use on page)
	  $available_query_vars = Realteo_Search::build_available_query_vars();
	  foreach ($available_query_vars as $key => $meta_key) {
	  	$atts[$meta_key] = '';
	  }
	  $taxonomy_objects = get_object_taxonomies( 'property', 'objects' );
		foreach ($taxonomy_objects as $tax) {
		  	$atts['tax-'.$tax->name] = '';
		  }

	  return $atts;
	}

	public function show_properties( $atts = array() ) {

		extract( $atts = shortcode_atts( apply_filters( 'realteo_output_defaults', array(
			'with_keyword_search'		=> 'no',
			'list_style'				=> 'list-layout',
			'layout_switch'				=> 'on',
			'order_switch'				=> '',
			'per_page'                  => get_option( 'posts_per_page' ),
			'orderby'                   => '',
			'order'                     => '',
			'keyword'                   => '',
			'agency'                   => '',
			'search_radius'             => '',
			'radius_type'               => '',
			'featured'                  => null, // True to show only featured, false to hide featured, leave null to show both.
			'custom_class'				=> '',
			'in_rows'					=> '',
			'more_button'					=> '',
		) ), $atts ) );
		  

		$template_loader = new Realteo_Template_Loader;
		// Get listings query
		$ordering_args = Realteo_Property::get_properties_ordering_args(  );
 
		if ( ! is_null( $featured ) ) {
			$featured = ( is_bool( $featured ) && $featured ) || in_array( $featured, array( '1', 'true', 'yes' ) ) ? true : false;
		}



		$get_properties = array_merge($atts,array(
				'posts_per_page'    => $per_page,
				'orderby'           => $ordering_args['orderby'],
				'order'             => $ordering_args['order'],
				'keyword'   		=> $keyword,
				'search_radius'   	=> $search_radius,
				'radius_type'   	=> $radius_type,
				'featured'          => $featured,
			));
		
		$realteo_query = Realteo_Property::get_real_properties( apply_filters( 'realteo_output_defaults_args', $get_properties ));
		ob_start();
		
		?>

			<div class="row margin-bottom-15">

				<?php do_action( 'realto_before_archive', $list_style, $layout_switch, $order_switch  ); ?>
			</div>
		<?php
		if ( $realteo_query->have_posts() ) { 
			$style_data = array(
				'style' => $list_style, 
				'class' => $custom_class, 
				'in_rows' => $in_rows, 
				'max_num_pages'=> $realteo_query->max_num_pages, 
				'counter'=> $realteo_query->found_posts 
				);
			$template_loader->set_template_data( $style_data )->get_template_part( 'listings-start' ); 
			
			// Loop through listings
			while ( $realteo_query->have_posts() ) {
				// Setup listing data
				$realteo_query->the_post();
				if($list_style == 'compact') {
					$template_loader->get_template_part( 'content-property-compact' ); 	
				} else {
					$template_loader->get_template_part( 'content-property' ); 	
				}
				
			}
			if($more_button) {

				
				$link = vc_build_link( $more_button );
		        $a_href = $link['url'];
		        $a_title = $link['title'];
		        $a_target = $link['target'];
		        
		        if($a_title != '') {
		        	if(!empty($in_rows)): ?>
					</div>
				<?php endif; ?>
				</div>

				<?php 
		        	echo '<a class="button centered" href="'.$a_href.'" title="'.esc_attr( $a_title ).'"';
			        if(!empty($a_target)){
			        	echo 'target="'.$a_target.'"';
			        }
			        echo '>';
			        echo $a_title.'</a>';
		        } else {
		        	$template_loader->set_template_data( $style_data )->get_template_part( 'listings-end' ); 	
		        }
			} else {
				$template_loader->set_template_data( $style_data )->get_template_part( 'listings-end' ); 	
			}
			
	
		} else {

			$template_loader->get_template_part( 'archive/no-found' ); 
		}

		wp_reset_query();
		return ob_get_clean();
	}


}


new Realteo_Shortcodes();
