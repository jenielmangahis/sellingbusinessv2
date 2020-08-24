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

findeo_Kirki::add_panel( 'header_options_panel', array(
    'priority'    => 21,
    'title'       => __( 'Header', 'findeo' ),
    'description' => __( 'Header settings', 'findeo' ),
) );


/*top header*/

	
	findeo_Kirki::add_section( 'top_header', array(
		    'title'          => __( 'Top Header','findeo'  ),
		    'description'    => __( 'Top Header settings','findeo' ),
		    'panel'          => 'header_options_panel', 
		    'priority'       => 20,
		    'capability'     => 'edit_theme_options',
		    'theme_supports' => '', // Rarely needed.
		) );

		findeo_Kirki::add_field( 'findeo', array(
			'type'        => 'radio-buttonset',
			'settings'    => 'findeo_enable_topheader',
			'label'       => __( 'Switching it to ON will globally enable topbar for all pages and post', 'findeo' ),
			'section'     => 'top_header',
			'description' => __('Can be set individually on single pages and posts', 'findeo' ),
			'default'     => false,
			'priority'    => 10,
			'choices'     => array(
				true  => esc_attr__( 'Enable', 'findeo' ),
				false => esc_attr__( 'Disable', 'findeo' ),
			),
		) );

		findeo_Kirki::add_field( 'findeo', array(
		    'type'        => 'text',
		    'settings'    => 'findeo_top_header_phone',
		    'label'       => esc_html__( 'Phone number', 'findeo' ),
		    'description'    => esc_html__( 'Set empty to hide' , 'findeo' ),
		    'default'     => '',
		    'section'     => 'top_header',
		    'priority'    => 10,
		) );
		findeo_Kirki::add_field( 'findeo', array(
		    'type'        => 'text',
		    'settings'    => 'findeo_top_header_email',
		    'label'       => esc_html__( 'Email address', 'findeo' ),
		    'description'    => __( 'Set empty to hide','findeo'  ),
		    'default'     => '',
		    'section'     => 'top_header',
		    'priority'    => 10,
		) );
		findeo_Kirki::add_field( 'findeo', array(
		    'type'        => 'text',
		    'settings'    => 'findeo_top_header_dropdown',
		    'label'       => esc_html__( 'Dropdown menu title', 'findeo' ),
		    'description'    => __( 'Set empty to hide menu' ,'findeo' ),
		    'default'     => 'Dropdown menu',
		    'section'     => 'top_header',
		    'priority'    => 10,
		) );

		findeo_Kirki::add_field( 'findeo', array(
		  'type'        => 'repeater',
		  'label'       => esc_attr__( 'Social Icons', 'findeo' ),
		  'section'     => 'top_header',
		  'priority'    => 10,
		  'transport' => 'refresh',
		  'settings'    => 'findeo_top_social_icons',
		  'row_label' => array(
				'type' => 'text',
				'value' => esc_attr__('Social Icon', 'findeo' ),
			),
		
		  'fields' => array(
		      'url' => array(
		          'type'        => 'text',
		          'label'       => esc_attr__( 'URL', 'findeo' ),
		          'description' => esc_attr__( 'This will be the link of the icon', 'findeo' ),
		          'default'     => '',
		      ),  
		
		      'icon' => array(
		          'type'        => 'select',
		          'label'       => esc_attr__( 'Social Service', 'findeo' ),
		          'description' => esc_attr__( 'Choose site', 'findeo' ),
		          'default'     => '',
		          'choices'     => array(
						'' => esc_attr__( '--Choose icon--', 'findeo' ),
						'twitter' => esc_attr__( 'Twitter', 'findeo' ),
						'wordpress' => esc_attr__( 'WordPress', 'findeo' ),
						'facebook' => esc_attr__( 'Facebook', 'findeo' ),
						'linkedin' => esc_attr__( 'LinkedIN', 'findeo' ),
						'steam' => esc_attr__( 'Steam', 'findeo' ),
						'tumblr' => esc_attr__( 'Tumblr', 'findeo' ),
						'github' => esc_attr__( 'GitHub', 'findeo' ),
						'delicious' => esc_attr__( 'Delicious', 'findeo' ),
						'instagram' => esc_attr__( 'Instagram', 'findeo' ),
						'xing' => esc_attr__( 'Xing', 'findeo' ),
						'amazon' => esc_attr__( 'Amazon', 'findeo' ),
						'dropbox' => esc_attr__( 'Dropbox', 'findeo' ),
						'paypal' => esc_attr__( 'PayPal', 'findeo' ),
						'gplus' => esc_attr__( 'Google Plus', 'findeo' ),
						'stumbleupon' => esc_attr__( 'StumbleUpon', 'findeo' ),
						'yahoo' => esc_attr__( 'Yahoo', 'findeo' ),
						'pinterest' => esc_attr__( 'Pinterest', 'findeo' ),
						'dribbble' => esc_attr__( 'Dribbble', 'findeo' ),
						'flickr' => esc_attr__( 'Flickr', 'findeo' ),
						'reddit' => esc_attr__( 'Reddit', 'findeo' ),
						'vimeo' => esc_attr__( 'Vimeo', 'findeo' ),
						'spotify' => esc_attr__( 'Spotify', 'findeo' ),
						'rss' => esc_attr__( 'RSS', 'findeo' ),
						'youtube' => esc_attr__( 'YouTube', 'findeo' ),
						'blogger' => esc_attr__( 'Blogger', 'findeo' ),
						'evernote' => esc_attr__( 'Evernote', 'findeo' ),
						'digg' => esc_attr__( 'Digg', 'findeo' ),
						'fivehundredpx' => esc_attr__( '500px', 'findeo' ),
						'forrst' => esc_attr__( 'Forrst', 'findeo' ),
						'appstore' => esc_attr__( 'AppStore', 'findeo' ),
						'lastfm' => esc_attr__( 'LastFM', 'findeo' ),
						
					),
		      ),
		  )
	) );


	findeo_Kirki::add_section( 'general_header', array(
		    'title'          => __( 'General Header','findeo'  ),
		    'description'    => __( 'General Header settings','findeo' ),
		    'panel'          => 'header_options_panel', 
		    'priority'       => 20,
		    'capability'     => 'edit_theme_options',
		    'theme_supports' => '', // Rarely needed.
		) );

    findeo_Kirki::add_field( 'findeo', array(
        'type'        => 'select',
        'settings'    => 'findeo_header_layout',
        'label'       => __( 'Header layout', 'findeo' ),
        'section'     => 'general_header',
        'default'     => 'off',
        'priority'    => 10,
        'choices'     => array(
            'boxed'  => esc_attr__( 'Boxed', 'findeo' ),
            'fullwidth' => esc_attr__( 'Full-width', 'findeo' ),
        ),
    ) );      

    findeo_Kirki::add_field( 'findeo', array(
        'settings'    => 'findeo_sticky_header',
        'label'		  => 'Sticky Header',
        'description' => __( 'Switching it to ON will globally enable sticky header for all pages and post', 'findeo' ),
        'section'     => 'general_header',
        'type'        => 'radio',
		'default'     => 0,
		'priority'    => 10,
		'choices'     => array(
			true  => esc_attr__( 'Enable', 'findeo' ),
			false => esc_attr__( 'Disable', 'findeo' ),
		),
    ) );    

    findeo_Kirki::add_field( 'findeo', array(
		'type'        => 'radio',
		'settings'    => 'header_bar_style',
		'label'       => __( 'Header style', 'findeo' ),
		'section'     => 'general_header',
		'default'     => 'standard',
		'priority'    => 10,
		'choices'     => array(
			'standard'  => esc_attr__( 'Standard', 'findeo' ),
			'alt' 		=> esc_attr__( 'Alternative', 'findeo' ),
		),
	) );

    findeo_Kirki::add_field( 'findeo', array(
        'type'        => 'radio',
        'settings'    => 'findeo_my_account_display',
        'label'       => __( 'Display "My account" button in header', 'findeo' ),
        'section'     => 'general_header',
        'default'     => 0,
		'priority'    => 10,
		'choices'     => array(
			true  => esc_attr__( 'Enable', 'findeo' ),
			false => esc_attr__( 'Disable', 'findeo' ),
		),
    ) );    
    findeo_Kirki::add_field( 'findeo', array(
        'type'        => 'radio',
        'settings'    => 'findeo_submit_display',
        'label'       => __( 'Display "Submit Property" button in header', 'findeo' ),
        'section'     => 'general_header',
        'default'     => 0,
		'priority'    => 10,
		'choices'     => array(
			true  => esc_attr__( 'Enable', 'findeo' ),
			false => esc_attr__( 'Disable', 'findeo' ),
		),
    ) );


?>