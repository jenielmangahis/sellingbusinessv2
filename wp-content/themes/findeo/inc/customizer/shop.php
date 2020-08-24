<?php 
findeo_Kirki::add_section( 'shop', array(
    'title'          => esc_html__( 'WooCommerce Options', 'findeo'  ),
    'description'    => esc_html__( 'Shop related options', 'findeo'  ),
    'panel'          => '', // Not typically needed.
    'priority'       => 27,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '', // Rarely needed.
) );

	findeo_Kirki::add_field( 'findeo', array(
	    'type'        => 'radio-image',
	    'settings'     => 'pp_shop_layout',
	    'label'       => esc_html__( 'Shop layout', 'findeo' ),
	    'description' => esc_html__( 'Choose the sidebar side for shop', 'findeo' ),
	    'section'     => 'shop',
	    'default'     => 'full-width',
	    'priority'    => 10,
	    'choices'     => array(
	        'left-sidebar' => trailingslashit( trailingslashit( get_template_directory_uri() )) . '/images/left-sidebar.png',
	        'right-sidebar' => trailingslashit( trailingslashit( get_template_directory_uri() )) . '/images/right-sidebar.png',
	        'full-width' => trailingslashit( trailingslashit( get_template_directory_uri() )) . '/images/full-width.png',
	    ),
	) );
	

	findeo_Kirki::add_field( 'findeo', array(
	    'type'        => 'radio',
	    'settings'    => 'pp_shop_ordering',
	    'label'       => esc_html__( 'Show/hide results count and order select on shop page', 'findeo' ),
	    'section'     => 'shop',
	   
	    'default'     => 'show',
	    'priority'    => 10,
	     'choices'     => array(
            'show'  => esc_attr__( 'Show', 'findeo' ),
            'hide' => esc_attr__( 'Hide', 'findeo' ),
        ),
	) );

 ?>