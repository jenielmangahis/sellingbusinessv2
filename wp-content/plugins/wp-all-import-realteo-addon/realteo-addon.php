<?php

/*
Plugin Name: WP All Import Realteo Add-On
Description: An add-on for importing property data to Realteo plugin/Findeo theme
Version: 1.0.1
Author: Purethemes.net
*/


include "rapid-addon.php";

$realteo_addon = new RapidAddon('Realteo Add-On', 'realteo_addon');

$realteo_addon->import_images( '_gallery', 'Gallery Images' );

$realteo_addon->add_field('_price', 'Property Price', 'text');
$realteo_addon->add_field('_price_per', 'Property Price per scale', 'text');


$realteo_addon->add_options( 
        $realteo_addon->add_field( '_address', 'Property Address', 'text' ),
        'Location Details', 
        array(
                $realteo_addon->add_field( '_friendly_address', 'Property Short Address', 'text' ),
                $realteo_addon->add_field( '_geolocation_lat', 'Latitude', 'text' ),
                $realteo_addon->add_field( '_geolocation_long', 'Longitude', 'text' ),
        )
);

$realteo_addon->add_field( '_property_type', 'Property Type','text');
$realteo_addon->add_field( '_offer_type', 'Offer Type', 'text');
$realteo_addon->add_field( '_rental_period', 'Rental Period', 'text');

$realteo_addon->add_field('_listing', 'Listing ID', 'text');
$realteo_addon->add_field('_area', 'Area', 'text');
$realteo_addon->add_field('_rooms', 'Rooms', 'text');
$realteo_addon->add_field('_bedrooms', 'Bedrooms', 'text');
$realteo_addon->add_field('_bathrooms', 'Bathrooms', 'text');
$realteo_addon->add_field('_building_age', 'Building Age', 'text');
$realteo_addon->add_field('_parking', 'Parking', 'text');
$realteo_addon->add_field('_heating', 'Heating', 'text');
$realteo_addon->add_field('_sewer', 'Sewer', 'text');
$realteo_addon->add_field('_water', 'Water', 'text');
$realteo_addon->add_field('_exercise_room', 'Exercise Room', 'text');
$realteo_addon->add_field('_storage_room', 'Storage Room', 'text');
$realteo_addon->add_field('_featured', 'Featured?', 'image');

$realteo_addon->set_import_function('realteo_addon_import');

// admin notice if WPAI and/or Yoast isn't installed

if (function_exists('is_plugin_active')) {

	
	if ( !is_plugin_active( "realteo/realteo.php" )  ) {

		// Specify a custom admin notice.
		$realteo_addon->admin_notice(
			'The WP All Import Realteo Add-On requires Realteo  plugin.'
		);
	}

	
	if ( is_plugin_active( "realteo/realteo.php" )  ) {
		
		$realteo_addon->run( array(
				"post_types" => array( "property" )
		) );
		
	}
}

function realteo_addon_import($post_id, $data, $import_options) {

	global $realteo_addon;

	// all fields except for slider and image fields
    $fields = array(
		'_price',
		'_price_per',
		'_address',
		'_friendly_address',
		'_geolocation_lat',
		'_geolocation_long',
		'_property_type',
		'_offer_type',
		'_rental_period',
		'_listing',
		'_area',
		'_rooms',
		'_bedrooms',
		'_bathrooms',
		'_building_age',
		'_parking',
		'_heating',
		'_sewer',
		'_water',
		'_exercise_room',
		'_storage_room',
		'_floorplans',
		
    );

// update everything in fields arrays
    foreach ( $fields as $field ) {

        if ( $realteo_addon->can_update_meta( $field, $import_options ) ) {

        	if ( strlen( $data[$field] ) == 0 ) {

        		delete_post_meta( $post_id, $field );

        	} else {

                update_post_meta( $post_id, $field, $data[$field] );

            }
        }
    }



	if ($realteo_addon->can_update_image($import_options)) {
		$image_url = wp_get_attachment_url($data['yoast_wpseo_opengraph-image']['attachment_id']);
		update_post_meta($post_id, '_yoast_wpseo_opengraph-image', $image_url);
	}

}


function _gallery( $post_id, $attachment_id, $image_filepath, $import_options ) {

	$current_images = get_post_meta( $post_id, '_gallery', true );

	$images_array = array();

	if ( !empty( $current_images ) ) {

		foreach ( $current_images as $id => $url ) {
			
			$images_array[$id] = $url;

		}

	}

	$image_url = wp_get_attachment_url( $attachment_id );

	$images_array[$attachment_id] = $image_url;

    update_post_meta( $post_id, '_gallery', $images_array );
}