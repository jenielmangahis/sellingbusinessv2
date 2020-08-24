<?php 

add_action( 'cmb2_admin_init', 'findeo_register_metabox_testimonial' );
/**
 * Hook in and add a demo metabox. Can only happen on the 'cmb2_admin_init' or 'cmb2_init' hook.
 */
function findeo_register_metabox_testimonial() {
	$prefix = 'findeo_';
	$findeo_testimonials_mb = new_cmb2_box( array(
		'id'            => $prefix . 'testimonial',
		'title'         => __( 'Additional Informations', 'findeo' ),
		'object_types'  => array( 'testimonial', ), // Post type
		'priority'   => 'high',
	) );
	$findeo_testimonials_mb->add_field( array(
		'name' => __( 'Company', 'findeo' ),
		'id'   => $prefix . 'pp_company',
		'type' => 'text_medium',
		
	) );	

}	

add_action( 'cmb2_admin_init', 'findeo_register_metabox_property' );
function findeo_register_metabox_property() {
	$prefix = 'findeo_';
	
	/* get the registered sidebars */
    global $wp_registered_sidebars;

    $sidebars = array();
    foreach( $wp_registered_sidebars as $id=>$sidebar ) {
      $sidebars[ $id ] = $sidebar[ 'name' ];
    }

	/**
	 * Sample metabox to demonstrate each field type included
	 */
	$findeo_property_mb = new_cmb2_box( array(
		'id'            => $prefix . 'property_sb_metabox',
		'title'         => esc_html__( 'Findeo Property Options', 'findeo' ),
		'object_types'  => array( 'property' ), // Post type
		'priority'   => 'high',
	) );

	$findeo_property_mb->add_field( array( 
			'name'    => esc_html__( 'Selected Sidebar', 'findeo' ),
			'id'      => $prefix . 'sidebar_select',
			'type'    => 'select',
			'default' => 'sidebar-property',
			'options' => $sidebars,
		) );
	$findeo_property_mb->add_field( array(
		'name'    => esc_html__( 'Slider Image field', 'findeo' ),
		'desc'    => esc_html__( 'Upload an image that will be used in Properties slider (recomended min 1920px wide). If not set, Post Thumbnail will be used instead.', 'findeo' ),
		'id'      => $prefix . 'slider_property_image',
		'type'    => 'file',
		// Optional:
		'options' => array(
			'url' => false, // Hide the text input for the url
		),

	) );
}


add_action( 'cmb2_admin_init', 'findeo_register_metabox_pages' );
/**
 * Hook in and add a demo metabox. Can only happen on the 'cmb2_admin_init' or 'cmb2_init' hook.
 */
