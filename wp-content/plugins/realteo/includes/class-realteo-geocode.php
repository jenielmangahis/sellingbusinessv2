<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Realteo_Geocode {

	const GOOGLE_MAPS_GEOCODE_API_URL = 'https://maps.googleapis.com/maps/api/geocode/json';
	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since  1.26.0
	 */
	private static $_instance = null;

	/**
	 * Allows for accessing single instance of class. Class should only be constructed once per call.
	 *
	 * @since  1.26.0
	 * @static
	 * @return self Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		
		add_action( 'realteo_update_property_data', array( $this, 'update_location_data' ), 20, 2 );
		add_action( 'save_post_property', array( $this, 'update_admin_location_data' ), 15, 3 );
		add_action( 'update_post_meta', array( $this, 'update_post_meta' ), 10, 4 );
		
	}

	public function update_location_data( $id, $values ) {
		if ( isset( $values['property']['_address'] ) ) {
			$address_data = self::get_location_data( $values['property']['_address'] );
			self::save_location_data( $id, $address_data );
		}
	}

	public function update_admin_location_data($post_ID, $post, $update){
		$address = get_post_meta($post_ID,'_address', true);
		$geolocated = get_post_meta($post_ID,'geolocated', true);
		
		if(!$geolocated && $address ) {
			
			$address_data = self::get_location_data( $address );
			
			self::save_location_data( $post_ID, $address_data );
		}

	}
	/**
	 * Gets Location Data from Google.
	 *
	 * Based on code by Eyal Fitoussi.
	 *
	 * @param string $raw_address
	 * @return array|bool location data
	 */
	public static function get_location_data( $raw_address ) {
		$invalid_chars = array( " " => "+", "," => "", "?" => "", "&" => "", "=" => "" , "#" => "" );
		$raw_address   = trim( strtolower( str_replace( array_keys( $invalid_chars ), array_values( $invalid_chars ), $raw_address ) ) );

		if ( empty( $raw_address ) ) {
			return false;
		}
		$raw_address = preg_replace('/[^(\x20-\x7F)]*/','', $raw_address);
		$geocode_api_url =  self::GOOGLE_MAPS_GEOCODE_API_URL;
		$api_key = realteo_get_option( 'realteo_maps_api_server' );

		$geocode_api_url = add_query_arg( 'key', urlencode( $api_key ), $geocode_api_url );
		$geocode_api_url = add_query_arg( 'address', urlencode( $raw_address ), $geocode_api_url );

		$locale = get_locale();
		if ( $locale ) {
			$geocode_api_url = add_query_arg( 'language',  substr( $locale, 0, 2 ), $geocode_api_url );
		}


		if ( false === $geocode_api_url ) {
			return false;
		}

		try {
			
				$result = wp_remote_get(
					$geocode_api_url,
					array(
						'timeout'     => 5,
						'redirection' => 1,
						'httpversion' => '1.1',
						'user-agent'  => 'WordPress/Realteo; ' . get_bloginfo( 'url' ),
						'sslverify'   => false
					)
				);
				$result           = wp_remote_retrieve_body( $result );
				
				$geocoded_address = json_decode( $result );

				if ( $geocoded_address->status ) {
					switch ( $geocoded_address->status ) {
						case 'ZERO_RESULTS' :
							throw new Exception( __( "No results found", 'wp-job-manager' ) );
						break;
						case 'OVER_QUERY_LIMIT' :
							throw new Exception( __( "Query limit reached", 'wp-job-manager' ) );
						break;
						case 'OK' :
							if ( ! empty( $geocoded_address->results[0] ) ) {
								//set_transient( $transient_name, $geocoded_address, DAY_IN_SECONDS * 7 );
							} else {
								throw new Exception( __( "Geocoding error", 'wp-job-manager' ) );
							}
						break;
						default :
							throw new Exception( __( "Geocoding error", 'wp-job-manager' ) );
						break;
					}
				} else {
					throw new Exception( __( "Geocoding error", 'wp-job-manager' ) );
				}
			
		} catch ( Exception $e ) {
			return new WP_Error( 'error', $e->getMessage() );
		}

		$address                      = array();
		
		if ( ! empty( $geocoded_address->results[0]->address_components ) ) {
			$address_data             		= $geocoded_address->results[0]->address_components;
			$address['point_of_interest'] 	= false;
			$address['route']        		= false; //street
			$address['street_number'] 		= false;
			$address['postal_code']      	= false;
			$address['locality']          	= false;
			$address['city']          		= false;
			$address['state_short']   		= false;
			$address['state_long']    		= false;
			
			$address['country_short'] = false;
			$address['country']  = false;

			foreach ( $address_data as $data ) {
				switch ( $data->types[0] ) {
					case 'street_number' :
						$address['street_number'] = sanitize_text_field( $data->long_name );
					break;
					case 'route' :
						$address['street']        = sanitize_text_field( $data->long_name );
					break;
					case 'sublocality_level_1' :
					case 'locality' :
					case 'postal_town' :
						$address['city']          = sanitize_text_field( $data->long_name );
					break;
					case 'administrative_area_level_1' :
					case 'administrative_area_level_2' :
						$address['state_short']   = sanitize_text_field( $data->short_name );
						$address['state_long']    = sanitize_text_field( $data->long_name );
					break;
					case 'postal_code' :
						$address['postal_code']      = sanitize_text_field( $data->long_name );
					break;
					case 'country' :
						$address['country_short'] = sanitize_text_field( $data->short_name );
						$address['country']  = sanitize_text_field( $data->long_name );
					break;
				}
			}
		}

		return $address;
	}


	/**
	 * Saves any returned data to post meta.
	 *
	 * @param  int   $job_id
	 * @param  array $address_data
	 */
	public static function save_location_data( $post_ID, $address_data ) {

		if ( ! is_wp_error( $address_data ) && $address_data ) {
			$terms = array();
			foreach ( $address_data as $key => $value ) {
				if ( $value ) {
					update_post_meta( $post_ID,  $key, $value );					
				}
				$add_region = realteo_get_option('realteo_auto_region');

				if($add_region == 'on' && $key == 'state_long') {

					$term = term_exists( $value, 'region' );
					
					if ( $term !== 0 && $term !== null ) { //exists
						$terms[] = (int)$term['term_id'];
					    wp_set_object_terms( $post_ID, $terms, 'region', true );
					
					} else {
					
						$tax_insert_id = wp_insert_term($value,'region' );
						$terms[] = $tax_insert_id['term_id'];
						wp_set_object_terms( $post_ID, $terms, 'region', true );

					}

				}

			}
			
			//update_post_meta( $post_ID, 'geolocated', 1 );
		}
	}

		/**
	 * Triggered when updating meta on a job listing.
	 *
	 * @param int    $meta_id
	 * @param int    $object_id
	 * @param string $meta_key
	 * @param mixed  $meta_value
	 */
	public function update_post_meta( $meta_id, $object_id, $meta_key, $meta_value ) {
		if ( 'property' === get_post_type( $object_id ) ) {
			if($meta_key == '_address') {
				$address_data = self::get_location_data( $meta_value );
				self::clear_location_data( $object_id );
				self::save_location_data( $object_id, $address_data );
			}
			
		}
	}

		public static function clear_location_data( $id ) {
		delete_post_meta( $id, 'geolocated' );
		delete_post_meta( $id, 'city' );
		delete_post_meta( $id, 'country_long' );
		delete_post_meta( $id, 'country' );
		delete_post_meta( $id, 'state_long' );
		delete_post_meta( $id, 'state_short' );
		delete_post_meta( $id, 'street' );
		delete_post_meta( $id, 'street_number' );
		delete_post_meta( $id, 'zipcode' );
		delete_post_meta( $id, 'postal_code' );
	}


}