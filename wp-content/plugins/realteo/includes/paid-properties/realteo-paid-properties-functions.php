<?php



/**
 * Give a user a package
 *
 * @param  int $user_id
 * @param  int $product_id
 * @param  int $order_id
 * @return int|bool false
 */
function realteo_give_user_package( $user_id, $product_id, $order_id = 0 ) {
	global $wpdb;

	$package = wc_get_product( $product_id );

	if ( ! $package->is_type( 'property_package' ) ) {
		return false;
	}

	$is_featured = false;
	$is_featured = $package->is_property_featured();
	

	$id = $wpdb->get_var( 
		$wpdb->prepare( "SELECT id FROM {$wpdb->prefix}realteo_user_packages WHERE
			user_id = %d
			AND product_id = %d
			AND order_id = %d
			AND package_duration = %d
			AND package_limit = %d
			AND package_featured = %d",
			$user_id,
			$product_id,
			$order_id,
			$package->get_duration(),
			$package->get_limit(),
			$is_featured ? 1 : 0
		));
		
	if ( $id ) {
		return $id;
	}

	$wpdb->insert(
		"{$wpdb->prefix}realteo_user_packages",
		array(
			'user_id'          => $user_id,
			'product_id'       => $product_id,
			'order_id'         => $order_id,
			'package_count'    => 0,
			'package_duration' => $package->get_duration(),
			'package_limit'    => $package->get_limit(),
			'package_featured' => $is_featured ? 1 : 0,
		)
	);

	return $wpdb->insert_id;
}




/**
 * See if a package is valid for use
 *
 * @param int $user_id
 * @param int $package_id
 * @return bool
 */
function realteo_package_is_valid( $user_id, $package_id ) {
	global $wpdb;

	$package = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}realteo_user_packages WHERE user_id = %d AND id = %d;", $user_id, $package_id ) );

	if ( ! $package ) {
		return false;
	}

	if ( $package->package_count >= $package->package_limit && $package->package_limit != 0 ) {
		return false;
	}

	return true;
}



/**
 * Increase job count for package
 *
 * @param  int $user_id
 * @param  int $package_id
 * @return int affected rows
 */
function realteo_increase_package_count( $user_id, $package_id ) {
	global $wpdb;

	$packages = realteo_user_packages( $user_id );

	if ( isset( $packages[ $package_id ] ) ) {
		$new_count = $packages[ $package_id ]->package_count + 1;
	} else {
		$new_count = 1;
	}

	return $wpdb->update(
		"{$wpdb->prefix}realteo_user_packages",
		array(
			'package_count' => $new_count,
		),
		array(
			'user_id' => $user_id,
			'id'      => $package_id,
		),
		array( '%d' ),
		array( '%d', '%d' )
	);
}


/**
 * Get a users packages from the DB
 *
 * @param  int          $user_id
 * @param string|array $package_type
 * @return array of objects
 */
function realteo_user_packages( $user_id ) {
	global $wpdb;

	
	$package_type = array( 'property_package' );


	$packages = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}realteo_user_packages WHERE user_id = %d AND ( package_count < package_limit OR package_limit = 0 );", $user_id ), OBJECT_K );

	return $packages;
}

/**
 * Get a package
 *
 * @param  stdClass $package
 * @return realteo__Package
 */
function realteo_get_package( $package ) {
	return new Realteo_Paid_Properties_Package( $package );
}


/**
 * Approve a listing
 *
 * @param  int $listing_id
 * @param  int $user_id
 * @param  int $user_package_id
 * @return void
 */
function realteo_approve_listing_with_package( $listing_id, $user_id, $user_package_id ) {
	if ( realteo_package_is_valid( $user_id, $user_package_id ) ) {
		$resumed_post_status = get_post_meta( $listing_id, '_post_status_before_package_pause', true );
		if ( ! empty( $resumed_post_status ) ) {
			$listing = array(
				'ID'            => $listing_id,
				'post_status'   => $resumed_post_status,
			);
			delete_post_meta( $listing_id, '_post_status_before_package_pause' );
		} else {
			$listing = array(
				'ID'            => $listing_id,
				'post_date'     => current_time( 'mysql' ),
				'post_date_gmt' => current_time( 'mysql', 1 ),
			);

			switch ( get_post_type( $listing_id ) ) {
				case 'property' :
					delete_post_meta( $listing_id, '_expires' );
					$listing[ 'post_status' ] = realteo_get_option( 'realteo_new_property_requires_approval' ) ? 'pending' : 'publish';
					break;
				
			}
		}

		// Do update
		wp_update_post( $listing );
		update_post_meta( $listing_id, '_user_package_id', $user_package_id );

		realteo_increase_package_count( $user_id, $user_package_id );
		
	}
}

/**
 * Get a package
 *
 * @param  int $package_id
 * @return realteo_Package
 */
function realteo_get_user_package( $package_id ) {
	global $wpdb;

	$package = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}realteo_user_packages WHERE id = %d;", $package_id ) );
	return realteo_get_package( $package );
}
