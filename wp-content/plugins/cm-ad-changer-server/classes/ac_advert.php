<?php

/**
 * CM Ad Changer
 *
 * @author CreativeMinds (http://ad-changer.cminds.com)
 * @copyright Copyright (c) 2014, CreativeMinds
 */

/**
 * Generic Advert class
 */
class AC_Advert {

    public static function cleanOldBanners( $data, $new_campaign_id ) {
        global $wpdb;

        /*
         * 6. Inserting banner images
         */
        $new_filenames = array();
        if ( isset( $new_campaign_id ) ) {
            if ( !isset( $data[ 'banner_filename' ] ) || !is_array( $data[ 'banner_filename' ] ) ) {
                $data[ 'banner_filename' ] = array();
            }
            $existing_filenames = $wpdb->get_col( 'SELECT filename FROM ' . ADS_TABLE . ' WHERE campaign_id="' . $new_campaign_id . '" AND parent_image_id=0 AND status=1' );

            $deleted_filenames = array();

            foreach ( $existing_filenames as $existing_filename ) {
                if ( !in_array( $existing_filename, $data[ 'banner_filename' ] ) ) {
                    $deleted_filenames[] = $existing_filename;
                }
            }

            foreach ( $data[ 'banner_filename' ] as $data_filename ) {
                if ( !in_array( $data_filename, $existing_filenames ) ) {
                    $new_filenames[] = $data_filename;
                }
            }

            /*
             *  cleaning images folder
             */
            if ( !empty( $deleted_filenames ) ) {
                foreach ( $deleted_filenames as $deleted_filename ) {
                    if ( !empty( $deleted_filename ) )
                        if ( !in_array( $deleted_filename, $data[ 'banner_filename' ] ) ) {
                            if ( !empty( $deleted_filename ) ) {
                                if ( file_exists( cmac_get_upload_dir() . $deleted_filename ) ) {
                                    $info = pathinfo( $deleted_filename );
                                    unlink( cmac_get_upload_dir() . $deleted_filename );
                                }
                            }
                            if ( !empty( $info[ 'extension' ] ) && file_exists( cmac_get_upload_dir() . $info[ 'filename' ] . BANNER_THUMB_WIDTH . 'x' . BANNER_THUMB_WIDTH . '.' . $info[ 'extension' ] ) ) {
                                unlink( cmac_get_upload_dir() . $info[ 'filename' ] . BANNER_THUMB_WIDTH . 'x' . BANNER_THUMB_WIDTH . '.' . $info[ 'extension' ] );
                            }
                        }

                    $banner_id                    = $wpdb->get_var( 'SELECT image_id FROM ' . ADS_TABLE . ' WHERE filename="' . $deleted_filename . '"' );
                    $existing_variation_filenames = $wpdb->get_col( 'SELECT filename FROM ' . ADS_TABLE . ' WHERE campaign_id="' . $new_campaign_id . '" AND parent_image_id="' . $banner_id . '"' );
                    if ( $existing_variation_filenames && !empty( $existing_variation_filenames ) ) {
                        foreach ( $existing_variation_filenames as $deleted_variation_filename ) {
                            if ( file_exists( cmac_get_upload_dir() . $deleted_variation_filename ) ) {
                                $info = pathinfo( $deleted_variation_filename );
                                unlink( cmac_get_upload_dir() . $deleted_variation_filename );
                                if ( file_exists( cmac_get_upload_dir() . $info[ 'filename' ] . BANNER_VARIATION_THUMB_WIDTH . 'x' . BANNER_VARIATION_THUMB_HEIGHT . '.' . $info[ 'extension' ] ) ) {
                                    unlink( cmac_get_upload_dir() . $info[ 'filename' ] . BANNER_VARIATION_THUMB_WIDTH . 'x' . BANNER_VARIATION_THUMB_HEIGHT . '.' . $info[ 'extension' ] );
                                }
                            }
                        }
                        $wpdb->query( 'DELETE FROM ' . ADS_TABLE . ' WHERE campaign_id="' . $new_campaign_id . '" AND parent_image_id="' . $banner_id . '"' );
                    }

                    $wpdb->query( $wpdb->prepare( 'UPDATE ' . ADS_TABLE . ' SET status=%d WHERE campaign_id=%s AND filename=%s', 0, $new_campaign_id, $deleted_filename ) );
                }
            }
        }
    }

    public static function validateBanners( $errors, $data ) {

        if ( 0 == $data[ 'campaign_type_id' ] && isset( $data[ 'banner_title' ] ) && !empty( $data[ 'banner_title' ] ) ) {
            foreach ( $data[ 'banner_title' ] as $banner_index => $banner_title ) {
                if ( !empty( $data[ 'banner_link' ][ $banner_index ] ) && filter_var( $data[ 'banner_link' ][ $banner_index ], FILTER_VALIDATE_URL ) === false ) {
                    $errors[] = 'Banner link is not valid. Valid urls should begin with: http:// or https://';
                }
            }
        }
        return $errors;
    }

