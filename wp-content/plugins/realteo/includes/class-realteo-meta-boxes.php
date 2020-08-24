<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * WPSight_Meta_Boxes class
 */
class Realteo_Meta_Boxes {
	/**
	 * Constructor
	 */
	public function __construct() {

		// Add custom meta boxes
		add_action( 'cmb2_admin_init', array( $this, 'add_meta_boxes' ) );
		
	}

	public function add_meta_boxes( ) {

		$prefix = '_realteo_';

		$tabs_box_options = array(
				'id'           => 'realteo_metaboxes',
				'title'        => __( 'Property fields', 'realteo' ),
				'object_types' => array( 'property' ),
				'show_names'   => true,
			);

		// Setup meta box
		$cmb_tabs = new_cmb2_box( $tabs_box_options );

		// setting tabs
		$tabs_setting  = array(
			'config' => $tabs_box_options,
			'layout' => 'vertical', // Default : horizontal
			'tabs'   => array()
		);
		
		$tabs_setting['tabs'] = array(
			 $this->meta_boxes_price(),
			 $this->meta_boxes_main_details(),
			 $this->meta_boxes_location(),
			 $this->meta_boxes_details(),
			 $this->meta_boxes_video(),
		);

		// set tabs
		$cmb_tabs->add_field( array(
			'id'   => '_tabs',
			'type' => 'tabs',
			'tabs' => $tabs_setting
		) );
  

  // GALLERY META BOX
		$property_admin_options = array(
				'id'           => 'realteo_property_admin_metaboxes',
				'title'        => __( 'Property admin data', 'realteo' ),
				'object_types' => array( 'property' ),
				'show_names'   => true,

		);
		$cmb_property_admin = new_cmb2_box( $property_admin_options );

		$cmb_property_admin->add_field( array(
			'name' => __( 'Expiration date', 'realteo' ),
			'desc' => '',
			'id'   => '_property_expires',
			'type' => 'text_date_timestamp',
			
		) );

		// GALLERY META BOX
		$gallery_options = array(
				'id'           => 'realteo_gallery_metaboxes',
				'title'        => __( 'Property gallery', 'realteo' ),
				'object_types' => array( 'property' ),
				'show_names'   => true,

		);

// Setup meta box
		$cmb_gallery = new_cmb2_box( $gallery_options );
  		$cmb_gallery->add_field( array(
			'name' => __( 'Property gallery', 'realteo' ),
			'desc' => '',
			'id'   => '_gallery',
			'type' => 'file_list',
			// 'preview_size' => array( 100, 100 ), // Default: array( 50, 50 )
		    'query_args' => array( 'type' => 'image' ), // Only images attachment
			// Optional, override default text strings
			'text' => array(
				'add_upload_files_text' => 'Add or Upload Images', // default: ""
			),
		) );		

  		$cmb_gallery->add_field( array(
			'name' => __( 'Gallery display layout', 'realteo' ),
			'desc' => '',
			'id'   => '_layout',
			'type' => 'select',
			'options'   => array(
				'style-1' => __( 'Style 1', 'realteo' ),
    			'style-2' => __( 'Style 2 (with contact form)', 'realteo' ),
    			'style-3' => __( 'Style 3', 'realteo' ),
			),
			'default' => realteo_get_option('default_gallery')
		) );

		
		// EOT GALLERY META BOX


		// FEATURED META BOX
		$featured_box_options = array(
				'id'           => 'realteo_featured_metabox',
				'title'        => __( 'Featured Property', 'realteo' ),
				'context'	   => 'side',
				'priority'     => 'core', 
				'object_types' => array( 'property' ),
				'show_names'   => false,

		);

		// Setup meta box
		$cmb_featured = new_cmb2_box( $featured_box_options );

		$cmb_featured->add_field( array(
			'name' => __( 'Featured', 'realteo' ),
			'id'   => '_featured',
			'type' => 'checkbox',
			'desc' => __( 'Tick the checkbox to make it Featured', 'realteo' ),
		));
		// EOT FEATURED METABOX



		// FLOORPLANS METABOX
		$cmb_floorplans = new_cmb2_box( array(
            'id'            => '_floorplans_metabox',
            'title'         => __( 'Floorplans', 'realteo' ),
            'object_types' => array( 'property' ), // post type
            'context'       => 'normal',
            'priority'      => 'core',
            'show_names'    => true,
        ) );


        // Repeatable group
        $floorplans_group = $cmb_floorplans->add_field( array(
            'id'          => '_floorplans',
            'type'        => 'group',
            'options'     => array(
                'group_title'   => __( 'Floorplan', 'realteo' ) . ' {#}', // {#} gets replaced by row number
                'add_button'    => __( 'Add another Editor', 'realteo' ),
                'remove_button' => __( 'Remove Editor', 'realteo' ),
                'sortable'      => true, // beta
            ),
        ) );

        //* Title
        $cmb_floorplans->add_group_field( $floorplans_group, array(
            'name'    => __( 'Title', 'realteo' ),
            'id'      => 'floorplan_title',
            'type'    => 'text',
        ) );    

        $cmb_floorplans->add_group_field( $floorplans_group, array(
            'name'    => __( 'Area', 'realteo' ),
            'id'      => 'floorplan_area',
            'type'    => 'text',
        ) );
        
        //* Textarea
        $cmb_floorplans->add_group_field( $floorplans_group, array(
            'name'    => __( 'Short description', 'realteo' ),
            'id'      => 'floorplan_desc',
            'type'    => 'textarea',
            'options' => array( 'textarea_rows' => 4, ),
        ) );

       	$cmb_floorplans->add_group_field( $floorplans_group, array(
			'name' => 'Image',
			'id'   => 'floorplan_image',
			'type' => 'file',
        	'query_args' => array( 'type' => 'image' )
		) );
		// EOT FLOORPLANS METABOX

       	// LOCATION DETAILS META BOX
		$location_details_options = array(
				'id'           => 'realteo_location_details_metaboxes',
				'title'        => __( 'Location details', 'realteo' ),
				'object_types' => array( 'property' ),
				'show_names'   => true,

		);
		

		// EOT LOCATION DETAILS META BOX
		
	}


