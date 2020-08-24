<?php
/*
Plugin Name: Web Fonts Social Icons WP
Plugin URI: http://www.purethemes.net
Description: This plugin gives you a shortcode & widget to display retina ready web font based social icons.
Version: 1.4
Author: Purethems.net
Author URI: http://purethemes.net
*/

class PureThemes_SocialIcons {

    private $pt_icons = array(
        "twitter" => 'Twitter',
        "wordpress" => 'WordPress',
        "facebook" => 'Facebook',
        "linkedin" => 'LinkedIn',
        "steam" => 'Steam',
        "tumblr" => 'Tumblr',
        "github" => 'GitHub',
        "delicious" => 'Delicious',
        "instagram" => 'Instagram',
        "xing" => 'Xing',
        "amazon" => 'Amazon',
        "dropbox" => 'Dropbox',
        "paypal" => 'PayPal',
        "lastfm" => 'LastFM',
        "gplus" => 'Google+',
        "yahoo" => 'Yahoo',
        "pinterest" => 'Pinterest',
        "dribbble" => 'Dribbble',
        "flickr" => 'Flickr',
        "reddit" => 'Reddit',
        "vimeo" => 'Vimeo',
        "spotify" => 'Spotify',
        "rss" => 'RSS',
        'youtube' => 'YouTube',
        'blogger' => 'Blogger',
        'appstore' => 'AppStore',
        'digg' => 'Digg',
        'evernote' => 'Evernote',
        'fivehundredpx' => '500px',
        'forrst' => 'Forrst',
        'stumbleupon' => 'StumbleUpon'
    );

    function __construct() {
        if (!defined('WFSI_TINYMCE_URI')) define('WFSI_TINYMCE_URI', plugin_dir_url( __FILE__ ));
        add_action('init', array($this, 'init'));
        add_action('admin_init', array($this, 'admin_init'));
        add_action( 'customize_controls_enqueue_scripts', array($this, 'theme_customize_script'));
    }

    function admin_init() {
        // js
        wp_enqueue_style( 'wfsi-social-widget', plugins_url( '/css/style.css' , __FILE__ ));
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        global $pagenow;
        if(  $pagenow == 'post.php') {
            wp_enqueue_script( 'wfsi-shortcode', WFSI_TINYMCE_URI . '/js/shortcode.js', false, '1.0', false );
        }
        if ($pagenow == 'widgets.php') {
            wp_enqueue_script( 'wfsi-custom', WFSI_TINYMCE_URI . '/js/custom.js', false, '1.0', false );
        }
    }

    function theme_customize_script() {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery-ui-core' );
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_style( 'wfsi-social-widget', plugins_url( '/css/style.css' , __FILE__ ));
        wp_enqueue_script( 'wfsi-custom', WFSI_TINYMCE_URI . '/js/custom.js', false, '1.0', false );
    }
    public function getIconsArray() {
        return $this->pt_icons;
    }

    function add_rich_plugins( $plugin_array ) {
        $plugin_array['wfsi'] = WFSI_TINYMCE_URI . '/js/plugin.js';
        return $plugin_array;
    }

    function register_rich_buttons( $buttons ) {
        array_push( $buttons, "|", 'wfsi_button' );
        return $buttons;
    }

    function init() {
        if( ! is_admin() ) {
            wp_enqueue_style( 'wfsi-socialicons', plugin_dir_url( __FILE__ ) . 'css/icons.css' );
            $options = get_option( 'pp_social_icons_colors', array('icons_color' => '#A0A0A0', 'bg_color' => '#F2F2F2' ) );
            $custom_css = "
                    a.ptwsi-social-icon,
                    a.ptwsi-social-icon:visited,
                    .ptwsi_social-icons li a:visited,
                    .ptwsi_social-icons li a {
                            color: ".$options['icons_color'].";
                            background:  ".$options['bg_color'].";
                    }";
            wp_add_inline_style( 'wfsi-socialicons', $custom_css );
        }

        if ( get_user_option('rich_editing') == 'true' ) {
            add_filter( 'mce_external_plugins', array(&$this, 'add_rich_plugins') );
            add_filter( 'mce_buttons', array(&$this, 'register_rich_buttons') );
        }
    }
}

//initilize widget
$pt_socials = new PureThemes_SocialIcons();


