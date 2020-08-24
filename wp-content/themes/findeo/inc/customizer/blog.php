<?php 
global $wpdb;

$rev_sliders = array();
// Table name
$table_name = $wpdb->prefix . "revslider_sliders";

// Get sliders
if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") == $table_name) {
	$sliders = $wpdb->get_results( "SELECT alias, title FROM $table_name" );
} else {
	$sliders = '';
}
$rev_sliders[] = esc_html__("--Select slider--","findeo");
// Iterate over the sliders
if($sliders) {
	foreach($sliders as $key => $item) {
	  $rev_sliders[$item->alias] = $item->title;
	}
} else {
	$rev_sliders = array();
}

findeo_Kirki::add_panel( 'blog_panel', array(
    'priority'    => 30,
    'title'       => __( 'Blog', 'findeo' ),
    'description' => __( 'Blog related settings', 'findeo' ),
) );

findeo_Kirki::add_section( 'blog', array(
	    'title'          => esc_html__( 'Blog Options', 'findeo'  ),
	    'description'    => esc_html__( 'Blog related options', 'findeo'  ),
	    'panel'          => 'blog_panel', // Not typically needed.
	    'priority'       => 30,
	    'capability'     => 'edit_theme_options',
	    'theme_supports' => '', // Rarely needed.
	) );



	findeo_Kirki::add_field( 'findeo', array(
	    'type'        => 'radio-image',
	    'settings'     => 'pp_blog_layout',
	    'label'       => esc_html__( 'Blog layout', 'findeo' ),
	    'description' => esc_html__( 'Choose the sidebar side for blog', 'findeo' ),
	    'section'     => 'blog',
	    'default'     => 'right-sidebar',
	    'priority'    => 10,
	    'choices'     => array(
	       // 'full-width' => trailingslashit( trailingslashit( get_template_directory_uri() )) . '/images/full-width.png',
	        'left-sidebar' => trailingslashit( trailingslashit( get_template_directory_uri() )) . '/images/left-sidebar.png',
	        'right-sidebar' => trailingslashit( trailingslashit( get_template_directory_uri() )) . '/images/right-sidebar.png',
	    ),	

	));
	
	findeo_Kirki::add_field( 'findeo', array(
	    'type'        => 'multicheck',
	    'settings'    => 'pp_meta_single',
	    'label'       => esc_html__( 'Post meta informations on single post', 'findeo' ),
	    'description' => esc_html__( 'Set which elements of posts meta data you want to display', 'findeo' ),
	    'section'     => 'blog',
	    'default'     => array('author'),
	    'priority'    => 10,
	    'choices'     => array(
	        'author' 	=> esc_html__( 'Author', 'findeo' ),
	        'date' 		=> esc_html__( 'Date', 'findeo' ),
	        'tags' 		=> esc_html__( 'Tags', 'findeo' ),
	        'cat' 		=> esc_html__( 'Categories', 'findeo' ),
	    ),
	) );
	
	findeo_Kirki::add_field( 'findeo', array(
	    'type'        => 'multicheck',
	    'settings'     => 'pp_post_share',
	    'label'       => esc_html__( 'Share buttons on single post', 'findeo' ),
	    'description' => esc_html__( 'Set which share buttons you want to display on single blog post', 'findeo' ),
	    'section'     => 'blog',
	    'default'     => array('author'),
	    'priority'    => 10,
	    'choices'     => array(
	        'facebook' 	=> esc_html__( 'Facebook', 'findeo' ),
	        'twitter' 		=> esc_html__( 'Twitter', 'findeo' ),
	        'google-plus' 		=> esc_html__( 'Google Plus', 'findeo' ),
	        'pinterest' 		=> esc_html__( 'Pinterest', 'findeo' ),
	    ),
	) );

	findeo_Kirki::add_field( 'findeo', array(
	    'type'        => 'multicheck',
	    'settings'     => 'pp_blog_meta',
	    'label'       => esc_html__( 'Post meta informations on blog post', 'findeo' ),
	    'description' => esc_html__( 'Set which elements of posts meta data you want to display on blog and archive pages', 'findeo' ),
	    'section'     => 'blog',
	    'default'     => array('author'),
	    'priority'    => 10,
	    'choices'     => array(
	        'author' 	=> esc_html__( 'Author', 'findeo' ),
	        'date' 		=> esc_html__( 'Date', 'findeo' ),
	        'tags' 		=> esc_html__( 'Tags', 'findeo' ),
	        'cat' 		=> esc_html__( 'Categories', 'findeo' ),
	        'com' 		=> esc_html__( 'Comments', 'findeo' ),
	    ),
	) );


/*blog header*/

findeo_Kirki::add_section( 'blog_header', array(
	    'title'          => esc_html__( 'Blog Header', 'findeo' ),
	    'description'    => esc_html__( 'Header settings', 'findeo' ),
	    'panel'          => 'blog_panel', 
	    'priority'       => 160,
	    'capability'     => 'edit_theme_options',
	    'theme_supports' => '', // Rarely needed.
	) );

	findeo_Kirki::add_field( 'findeo', array(
	    'type'        => 'text',
	    'settings'    => 'findeo_blog_title',
	    'label'       => esc_html__( 'Blog page title', 'findeo' ),
	    'default'     => 'Blog',
	    'section'     => 'blog_header',
	    'priority'    => 10,
	) );

	findeo_Kirki::add_field( 'findeo', array(
	    'type'        => 'text',
	    'settings'    => 'findeo_blog_subtitle',
	    'label'       => esc_html__( 'Blog page subtitle', 'findeo' ),
	    'default'     => 'Latest News',
	    'section'     => 'blog_header',
	    'priority'    => 10,
	) );




?>