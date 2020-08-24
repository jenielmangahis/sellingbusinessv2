<?php 



findeo_Kirki::add_section( 'homepage', array(
    'title'          => esc_html__( 'Home Page Options', 'findeo'  ),
    'description'    => esc_html__( 'Options for Page with Search', 'findeo'  ),
    'priority'       => 21,
    'capability'     => 'edit_theme_options',
    'theme_supports' => '', // Rarely needed.
) );
		
		findeo_Kirki::add_field( 'findeo', array(
		    'type'        => 'text',
		    'settings'     => 'pp_home_title',
		    'label'       => esc_html__( 'Search Banner Title', 'findeo' ),
		    'description' => __( 'Text above search form ', 'findeo' ),
		    'section'     => 'homepage',
		    'default'     => esc_html__('Find Your Dream Home','findeo') ,
		    'priority'    => 1,
		) );	

		findeo_Kirki::add_field( 'findeo', array(
		    'type'        => 'image',
		    'settings'     => 'findeo_search_bg',
		    'label'       => esc_html__( 'Background for search banner on homepage', 'findeo' ),
		    'description' => esc_html__( 'Set image for search banner, should be 1920px wide', 'findeo' ),
		    'section'     => 'homepage',
		    'default'     => '',
		    'priority'    => 2,
		) );

		findeo_Kirki::add_field( 'findeo', array(
			'type'        => 'slider',
			'settings'    => 'findeo_search_bg_opacity',
			'label'       => esc_html__( 'Banner opacity', 'findeo' ),
			'section'     => 'homepage',
			'default'     => '0.45',
			'choices'     => array(
				'min'  => '0',
				'max'  => '1',
				'step' => '0.01',
			),
			'priority'    => 3,
		) ); 

		findeo_Kirki::add_field( 'findeo', array(
		    'type'        => 'color',
		    'settings'     => 'findeo_search_color',
		    'label'       => esc_html__( 'Color for the image overlay on homepage search banner', 'findeo' ),
		    'section'     => 'homepage',
		    'default'     => '#36383e',
		    'priority'    => 4,
		) );

		findeo_Kirki::add_field( 'findeo', array(
		    'type'        => 'image',
		    'settings'    => 'findeo_search_video_poster',
		    'label'       => esc_html__( 'Video Poster', 'findeo' ),
		    'section'     => 'homepage',
		    'default'     => false,
		    'priority'    => 5,
		) );

		findeo_Kirki::add_field( 'findeo', array(
	    'type'        => 'upload',
	    'settings'    => 'findeo_search_video_webm',
	    'label'       => esc_html__( 'Upload webm file', 'findeo' ),
	    'section'     => 'homepage',
	    'default'     => false,
	    'priority'    => 6,
	    
		) );
		findeo_Kirki::add_field( 'findeo', array(
		    'type'        => 'upload',
		    'settings'    => 'findeo_search_video_mp4',
		    'label'       => esc_html__( 'Upload mp4 file', 'findeo' ),
		    'section'     => 'homepage',
		    'default'     => false,
		    'priority'    => 7,
		    
		) );
	

		findeo_Kirki::add_field( 'findeo', array(
		    'type'        => 'color',
		    'settings'     => 'findeo_video_search_color',
		    'label'       => esc_html__( 'Video overlay color and opacity', 'findeo' ),
		    'section'     => 'homepage',
		    'default'     => 'rgba(22,22,22,0.4)',
		    'priority'    => 9,
		    'choices'     => array(
				'alpha' => true,
			),
		) );
?>