<?php

/**
 * CM Ad Changer
 *
 * @author CreativeMinds (http://ad-changer.cminds.com)
 * @copyright Copyright (c) 2013, CreativeMinds
 */
class AC_Data {

    /**
     * Performs campaign storage
     * @return Array
     * @param Array   $data  Array of fields
     */
    public static function ac_handle_campaigns_post( $data ) {
        if ( (!isset( $_POST[ 'campaign_settings_noncename' ] ) || !wp_verify_nonce( $_POST[ 'campaign_settings_noncename' ], 'CM_ADCHANGER_CAMPAIGN_SETTINGS' )) && $_GET[ 'action' ] != 'duplicate' ) {
            exit( 'Bad Request!' );
        };

        global $wpdb;
        $errors = array();
        // VALIDATIONS START
        if ( empty( $data ) ) {
            return array( 'errors' => array( 'No data entered' ), 'fields_data' => $data );
        }

        if ( empty( $data[ 'title' ] ) ) {
            $errors[] = 'Campaign Name field is empty';
        }

        if ( isset( $data[ 'category_title' ] ) && empty( $data[ 'category_title' ] ) ) {
            foreach ( $data[ 'category_title' ] as $category_title ) {
                if ( strlen( $category_title ) < 7 ) {
                    $errors[] = "Domain Name is too short";
                }
            }
        }

        if ( isset( $data[ 'date_from' ] ) && !empty( $data[ 'date_from' ] ) ) {
            foreach ( $data[ 'date_from' ] as $period_index => $date_from ) {
                if ( empty( $date_from ) ) {
                    $errors[] = 'Start Date not given';
                    break;
                }

                $date_from = new DateTime( $date_from );
                $hour_from = $data[ 'hours_from' ][ $period_index ];
                $min_from  = $data[ 'mins_from' ][ $period_index ];

                if ( empty( $data[ 'date_till' ][ $period_index ] ) ) {
                    $errors[] = 'End Date not given';
                    break;
                }
                $date_till = new DateTime( $data[ 'date_till' ][ $period_index ] );
                $hour_to   = $data[ 'hours_to' ][ $period_index ];
                $min_to    = $data[ 'mins_to' ][ $period_index ];

                $datetime_from = $date_from->format( 'Y-m-d' ) . ' ' . $hour_from . ':' . $min_from . ':00';
                $datetime_till = $date_till->format( 'Y-m-d' ) . ' ' . $hour_to . ':' . $min_to . ':00';
                /*
                  if(strtotime($datetime_from)<=$today||strtotime($datetime_till)<=$today){
                  $errors[]='Dates must be in future';
                  break;
                  }
                 */
                $date_diff     = strtotime( $datetime_till ) - strtotime( $datetime_from );

                if ( $date_diff < 0 ) {
                    $errors[] = 'The End Date must be after the Start Date';
                    break;
                }
            }
        }

        if ( !isset( $data[ 'active_week_days' ] ) || empty( $data[ 'active_week_days' ] ) ) {
            $data[ 'active_week_days' ] = array( 'Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat' );
        }

        if ( strlen( $data[ 'comment' ] ) > 500 ) {
            $errors[] = 'Note is too long';
        }

        if ( isset( $data[ 'campaign_id' ] ) && !is_numeric( $data[ 'campaign_id' ] ) ) {
            $errors[] = 'Unknown campaign';
        }

        if ( strlen( $data[ 'manager_email' ] ) > 100 ) {
            $errors[] = 'Manager Email is too long(max. 100)';
        }

        if ( !empty($data[ 'link' ]) && filter_var($data[ 'link' ], FILTER_VALIDATE_URL) === false) {
            $errors[] = 'Campaign url is not valid. Valid urls should begin with http:// or https://';
        }

        if ( !empty( $data[ 'cloud_url' ] ) ) {
            $matches = array();
            if ( !preg_match( '/^((http|https|ftp):\/\/)?[\.a-zA-Z0-9_\-]{2,100}\.[a-zA-Z0-9]{2,5}\/?[a-zA-Z0-9_\-\/]{0,100}:?\d{0,10}$/i', $data[ 'cloud_url' ], $matches ) ) {
                $errors[] = 'Please specify valid Cloud URL';
            }

            if ( substr( $data[ 'cloud_url' ], 0, 7 ) != 'http://' && substr( $data[ 'cloud_url' ], 0, 8 ) != 'https://' ) {
                $data[ 'cloud_url' ] = 'http://' . $data[ 'cloud_url' ];
            }
            if ( $data[ 'cloud_url' ][ strlen( $data[ 'cloud_url' ] ) - 1 ] != '/' ) {
                $data[ 'cloud_url' ] .= '/';
            }
        }

        $errors = apply_filters('cmac_campaign_errors', $errors, $data);

        if ( !empty( $errors ) ) {
            return array( 'errors' => $errors, 'fields_data' => $data );
        }

        $meta[ 'width' ]                             = !empty( $data[ 'width' ] ) ? $data[ 'width' ] : '';
        $meta[ 'height' ]                            = !empty( $data[ 'height' ] ) ? $data[ 'height' ] : '';
        $meta[ 'background' ]                        = !empty( $data[ 'background' ] ) ? $data[ 'background' ] : '';
        $meta[ 'seconds_to_show' ]                   = !empty( $data[ 'seconds_to_show' ] ) ? intval( $data[ 'seconds_to_show' ] ) : '';
        $meta[ 'banner_edges' ]                      = !empty( $data[ 'banner_edges' ] ) ? $data[ 'banner_edges' ] : '';
        $meta[ 'show_effect' ]                       = !empty( $data[ 'show_effect' ] ) ? $data[ 'show_effect' ] : '';
        $meta[ 'user_show_method' ]                  = !empty( $data[ 'user_show_method' ] ) ? $data[ 'user_show_method' ] : '';
        $meta[ 'reset_floating_banner_cookie_time' ] = !empty( $data[ 'reset_floating_banner_cookie_time' ] ) ? intval( $data[ 'reset_floating_banner_cookie_time' ] ) : '';
        $meta[ 'underlay_type' ]                     = !empty( $data[ 'underlay_type' ] ) ? $data[ 'underlay_type' ] : '';
        $meta[ 'rotated_random' ]                     = !empty( $data[ 'rotated_random' ] ) ? $data[ 'rotated_random' ] : false;

        array_filter( $meta );

        /*
         * fix for banner display method not selected
         */
        if ( empty( $data[ 'banner_display_method' ] ) ) {
            $data[ 'banner_display_method' ] = 'random';
        }
        if ( !empty( $meta[ 'width' ] ) && is_numeric( $meta[ 'width' ] ) ) {
            $meta[ 'width' ] .= 'px';
        }
        if ( !empty( $meta[ 'height' ] ) && is_numeric( $meta[ 'height' ] ) ) {
            $meta[ 'height' ] .= 'px';
        }
        if ( !empty( $meta[ 'background' ] ) && !preg_match( "/#/", $meta[ 'background' ] ) ) {
            $meta[ 'background' ] = '#' . $meta[ 'background' ];
        }
        $data[ 'meta' ] = maybe_serialize( $meta );

        // VALIDATIONS END
        /*
         *  1. Inserting the campaign record
         */
        if ( !isset( $data[ 'campaign_id' ] ) ) {
            $sqlStart = 'INSERT INTO ' . CAMPAIGNS_TABLE . ' SET ';
            $sqlEnd   = '';
            $sql      = 'group_id=%d,group_priority=%d,campaign_type_id=%d, title=%s, link=%s, max_impressions=%d, max_clicks=%d, comment=%s, custom_js=%s, active_week_days=%s, status=%d, send_notifications=%d, meta=%s, banner_new_window=%d';
            $params   = array( $data[ 'group_id' ], $data[ 'group_priority' ], $data[ 'campaign_type_id' ], $data[ 'title' ], $data[ 'link' ], $data[ 'max_impressions' ], $data[ 'max_clicks' ], $data[ 'comment' ], $data[ 'custom_js' ], implode( ',', $data[ 'active_week_days' ] ), isset( $data[ 'status' ] ) ? 1 : 0, isset( $data[ 'send_notifications' ] ) ? 1 : 0, $data[ 'meta' ], isset( $data[ 'banner_new_window' ] ) ? 1 : 0 );
        } else {
            $sqlStart = 'UPDATE ' . CAMPAIGNS_TABLE . ' SET ';
            $sqlEnd   = ' WHERE campaign_id="' . $data[ 'campaign_id' ] . '"';
            $sql      = 'group_id=%d,group_priority=%d,campaign_type_id=%d, title=%s, link=%s, max_impressions=%d, max_clicks=%d, comment=%s, custom_js=%s, active_week_days=%s, status=%d, send_notifications=%d, meta=%s, banner_new_window=%d';
            $params   = array( $data[ 'group_id' ], $data[ 'group_priority' ], $data[ 'campaign_type_id' ], $data[ 'title' ], $data[ 'link' ], $data[ 'max_impressions' ], $data[ 'max_clicks' ], $data[ 'comment' ], $data[ 'custom_js' ], implode( ',', $data[ 'active_week_days' ] ), isset( $data[ 'status' ] ) ? 1 : 0, isset( $data[ 'send_notifications' ] ) ? 1 : 0, $data[ 'meta' ], isset( $data[ 'banner_new_window' ] ) ? 1 : 0 );
        }

        /*
         * Saving the other campaigns than banners
         */
        switch ( $data[ 'campaign_type_id' ] ) {
            case '0':
                $result = AC_Advert::additionalBannerData( $data, $sql, $params );
                extract( $result );
                break;
            case '1':
                $result = AC_Advert::additionalHTMLData( $data, $sql, $params );
                extract( $result );
                break;
            case '2':
                $result = AC_Advert::additionalAdsenseData( $data, $sql, $params );
                extract( $result );
                break;
            case '3':
                $result = AC_Advert::additionalVideoData( $data, $sql, $params );
                extract( $result );
                break;
            case '4':
                $result = AC_Advert::additionalHTMLData( $data, $sql, $params );
                extract( $result );
                break;
            case '5':
                $result = AC_Advert::additionalHTMLData( $data, $sql, $params );
                extract( $result );
                break;
        }
        // VALIDATIONS END
        /*
         *  1. Inserting the campaign record
         */
        if ( !isset( $data[ 'campaign_id' ] ) ) {
            $wpdb->query( $wpdb->prepare( $sqlStart . $sql . $sqlEnd, $params ) );
            $new_campaign_id = $wpdb->insert_id;
        } else {
            $wpdb->query( $wpdb->prepare( $sqlStart . $sql . $sqlEnd, $params ) );
            $new_campaign_id = $data[ 'campaign_id' ];
        }

        /*
         * Clean the old banners
         */
        AC_Advert::cleanOldBanners( $data, $new_campaign_id );

        /*
         * Saving the banners
         */
        switch ( $data[ 'campaign_type_id' ] ) {
            case '0':
                AC_Advert::handleBannerPost( $data, $new_campaign_id );
                break;
            case '1':
                AC_Advert::handleHTMLPost( $data, $new_campaign_id );
                break;
            case '3':
                AC_Advert::handleVideoPost( $data, $new_campaign_id );
                break;
            case '4':
                AC_Advert::handleFloatingHTMLPost( $data, $new_campaign_id, 4 );
                break;
            case '5':
                AC_Advert::handleFloatingHTMLPost( $data, $new_campaign_id, 5 );
                break;
        }

        /*
         *  2. Inserting categories
         */

        $existing_categories = $wpdb->get_col( 'SELECT category_id FROM ' . CAMPAIGN_CATEGORIES_REL_TABLE . ' WHERE campaign_id = ' . $new_campaign_id );

        $deleted_categories = array();
        if ( empty( $data[ 'category_ids' ] ) || !is_array( $data[ 'category_ids' ] ) ) {
            $data[ 'category_ids' ] = array();
        }
        if ( !empty( $existing_categories ) ) {
            $deleted_categories = array_diff( $existing_categories, $data[ 'category_ids' ] );
        }

        if ( !empty( $deleted_categories ) ) {
            $wpdb->query( 'DELETE FROM ' . CATEGORIES_TABLE . ' WHERE category_id IN(' . implode( ', ', $deleted_categories ) . ')' );
            $wpdb->query( 'DELETE FROM ' . CAMPAIGN_CATEGORIES_REL_TABLE . ' WHERE campaign_id="' . $new_campaign_id . '" AND category_id IN(' . implode( ', ', $deleted_categories ) . ')' );
        }

        if ( !empty( $data[ 'category_title' ] ) && is_array( $data[ 'category_title' ] ) ) {
            foreach ( $data[ 'category_title' ] as $category_index => $category_title ) {
                if ( !isset( $data[ 'category_ids' ][ $category_index ] ) ) {
                    if ( !empty( $category_title ) ) {
                        $wpdb->query( $wpdb->prepare( 'INSERT INTO ' . CATEGORIES_TABLE . ' SET category_title=%s', $category_title ) );

                        $new_category_id = $wpdb->insert_id;
                        // setting relations to campaign
                        $wpdb->query( 'INSERT INTO ' . CAMPAIGN_CATEGORIES_REL_TABLE . ' SET campaign_id="' . $new_campaign_id . '", category_id="' . $new_category_id . '"' );
                    }
                } elseif ( !empty( $category_title ) ) {
                    $wpdb->query( $wpdb->prepare( 'UPDATE ' . CATEGORIES_TABLE . ' SET category_title=%s WHERE category_id=%d', $category_title, $data[ 'category_ids' ][ $category_index ] ) );
                }
            }
        }

        // 5. Storing advertiser
        $advertisers_cnt  = $wpdb->get_var( 'SELECT count(1) FROM ' . $wpdb->term_relationships . ' WHERE object_id="' . $new_campaign_id . '"' );
        $term_taxonomy_id = $wpdb->get_var( 'SELECT term_taxonomy_id FROM ' . $wpdb->term_taxonomy . ' WHERE term_id="' . $data[ 'advertiser_id' ] . '" AND taxonomy="' . AC_ADVERTISERS_TAXONOMY . '"' );

        if ( is_numeric( $term_taxonomy_id ) ) {
            if ( $advertisers_cnt != 1 ) {
                if ( $advertisers_cnt > 1 ) {
                    $wpdb->query( 'DELETE FROM ' . $wpdb->term_relationships . ' WHERE object_id="' . $new_campaign_id . '"' );
                }

                $wpdb->query( 'INSERT INTO ' . $wpdb->term_relationships . ' SET object_id="' . $new_campaign_id . '", term_taxonomy_id="' . $term_taxonomy_id . '"' );
            } else {
                $wpdb->query( 'UPDATE ' . $wpdb->term_relationships . ' SET term_taxonomy_id="' . $term_taxonomy_id . '" WHERE object_id="' . $new_campaign_id . '"' );
            }
        }

        /*
         * 4. Inserting activity dates
         */
        if ( isset( $new_campaign_id ) ) {
            $wpdb->query( 'DELETE FROM ' . PERIODS_TABLE . ' WHERE campaign_id="' . $new_campaign_id . '"' );
        }

        if ( isset( $data[ 'date_from' ] ) && !empty( $data[ 'date_from' ] ) ) {
            foreach ( $data[ 'date_from' ] as $period_index => $date_from ) {
                $date_from = new DateTime( $date_from );
                $hour_from = $data[ 'hours_from' ][ $period_index ];
                $min_from  = $data[ 'mins_from' ][ $period_index ];

                if ( empty( $data[ 'date_till' ][ $period_index ] ) ) {
                    $errors[] = 'End date not given';
                    break;
                }
                $date_till = new DateTime( $data[ 'date_till' ][ $period_index ] );
                $hour_to   = $data[ 'hours_to' ][ $period_index ];
                $min_to    = $data[ 'mins_to' ][ $period_index ];

                $datetime_from = $date_from->format( 'Y-m-d' ) . ' ' . ((int) $hour_from < 10 ? '0' . $hour_from : $hour_from) . ':' . ((int) $min_from < 10 ? '0' . $min_from : $min_from) . ':00';
                $datetime_till = $date_till->format( 'Y-m-d' ) . ' ' . ((int) $hour_to < 10 ? '0' . $hour_to : $hour_to) . ':' . ((int) $min_to < 10 ? '0' . $min_to : $min_to) . ':00';

                $wpdb->query( $wpdb->prepare( 'INSERT INTO ' . PERIODS_TABLE . ' SET campaign_id=%d, date_from=%s, date_till=%s', $new_campaign_id, $datetime_from, $datetime_till ) );
            }
        }

        /*
         * 5. Inserting manager
         */
        if ( isset( $data[ 'manager_email' ] ) ) {
            $manager_exists = (int) $wpdb->get_var( 'SELECT count(1) FROM ' . MANAGERS_TABLE . ' WHERE campaign_id="' . $new_campaign_id . '"' );
            if ( $manager_exists == 0 ) {
                $wpdb->query( $wpdb->prepare( 'INSERT INTO ' . MANAGERS_TABLE . ' SET campaign_id=%d, manager_email=%s', $new_campaign_id, $data[ 'manager_email' ] ) );
            } else {
                $wpdb->query( $wpdb->prepare( 'UPDATE ' . MANAGERS_TABLE . ' SET manager_email=%s WHERE campaign_id=%d', $data[ 'manager_email' ], $new_campaign_id ) );
            }
        } elseif ( !isset( $data[ 'send_notifications' ] ) ) {
            $wpdb->query( 'DELETE FROM ' . MANAGERS_TABLE . ' WHERE campaign_id="' . $new_campaign_id . '"' );
        }

        /*
         *  cleaning tmp folder
         */
        $handle = opendir( cmac_get_upload_dir() . AC_TMP_UPLOAD_PATH );
        if ( $handle ) {
            while ( false !== ($entry = readdir( $handle )) ) {
                if ( file_exists( cmac_get_upload_dir() . AC_TMP_UPLOAD_PATH . $entry ) && $entry != '.' && $entry != '..' ) {
                    unlink( cmac_get_upload_dir() . AC_TMP_UPLOAD_PATH . $entry );
                }
            }
        }
        /*
         *  Cleaning uploads folder
         */
        $images        = $wpdb->get_col( 'SELECT filename FROM ' . ADS_TABLE );
        $uploadsHandle = opendir( cmac_get_upload_dir() );
        if ( $uploadsHandle ) {
            while ( false !== ($entry = readdir( $uploadsHandle )) ) {
                $info = pathinfo( $entry );
                if ( file_exists( cmac_get_upload_dir() . $entry ) && is_file( cmac_get_upload_dir() . $entry ) && $entry != '.' && $entry != '..' && !in_array( $entry, $images ) ) {
                    if ( strpos( $entry, BANNER_THUMB_WIDTH . 'x' . BANNER_THUMB_WIDTH ) === false && strpos( $entry, BANNER_VARIATION_THUMB_WIDTH . 'x' . BANNER_VARIATION_THUMB_HEIGHT ) === false ) {
                        unlink( cmac_get_upload_dir() . $entry );
                        if ( file_exists( cmac_get_upload_dir() . $info[ 'filename' ] . BANNER_VARIATION_THUMB_WIDTH . 'x' . BANNER_VARIATION_THUMB_HEIGHT . '.' . $info[ 'extension' ] ) ) {
                            unlink( cmac_get_upload_dir() . $info[ 'filename' ] . BANNER_VARIATION_THUMB_WIDTH . 'x' . BANNER_VARIATION_THUMB_HEIGHT . '.' . $info[ 'extension' ] );
                        }

                        if ( file_exists( cmac_get_upload_dir() . $info[ 'filename' ] . BANNER_THUMB_WIDTH . 'x' . BANNER_THUMB_WIDTH . '.' . $info[ 'extension' ] ) ) {
                            unlink( cmac_get_upload_dir() . $info[ 'filename' ] . BANNER_THUMB_WIDTH . 'x' . BANNER_THUMB_WIDTH . '.' . $info[ 'extension' ] );
                        }
                    }
                }
            }
        }

        /**
         * Handle assigned dashboard user save
         */
        apply_filters( 'cmadcd-campaign-allowed-users-save', $data, $new_campaign_id );

        if ( !empty( $wpdb->last_error ) ) {
            return array( 'errors' => array( 'Database error' ), 'fields_data' => $data );
        }
        if ( empty( $errors ) ) {
            //fix for headers error
            //wp_redirect(admin_url('admin.php?page=ac_server_campaigns&action=edit&saved=1&campaign_id=' . $new_campaign_id));
            echo '<META HTTP-EQUIV="Refresh" Content="0; URL=' . admin_url( 'admin.php?page=ac_server_campaigns&action=edit&saved=1&campaign_id=' . $new_campaign_id ) . '">';
            exit;
        }
    }