/**
* Social icons shortcodes
*
*/
if ( ! function_exists( 'wfsi_social_icon' ) ) :
    function wfsi_social_icon($atts) {
        extract(shortcode_atts(array(
            "service" => 'facebook',
            "type" => '',   //single (just anchor)
            "iconsize" => '', //small

            "url" => '',
            "target" => '',
            ), $atts));

        if($type == 'single') {
            $output = '<a target="'.$target.'" class="'.$service.' '.$iconsize.' ptwsi-social-icon" href="'.$url.'"><i class="ptwsi-icon-'.$service.'"></i></a>';
        } else {
            $output = '<li><a target="'.$target.'" class="'.$service.' '.$iconsize.' ptwsi-social-icon" href="'.$url.'"><i class="ptwsi-icon-'.$service.'"></i></a></li>';
        }
        return $output;
    }
    add_shortcode('pt_social_icon', 'wfsi_social_icon');
endif;

if ( ! function_exists( 'wfsi_social_icons' ) ) :
    function wfsi_social_icons($atts,$content ) {
        extract(shortcode_atts(
            array(
                'title'=>"Social Icons",
                "rounded" => '',
                "border" => '',
                "color" => '',    
                ), $atts));

        $css_class = ($rounded) ? 'rounded ' : ' ' ;
        $css_class .= ($border) ? 'border ' : ' ' ;
        $css_class .= ($color) ? 'color ' : ' ' ;
        $output = '<ul class="ptwsi_social-icons ptwsi  '.$css_class.' ">'.do_shortcode( $content ).'</ul><div class="clearfix"></div>';
        return $output;
    }
    add_shortcode('pt_social_icons', 'wfsi_social_icons');
endif;

require_once('socialicons-widget.php');


// Settings page for icons
class PureThemes_SocialIcons_SettingsPage
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page() {
        // This page will be under "Settings"
        $settings = add_theme_page(
            'Settings Admin',
            'Web Font Social Icons',
            'manage_options',
            'social-icons-admin',
            array( $this, 'create_admin_page' )
        );
        add_action( 'load-' . $settings, array($this, 'add_styles_scripts' ));
    }

    /**
     * Options page callback
     */
    public function create_admin_page() {
        // Set class property
        $this->options = get_option( 'pp_social_icons_colors' ); ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2><?php _e('Web Font Social Icons','wfsi') ?></h2>
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'social_option_group' );
                do_settings_sections( 'social-icons-admin' );
                submit_button();
            ?>
            </form>
        </div>
        <?php
    }

    function add_styles_scripts() {
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );
        //Load our custom Javascript file
        wp_enqueue_script( 'wp-color-picker-settings', plugin_dir_url(__FILE__) . 'js/settings.js' );
    }

    /**
     * Register and add settings
     */

    public function page_init() {
        register_setting(
            'social_option_group', // Option group
            'pp_social_icons_colors', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'social_option_section', // ID
            __('Color settings','wfsi'), // Title
            array( $this, 'print_section_info' ), // Callback
            'social-icons-admin' // Page
        );

        add_settings_field(
            'icons_color', // ID
            __('Icons color','wfsi'), // Title
            array( $this, 'icons_color_callback' ), // Callback
            'social-icons-admin', // Page
            'social_option_section' // Section
        );

        add_settings_field(
            'bg_color', // ID
            __('Background color', 'wfsi'), // Title
            array( $this, 'bg_color_callback' ), // Callback
            'social-icons-admin', // Page
            'social_option_section' // Section
        );


    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['icons_color'] ) )
            $new_input['icons_color'] = sanitize_text_field( $input['icons_color'] );

        if( isset( $input['bg_color'] ) )
            $new_input['bg_color'] = sanitize_text_field( $input['bg_color'] );

        return $new_input;
    }

    /**
     * Print the Section text
     */
    public function print_section_info()
    {
        _e('<p>Choose default colors for icons background and color. This will affect all icons added to post/pages content. <br/> For widget you can choose
        separate color scheme for each widget settings.</p>','wfsi');
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function icons_color_callback()
    {
        printf(
            '<input class="wp-color-picker" data-default-color="#A0A0A0" type="text" id="icons_color" name="pp_social_icons_colors[icons_color]" value="%s" />',
            isset( $this->options['icons_color'] ) ? esc_attr( $this->options['icons_color']) : '#A0A0A0'
        );
    }

    /**
     * Get the settings option array and print one of its values
     */
    public function bg_color_callback()
    {
        printf(
            '<input class="wp-color-picker" data-default-color="#F2F2F2" type="text" id="bg_color" name="pp_social_icons_colors[bg_color]" value="%s" />',
            isset( $this->options['bg_color'] ) ? esc_attr( $this->options['bg_color']) : '#F2F2F2'
        );
    }
}

if( is_admin() ) {
    $pt_settings_page = new PureThemes_SocialIcons_SettingsPage();
    }
?>