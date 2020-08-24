<?php

findeo_Kirki::add_section( 'general', array(
    'title'          => esc_html__( 'General Options', 'findeo'  ),
    'description'    => esc_html__( 'General options', 'findeo'  ),
    'panel'          => '', // Not typically needed.
    'priority'       => 10,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '', // Rarely needed.
) );

 	
	findeo_Kirki::add_field( 'findeo', array(
		  'type'        => 'repeater',
		  'label'       => esc_attr__( 'Sidebar generator', 'findeo' ),
		  'section'     => 'general',
		  'priority'    => 10,
		  'settings'    => 'pp_findeo_sidebar',

		  'fields' => array(
		      'sidebar_name' => array(
		          'type'        => 'text',
		          'label'       => esc_attr__( 'Sidebar name', 'findeo' ),
		          'description' => esc_attr__( 'This will be name of sidebar', 'findeo' ),
		          'default'     => 'Sidebar name',
		      ),
		      'sidebar_id' => array(
		          'type'        => 'text',
		          'label'       => esc_attr__( 'Sidebar ID', 'findeo' ),
		          'description' => esc_attr__( 'Replace x with a number', 'findeo' ),
		          'default'     => 'sidebar_id_x',
		
		      ),
		  )
		) );
 ?>