    /**
     * Duplicates campaign db records and uploaded pics
     * @return Mixed
     * @param Int Campaign ID
     */
    function duplicate_campaign( $campaign_id ) {
        if ( !is_numeric( $campaign_id ) ) {
            return array( 'error' => 'Campaign ID not given' );
        }

        $campaign = self::get_campaign( $campaign_id );
        if ( !empty( $campaign[ 'banner_filename' ] ) ) {
            $uploadDir = wp_upload_dir();
            $baseDir   = $uploadDir[ 'basedir' ] . '/' . AC_UPLOAD_PATH;
            $tmpDir    = $baseDir . AC_TMP_UPLOAD_PATH;

            if ( ($handle = opendir( $baseDir )) !== FALSE ) {
                $existing_files = array();
                while ( false !== ($entry          = readdir( $handle )) ) {
                    $existing_files[] = $entry;
                }
            }

            foreach ( $campaign[ 'banner_filename' ] as $index => $banner_filename ) {
                $info = pathinfo( $banner_filename );
                do {
                    $new_filename = rand( 1000000, 999999999 ) . '.' . $info[ 'extension' ];
                } while ( in_array( $new_filename, $existing_files ) );

                $campaign[ 'banner_filename' ][ $index ] = $new_filename;
                $banner_contents                         = file_get_contents( $baseDir . $banner_filename );
                $f                                       = fopen( $tmpDir . $new_filename, 'w' );
                fwrite( $f, $banner_contents );
                fclose( $f );

                if ( file_exists( $baseDir . $info[ 'filename' ] . BANNER_THUMB_WIDTH . 'x' . BANNER_THUMB_HEIGHT . '.' . $info[ 'extension' ] ) ) {
                    $banner_thumb_contents = file_get_contents( $baseDir . $info[ 'filename' ] . BANNER_THUMB_WIDTH . 'x' . BANNER_THUMB_HEIGHT . '.' . $info[ 'extension' ] );
                    $new_filename_info     = pathinfo( $tmpDir . $new_filename );
                    $f                     = fopen( $tmpDir . $new_filename_info[ 'filename' ] . BANNER_THUMB_WIDTH . 'x' . BANNER_THUMB_HEIGHT . '.' . $new_filename_info[ 'extension' ], 'w' );
                    fwrite( $f, $banner_thumb_contents );
                    fclose( $f );
                }

                if ( isset( $campaign[ 'banner_variation' ][ $banner_filename ] ) && !empty( $campaign[ 'banner_variation' ][ $banner_filename ] ) ) {

                    foreach ( $campaign[ 'banner_variation' ][ $banner_filename ] as $index2 => $banner_variation_filename ) {
                        $info = pathinfo( $banner_variation_filename );
                        do {
                            $new_banner_variation_filename = rand( 1000000, 999999999 ) . '.' . $info[ 'extension' ];
                        } while ( in_array( $new_banner_variation_filename, $existing_files ) );

                        $campaign[ 'banner_variation' ][ $new_filename ][ $index2 ] = $new_banner_variation_filename;
                        $banner_contents                                            = file_get_contents( $baseDir . $banner_variation_filename );
                        $f                                                          = fopen( $tmpDir . $new_banner_variation_filename, 'w' );
                        fwrite( $f, $banner_contents );
                        fclose( $f );

                        if ( file_exists( $baseDir . $info[ 'filename' ] . BANNER_VARIATION_THUMB_WIDTH . 'x' . BANNER_VARIATION_THUMB_HEIGHT . '.' . $info[ 'extension' ] ) ) {
                            $banner_variation_thumb_contents = file_get_contents( $baseDir . $info[ 'filename' ] . BANNER_VARIATION_THUMB_WIDTH . 'x' . BANNER_VARIATION_THUMB_HEIGHT . '.' . $info[ 'extension' ] );
                            $new_filename_info               = pathinfo( $new_banner_variation_filename );
                            $f                               = fopen( $tmpDir . $new_filename_info[ 'filename' ] . BANNER_VARIATION_THUMB_WIDTH . 'x' . BANNER_VARIATION_THUMB_HEIGHT . '.' . $new_filename_info[ 'extension' ], 'w' );
                            fwrite( $f, $banner_variation_thumb_contents );
                            fclose( $f );
                        }
                    }
                } elseif ( empty( $campaign[ 'banner_variation' ][ $banner_filename ] ) ) {
                    unset( $campaign[ 'banner_variation' ][ $banner_filename ] );
                }

                if ( isset( $campaign[ 'banner_variation' ][ $banner_filename ] ) ) {
                    unset( $campaign[ 'banner_variation' ][ $banner_filename ] );
                }
            }
        }

        // preparing fields to pass to ac_handle_campaigns_post()
        unset( $campaign[ 'campaign_id' ] );
        $campaign[ 'advertiser_id' ] = $campaign[ 'advertiser' ][ 'advertiser_id' ];
        unset( $campaign[ 'advertiser' ] );

        if ( $campaign[ 'status' ] == '1' ) {
            $campaign[ 'status' ] = 'on';
        } else {
            unset( $campaign[ 'status' ] );
        }
        if ( $campaign[ 'banner_new_window' ] == '1' ) {
            $campaign[ 'banner_new_window' ] = 'on';
        } else {
            unset( $campaign[ 'banner_new_window' ] );
        }

        if ( isset( $campaign[ 'categories' ] ) ) {
            unset( $campaign[ 'categories' ] );
        }

        unset( $campaign[ 'banner_id' ] );
        unset( $campaign[ 'banner_clicks_cnt' ] );
        unset( $campaign[ 'banner_impressions_cnt' ] );
        $campaign[ 'selected_banner' ] = $campaign[ 'selected_banner_id' ];

        if ( !is_null( $campaign[ 'manager_email' ] ) ) {
            $campaign[ 'send_notifications' ] = 'on';
        }

        $campaigns       = self::get_campaigns();
        $existing_titles = array();

        foreach ( $campaigns as $index => $existing_campaign ) {
            $existing_titles[ $index ] = $existing_campaign->title;
        }

        $i = 1;

        do {
            $new_title = $campaign[ 'title' ] . ' (copy ' . $i . ')';
            $i++;
        } while ( in_array( $new_title, $existing_titles ) );

        if ( !empty( $campaign[ 'meta' ] ) && is_array( $campaign[ 'meta' ] ) ) {
            $new_banner_weight = array();
            foreach ( $campaign[ 'meta' ] as $key => $value ) {
                if ( !empty( $value[ 'html' ] ) || !empty( $campaign[ 'banner_custom_js' ][ $key ] ) ) {
                    $campaign[ 'html_ads' ][]   = $value[ 'html' ];
                    $campaign[ 'banner_ids' ][] = 'new';
                    $new_banner_weight[]        = $campaign[ 'banner_weight' ][ $key ];
                }

                if ( !empty( $value[ 'video' ] ) ) {
                    $campaign[ 'video_ads' ][]  = $value[ 'video' ];
                    $campaign[ 'banner_ids' ][] = 'new';
                    $new_banner_weight[]        = $campaign[ 'banner_weight' ][ $key ];
                }
            }
            $campaign[ 'banner_weight' ] = $new_banner_weight;
        }

        $campaign[ 'title' ] = $new_title;

        if ( !empty( $campaign[ 'banner_custom_js' ] ) ) {
            $campaign[ 'banner_custom_js' ] = array_values( $campaign[ 'banner_custom_js' ] );
        }
        self::ac_handle_campaigns_post( $campaign );

        if ( !$campaign ) {
            return array( 'error' => 'Campaign not found' );
        }

        return true;
    }

