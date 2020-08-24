<?php

/**
 * CM Ad Changer
 *
 * @author CreativeMinds (http://ad-changer.cminds.com)
 * @copyright Copyright (c) 2013, CreativeMinds
 */
class AC_Requests {

	/**
	 * Accepts API requests (called by "wp_loaded" action)
	 */
	public static function handle_api_requests() {
		if ( isset( $_GET[ 'acs_action' ] ) ) {
			switch ( $_GET[ 'acs_action' ] ) {
				case "get_banner":
					self::get_banner();
					break;
				case "ping_server":
					self::ping_server();
					break;
				case "check_campaign_status":
					self::check_campaign_status();
					break;
				case 'trigger_click_event':
					self::trigger_click_event();
					break;
				case 'trigger_impression_event':
					self::trigger_impression_event();
					break;
				case 'send_notifications':
					ac_send_campaign_notifications();
					break;
				case "get_campaigns_info":
					echo json_encode( AC_Data::get_campaigns_info() );
					break;
				case "get_campaigns_cache_info":
					echo json_encode( AC_Data::get_campaigns_cache_info() );
					break;
				case "check_server_connection":
					echo '<div style="color:green; font-weight: bold; font-size: 20px; padding: 20px;">Connection Successful!</div>';
					break;
				case "get_addesigner":
					self::get_addesigner();
					break;
				default:
					return self::show_error( AC_API_ERROR_8 );
			}
			exit;
		}
	}