    public static function handleBannerPost( $data, $new_campaign_id ) {
        global $wpdb;
        if ( !isset( $data[ 'banner_weight' ] ) || !is_array( $data[ 'banner_weight' ] ) ) {
            $data[ 'banner_weight' ] = array();
        }

        if ( $data[ 'banner_display_method' ] == 'selected' && !empty( $data[ 'banner_filename' ] ) && empty( $data[ 'selected_banner' ] ) ) {
            //$errors[] = 'Please select a banner';
            //if no banner selected then select first banner
            $data[ 'selected_banner' ] = $data[ 'banner_filename' ][ 0 ];
        }

        $banner_weight_sum = 0;
        $banners_natural   = true;
        foreach ( $data[ 'banner_weight' ] as $banner_weight ) {
            if ( !is_numeric( $banner_weight ) || ((int) $banner_weight != (float) $banner_weight) || $banner_weight < 0 ) {
                $errors[]        = 'Please enter numeric positive Banner Weights';
                $banners_natural = false;
                break;
            }

            $banner_weight_sum+=(int) $banner_weight;
        }

        if ( $banners_natural ) {
            if ( $banner_weight_sum > 1000 ) {
                $errors[] = 'Banner Weight sum is too big';
            }
        }

        $existing_filenames = $wpdb->get_col( 'SELECT filename FROM ' . ADS_TABLE . ' WHERE campaign_id="' . $new_campaign_id . '" AND parent_image_id=0 AND status=1' );

        $new_filenames = array();
        if ( !empty( $data[ 'banner_filename' ] ) ) {
            foreach ( $data[ 'banner_filename' ] as $data_filename ) {
                if ( !in_array( $data_filename, $existing_filenames ) ) {
                    $new_filenames[] = $data_filename;
                }
            }
        }

        $selected_banner_id = null;

        if ( isset( $data[ 'banner_title' ] ) && !empty( $data[ 'banner_title' ] ) ) {
            $data[ 'banner_weight' ] = ac_normalize_weights( $data[ 'banner_weight' ] );
            foreach ( $data[ 'banner_title' ] as $banner_index => $banner_title ) {
                $meta                             = array();
                $meta[ 'banner_expiration_date' ] = esc_sql( (!empty( $data[ 'banner_expiration_date' ][ $banner_index ] ) ? $data[ 'banner_expiration_date' ][ $banner_index ] : '' ) );
                $meta                             = maybe_serialize( $meta );
                if ( in_array( $data[ 'banner_filename' ][ $banner_index ], $new_filenames ) ) {

                    $image_file_content = file_get_contents( cmac_get_upload_dir() . AC_TMP_UPLOAD_PATH . $data[ 'banner_filename' ][ $banner_index ] );
                    $info               = pathinfo( cmac_get_upload_dir() . AC_TMP_UPLOAD_PATH . $data[ 'banner_filename' ][ $banner_index ] );
                    $thumb_file_content = file_get_contents( cmac_get_upload_dir() . AC_TMP_UPLOAD_PATH . $info[ 'filename' ] . BANNER_THUMB_WIDTH . 'x' . BANNER_THUMB_HEIGHT . '.' . $info[ 'extension' ] );

                    if ( $image_file_content ) {
                        $f = fopen( cmac_get_upload_dir() . $data[ 'banner_filename' ][ $banner_index ], 'w+' );
                        fwrite( $f, $image_file_content );
                        fclose( $f );

                        $f2 = fopen( cmac_get_upload_dir() . $info[ 'filename' ] . BANNER_THUMB_WIDTH . 'x' . BANNER_THUMB_HEIGHT . '.' . $info[ 'extension' ], 'w+' );
                        fwrite( $f2, $thumb_file_content );
                        fclose( $f2 );
                    }
                    $custom_banner_new_window = (!empty( $data[ 'custom_banner_new_window' ][ $banner_index ] ) ? $data[ 'custom_banner_new_window' ][ $banner_index ] : '' );
                    $banner_custom_js         = (!empty( $data[ 'banner_custom_js' ][ $banner_index ] ) ? $data[ 'banner_custom_js' ][ $banner_index ] : '' );
                    $query                    = 'INSERT INTO ' . ADS_TABLE . ' SET campaign_id=%d, title=%s, title_tag=%s, alt_tag=%s, link=%s, weight=%d, filename=%s, banner_custom_js=%s, custom_banner_new_window=%s, meta=%s';
                    $params                   = array( $new_campaign_id, $banner_title, $data[ 'banner_title_tag' ][ $banner_index ], $data[ 'banner_alt_tag' ][ $banner_index ], $data[ 'banner_link' ][ $banner_index ], $data[ 'banner_weight' ][ $banner_index ], $data[ 'banner_filename' ][ $banner_index ], $banner_custom_js, $custom_banner_new_window, $meta );
                    $wpdb->query( $wpdb->prepare( $query, $params ) );

                    if ( $data[ 'banner_filename' ][ $banner_index ] == $data[ 'selected_banner' ] ) {
                        $selected_banner_id = $wpdb->insert_id;
                    }
                } else {
                    $custom_banner_new_window = (!empty( $data[ 'custom_banner_new_window' ][ $banner_index ] ) ? $data[ 'custom_banner_new_window' ][ $banner_index ] : '' );
                    $banner_custom_js         = (!empty( $data[ 'banner_custom_js' ][ $banner_index ] ) ? $data[ 'banner_custom_js' ][ $banner_index ] : '' );
                    $query                    = 'UPDATE ' . ADS_TABLE . ' SET title=%s, meta=%s, title_tag=%s, alt_tag=%s, link=%s, weight=%d, banner_custom_js=%s, custom_banner_new_window=%s WHERE filename=%s';
                    $params                   = array( $banner_title, $meta, $data[ 'banner_title_tag' ][ $banner_index ], $data[ 'banner_alt_tag' ][ $banner_index ], $data[ 'banner_link' ][ $banner_index ], $data[ 'banner_weight' ][ $banner_index ], $banner_custom_js, $custom_banner_new_window, $data[ 'banner_filename' ][ $banner_index ] );
                    $wpdb->query( $wpdb->prepare( $query, $params ) );
                    if ( $data[ 'banner_filename' ][ $banner_index ] == $data[ 'selected_banner' ] ) {
                        $selected_banner_id = $wpdb->get_var( 'SELECT image_id FROM ' . ADS_TABLE . ' WHERE filename="' . $data[ 'banner_filename' ][ $banner_index ] . '"' );
                    }
                }

                /*
                 *  Banner variations
                 */
                $banner_id = $wpdb->get_var( 'SELECT image_id FROM ' . ADS_TABLE . ' WHERE filename="' . $data[ 'banner_filename' ][ $banner_index ] . '"' );
                if ( !isset( $data[ 'banner_variation' ][ $data[ 'banner_filename' ][ $banner_index ] ] ) || !is_array( $data[ 'banner_variation' ][ $data[ 'banner_filename' ][ $banner_index ] ] ) ) {
                    $data[ 'banner_variation_filename' ] = array();
                }
                $existing_variation_filenames = $wpdb->get_col( 'SELECT filename FROM ' . ADS_TABLE . ' WHERE campaign_id="' . $new_campaign_id . '" AND parent_image_id="' . $banner_id . '"' );

                $new_variation_filenames     = array();
                $deleted_variation_filenames = array();

                foreach ( $existing_variation_filenames as $existing_variation_filename ) {
                    if ( !in_array( $existing_variation_filename, $data[ 'banner_variation' ][ $data[ 'banner_filename' ][ $banner_index ] ] ) ) {
                        $deleted_variation_filenames[] = $existing_variation_filename;
                    }
                }

                if ( isset( $data[ 'banner_variation' ][ $data[ 'banner_filename' ][ $banner_index ] ] ) && !empty( $data[ 'banner_variation' ][ $data[ 'banner_filename' ][ $banner_index ] ] ) ) {
                    foreach ( $data[ 'banner_variation' ][ $data[ 'banner_filename' ][ $banner_index ] ] as $data_variation_filename ) {
                        if ( !in_array( $data_variation_filename, $existing_variation_filenames ) ) {
                            $new_variation_filenames[] = $data_variation_filename;
                        }
                    }
                }

                /*
                 * removing deleted variations from folder and from table
                 */
                if ( !empty( $deleted_variation_filenames ) ) {
                    foreach ( $deleted_variation_filenames as $deleted_variation_filename ) {
                        if ( !in_array( $deleted_variation_filename, $data[ 'banner_variation' ][ $data[ 'banner_filename' ][ $banner_index ] ] ) ) {
                            if ( file_exists( cmac_get_upload_dir() . $deleted_variation_filename ) ) {
                                $info = pathinfo( $deleted_variation_filename );
                                unlink( cmac_get_upload_dir() . $deleted_variation_filename );
                                unlink( cmac_get_upload_dir() . $info[ 'filename' ] . BANNER_VARIATION_THUMB_WIDTH . 'x' . BANNER_VARIATION_THUMB_HEIGHT . '.' . $info[ 'extension' ] );
                            }
                        }

                        $wpdb->query( 'DELETE FROM ' . ADS_TABLE . ' WHERE campaign_id="' . $new_campaign_id . '" AND filename="' . $deleted_variation_filename . '"' );
                    }
                }

                /*
                 *  inserting new variations
                 */
                if ( isset( $new_variation_filenames ) && !empty( $new_variation_filenames ) ) {

                    foreach ( $new_variation_filenames as $banner_variation_filename ) {
                        $image_file_content = file_get_contents( cmac_get_upload_dir() . AC_TMP_UPLOAD_PATH . $banner_variation_filename );
                        $info               = pathinfo( cmac_get_upload_dir() . AC_TMP_UPLOAD_PATH . $banner_variation_filename );
                        $thumb_file_content = file_get_contents( cmac_get_upload_dir() . AC_TMP_UPLOAD_PATH . $info[ 'filename' ] . BANNER_VARIATION_THUMB_WIDTH . 'x' . BANNER_VARIATION_THUMB_HEIGHT . '.' . $info[ 'extension' ] );

                        if ( $image_file_content ) {
                            $f = fopen( cmac_get_upload_dir() . $banner_variation_filename, 'w+' );
                            fwrite( $f, $image_file_content );
                            fclose( $f );

                            $f2 = fopen( cmac_get_upload_dir() . $info[ 'filename' ] . BANNER_VARIATION_THUMB_WIDTH . 'x' . BANNER_VARIATION_THUMB_HEIGHT . '.' . $info[ 'extension' ], 'w+' );
                            fwrite( $f2, $thumb_file_content );
                            fclose( $f2 );
                        }

                        $wpdb->query( $wpdb->prepare( 'INSERT INTO ' . ADS_TABLE . ' SET campaign_id=%d, parent_image_id=%d, filename=%s', $new_campaign_id, $banner_id, $banner_variation_filename ) );
                    }
                }
            }
        }

        /*
         *  updating campaigns : setting selected banner
         */
        $wpdb->query( 'UPDATE ' . CAMPAIGNS_TABLE . ' SET selected_banner="' . ($selected_banner_id ? $selected_banner_id : '0') . '" WHERE campaign_id="' . $new_campaign_id . '"' );
    }

