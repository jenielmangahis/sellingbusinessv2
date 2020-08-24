<?php 

	findeo_Kirki::add_field( 'findeo', array(
	    'type'        => 'upload',
	    'settings'     => 'pp_logo_upload',
	    'label'       => esc_html__( 'Logo image', 'findeo' ),
	    'description' => esc_html__( 'Upload logo for your website', 'findeo' ),
	    'section'     => 'title_tagline',
	    'default'     => '',
	    'priority'    => 10,
	 /*   'transport'   => 'postMessage',
	    'js_vars'   => array(
			array(
				'element'  => '#logo img',
				'function' => 'html',
			
			),
			
		)*/
	) );		


	findeo_Kirki::add_field( 'findeo', array(
        'type'        => 'slider',
        'settings'    => 'large_logo_max_height',
        'label'       => esc_attr__( 'Logo Max Height (px)', 'travelmatic' ),
        'section'     => 'title_tagline',
        'priority'     => 11,
        'default'     => 43,
        'choices'     => array(
            'min'  => '30',
            'max'  => '500',
            'step' => '1',
        ),
        'output' => array(
            array(
                'element'  => '#logo img',
                'property' => 'max-height',
                'units'    => 'px',
            ),
        ),
    ) );   

	findeo_Kirki::add_field( 'findeo', array(
	    'type'        => 'upload',
	    'settings'     => 'pp_sticky_logo_upload',
	    'label'       => esc_html__( 'Alternative header Sticky Logo image', 'findeo' ),
	    'description' => esc_html__( 'Upload logo used in sticky header', 'findeo' ),
	    'section'     => 'title_tagline',
	    'default'     => '',
	    'priority'    => 10,
	     'active_callback'  => array(
            array(
                'setting'  => 'header_bar_style',
                'operator' => '==',
                'value'    => 'alt',
            ),
        )
    	
	 /*   'transport'   => 'postMessage',
	    'js_vars'   => array(
			array(
				'element'  => '#logo img',
				'function' => 'html',
			
			),
			
		)*/
	) );	

	findeo_Kirki::add_field( 'findeo', array(
	    'type'        => 'color',
	    'settings'    => 'pp_main_color',
	    'label'       => esc_html__( 'Select main theme color', 'findeo' ),
	    'section'     => 'colors',
	    'default'     => '#274abb',
	    'priority'    => 10,
	) );

?>