    /**
     * Performs settings storage
     * @return Array
     * @param Array   $data  Array of fields
     */
    public static function ac_handle_settings_post( $data ) {
        if ( !isset( $_POST[ 'general_settings_noncename' ] ) || !wp_verify_nonce( $_POST[ 'general_settings_noncename' ], 'CM_ADCHANGER_GENERAL_SETTINGS' ) ) {
            exit( 'Bad Request!' );
        };

        $errors = array();

        $campaigns = self::get_campaigns();

        if ( !empty( $errors ) )
            return array( 'errors' => $errors, 'data' => $data );

        if ( isset( $_POST[ 'acs_active' ] ) ) {
            update_option( 'acs_active', 1 );
        } else {
            update_option( 'acs_active', 0 );
        }
        /*
         * Use history table option
         */
        if ( isset( $_POST[ 'acs_disable_history_table' ] ) ) {
            update_option( 'acs_disable_history_table', 1 );
        } else {
            update_option( 'acs_disable_history_table', 0 );
        }

        if ( isset( $_POST[ 'acs_notification_email_tpl' ] ) )
            update_option( 'acs_notification_email_tpl', esc_html( stripslashes( $_POST[ 'acs_notification_email_tpl' ] ) ) );

        if ( isset( $_POST[ 'acs_inject_scripts' ] ) )
            update_option( 'acs_inject_scripts', 1 );
        else
            update_option( 'acs_inject_scripts', 0 );

        if ( isset( $_POST[ 'acs_auto_deactivate_campaigns' ] ) ) {
            update_option( 'acs_auto_deactivate_campaigns', 1 );
        } else {
            update_option( 'acs_auto_deactivate_campaigns', 0 );
        }

        if ( isset( $_POST[ 'acc_campaign_id' ] ) )
            update_option( 'acc_campaign_id', $_POST[ 'acc_campaign_id' ] );

        if ( isset( $_POST[ 'acs_custom_css' ] ) )
            update_option( 'acs_custom_css', $_POST[ 'acs_custom_css' ] );

        if ( isset( $_POST[ 'acs_geolocation_api_key' ] ) )
            update_option( 'acs_geolocation_api_key', $_POST[ 'acs_geolocation_api_key' ] );

        if ( isset( $_POST[ 'acs_use_banner_variations' ] ) )
            update_option( 'acs_use_banner_variations', $_POST[ 'acs_use_banner_variations' ] );

        if ( isset( $_POST[ 'acs_banner_area' ] ) )
            update_option( 'acs_banner_area', $_POST[ 'acs_banner_area' ] );

        if ( isset( $_POST[ 'acs_resize_banner' ] ) )
            update_option( 'acs_resize_banner', '1' );
        else
            update_option( 'acs_resize_banner', '0' );

        if ( isset( $_POST[ 'acs_slideshow_effect' ] ) )
            update_option( 'acs_slideshow_effect', $_POST[ 'acs_slideshow_effect' ] );

        if ( isset( $_POST[ 'acs_slideshow_interval' ] ) )
            update_option( 'acs_slideshow_interval', $_POST[ 'acs_slideshow_interval' ] );

        if ( isset( $_POST[ 'acs_slideshow_transition_time' ] ) )
            update_option( 'acs_slideshow_transition_time', $_POST[ 'acs_slideshow_transition_time' ] );

// Scripts in footer
        if ( isset( $_POST[ 'acs_script_in_footer' ] ) ) {
            update_option( 'acs_script_in_footer', $_POST[ 'acs_script_in_footer' ] );
        }

        return array();
    }

