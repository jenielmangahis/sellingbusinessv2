<?php
class N2_SMARTSLIDER_3_PRO_UPDATE {

    public static function init() {
        add_filter('site_transient_update_plugins', 'N2_SMARTSLIDER_3_PRO_UPDATE::injectUpdate'); //WP 3.0+
        add_filter('transient_update_plugins', 'N2_SMARTSLIDER_3_PRO_UPDATE::injectUpdate'); //WP 2.8+

        add_filter('upgrader_pre_download', 'N2_SMARTSLIDER_3_PRO_UPDATE::upgrader_pre_download', 10, 3);

        add_filter('plugins_api_args', 'N2_SMARTSLIDER_3_PRO_UPDATE::plugins_api_args', 10, 2);
    }

    public static function plugins_api_args($args, $action) {
        if ($action == 'plugin_information' && $args->slug == 'nextend-smart-slider3-pro') {
            $args->slug = 'smart-slider-3';
        }

        return $args;
    }

    public static function injectUpdate($updates) {
        global $wp_version;

        if (!isset($updates->response["nextend-smart-slider3-pro/nextend-smart-slider3-pro.php"])) {
            N2Base::getApplication("smartslider")
                  ->getApplicationType('backend');
            N2Loader::import(array(
                'models.License',
                'models.Update'
            ), 'smartslider');

            $updater = N2SmartsliderUpdateModel::getInstance();
            if ($updater->hasUpdate()) {
                $updates->response["nextend-smart-slider3-pro/nextend-smart-slider3-pro.php"] = (object)array(
                    "id"            => 0,
                    "slug"          => "nextend-smart-slider3-pro",
                    "plugin"        => "nextend-smart-slider3-pro/nextend-smart-slider3-pro.php",
                    "new_version"   => $updater->getVersion(),
                    "url"           => "https://wordpress.org/plugins/smart-slider-3/",
                    "package"       => str_replace('http://', 'https://', N2SS3::api(array(
                        'action' => 'update'
                    ), true)),
                    "tested"        => $wp_version,
                    "compatibility" => true,
                    "icons"         => array(
                        '1x'      => 'https://smartslider3.com/images/icon-128x128.png',
                        '2x'      => 'https://smartslider3.com/images/icon-256x256.png',
                        'default' => 'https://smartslider3.com/images/icon-128x128.png'
                    )
                );
            }
        }

        return $updates;
    }

    public static function upgrader_pre_download($reply, $package, $upgrader) {
        if (strpos($package, 'product=smartslider3') === false) {
            return $reply;
        }

        N2Base::getApplication("smartslider")
              ->getApplicationType('backend');
        N2Loader::import(array(
            'models.License'
        ), 'smartslider');

        $status = N2SmartsliderLicenseModel::getInstance()
                                           ->isActive(false);

        $message = '';
        switch ($status) {
            case 'OK':
                return $reply;
            case 'ASSET_PREMIUM':
            case 'LICENSE_EXPIRED':
                $message = 'Your <a href="https://smartslider3.helpscoutdocs.com/article/1101-license" target="_blank">license</a> expired! Get new one: <a href="https://smartslider3.com/pricing" target="_blank">smartslider3.com</a>';
                break;
            case 'DOMAIN_REGISTER_FAILED':
                $message = 'Your license key authorized on a different domain! You can move it to this domain like <a href="https://smartslider3.helpscoutdocs.com/article/1101-license#move" target="_blank">this</a>, or get new one: <a href="https://smartslider3.com/pricing" target="_blank">smartslider3.com</a>';
                break;
            case 'LICENSE_INVALID':
                $message = 'License key is missing or invalid, please <a href="https://smartslider3.helpscoutdocs.com/article/1101-license" target="_blank">enter again</a> or get one: <a href="https://smartslider3.com/pricing" target="_blank">smartslider3.com</a>';
                N2SmartsliderLicenseModel::getInstance()
                                         ->setKey('');
                break;
                break;
            case 'PLATFORM_NOT_ALLOWED':
                $message = 'Your <a href="https://smartslider3.helpscoutdocs.com/article/1101-license" target="_blank">license key</a> is not valid for WordPress! Get a license for WordPress: <a href="https://smartslider3.com/pricing" target="_blank">smartslider3.com</a>';
                break;
            case '503':
                $message = 'Licensing server is down, try again later!';
                break;
            case null:
                $message = 'Licensing server not reachable, try again later!';
                break;
            default:
                $message = 'Unknown error, please write an email to support@nextendweb.com with the following status: ' . $status;
                break;
        }

        $reply                  = new WP_Error('SS3_ERROR', $message);
        $upgrader->result       = null;
        $upgrader->skin->result = $reply;

        return $reply;
    }
}

N2_SMARTSLIDER_3_PRO_UPDATE::init();