	/**
	 * Gets banner
	 * @return Array
	 */
	public static function get_banner( $http_referer = null, $user_ip = null, $webpage_url = null, $campaign_id = null,
									$container_width = null, $groupId = null ) {
		$historyDisabled = get_option( 'acs_disable_history_table', null );
		cmac_log( "AC_Request::get_banner()" );

		if ( !$http_referer && isset( $_SERVER[ 'HTTP_REFERER' ] ) ) {
			$http_referer = $_SERVER[ 'HTTP_REFERER' ];
		}

		if ( !$user_ip && isset( $_GET[ 'user_ip' ] ) ) {
			$user_ip = $_GET[ 'user_ip' ];
		}

		if ( !$webpage_url && isset( $_GET[ 'webpage_url' ] ) ) {
			$webpage_url = $_GET[ 'webpage_url' ];
		}

		if ( !$campaign_id && isset( $_GET[ 'campaign_id' ] ) ) {
			$campaign_id = $_GET[ 'campaign_id' ];
		}

		if ( !$container_width && isset( $_GET[ 'container_width' ] ) ) {
			$container_width = $_GET[ 'container_width' ];
		}

		if ( !isset( $http_referer ) || empty( $http_referer ) ) {
			return self::show_error( AC_API_ERROR_1 );
		}

		if ( get_option( 'acs_active', 1 ) != '1' ) {
			return self::show_error( AC_API_ERROR_9 );
		}

		if ( !isset( $campaign_id ) || empty( $campaign_id ) || !is_numeric( $campaign_id ) ) {
			return self::show_error( AC_API_ERROR_2 );
		}

		if ( isset( $container_width ) && !is_numeric( $container_width ) ) {
			return self::show_error( AC_API_ERROR_14 );
		}

		if ( !$groupId && isset( $_GET[ 'group_id' ] ) ) {
			$groupId = $_GET[ 'group_id' ];
		}

		$campaign = AC_Data::get_campaign( $campaign_id );
		// Checking if the client site belogns to this campaign

        if(null === $groupId && !empty($campaign['group_id'])){
            $groupId = $campaign['group_id'];
        }

		if ( strpos( $http_referer, get_bloginfo( 'wpurl' ) ) !== false || empty( $campaign[ 'categories' ] ) ) { // the request is from server side? Or no domain limitations set?
			cmac_log( 'Request comes from Ad Changer server' );
			$campaign_from_right_category = true;
		} else { // check if request comes from registered domain
			$campaign_from_right_category = false;
			foreach ( $campaign[ 'categories' ] as $category_id ) {
				$category = AC_Data::ac_get_category( $category_id );
				if ( strpos( $http_referer, $category->category_title ) !== false ) {
					$campaign_from_right_category = true;
					break;
				}
			}

			if ( !$campaign_from_right_category ) {
				return self::show_error( AC_API_ERROR_4 );
			}
		}

		if ( (int) $campaign[ 'status' ] == 0 ) {
			return self::show_error( AC_API_ERROR_5 );
		}

		/*
		 * don't count impressions or clicks if history is disabled
		 */
		if ( $historyDisabled != 1 ) {
			/*
			 * count clicks and impressions only when they needed
			 */
			if ( (int) $campaign[ 'max_impressions' ] > 0 ) {
				if ( AC_Data::ac_get_impressions_cnt( $campaign[ 'campaign_id' ] ) >= $campaign[ 'max_impressions' ] ) {
					return self::show_error( AC_API_ERROR_10 );
				}
			}
			if ( (int) $campaign[ 'max_clicks' ] > 0 ) {
				if ( AC_Data::ac_get_clicks_cnt( $campaign[ 'campaign_id' ] ) >= $campaign[ 'max_clicks' ] ) {
					return self::show_error( AC_API_ERROR_11 );
				}
			}
		}

		switch ( $campaign[ 'campaign_type_id' ] ) {
			case '0':
				$campaign	 = AC_Advert::getImageBanner( $campaign, $container_width );
				break;
			case '1':
				$campaign	 = AC_Advert::getHTMLBanner( $campaign );
				break;
			case '2':
				break;
			case '3':
				$campaign	 = AC_Advert::getVideoBanner( $campaign );
				break;
			case '4':
				$campaign	 = AC_Advert::getFloatingBanner( $campaign );
				break;
			case '5':
				$campaign	 = AC_Advert::getFloatingBottomBanner( $campaign );
				break;
			default:
				break;
		}
		$campaign_active = $campaign[ 'campaign_active' ];

		if ( !$campaign_active ) {
			return self::show_error( AC_API_ERROR_13 );
		}

		unset( $campaign[ 'date_from' ], $campaign[ 'hours_from' ], $campaign[ 'mins_from' ], $campaign[ 'date_till' ], $campaign[ 'hours_to' ], $campaign[ 'mins_to' ] );

		switch ( $campaign[ 'campaign_type_id' ] ) {
			case '0':
				$ret_array	 = AC_Advert::prepareImageReturn( $campaign );
				break;
			case '1':
				$ret_array	 = AC_Advert::prepareHTMLReturn( $campaign );
				break;
			case '2':
				$ret_array	 = AC_Advert::prepareAdSenseReturn( $campaign );
				break;
			case '3':
				$ret_array	 = AC_Advert::prepareVideoReturn( $campaign );
				break;
			case '4':
				$ret_array	 = AC_Advert::prepareFloatingReturn( $campaign );
				break;
			case '5':
				$ret_array	 = AC_Advert::prepareFloatingBottomReturn( $campaign );
				break;
			default:
				break;
		}
		$ret_array[ 'campaign_type_id' ] = $campaign[ 'campaign_type_id' ];
		$ret_array[ 'campaign_id' ]		 = $campaign[ 'campaign_id' ];

		/*
		 *  RETURN ARRAY
		 */
		$ret_array[ 'custom_js' ]			 = trim( $campaign[ 'custom_js' ] );
		/*
		 * Open target URL in new window
		 */
		$ret_array[ 'banner_new_window' ]	 = $campaign[ 'banner_new_window' ];
		cmac_log( 'Returning response from AC_Request:get_banner()' );

		if ( isset( $_GET[ 'acs_action' ] ) ) {
			echo json_encode( $ret_array );
		}

		if ( !empty( $campaign[ 'selected_banner_id' ] ) ) {
			$history_selected_banner_id = $campaign[ 'selected_banner_id' ];
			ac_trigger_event( 'new_impression', array( 'campaign_id' => $campaign[ 'campaign_id' ], 'banner_id' => $history_selected_banner_id, 'http_referer' => $http_referer, 'remote_ip' => $user_ip, 'webpage_url' => urldecode( $webpage_url ), 'campaign_type' => $campaign[ 'banner_display_method' ], 'group_id' => $groupId ) );
		} else {

			/*
			 * It's possible that's AdSense ad so there's no 'banners' key
			 */
			if ( isset( $ret_array[ 'banners' ] ) ) {
				$banners = $ret_array[ 'banners' ];

				if ( $banners ) {
					foreach ( $banners as $banner ) {
						$history_selected_banner_id = $banner[ 'id' ];
						ac_trigger_event( 'new_impression', array( 'campaign_id' => $campaign[ 'campaign_id' ], 'banner_id' => $history_selected_banner_id, 'http_referer' => $http_referer, 'remote_ip' => $user_ip, 'webpage_url' => urldecode( $webpage_url ), 'campaign_type' => $campaign[ 'banner_display_method' ], 'group_id' => $groupId ) );
					}
				}
			}
		}

		if ( isset( $_GET[ 'acs_action' ] ) ) {
			exit;
		} else {
			return $ret_array;
		}
	}