function findeo_register_metabox_pages() {
	$prefix = 'findeo_';

	
	/* get the registered sidebars */
    global $wp_registered_sidebars;

    $sidebars = array();
    foreach( $wp_registered_sidebars as $id=>$sidebar ) {
      $sidebars[ $id ] = $sidebar[ 'name' ];
    }

	/**
	 * Sample metabox to demonstrate each field type included
	 */
	$findeo_page_mb = new_cmb2_box( array(
		'id'            => $prefix . 'page_metabox',
		'title'         => __( 'Page Options', 'findeo' ),
		'object_types'  => array( 'page','post' ), // Post type
		'priority'   => 'high',
	) );

		$findeo_page_mb->add_field( array(
			'name'             => __( 'Page Top Section', 'findeo' ),
			'desc'             => __( 'Select page layout, default is full-width', 'findeo' ),
			'id'               => $prefix . 'page_top',
			'type'             => 'select',
			'default'			=> 'titlebar',
			'options'          => array(
				'titlebar' 	=> __( 'Regular Titlebar', 'findeo' ),
				'parallax'  => __( 'Parallax image background', 'findeo' ),
				'off'     	=> __( 'Disable top section', 'findeo' ),
			),
		) );

		
		$findeo_page_mb->add_field( array(
		    'name' => __( 'Top bar header', 'findeo' ),
		    'desc' =>  __( 'Enable top bar header on that page','findeo' ),
		    'id'   => $prefix . 'top_bar',
		    'type' => 'select',
		    'default' => 'use_global',
		    'options'     => array(
				'use_global' 	=> __( 'Use Global setting from Customizer', 'findeo' ),
				'disable' 		=> __( 'Hide top bar no matter what', 'findeo' ),
				'enable'     	=> __( 'Show top bar, always', 'findeo' ),
			),
		) );

	
		$findeo_page_mb->add_field( array(
			'name' => __( '"Glue" footer to content', 'findeo' ),
			'desc' => __( 'Removes the top margin from footer section,', 'findeo' ),
			'id'   => $prefix . 'glued_footer',
			'type' => 'checkbox', //#303133
			
		) );
	
		$findeo_page_mb->add_field( array(
			'name' => __( 'Sticky footer', 'findeo' ),
			'desc' => __( 'Enables sticky footer for this page, even if it disabled in global settings', 'findeo' ),
			'id'   => $prefix . 'sticky_footer',
			'type' => 'select',
		    'default' => 'use_global',
		    'options'     => array(
				'use_global' 	=> __( 'Use Global setting from Customizer', 'findeo' ),
				'disable' 		=> __( 'Disable sticky footer', 'findeo' ),
				'enable'     	=> __( 'Enable sticky footer', 'findeo' ),
			),
		) );

		
		$findeo_page_mb->add_field( array(
			'name'             => __( 'Footer color style', 'findeo' ),
			'desc'             => __( 'Sets footer color style, ignoring global settings', 'findeo' ),
			'id'               => $prefix . 'footer_style',
			'type'             => 'select',
			'default'			=> 'light',
			'options'          => array(
				'use_global' 	=> __( 'Use Global setting from Customizer', 'findeo' ),
				'light' 	=> __( 'Light', 'findeo' ),
				'dark'  	=> __( 'Dark', 'findeo' ),
			),
		) );

		$findeo_page_mb->add_field( array(
			'name' => __( 'Sticky header', 'findeo' ),
			'desc' => __( 'Enables sticky header for this page, even if it disabled in global settings', 'findeo' ),
			'id'   => $prefix . 'sticky_header',
			'type' => 'select',
		    'default' => 'use_global',
		    'options'     => array(
				'use_global' 	=> __( 'Use Global setting from Customizer', 'findeo' ),
				'disable' 		=> __( 'Disable', 'findeo' ),
				'enable'     	=> __( 'Enable, always', 'findeo' ),
			),
		) );

		$findeo_page_mb->add_field( array(
			'name' => __( 'Full-width header', 'findeo' ),
			'desc' => __( 'Enables full-width header for this page, even if it disabled in global settings', 'findeo' ),
			'type' => 'select',
		    'default' => 'use_global',
		    'options'     => array(
				'use_global' 	=> __( 'Use Global setting from Customizer', 'findeo' ),
				'disable' 		=> __( 'Disable', 'findeo' ),
				'enable'     	=> __( 'Enable, always', 'findeo' ),
			),
			//'default' => get_option('findeo_header_layout'),
		) );



		global $wpdb;


		/*parallax*/
		$findeo_page_mb->add_field( array(
			'name' => __( 'Parallax background for header', 'findeo' ),
			'desc' => __( 'If added, titlebar will use parallax effect', 'findeo' ),
			'id'   => $prefix . 'parallax_image',
			'type' => 'file',
			
		) );
		$findeo_page_mb->add_field( array(
			'name' => __( 'Overlay color', 'findeo' ),
			'desc' => __( 'For Parallax or Titlebar section', 'findeo' ),
			'id'   => $prefix . 'parallax_color',
			'type' => 'colorpicker', //
			'default' => '#303133'
			
		) );		


		$findeo_page_mb->add_field( array(
			    'name' 		=> __( 'Parallax overlay opacity', 'findeo' ),
			    'desc'        => __( 'Set your value', 'findeo' ),
			    'id'          => $prefix . 'parallax_opacity',
			    'type'        => 'own_slider',
			    'min'         => '0',
			    'max'         => '1',
			    'step'        => '0.01',
			    'default'     => '0.6', // start value
			    'value_label' => 'Opacity Value:',
		) );
/* eof parallax*/

/* video */ 

		

		$findeo_page_mb->add_field( array(
			'name'             => __( 'Page Layout', 'findeo' ),
			'desc'             => __( 'Select page layout, default is full-width', 'findeo' ),
			'id'               => $prefix . 'page_layout',
			'type'             => 'radio_inline',
			'default_cb'			=> 'findeo_set_sidebar_default_cmb2_metabox',
			'options'          => array(
				'full-width' => __( 'Full width', 'findeo' ),
				'left-sidebar'   => __( 'Left Sidebar', 'findeo' ),
				'right-sidebar'     => __( 'Right Sidebar', 'findeo' ),
			),
		) );


		$findeo_page_mb->add_field( array(
			'name' => __( 'Subtitle', 'findeo' ),
			'desc' => __( 'If added, displayed under page title (if applicable)', 'findeo' ),
			'id'   => $prefix . 'subtitle',
			'type' => 'text',
		) );

		


		$findeo_page_mb->add_field( array( 
			'name'    => __( 'Selected Sidebar', 'findeo' ),
			'id'      => $prefix . 'sidebar_select',
			'type'    => 'select',
			'default' => 'sidebar-1',
			'options' => $sidebars,
		) );
}


// render icon_select
add_action( 'cmb2_render_icon_select', 'findeo_cmb_render_icon_select', 10, 5 );
function findeo_cmb_render_icon_select( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
    echo '<ul class="findeo-cmb2-select-icon">';
    for ($i=1; $i < 50 ; $i++) {
    	echo '<li'; 
		if(!empty($escaped_value) && $escaped_value == $i ) {
			echo ' class="active" ';
    	} 
    	echo ' data-icon-id="'.esc_attr($i).'""><i class="findeo-icons findeo-icon-'.esc_attr($i).'"></i></li>';
    }
    echo "</ul>";

    echo $field_type_object->input( array( 'class' => 'cmb2-icon-select', 'type' => 'hidden' ));
}

// sanitize the field
add_filter( 'cmb2_sanitize_icon_select', 'findeo_cmb2_sanitize_icon_select', 10, 2 );
function findeo_cmb2_sanitize_icon_select( $null, $new ) {
    $new = preg_replace( "/[^0-9]/", "", $new );

    return $new;
}
?>