<?php
defined('ABSPATH') || exit;

class NextendSmartSlider3Gutenberg {

    public function __construct() {

        add_action('enqueue_block_editor_assets', array(
            $this,
            'enqueue_block_editor_assets'
        ));
    }

    public function enqueue_block_editor_assets() {

        wp_enqueue_script('gutenberg-smartslider3', plugins_url('block.js', __FILE__), array(
            'wp-blocks',
            'wp-i18n',
            'wp-element',
            'underscore'
        ), filemtime(plugin_dir_path(__FILE__) . 'block.js'));

        wp_add_inline_script('gutenberg-smartslider3', 'window.gutenberg_smartslider3=' . json_encode(array(
                'template' => \N2SS3Shortcode::renderIframe('{{{slider}}}')
            )) . ';');

        \N2SS3Shortcode::forceIframe('gutenberg');
        wp_enqueue_style('gutenberg-smartslider3', plugins_url('style.min.css', __FILE__), array('wp-blocks'), filemtime(plugin_dir_path(__FILE__) . 'style.min.css'));
    

    }
}

new NextendSmartSlider3Gutenberg();