	public static function meta_boxes_price() {
		
		$fields = array(
			'id'     => 'price_tab',
			'title'  => __( 'Price', 'realteo' ),
			'fields' => array(
				'price' => array(
					'name' => __( 'Price', 'realteo' ),
					'id'   => '_price',
					'type' => 'text',
					'desc'      => __( 'No currency symbols or thousands separators', 'realteo' ),
					
				),
				'price_per' => array(
					'name' => __( 'Price per  sq meter/sq ft', 'realteo' ),
					'id'   => '_price_per',
					'type' => 'text',
					'desc'      => __( 'If empty it will be auto calculated based on the area of property', 'realteo' ),
				),
				'offer_type' => array(
					'name'      => __( 'Offer Type', 'realteo' ),
					'id'        => '_offer_type',
					'type'      => 'select',
					'default'   => 'sale',
					'options_source' => 'predefined',
					'options_cb' => 'realteo_get_offer_types_flat',
				),				
				'property_type' => array(
					'name'      => __( 'Property Type', 'realteo' ),
					'id'        => '_property_type',
					'type'      => 'select',
					'default'   => 'apartments',
					'options_source' => 'predefined',
					'options_cb' => 'realteo_get_property_types',
				),
				'rental_period' => array(
					'name'      => __( 'Rental Period', 'realteo' ),
					'id'        => '_rental_period',
					'type'      => 'select',
					'default'   => 'weekly',
					'options_source' => 'predefined',
					'options_cb' => 'realteo_get_rental_period',
				),	
			
			)
		);
		$fields = apply_filters( 'realteo_price_fields', $fields );
		
		// Set meta box
		return $fields;
	}

	public static function meta_boxes_video() {
		
		$fields = array(
			'id'     => 'video_tab',
			'title'  => __( 'Video', 'realteo' ),
			'fields' => array(
				'video' => array(
					'name' => __( 'Video', 'realteo' ),
					'id'   => '_video',
					'type' => 'text',
					'desc'      => __( 'URL to oEmbed supported service','realteo' ),
				),
			
			)
		);
		$fields = apply_filters( 'realteo_video_fields', $fields );
		
		// Set meta box
		return $fields;
	}