    public static function handleHTMLPost( $data, $new_campaign_id ) {
        global $wpdb;

        $existingBanners   = $wpdb->get_col( 'SELECT image_id FROM ' . ADS_TABLE . ' WHERE campaign_id="' . $new_campaign_id . '" AND parent_image_id=0 AND type=1' );
        $useSelectedBanner = isset( $data[ 'banner_display_method' ] ) && $data[ 'banner_display_method' ] == 'selected';
        $newBannerId       = '0';

        $newBanners = array();
        foreach ( $data[ 'banner_ids' ] as $banner_id ) {
            if ( !in_array( $banner_id, $existingBanners ) ) {
                $newBanners[] = $banner_id;
            }
        }
        if ( isset( $data[ 'html_ads' ] ) && !empty( $data[ 'html_ads' ] ) ) {
            foreach ( $data[ 'html_ads' ] as $banner_index => $banner_title ) {
                /*
                 * Ignore empty ads
                 */
                if ( empty( $banner_title ) && empty( $data[ 'banner_custom_js' ][ $banner_index ] ) ) {
                    continue;
                }

                $meta                             = array();
                $meta[ 'banner_expiration_date' ] = esc_sql( (!empty( $data[ 'banner_expiration_date' ][ $banner_index ] ) ? $data[ 'banner_expiration_date' ][ $banner_index ] : '' ) );
                $meta[ 'html' ]                   = esc_html( stripslashes( $banner_title ) );
                $meta                             = maybe_serialize( $meta );

                $status         = $data[ 'status' ] ? 1 : 1;
                $type           = 1;
                $weight         = $data[ 'banner_weight' ][ $banner_index ];
                $bannerLink     = $data[ 'banner_link' ][ $banner_index ];
                $bannerCustomJs = $data[ 'banner_custom_js' ][ $banner_index ];
                $html_title     = $data[ 'html_title' ][ $banner_index ];
                if ( in_array( $data[ 'banner_ids' ][ $banner_index ], $newBanners ) ) {
                    $sql         = 'INSERT INTO ' . ADS_TABLE . ' SET campaign_id=%d, meta=%s, type=%d, status=%d, weight=%d, link=%s, banner_custom_js=%s, title=%s';
                    $params      = array( $new_campaign_id, $meta, $type, $status, $weight, $bannerLink, $bannerCustomJs, $html_title );
                    $wpdb->query( $wpdb->prepare( $sql, $params ) );
                    $newBannerId = $wpdb->insert_id;
                } else {
                    $bannerId = $data[ 'banner_ids' ][ $banner_index ];
                    $sql      = 'UPDATE ' . ADS_TABLE . ' SET meta=%s, status=%d, weight=%d, link=%s, banner_custom_js=%s, title=%s WHERE image_id=%d';
                    $params   = array( $meta, $status, $weight, $bannerLink, $bannerCustomJs, $html_title, $bannerId );
                    $wpdb->query( $wpdb->prepare( $sql, $params ) );
                }

                $meta = null;
            }
        }

        if ( $useSelectedBanner ) {
            $selected_banner_id = ($data[ 'selected_html_ad' ] == 'new') ? $newBannerId : $data[ 'selected_html_ad' ];
            /*
             *  updating campaigns : setting selected banner
             */
            $wpdb->query( 'UPDATE ' . CAMPAIGNS_TABLE . ' SET selected_banner="' . ($selected_banner_id ? $selected_banner_id : '0') . '" WHERE campaign_id="' . $new_campaign_id . '"' );
        }
    }