	/**
	 * Checks the server status
	 */
	private static function ping_server() {
		if ( get_option( 'acs_active', 1 ) === '1' ) {
			echo json_encode( array( 'success' => '1', 'message' => 'CM Ad Changer Server is ON' ) );
		} else {
			echo json_encode( array( 'error' => '1', 'message' => 'CM Ad Changer Server is OFF' ) );
		}
	}

	/**
	 * Gets the AdDesigner
	 */
	private static function get_addesigner() {
		include ACS_PLUGIN_PATH . '/views/addesigner.phtml';
	}

	/**
	 * Checks the campaign status
	 */
	private static function check_campaign_status() {
		if ( !$campaign_id && isset( $_GET[ 'campaign_id' ] ) ) {
			$campaign_id = $_GET[ 'campaign_id' ];
		}

		if ( !isset( $campaign_id ) || empty( $campaign_id ) || !is_numeric( $campaign_id ) ) {
			return self::show_error( AC_API_ERROR_2 );
		}
		$campaign = AC_Data::get_campaign( $campaign_id );
		if ( $campaign[ 'status' ] ) {
			echo json_encode( array( 'success' => '1', 'message' => 'Campaign with ID: ' . $campaign_id . ' is ON' ) );
		} else {
			echo json_encode( array( 'error' => '1', 'message' => 'Campaign with ID: ' . $campaign_id . ' is OFF' ) );
		}
	}

	/**
	 * Handles trigger click request
	 * @return String
	 */
	public static function trigger_click_event( $http_referer = null, $user_ip = null, $webpage_url = null,
											 $campaign_id = null, $banner_id = null, $group_id = null ) {
		$historyDisabled = get_option( 'acs_disable_history_table', null );
		if ( $historyDisabled == 1 ) {
			exit;
		}
		if ( !$http_referer && isset( $_SERVER[ 'HTTP_REFERER' ] ) ) {
			$http_referer = $_SERVER[ 'HTTP_REFERER' ];
		}

		if ( !$user_ip && isset( $_GET[ 'user_ip' ] ) ) {
			$user_ip = $_GET[ 'user_ip' ];
		}

		if ( !$webpage_url && isset( $_GET[ 'webpage_url' ] ) ) {
			$webpage_url = $_GET[ 'webpage_url' ];
		}

		if ( !$campaign_id && isset( $_GET[ 'campaign_id' ] ) ) {
			$campaign_id = $_GET[ 'campaign_id' ];
		}

		if ( !$banner_id && isset( $_GET[ 'banner_id' ] ) ) {
			$banner_id = $_GET[ 'banner_id' ];
		}

		if ( !$group_id && isset( $_GET[ 'group_id' ] ) ) {
			$group_id = $_GET[ 'group_id' ];
		}

		if ( !isset( $http_referer ) || empty( $http_referer ) ) {
			return self::show_error( AC_API_ERROR_1 );
		}
		if ( get_option( 'acs_active', 1 ) != '1' ) {
			return self::show_error( AC_API_ERROR_9 );
		}
		if ( !isset( $campaign_id ) || empty( $campaign_id ) || !is_numeric( $campaign_id ) ) {
			return self::show_error( AC_API_ERROR_2 );
		}
		if ( !isset( $banner_id ) || empty( $banner_id ) || !is_numeric( $banner_id ) ) {
			return self::show_error( AC_API_ERROR_12 );
		}

		$campaign = AC_Data::get_campaign( $campaign_id );
		if ( (int) $campaign[ 'max_impressions' ] > 0 ) {
			if ( AC_Data::ac_get_impressions_cnt( $campaign[ 'campaign_id' ] ) >= $campaign[ 'max_impressions' ] ) {
				return self::show_error( AC_API_ERROR_10 );
			}
		}
		if ( (int) $campaign[ 'max_clicks' ] > 0 ) {
			if ( AC_Data::ac_get_clicks_cnt( $campaign[ 'campaign_id' ] ) >= $campaign[ 'max_clicks' ] ) {
				return self::show_error( AC_API_ERROR_11 );
			}
		}
		$campaign	 = AC_Data::get_campaign( $campaign_id );
		$res		 = ac_trigger_event( 'new_click', array( 'campaign_id' => $campaign_id, 'banner_id' => $banner_id, 'http_referer' => $http_referer, 'remote_ip' => $user_ip, 'webpage_url' => urldecode( $webpage_url ), 'campaign_type' => $campaign[ 'banner_display_method' ], 'group_id' => $group_id ) );

		echo json_encode( array( 'success' => '1' ) );

		if ( isset( $_GET[ 'acs_action' ] ) ) {
			exit;
		} else {
			return array( 'success' => '1' );
		}
	}

