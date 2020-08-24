<?php

class Advanced_Ads_Pro_Utils {
	/**
	 * generate unique wrapper id
	 *
	 * @return string
	 */
	public static function generate_wrapper_id() {
		static $count = 0;
		$prefix = Advanced_Ads_Plugin::get_instance()->get_frontend_prefix();
		return $prefix . ++$count . mt_rand();
	}
	
	/**
	 * Checks if a blog exists and is not marked as deleted.
	 *
	 * @link   http://wordpress.stackexchange.com/q/138300/73
	 * @param  int $blog_id
	 * @param  int $site_id
	 * @return bool
	 */
	public static function blog_exists( $blog_id, $site_id = 0 ) {
		global $wpdb;
		static $cache = array();

		$site_id = absint( $site_id );

		if ( 0 === $site_id ) {
			$site_id = get_current_site()->id;
		}

		if ( empty ( $cache[ $site_id ] ) ) {

			if ( wp_is_large_network() ) // we do not test large sites.
				return true;

			$query = "SELECT `blog_id` FROM $wpdb->blogs WHERE site_id = $site_id AND deleted = 0";

			$result = $wpdb->get_col( $query );

			// Make sure the array is always filled with something.
			if ( empty ( $result ) )
				$cache[ $site_id ] = array( 'checked' );
			else
				$cache[ $site_id ] = $result;
		}

		return in_array( $blog_id, $cache[ $site_id ] );
	}
}