    public static function handleFloatingHTMLPost( $data, $new_campaign_id, $type = 4 ) {
        global $wpdb;

        $existingBanners   = $wpdb->get_col( 'SELECT image_id FROM ' . ADS_TABLE . ' WHERE campaign_id="' . $new_campaign_id . '" AND parent_image_id=0 AND type=' . intval( $type ) );
        $useSelectedBanner = isset( $data[ 'banner_display_method' ] ) && $data[ 'banner_display_method' ] == 'selected';
        $newBannerId       = '0';
        $newBanners        = array();
        foreach ( $data[ 'banner_ids' ] as $banner_id ) {
            if ( !in_array( $banner_id, $existingBanners ) ) {
                $newBanners[] = $banner_id;
            }
        }
        $newSelectedBanner  = false;
        $lastSelectedBanner = false;
        if ( isset( $data[ 'html_ads' ] ) && !empty( $data[ 'html_ads' ] ) ) {
            foreach ( $data[ 'html_ads' ] as $banner_index => $banner_title ) {
                /*
                 * Ignore empty ads
                 */
                if ( empty( $banner_title ) ) {
                    continue;
                }

                $meta[ 'html' ] = esc_html( stripslashes( $banner_title ) );
                $meta           = maybe_serialize( $meta );

                $status     = $data[ 'status' ] ? 1 : 1;
                $weight     = isset( $data[ 'banner_weight' ][ $banner_index ] ) ? $data[ 'banner_weight' ][ $banner_index ] : 0;
                $bannerLink = isset( $data[ 'banner_link' ][ $banner_index ] ) ? $data[ 'banner_link' ][ $banner_index ] : '';
                $bannerName = isset( $data[ 'filename' ][ $banner_index ] ) ? $data[ 'filename' ][ $banner_index ] : '';

                if ( in_array( $data[ 'banner_ids' ][ $banner_index ], $newBanners ) ) {
                    $sql         = 'INSERT INTO ' . ADS_TABLE . ' SET campaign_id=%d, meta=%s, type=%d, status=%d, weight=%d, link=%s, filename=%s';
                    $params      = array( $new_campaign_id, $meta, $type, $status, $weight, $bannerLink, $bannerName );
                    $wpdb->query( $wpdb->prepare( $sql, $params ) );
                    $newBannerId = $wpdb->insert_id;
                    if ( !$newSelectedBanner ) {
                        $newSelectedBanner = $newBannerId;
                    }
                    $lastSelectedBanner = $newBannerId;
                } else {
                    $bannerId = $data[ 'banner_ids' ][ $banner_index ];
                    $sql      = 'UPDATE ' . ADS_TABLE . ' SET meta=%s, status=%d, weight=%d, link=%s, filename=%s WHERE image_id=%d';
                    $params   = array( $meta, $status, $weight, $bannerLink, $bannerName, $bannerId );
                    $wpdb->query( $wpdb->prepare( $sql, $params ) );
                }

                $meta = null;
            }
        }
        if ( $useSelectedBanner ) {
            /*
             * selected banner workaround
             * touch gently
             */
            $firstExistingBanner = intval( $data[ 'banner_ids' ][ 0 ] );
            if ( !empty( $data[ 'selected_html_ad' ] ) ) {
                if ( $data[ 'selected_html_ad' ] == 'new' ) {
                    $selected_banner_id = $lastSelectedBanner;
                } else {
                    $selected_banner_id = intval( $data[ 'selected_html_ad' ] );
                }
            } elseif ( !empty( $firstExistingBanner ) ) {
                $selected_banner_id = intval( $data[ 'banner_ids' ][ 0 ] );
            } else {
                $selected_banner_id = $newSelectedBanner;
            }
            /*
             *  updating campaigns : setting selected banner
             */
            $wpdb->query( 'UPDATE ' . CAMPAIGNS_TABLE . ' SET selected_banner="' . ($selected_banner_id ? $selected_banner_id : '0') . '" WHERE campaign_id="' . $new_campaign_id . '"' );
        }
    }

