<?php 

findeo_Kirki::add_panel( 'properties_panel', array(
    'priority'    => 30,
    'title'       => __( 'Properties & Agencies', 'findeo' ),
    'description' => __( 'Properties settings', 'findeo' ),
) );

findeo_Kirki::add_section( 'properties_list', array(
	    'title'          => esc_html__( 'Properties Archive Options', 'findeo'  ),
	    'description'    => esc_html__( 'Archive page related options', 'findeo'  ),
	    'panel'          => 'properties_panel', // Not typically needed.
	    'priority'       => 30,
	    'capability'     => 'edit_theme_options',
	    'theme_supports' => '', // Rarely needed.
	) );

	findeo_Kirki::add_field( 'findeo', array(
	    'type'        => 'text',
	    'settings'    => 'findeo_properties_archive_title',
	    'label'       => esc_html__( 'Properties archive title', 'findeo' ),
	    'default'     => 'Listings',
	    'section'     => 'properties_list',
	    'priority'    => 10,
	) );

	findeo_Kirki::add_field( 'findeo', array(
	    'type'        => 'text',
	    'settings'    => 'findeo_properties_archive_subtitle',
	    'label'       => esc_html__( 'Properties archive subtitle', 'findeo' ),
	    'default'     => 'Latest Properties',
	    'section'     => 'properties_list',
	    'priority'    => 10,
	) );

    findeo_Kirki::add_field( 'findeo', array(
		'type'        => 'radio',
		'settings'    => 'findeo_properties_gallery_on_list',
		'label'       => __( 'Gallery in thumbnails', 'findeo' ),
		'description' => esc_html__( 'On properties list, show gallery slider instead of thumbnail', 'findeo' ),
		'section'     => 'properties_list',
		'default'     => 'gallery',
		'priority'    => 10,
		'choices'     => array(
			'image'   => esc_attr__( 'Single Featured Image', 'findeo' ),
			'gallery' => esc_attr__( 'Gallery slider (if set)', 'findeo' ),
		),
	) );


	findeo_Kirki::add_field( 'findeo', array(
	    'type'        => 'select',
	    'settings'     => 'pp_properties_top_layout',
	    'label'       => esc_html__( 'Properties archive general layout', 'findeo' ),
	    'description' => esc_html__( 'Choose the general archive  layout', 'findeo' ),
	    'section'     => 'properties_list',
	    'default'     => 'list_with_sidebar',
	    'priority'    => 10,
	    'choices'     => array(
	       'titlebar' => esc_attr__( 'Standard titlebar', 'findeo' ),
	       'map' => esc_attr__( 'Map on top', 'findeo' ),
	       'searchfw' => esc_attr__( 'Full Width Search form ', 'findeo' ),
	       'half' => esc_attr__( 'Split Map/Content', 'findeo' ),
	       'disable' => esc_attr__( 'Disable titlebar', 'findeo' ),
 		),	
	));

	findeo_Kirki::add_field( 'findeo', array(
		'type'        => 'radio',
		'settings'    => 'findeo_properties_map_type',
		'label'       => __( 'Map content type', 'findeo' ),
		'description' => esc_html__( 'How the markers are added to map', 'findeo' ),
		'section'     => 'properties_list',
		'default'     => 'dynamic',
		'priority'    => 10,
		'choices'     => array(
			'dynamic'   => esc_attr__( 'Map shows only markers based on currently displayed properties', 'findeo' ),
			'static' => esc_attr__( 'Map shows all markers ignoring the search form', 'findeo' ),
		),
		'active_callback'  => array(
            array(
                'setting'  => 'pp_properties_top_layout',
                'operator' => '==',
                'value'    => 'map',
            ),
        )
	) );


	findeo_Kirki::add_field( 'findeo', array(
	    'type'        => 'select',
	    'settings'     => 'pp_properties_layout',
	    'label'       => esc_html__( 'Properties content layout', 'findeo' ),
	    'description' => esc_html__( 'Choose the general archive content  layout', 'findeo' ),
	    'section'     => 'properties_list',
	    'default'     => 'list',
	    'priority'    => 10,
	    'choices'     => array(
	       'list' => esc_attr__( 'List', 'findeo' ),
	       'grid' => esc_attr__( 'Grid', 'findeo' ),
	       'grid-three' => esc_attr__( 'Grid 3 columns', 'findeo' ),
	       'compact' => esc_attr__( 'Compact Grid', 'findeo' ),
 		),	
	));

	findeo_Kirki::add_field( 'findeo', array(
	    'type'        => 'radio-image',
	    'settings'     => 'pp_properties_sidebar_layout',
	    'label'       => esc_html__( 'Sidebar side', 'findeo' ),
	    'description' => esc_html__( 'Applies if the choosen layout has sidebar', 'findeo' ),
	    'section'     => 'properties_list',
	    'default'     => 'right-sidebar',
	    'priority'    => 10,
	    'choices'     => array(
	        'full-width' => trailingslashit( trailingslashit( get_template_directory_uri() )) . '/images/full-width.png',
	        'left-sidebar' => trailingslashit( trailingslashit( get_template_directory_uri() )) . '/images/left-sidebar.png',
	        'right-sidebar' => trailingslashit( trailingslashit( get_template_directory_uri() )) . '/images/right-sidebar.png',
	    ),	

	));

	findeo_Kirki::add_field( 'findeo', array(
	    'type'        => 'upload',
	    'settings'     => 'pp_properties_header_upload',
	    'label'       => esc_html__( 'Header image', 'findeo' ),
	    'description' => esc_html__( 'Used on Properties archive page. Set image for header, should be 1920px wide', 'findeo' ),
	    'section'     => 'properties_list',
	    'default'     => '',
	    'priority'    => 10,
	) );

findeo_Kirki::add_section( 'agencies', array(
	    'title'          => esc_html__( 'Agencies archive Options', 'findeo'  ),
	    'description'    => esc_html__( 'Archive page related options', 'findeo'  ),
	    'panel'          => 'properties_panel', // Not typically needed.
	    'priority'       => 30,
	    'capability'     => 'edit_theme_options',
	    'theme_supports' => '', // Rarely needed.
	) );	

	findeo_Kirki::add_field( 'findeo', array(
	    'type'        => 'text',
	    'settings'    => 'findeo_agencies_archive_title',
	    'label'       => esc_html__( 'Agencies archive title', 'findeo' ),
	    'default'     => 'Agencies',
	    'section'     => 'agencies',
	    'priority'    => 10,
	) );

	findeo_Kirki::add_field( 'findeo', array(
	    'type'        => 'text',
	    'settings'    => 'findeo_agencies_archive_subtitle',
	    'label'       => esc_html__( 'Agencies archive subtitle', 'findeo' ),
	    'default'     => 'Award winning agencies',
	    'section'     => 'agencies',
	    'priority'    => 10,
	) );
 ?>