	public static function meta_boxes_main_details() {
		
		$fields = array(
			'id'     => 'main_details_tab',
			'title'  => __( 'Main Details', 'realteo' ),
			'fields' => array(
				array(
					'name' 	=> __( 'Listing ID', 'realteo' ),
					'id'   	=> '_listing',
					'type' 	=> 'text',
					'invert' => false
				),				
				array(
					'name' 	=> __( 'Area', 'realteo' ),
					'id'   	=> '_area',
					'type' 	=> 'text',
					'invert' => true
				),					
				array(
					'name' 	=> __( 'Rooms', 'realteo' ),
					'id'   	=> '_rooms',
					'type' 	=> 'text',
					'invert' => true
				),				
				array(
					'name' 	=> __( 'Bedrooms', 'realteo' ),
					'id'   	=> '_bedrooms',
					'type' 	=> 'text',
					'invert' => false
				),				
				array(
					'name' 	=> __( 'Bathrooms', 'realteo' ),
					'id'   	=> '_bathrooms',
					'type' 	=> 'text',
					'invert' => false
				),
			)
		);

		// Set meta box
		return apply_filters( 'realteo_main_details_fields', $fields );
	}

	public static function meta_boxes_details() {
		
		$fields = array(
			'id'     => 'details_tab',
			'title'  => __( 'Details', 'realteo' ),
			'fields' => array(
				array(
					'name' 	=> __( 'Building Age', 'realteo' ),
					'id'   	=> '_building_age',
					'type' 	=> 'text',
					'invert' => false
				),				
				array(
					'name' 	=> __( 'Parking', 'realteo' ),
					'id'   	=> '_parking',
					'type' 	=> 'text',
					'invert' => false
				),					
				array(
					'name' 	=> __( 'Cooling', 'realteo' ),
					'id'   	=> '_cooling',
					'type' 	=> 'text',
					'invert' => true
				),				
				array(
					'name' 	=> __( 'Heating', 'realteo' ),
					'id'   	=> '_heating',
					'type' 	=> 'text',
					'invert' => false
				),				
				array(
					'name' 	=> __( 'Sewer', 'realteo' ),
					'id'   	=> '_sewer',
					'type' 	=> 'text',
					'invert' => false
				),				
				array(
					'name' 	=> __( 'Water', 'realteo' ),
					'id'   	=> '_water',
					'type' 	=> 'text',
					'invert' => false
				),				
				array(
					'name' 	=> __( 'Exercise Room', 'realteo' ),
					'id'   	=> '_exercise_room',
					'type' 	=> 'text',
					'invert' => false
				),				
				array(
					'name' 	=> __( 'Storage Room', 'realteo' ),
					'id'   	=> '_storage_room',
					'type' 	=> 'text',
					'invert' => false
				),
			)
		);

		// Set meta box
		return apply_filters( 'realteo_details_fields', $fields );
	}

	public static function meta_boxes_location() {
		
		$fields = array(
			'id'     => 'locations_tab',
			'title'  => __( 'Location', 'realteo' ),
			'fields' => array(
				array(
					'name' => __( 'Address', 'realteo' ),
					'id'   => '_friendly_address',
					'type' => 'text',
					'desc' => 'Human readable address'
				),			
				array(
					'name' => __( 'Google Maps Address', 'realteo' ),
					'id'   => '_address',
					'type' => 'text',
					'desc' => 'Used for geolocation and links'
				),				
				array(
					'name' => __( 'Latitude', 'realteo' ),
					'id'   => '_geolocation_lat',
					'type' => 'text',
				),				
				array(
					'name' => __( 'Longitude', 'realteo' ),
					'id'   => '_geolocation_long',
					'type' => 'text',
				),
			)
		);

		// Set meta box
		return apply_filters( 'realteo_location_fields', $fields );
	}


	public static function meta_boxes_user_agent(){
		$fields = array(
				'agent_title' => array(
					'id'                =>  'agent_title',
					'name'              => __( 'Agent Title', 'realteo' ),
					'label'             => __( 'Agent Title', 'realteo' ),
					'type'              => 'text',
					
				),
				'phone' => array(
					'id'                => 'phone',
					'name'              => __( 'Phone', 'realteo' ),
					'label'             => __( 'Phone', 'realteo' ),
					'type'              => 'text',
					
				),
				'header_social' => array(
					'label'       => __( 'Social', 'realteo' ),
					'type'        => 'header',
					'id'          => 'header_social',
					'name'        => __( 'Social', 'realteo' ),
				),
				'twitter' => array(
					'id'                => 'twitter',
					'name'              => __( '<i class="fa fa-twitter"></i> Twitter', 'realteo' ),
					'label'             => __( '<i class="fa fa-twitter"></i> Twitter', 'realteo' ),
					'type'              => 'text',
				),
				'facebook' => array(
					'id'                => 'facebook',
					'name'              => __( '<i class="fa fa-facebook-square"></i> Facebook', 'realteo' ),
					'label'             => __( '<i class="fa fa-facebook-square"></i> Facebook', 'realteo' ),
					'type'              => 'text',
				),				
				'gplus' => array(
					'id'                => 'gplus',
					'name'              => __( '<i class="fa fa-google-plus"></i> Google+', 'realteo' ),
					'label'             => __( '<i class="fa fa-google-plus"></i> Google+', 'realteo' ),
					'type'              => 'text',

				),
				'linkedin' => array(
					'id'                => 'linkedin',
					'name'              => __( '<i class="fa fa-linkedin"></i> Linkedin', 'realteo' ),
					'label'             => __( '<i class="fa fa-linkedin"></i> Linkedin', 'realteo' ),
					'type'              => 'text',
					
				),
			);
		$fields = apply_filters( 'realteo_user_agent_fields', $fields );
		
		// Set meta box
		return $fields;
	}