    public static function handleVideoPost( $data, $new_campaign_id ) {
        global $wpdb;

        $existingBanners   = $wpdb->get_col( 'SELECT image_id FROM ' . ADS_TABLE . ' WHERE campaign_id="' . $new_campaign_id . '" AND parent_image_id=0 AND type=3' );
        $useSelectedBanner = isset( $data[ 'banner_display_method' ] ) && $data[ 'banner_display_method' ] == 'selected';
        $newBannerId       = '0';

        $newBanners = array();
        foreach ( $data[ 'banner_ids' ] as $banner_id ) {
            if ( !in_array( $banner_id, $existingBanners ) ) {
                $newBanners[] = $banner_id;
            }
        }

        if ( isset( $data[ 'video_ads' ] ) && !empty( $data[ 'video_ads' ] ) ) {
            foreach ( $data[ 'video_ads' ] as $banner_index => $banner_title ) {
                /*
                 * Ignore empty ads
                 */
                if ( empty( $banner_title ) ) {
                    continue;
                }

                $meta[ 'video' ] = stripslashes( $banner_title );
                $meta            = maybe_serialize( $meta );

                $status = $data[ 'status' ] ? 1 : 1;
                $type   = 3;
                $weight = $data[ 'banner_weight' ][ $banner_index ];

                if ( in_array( $data[ 'banner_ids' ][ $banner_index ], $newBanners ) ) {

                    $sql         = 'INSERT INTO ' . ADS_TABLE . ' SET campaign_id=%d, meta=%s, type=%d, status=%d, weight=%d';
                    $params      = array( $new_campaign_id, $meta, $type, $status, $weight );
                    $wpdb->query( $wpdb->prepare( $sql, $params ) );
                    $newBannerId = $wpdb->insert_id;
                } else {
                    $bannerId = $data[ 'banner_ids' ][ $banner_index ];
                    $sql      = 'UPDATE ' . ADS_TABLE . ' SET meta=%s, status=%d, weight=%d WHERE image_id=%d';
                    $params   = array( $meta, $status, $weight, $bannerId );
                    $wpdb->query( $wpdb->prepare( $sql, $params ) );
                }

                $meta = null;
            }
        }

        if ( $useSelectedBanner ) {
            $selected_banner_id = ($data[ 'selected_video_ad' ] == 'new') ? $newBannerId : $data[ 'selected_video_ad' ];
            /*
             *  updating campaigns : setting selected banner
             */
            $wpdb->query( 'UPDATE ' . CAMPAIGNS_TABLE . ' SET selected_banner="' . ($selected_banner_id ? $selected_banner_id : '0') . '" WHERE campaign_id="' . $new_campaign_id . '"' );
        }
    }

    public static function additionalBannerData( $data, $sql, $params ) {
        $sql .= ', banner_display_method=%s, cloud_url=%s, use_cloud=%d';
        $params = array_merge( $params, array( $data[ 'banner_display_method' ], $data[ 'cloud_url' ], (isset( $data[ 'use_cloud' ] ) && $data[ 'use_cloud' ]) ? 1 : 0 ) );

        return array(
            'sql'    => $sql,
            'params' => $params
        );
    }

    public static function additionalHTMLData( $data, $sql, $params ) {
        $sql .= ', banner_display_method=%s';
        if ( !isset( $data[ 'banner_display_method' ] ) ) {
            $data[ 'banner_display_method' ] = '';
        }
        $params = array_merge( $params, array( $data[ 'banner_display_method' ] ) );

        return array(
            'sql'    => $sql,
            'params' => $params
        );
    }

    public static function additionalAdsenseData( $data, $sql, $params ) {
        $sql    = 'adsense_client=%s, adsense_slot=%s, ' . $sql;
        $params = array_merge( array( $data[ 'adsense_client' ], $data[ 'adsense_slot' ] ), $params );

        return array(
            'sql'    => $sql,
            'params' => $params
        );
    }

    public static function additionalVideoData( $data, $sql, $params ) {
        $sql .= ', banner_display_method=%s';
        if ( !isset( $data[ 'banner_display_method' ] ) ) {
            $data[ 'banner_display_method' ] = '';
        }
        $params = array_merge( $params, array( $data[ 'banner_display_method' ] ) );

        return array(
            'sql'    => $sql,
            'params' => $params
        );
    }