    /**
     * Inserts and updates advertisers term
     * @return Array or Boolean
     */
    function handle_advertiser_post( $data ) {
        global $wpdb;
        $error = '';
        if ( empty( $data[ 'advertiser_name' ] ) ) {
            $error = 'Advertiser Name field is empty';
        } elseif ( strlen( $data[ 'advertiser_name' ] ) < 3 ) {
            $error = 'Advertiser Name is too short';
        } elseif ( strlen( $data[ 'advertiser_name' ] ) > 200 ) {
            $error = 'Advertiser Name is too long';
        } elseif ( !preg_match( '/^[a-zA-Z0-9\s\-]/i', $data[ 'advertiser_name' ], $matches ) ) {
            $error = 'Advertiser Name can contain only letters, numbers, spaces and signs -_';
        }

        if ( empty( $error ) ) {
            $slug = strtolower( str_replace( ' ', '-', $data[ 'advertiser_name' ] ) );

            if ( !isset( $data[ 'advertiser_id' ] ) ) {
                $inDatabase = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . $wpdb->terms . ' WHERE slug=%s', $slug ) );

                if ( $inDatabase ) {
                    return array( 'success' => 1, 'advertiser_id' => $inDatabase->term_id );
                }

                $wpdb->query( $wpdb->prepare( 'INSERT INTO ' . $wpdb->terms . ' SET name=%s, slug=%s', $data[ 'advertiser_name' ], $slug ) );
                $new_term_id = $wpdb->insert_id;

                if ( $new_term_id && empty( $wpdb->last_error ) ) {
                    $wpdb->query( $wpdb->prepare( 'INSERT INTO ' . $wpdb->term_taxonomy . ' SET term_id=%d, taxonomy=%s, description=""', $new_term_id, AC_ADVERTISERS_TAXONOMY ) );

                    if ( empty( $wpdb->last_error ) ) {
                        return array( 'success' => 1, 'advertiser_id' => $new_term_id );
                    } else {
                        $result = array( 'error' => "Database error" );
                        if ( CMAC_DEBUG ) {
                            $result[ 'error' ] .= ': ' . $wpdb->last_error;
                        }
                        return $result;
                    }
                } else {
                    $result = array( 'error' => "Database error" );
                    if ( CMAC_DEBUG ) {
                        $result[ 'error' ] .= ': ' . $wpdb->last_error;
                    }
                    return $result;
                }
            } else {
                $wpdb->query( $wpdb->prepare( 'UPDATE ' . $wpdb->terms . ' SET name=%s, slug=%s WHERE term_id=%d', $data[ 'advertiser_name' ], $slug, $data[ 'advertiser_id' ] ) );

                if ( empty( $wpdb->last_error ) ) {
                    return array( 'success' => 1 );
                } else {
                    $result = array( 'error' => "Database error" );
                    if ( CMAC_DEBUG ) {
                        $result[ 'error' ] .= ': ' . $wpdb->last_error;
                    }
                    return $result;
                }
            }
        } else {
            return array( 'error' => $error );
        }
    }

    /**
     * Deletes advertisers term
     * @return Boolean
     */
    function delete_advertiser( $advertiser_id ) {
        global $wpdb;
        $error = '';
        if ( !is_numeric( $advertiser_id ) )
            $error = 'Advertiser not chosen';

        if ( empty( $error ) ) {
            $term_taxonomy_ids = $wpdb->get_col( 'SELECT term_taxonomy_id FROM ' . $wpdb->term_taxonomy . ' WHERE term_id="' . $advertiser_id . '"' );
            if ( $term_taxonomy_ids && !empty( $term_taxonomy_ids ) )
                foreach ( $term_taxonomy_ids as $term_taxonomy_id )
                    $wpdb->query( 'DELETE FROM ' . $wpdb->term_relationships . ' WHERE term_taxonomy_id="' . $term_taxonomy_id . '"' );

            $wpdb->query( 'DELETE FROM ' . $wpdb->terms . ' WHERE term_id="' . $advertiser_id . '"' );
            $wpdb->query( 'DELETE FROM ' . $wpdb->term_taxonomy . ' WHERE term_id="' . $advertiser_id . '"' );
            return array( 'success' => 1 );
        } else
            return array( 'error' => $error );
    }

    /**
     * Gets advertisers list
     * @return Array
     */
    public static function get_advertisers() {
        global $wpdb;

        $advertisers = $wpdb->get_results( 'SELECT t.term_id as advertiser_id, t.name, t.slug
											FROM ' . $wpdb->terms . ' t
											INNER JOIN ' . $wpdb->term_taxonomy . ' tt ON tt.term_id=t.term_id
											WHERE tt.taxonomy="' . AC_ADVERTISERS_TAXONOMY . '"
											ORDER BY t.name ASC', ARRAY_A );

        return $advertisers;
    }

    /**
     * Gets list of all campaigns
     * @return Array
     */
    public static function get_campaigns() {
        global $wpdb;
        $campaigns = $wpdb->get_results( 'SELECT c.*, count(ci.image_id) as banners_cnt FROM ' . CAMPAIGNS_TABLE . ' as c
                                        LEFT JOIN ' . ADS_TABLE . ' as ci ON ci.campaign_id=c.campaign_id AND ci.status = 1
                                        GROUP BY c.campaign_id
                                        ' );
        foreach ( $campaigns as $campaign_index => $campaign ) {
            /*
             * These are updated only on hourly basis for performance reasons
             */
            $campaigns[ $campaign_index ]->impressions_cnt = self::ac_get_impressions_cnt( $campaign->campaign_id );
            $campaigns[ $campaign_index ]->clicks_cnt      = self::ac_get_clicks_cnt( $campaign->campaign_id );
            $campaigns[ $campaign_index ]->advertiser      = self::get_advertiser( $campaign->campaign_id );
        }
        return $campaigns;
    }

    /**
     * Gets list of all campaigns
     * @return Array
     */
    public static function getGroupsForStatisticsDropdown() {
        global $wpdb;

        $groups = $wpdb->get_results( 'SELECT * FROM ' . GROUPS_TABLE . ' ORDER BY description DESC' );

        return $groups;
    }

    /**
     * Gets single campaign
     * @return Array
     * @param Int   $campaign_id  Campaign ID
     */
    public static function get_campaign( $campaign_id, $getImageCounts = false ) {
        global $wpdb;
        $ret_fields = array( 'campaign_id' => $campaign_id );

        $campaign = $wpdb->get_row( 'SELECT c.*, cm.manager_email FROM ' . CAMPAIGNS_TABLE . ' c
									LEFT JOIN ' . MANAGERS_TABLE . ' cm ON cm.campaign_id=c.campaign_id
									WHERE c.campaign_id="' . $campaign_id . '"' );
        if ( !$campaign ) {
            return null;
        }
        $ret_fields[ 'title' ]                 = $campaign->title;
        $ret_fields[ 'link' ]                  = $campaign->link;
        $ret_fields[ 'banner_display_method' ] = $campaign->banner_display_method;
        $ret_fields[ 'selected_banner_id' ]    = $campaign->selected_banner;
        $ret_fields[ 'max_impressions' ]       = $campaign->max_impressions;
        $ret_fields[ 'max_clicks' ]            = $campaign->max_clicks;
        $ret_fields[ 'comment' ]               = $campaign->comment;
        $ret_fields[ 'custom_js' ]             = $campaign->custom_js;
        $ret_fields[ 'adsense_client' ]        = $campaign->adsense_client;
        $ret_fields[ 'adsense_slot' ]          = $campaign->adsense_slot;
        $ret_fields[ 'active_week_days' ]      = !empty( $campaign->active_week_days ) ? explode( ',', $campaign->active_week_days ) : array();
        $ret_fields[ 'manager_email' ]         = $campaign->manager_email;
        $ret_fields[ 'send_notifications' ]    = $campaign->send_notifications;
        $ret_fields[ 'cloud_url' ]             = $campaign->cloud_url;
        $ret_fields[ 'use_cloud' ]             = $campaign->use_cloud;
        $ret_fields[ 'status' ]                = $campaign->status;
        $ret_fields[ 'banner_new_window' ]     = $campaign->banner_new_window;
        $ret_fields[ 'campaign_type_id' ]      = $campaign->campaign_type_id;
        $ret_fields[ 'group_id' ]              = $campaign->group_id;
        $ret_fields[ 'group_priority' ]        = $campaign->group_priority;
        $ret_fields[ 'meta' ]                  = maybe_unserialize( $campaign->meta );

        $categories = $wpdb->get_results( 'SELECT cc.category_id, cc.category_title FROM ' . CATEGORIES_TABLE . ' cc
											INNER JOIN ' . CAMPAIGN_CATEGORIES_REL_TABLE . ' ccr ON ccr.category_id=cc.category_id
											WHERE ccr.campaign_id="' . $campaign_id . '"' );

        foreach ( $categories as $category ) {
            $ret_fields[ 'category_ids' ][]   = $category->category_id;
            $ret_fields[ 'category_title' ][] = $category->category_title;
        }

        $campaign_categories = $wpdb->get_results( 'SELECT cc.category_id FROM ' . CAMPAIGNS_TABLE . ' c
												   INNER JOIN ' . CAMPAIGN_CATEGORIES_REL_TABLE . ' ccr ON ccr.campaign_id=c.campaign_id
												   INNER JOIN ' . CATEGORIES_TABLE . ' cc ON cc.category_id=ccr.category_id
												   WHERE c.campaign_id="' . $campaign_id . '"' );

        foreach ( $campaign_categories as $category ) {
            $ret_fields[ 'categories' ][] = $category->category_id;
        }


        $ret_fields[ 'advertiser' ] = self::get_advertiser( $campaign_id );


        $images = $wpdb->get_results( 'SELECT ci.* FROM ' . ADS_TABLE . ' ci '
        . 'WHERE ci.campaign_id="' . $campaign_id . '" AND parent_image_id<1 AND status=1 AND type="' . $ret_fields[ 'campaign_type_id' ] . '"' );
        foreach ( $images as $image ) {
            $ret_fields[ 'banner_id' ][ $image->image_id ] = $image->image_id;
            /*
             * take counts only when needed
             */
            if ( $getImageCounts === true ) {
                $ret_fields[ 'banner_clicks_cnt' ][ $image->image_id ]      = self::ac_get_banner_clicks_cnt( $image->image_id );
                $ret_fields[ 'banner_impressions_cnt' ][ $image->image_id ] = self::ac_get_banner_impressions_cnt( $image->image_id );
            } else {
                $ret_fields[ 'banner_clicks_cnt' ][ $image->image_id ]      = 0;
                $ret_fields[ 'banner_impressions_cnt' ][ $image->image_id ] = 0;
            }
            $ret_fields[ 'meta' ][ $image->image_id ]             = maybe_unserialize( $image->meta );
            $ret_fields[ 'type' ][ $image->image_id ]             = $image->type;
            $ret_fields[ 'banner_weight' ][ $image->image_id ]    = $image->weight;
            $ret_fields[ 'banner_custom_js' ][ $image->image_id ] = $image->banner_custom_js;
            $ret_fields[ 'html_title' ][ $image->image_id ] = $image->title;
            $ret_fields[ 'banner_link' ][ $image->image_id ]      = $image->link;
            if ( $image->type == 0 ) {
                $ret_fields[ 'banner_filename' ][ $image->image_id ]          = $image->filename;
                $ret_fields[ 'banner_title' ][ $image->image_id ]             = $image->title;
                $ret_fields[ 'banner_title_tag' ][ $image->image_id ]         = $image->title_tag;
                $ret_fields[ 'banner_alt_tag' ][ $image->image_id ]           = $image->alt_tag;
                $ret_fields[ 'custom_banner_new_window' ][ $image->image_id ] = $image->custom_banner_new_window;
                $bannerFilename                                               = $wpdb->get_col( 'SELECT filename FROM ' . ADS_TABLE . ' WHERE campaign_id="' . $campaign_id . '" AND parent_image_id="' . $image->image_id . '"' );
                $ret_fields[ 'banner_variation' ][ $image->filename ]         = $bannerFilename;
            } elseif ( $image->type == 4 || $image->type == 5 ) {
                $ret_fields[ 'floating_ad_banner_filenames' ][ $image->image_id ] = $image->filename;
                $ret_fields[ 'banner_variation' ][ $image->filename ]             = null;
            } else {
                $ret_fields[ 'banner_variation' ][ $image->filename ] = null;
            }
            if ( $image->image_id == $campaign->selected_banner ) {
                $ret_fields[ 'selected_banner' ] = $image->filename;
            }
        }

        $periods = $wpdb->get_results( 'SELECT cp.date_from, cp.date_till FROM ' . PERIODS_TABLE . ' cp
												   WHERE cp.campaign_id="' . $campaign_id . '"' );
        foreach ( $periods as $period ) {
            $date_from_int = strtotime( $period->date_from );
            $date_till_int = strtotime( $period->date_till );

            $ret_fields[ 'date_from' ][]      = date( 'm/d/Y', $date_from_int );
            $ret_fields[ 'date_from_time' ][] = $date_from_int;
            $ret_fields[ 'hours_from' ][]     = date( 'H', $date_from_int );
            $ret_fields[ 'mins_from' ][]      = date( 'i', $date_from_int );

            $ret_fields[ 'date_till' ][]      = date( 'm/d/Y', $date_till_int );
            $ret_fields[ 'date_till_time' ][] = $date_till_int;
            $ret_fields[ 'hours_to' ][]       = date( 'H', $date_till_int );
            $ret_fields[ 'mins_to' ][]        = date( 'i', $date_till_int );
        }

        /*
         * Check if campaign is active (field: "Acvite" is set and the Activity Settings are met
         */
        $ret_fields[ 'campaign_active' ] = $ret_fields[ 'status' ] && self::is_campaign_active( $ret_fields );

        $ret_fields[ 'width' ]                             = !empty( $ret_fields[ 'meta' ][ 'width' ] ) ? $ret_fields[ 'meta' ][ 'width' ] : '';
        $ret_fields[ 'height' ]                            = !empty( $ret_fields[ 'meta' ][ 'height' ] ) ? $ret_fields[ 'meta' ][ 'height' ] : '';
        $ret_fields[ 'background' ]                        = !empty( $ret_fields[ 'meta' ][ 'background' ] ) ? $ret_fields[ 'meta' ][ 'background' ] : '';
        $ret_fields[ 'seconds_to_show' ]                   = !empty( $ret_fields[ 'meta' ][ 'seconds_to_show' ] ) ? $ret_fields[ 'meta' ][ 'seconds_to_show' ] : '';
        $ret_fields[ 'show_effect' ]                       = !empty( $ret_fields[ 'meta' ][ 'show_effect' ] ) ? $ret_fields[ 'meta' ][ 'show_effect' ] : '';
        $ret_fields[ 'banner_edges' ]                      = !empty( $ret_fields[ 'meta' ][ 'banner_edges' ] ) ? $ret_fields[ 'meta' ][ 'banner_edges' ] : '';
        $ret_fields[ 'user_show_method' ]                  = !empty( $ret_fields[ 'meta' ][ 'user_show_method' ] ) ? $ret_fields[ 'meta' ][ 'user_show_method' ] : '';
        $ret_fields[ 'underlay_type' ]                     = !empty( $ret_fields[ 'meta' ][ 'underlay_type' ] ) ? $ret_fields[ 'meta' ][ 'underlay_type' ] : '';
        $ret_fields[ 'reset_floating_banner_cookie_time' ] = !empty( $ret_fields[ 'meta' ][ 'reset_floating_banner_cookie_time' ] ) ? $ret_fields[ 'meta' ][ 'reset_floating_banner_cookie_time' ] : '';
        unset( $ret_fields[ 'meta' ][ 'width' ] );
        unset( $ret_fields[ 'meta' ][ 'height' ] );
        unset( $ret_fields[ 'meta' ][ 'background' ] );
        unset( $ret_fields[ 'meta' ][ 'seconds_to_show' ] );
        unset( $ret_fields[ 'meta' ][ 'show_effect' ] );
        unset( $ret_fields[ 'meta' ][ 'banner_edges' ] );
        unset( $ret_fields[ 'meta' ][ 'user_show_method' ] );
        unset( $ret_fields[ 'meta' ][ 'underlay_type' ] );
        unset( $ret_fields[ 'meta' ][ 'reset_floating_banner_cookie_time' ] );
        return $ret_fields;
    }

    /**
     * Check if the campaign is active
     * @param type $campaign
     * @return boolean
     */
    public static function is_campaign_active( $campaign ) {
        $campaign_active = false;
        $activity_dates  = false;
        $will_be_active  = false;
        $auto_deactivate = get_option( 'acs_auto_deactivate_campaigns', '0' );

        /*
         * CAMPAIGN ACTIVITY PERIODS
         */
        if ( isset( $campaign[ 'date_from_time' ] ) && is_array( $campaign[ 'date_from_time' ] ) ) {
            $activity_dates = true;
            $time           = current_time( 'timestamp' );

            foreach ( $campaign[ 'date_from_time' ] as $period_index => $date_from_str ) {

                if ( $time < $campaign[ 'date_till_time' ][ $period_index ] ) {
                    /*
                     * There's a campaign end in the future
                     */
                    $will_be_active = true;

                    if ( $time > $date_from_str ) {
                        $campaign_active = true;
                        continue;
                    }
                }
            }

            /*
             * If campaign will not be active anymore (has ended all of it's activity periods) and the setting says we need to deactivate it
             */
            if ( !$will_be_active && $auto_deactivate ) {
                self::deactivate_campaign( $campaign[ 'campaign_id' ] );
            }
        } else {
            $campaign_active = true;
        }

        if ( $campaign_active ) {
            if ( is_array( $campaign[ 'active_week_days' ] ) ) {
                if ( in_array( date( 'D' ), $campaign[ 'active_week_days' ] ) ) {
                    $campaign_active = true;
                } else {
                    $campaign_active = false;
                }
            }
        }

        if ( $activity_dates ) {
            AC_Data::_internal_error( 'Campaign activity dates not matched.' );
        }

        return $campaign_active;
    }

    /**
     * Gets single banner
     * @return Array
     * @param Mixed   $arg: banner id or banner filename
     */
    public static function get_banner( $arg = NULL ) {
        global $wpdb;

        if ( is_null( $arg ) ) {
            return false;
        }

        if ( is_numeric( $arg ) ) {
            $where = 'image_id="' . $arg . '"';
        } else {
            $where = 'filename="' . $arg . '"';
        }

        $banner = $wpdb->get_row( 'SELECT * FROM ' . ADS_TABLE . ' WHERE ' . $where, ARRAY_A );

        return $banner;
    }

    /**
     * Gets impressions count for a campaign
     * @return Int
     * @param Int   $campaign_id  Campaign ID
     */
    public static function ac_get_impressions_cnt( $campaign_id ) {
        global $wpdb;
        $lastCount = get_option( 'acs_last_impressions_count_campaign_' . $campaign_id );
        if ( !$lastCount || ((time() - $lastCount) > HOURINSECONDS) ) {
            $impressions_cnt = $wpdb->get_var( 'SELECT count(1) FROM ' . HISTORY_TABLE . ' WHERE event_type="impression" AND campaign_id="' . $campaign_id . '"' );
            $wpdb->query( $wpdb->prepare( 'UPDATE ' . CAMPAIGNS_TABLE . ' SET impressions_count = %d WHERE campaign_id=%d', $impressions_cnt, $campaign_id ) );
            update_option( 'acs_last_impressions_count_campaign_' . $campaign_id, time() );
        } else {
            $impressions_cnt = $wpdb->get_var( 'SELECT impressions_count FROM ' . CAMPAIGNS_TABLE . ' WHERE campaign_id="' . $campaign_id . '"' );
        }
        return $impressions_cnt;
    }

    /**
     * Gets clicks count for a campaign
     * @return Int
     * @param Int   $banner_id  Campaign ID
     */
    public static function ac_get_clicks_cnt( $campaign_id ) {
        global $wpdb;

        $lastCount = get_option( 'acs_last_clicks_count_campaign_' . $campaign_id );
        if ( !$lastCount || ((time() - $lastCount) > HOURINSECONDS) ) {
            $clicks_cnt = $wpdb->get_var( 'SELECT count(1) FROM ' . HISTORY_TABLE . ' WHERE event_type="click" AND campaign_id="' . $campaign_id . '"' );
            $wpdb->query( $wpdb->prepare( 'UPDATE ' . CAMPAIGNS_TABLE . ' SET clicks_count = %d WHERE campaign_id=%d', $clicks_cnt, $campaign_id ) );
            update_option( 'acs_last_clicks_count_campaign_' . $campaign_id, time() );
        } else {
            $clicks_cnt = $wpdb->get_var( 'SELECT clicks_count FROM ' . CAMPAIGNS_TABLE . ' WHERE campaign_id="' . $campaign_id . '"' );
        }
        return $clicks_cnt;
    }

    /**
     * Gets clicks count for a campaign
     * @return Int
     * @param Int   $banner_id  Banner ID
     */
    public static function ac_get_banner_clicks_cnt( $banner_id ) {
        global $wpdb;
        $lastCount = get_option( 'acs_last_clicks_count_banner_' . $banner_id );
        if ( !$lastCount || ((time() - $lastCount) > HOURINSECONDS) ) {
            $clicks_cnt = $wpdb->get_var( 'SELECT count(1) FROM ' . HISTORY_TABLE . ' WHERE event_type="click" AND banner_id="' . $banner_id . '"' );
            $wpdb->query( $wpdb->prepare( 'UPDATE ' . ADS_TABLE . ' SET clicks_count = %d WHERE image_id=%d', $clicks_cnt, $banner_id ) );
            update_option( 'acs_last_clicks_count_banner_' . $banner_id, time() );
        } else {
            $clicks_cnt = $wpdb->get_var( 'SELECT clicks_count FROM ' . ADS_TABLE . ' WHERE image_id="' . $banner_id . '"' );
        }
        return $clicks_cnt;
    }

    /**
     * Gets impressions count for a banner
     * @return Int
     * @param Int   $banner_id  Banner ID
     */
    public static function ac_get_banner_impressions_cnt( $banner_id ) {
        global $wpdb;
        $lastCount = get_option( 'acs_last_impressions_count_banner_' . $banner_id );
        if ( !$lastCount || ((time() - $lastCount) > HOURINSECONDS) ) {
            $impressions_cnt = $wpdb->get_var( 'SELECT count(1) FROM ' . HISTORY_TABLE . ' WHERE event_type="impression" AND banner_id="' . $banner_id . '"' );
            $wpdb->query( $wpdb->prepare( 'UPDATE ' . ADS_TABLE . ' SET impressions_count = %d WHERE image_id=%d', $impressions_cnt, $banner_id ) );
            update_option( 'acs_last_impressions_count_banner_' . $banner_id, time() );
        } else {
            $impressions_cnt = $wpdb->get_var( 'SELECT impressions_count FROM ' . ADS_TABLE . ' WHERE image_id="' . $banner_id . '"' );
        }
        return $impressions_cnt;
    }

    public static function get_advertiser( $campaign_id ) {
        global $wpdb;

        $advertiser = $wpdb->get_row( 'SELECT t.name, t.term_id as advertiser_id
                                    FROM ' . $wpdb->terms . ' t
                                    INNER JOIN ' . $wpdb->term_taxonomy . ' tt ON tt.term_id=t.term_id
                                    INNER JOIN ' . $wpdb->term_relationships . ' tr ON tr.term_taxonomy_id = tt.term_taxonomy_id
                                    WHERE tr.object_id="' . $campaign_id . '" AND tt.taxonomy="' . AC_ADVERTISERS_TAXONOMY . '"', ARRAY_A );

        return $advertiser;
    }

    /**
     * Removes campaign and all related data
     * @param Int   $campaign_id  Campaign ID
     */
    public static function ac_remove_campaign( $campaign_id ) {
        global $wpdb;

        $images = $wpdb->get_col( 'SELECT filename FROM ' . ADS_TABLE . ' WHERE campaign_id="' . $campaign_id . '"' );

        foreach ( $images as $image )
            if ( file_exists( cmac_get_upload_dir() . $image ) ) {
                $info     = pathinfo( $image );
                $filename = cmac_get_upload_dir() . $image;
                if ( file_exists( $filename ) && !is_dir( $filename ) ) {
                    unlink( $filename );
                }

                if ( file_exists( cmac_get_upload_dir() . $info[ 'filename' ] . BANNER_THUMB_WIDTH . 'x' . BANNER_THUMB_HEIGHT . '.' . $info[ 'extension' ] ) )
                    unlink( cmac_get_upload_dir() . $info[ 'filename' ] . BANNER_THUMB_WIDTH . 'x' . BANNER_THUMB_HEIGHT . '.' . $info[ 'extension' ] );
                if ( file_exists( cmac_get_upload_dir() . $info[ 'filename' ] . BANNER_VARIATION_THUMB_WIDTH . 'x' . BANNER_VARIATION_THUMB_HEIGHT . '.' . $info[ 'extension' ] ) )
                    unlink( cmac_get_upload_dir() . $info[ 'filename' ] . BANNER_VARIATION_THUMB_WIDTH . 'x' . BANNER_VARIATION_THUMB_HEIGHT . '.' . $info[ 'extension' ] );
            }

        $wpdb->query( 'DELETE FROM ' . CAMPAIGNS_TABLE . ' WHERE campaign_id="' . $campaign_id . '"' );
        $wpdb->query( 'DELETE FROM ' . CAMPAIGN_CATEGORIES_REL_TABLE . ' WHERE campaign_id="' . $campaign_id . '"' );
        $wpdb->query( 'DELETE FROM ' . ADS_TABLE . ' WHERE campaign_id="' . $campaign_id . '"' );
        $wpdb->query( 'DELETE FROM ' . PERIODS_TABLE . ' WHERE campaign_id="' . $campaign_id . '"' );
    }

    /**
     * Gets category
     * @return Array
     * @param Int   $category_id  Category ID
     */
    public static function ac_get_category( $category_id ) {
        global $wpdb;
        return $wpdb->get_row( 'SELECT * FROM ' . CATEGORIES_TABLE . ' WHERE category_id="' . $category_id . '"' );
    }

    /**
     * Gets paged history
     * @return Array
     * @param Int   $page  Page Number
     * @param Int   $output_type OBJECT OF ARRAY
     */
    public static function get_history( $page = 1, $output_type = OBJECT ) {
        global $wpdb;

        if ( !is_numeric( $page ) || $page < 0 ) {
            return array();
        }

        if ( $page > 0 ) {
            $offset = ($page - 1) * AC_HISTORY_PER_PAGE_LIMIT;
            $limit  = AC_HISTORY_PER_PAGE_LIMIT;
        } else {
            $limit = false;
        }

        $where     = array();
        $condition = null;

        if ( isset( $_REQUEST[ 'events_filter' ] ) && $_REQUEST[ 'events_filter' ] != 'all' ) {
            $where[] = 'h.event_type="' . $_REQUEST[ 'events_filter' ] . '"';
        }

        if ( isset( $_REQUEST[ 'campaign_name' ] ) && !empty( $_REQUEST[ 'campaign_name' ] ) ) {
            $where[] = 'c.title LIKE "%' . $_REQUEST[ 'campaign_name' ] . '%" ';
        }

        if ( isset( $_REQUEST[ 'advertiser_id' ] ) && !empty( $_REQUEST[ 'advertiser_id' ] ) && (int) $_REQUEST[ 'advertiser_id' ] != 0 ) {
            $where[] = 't.term_id="' . $_REQUEST[ 'advertiser_id' ] . '" ';
        }

        if ( !empty( $where ) ) {
            $condition = ' WHERE ' . implode( ' AND ', $where ) . ' ';
        }

        $history = $wpdb->get_results( 'SELECT h.*, c.title as campaign_title, c.campaign_id, c.banner_display_method, t.name as advertiser_name, i.* FROM ' . HISTORY_TABLE . ' h
										LEFT JOIN ' . CAMPAIGNS_TABLE . ' c ON c.campaign_id=h.campaign_id
										LEFT JOIN ' . ADS_TABLE . ' i ON i.image_id=h.banner_id
										LEFT JOIN ' . $wpdb->term_relationships . ' tr ON tr.object_id = c.campaign_id
										LEFT JOIN ' . $wpdb->term_taxonomy . ' tt ON tt.term_taxonomy_id=tr.term_taxonomy_id
										LEFT JOIN ' . $wpdb->terms . ' t ON t.term_id = tt.term_id
									    ' . (!is_null( $condition ) ? $condition : '') . '
									   ORDER BY h.regdate DESC' . ($limit !== false ? ' LIMIT ' . $offset . ', ' . AC_HISTORY_PER_PAGE_LIMIT : ''), $output_type );
        return $history;
    }

    /**
     * Gets months
     * @return Array
     */
    public static function get_history_months() {
        global $wpdb;

        $months     = $wpdb->get_results( 'SELECT DISTINCT DATE_FORMAT(regdate,"%M") as month, DATE_FORMAT(regdate,"%Y") as year FROM ' . HISTORY_TABLE . '
									  GROUP BY year, month
									  ORDER BY regdate DESC' );
        $ret_months = array();

        foreach ( $months as $month ) {
            if ( $month->year == date( 'Y' ) ) {
                $ret_months[] = $month->month;
            } else {
                $ret_months[] = $month->month . ', ' . $month->year;
            }
        }
        return $ret_months;
    }

    /**
     * Gets info for one month
     * @return Array
     * @param String   $month  month and year
     * @param Int   $campaign_id Campaign ID
     */
    public static function get_history_month( $month, $campaign_id = null ) {
        global $wpdb;

        $month = explode( ', ', $month );

        if ( count( $month ) == 1 ) {
            $month[ 1 ] = date( 'Y' );
        }

        $month_details = $wpdb->get_results( 'SELECT count(1) as cnt, i.title, i.type as banner_type, c.title as campaign_title, h.banner_id, i.filename, "impressions" as event_type
											FROM ' . HISTORY_TABLE . ' h
                                            INNER JOIN ' . ADS_TABLE . ' i ON h.banner_id=i.image_id
                                            JOIN ' . CAMPAIGNS_TABLE . ' c ON c.campaign_id=h.campaign_id
											WHERE DATE_FORMAT(h.regdate,"%M")="' . $month[ 0 ] . '" AND
												  DATE_FORMAT(h.regdate,"%Y")="' . $month[ 1 ] . '" AND
												  h.event_type="impression"
												  ' . ($campaign_id ? ' AND h.campaign_id=' . $campaign_id : '') . '
											GROUP BY i.title
										UNION
											SELECT count(1) as cnt, i.title, i.type as banner_type, c.title as campaign_title, h.banner_id, i.filename, "clicks" as event_type
											FROM ' . HISTORY_TABLE . ' h
                                            INNER JOIN ' . ADS_TABLE . ' i ON h.banner_id=i.image_id
                                            JOIN ' . CAMPAIGNS_TABLE . ' c ON c.campaign_id=h.campaign_id
											WHERE DATE_FORMAT(h.regdate,"%M")="' . $month[ 0 ] . '" AND
												  DATE_FORMAT(h.regdate,"%Y")="' . $month[ 1 ] . '" AND
												  h.event_type="click"
												  ' . ($campaign_id ? ' AND h.campaign_id=' . $campaign_id : '') . '
											GROUP BY i.title
										ORDER BY title' );

        $banners_stats = array();
        foreach ( $month_details as $record ) {
            $banners_stats[ $record->title ][ 'filename' ] = $record->filename;
            $banners_stats[ $record->title ][ 'campaign_title' ] = $record->campaign_title;
            $banners_stats[ $record->title ][ 'banner_id' ] = $record->banner_id;
            $banners_stats[ $record->title ][ 'banner_type' ] = $record->banner_type;
            if ( $record->event_type == 'impressions' ) {
                $banners_stats[ $record->title ][ 'impressions' ] = $record->cnt;
            }
            if ( $record->event_type == 'clicks' ) {
                $banners_stats[ $record->title ][ 'clicks' ] = $record->cnt;
            }
        }

        foreach ( $banners_stats as $title => $stats ) {
            if ( !isset( $stats[ 'impressions' ] ) ) {
                $banners_stats[ $title ][ 'impressions' ] = '0';
            }
            if ( !isset( $stats[ 'clicks' ] ) ) {
                $banners_stats[ $title ][ 'clicks' ] = '0';
            }
        }

        return $banners_stats;
    }

    /**
     * Gets info for one day or period of days
     * @return Array
     * @param String   $date_from date to start from
     * @param String   $date_to date to end to
     * @param Int   $campaign_id Campaign ID
     */
    public static function get_history_days_data( $date_from, $date_to, $campaign_id = null ) {
        global $wpdb;
        $whereDatePart = '';
        if ( !empty( $date_from ) ) {
            if ( empty( $date_to ) ) {
                $whereDatePart = ' (h.regdate BETWEEN \'' . date( 'Y-m-d 00:00:00', strtotime( $date_from ) ) . '\' AND \'' . date( 'Y-m-d 23:59:59', strtotime( $date_from ) ) . '\')';
            } else {
                if ( strtotime( $date_from ) > strtotime( $date_to ) ) {
                    return array( 'success' => false, 'data' => array(), 'counters' => array(), 'message' => translate( 'Date to cannot be before date from.' ) );
                }
                $whereDatePart = ' (h.regdate BETWEEN \'' . date( 'Y-m-d 00:00:00', strtotime( $date_from ) ) . '\' AND \'' . date( 'Y-m-d 23:59:59', strtotime( $date_to ) ) . '\')';
            }
        } else {
            /*
             * No dates
             */
            return array( 'success' => false, 'data' => array(), 'message' => translate( 'Please provide at least from date.' ) );
        }
        $range_details_clicks = $wpdb->get_results( 'SELECT count(event_id) AS cnt, date(h.regdate) AS date, i.type as banner_type, h.banner_id, i.title, c.title as campaign_title, i.filename, h.event_type, i.type, i.meta
                                                    FROM ' . HISTORY_TABLE . ' h
                                                    JOIN ' . ADS_TABLE . ' i ON h.banner_id=i.image_id
                                                    JOIN ' . CAMPAIGNS_TABLE . ' c ON c.campaign_id=h.campaign_id
                                                    WHERE ' . $whereDatePart . '
						    ' . ($campaign_id ? ' AND h.campaign_id=' . $campaign_id : '') . '
						    GROUP BY date(h.regdate), i.title, i.filename, h.event_type
                                                    ORDER BY regdate DESC;', ARRAY_A );
        /*
         * no data
         */
        if ( empty( $range_details_clicks ) ) {
            return array( 'success' => false, 'data' => array(), 'message' => translate( 'No data to show.' ), 'counters' => array() );
        }
        /*
         * output arrays
         */
        $banners_stats = array();
        $counters      = array( 'clicks' => 0, 'impressions' => 0 );
        foreach ( $range_details_clicks as $record ) {
            if ( !isset( $banners_stats[ $record[ 'date' ] ][ $record[ 'title' ] ] ) ) {
                $banners_stats[ $record[ 'date' ] ][ $record[ 'title' ] ] = array(
                    'title'       => $record[ 'title' ],
                    'filename'    => $record[ 'filename' ],
                    'clicks'      => 0,
                    'impressions' => 0,
                    'type'        => $record[ 'type' ],
                    'meta'        => $record[ 'meta' ],
                    'campaign_title' => $record[ 'campaign_title' ],
                    'banner_id' => $record[ 'banner_id' ],
                    'banner_type' => $record[ 'banner_type' ]
                );
            }
            if ( $record[ 'event_type' ] == 'click' ) {
                $banners_stats[ $record[ 'date' ] ][ $record[ 'title' ] ][ 'clicks' ] = $record[ 'cnt' ];
                $counters[ 'clicks' ] += $record[ 'cnt' ];
            } elseif ( $record[ 'event_type' ] == 'impression' ) {
                $banners_stats[ $record[ 'date' ] ][ $record[ 'title' ] ][ 'impressions' ] = $record[ 'cnt' ];
                $counters[ 'impressions' ] += $record[ 'cnt' ];
            }
        }
        return array( 'success' => true, 'data' => $banners_stats, 'counters' => $counters );
    }

    /**
     * Gets info for one month for groups
     * @return Array
     * @param String   $month  month and year
     * @param Int   $group_id Group id
     */
    public static function getHistoryGroups( $month, $group_id = null ) {
        global $wpdb;

        $month = explode( ', ', $month );

        if ( count( $month ) == 1 ) {
            $month[ 1 ] = date( 'Y' ); 
        }

        $range_details_clicks = $wpdb->get_results( 'SELECT count(event_id) AS cnt, date(h.regdate) AS date, h.banner_id, i.type as banner_type, c.title as campaign_title, i.title, i.filename, h.event_type
                                                    FROM ' . HISTORY_TABLE . ' h
                                                    JOIN ' . ADS_TABLE . ' i ON h.banner_id=i.image_id
                                                    JOIN ' . CAMPAIGNS_TABLE . ' c ON c.campaign_id=h.campaign_id
                                                    WHERE  h.group_id=' . $group_id . '
                                                    AND DATE_FORMAT(h.regdate,"%M")="' . $month[ 0 ] . '" AND
												  DATE_FORMAT(h.regdate,"%Y")="' . $month[ 1 ] . '"
						    GROUP BY date(h.regdate), i.title, i.filename, h.event_type
                                                    ORDER BY h.campaign_id DESC;', ARRAY_A );
        /*
         * no data
         */
        if ( empty( $range_details_clicks ) ) {
            return array( 'success' => false, 'data' => array(), 'message' => translate( 'No data to show.' ), 'counters' => array() );
        }
        /*
         * output arrays
         */
        $banners_stats = array();
        $counters      = array( 'clicks' => 0, 'impressions' => 0 );
        foreach ( $range_details_clicks as $record ) {
            if ( !isset( $banners_stats[ $record[ 'title' ] ] ) ) {
                $banners_stats[ $record[ 'title' ] ] = array(
                    'filename'    => $record[ 'filename' ],
                    'clicks'      => 0,
                    'impressions' => 0,
                    'campaign_title' => $record[ 'campaign_title' ],
                    'banner_id' => $record[ 'banner_id' ],
                    'banner_type' => $record[ 'banner_type' ]                    
                );
            }
            if ( $record[ 'event_type' ] == 'click' ) {
                $banners_stats[ $record[ 'title' ] ][ 'clicks' ] += $record[ 'cnt' ];
                $counters[ 'clicks' ] += $record[ 'cnt' ];
            } elseif ( $record[ 'event_type' ] == 'impression' ) {
                $banners_stats[ $record[ 'title' ] ][ 'impressions' ] += $record[ 'cnt' ];
                $counters[ 'impressions' ] += $record[ 'cnt' ];
            }
        } 
        return array( 'success' => true, 'data' => $banners_stats, 'counters' => $counters );
    }

    /**
     * removes all records from history table
     */
    public static function empty_history() {
        global $wpdb;
        $wpdb->query( 'TRUNCATE TABLE ' . HISTORY_TABLE );
    }

	/**
     * removes records from history table by date
     */
    public static function empty_history_by_date() {
        global $wpdb;
		$rowcount = $wpdb->get_var('SELECT COUNT(*) FROM '.HISTORY_TABLE.' WHERE regdate BETWEEN \'' . date( 'Y-m-d 00:00:00', strtotime( $_POST['start_date'] ) ) . '\' AND \'' . date( 'Y-m-d 23:59:59', strtotime( $_POST['end_date'] ) ) . '\'');
		if($rowcount > 0)
		{
			$sql = 'DELETE FROM '.HISTORY_TABLE.' WHERE regdate BETWEEN \'' . date( 'Y-m-d 00:00:00', strtotime( $_POST['start_date'] ) ) . '\' AND \'' . date( 'Y-m-d 23:59:59', strtotime( $_POST['end_date'] ) ) . '\'';
			$wpdb->query($sql);
			return '<span style="color:green;">'.$rowcount.' statistics successfully removed</span>';
		}
		else
		{
			return '<span style="color:red;">No statistics found between these dates</span>';
		}
    }

    /**
     * Gets clients logs
     * @return Array
     */
    public static function get_clients_logs() {
        global $wpdb;

        $client_domains = $wpdb->get_col( 'SELECT referer_url FROM ' . HISTORY_TABLE . '
											GROUP BY referer_url
											ORDER BY referer_url ASC, regdate DESC' );

        $clients_logs   = array();
        if ( $client_domains && !empty( $client_domains ) )
            foreach ( $client_domains as $domain )
                $clients_logs[] = $wpdb->get_row( 'SELECT h.referer_url, c.title as campaign_name, c.banner_display_method, h.regdate, i.filename, i.title as banner_name FROM ' . HISTORY_TABLE . ' h
														INNER JOIN ' . CAMPAIGNS_TABLE . ' c ON c.campaign_id=h.campaign_id
														INNER JOIN ' . ADS_TABLE . ' i ON h.banner_id=i.image_id
														WHERE h.referer_url = "' . $domain . '"
														ORDER BY h.regdate DESC
														LIMIT 1' );
        return $clients_logs;
    }

    public static function get_server_load( $time_range, $campaign_id = null ) {
        global $wpdb;
        $sql = 'SELECT count(1) as cnt FROM ' . HISTORY_TABLE . ' WHERE ';

        if ( $campaign_id && (int) $campaign_id != 0 ) {
            $additional_condition .= ' AND campaign_id="' . $campaign_id . '"';
        } else {
            $additional_condition = '';
        }

        switch ( $time_range ) {
            case 'day':
                $sql .= '"' . date( 'Y-m-d' ) . '"=DATE_FORMAT(regdate,"%Y-%m-%d") AND DATEDIFF("' . date( 'Y-m-d H:i:s' ) . '",DATE_FORMAT(regdate,"%Y-%m-%d %H:%i:%s"))<=1';
                $cur_hour = (int) date( 'H' );

                $today_requests = array();

                for ( $i = 1; $i <= $cur_hour; $i++ ) {
                    $hour                    = $i >= 10 ? $i : '0' . $i;
                    $today_requests[ $hour ] = $wpdb->get_var( 'SELECT count(1) as cnt FROM ' . HISTORY_TABLE . ' WHERE "' . date( 'Y-m-d' ) . '"=DATE_FORMAT(regdate,"%Y-%m-%d") AND DATE_FORMAT(regdate,"%H")="' . $hour . '"' . $additional_condition );
                }

                break;
            case 'week':
                $sql .= '"' . date( 'W' ) . '"=DATE_FORMAT(regdate,"%u") && DATEDIFF("' . date( 'Y-m-d H:i:s' ) . '",DATE_FORMAT(regdate,"%Y-%m-%d %H:%i:%s"))<=7';

                $cur_week_day = (int) date( 'N' );
                $week_days    = array( '1' => 'Monday', '2' => 'Tuesday', '3' => 'Wednesday', '4' => 'Thursday', '5' => 'Friday', '6' => 'Saturday', '7' => 'Sunday' );

                $cur_week_requests = array();

                for ( $i = 1; $i <= $cur_week_day; $i++ ) {
                    $day                                   = $i < 7 ? $i : 0;
                    $cur_week_requests[ $week_days[ $i ] ] = $wpdb->get_var( 'SELECT count(1) as cnt FROM ' . HISTORY_TABLE . ' WHERE "' . date( 'W' ) . '"=DATE_FORMAT(regdate,"%u") AND DATE_FORMAT(regdate,"%w")="' . $day . '"' . $additional_condition );
                }

                break;
            case 'month':
                $sql .= '"' . date( 'Y-m' ) . '"=DATE_FORMAT(regdate,"%Y-%m")';

                $sql_m1         = 'SELECT count(1) as cnt FROM ' . HISTORY_TABLE . ' WHERE DATE_FORMAT(regdate,"%Y-%m") = "' . date( 'Y-m' ) . '" AND DATEDIFF(NOW(),regdate)<=31';
                $month_requests = $wpdb->get_var( $sql_m1 );

                $cur_month_requests = array();

                for ( $i = 1; $i <= (int) date( 'j' ); $i++ ) {
                    $day                      = $i >= 10 ? $i : '0' . $i;
                    $date                     = date( 'Y-m-d', strtotime( date( 'Y-m-' . $i ) ) );
                    $cur_month_requests[ $i ] = $wpdb->get_var( 'SELECT count(1) as cnt FROM ' . HISTORY_TABLE . ' WHERE "' . $date . '"=DATE_FORMAT(regdate,"%Y-%m-%d")' . $additional_condition );
                }

                break;
            case 'six_months':
                $sql .= 'DATEDIFF("' . date( 'Y-m-d H:i:s' ) . '",DATE_FORMAT(regdate,"%Y-%m-%d %H:%i:%s"))<186';

                $this_month  = (int) date( 'n' );
                $prev_month1 = $this_month > 1 ? $this_month - 1 : 12;
                $prev_month2 = $prev_month1 > 1 ? $prev_month1 - 1 : 12;
                $prev_month3 = $prev_month2 > 1 ? $prev_month2 - 1 : 12;
                $prev_month4 = $prev_month3 > 1 ? $prev_month3 - 1 : 12;
                $prev_month5 = $prev_month4 > 1 ? $prev_month4 - 1 : 12;

                $sql_m1 = 'SELECT count(1) as cnt FROM ' . HISTORY_TABLE . ' WHERE DATE_FORMAT(regdate,"%m")=' . $this_month . ' AND DATEDIFF(NOW(),regdate)<=31' . $additional_condition;
                $sql_m2 = 'SELECT count(1) as cnt FROM ' . HISTORY_TABLE . ' WHERE DATE_FORMAT(regdate,"%m")=' . $prev_month1 . ' AND DATEDIFF(NOW(),regdate)<=62' . $additional_condition;
                $sql_m3 = 'SELECT count(1) as cnt FROM ' . HISTORY_TABLE . ' WHERE DATE_FORMAT(regdate,"%m")=' . $prev_month2 . ' AND DATEDIFF(NOW(),regdate)<=93' . $additional_condition;
                $sql_m4 = 'SELECT count(1) as cnt FROM ' . HISTORY_TABLE . ' WHERE DATE_FORMAT(regdate,"%m")=' . $prev_month3 . ' AND DATEDIFF(NOW(),regdate)<=124' . $additional_condition;
                $sql_m5 = 'SELECT count(1) as cnt FROM ' . HISTORY_TABLE . ' WHERE DATE_FORMAT(regdate,"%m")=' . $prev_month4 . ' AND DATEDIFF(NOW(),regdate)<=155' . $additional_condition;
                $sql_m6 = 'SELECT count(1) as cnt FROM ' . HISTORY_TABLE . ' WHERE DATE_FORMAT(regdate,"%m")=' . $prev_month5 . ' AND DATEDIFF(NOW(),regdate)<=186' . $additional_condition;

                $month_requests                                                             = array();
                $month_requests[ date( 'F', strtotime( '2014-' . $prev_month5 . '-01' ) ) ] = $wpdb->get_var( $sql_m6 );
                $month_requests[ date( 'F', strtotime( '2014-' . $prev_month4 . '-01' ) ) ] = $wpdb->get_var( $sql_m5 );
                $month_requests[ date( 'F', strtotime( '2014-' . $prev_month3 . '-01' ) ) ] = $wpdb->get_var( $sql_m4 );
                $month_requests[ date( 'F', strtotime( '2014-' . $prev_month2 . '-01' ) ) ] = $wpdb->get_var( $sql_m3 );
                $month_requests[ date( 'F', strtotime( '2014-' . $prev_month1 . '-01' ) ) ] = $wpdb->get_var( $sql_m2 );
                $month_requests[ date( 'F', strtotime( '2014-' . $this_month . '-01' ) ) ]  = $wpdb->get_var( $sql_m1 );
                break;
            default: // should be hour
                $sql .= 'TIMEDIFF("' . date( 'Y-m-d H:i:s' ) . '",DATE_FORMAT(regdate,"%Y-%m-%d %H:%i:%s"))<="01:00:00"';
                break;
        }

        $sql .= $additional_condition;
        $cnt = $wpdb->get_var( $sql );

        if ( isset( $month_requests ) ) {
            $ret_array = array( 'cnt' => $cnt, 'month_requests' => $month_requests );
            if ( isset( $cur_month_requests ) ) {
                $ret_array[ 'cur_month_requests' ] = $cur_month_requests;
            }

            return $ret_array;
        } elseif ( isset( $cur_week_requests ) ) {
            $ret_array = array( 'cnt' => $cnt, 'cur_week_requests' => $cur_week_requests );
            return $ret_array;
        } elseif ( isset( $today_requests ) ) {
            $ret_array = array( 'cnt' => $cnt, 'today_requests' => $today_requests );
            return $ret_array;
        }
        return $cnt;
    }

    public static function get_groups() {
        global $wpdb;

        $groups = $wpdb->get_results( 'SELECT g.*, count(c.campaign_id) as campaigns_cnt FROM ' . GROUPS_TABLE . ' as g
                                            LEFT JOIN ' . CAMPAIGNS_TABLE . ' as c ON g.group_id=c.group_id AND c.status = 1
                                            GROUP BY g.group_id
                                            ' );

        return $groups;
    }

    public static function get_group( $group_id ) {
        global $wpdb;

        $group = $wpdb->get_row( 'SELECT * FROM ' . GROUPS_TABLE . ' WHERE group_id=' . $group_id, ARRAY_A );

        if ( !$group ) {
            return null;
        }

        return $group;
    }

    public static function ac_handle_groups_post( $data ) {
        if ( !isset( $_REQUEST[ 'groups_settings_noncename' ] ) || !wp_verify_nonce( $_REQUEST[ 'groups_settings_noncename' ], 'CM_ADCHANGER_GROUPS_SETTINGS' ) ) {
            exit( 'Bad Request!' );
        };
        global $wpdb;
        $errors = array();
        // VALIDATIONS START
        if ( empty( $data ) ) {
            return array( 'errors' => array( 'No data entered' ), 'fields_data' => $data );
        }

        if ( empty( $data[ 'group_order' ] ) ) {
            $errors[] = 'Group Order cannot be empty';
        }

        if ( empty( $data[ 'description' ] ) ) {
            $errors[] = 'Group Name cannot be empty';
        }

        if ( strlen( $data[ 'description' ] ) > 50 ) {
            $errors[] = 'Group Name is too long';
        }

        if ( isset( $data[ 'group_id' ] ) && !is_numeric( $data[ 'group_id' ] ) ) {
            $errors[] = 'Unknown campaign';
        }

        if ( !empty( $errors ) ) {
            return array( 'errors' => $errors, 'fields_data' => $data );
        }

        // VALIDATIONS END
        $sql    = 'description=%s, group_order=%d';
        $params = array( $data[ 'description' ], $data[ 'group_order' ] );

        if ( !isset( $data[ 'group_id' ] ) ) {
            $sqlStart = 'INSERT INTO ' . GROUPS_TABLE . ' SET created_on = now(), ';
            $sqlEnd   = '';
        } else {
            $sqlStart = 'UPDATE ' . GROUPS_TABLE . ' SET ';
            $sqlEnd   = ' WHERE group_id="' . $data[ 'group_id' ] . '"';
        }

        $wpdb->query( $wpdb->prepare( $sqlStart . $sql . $sqlEnd, $params ) );
        $newGroupId = '';
        if ( !isset( $data[ 'group_id' ] ) ) {
            $newGroupId = $wpdb->insert_id;
        } else {
            if ( !empty( $data[ 'campaign_id' ] ) ) {
                $newGroupId = $data[ 'campaign_id' ];
            }
        }

        if ( !empty( $wpdb->last_error ) ) {
            return array( 'errors' => array( 'Database error' ), 'fields_data' => $data );
        }

        if ( empty( $errors ) ) {
            return array( 'group_id' => $newGroupId );
        }
    }

    /**
     * Duplicates group db records
     * @return Mixed
     * @param Int Campaign ID
     */
    function duplicate_group( $group_id ) {
        if ( !is_numeric( $group_id ) ) {
            return array( 'error' => 'Group ID not given' );
        }

        $group = self::get_group( $group_id );

        // preparing fields to pass to ac_handle_groups_post()
        unset( $group[ 'group_id' ] );

        $groups          = self::get_groups();
        $existing_groups = array();

        foreach ( $groups as $index => $existing_group ) {
            $existing_groups[ $index ] = $existing_group->description;
        }

        $i = 1;
        do {
            $new_title = $group[ 'description' ] . ' (copy ' . $i . ')';
            $i++;
        } while ( in_array( $new_title, $existing_groups ) );

        $group[ 'description' ] = $new_title;
        self::ac_handle_groups_post( $group );

        if ( !$group ) {
            return array( 'error' => 'Group not found' );
        }

        return true;
    }

    /**
     * Removes group and all related data
     * @param Int   $group_id  Group ID
     */
    public static function ac_remove_group( $group_id ) {
        global $wpdb;
        $wpdb->query( 'DELETE FROM ' . GROUPS_TABLE . ' WHERE group_id="' . $group_id . '"' );
    }

    /**
     * Selects the campaigns which are not in the group
     * @param type $group_id
     */
    public static function get_non_group_campaigns( $group_id ) {
        global $wpdb;
        $campaigns = $wpdb->get_results( 'SELECT * FROM ' . CAMPAIGNS_TABLE . ' WHERE group_id != "' . $group_id . '"' );
        return $campaigns;
    }

    /**
     * Selects the campaigns which are not in the group
     * @param type $group_id
     */
    public static function get_group_campaigns( $group_id, $onlyActive = false ) {
        global $wpdb;
        $campaigns = $wpdb->get_results( 'SELECT * FROM ' . CAMPAIGNS_TABLE . ' WHERE group_id = "' . $group_id . '" ' . (($onlyActive) ? ('AND status = 1') : ('')) . ' ORDER BY group_priority DESC' );
        return $campaigns;
    }

    /**
     * Removes the group id from the Campaign
     * @param type $campaign_id
     */
    public static function remove_campaign_from_group( $campaign_id ) {
        global $wpdb;

        $sqlStart = 'UPDATE ' . CAMPAIGNS_TABLE . ' SET ';
        $sqlEnd   = ' WHERE campaign_id="' . $campaign_id . '"';
        $sql      = 'group_id=0';

        $wpdb->query( $sqlStart . $sql . $sqlEnd );
        //Bug fixing
        $wpdb->query( 'UPDATE ' . HISTORY_TABLE . ' SET group_id=0 WHERE campaign_id="' . $campaign_id . '"' );
    }

    /**
     * Adds the group id from the Campaign
     * @param type $campaign_id
     */
    public static function add_campaign_to_group( $campaign_id, $group_id ) {
        global $wpdb;

        $sqlStart = 'UPDATE ' . CAMPAIGNS_TABLE . ' SET ';
        $sqlEnd   = ' WHERE campaign_id="' . $campaign_id . '"';
        $sql      = 'group_id=%d';
        $params   = array( $group_id );

        $wpdb->query( $wpdb->prepare( $sqlStart . $sql . $sqlEnd, $params ) );
        //Bug fixing
        $wpdb->query( 'UPDATE ' . HISTORY_TABLE . ' SET group_id='.$group_id.' WHERE campaign_id="' . $campaign_id . '"' );
    }

    /**
     * Adds the group id from the Campaign
     * @param type $campaign_id
     */
    public static function deactivate_campaign( $campaign_id ) {
        global $wpdb;
        $sqlStart = 'UPDATE ' . CAMPAIGNS_TABLE . ' SET status=0 WHERE campaign_id=%d';
        $params   = array( $campaign_id );
        $result   = $wpdb->query( $wpdb->prepare( $sqlStart, $params ) );
        return $result;
    }

    /**
     * Selects the campaign from the group
     * @param type $group_id
     */
    public static function get_group_campaign( $group_id ) {
        static $groupCampaigns = array();

        if ( isset( $groupCampaigns[ $group_id ] ) ) {
            return $groupCampaigns[ $group_id ];
        }

        $group          = self::get_group( $group_id );
        $campaigns      = self::get_group_campaigns( $group_id, true );
        $countCampaings = count( $campaigns );
        $campaignId     = NULL;

        if ( !$countCampaings ) {
            return $campaignId;
        } else {
            if ( $countCampaings == 1 ) {
                $campaignId = $campaigns[ 0 ]->campaign_id;
            } else {

                /*
                 * 24.08.2016 we need to unset the inactive campaigns here
                 */
                foreach ( $campaigns as $key => $campaign ) {
                    $result = self::get_campaign( $campaign->campaign_id, false );
                    if ( !$result[ 'campaign_active' ] ) {
                        unset( $campaigns[ $key ] );
                    }
                }

                $groupOrder = trim( $group[ 'group_order' ] );
                switch ( $groupOrder ) {
                    /*
                     * Random Campaign
                     */
                    default:
                    case '1': {
                            if ( empty( $campaigns ) ) {
                                return $campaignId;
                            }
                            $campaignWeights = array();
                            foreach ( $campaigns as $key => $campaign ) {
                                $campaignWeights[ $key ] = $campaign->group_priority;
                            }
                            $normalisedWeights = ac_normalize_weights( $campaignWeights );
                            if ( array_sum( $normalisedWeights ) ) {
                                $normalisedWeights = array_filter( $normalisedWeights );
                                $rand              = mt_rand( 1, (int) array_sum( $normalisedWeights ) );
                                foreach ( $normalisedWeights as $key => $value ) {
                                    $rand -= $value;
                                    if ( $rand <= 0 ) {
                                        $campaignId = $campaigns[ $key ]->campaign_id;
                                        break;
                                    }
                                }
                            } else {
                                $result     = array_rand( $campaigns );
                                $campaignId = $campaigns[ $result ]->campaign_id;
                            }
                            break;
                        }
                    /*
                     * Selected Campaign
                     */
                    case '2': {
                            setlocale( LC_ALL, 'en_US' );
                            $currentDay       = date( "D" );
                            $selectedCampaign = '';
                            foreach ( $campaigns as $campaign ) {
                                if ( strpos( $campaign->active_week_days, $currentDay ) ) {
                                    if ( $selectedCampaign != '' ) {
                                        if ( $campaign->group_priority >= $selectedCampaign->group_priority ) {
                                            $selectedCampaign = $campaign;
                                        }
                                    } else {
                                        $selectedCampaign = $campaign;
                                    }
                                }
                            }
                            if ( $selectedCampaign == '' ) {
                                $campaignId = NULL;
                            } else {
                                $campaignId = $selectedCampaign->campaign_id;
                            }
                            break;
                        }
                }
            }
        }

        $groupCampaigns[ $group_id ] = $campaignId;
        return $campaignId;
    }

    /**
     * Gather the informations about the campaigns running on the server once to limit the number of requests
     */
    public static function get_global_campaigns_info( $campaignId, $isGroup ) {
        global $CMAdChangerClientCampaignCache, $CMAdChangerClientGroupCache;

        if ( $CMAdChangerClientCampaignCache === NULL ) {
            $response = self::get_campaigns_info();

            if ( isset( $response[ 'success' ] ) ) {
                $CMAdChangerClientCampaignCache = isset( $response[ 'campaigns' ] ) ? $response[ 'campaigns' ] : array();
                $CMAdChangerClientGroupCache    = isset( $response[ 'groups' ] ) ? $response[ 'groups' ] : array();
            } else {
                $CMAdChangerClientCampaignCache = array();
            }
        }

        if ( empty( $campaignId ) || !is_numeric( $campaignId ) ) {
            AC_Data::_internal_error( 'CampaignID not numeric or equals 0' );
            return FALSE;
        }

        if ( $isGroup ) {
            if ( !empty( $CMAdChangerClientGroupCache ) ) {
                foreach ( $CMAdChangerClientGroupCache as $group ) {
                    if ( $group[ 'group_id' ] == $campaignId ) {
                        return $group[ 'campaign_id' ];
                    }
                }
            }
        } else {
            if ( !empty( $CMAdChangerClientCampaignCache ) ) {
                foreach ( $CMAdChangerClientCampaignCache as $campaign ) {
                    if ( $campaign[ 'campaign_id' ] == $campaignId ) {
                        /*
                         * If campaign found but not active
                         */
                        if ( !$campaign[ 'campaign_active' ] ) {
                            AC_Data::_internal_error( 'Campaign is not active!' );
                            return FALSE;
                        }
                        return $campaign;
                    }
                }
            }
        }

        return FALSE;
    }

    /**
     * Returns the information list about the campaigns to the client
     */
    public static function get_campaigns_info() {
        $campaignsArr = array();
        $groupsArr    = array();
        $result       = array();
        $error        = null;

        $campaigns = self::get_campaigns();

        if ( !empty( $campaigns ) ) {
            foreach ( $campaigns as $campaign ) {
                $campaignsArr[] = self::get_campaign( $campaign->campaign_id );
            }

            $result[ 'campaigns' ] = $campaignsArr;
        }

        $groups = AC_Data::get_groups();
        if ( !empty( $groups ) ) {
            foreach ( $groups as $group ) {
                $groupInfo   = array(
                    'group_id'    => $group->group_id,
                    'campaign_id' => self::get_group_campaign( $group->group_id )
                );
                $groupsArr[] = $groupInfo;
            }
            $result[ 'groups' ] = $groupsArr;
        }

        if ( $error ) {
            $result[ 'error' ]         = '1';
            $result[ 'error_message' ] = $error;
        } else {
            $result[ 'success' ] = '1';
        }

        return $result;
    }

    /**
     * Returns the information list about the campaigns to the client
     */
    public static function get_campaigns_cache_info() {
        global $wpdb;
        $campaignsArr      = array();
        $groupsArr         = array();
        $result            = array();
        $error             = null;
        $groupsReturnArray = array();
        $campaignsIds      = array();
        $outputImages      = array();
        $campaigns         = self::get_campaigns();

        if ( !empty( $campaigns ) ) {
            foreach ( $campaigns as $campaign ) {
                $fullCampaignInfo = self::get_campaign( $campaign->campaign_id );
                $campaignAllowed  = true;
                /**
                 * Check if domain is allowed, if not don't return the campaign
                 */
                if ( !empty( $fullCampaignInfo[ 'category_title' ] ) ) {
                    $campaignAllowed = false;
                    foreach ( $fullCampaignInfo[ 'category_title' ] as $oneCategory ) {
                        if ( $_SERVER[ 'HTTP_REFERER' ] == $oneCategory ) {
                            $campaignAllowed = true;
                        }
                    }
                }
                if ( $campaignAllowed ) {
                    $campaignsArr[ $campaign->campaign_id ] = $fullCampaignInfo;
                    $campaignsIds[]                         = $campaign->campaign_id;
                }
            }
            $result[ 'campaigns' ] = $campaignsArr;
        }
        $groups = AC_Data::get_groups();
        if ( !empty( $groups ) ) {
            foreach ( $groups as $groupKey => $group ) {
                $groupInfo = self::get_group_campaigns( $group->group_id, true );
                if ( !empty( $groupInfo ) ) {
                    $groupsCampaigns = array();
                    foreach ( $groupInfo AS $oneGroup ) {
                        $groupsCampaigns[] = $oneGroup->campaign_id;
                    }
                }
                $groups[ $groupKey ]->campaigns        = $groupsCampaigns;
                $groupsReturnArray[ $group->group_id ] = $groups[ $groupKey ];
            }
            $result[ 'groups' ] = $groupsReturnArray;
        }
        if ( $error ) {
            $result[ 'error' ]         = '1';
            $result[ 'error_message' ] = $error;
        } else {
            $result[ 'success' ] = '1';
        }
        $result[ 'misc' ][ 'upload_url' ]                    = cmac_get_upload_url();
        $result[ 'misc' ][ 'acs_slideshow_effect' ]          = get_option( 'acs_slideshow_effect', 'fade' );
        $result[ 'misc' ][ 'acs_slideshow_interval' ]        = get_option( 'acs_slideshow_interval', '5000' );
        $result[ 'misc' ][ 'acs_slideshow_transition_time' ] = get_option( 'acs_slideshow_transition_time', '400' );
        /*
         * Banner variations settings
         */
        $result[ 'misc' ][ 'acs_use_banner_variations' ]     = get_option( 'acs_use_banner_variations', '1' );
        $result[ 'misc' ][ 'acs_banner_area' ]               = get_option( 'acs_banner_area', 'screen' );
        $result[ 'misc' ][ 'acs_resize_banner' ]             = get_option( 'acs_resize_banner', '1' );

        $how_many = count( $campaignsIds );
        if ( $how_many > 0 ) {
            $placeholders = array_fill( 0, $how_many, '%d' );
            $format       = implode( ', ', $placeholders );
            $query        = "($format)";
            $whereIds     = $wpdb->prepare( $query, $campaignsIds );
            $images       = $wpdb->get_results( 'SELECT * FROM ' . ADS_TABLE . ' WHERE campaign_id IN' . $whereIds, ARRAY_A );
            foreach ( $images as $oneImage ) {
                if ( is_file( cmac_get_upload_dir() . $oneImage[ 'filename' ] ) && is_readable( cmac_get_upload_dir() . $oneImage[ 'filename' ] )
                ) {
                    $imageSize = getimagesize( cmac_get_upload_dir() . $oneImage[ 'filename' ] );
                } else {
                    $imageSize = array( 0 => 0, 0 => 0 );
                }
                $oneImage[ 'image_size' ]                = $imageSize;
                $outputImages[ $oneImage[ 'image_id' ] ] = $oneImage;
            }
            $result[ 'images' ] = $outputImages;
        } else {

        }
        return $result;
    }

    /**
     * Gets all campaigns that has type select and no banner is selected
     * @return Array
     */
    public static function get_select_campaigns_with_no_banner_selected() {
        global $wpdb;
        return $wpdb->get_results( 'SELECT campaign_id, title FROM ' . CAMPAIGNS_TABLE . '
                                                  WHERE banner_display_method = "selected"
                                                  AND selected_banner = 0
                                                  AND campaign_type_id NOT IN (2)' );
    }

    /**
     * Sets and gets the internal errors
     * @staticvar array $_errors
     * @param type $error
     * @param type $return
     * @return type
     */
    public static function _internal_error( $error = null, $return = false ) {
        static $_errors = array();

        if ( is_string( $error ) ) {
            $_errors[] = $error;
        }

        if ( $return ) {
            if ( !empty( $_errors ) ) {
                return implode( '<br>', $_errors );
            } else {
                '';
            }
        }
    }

}
