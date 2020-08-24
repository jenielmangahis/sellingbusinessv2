<?php

/**
 * CM Ad Changer
 *
 * @author CreativeMinds (http://ad-changer.cminds.com)
 * @copyright Copyright (c) 2013, CreativeMinds
 */
class AC_Client {

    public static $uniqueIds = array();

    public static function getRandomId() {
        $randomId = md5( uniqid() ); // is required to distinguish banners which are on the same page

        while ( in_array( $randomId, self::$uniqueIds ) ) {
            $randomId = md5( uniqid() );
        }

        self::$uniqueIds[] = $randomId;
        return $randomId;
    }

    /**
     * [cm_ad_changer] shortcode
     * @return String
     * @param Array   $args  Shortcode arguments
     */
    public static function banners( $args ) {
        /*
         * Is history disabled option checked?
         */
        $historyDisabled  = get_option( 'acs_disable_history_table', null );
        cmac_log( 'AC_Client::banners()' );
        $groupId          = null;
        $initialArgsArray = array(
            'campaign_id'       => null,
            'group_id'          => null,
            'linked_banner'     => '1',
            'no_responsive'     => '0',
            'demonstration_id'  => null,
            'custom_css'        => '',
            'container_width'   => null,
            'class'             => null,
            'debug'             => FALSE,
            'height'            => null,
            'width'             => null,
            'allow_inject_html' => '1',
            'allow_inject_js'   => '1',
            'wrapper'           => '0',
        );
        /*
         * No default arg value
         */
        if ( isset( $args[ 'target_blank' ] ) && is_numeric( $args[ 'target_blank' ] ) ) {
            $initialArgsArray[ 'target_blank' ] = $args[ 'target_blank' ];
        }
        $args               = shortcode_atts( $initialArgsArray, $args );
        $groupId            = $args[ 'group_id' ];
        $CMAC_HEAD_ENQUEUED = defined( 'CMAC_HEAD_ENQUEUED' );
        $isGroup            = FALSE;

        if ( is_array( $args ) ) {
            if ( !empty( $args[ 'group_id' ] ) ) {
                $isGroup               = FALSE;
                $campaign_id           = AC_Data::get_group_campaign( $args[ 'group_id' ] );
                $groupId               = $args[ 'group_id' ];
                $args[ 'campaign_id' ] = $campaign_id;
                /*
                 * @todo Check if that fix does not breaks something up
                 */
                unset( $args[ 'group_id' ] );

                if ( empty( $campaign_id ) ) {
                    return 'No campaigns in group!';
                }
            } else {
                $campaign_id = $args[ 'campaign_id' ];
            }
        } elseif ( is_numeric( $args ) ) {
            $campaign_id = $args;
        } else {
            return '<div class="cmac_error">Wrong campaign ID</div>';
        }

        if ( get_option( 'acs_active', 1 ) != '1' ) {
            return '';
        }

        /*
         * At this moment we have to check if server and campaign are active
         */
        $serverAndCampaign = AC_Data::get_global_campaigns_info( $campaign_id, $isGroup );

        /*
         * Something went wrong e.g. server is inactive, or campaigng is inactive etc.
         */
        if ( !$serverAndCampaign ) {
            $error_return = '';
            if ( filter_input( INPUT_GET, 'cminds_debug' ) ) {
                $error_return = AC_Data::_internal_error( null, true );
            }
            return $error_return;
        }

        $demonstration_id = self::getRandomId();
        $slideshow_id     = 'acc_div' . $demonstration_id;

        $globalVariationsSetting = get_option( 'acs_use_banner_variations', '1' ) == '1';
        $localVariationsSetting  = !isset( $args[ 'no_responsive' ] ) || isset( $args[ 'no_responsive' ] ) && $args[ 'no_responsive' ] != '1';

        /*
         * Use variations only if enabled both globally and locally
         */
        $use_banner_variations = $globalVariationsSetting && $localVariationsSetting;

        $ret_html = '';

        if ( !$use_banner_variations ) {
            cmac_log( 'Not using banner variations' );
            $args[ 'demonstration_id' ] = $demonstration_id;

            $requested_banners = self::request_banners( $args, $groupId );

            if ( isset( $requested_banners[ 'error' ] ) ) {
                return '';
            }
            $ret_html .= $requested_banners[ 'html' ]; // if no banner variations required => request the banners directly

            $ret_html .= '<script type="text/javascript">
                            jQuery(document).ready(function(){';

            if ( $historyDisabled != '1' ) {
                $ret_html .= 'cm_bind_click_banner' . $demonstration_id . '();';
            }

            $ret_html .= 'cm_init_slider' . $demonstration_id . '();
                            });
                          </script>';
        } else {
            cmac_log( 'Using banner variations' );

            $banner_area = get_option( 'acs_banner_area', 'screen' );
            $ret_html    = '<div id="' . $slideshow_id . '"></div>';

            $ret_html .= '<script type="text/javascript">';

            /*
             *  Create AJAX request in case if banner variations are allowed
             */
            if ( $banner_area == 'container' ) {
                $ret_html .='
				width = jQuery("#' . $slideshow_id . '").parent().width();
				if(parseInt(width)<=0){
					width = jQuery(window).width();}';
            } elseif ( $banner_area == 'screen' ) {
                $ret_html .= 'width = jQuery(window).width();';
            }
            $ret_html .='
				jQuery.ajax({
                                url: "' . get_bloginfo( 'wpurl' ) . '/wp-admin/admin-ajax.php?action=acc_get_banners",
                                type: "post",
                                dataType: "json",
                                data:{
                                        args: "' . addslashes( serialize( $args ) ) . '",
                                        container_width: width,
                                        demonstration_id: "' . $demonstration_id . '",
                                        group_id: ' . intval( $groupId ) . '
                                }
                        }).done(function(response){
                                if(typeof(response) !== "undefined" && response !== null && typeof(response.error) !== "undefined")
                                {
                                    if(typeof(response.html) !== "undefined")
                                    {
                                        alert(response.html);
                                    }
                                    return;
                                }
                                jQuery("#' . $slideshow_id . '").after(response.html);
                                jQuery("#' . $slideshow_id . '").remove();
                                cm_init_slider' . $demonstration_id . '();';
            /*
             * is history disabled?
             */
            if ( $historyDisabled != '1' ) {
                $ret_html .= 'cm_bind_click_banner' . $demonstration_id . '();';
            }
            $ret_html .= '})';
            $ret_html .= '</script>';
        }

        /*
         * Custom CSS
         */
        if ( !empty( $args[ 'custom_css' ] ) ) {
            $ret_html .= '<style type="text/css">' . trim( $args[ 'custom_css' ] ) . '</style>';
        }

        $ret_html .= '<script type="text/javascript">';

        /*
         * include click and impressions ajax only if disable history option is not checked
         */
        if ( $historyDisabled != '1' ) {
            $ret_html .= '
            function cm_bind_click_banner' . $demonstration_id . '(){
                    var currentEl = jQuery(".' . 'acc_banner_link' . $demonstration_id . '");
                    jQuery(currentEl).on("click",function(e){
                            meBannerObject = this;
                            if (meBannerObject.getAttribute("target") == "_blank"){
                            var newWindowObject = window.open("", "_blank");
                            }
                            ';


            if ( $historyDisabled != 1 ) {
                $ret_html .= 'jQuery.ajax({url: "' . get_bloginfo( 'wpurl' ) . '/wp-admin/admin-ajax.php?action=acc_trigger_click_event",
                                              type: "post",
                                              async: false,
                                              data: {campaign_id: ' . $campaign_id . ', banner_id: jQuery(this).attr("banner_id")' . ((!empty( $groupId )) ? (", group_id: " . $groupId) : ("")) . '},
                                              complete:
                                                function(){
                                                    var href = meBannerObject.dataset.href;

                                                    if(!href){
                                                        return false;
                                                    }

                                                    if (meBannerObject.getAttribute("target") == "_blank"){
                                                        newWindowObject.location = href;
                                                    }else {
                                                        document.location = meBannerObject.href;
                                                    }
                                                }
                            });';
            } else {
                $ret_html .= '
                    var href = meBannerObject.dataset.href;

                    if(!href){
                        return false;
                    }

                    if (meBannerObject.getAttribute("target") == "_blank"){
                        newWindowObject.location = href;
                    }else {
                        document.location = meBannerObject.href;
                    }';
            }
            $ret_html .= '
                            e.preventDefault();
                            return false;
                    });
            }';
        }

        $ret_html .= 'function cm_bind_impression_banner' . $demonstration_id . '(){';
        if ( $historyDisabled != 1 ) {
            $ret_html .= 'jQuery.ajax({url: "' . get_bloginfo( 'wpurl' ) . '/wp-admin/admin-ajax.php?action=acc_trigger_impression_event",
                                            type: "post",
                                            data: {
                                                campaign_id: ' . $campaign_id . ',
                                                banner_id: jQuery(".acc_banner_link' . $demonstration_id . '").attr("banner_id")'
            . ((!empty( $groupId )) ? (", group_id: " . $groupId) : ("")) . '
                                            }
                                        });';
        }
        $ret_html .= '}';

        $isImageBanner      = isset( $serverAndCampaign[ 'campaign_type_id' ] ) && $serverAndCampaign[ 'campaign_type_id' ] == '0' || !isset( $serverAndCampaign[ 'campaign_type_id' ] );
        $isRotatingCampaign = isset( $serverAndCampaign[ 'banner_display_method' ] ) && $serverAndCampaign[ 'banner_display_method' ] == 'all';

        if ( $isImageBanner && $isRotatingCampaign ) {
            $ret_html .= '
                function cm_init_slider' . $demonstration_id . '(){
                            jQuery("#' . $slideshow_id . '").tcycle();
                    }';
        } else {
            $ret_html .= '
                function cm_init_slider' . $demonstration_id . '(){}';
        }

        $ret_html .= '</script>';

        return $ret_html;
    }

    /**
     * Banner output
     * @return String
     * @param Array   $args  Shortcode arguments
     */
    public static function request_banners( $args, $groupId = null ) {
        cmac_log( 'AC_Client::request_banners()' );
        if ( is_array( $args ) ) {
            $campaign_id = $args[ 'campaign_id' ];
        } elseif ( is_numeric( $args ) ) {
            $campaign_id = $args;
        } else {
            return 'Wrong campaign ID';
        }

        $server_url = get_bloginfo( 'wpurl' );
        $url        = $server_url . '/?acs_action=get_banner&campaign_id=' . $campaign_id;
        if ( isset( $args[ 'container_width' ] ) ) {
            $url .= '&container_width=' . $args[ 'container_width' ];
        }

        $userIp      = filter_input( INPUT_SERVER, 'REMOTE_ADDR' );
        $httpReferer = filter_input( INPUT_SERVER, 'HTTP_REFERER' );

        $response = AC_Requests::get_banner( get_bloginfo( 'wpurl' ), // http referer
                                                           $userIp, // user IP
                                                           $httpReferer, // web page url
                                                           $campaign_id, // campaign id
                                                           isset( $args[ 'container_width' ] ) ? $args[ 'container_width' ] : null, // container width
                                                                  $groupId
        );
        $ret_html = '';
        if ( isset( $args[ 'debug' ] ) && $args[ 'debug' ] == '1' ) {
            if ( is_array( $response ) ) {
                $ret_html = ac_format_list( $response, 'CM AdChanger Debug Info:', 'acc_debug' );
            }
        }

        if ( !isset( $response[ 'error' ] ) ) {
            if ( isset( $args[ 'class' ] ) && !empty( $args[ 'class' ] ) ) {
                $css_class = $args[ 'class' ];
            } else {
                $css_class = null;
            }

            if ( isset( $args[ 'wrapper' ] ) && $args[ 'wrapper' ] == '1' ) {
                $ret_html .= '<div' . (!is_null( $css_class ) ? ' class="' . $css_class . '"' : '') . '>';
                $css_class = null; // not needed in other tags
            }

            if ( !empty( $args[ 'allow_inject_js' ] ) && !empty( $response[ 'custom_js' ] ) ) {
                $ret_html .= '<script type="text/javascript">';
                $ret_html .= stripslashes( $response[ 'custom_js' ] );
                $ret_html .= '</script>';
            }
            if ( !empty( $response[ 'banner_custom_js' ] ) ) {
                $ret_html .= stripslashes( $response[ 'banner_custom_js' ] );
            }
            switch ( $response[ 'campaign_type_id' ] ) {
                default:
                case '0':
                    $ret_html = self::displayImageBanner( $response, $ret_html, $args );
                    break;
                case '1':
                    if ( !empty( $args[ 'allow_inject_html' ] ) ) {
                        $ret_html = self::displayHTMLBanner( $response, $ret_html, $args );
                    }
                    break;
                case '2':
                    $ret_html = self::displayAdsenseBanner( $response, $ret_html, $args );
                    break;
                case '3':
                    if ( !empty( $args[ 'allow_inject_html' ] ) ) {
                        $ret_html = self::displayVideoBanner( $response, $ret_html, $args );
                    }
                    break;
                case '4':
                    $ret_html = self::displayFloatingBanner( $response, $ret_html, $args );
                    break;
                case '5':
                    $ret_html = self::displayFloatingBottomBanner( $response, $ret_html, $args );
                    break;
                    break;
            }

            if ( isset( $args[ 'wrapper' ] ) && $args[ 'wrapper' ] == '1' ) {
                $ret_html .= '</div>';
            }

            $retArr = array(
                'html' => $ret_html,
            );
            return $retArr;
        } else {
            cmac_log( 'Error response from AC_Request: ' . $response[ 'error' ] );

            if ( isset( $args[ 'debug' ] ) && $args[ 'debug' ] == '1' ) {
                $retArr = array(
                    'html' => 'CM AdChanger Banner Request Error: "' . $response[ 'error' ] . '"',
                );
            }

            $retArr[ 'error' ] = 1;
            return $retArr;
        }
    }

    public static function displayImageBanner( $response, $ret_html, $args ) {
        $demonstration_id      = isset( $args[ 'demonstration_id' ] ) ? $args[ 'demonstration_id' ] : self::getRandomId();
        $use_banner_variations = get_option( 'acs_use_banner_variations', '1' ) == '1';
        $resize_banner         = get_option( 'acs_resize_banner', '1' ) == '1';

        $tcycle_fx      = get_option( 'acs_slideshow_effect', 'fade' ) == "fade" ? "fade" : "scroll";
        $tcycle_speed   = get_option( 'acs_slideshow_transition_time', '400' );
        $tcycle_timeout = get_option( 'acs_slideshow_interval', '5000' );

        $banner_class = 'acc_banner_link' . $demonstration_id;
        $target       = '';
        if ( isset( $args[ 'target_blank' ] ) ) {
            if ( $args[ 'target_blank' ] == 1 ) {
                $target = 'target="_blank"';
            }
        } elseif ( isset( $response[ 'banner_new_window' ] ) ) {
            if ( $response[ 'banner_new_window' ] == 1 ) {
                $target = 'target="_blank"';
            }
        }
        if ( !empty( $response[ 'custom_banner_new_window' ] ) ) {
            if ( $response[ 'custom_banner_new_window' ] == 'target_blank' ) {
                $target = 'target="_blank"';
            }
            if ( $response[ 'custom_banner_new_window' ] == 'target_self' ) {
                $target = '';
            }
        }
        $style = '';

        $css_class = ((!isset( $args[ 'wrapper' ] ) || !$args[ 'wrapper' ]) && !is_null( $args[ 'class' ] ) ? $args[ 'class' ] : '');

        if ( !isset( $response[ 'banners' ] ) ) {
            /*
             * if one banner was received
             */
            cmac_log( '1 banner received' );

            if ( $use_banner_variations && $resize_banner && isset( $response[ 'resize' ] ) && isset( $args[ 'container_width' ] ) ) {
//                $image_content = file_get_contents($response['image']);
//                $info = pathinfo($response['image']);
//                $new_filename = cmac_get_upload_dir() . $info['filename'] . '.' . $info['extension'];
//                $thumb_filename = cmac_get_upload_dir() . $info['filename'] . '_w' . $args['container_width'] . '.' . $info['extension'];
//                $thumb_url = cmac_get_upload_url() . $info['filename'] . '_w' . $args['container_width'] . '.' . $info['extension'];
//                $f = fopen($new_filename, 'w');
//                fwrite($f, $image_content);
//                fclose($f);
//
//                $image = new Image($new_filename);
//                $image->resize($args['container_width']);
//                $image->save($thumb_filename);
                $style = 'style="width: ' . $args[ 'container_width' ] . 'px"';
            }
            $img_html = '';
            $alt      = (isset( $response[ 'alt_tag' ] ) && !empty( $response[ 'alt_tag' ] )) ? ' alt="' . $response[ 'alt_tag' ] . '"' : '';
            $title    = (isset( $response[ 'title_tag' ] ) && !empty( $response[ 'title_tag' ] )) ? ' title="' . $response[ 'title_tag' ] . '"' : '';

            if ( isset( $response[ 'banner_link' ] ) && ($args[ 'linked_banner' ] == '1' || !isset( $args[ 'linked_banner' ] )) ) {
                $img_html .= '<img src="' . (isset( $thumb_url ) ? $thumb_url : $response[ 'image' ]) . '"' . $alt . $title . $style . ' />';
                $ret_html .= '<a href="' . $response[ 'banner_link' ] . '" data-href="' . $response[ 'banner_link' ] . '" ' . $target . ' banner_id="' . $response[ 'banner_id' ] . '" class="' . $banner_class . ' ' . (!is_null( $css_class ) ? $css_class : '') . '">' . $img_html . '</a>';
            } else {
                $img_html .= '<img src="' . (isset( $thumb_url ) ? $thumb_url : $response[ 'image' ]) . '"' . $alt . $title . $style . '' . (!is_null( $css_class ) ? ' class="' . $css_class . '"' : '') . ' />';
                $ret_html .= $img_html;
            }
        } else {
            /*
             *  several banners received
             */
            cmac_log( 'Several banners received' );

            $width      = isset( $args[ 'width' ] ) ? $args[ 'width' ] : 'auto';
            $max_height = 0;

            if ( $width != 'auto' ) {
                // finding max banner height, to define slideshow height
                $heights = array();
                foreach ( $response[ 'banners' ] as $banner ) {
                    $heights[] = $banner[ 'image_height' ];
                }
                if ( !empty( $heights ) ) {
                    $max_height = max( $heights );
                }
            }

            $height = isset( $args[ 'height' ] ) ? 'height="' . $args[ 'height' ] . '"' : ($max_height) ? 'height="' . $max_height . '"' : '';

            $slideshow_id = 'acc_div' . $demonstration_id;
            $ret_html .= '<div id="' . $slideshow_id . '" style="text-align: center;width: ' . $width . ';' . $height . '" data-fx="' . $tcycle_fx . '" data-speed="' . $tcycle_speed . '" data-timeout="' . $tcycle_timeout . '">' . "\n";

            $bannerDivDisplay = 'style="display:block"';

            foreach ( $response[ 'banners' ] as $banner ) {
                $thumb_url = null;
                if ( $use_banner_variations && $resize_banner && isset( $banner[ 'resize' ] ) && isset( $args[ 'container_width' ] ) ) {
//                    $image_content = file_get_contents($banner['image']);
//                    $info = pathinfo($banner['image']);
//                    $new_filename = cmac_get_upload_dir() . $info['filename'] . '.' . $info['extension'];
//                    $thumb_filename = cmac_get_upload_dir() . $info['filename'] . '_w' . $args['container_width'] . '.' . $info['extension'];
//                    $thumb_url = cmac_get_upload_url() . $info['filename'] . '_w' . $args['container_width'] . '.' . $info['extension'];
//                    $f = fopen($new_filename, 'w');
//                    fwrite($f, $image_content);
//                    fclose($f);
//
//                    $image = new Image($new_filename);
//                    $image->resize($args['container_width']);
//                    $image->save($thumb_filename);
                    $style = 'style="width: ' . $args[ 'container_width' ] . 'px"';
                }

                $ret_html .= '<div ' . $bannerDivDisplay . '>';
                /**
                 * In case something's wrong with JS show only the first banner
                 */
                if ( $bannerDivDisplay == 'style="display:block"' ) {
                    $bannerDivDisplay = 'style="display:none"';
                }

                $img_html = '';
                $alt      = (isset( $banner[ 'alt_tag' ] ) && !empty( $banner[ 'alt_tag' ] )) ? ' alt="' . $banner[ 'alt_tag' ] . '"' : '';
                $title    = (isset( $banner[ 'title_tag' ] ) && !empty( $banner[ 'title_tag' ] )) ? ' title="' . $banner[ 'title_tag' ] . '"' : '';

                if ( isset( $banner[ 'link' ] ) && ($args[ 'linked_banner' ] == '1' || !isset( $args[ 'linked_banner' ] )) ) {
                    $img_html .= '<img src="' . (!is_null( $thumb_url ) ? $thumb_url : $banner[ 'image' ]) . '"' . $alt . $title . $style . ' />';
                    $ret_html .= '<a href="' . $banner[ 'link' ] . '" data-href="' . $banner[ 'link' ] . '" ' . $target . ' banner_id="' . $banner[ 'id' ] . '" class="' . $banner_class . ' ' . (!is_null( $css_class ) ? $css_class : '') . '">' . $img_html . '</a>';
                } else {
                    $img_html .= '<img src="' . (!is_null( $thumb_url ) ? $thumb_url : $banner[ 'image' ]) . '"' . $alt . $title . $style . '' . (!is_null( $css_class ) ? ' class="' . $css_class . '"' : '') . ' />';
                    $ret_html .= $img_html;
                }

                $ret_html .= '</div>' . "\n";
            }
            $ret_html .= '</div>';
        }

        return $ret_html;
    }

    public static function displayAdsenseBanner( $response, $ret_html, $args ) {
        static $adsenseInjected = false;

        /*
         * If there's AdSense - ignore the banners
         */
        if ( !empty( $response[ 'adsense_client' ] ) && !empty( $response[ 'adsense_slot' ] ) ) {
            if ( !$adsenseInjected ) {
                $ret_html .= '<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>';
            }

            $base_classes_arr = array();
            if ( isset( $response[ 'campaign_id' ] ) ) {
                $base_classes_arr[] = 'cmac_campaign_' . $response[ 'campaign_id' ];
            }
            $additional_classes_arr = apply_filters( 'cmac_adsense_ad_classes', $base_classes_arr );
            $additional_classes     = implode( ' ', (array) $additional_classes_arr );

            $style = '';
            if ( !empty( $response[ 'height' ] ) ) {
                $style .= 'min-height:auto;height:' . esc_attr( $response[ 'height' ] ) . ';';
            }
            if ( !empty( $response[ 'width' ] ) ) {
                $style .= 'min-width:auto;width:' . esc_attr( $response[ 'width' ] ) . ';';
            }

            $ret_html .= '<div class="cmac_adsense_ad ' . $additional_classes . '" style="' . $style . '">';

            $ret_html .= '
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="' . $response[ 'adsense_client' ] . '"
     data-ad-slot="' . $response[ 'adsense_slot' ] . '"
     data-ad-format="auto"></ins>
';
            if ( !$adsenseInjected ) {
                $ret_html .= '<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script>';
                $adsenseInjected = TRUE;
            }

            $ret_html .= '</div>';
        }

        return $ret_html;
    }

    public static function displayHTMLBanner( $response, $ret_html, $args ) {
        if ( !empty( $response[ 'html' ] ) ) {
            $css_class    = ((!isset( $args[ 'wrapper' ] ) || !$args[ 'wrapper' ]) && !is_null( $args[ 'class' ] ) ? $args[ 'class' ] : '');
            $banner_class = 'acc_banner_link' . $args[ 'demonstration_id' ];
            $link         = isset( $response[ 'link' ] ) ? $response[ 'link' ] : null;
            $bannerId     = isset( $response[ 'banner_id' ] ) ? $response[ 'banner_id' ] : '';
            $target       = '';
            if ( isset( $args[ 'target_blank' ] ) ) {
                if ( $args[ 'target_blank' ] == 1 ) {
                    $target = 'target="_blank"';
                }
            } elseif ( isset( $response[ 'banner_new_window' ] ) ) {
                if ( $response[ 'banner_new_window' ] == 1 ) {
                    $target = 'target="_blank"';
                }
            }

            $additionalStyle = '';
            if ( !empty( $response[ 'width' ] ) ) {
                $additionalStyle .= 'max-width:' . $response[ 'width' ] . ';';
            }
            if ( !empty( $response[ 'height' ] ) ) {
                $additionalStyle .= 'max-height:' . $response[ 'height' ] . ';';
            }

            if ( !empty( $additionalStyle ) ) {
                $banner_class .= ' cmac_limited_size';
            }

            $response[ 'html' ] = html_entity_decode( $response[ 'html' ], ENT_COMPAT | ENT_HTML5, 'UTF-8' );

            if ( !empty( $response[ 'html' ] ) ) {
                if ( ($args[ 'linked_banner' ] == '1' || !isset( $args[ 'linked_banner' ] )) && !empty( $link ) ) {
                    $html_banner = '<div style="cursor:pointer;' . $additionalStyle . '" data-href="' . $link . '" ' . $target . ' banner_id="' . $bannerId . '" class="' . $banner_class . ' ' . (!is_null( $css_class ) ? $css_class : '') . '">' . $response[ 'html' ] . '</div>';
                } else {
                    $html_banner = $response[ 'html' ];
                }
            }
            $ret_html .= $html_banner;
        }


        return $ret_html;
    }

    public static function displayVideoBanner( $response, $ret_html, $args ) {
        if ( !empty( $response[ 'html' ] ) ) {
            $css_class    = ((!isset( $args[ 'wrapper' ] ) || !$args[ 'wrapper' ]) && !is_null( $args[ 'class' ] ) ? $args[ 'class' ] : '');
            $banner_class = 'acc_banner_link' . $args[ 'demonstration_id' ];
            $link         = isset( $response[ 'link' ] ) ? $response[ 'link' ] : null;
            $bannerId     = isset( $response[ 'banner_id' ] ) ? $response[ 'banner_id' ] : '';
            $target       = '';
            if ( isset( $args[ 'target_blank' ] ) ) {
                if ( $args[ 'target_blank' ] == 1 ) {
                    $target = 'target="_blank"';
                }
            } elseif ( isset( $response[ 'banner_new_window' ] ) ) {
                if ( $response[ 'banner_new_window' ] == 1 ) {
                    $target = 'target="_blank"';
                }
            }

            if ( ($args[ 'linked_banner' ] == '1' || !isset( $args[ 'linked_banner' ] )) && !empty( $link ) ) {
                $html_banner = '<div style="cursor:pointer" data-href="' . $link . '" ' . $target . ' banner_id="' . $bannerId . '" class="' . $banner_class . ' ' . (!is_null( $css_class ) ? $css_class : '') . '">' . $response[ 'html' ] . '</div>';
            } else {
                $html_banner = $response[ 'html' ];
            }
            $ret_html .= $html_banner;
        }

        return $ret_html;
    }

    public static function displayFloatingBanner( $response, $ret_html, $args ) {
        /*
         * banner config resolve
         */
        $width          = ((!empty( $response[ 'width' ] ) ? ($response[ 'width' ]) : ('600px')));
        $height         = ((!empty( $response[ 'height' ] ) ? ($response[ 'height' ]) : ('400px')));
        $content        = ((!empty( $response[ 'html' ] ) ? ($response[ 'html' ]) : ('')));
        $background     = ((!empty( $response[ 'background' ] ) ? ($response[ 'background' ]) : ('#f0f1f2')));
        $userShowMethod = ((!empty( $response[ 'user_show_method' ] ) ? ($response[ 'user_show_method' ]) : ('always')));
        $underlayType   = ((!empty( $response[ 'underlay_type' ] ) ? ($response[ 'underlay_type' ]) : ('dark')));
        $resetTime      = ((!empty( $response[ 'reset_floating_banner_cookie_time' ] ) ? ($response[ 'reset_floating_banner_cookie_time' ]) : (7)));

        switch ( $underlayType ) {
            case 'dark' : $underlayColor = 'rgba(0,0,0,0.5)';
                break;
            case 'light' : $underlayColor = 'rgba(0,0,0,0.2)';
                break;
            default : $underlayColor = 'rgba(0,0,0,0.5)';
                break;
        }
        if ( !empty( $response[ 'banner_edges' ] ) ) {
            switch ( $response[ 'banner_edges' ] ) {
                case 'rounded' : $banner_edges = '4px';
                    break;
                case 'sharp' : $banner_edges = '0px';
                    break;
                default : $banner_edges = '4px';
            }
        } else {
            $banner_edges = '4px';
        }

        if ( !empty( $response[ 'show_effect' ] ) ) {
            switch ( $response[ 'show_effect' ] ) {
                case 'popin' : $show_effect = 'popin 1.0s';
                    break;
                case 'bounce' : $show_effect = 'bounce 1.0s';
                    break;
                case 'shake' : $show_effect = 'shake 1.0s';
                    break;
                case 'flash' : $show_effect = 'flash 0.5s';
                    break;
                case 'tada' : $show_effect = 'tada 1.5s';
                    break;
                case 'swing' : $show_effect = 'swing 1.0s';
                    break;
                case 'rotateIn' : $show_effect = 'rotateIn 1.0s';
                    break;
                default : $show_effect = 'popin 1.0s';
            }
        } else {
            $show_effect = 'popin 1.0s;';
        }
        $content = html_entity_decode( $content, ENT_COMPAT | ENT_HTML5, 'UTF-8' );
        $content = preg_replace( "/'/", "\"", $content );
        $ret_html .= '<style>'
        . '#ouibounce-modal .modal {
                        width: ' . $width . ';
                        height: ' . $height . ';
                        background-color: ' . $background . ';
                        z-index: 1;
                        position: absolute;
                        margin: auto;
                        top: 0;
                        right: 0;
                        bottom: 0;
                        left: 0;
                        border-radius: ' . $banner_edges . ';
                        -webkit-animation: ' . $show_effect . ';
                        animation: ' . $show_effect . ';
                      }'
        . (($underlayType != 'no') ? ('#ouibounce-modal .underlay {background-color: ' . $underlayColor . ';}') : ("")) .
        '</style>';
        $ret_html .= '<div id="ouibounce-modal">
                ' . (($underlayType != 'no') ? ('<div class="underlay"></div>') : ("")) . '
                <div class="modal">
                <div id="close_button"></div>
                  <div class="modal-body">' . $content . '</div>
                </div>
              </div>';
        $ret_html .= "
        <script type='text/javascript'>
            jQuery(document).ready(function () {
                " . (($userShowMethod == 'always') ? ("setCookie('ouibounceBannerShown', 'true', -1);") : ("")) . "
                if(getCookie('ouibounceBannerShown') == ''){
                    ouibounce = ouibounce(document.getElementById('ouibounce-modal'), {});
                    setTimeout(function(){
                        ouibounce.fire();
                        " . (($userShowMethod == 'once') ? ("setCookie('ouibounceBannerShown', 'true', " . $resetTime . ");") : ("")) . "
                        },
                    " . (((!empty( $response[ 'seconds_to_show' ] ) && (intval( $response[ 'seconds_to_show' ] ) > 0))) ? (intval( $response[ 'seconds_to_show' ] ) * 1000) : ('0')) . ");
                    jQuery('body').on('click', function() {
                      ouibounce.close();
                    });

                    jQuery('#ouibounce-modal #close_button').on('click', function() {
                      ouibounce.close();
                    });

                    jQuery('#ouibounce-modal .modal').on('click', function(e) {
                      e.stopPropagation();
                    });
                }
            });
        </script>
        ";
        return $ret_html;
    }

    public static function displayFloatingBottomBanner( $response, $ret_html, $args ) {
        /*
         * banner config resolve
         */
        $width          = ((!empty( $response[ 'width' ] ) ? ($response[ 'width' ]) : ('200px')));
        $height         = ((!empty( $response[ 'height' ] ) ? ($response[ 'height' ]) : ('300px')));
        $content        = ((!empty( $response[ 'html' ] ) ? (preg_replace( "/\"/", "'", $response[ 'html' ] )) : ('')));
        $background     = ((!empty( $response[ 'background' ] ) ? ($response[ 'background' ]) : ('#f0f1f2')));
        $userShowMethod = ((!empty( $response[ 'user_show_method' ] ) ? ($response[ 'user_show_method' ]) : ('always')));
        $resetTime      = ((!empty( $response[ 'reset_floating_banner_cookie_time' ] ) ? ($response[ 'reset_floating_banner_cookie_time' ]) : (7)));

        if ( !empty( $response[ 'banner_edges' ] ) ) {
            switch ( $response[ 'banner_edges' ] ) {
                case 'rounded' : $banner_edges = '10px';
                    break;
                case 'sharp' : $banner_edges = '0px';
                    break;
                default : $banner_edges = '10px';
            }
        } else {
            $banner_edges = '10px';
        }

        if ( !empty( $response[ 'show_effect' ] ) ) {
            switch ( $response[ 'show_effect' ] ) {
                case 'popin' : $show_effect = 'popin 1.0s';
                    break;
                case 'bounce' : $show_effect = 'bounce 1.0s';
                    break;
                case 'shake' : $show_effect = 'shake 1.0s';
                    break;
                case 'flash' : $show_effect = 'flash 0.5s';
                    break;
                case 'tada' : $show_effect = 'tada 1.5s';
                    break;
                case 'swing' : $show_effect = 'swing 1.0s';
                    break;
                case 'rotateIn' : $show_effect = 'rotateIn 1.0s';
                    break;
                default : $show_effect = 'popin 1.0s';
            }
        } else {
            $show_effect = 'popin 1.0s;';
        }
        $content = html_entity_decode( $content, ENT_COMPAT | ENT_HTML5, 'UTF-8' );
        $content = preg_replace( "/'/", "\"", $content );
        $ret_html .= '
            <style>
            #flyingBottomAd {
            padding: 0px;
            z-index: 100;
            border-radius: ' . $banner_edges . ' 0 0;
            -moz-border-radius: ' . $banner_edges . ' 0 0;
            -webkit-border-radius: ' . $banner_edges . ' 0 0;
            background: ' . $background . ';
            box-shadow: 0 0 20px rgba(0,0,0,.2);
            width: ' . $width . ';
            height: ' . $height . ';
            position: fixed;
            bottom: 0;
            right: 0;
            -webkit-backface-visibility: visible!important;
            -ms-backface-visibility: visible!important;
            backface-visibility: visible!important;
            -webkit-animation: ' . $show_effect . ';
            -moz-animation: ' . $show_effect . ';
            -o-animation: ' . $show_effect . ';
            animation: ' . $show_effect . ';
            -webkit-transition: bottom .5s ease,background-position .5s ease;
            transition: bottom .5s ease,background-position .5s ease;
        }
        </style>
        ';
        $ret_html .= "<script type='text/javascript'>
            " . (($userShowMethod == 'always') ? ("setCookie('ouibounceBannerBottomShown', 'true', -1);") : ("")) . "
            if(getCookie('ouibounceBannerBottomShown') == ''){
            var _flyingBottomOui = flyingBottomAd({
                htmlContent: '<div id=\"flyingBottomAd\"><span class=\"flyingBottomAdClose\"></span>" . $content . "</div>',
                delay: " . (((!empty( $response[ 'seconds_to_show' ] ) && (intval( $response[ 'seconds_to_show' ] ) > 0))) ? (intval( $response[ 'seconds_to_show' ] ) * 1000) : ('0')) . "
            });
            " . (($userShowMethod == 'once') ? ("setCookie('ouibounceBannerBottomShown', 'true', " . $resetTime . ");") : ("")) . "
            }
            </script>
        ";
        return $ret_html;
    }

    /**
     * AJAX call - Gets banners from the server
     */
    public static function get_banners() {
        cmac_log( 'AC_Client::get_banners() - AJAX request' );
        if ( !isset( $_REQUEST[ 'args' ] ) ) {
            die( 'args not set' );
        }

        if ( !isset( $_REQUEST[ 'container_width' ] ) ) {
            $_REQUEST[ 'container_width' ] = 0;
        }

        if ( !isset( $_REQUEST[ 'demonstration_id' ] ) ) {
            die();
        }

        $args                       = unserialize( stripslashes( $_REQUEST[ 'args' ] ) );
        $args[ 'container_width' ]  = $_REQUEST[ 'container_width' ];
        $args[ 'demonstration_id' ] = $_REQUEST[ 'demonstration_id' ];
        $gpoupId                    = ((!empty( $_REQUEST[ 'group_id' ] )) ? ($_REQUEST[ 'group_id' ]) : (''));
        $result                     = self::request_banners( $args, $gpoupId );
        $result['html'] = do_shortcode($result['html']);
        wp_send_json( $result );
        exit;
    }

    /**
     * AJAX call  - Send trigger click request to the server
     */
    function trigger_click_event() {
        $historyDisabled = get_option( 'acs_disable_history_table', null );
        if ( $historyDisabled == 1 ) {
            exit;
        }
        cmac_log( 'AC_Client::trigger_click_event()' );

        /*
         * If server_url is set, use it else - assume self call
         */
        $server_url       = isset( $_GET[ 'server_url' ] ) ? $_GET[ 'server_url' ] : get_bloginfo( 'wpurl' );
        $currentServerUrl = get_bloginfo( 'wpurl' );

        $userIP        = filter_input( INPUT_SERVER, 'REMOTE_ADDR' );
        $referringPage = filter_input( INPUT_SERVER, 'HTTP_REFERER' );
        $campaignId    = filter_input( INPUT_POST, 'campaign_id' );
        $bannerId      = filter_input( INPUT_POST, 'banner_id' );
        $groupId       = filter_input( INPUT_POST, 'group_id' );
        $data          = AC_Requests::trigger_click_event( $currentServerUrl, $userIP, $referringPage, $campaignId, $bannerId, $groupId );
        wp_send_json( $data );
        exit;
    }

    /**
     * AJAX call - Send trigger impression request to the server
     */
    function trigger_impression_event() {
        $historyDisabled = get_option( 'acs_disable_history_table', null );
        if ( $historyDisabled == '1' ) {
            exit;
        }
        cmac_log( 'AC_Client::trigger_impression_event()' );
        if ( !isset( $_GET[ 'server_url' ] ) ) {
            $server_url = get_bloginfo( 'wpurl' );
        } else {
            $server_url = $_GET[ 'server_url' ];
        }

        $data = AC_Requests::trigger_impression_event( get_bloginfo( 'wpurl' ), // http referer
                                                                     $_SERVER[ "HTTP_REFERER" ], // web page url
                                                                     $_REQUEST[ 'campaign_id' ], // campaign id
                                                                     $_SERVER[ 'REMOTE_ADDR' ], // user IP
                                                                     $_REQUEST[ 'banner_id' ] // banner id
        );

        exit;
    }

}