    public static function getImageBanner( $campaign, $container_width ) {
        // Making the return array

        unset( $campaign[ 'category_ids' ], $campaign[ 'category_title' ], $campaign[ 'categories' ] );

        if ( $campaign[ 'banner_display_method' ] == 'selected' && empty( $campaign[ 'selected_banner_id' ] ) ) {
            return self::show_error( AC_API_ERROR_6 );
        }

        // CAMPAIGN BANNERS

        if ( (int) $campaign[ 'use_cloud' ] == 1 ) {
            cmac_log( 'Using cloud storage' );
            $banner_upload_path = $campaign[ 'cloud_url' ];
        } else {
            cmac_log( 'Using server storage' );
            $banner_upload_path = cmac_get_upload_url();
        }

        if ( $campaign[ 'banner_display_method' ] == 'selected' ) {
            if ( isset( $container_width ) &&
            is_array( $campaign[ 'banner_variation' ][ $campaign[ 'selected_banner' ] ] ) &&
            !empty( $campaign[ 'banner_variation' ][ $campaign[ 'selected_banner' ] ] ) ) {
                $banner_variants = $campaign[ 'banner_variation' ][ $campaign[ 'selected_banner' ] ] + array( count( $campaign[ 'banner_variation' ][ $campaign[ 'selected_banner' ] ] ) => $campaign[ 'selected_banner' ] );
                $banner          = ac_get_responsive_banner( $banner_variants, $container_width );
            } else {
                $banner = AC_Data::get_banner( $campaign[ 'selected_banner' ] );
            }

            $image_size                                      = getimagesize( cmac_get_upload_dir() . $banner[ 'filename' ] );
            //$image_size = getimagesize($banner_upload_path . $banner['filename']);
            $campaign[ 'selected_banner' ]                   = $banner_upload_path . $banner[ 'filename' ];
            $campaign[ 'selected_banner_title_tag' ]         = $campaign[ 'banner_title_tag' ][ $campaign[ 'selected_banner_id' ] ];
            $campaign[ 'selected_custom_banner_new_window' ] = $campaign[ 'custom_banner_new_window' ][ $campaign[ 'selected_banner_id' ] ];
            $campaign[ 'selected_banner_alt_tag' ]           = $campaign[ 'banner_alt_tag' ][ $campaign[ 'selected_banner_id' ] ];
            $campaign[ 'selected_banner_link' ]              = $campaign[ 'banner_link' ][ $campaign[ 'selected_banner_id' ] ];
            $campaign[ 'selected_banner_id' ]                = $banner[ 'image_id' ];

            if ( isset( $container_width ) && $image_size[ 0 ] > $container_width ) {
                $campaign[ 'resize' ] = 1;
            }
        } elseif ( $campaign[ 'banner_display_method' ] == 'random' ) {
            $random_banner_index = ac_get_random_banner_index( $campaign[ 'banner_weight' ] );

            if ( isset( $container_width ) &&
            is_array( $campaign[ 'banner_variation' ][ $campaign[ 'banner_filename' ][ $random_banner_index ] ] ) &&
            !empty( $campaign[ 'banner_variation' ][ $campaign[ 'banner_filename' ][ $random_banner_index ] ] ) ) {
                $banner_variants = $campaign[ 'banner_variation' ][ $campaign[ 'banner_filename' ][ $random_banner_index ] ] + array( count( $campaign[ 'banner_variation' ][ $campaign[ 'banner_filename' ][ $random_banner_index ] ] ) => $campaign[ 'banner_filename' ][ $random_banner_index ] );
                $banner          = ac_get_responsive_banner( $banner_variants, $container_width );
            } else {
                $banner = AC_Data::get_banner( $campaign[ 'banner_filename' ][ $random_banner_index ] );
            }
            $image_size                                      = getimagesize( cmac_get_upload_dir() . $banner[ 'filename' ] );
//            $image_size = getimagesize($banner_upload_path . $banner['filename']);
            $campaign[ 'selected_banner' ]                   = $banner_upload_path . $banner[ 'filename' ];
            $campaign[ 'selected_banner_title_tag' ]         = $campaign[ 'banner_title_tag' ][ $random_banner_index ];
            $campaign[ 'selected_custom_banner_new_window' ] = $campaign[ 'custom_banner_new_window' ][ $random_banner_index ];
            $campaign[ 'selected_banner_alt_tag' ]           = $campaign[ 'banner_alt_tag' ][ $random_banner_index ];
            $campaign[ 'selected_banner_link' ]              = $campaign[ 'banner_link' ][ $random_banner_index ];
            $campaign[ 'selected_banner_id' ]                = $banner[ 'image_id' ];

            if ( isset( $container_width ) && $image_size[ 0 ] > $container_width ) {
                $campaign[ 'resize' ] = 1;
            }
        } elseif ( $campaign[ 'banner_display_method' ] == 'all' ) {
            foreach ( $campaign[ 'banner_title_tag' ] as $index => $title_tag ) {
                if ( isset( $container_width ) &&
                is_array( $campaign[ 'banner_variation' ][ $campaign[ 'banner_filename' ][ $index ] ] ) &&
                !empty( $campaign[ 'banner_variation' ][ $campaign[ 'banner_filename' ][ $index ] ] ) ) {
                    $banner_variants = $campaign[ 'banner_variation' ][ $campaign[ 'banner_filename' ][ $index ] ] + array( count( $campaign[ 'banner_variation' ][ $campaign[ 'banner_filename' ][ $index ] ] ) => $campaign[ 'banner_filename' ][ $index ] );
                    $banner          = ac_get_responsive_banner( $banner_variants, $container_width );
                } else {
                    $banner = AC_Data::get_banner( $campaign[ 'banner_filename' ][ $index ] );
                }

//                $image_size = getimagesize($banner_upload_path . $banner['filename']);
                $image_size                                                    = getimagesize( cmac_get_upload_dir() . $banner[ 'filename' ] );
                $campaign[ 'banners' ][ $index ][ 'image' ]                    = $banner_upload_path . $banner[ 'filename' ];
                $campaign[ 'banners' ][ $index ][ 'title_tag' ]                = $title_tag;
                $campaign[ 'banners' ][ $index ][ 'alt_tag' ]                  = $campaign[ 'banner_alt_tag' ][ $index ];
                $campaign[ 'banners' ][ $index ][ 'custom_banner_new_window' ] = $campaign[ 'custom_banner_new_window' ][ $index ];
                $campaign[ 'banners' ][ $index ][ 'link' ]                     = $campaign[ 'banner_link' ][ $index ];
                $campaign[ 'banners' ][ $index ][ 'image_width' ]              = $image_size[ 0 ];
                $campaign[ 'banners' ][ $index ][ 'image_height' ]             = $image_size[ 1 ];
                $campaign[ 'banners' ][ $index ][ 'id' ]                       = $banner[ 'image_id' ];

                if ( isset( $container_width ) && $image_size[ 0 ] > $container_width ) {
                    $campaign[ 'banners' ][ $index ][ 'resize' ] = 1;
                }
            }

            if ( !empty( $campaign[ 'meta' ][ 'rotated_random' ] ) ) {
                /*
                 * Randomize the array
                 */
                $campaign[ 'banners' ] = self::shuffle_assoc( $campaign[ 'banners' ] );
            }
        }

        return $campaign;
    }

    private static function shuffle_assoc( $list ) {
        if ( !is_array( $list ) ) {
            return $list;
        }

        $keys   = array_keys( $list );
        shuffle( $keys );
        $random = array();
        foreach ( $keys as $key ) {
            $random[ $key ] = $list[ $key ];
        }
        return $random;
    }

    public static function getHTMLBanner( $campaign ) {
        // Making the return array

        foreach ( $campaign[ 'meta' ] as $index => $meta ) {
            if ( !is_numeric( $index ) ) {
                continue;
            }
            $campaign[ 'banners' ][ $index ][ 'html' ] = $meta[ 'html' ];
            $campaign[ 'banners' ][ $index ][ 'id' ]   = $index;
        }

        /*
         * Remove the expired banners
         */
        $campaign[ 'banners' ] = self::removeExpiredBanners( $campaign[ 'banners' ], $campaign );

        return $campaign;
    }

