<?php 
 
if ( !defined('ABSPATH')) exit;

/************************************/
// WP Common functions
/************************************/


/** *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 */


if ( ! function_exists( 'sba_setup' ) ) :
/**
 * Sets up theme defaults and registers support for various WordPress features.
 *
 * Note that this function is hooked into the after_setup_theme hook, which
 * runs before the init hook. The init hook is too late for some features, such
 * as indicating support for post thumbnails.
 */
function sba_setup() {

	// Add default posts and comments RSS feed links to head.
	//add_theme_support( 'automatic-feed-links' );

	/*
	 * Let WordPress manage the document title.
	 * By adding theme support, we declare that this theme does not use a
	 * hard-coded <title> tag in the document head, and expect WordPress to
	 * provide it for us.
	 */
	//add_theme_support( 'title-tag' );

	/*
	 * Enable support for Post Thumbnails on posts and pages.
	 *
	 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
	 */
	//add_theme_support( 'post-thumbnails' );
	
	
	add_image_size( 'properties_banner', 520, 1900, true );
	//add_image_size( 'testi_photo', 73, 73, true );
	//
	
}
endif;
	add_action( 'after_setup_theme', 'sba_setup' );


/************************************/
// REMOVE OTHER ELEMENTS THAT NEEDS TO HIDE / START
/************************************/


/************************************/
// REMOVE HELP AFTER THE ADMIN BAR IN ADMIN AREA / START
/************************************/
function wpse50723_remove_help($old_help, $screen_id, $screen){
	
	$screen->remove_help_tabs();
	return $old_help;
	
}

add_filter( 'contextual_help', 'wpse50723_remove_help', 999, 3 );
/************************************/
// REMOVE HELP AFTER THE ADMIN BAR IN ADMIN AREA / END
/************************************/



// Remove the Admin Toolbar
add_filter('show_admin_bar', '__return_false');


/************************************/
// ADD CLASS FPR NEXT AND PREV LINK / START
/************************************/
