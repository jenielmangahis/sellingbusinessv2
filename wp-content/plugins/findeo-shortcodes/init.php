<?php 
/*
Plugin Name: Findeo Shortcodes
Plugin URI:
Description: Shortcodes required by Findeo Theme.
Version: 1.2.7
Author: Purethemes.net
Author URI: http://purethemes.net
Text Domain: findeo-shortcodes
Domain Path: /languages
*/

// Begin Shortcodes
class FindeoShortcodes {
    
    function __construct() {
    
        //Initialize shortcodes
        add_action( 'init', array( $this, 'add_shortcodes' ) );
        add_action( 'init', array( $this, 'load_localisation' ), 0 );    
    }


    public function load_localisation () {
        load_plugin_textdomain( 'findeo-shortcodes', false,  basename( dirname( __FILE__ ) ) . '/languages/' );

    } 
    function add_shortcodes() {

        $shortcodes = array(
            'recent-properties',
            'headline',
            'iconbox',
            'imagebox',
            'posts-carousel',
            'flip-banner',
            'testimonials',
            'pricing-table',
            'pricingwrapper',
            'logo-slider',
            'fullwidth-property-slider',
            'counters',
            'counter',
            'agents',
            'agents-carousel',
            'address-box',
            'button',
            'alertbox',
            'list',
            'pricing-tables-wc'

        );

        foreach ( $shortcodes as $shortcode ) {
            $function = 'findeo_' . str_replace( '-', '_', $shortcode );
            
            include_once wp_normalize_path( dirname( __FILE__ ) . '/shortcodes/'.$shortcode.'.php' );
            add_shortcode( $shortcode, $function);
          
        }
    }

     public static function get_filters($categories = false) {
        if(!empty($categories)) {
            if(is_array($categories)){
                $terms = array();
                foreach ($categories as $cat) {
                      $term = get_term_by('slug', $cat, 'filters'); 
                      $terms[] = $term;
                }
            } 
          
        } else {
            $terms = get_terms("filters");    
        }
        
        $count = count($terms);

        if ( $count > 0 ){ 
            $output = '
            <div id="filters">
                <ul class="option-set alt">
                    <li><a href="#filter" class="selected" data-filter="*">'.__('All', 'findeo-shortcodes').'</a></li>';
                    foreach ( $terms as $term ) {
                        $output .= '<li><a href="#filter" data-filter=".'.$term->slug.'">'. $term->name .'</a></li>';
                    } 
                $output .= '</ul>
                <div class="clearfix"></div>
            </div>';
            return $output;
        }
    }   

    
}

new FindeoShortcodes();
?>