	/**
	 * Handles trigger impression request
	 * @return String
	 */
	public static function trigger_impression_event( $http_referer = null, $user_ip = null, $webpage_url = null,
												  $banner_id = null, $group_id = null ) {
		$historyDisabled = get_option( 'acs_disable_history_table', null );
		if ( $historyDisabled == 1 ) {
			exit;
		}
		if ( !$http_referer && isset( $_SERVER[ 'HTTP_REFERER' ] ) ) {
			$http_referer = $_SERVER[ 'HTTP_REFERER' ];
		}

		if ( !$user_ip && isset( $_GET[ 'user_ip' ] ) ) {
			$user_ip = $_GET[ 'user_ip' ];
		}

		if ( !$webpage_url && isset( $_GET[ 'webpage_url' ] ) ) {
			$webpage_url = $_GET[ 'webpage_url' ];
		}

		if ( !$campaign_id && isset( $_GET[ 'campaign_id' ] ) ) {
			$campaign_id = $_GET[ 'campaign_id' ];
		}

		if ( !$banner_id && isset( $_GET[ 'banner_id' ] ) ) {
			$banner_id = $_GET[ 'banner_id' ];
		}

		if ( !$group_id && isset( $_GET[ 'group_id' ] ) ) {
			$group_id = $_GET[ 'group_id' ];
		}

		if ( !isset( $http_referer ) || empty( $http_referer ) ) {
			return self::show_error( AC_API_ERROR_1 );
		}
		if ( get_option( 'acs_active', 1 ) !== '1' ) {
			return self::show_error( AC_API_ERROR_9 );
		}
		if ( !isset( $campaign_id ) || empty( $campaign_id ) || !is_numeric( $campaign_id ) ) {
			return self::show_error( AC_API_ERROR_2 );
		}
		if ( !isset( $banner_id ) || empty( $banner_id ) || !is_numeric( $banner_id ) ) {
			return self::show_error( AC_API_ERROR_12 );
		}

		$campaign = AC_Data::get_campaign( $campaign_id );
		if ( (int) $campaign[ 'max_impressions' ] > 0 ) {
			if ( AC_Data::ac_get_impressions_cnt( $campaign[ 'campaign_id' ] ) >= $campaign[ 'max_impressions' ] ) {
				return self::show_error( AC_API_ERROR_10 );
			}
		}
		if ( (int) $campaign[ 'max_clicks' ] > 0 ) {
			if ( AC_Data::ac_get_clicks_cnt( $campaign[ 'campaign_id' ] ) >= $campaign[ 'max_clicks' ] ) {
				return self::show_error( AC_API_ERROR_11 );
			}
		}
		$campaign	 = AC_Data::get_campaign( $campaign_id );
		$res		 = ac_trigger_event( 'new_impression', array( 'campaign_id' => $campaign_id, 'banner_id' => $banner_id, 'http_referer' => $http_referer, 'remote_ip' => $user_ip, 'webpage_url' => urldecode( $webpage_url ), 'campaign_type' => $campaign[ 'banner_display_method' ], 'group_id' => $group_id ) );

		echo json_encode( array( 'success' => '1' ) );

		if ( isset( $_GET[ 'acs_action' ] ) ) {
			exit;
		} else {
			return array( 'success' => '1' );
		}
	}

	/**
	 * Performs error response
	 */
	private static function show_error( $error ) {
		if ( isset( $_GET[ 'acs_action' ] ) ) {
			echo json_encode( array( 'error' => $error ) );
			exit;
		} else {
			return array( 'error' => $error );
		}
	}

}
