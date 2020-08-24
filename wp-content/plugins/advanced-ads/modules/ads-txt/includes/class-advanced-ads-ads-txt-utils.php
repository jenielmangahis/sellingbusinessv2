<?php
/**
 * User interface for managing the 'ads.txt' file.
 */
class Advanced_Ads_Ads_Txt_Utils {
	private static $location;

	/**
	 * Get file info.
	 *
	 * @return array/WP_Error An array containing 'exists', 'is_third_party'.
	 *                        A WP_Error upon error.
	 */
	public static function get_file_info( $url = null ) {
		$url = $url ? $url : home_url( '/' );

		$response     = wp_remote_get( trailingslashit( $url ) . 'ads.txt', array( 'timeout' => 3 ) );
		$code         = wp_remote_retrieve_response_code( $response );
		$content      = wp_remote_retrieve_body( $response );
		$content_type = wp_remote_retrieve_header( $response, 'content-type' );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$file_exists = ! is_wp_error( $response )
			&& 404 !== $code
			&& ( false !== stripos( $content_type, 'text/plain' ) );
		$header_exists = false !== strpos( $content, Advanced_Ads_Ads_Txt_Public::TOP );

		$r = array(
			'exists'      => $file_exists && $header_exists,
			'is_third_party' => $file_exists && ! $header_exists
		);

		return $r;
	}



	/**
	 * Check if the another 'ads.txt' file should be hosted on the root domain.
	 *
	 * @return bool
	 */
	public static function need_file_on_root_domain( $url = null ) {
		$url = $url ? $url : home_url( '/' );


		$parsed_url = wp_parse_url( $url );
		if ( ! isset( $parsed_url['host'] ) ) {
			return false;
		}

		$host = $parsed_url['host'];

		if ( WP_Http::is_ip_address( $host ) ) {
			return false;
		}

		$host_parts = explode( '.', $host );
		$count      = count( $host_parts );
		if ( $count < 3 ) {
			return false;
		}

		if ( 3 === $count ) {
			// Example: `http://one.{net/org/gov/edu/co}.two`.
			$suffixes = array( 'net', 'org', 'gov', 'edu', 'co'  );
			if ( in_array( $host_parts[ $count - 2 ], $suffixes, true ) ) {
				return false;
			}

			// `http://www.one.two` will only be crawled if `http://one.two` redirects to it.
			// Check if such redirect exists.
			if ( 'www' === $host_parts[0] ) {
				/*
				 * Do not append `/ads.txt` because otherwise the redirect will not happen.
				 */
				$no_www_url = $parsed_url['scheme'] . '://' . trailingslashit( $host_parts[1] . '.' . $host_parts[2] );

				add_action( 'requests-requests.before_redirect', array( __CLASS__, 'collect_locations' ) );
				wp_remote_get( $no_www_url, array( 'timeout' => 5, 'redirection' => 3 ) );
				remove_action( 'requests-requests.before_redirect', array( __CLASS__, 'collect_locations' ) );

				$no_www_url_parsed = wp_parse_url( self::$location );
				if ( isset( $no_www_url_parsed['host'] ) && $no_www_url_parsed['host'] === $host ) {
					return false;
				}
			}
		}

		return true;
	}

	/**
	 * Collect last location.
	 *
	 * @return string $location An URL.
	 */
	public static function collect_locations( $location ) {
		self::$location = $location;
	}

	/**
	 * Check if the site is in a subdirectory, for example 'http://one.two/three'.
	 *
	 * @return bool
	 */
	public static function is_subdir( $url = null ) {
		$url = $url ? $url : home_url( '/' );

		$parsed_url = wp_parse_url( $url );
		if ( ! empty( $parsed_url['path'] ) && '/' !== $parsed_url['path'] ) {
			return true;
		}
		return false;
	}
}
