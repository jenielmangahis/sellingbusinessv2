<?php

class DiviSmartSlider3Extension extends DiviExtension {

    public $gettext_domain = 'smart-slider-3';

    public $name = 'smart-slider-3';

    public $version = '1.0.0';

    public function __construct($name = 'smart-slider-3', $args = array()) {
        $this->plugin_dir     = plugin_dir_path(__FILE__);
        $this->plugin_dir_url = plugin_dir_url(__FILE__);


        parent::__construct($name, $args);

        add_action('admin_enqueue_scripts', array(
            $this,
            'admin_enqueue_scripts'
        ));

    }

    /**
     * Enqueues minified, production javascript bundles.
     *
     * @since 3.1
     */
    protected function _enqueue_bundles() {

        if (et_core_is_fb_enabled()) {
            // Builder Bundle
            $bundle_url = "{$this->plugin_dir_url}scripts/builder-bundle.min.js";

            wp_enqueue_script("{$this->name}-builder-bundle", $bundle_url, $this->_bundle_dependencies['builder'], $this->version, true);
        }
    }

    /**
     * Sets initial value of {@see self::$_bundle_dependencies}.
     *
     * @since 3.1
     */
    protected function _set_bundle_dependencies() {
        $this->_bundle_dependencies = array(
            'builder' => array(
                'react',
                'react-dom'
            )
        );
    }

    /**
     * Enqueues the extension's scripts and styles.
     * {@see 'wp_enqueue_scripts'}
     *
     * @since 3.1
     */
    public function wp_hook_enqueue_scripts() {

        if (et_core_is_fb_enabled()) {

            $styles_url = "{$this->plugin_dir_url}styles/style.min.css";

            wp_enqueue_style("{$this->name}-styles", $styles_url, array(), $this->version);
        }

        $this->_enqueue_bundles();
    }

    public function admin_enqueue_scripts() {

        $styles_url = "{$this->plugin_dir_url}styles/admin/style.min.css";

        wp_enqueue_style("{$this->name}-admin-styles", $styles_url, array(), $this->version);

        ?>
        <script>
            if (typeof localStorage !== 'undefined') {
                localStorage.removeItem('et_pb_templates_et_pb_nextend_smart_slider_3');
                localStorage.removeItem('et_pb_templates_et_pb_nextend_smart_slider_3_fullwidth');
            }
        </script>
        <?php
    }
}

new DiviSmartSlider3Extension;