    public static function getVideoBanner( $campaign ) {
        // Making the return array
        foreach ( $campaign[ 'meta' ] as $index => $meta ) {
            if ( !is_numeric( $index ) ) {
                continue;
            }
            $campaign[ 'banners' ][ $index ][ 'html' ] = $meta[ 'video' ];
            $campaign[ 'banners' ][ $index ][ 'id' ]   = $index;
        }

        return $campaign;
    }

    public static function getFloatingBanner( $campaign ) {
        // Making the return array
        foreach ( $campaign[ 'meta' ] as $index => $meta ) {
            if ( !is_numeric( $index ) ) {
                continue;
            }
            $campaign[ 'banners' ][ $index ][ 'html' ] = $meta[ 'html' ];
            $campaign[ 'banners' ][ $index ][ 'id' ]   = $index;
        }
        return $campaign;
    }

    public static function getFloatingBottomBanner( $campaign ) {
        // Making the return array
        foreach ( $campaign[ 'meta' ] as $index => $meta ) {
            if ( !is_numeric( $index ) ) {
                continue;
            }
            $campaign[ 'banners' ][ $index ][ 'html' ] = $meta[ 'html' ];
            $campaign[ 'banners' ][ $index ][ 'id' ]   = $index;
        }
        return $campaign;
    }

    public static function removeExpiredBanners( $banners, $campaign ) {
        $now = new DateTime();
        if ( !empty( $banners ) ) {
            foreach ( $banners as $index => $banner ) {
                $date = new DateTime( $campaign[ 'meta' ][ $index ][ 'banner_expiration_date' ] );
                if ( $date < $now ) {
                    unset( $banners[ $index ] );
                }
            }
        }

        return $banners;
    }

    public static function prepareImageReturn( $campaign ) {
        /*
         * IMAGE BANNERS
         */
        if ( $campaign[ 'banner_display_method' ] != 'all' ) {
            $ret_array[ 'banner_id' ]                = $campaign[ 'selected_banner_id' ];
            $ret_array[ 'image' ]                    = $campaign[ 'selected_banner' ];
            $ret_array[ 'title_tag' ]                = $campaign[ 'selected_banner_title_tag' ];
            $ret_array[ 'alt_tag' ]                  = $campaign[ 'selected_banner_alt_tag' ];
            $ret_array[ 'custom_banner_new_window' ] = $campaign[ 'selected_custom_banner_new_window' ];
            if ( isset( $campaign[ 'resize' ] ) ) {
                $ret_array[ 'resize' ] = 1;
            }
        } else {
            $ret_array[ 'banners' ] = $campaign[ 'banners' ];
            foreach ( $campaign[ 'banners' ] as $index => $banner ) {
                if ( empty( $banner[ 'link' ] ) && !empty( $campaign[ 'link' ] ) ) {
                    $ret_array[ 'banners' ][ $index ][ 'link' ] = $campaign[ 'link' ];
                }
                if ( isset( $banner[ 'resize' ] ) ) {
                    $ret_array[ 'banners' ][ $index ][ 'resize' ] = 1;
                }
            }
        }

        if ( !empty( $ret_array[ 'banners' ] ) ) {
            /*
             * Remove the expired banners
             */
            $ret_array[ 'banners' ] = self::removeExpiredBanners( $ret_array[ 'banners' ], $campaign );
        }

        if ( !empty( $campaign[ 'selected_banner_link' ] ) ) {
            $ret_array[ 'banner_link' ] = $campaign[ 'selected_banner_link' ];
        } else if ( !empty( $campaign[ 'link' ] ) ) {
            $ret_array[ 'banner_link' ] = $campaign[ 'link' ];
        }

        $ret_array[ 'banner_variations' ] = (bool) get_option( 'acs_use_banner_variations' ) ? 'Yes' : 'No';

        return $ret_array;
    }

    public static function prepareHTMLReturn( $campaign ) {
        $ret_array[ 'html' ]   = '';
        $ret_array[ 'width' ]  = $campaign[ 'width' ];
        $ret_array[ 'height' ] = $campaign[ 'height' ];

        $displayMethod = $campaign[ 'banner_display_method' ];
        switch ( $displayMethod ) {
            case 'selected':
                $selectedBanner                      = $campaign[ 'selected_banner_id' ];
                $ret_array[ 'html' ] .= $campaign[ 'meta' ][ $selectedBanner ][ 'html' ];
                $ret_array[ 'banners' ][ 0 ][ 'id' ] = $selectedBanner;
                $ret_array[ 'banner_id' ]            = strval( $selectedBanner );
                $ret_array[ 'selected_banner_link' ] = $campaign[ 'banner_link' ][ $selectedBanner ];
                $ret_array[ 'banner_custom_js' ]     = $campaign[ 'banner_custom_js' ][ $selectedBanner ];
                break;
            case 'random':
                $randomAdKey                         = array_rand( $campaign[ 'banners' ] );
                $ret_array[ 'html' ] .= $campaign[ 'meta' ][ $randomAdKey ][ 'html' ];
                $ret_array[ 'banners' ][ 0 ][ 'id' ] = $randomAdKey;
                $ret_array[ 'banner_id' ]            = strval( $randomAdKey );
                $ret_array[ 'selected_banner_link' ] = $campaign[ 'banner_link' ][ $randomAdKey ];
                $ret_array[ 'banner_custom_js' ]     = $campaign[ 'banner_custom_js' ][ $randomAdKey ];
                break;
            default:
                break;
        }

        $ret_array[ 'link' ] = !empty( $ret_array[ 'selected_banner_link' ] ) ? $ret_array[ 'selected_banner_link' ] : $campaign[ 'link' ];
        return $ret_array;
    }