	public static function meta_boxes_user_buyer(){
		$fields = array(
		
				'phone' => array(
					'id'                => 'phone',
					'name'              => __( 'Phone', 'realteo' ),
					'label'             => __( 'Phone', 'realteo' ),
					'type'              => 'text',
					
				),
				'header_social' => array(
					'label'       => __( 'Social', 'realteo' ),
					'type'        => 'header',
					'id'          => 'header_social',
					'name'        => __( 'Social', 'realteo' ),
				),
				'twitter' => array(
					'id'                => 'twitter',
					'name'              => __( '<i class="fa fa-twitter"></i> Twitter', 'realteo' ),
					'label'             => __( '<i class="fa fa-twitter"></i> Twitter', 'realteo' ),
					'type'              => 'text',
				),
				'facebook' => array(
					'id'                => 'facebook',
					'name'              => __( '<i class="fa fa-facebook-square"></i> Facebook', 'realteo' ),
					'label'             => __( '<i class="fa fa-facebook-square"></i> Facebook', 'realteo' ),
					'type'              => 'text',
				),				
				'gplus' => array(
					'id'                => 'gplus',
					'name'              => __( '<i class="fa fa-google-plus"></i> Google+', 'realteo' ),
					'label'             => __( '<i class="fa fa-google-plus"></i> Google+', 'realteo' ),
					'type'              => 'text',

				),
				'linkedin' => array(
					'id'                => 'linkedin',
					'name'              => __( '<i class="fa fa-linkedin"></i> Linkedin', 'realteo' ),
					'label'             => __( '<i class="fa fa-linkedin"></i> Linkedin', 'realteo' ),
					'type'              => 'text',
					
				),
			);
		$fields = apply_filters( 'realteo_user_buyer_fields', $fields );
		
		// Set meta box
		return $fields;
	}

	public static function meta_boxes_user_owner(){
		$fields = array(
		
				'phone' => array(
					'id'                => 'phone',
					'name'              => __( 'Phone', 'realteo' ),
					'label'             => __( 'Phone', 'realteo' ),
					'type'              => 'text',
					
				),
				'header_social' => array(
					'label'       => __( 'Social', 'realteo' ),
					'type'        => 'header',
					'id'          => 'header_social',
					'name'        => __( 'Social', 'realteo' ),
				),
				'twitter' => array(
					'id'                => 'twitter',
					'name'              => __( '<i class="fa fa-twitter"></i> Twitter', 'realteo' ),
					'label'             => __( '<i class="fa fa-twitter"></i> Twitter', 'realteo' ),
					'type'              => 'text',
				),
				'facebook' => array(
					'id'                => 'facebook',
					'name'              => __( '<i class="fa fa-facebook-square"></i> Facebook', 'realteo' ),
					'label'             => __( '<i class="fa fa-facebook-square"></i> Facebook', 'realteo' ),
					'type'              => 'text',
				),				
				'gplus' => array(
					'id'                => 'gplus',
					'name'              => __( '<i class="fa fa-google-plus"></i> Google+', 'realteo' ),
					'label'             => __( '<i class="fa fa-google-plus"></i> Google+', 'realteo' ),
					'type'              => 'text',

				),
				'linkedin' => array(
					'id'                => 'linkedin',
					'name'              => __( '<i class="fa fa-linkedin"></i> Linkedin', 'realteo' ),
					'label'             => __( '<i class="fa fa-linkedin"></i> Linkedin', 'realteo' ),
					'type'              => 'text',
					
				),
			);
		$fields = apply_filters( 'realteo_user_owner_fields', $fields );
		
		// Set meta box
		return $fields;
	}


	

}
