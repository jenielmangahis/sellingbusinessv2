<?php

class N2SS3Shortcode {

    public static $iframe = false;

    public static $iframeReason = '';

    public static function forceIframe($reason) {
        self::$iframe       = true;
        self::$iframeReason = $reason;
    }

    public static function doShortcode($parameters) {

        if (is_admin() && isset($_GET['nextendajax'])) {
            return '';
        }

        if (!empty($parameters['alias'])) {
            $parameters['slider'] = $parameters['alias'];
        }

        if (self::$iframe) {
            if (isset($parameters['slider'])) {
                return self::renderIframe($parameters['slider']);
            }

            return 'Smart Slider - Please select a slider!';
        }

        return self::render($parameters);
    }

    public static function renderIframe($sliderIDorAlias) {
        $script = file_get_contents(dirname(__FILE__) . '/iframe.min.js');
    

        $attributes = array(
            'class'       => "n2-ss-slider-frame",
            'style'       => 'width:100%;display:block;border:0;',
            'frameborder' => 0,
            'src'         => site_url() . '?n2prerender=1&n2app=smartslider&n2controller=slider&n2action=iframe&sliderid=' . $sliderIDorAlias . '&hash=' . md5($sliderIDorAlias . NONCE_SALT)
        );
        $html       = '';

        switch (self::$iframeReason) {
            case 'divi':
                $attributes['onload'] = str_replace(array(
                        "\n",
                        "\r",
                        "\r\n",
                        '"',
                    ), array(
                        "",
                        "",
                        "",
                        "'"
                    ), $script) . 'n2SSIframeLoader(this);';
                break;
            case 'visualcomposer':
            default:
                $attributes['onload'] = str_replace(array(
                        "\n",
                        "\r",
                        "\r\n"
                    ), "", $script) . 'n2SSIframeLoader(this);';
                break;
        }

        return $html . '<div>' . N2HTML::tag('iframe', $attributes) . '</div>';
    }

    public static function render($parameters, $usage = 'WordPress Shortcode') {
        if (isset($parameters['logged_in'])) {
            $logged_in = !!$parameters['logged_in'];
            if (is_user_logged_in() !== $logged_in) {
                return '';
            }
        }

        if (isset($parameters['role']) || isset($parameters['cap'])) {
            $current_user = wp_get_current_user();

            if (isset($parameters['role'])) {
                $current_user_roles = $current_user->roles;
                if (!in_array($parameters['role'], $current_user_roles)) {
                    return '';
                }
            }

            if (isset($parameters['cap'])) {
                $current_user_caps = $current_user->allcaps;
                if (!isset($current_user_caps[$parameters['cap']]) || !$current_user_caps[$parameters['cap']]) {
                    return '';
                }
            }
        }

        if (isset($parameters['slide'])) {
            $slideTo = intval($parameters['slide']);
        }

        if (isset($parameters['get']) && !empty($_GET[$parameters['get']])) {
            $slideTo = intval($_GET[$parameters['get']]);
        }

        if (isset($slideTo)) {
            echo "<script type=\"text/javascript\">window['ss" . $parameters['slider'] . "'] = " . ($slideTo - 1) . ";</script>";
        }

        if (isset($parameters['page'])) {
            if ($parameters['page'] == 'home') {
                $condition = (!is_home() && !is_front_page());
            } else {
                $condition = ((get_the_ID() != intval($parameters['page'])) || (is_home() || is_front_page()));
            }
            if ($condition) {
                return '';
            }
        }

        $parameters = shortcode_atts(array(
            'id'     => md5(time()),
            'slider' => 0
        ), $parameters);

        if ((is_numeric($parameters['slider']) && intval($parameters['slider']) > 0) || !is_numeric($parameters['slider'])) {
            ob_start();
            N2Base::getApplication("smartslider")
                  ->getApplicationType('frontend')
                  ->render(array(
                      "controller" => 'home',
                      "action"     => 'wordpress',
                      "useRequest" => false
                  ), array(
                      $parameters['slider'],
                      $usage
                  ));

            return ob_get_clean();
        }

        return '';
    }

    public static function addShortCode() {
        add_shortcode('smartslider3', 'N2SS3Shortcode::doShortcode');
    }

    public static function addNoopShortCode() {
        add_shortcode('smartslider3', 'N2SS3Shortcode::doNoopShortcode');
    }

    public static function doNoopShortcode() {
        return '';
    }

    public static function removeShortcode() {
        remove_shortcode('smartslider3');
    }
}

N2SS3Shortcode::addShortCode();

if (defined('DOING_AJAX') && DOING_AJAX) {
    if (isset($_POST['action']) && ($_POST['action'] == 'stpb_preview_builder_item' || $_POST['action'] == 'stpb_load_builder_templates' || $_POST['action'] == 'stpb_load_template')) {
        N2SS3Shortcode::removeShortcode();
    }
}

add_action('woocommerce_shop_loop', 'N2SS3Shortcode::addNoopShortCode', 9);
add_action('woocommerce_shop_loop', 'N2SS3Shortcode::addShortCode', 11);

add_action('woocommerce_single_product_summary', 'N2SS3Shortcode::addNoopShortCode', 59);
add_action('woocommerce_single_product_summary', 'N2SS3Shortcode::addShortCode', 61);

/**
 * Remove Smart Slider from feeds
 */
add_action('do_feed_rdf', 'N2SS3Shortcode::addNoopShortCode', 0);
add_action('do_feed_rss', 'N2SS3Shortcode::addNoopShortCode', 0);
add_action('do_feed_rss2', 'N2SS3Shortcode::addNoopShortCode', 0);
add_action('do_feed_atom', 'N2SS3Shortcode::addNoopShortCode', 0);

/**
 * Remove sliders from the AMP version of the site
 * @url https://wordpress.org/plugins/amp/
 */
add_action('pre_amp_render_post', 'N2SS3Shortcode::addNoopShortCode', 0);


add_action('after_setup_theme', function () {
    if (function_exists('KTT_share_args_for_posts')) {
        /**
         * Theme: Narratium
         * @url https://themeforest.net/item/narratium-simplicity-for-authors/20844434
         */
        add_action('wp', 'N2SS3Shortcode::addNoopShortCode', 0);
        add_action('wp', 'N2SS3Shortcode::addShortCode', 11);
    }
});