<?php

/*section typography*/ 
findeo_Kirki::add_section( 'typography', array(
    'title'          => esc_html__( 'Typography', 'findeo'  ),
    'description'    => esc_html__( 'Fonts options', 'findeo'  ),
    'panel'          => '', // Not typically needed.
    'priority'       => 60,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '', // Rarely needed.
) );

	findeo_Kirki::add_field( 'findeo', array(
		'type'        => 'typography',
		'settings'    => 'pp_body_font',
		'label'       => esc_attr__( 'Body font', 'findeo' ),
		'section'     => 'typography',
		'default'     => array(
			'font-family'    => 'Varela Round',
			'variant'        => 'regular',
			'font-size'      => '15px',
			'line-height'    => '27px',
			'letter-spacing' => '0',
			'subsets'        => array( 'latin-ext' ),
			'color'          => '#707070',
			'text-transform' => 'none',
			'text-align'     => 'left'
		),
		'priority'    => 10,
		'output'      => array(
			array(
				'element' => 'body,.chosen-single, #tiptip_content, .map-box,  body .pac-container',
			),
		),
	) );	

	findeo_Kirki::add_field( 'findeo', array(
		'type'        => 'typography',
		'settings'    => 'pp_logo_font',
		'label'       => esc_attr__( 'Text logo font', 'findeo' ),
		'section'     => 'typography',
		'default'     => array(
			'font-family'    => 'Varela Round',
			'variant'        => 'regular',
			'color'          => '#666',
			'text-transform' => 'none',
			'font-size'      => '24px',
			'line-height'    => '27px',
			'text-align'     => 'left',
			'subsets'        => array( 'latin-ext' ),
			
		),
		'priority'    => 10,
		'output'      => array(
			array(
				'element' => '#logo h1 a,#logo h2 a',
			),
		),
	) );

	findeo_Kirki::add_field( 'findeo', array(
		'type'        => 'typography',
		'settings'    => 'pp_headers_font',
		'label'       => esc_attr__( 'h1..h6 font', 'findeo' ),
		'section'     => 'typography',
		'default'     => array(
			'font-family'    => 'Varela Round',
			'variant'        => 'regular',
			'subsets'        => array( 'latin-ext' ),
			
		),
		'priority'    => 10,
		'output'      => array(
			array(
				'element' => 'h1,h2,h3,h4,h5,h6',
			),
		),
	) );

	findeo_Kirki::add_field( 'findeo', array(
		'type'        => 'typography',
		'settings'    => 'pp_menu_font',
		'label'       => esc_attr__( 'Menu font', 'findeo' ),
		'section'     => 'typography',
		'default'     => array(
			'font-family'    => 'Varela Round',
			'variant'        => '400',
			'font-size'      => '13px',
			'line-height'    => '32px',
			'subsets'        => array( 'latin-ext' ),
			'color'          => '#333',
			'text-transform' => 'none',
			'text-align'     => 'left'
			
		),
		'priority'    => 10,
		'output'      => array(
			array(
				'element' => '#navigation ul > li > a',
			),
		),
	) );

	?>