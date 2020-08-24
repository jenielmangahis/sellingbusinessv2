<?php 
if ( !defined('ABSPATH')) exit;

/************************************/
// LOAD/UNLOAD CSS & JS / START
/************************************/
if (!is_admin()) {
	
	function enqueue_child_theme_styles() {
		wp_enqueue_style( 'jquery-theme-style', 'https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css' );
		wp_enqueue_style( 'parent-style', get_template_directory_uri().'/style.css' );
		wp_enqueue_style( 'custom-style', get_stylesheet_directory_uri().'/assets/css/custom.css' );
		
	
		//wp_enqueue_script( 'jquery-js', 'https://code.jquery.com/jquery-1.12.4.js');
		wp_enqueue_script( 'jquery-js-ui', 'https://code.jquery.com/ui/1.12.1/jquery-ui.js', array( 'jquery' ));
		wp_enqueue_script( 'sellingbusiness-script', get_stylesheet_directory_uri() . '/assets/js/scripts.js', array( 'jquery' ) );
	}
	add_action( 'wp_enqueue_scripts', 'enqueue_child_theme_styles', PHP_INT_MAX);
}



/************************************/
// LOAD CSS / END
/************************************/


/************************************/
// ADD FONT / START DECLARE ALL YOUR FONTS HERE FROM GOOGLE
/************************************/
function init_addfont() {

	if (!is_admin()) {
/*
	font-family: 'Roboto', sans-serif;
		light 100
		regular 400
		semi 500
		bold 700
	
*/
		wp_register_style( 'addfont-Roboto', 'https://fonts.googleapis.com/css?family=Roboto:100,400,500,700&ver=0.0.1');
		wp_enqueue_style( 'addfont-Roboto');
	}
}
//add_action('init', 'init_addfont');

/************************************/
// ADD FONT / END
/************************************/
