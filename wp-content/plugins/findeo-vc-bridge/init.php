<?php 
/*
Plugin Name: Findeo VC Bridge
Plugin URI:
Description: Adds Visual Composer compatibiliy to Findeo theme..
Version: 1.2.92
Author: Purethems.net
Author URI: http://purethemes.net
*/

// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }

add_action('vc_after_init', 'run_findeo_vc');

function run_findeo_vc(){
  require 'vc.php';
}

?>