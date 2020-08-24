<?php
findeo_Kirki::add_section( 'footer', array(
    'title'          => esc_html__( 'Footer Options', 'findeo'  ),
    'description'    => esc_html__( 'Footer related options', 'findeo'  ),
    'panel'          => '', // Not typically needed.
    'priority'       => 50,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '', // Rarely needed.
) );

    findeo_Kirki::add_field( 'findeo', array(
        'type'        => 'radio',
        'settings'    => 'findeo_sticky_footer',
        'label'       => __( 'Switching it to ON will globally enable sticky footer for all pages and post', 'findeo' ),
        'section'     => 'footer',
        'description' => __('Can be set individually on single pages and posts', 'findeo' ),
        'default'     => 'off',
        'priority'    => 10,
       'choices'     => array(
                true  => esc_attr__( 'Enable', 'findeo' ),
                false => esc_attr__( 'Disable', 'findeo' ),
            ),
    ) );

    findeo_Kirki::add_field( 'findeo', array(
        'type'        => 'radio',
        'settings'    => 'findeo_footer_style',
        'label'       => __( 'Footer style', 'findeo' ),
        'section'     => 'footer',
        'default'     => 'light',
        'priority'    => 10,
        'choices'     => array(
            'light'  => esc_attr__( 'Light', 'findeo' ),
            'dark'  => esc_attr__( 'Dark', 'findeo' ),
        ),
    ) );      


	findeo_Kirki::add_field( 'findeo', array(
	    'type'        => 'textarea',
	    'settings'    => 'pp_copyrights',
	    'label'       => esc_html__( 'Copyrights text', 'findeo' ),
	    'default'     => '&copy; Theme by Purethemes.net. All Rights Reserved.',
	    'section'     => 'footer',
	    'priority'    => 10,
	) );

	findeo_Kirki::add_field( 'findeo', array(
    'type'        => 'select',
    'settings'    => 'pp_footer_widgets',
    'label'       => esc_html__( 'Footer widgets layout', 'findeo' ),
    'description' => esc_html__( 'Total width of footer is 16 columns, here you can decide layout based on columns number for each widget area in footer', 'findeo' ),
    'section'     => 'footer',
    'default'     => '6,3,3',
    'priority'    => 10,
    'choices'     => array(
        '6,6'		=> esc_html__( '6 | 6', 'findeo' ),
        '3,3,3,3' 	=> esc_html__( '3 | 3 | 3 | 3', 'findeo' ),
        '6,3,3'     => esc_html__( '6 | 3 | 3 ', 'findeo' ),
        '5,4,3' 	=> esc_html__( '5 | 4 | 3 ', 'findeo' ),
        '3,6,3' 	=> esc_html__( '3 | 6 | 3', 'findeo' ),
        '3,3,6' 	=> esc_html__( '3 | 3 | 6', 'findeo' ),
        '4,4,4' 	=> esc_html__( '4 | 4 | 4', 'findeo' ),
        '4,8' 		=> esc_html__( '4 | 8', 'findeo' ),
        '8,4,' 		=> esc_html__( '8 | 4', 'findeo' ),
        '12' 		=> esc_html__( '12', 'findeo' ),
       
    ),
	) );
?>