    public static function prepareFloatingReturn( $campaign ) {
        $ret_array[ 'html' ]                              = '';
        $ret_array[ 'width' ]                             = $campaign[ 'width' ];
        $ret_array[ 'height' ]                            = $campaign[ 'height' ];
        $ret_array[ 'background' ]                        = $campaign[ 'background' ];
        $ret_array[ 'seconds_to_show' ]                   = $campaign[ 'seconds_to_show' ];
        $ret_array[ 'banner_edges' ]                      = $campaign[ 'banner_edges' ];
        $ret_array[ 'show_effect' ]                       = $campaign[ 'show_effect' ];
        $ret_array[ 'user_show_method' ]                  = $campaign[ 'user_show_method' ];
        $ret_array[ 'reset_floating_banner_cookie_time' ] = $campaign[ 'reset_floating_banner_cookie_time' ];
        $ret_array[ 'underlay_type' ]                     = $campaign[ 'underlay_type' ];

        $displayMethod = $campaign[ 'banner_display_method' ];

        switch ( $displayMethod ) {
            case 'selected':
                $selectedBanner                      = $campaign[ 'selected_banner_id' ];
                $ret_array[ 'html' ] .= $campaign[ 'meta' ][ $selectedBanner ][ 'html' ];
                $ret_array[ 'banners' ][ 0 ][ 'id' ] = $selectedBanner;
                $ret_array[ 'banner_id' ]            = strval( $selectedBanner );
                $ret_array[ 'selected_banner_link' ] = $campaign[ 'banner_link' ][ $selectedBanner ];
                break;
            case 'random':
                $randomAdKey                         = array_rand( $campaign[ 'meta' ] );
                $ret_array[ 'html' ] .= $campaign[ 'meta' ][ $randomAdKey ][ 'html' ];
                $ret_array[ 'banners' ][ 0 ][ 'id' ] = $randomAdKey;
                $ret_array[ 'banner_id' ]            = strval( $randomAdKey );
                $ret_array[ 'selected_banner_link' ] = $campaign[ 'banner_link' ][ $randomAdKey ];
                break;
            default:
                break;
        }

        $ret_array[ 'link' ] = !empty( $ret_array[ 'selected_banner_link' ] ) ? $ret_array[ 'selected_banner_link' ] : $campaign[ 'link' ];
        return $ret_array;
    }

    public static function prepareFloatingBottomReturn( $campaign ) {
        $ret_array[ 'html' ]                              = '';
        $ret_array[ 'width' ]                             = $campaign[ 'width' ];
        $ret_array[ 'height' ]                            = $campaign[ 'height' ];
        $ret_array[ 'background' ]                        = $campaign[ 'background' ];
        $ret_array[ 'seconds_to_show' ]                   = $campaign[ 'seconds_to_show' ];
        $ret_array[ 'banner_edges' ]                      = $campaign[ 'banner_edges' ];
        $ret_array[ 'show_effect' ]                       = $campaign[ 'show_effect' ];
        $ret_array[ 'user_show_method' ]                  = $campaign[ 'user_show_method' ];
        $ret_array[ 'reset_floating_banner_cookie_time' ] = $campaign[ 'reset_floating_banner_cookie_time' ];
//        $ret_array['underlay_type'] = $campaign['underlay_type'];

        $displayMethod = $campaign[ 'banner_display_method' ];

        switch ( $displayMethod ) {
            case 'selected':
                $selectedBanner                      = $campaign[ 'selected_banner_id' ];
                $ret_array[ 'html' ] .= $campaign[ 'meta' ][ $selectedBanner ][ 'html' ];
                $ret_array[ 'banners' ][ 0 ][ 'id' ] = $selectedBanner;
                $ret_array[ 'banner_id' ]            = strval( $selectedBanner );
                $ret_array[ 'selected_banner_link' ] = $campaign[ 'banner_link' ][ $selectedBanner ];
                break;
            case 'random':
                $randomAdKey                         = array_rand( $campaign[ 'meta' ] );
                $ret_array[ 'html' ] .= $campaign[ 'meta' ][ $randomAdKey ][ 'html' ];
                $ret_array[ 'banners' ][ 0 ][ 'id' ] = $randomAdKey;
                $ret_array[ 'banner_id' ]            = strval( $randomAdKey );
                $ret_array[ 'selected_banner_link' ] = $campaign[ 'banner_link' ][ $randomAdKey ];
                break;
            default:
                break;
        }

        $ret_array[ 'link' ] = !empty( $ret_array[ 'selected_banner_link' ] ) ? $ret_array[ 'selected_banner_link' ] : $campaign[ 'link' ];
        return $ret_array;
    }

    public static function prepareAdSenseReturn( $campaign ) {
        /*
         * AD SENSE
         */
        $ret_array[ 'adsense_client' ] = trim( $campaign[ 'adsense_client' ] );
        $ret_array[ 'adsense_slot' ]   = trim( $campaign[ 'adsense_slot' ] );
        $ret_array[ 'width' ]          = trim( $campaign[ 'width' ] );
        $ret_array[ 'height' ]         = trim( $campaign[ 'height' ] );
        return $ret_array;
    }

    public static function prepareVideoReturn( $campaign ) {
        $ret_array[ 'html' ] = '';

        $displayMethod = $campaign[ 'banner_display_method' ];

        switch ( $displayMethod ) {
            case 'selected':
                $selectedBanner                      = $campaign[ 'selected_banner_id' ];
                $ret_array[ 'html' ] .= $campaign[ 'meta' ][ $selectedBanner ][ 'video' ];
                $ret_array[ 'banners' ][ 0 ][ 'id' ] = $selectedBanner;
                $ret_array[ 'banner_id' ]            = strval( $selectedBanner );
                break;
            case 'random':
                $randomAdKey                         = array_rand( $campaign[ 'meta' ] );
                $ret_array[ 'html' ] .= $campaign[ 'meta' ][ $randomAdKey ][ 'video' ];
                $ret_array[ 'banners' ][ 0 ][ 'id' ] = $randomAdKey;
                $ret_array[ 'banner_id' ]            = strval( $randomAdKey );
                break;
            default:
                break;
        }

        $ret_array[ 'link' ] = $campaign[ 'link' ];
        return $ret_array;
    }

}
