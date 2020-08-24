<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
//include_once( 'abstracts/abstract-realteo-form.php' );

class Realteo_Submit  {

	/**
	 * Form name.
	 *
	 * @var string
	 */
	public $form_name = 'submit-property';

	/**
	 * Property ID.
	 *
	 * @access protected
	 * @var int
	 */
	protected $property_id;


	/**
	 * Form fields.
	 *
	 * @access protected
	 * @var array
	 */
	protected $fields = array();


	/**
	 * Form errors.
	 *
	 * @access protected
	 * @var array
	 */
	protected $errors = array();

	/**
	 * Form steps.
	 *
	 * @access protected
	 * @var array
	 */
	protected $steps = array();

	/**
	 * Current form step.
	 *
	 * @access protected
	 * @var int
	 */
	protected $step = 0;


	/**
	 * Form action.
	 *
	 * @access protected
	 * @var string
	 */
	protected $action = '';

	/**
	 * Form form_action.
	 *
	 * @access protected
	 * @var string
	 */
	protected $form_action = '';

	private static $package_id      = 0;
	private static $is_user_package = false;

	/**
	 * Stores static instance of class.
	 *
	 * @access protected
	 * @var Realteo_Submit The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * Returns static instance of class.
	 *
	 * @return self
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}


	/**
	 * Constructor
	 */
	public function __construct() {

		add_shortcode( 'realteo_submit_property', array( $this, 'get_form' ) );
		//add_filter( 'ajax_query_attachments_args', array( $this, 'filter_media' ) );
	
		//add_filter( 'the_title', array( $this, 'change_page_title' ), 10, 2 );
		add_filter( 'submit_property_steps', array( $this, 'enable_paid_listings' ), 30 );

		add_action( 'wp', array( $this, 'process' ) );

		$this->steps  = (array) apply_filters( 'submit_property_steps', array(

			'submit' => array(
				'name'     => __( 'Submit Details', 'realteo' ),
				'view'     => array( $this, 'submit' ),
				'handler'  => array( $this, 'submit_handler' ),
				'priority' => 10
				),
			'preview' => array(
				'name'     => __( 'Preview', 'realteo' ),
				'view'     => array( $this, 'preview' ),
				'handler'  => array( $this, 'preview_handler' ),
				'priority' => 20
			),
			'done' => array(
				'name'     => __( 'Done', 'realteo' ),
				'view'     => array( $this, 'done' ),
				'priority' => 30
			)
		) );
		if(realteo_get_option_with_name('realteo_property_submit_option', 'realteo_new_property_preview' )) {
			unset($this->steps['preview']);
		}
	
		uasort( $this->steps, array( $this, 'sort_by_priority' ) );


		if ( ! empty( $_POST['package'] ) ) {
			if ( is_numeric( $_POST['package'] ) ) {
	
				self::$package_id      = absint( $_POST['package'] );
				self::$is_user_package = false;
			} else {
			
				self::$package_id      = absint( substr( $_POST['package'], 5 ) );
				self::$is_user_package = true;
			}
		} elseif ( ! empty( $_COOKIE['chosen_package_id'] ) ) {
			self::$package_id      = absint( $_COOKIE['chosen_package_id'] );
			self::$is_user_package = absint( $_COOKIE['chosen_package_is_user_package'] ) === 1;
		}

		// Get step/property
		if ( isset( $_POST['step'] ) ) {
			$this->step = is_numeric( $_POST['step'] ) ? max( absint( $_POST['step'] ), 0 ) : array_search( $_POST['step'], array_keys( $this->steps ) );
		} elseif ( ! empty( $_GET['step'] ) ) {
			$this->step = is_numeric( $_GET['step'] ) ? max( absint( $_GET['step'] ), 0 ) : array_search( $_GET['step'], array_keys( $this->steps ) );
		}

		$this->property_id = ! empty( $_REQUEST[ 'property_id' ] ) ? absint( $_REQUEST[ 'property_id' ] ) : 0;
		
		 if(isset($_GET["action"]) && $_GET["action"] == 'edit' ) {
		 	$this->form_action = "editing";
		 	$this->property_id = ! empty( $_GET[ 'property_id' ] ) ? absint( $_GET[ 'property_id' ] ) : 0;
		 } 

		 if(isset($_GET["action"]) && $_GET["action"] == 'renew' ) {
		 	$this->form_action = "renew";
		 	$this->property_id = ! empty( $_GET[ 'property_id' ] ) ? absint( $_GET[ 'property_id' ] ) : 0;
		 }



		$this->property_edit = false;
		if ( ! isset( $_GET[ 'new' ] ) && ( ! $this->property_id ) && ! empty( $_COOKIE['realteo-submitting-property-id'] ) && ! empty( $_COOKIE['realteo-submitting-property-key'] ) ) {
			$property_id     = absint( $_COOKIE['realteo-submitting-property-id'] );
			$property_status = get_post_status( $property_id );

			if ( ( 'preview' === $property_status || 'pending_payment' === $property_status ) && get_post_meta( $property_id, '_submitting_key', true ) === $_COOKIE['realteo-submitting-property-key'] ) {
				$this->property_id = $property_id;
				$this->property_edit = get_post_meta( $property_id, '_submitting_key', true );
				
			}
		}
		
		// We should make sure new jobs are pending payment and not published or pending.
		add_filter( 'submit_property_post_status', array( $this, 'submit_property_post_status' ), 10, 2 );

	}


	/**
	 * Processes the form result and can also change view if step is complete.
	 */
	public function process() {

		// reset cookie
		if (
			isset( $_GET[ 'new' ] ) &&
			isset( $_COOKIE[ 'realteo-submitting-property-id' ] ) &&
			isset( $_COOKIE[ 'realteo-submitting-property-key' ] ) &&
			get_post_meta( $_COOKIE[ 'realteo-submitting-property-id' ], '_submitting_key', true ) == $_COOKIE['realteo-submitting-property-key']
		) {
			delete_post_meta( $_COOKIE[ 'realteo-submitting-property-id' ], '_submitting_key' );
			setcookie( 'realteo-submitting-property-id', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, false );
			setcookie( 'realteo-submitting-property-key', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, false );

			wp_redirect( remove_query_arg( array( 'new', 'key' ), $_SERVER[ 'REQUEST_URI' ] ) );

		}

		$step_key = $this->get_step_key( $this->step );

		if(isset( $_POST[ 'realteo_form' ] )) {
			if ( $step_key && is_callable( $this->steps[ $step_key ]['handler'] ) ) {
				call_user_func( $this->steps[ $step_key ]['handler'] );
			}
		}
		$next_step_key = $this->get_step_key( $this->step );

		// if the step changed, but the next step has no 'view', call the next handler in sequence.
		if ( $next_step_key && $step_key !== $next_step_key && ! is_callable( $this->steps[ $next_step_key ]['view'] ) ) {
			$this->process();
		}
	}

	/**
	 * Initializes the fields used in the form.
	 */
	public function init_fields() {

		if ( $this->fields ) {
			return;
		}
		$scale = realteo_get_option( 'scale', 'sq ft' );
		$this->fields = apply_filters( 'submit_property_form_fields', array(
			'property' => array(
				'header1' => array(
					'label'       => __( 'Basic Information', 'realteo' ),
					'type'        => 'header',
				),
				'property_title' => array(
					'label'       => __( 'Property Title', 'realteo' ),
					'type'        => 'text',
					'name'       => 'property_title',
					'tooltip'	  => __( 'Type title that will also contains an unique feature of your property (e.g. renovated, air contidioned)', 'realteo' ),
					'required'    => true,
					'placeholder' => '',
					'class'		  => '',
					'priority'    => 1
				),
				'_offer_type' => array(
					'label'       	 => __( 'Offer', 'realteo' ),
					'name'       	 => '_offer_type',
					'type'       	 => 'select',
					'options_source' => 'predefined',
					'options_cb'     =>  'realteo_get_offer_types_flat', //realteo_get_offer_types_flat(false),
					'class'		  	 => 'chosen-select-no-single',
					'default'    	 => 'sale',
					'before_row' 	 => '<div class="row with-forms">',
					'render_row_col' => '4',
					'priority'    	 => 2,
					'required'    	 => false,
				),						
				'_rental_period' => array(
					'label'       	 => __( 'Rental Period', 'realteo' ),
					'type'       	 => 'select',
					'name'       	 => '_rental_period',
					'options_source' => 'predefined',
					'options_cb'     => 'realteo_get_rental_period', //realteo_get_rental_period(),
					'class'		  	 => 'chosen-select-no-single',
					'default'    	 => 'monthly',
					'render_row_col' => '4',
					'priority'    	 => 3,
					'required'    	 => false,
				),				
				'_property_type' => array(
					'label'       	 => __( 'Property Type', 'realteo' ),
					'type'       	 => 'select',
					'name'       	 => '_property_type',
					'options_source' => 'predefined',
					'options_cb'   	 => 'realteo_get_property_types', //realteo_get_property_types(),
					'class'		  	 => 'chosen-select-no-single',
					'default'    	 => 'house',
					'after_row' 	 => '</div>',
					'render_row_col' => '4',
					'priority'    	 => 3,
					'required'    => false,
				),
				'_price' => array(
					'label'       	=> __( 'Price', 'realteo' ),
					'type'        	=> 'text',
					'name'       	 => '_price',
					'tooltip'	  	=> __( 'Type overall or monthly price if property is for rent', 'realteo' ),
					'required'   	=> true,
					'placeholder'	=> '',
					'class'		  	=> '',
					'unit'		  	=> realteo_get_option( 'currency' ),
					'priority'    	=> 4,
					'before_row' 	 => '<div class="row with-forms">',
					'render_row_col' => '4'
				),				
				'_area' => array(
					'label'       => __( 'Area', 'realteo' ),
					'type'        => 'text',
					'name'        => '_area',
					'required'    => false,
					'placeholder' => '',
					'class'		  => '',
					'unit'		  => apply_filters('realteo_scale',$scale),
					'priority'    => 5,
					'render_row_col' => '4'
				),
				'_rooms' => array(
					'label'       	=> __( 'Rooms', 'realteo' ),
					'type'       	=> 'select',
					'name'       	 => '_rooms',
					'options_source' => 'custom',
					'options'   => array(
						'1' 	 => __( '1', 'realteo' ),
						'2' 	 => __( '2', 'realteo' ),
						'3' 	 => __( '3', 'realteo' ),
						'4' 	 => __( '4', 'realteo' ),
						'5+' 	 => __( '5+', 'realteo' ),
					),
					'class'		  	 => 'chosen-select-no-single',
					'default'    	 => '1',
					'after_row' 	 => '</div>',
					'render_row_col' => '4',
					'priority'    	 => 6,
					'required'    => false,
				),
				'_video' => array(
					'label'       => __( 'Video', 'realteo' ),
					'type'        => 'text',
					'name'        => '_video',
					'required'    => false,
					'placeholder' => 'URL to oEmbed supported service',
					'class'		  => '',
					'priority'    => 5,
				),

				'_header_gallery' => array(
					'label'       => __( 'Gallery', 'realteo' ),
					'name'        => '_header_gallery',
					'type'        => 'header',
					'required'    => false,

				),
				'_gallery' => array(
					'label'       => __( 'Gallery', 'realteo' ),
					'name'       => '_gallery',
					'type'        => 'files',
					'description' => __( 'By selecting (clicking on a photo) one of the uploaded photos you will set it as Featured Image for this property (marked by icon with star). Drag and drop thumbnails to re-order images in gallery.', 'realteo' ),
					'placeholder' => 'Upload images',
					'class'		  => '',
					'priority'    => 1,
					'required'    => false,
				),				
				'_thumbnail_id' => array(
					'label'       => __( 'Thumbnail ID', 'realteo' ),
					'type'        => 'hidden',
					'name'        => '_thumbnail_id',
					'class'		  => '',
					'priority'    => 1,
					'required'    => false,
				),
				'header_location' => array(
					'label'       => __( 'Location', 'realteo' ),
					'type'        => 'map',
					'required'    => false,
				),
				'_address' => array(
					'label'       => __( 'Google Maps Address', 'realteo' ),
					'type'        => 'text',
					'required'    => true,
					'name'        => '_address',
					'placeholder' => '',
					'class'		  => '',
					'before_row' 	 => '<div class="row with-forms">',
					'priority'    => 7,
					'render_row_col' => '6'
				),				
				'_friendly_address' => array(
					'label'       => __( 'Friendly Address', 'realteo' ),
					'type'        => 'text',
					'required'    => false,
					'name'        => '_friendly_address',
					'placeholder' => '',
					'tooltip'	  => __('Human readable address, if not set, the Google address will be used', 'realteo'),
					'class'		  => '',
					'after_row' 	 => '</div>',
					'priority'    => 8,
					'render_row_col' => '6'
				),				
				'_geolocation_long' => array(
					'label'       => __( 'Longitude', 'realteo' ),
					'type'        => 'text',
					'required'    => true,
					'placeholder' => '',
					'name'        => '_geolocation_long',
					'class'		  => '',
					'before_row' 	 => '<div class="row with-forms">',
					'priority'    => 9,
					'render_row_col' => '3'
				),				
				'_geolocation_lat' => array(
					'label'       => __( 'Latitude', 'realteo' ),
					'type'        => 'text',
					'required'    => true,
					'placeholder' => '',
					'name'        => '_geolocation_lat',
					'class'		  => '',
					'priority'    => 10,
					'render_row_col' => '3'
				),
				'region' => array(
					'label'       => __( 'Region', 'realteo' ),
					'type'        => 'term-select',
					'placeholder' => '',
					'name'        => 'region',
					'taxonomy'	  => 'region',
					'after_row'   => '</div>',
					'priority'    => 10,
					'default'	  => '',
					'render_row_col' => '6',
					'required'    => false,
				),

				/**/
				'header3' => array(
					'label'       => __( 'Detailed Information', 'realteo' ),
					'type'        => 'header',
					'required'    => false,
				),
				'property_description' => array(
					'label'       => __( 'Description', 'realteo' ),
					'type'        => 'wp-editor',
					'required'    => true,
					'placeholder' => '',
					'priority'    => 11
				),
				'_building_age' => array(
					'label'       	=> __( 'Building age', 'realteo' ),
					'type'       	=> 'select',
					'name'        => '_building_age',
					'options_source' => 'custom',
					'options'    	=> array(
						'0 - 1 Years' => __( '0 - 1 Years', 'realteo' ),
						'2 - 5 Years' => __( '2 - 5 Years', 'realteo' ),
						'6 - 10 Years' => __( '6 - 10 Years', 'realteo' ),
						'11 - 20 Years' => __( '11 - 20 Years', 'realteo' ),
						'21 - 50 Years' => __( '21 - 50 Years', 'realteo' ),
					),
					'class'		  	 => 'chosen-select-no-single',
					'default'    	 => '',
					'before_row' 	 => '<div class="row with-forms">',
					'render_row_col' => '4',
					'priority'    	 => 2,
					'required'    => false,
				),
				'_bedrooms' => array(
					'label'       	=> __( 'Bedrooms', 'realteo' ),
					'type'       	=> 'select',
					'name'        => '_bedrooms',
					'options_source' => 'custom',
					'options'    	=> array(
						'1'  => __( '1', 'realteo' ),
						'2'  => __( '2', 'realteo' ),
						'3'  => __( '3', 'realteo' ),
						'4'  => __( '4', 'realteo' ),
						'5+' => __( '5+', 'realteo' ),
					),
					'class'		  	 => 'chosen-select-no-single',
					'default'    	 => 'sale',
					'render_row_col' => '4',
					'priority'    	 => 2,
					'required'    => false,
				),				
				'_bathrooms' => array(
					'label'       	=> __( 'Bathrooms', 'realteo' ),
					'type'       	=> 'select',
					'name'			=> '_bathrooms',
					'options_source' => 'custom',
					'options'    	=> array(
						'1'  => __( '1', 'realteo' ),
						'2'  => __( '2', 'realteo' ),
						'3'  => __( '3', 'realteo' ),
						'4'  => __( '4', 'realteo' ),
						'5+' => __( '5', 'realteo' ),
					),
					'class'		  	 => 'chosen-select-no-single',
					'default'    	 => 'sale',
					'after_row' 	 => '</div>',
					'render_row_col' => '4',
					'priority'    	 => 2,
					'required'    => false,
				),

				'_parking' => array(
					'label'       => __( 'Parking', 'realteo' ),
					'type'        => 'text',
					'required'    => false,
					'name'			=> '_parking',
					'placeholder' => '',
					'class'		  => '',
					'before_row' 	 => '<div class="row with-forms">',
					'priority'    => 5,
					'render_row_col' => '4'
				),				
				'_cooling' => array(
					'label'       => __( 'Cooling', 'realteo' ),
					'type'        => 'text',
					'name'			=> '_cooling',
					'required'    => false,
					'placeholder' => '',
					'class'		  => '',
					'priority'    => 5,
					'render_row_col' => '4'
				),
				'_heating' => array(
					'label'       => __( 'Heating', 'realteo' ),
					'type'        => 'text',
					'name'			=> '_heating',
					'required'    => false,
					'placeholder' => '',
					'class'		  => '',
					'after_row' 	 => '</div>',
					'priority'    => 5,
					'render_row_col' => '4'
				),

				'_sewer' => array(
					'label'       => __( 'Sewer', 'realteo' ),
					'type'        => 'text',
					'required'    => false,
					'placeholder' => '',
					'name'			=> '_sewer',
					'class'		  => '',
					'before_row' 	 => '<div class="row with-forms">',
					'priority'    => 5,
					'render_row_col' => '3'
				),				
				'_water' => array(
					'label'       => __( 'Water', 'realteo' ),
					'type'        => 'text',
					'required'    => false,
					'name'			=> '_water',
					'placeholder' => '',
					'class'		  => '',
					'priority'    => 5,
					'render_row_col' => '3'
				),
				'_exercise_room' => array(
					'label'       => __( 'Exercise Room', 'realteo' ),
					'type'        => 'text',
					'required'    => false,
					'name'			=> '_exercise_room',
					'placeholder' => '',
					'class'		  => '',
					'priority'    => 5,
					'render_row_col' => '3'
				),				
				'_storage_room' => array(
					'label'       => __( 'Storage Room', 'realteo' ),
					'type'        => 'text',
					'required'    => false,
					'name'			=> '_storage_room',
					'placeholder' => '',
					'class'		  => '',
					'after_row' 	 => '</div>',
					'priority'    => 5,
					'render_row_col' => '3'
				),

				'property_feature' => array(
					'label'       	=> __( 'Other Features', 'realteo' ),
					'type'        	=> 'term-checkboxes',
					'taxonomy'		=> 'property_feature',
					'name'			=> 'property_feature',
					'class'		  	 => 'chosen-select-no-single',
					'default'    	 => '',
					'priority'    	 => 2,
					'required'    => false,
				),
				/**/
				'header4' => array(
					'label'       => __( 'Floorplans', 'realteo' ),
					'type'        => 'header',
					'required'    => false,
				),
				'_floorplans' => array(
					'label'       => __( 'Floorplans', 'realteo' ),
					'name'       => '_floorplans',
					'type'        => 'floorplans',
					'placeholder' => 'Upload floorplans',
					'class'		  => '',
					'priority'    => 1,
					'required'    => false,
				),		
			),
		
		) );
	}

	/**
	 * Validates the posted fields.
	 *
	 * @param array $values
	 * @throws Exception Uploaded file is not a valid mime-type or other validation error
	 * @return bool|WP_Error True on success, WP_Error on failure
	 */
	protected function validate_fields( $values ) {

		foreach ( $this->fields as $group_key => $group_fields ) {
			foreach ( $group_fields as $key => $field ) {
				if ( $field['type'] != 'header' && $field['required'] && empty( $values[ $group_key ][ $key ] ) ) {
					return new WP_Error( 'validation-error', sprintf( __( '%s is a required field', 'realteo' ), $field['label'] ) );
				}
				if ( ! empty( $field['taxonomy'] ) && in_array( $field['type'], array( 'term-checkboxes','checkbox', 'term-select', 'term-multiselect' ) ) ) {
					if ( is_array( $values[ $group_key ][ $key ] ) ) {
						$check_value = $values[ $group_key ][ $key ];
					} else {
						$check_value = empty( $values[ $group_key ][ $key ] ) ? array() : array( $values[ $group_key ][ $key ] );
					}

					foreach ( $check_value as $term ) {
						if ( ! term_exists( (int) $term, $field['taxonomy'] ) ) {
							return new WP_Error( 'validation-error', sprintf( __( '%s is invalid', 'realteo' ), $field['label'] ) );
						}
					}
				}
				if ( 'file' === $field['type'] && ! empty( $field['allowed_mime_types'] ) ) {
					if ( is_array( $values[ $group_key ][ $key ] ) ) {
						$check_value = array_filter( $values[ $group_key ][ $key ] );
					} else {
						$check_value = array_filter( array( $values[ $group_key ][ $key ] ) );
					}
					if ( ! empty( $check_value ) ) {
						foreach ( $check_value as $file_url ) {
							$file_url  = current( explode( '?', $file_url ) );
							$file_info = wp_check_filetype( $file_url );

							if ( ! is_numeric( $file_url ) && $file_info && ! in_array( $file_info['type'], $field['allowed_mime_types'] ) ) {
								throw new Exception( sprintf( __( '"%s" (filetype %s) needs to be one of the following file types: %s', 'realteo' ), $field['label'], $file_info['ext'], implode( ', ', array_keys( $field['allowed_mime_types'] ) ) ) );
							}
						}
					}
				}
			}
		}
	
		return apply_filters( 'submit_property_form_validate_fields', true, $this->fields, $values );
	}

	/**
	 * Displays the form.
	 */
	public function submit() {

		$this->init_fields();
		$template_loader = new Realteo_Template_Loader;
		if ( ! is_user_logged_in() ) {
			$template_loader->get_template_part( 'property-sign-in' );
			$template_loader->get_template_part( 'account/login' ); 
		} else {


		if ( is_user_logged_in() && $this->property_id ) {
			$property = get_post( $this->property_id );

			if($property){
				foreach ( $this->fields as $group_key => $group_fields ) {
					foreach ( $group_fields as $key => $field ) {
						switch ( $key ) {
							case 'property_title' :
								$this->fields[ $group_key ][ $key ]['value'] = $property->post_title;
							break;
							case 'property_description' :
								$this->fields[ $group_key ][ $key ]['value'] = $property->post_content;
							break;
							case 'property_feature' :
								$this->fields[ $group_key ][ $key ]['value'] =  wp_get_object_terms( $property->ID, 'property_feature', array( 'fields' => 'ids' ) ) ;
							break;
							case 'region' :
								$this->fields[ $group_key ][ $key ]['value'] = wp_get_object_terms( $property->ID, 'region', array( 'fields' => 'ids' ) );
							break;
					
							default:
								//echo $this->fields[ $group_key ][ $key ]['value'];
								if(isset($this->fields[ $group_key ][ $key ]['multi']) && $this->fields[ $group_key ][ $key ]['multi']) {

									$this->fields[ $group_key ][ $key ]['value'] = get_post_meta( $property->ID, $key, false );
								} else {
									$this->fields[ $group_key ][ $key ]['value'] = get_post_meta( $property->ID, $key, true );
								}
							break;
						}
					}
				}
			}
			
		}  elseif ( is_user_logged_in() && empty( $_POST['submit_property'] ) ) {
			$this->fields = apply_filters( 'submit_property_form_fields_get_user_data', $this->fields, get_current_user_id() );
		}

		
		$template_loader->set_template_data( 
			array( 
				'action' 		=> $this->get_action(),
				'fields' 		=> $this->fields,
				'form'      	=> $this->form_name,
				'property_edit' => $this->property_edit,
				'property_id'   => $this->get_property_id(),
				'step'      	=> $this->get_step(),
				'submit_button_text' => apply_filters( 'submit_property_form_submit_button_text', __( 'Preview', 'realteo' ) )
				) 
			)->get_template_part( 'property-submit' );
		}
	} 
	

	/**
	 * Handles the submission of form data.
	 */
	public function submit_handler() {
		// Posted Data

		try {
			// Init fields
			$this->init_fields();

			// Get posted values
			$values = $this->get_posted_fields();
			

			if ( empty( $_POST['submit_property'] ) ) {
				return;
			}

			// Validate required
			if ( is_wp_error( ( $return = $this->validate_fields( $values ) ) ) ) {
				throw new Exception( $return->get_error_message() );
			}


			if ( ! is_user_logged_in() ) {
				throw new Exception( __( 'You must be signed in to post a new listing.', 'realteo' ) );
			}
		
			// Update the property
			$this->save_property( $values['property']['property_title'], $values['property']['property_description'], $this->property_id ? '' : 'preview', $values );

			$this->update_property_data( $values );

			// Successful, show next step
			$this->step++;


		} catch ( Exception $e ) {

			$this->add_error( $e->getMessage() );
			return;
		}
	}

	/**
	 * Handles the preview step form response.
	 */
	public function preview_handler() {
		if ( ! $_POST ) {
			return;
		}


		if ( ! is_user_logged_in() ) {
			throw new Exception( __( 'You must be signed in to post a new listing.', 'realteo' ) );
		}

		// Edit = show submit form again
		if ( ! empty( $_POST['edit_property'] ) ) {
			$this->step --;
		}

		// Continue = change property status then show next screen
		if ( ! empty( $_POST['continue'] ) ) {

			$property = get_post( $this->property_id );

			if ( in_array( $property->post_status, array( 'preview', 'expired' ) ) ) {
				// Reset expiry
				delete_post_meta( $property->ID, '_property_expires' );

				// Update property listing
				$update_property                  = array();
				$update_property['ID']            = $property->ID;
				if($this->form_action == "editing") {
					$update_property['post_status'] = $property->post_status;
				} else {
					$update_property['post_status']   = apply_filters( 'submit_property_post_status', realteo_get_option( 'realteo_new_property_requires_approval' ) ? 'pending' : 'publish', $property );
				}
				$update_property['post_date']     = current_time( 'mysql' );
				$update_property['post_date_gmt'] = current_time( 'mysql', 1 );
				$update_property['post_author']   = get_current_user_id();
				wp_update_post( $update_property );
			}

			$this->step ++;
		}
	}

	/**
	 * Displays the final screen after a property listing has been submitted.
	 */
	public function done() {
		do_action( 'realteo_property_submitted', $this->property_id );
		$template_loader = new Realteo_Template_Loader;
		$template_loader->set_template_data( 
			array( 
				'property' 	=>  get_post( $this->property_id ),
				'id' 		=> 	$this->property_id,
				) 
			)->get_template_part( 'property-submitted' );
	}


	public function choose_package( $atts = array() ) {
	$template_loader = new Realteo_Template_Loader;
		if ( ! is_user_logged_in() ) {
			$template_loader->get_template_part( 'property-sign-in' );
			$template_loader->get_template_part( 'account/login' ); 
		} else {
			$packages      = self::get_packages(  );
			$user_packages = realteo_user_packages( get_current_user_id() );
			
			$template_loader->set_template_data( 
				array( 
					'packages' 		=> $packages,
					'user_packages' => $user_packages,
					'form'      	=> $this->form_name,
					'action' 		=> $this->get_action(),
					'property_id'   => $this->get_property_id(),
					'step'      	=> $this->get_step(),
					'submit_button_text' => __( 'Submit Property', 'realteo' ),
					) 
				)->get_template_part( 'property-submit-package' );
		}
	}

	public function choose_package_handler() {

		// Validate Selected Package
		$validation = self::validate_package( self::$package_id, self::$is_user_package );

		// Error? Go back to choose package step.
		if ( is_wp_error( $validation ) ) {
			$this->add_error( $validation->get_error_message() );
			$this->set_step( array_search( 'package', array_keys( $this->get_steps() ) ) );
			return false;
		}

		// Store selection in cookie
		wc_setcookie( 'chosen_package_id', self::$package_id );
		wc_setcookie( 'chosen_package_is_user_package', self::$is_user_package ? 1 : 0 );

		// Process the package unless we're doing this before a job is submitted
		if ( 'process-package' === $this->get_step_key() ) {
			// Product the package
			if ( self::process_package( self::$package_id, self::$is_user_package, $this->get_property_id() ) ) {
				$this->next_step();
			}
		} else {
			$this->next_step();
		}
	}

	/**
	 * Validate package
	 *
	 * @param  int  $package_id
	 * @param  bool $is_user_package
	 * @return bool|WP_Error
	 */
	private static function validate_package( $package_id, $is_user_package ) {
		if ( empty( $package_id ) ) {
			return new WP_Error( 'error', __( 'Invalid Package', 'realteo' ) );
		} elseif ( $is_user_package ) {
			if ( ! realteo_package_is_valid( get_current_user_id(), $package_id ) ) {
				return new WP_Error( 'error', __( 'Invalid Package', 'realteo' ) );
			}
		} else {
			$package = wc_get_product( $package_id );

			if ( ! $package->is_type( 'property_package' )  ) {
				return new WP_Error( 'error', __( 'Invalid Package', 'realteo' ) );
			}

		}
		return true;
	}


	/**
	 * Purchase a job package
	 *
	 * @param  int|string $package_id
	 * @param  bool       $is_user_package
	 * @param  int        $property_id
	 * @return bool Did it work or not?
	 */
	private static function process_package( $package_id, $is_user_package, $property_id ) {
		// Make sure the job has the correct status
		
		if ( 'preview' === get_post_status( $property_id ) ) {
			// Update job listing
			$update_job                  = array();
			$update_job['ID']            = $property_id;
			$update_job['post_status']   = 'pending_payment';
			$update_job['post_date']     = current_time( 'mysql' );
			$update_job['post_date_gmt'] = current_time( 'mysql', 1 );
			$update_job['post_author']   = get_current_user_id();
		
			wp_update_post( $update_job );
		}

		if ( $is_user_package ) {
			$user_package = realteo_get_user_package( $package_id );
			$package      = wc_get_product( $user_package->get_product_id() );

			// Give job the package attributes
			update_post_meta( $property_id, '_duration', $user_package->get_duration() );
			update_post_meta( $property_id, '_featured', $user_package->is_featured() ? 1 : 0 );
			update_post_meta( $property_id, '_package_id', $user_package->get_product_id() );
			update_post_meta( $property_id, '_user_package_id', $package_id );


			// Approve the job
			if ( in_array( get_post_status( $property_id ), array( 'pending_payment', 'expired' ) ) ) {
				realteo_approve_listing_with_package( $property_id, get_current_user_id(), $package_id );
			}

			return true;
		} elseif ( $package_id ) {
			$package = wc_get_product( $package_id );

			
			$is_featured = $package->is_property_featured();
			

			// Give job the package attributes
			update_post_meta( $property_id, '_duration', $package->get_duration() );
			update_post_meta( $property_id, '_featured', $is_featured ? 1 : 0 );
			update_post_meta( $property_id, '_package_id', $package_id );

			// Clear cookie
			wc_setcookie( 'chosen_package_id', '', time() - HOUR_IN_SECONDS );
			wc_setcookie( 'chosen_package_is_user_package', '', time() - HOUR_IN_SECONDS );


			// Add package to the cart
			WC()->cart->add_to_cart( $package_id, 1, '', '', array(
				'property_id' => $property_id,
			) );

			wc_add_to_cart_message( $package_id );


			// Redirect to checkout page
			wp_redirect( get_permalink( wc_get_page_id( 'checkout' ) ) );
			exit;
		}// End if().
	}


	/**
	 * Adds an error.
	 *
	 * @param string $error The error message.
	 */
	public function add_error( $error ) {
		$this->errors[] = $error;
	}

	/**
	 * Gets post data for fields.
	 *
	 * @return array of data
	 */
	protected function get_posted_fields() {
		$this->init_fields();

		$values = array();

		foreach ( $this->fields as $group_key => $group_fields ) {
			foreach ( $group_fields as $key => $field ) {
				// Get the value
				$field_type = str_replace( '-', '_', $field['type'] );

				if ( $handler = apply_filters( "realteo_get_posted_{$field_type}_field", false ) ) {
					$values[ $group_key ][ $key ] = call_user_func( $handler, $key, $field );
				} elseif ( method_exists( $this, "get_posted_{$field_type}_field" ) ) {
					$values[ $group_key ][ $key ] = call_user_func( array( $this, "get_posted_{$field_type}_field" ), $key, $field );
				} else {
					$values[ $group_key ][ $key ] = $this->get_posted_field( $key, $field );
				}

				// Set fields value
				$this->fields[ $group_key ][ $key ]['value'] = $values[ $group_key ][ $key ];
			}
		}

		return $values;
	}


	/**
	 * Gets the value of a posted field.
	 *
	 * @param  string $key
	 * @param  array  $field
	 * @return string|array
	 */
	protected function get_posted_field( $key, $field ) {
		return isset( $_POST[ $key ] ) ? $this->sanitize_posted_field( $_POST[ $key ] ) : '';
	}

	/**
	 * Navigates through an array and sanitizes the field.
	 *
	 * @param array|string $value The array or string to be sanitized.
	 * @return array|string $value The sanitized array (or string from the callback).
	 */
	protected function sanitize_posted_field( $value ) {
		// Santize value
		$value = is_array( $value ) ? array_map( array( $this, 'sanitize_posted_field' ), $value ) : sanitize_text_field( stripslashes( trim( $value ) ) );

		return $value;
	}

	/**
	 * Gets the value of a posted textarea field.
	 *
	 * @param  string $key
	 * @param  array  $field
	 * @return string
	 */
	protected function get_posted_textarea_field( $key, $field ) {
		return isset( $_POST[ $key ] ) ? wp_kses_post( trim( stripslashes( $_POST[ $key ] ) ) ) : '';
	}

	/**
	 * Gets the value of a posted textarea field.
	 *
	 * @param  string $key
	 * @param  array  $field
	 * @return string
	 */
	protected function get_posted_wp_editor_field( $key, $field ) {
		return $this->get_posted_textarea_field( $key, $field );
	}

	/**
	 * Gets the value of a posted file field.
	 *
	 * @param  string $key
	 * @param  array  $field
	 * @return string|array
	 */
	protected function get_posted_file_field( $key, $field ) {
		$file = $this->upload_file( $key, $field );

		if ( ! $file ) {
			$file = $this->get_posted_field( 'current_' . $key, $field );
		} elseif ( is_array( $file ) ) {
			$file = array_filter( array_merge( $file, (array) $this->get_posted_field( 'current_' . $key, $field ) ) );
		}

		return $file;
	}

	/**
	 * Updates or creates a property listing from posted data.
	 *
	 * @param  string $post_title
	 * @param  string $post_content
	 * @param  string $status
	 * @param  array  $values
	 * @param  bool   $update_slug
	 */
	protected function save_property( $post_title, $post_content, $status = 'preview', $values = array(), $update_slug = true ) {
		$property_data = array(
			'post_title'     => $post_title,
			'post_content'   => $post_content,
			'post_type'      => 'property',
			'comment_status' => 'closed'
		);

		if ( $update_slug ) {
			$property_slug   = array();

			$property_slug[]            = $post_title;
			$property_data['post_name'] = sanitize_title( implode( '-', $property_slug ) );
		}

		if ( $status && $this->form_action != "editing") {
			$property_data['post_status'] = $status;
		}

		$property_data = apply_filters( 'submit_property_form_save_property_data', $property_data, $post_title, $post_content, $status, $values );

		if ( $this->property_id ) {
			$property_data['ID'] = $this->property_id;
			wp_update_post( $property_data );
		} else {
			$this->property_id = wp_insert_post( $property_data );

			if ( ! headers_sent() ) {
				$submitting_key = uniqid();

				setcookie( 'realteo-submitting-property-id', $this->property_id, false, COOKIEPATH, COOKIE_DOMAIN, false );
				setcookie( 'realteo-submitting-property-key', $submitting_key, false, COOKIEPATH, COOKIE_DOMAIN, false );

				update_post_meta( $this->property_id, '_submitting_key', $submitting_key );
			}
		}
	}

	/**
	 * Sets property meta and terms based on posted values.
	 *
	 * @param  array $values
	 */
	protected function update_property_data( $values ) {
		// Set defaults

		$maybe_attach = array();

		// Loop fields and save meta and term data
		foreach ( $this->fields as $group_key => $group_fields ) {
	
			foreach ( $group_fields as $key => $field ) {
				// Save taxonomies
				
				if ( ! empty( $field['taxonomy'] ) ) {
					if ( is_array( $values[ $group_key ][ $key ] ) ) {

						/*TODO - fix the damn region string*/
						wp_set_object_terms( $this->property_id, $values[ $group_key ][ $key ], $field['taxonomy'], false );
					} else {
						wp_set_object_terms( $this->property_id, array( intval($values[ $group_key ][ $key ]) ), $field['taxonomy'], false );
					}

				// Company logo is a featured image
				} elseif ( 'thumbnail' === $key || '_thumbnail_id' === $key) {
					$attachment_id = is_numeric( $values[ $group_key ][ $key ] );
					if ( empty( $attachment_id ) ) {
						delete_post_thumbnail( $this->property_id );
					} else {
						set_post_thumbnail( $this->property_id, $attachment_id );
					}
					

				// Save meta data
				} else {

					if($field['multi']) {
						
						delete_post_meta($this->property_id, $key); 
						
						if ( is_array( $values[ $group_key ][ $key ] ) ) {
							foreach( $values[ $group_key ][ $key ] as $value ) {
								add_post_meta( $this->property_id, $key, $value );
							}
						} else {
							if(!empty($values[ $group_key ][ $key ])){
								add_post_meta( $this->property_id, $key, $values[ $group_key ][ $key ] );	
							}
							
						}
					} else {
						update_post_meta( $this->property_id, $key, $values[ $group_key ][ $key ] );
					}
					if($key == '_gallery') {
						$ids = $values[ $group_key ][ $key ];
						if(is_array($ids) && !empty($ids)){
							foreach ($ids as $key => $value) {
								$attachment = array(
					                'ID' => $key,
					                'post_parent' => $this->property_id,
					            );

					            $res = wp_update_post($attachment);
							}
						}
					}

					// Handle attachments
					if ( 'file' === $field['type'] ) {
						$attachment_id = is_numeric( $values[ $group_key ][ $key ] ) ? absint( $values[ $group_key ][ $key ] ) : $this->create_attachment( $values[ $group_key ][ $key ] );
				
						update_post_meta( $this->property_id, $key.'_id', $attachment_id  );
						
						// if ( is_array( $values[ $group_key ][ $key ] ) ) {
						// 	foreach ( $values[ $group_key ][ $key ] as $file_url ) {
						// 		$maybe_attach[] = $file_url;
						// 	}
						// } else {
						// 	$maybe_attach[] = $values[ $group_key ][ $key ];
						// }
					}
				}
			}
		}

		// $maybe_attach = array_filter( $maybe_attach );

		// // Handle attachments
		// if ( sizeof( $maybe_attach ) && apply_filters( 'realteo_attach_uploaded_files', true ) ) {
		// 	// Get attachments
		// 	$attachments     = get_posts( 'post_parent=' . $this->property_id . '&post_type=attachment&fields=ids&post_mime_type=image&numberposts=-1' );
		// 	$attachment_urls = array();

		// 	// Loop attachments already attached to the property
		// 	foreach ( $attachments as $attachment_id ) {
		// 		$attachment_urls[] = wp_get_attachment_url( $attachment_id );
		// 	}

		// 	foreach ( $maybe_attach as $attachment_url ) {
		// 		if ! in_array( $attachment_url, $attachment_urls ) ) {
		// 			$this->create_attachment( $attachment_url );

		// 		}
		// 	}
		// }

		// And user meta to save time in future
		

		do_action( 'realteo_update_property_data', $this->property_id, $values );
	}


	/**
	 * Displays preview of property Listing.
	 */
	public function preview() {
		global $post, $property_preview;
		
		if ( $this->property_id ) {
			$property_preview       = true;
			$post              = get_post( $this->property_id );
			$post->post_status = 'preview';

			setup_postdata( $post );

			$template_loader = new Realteo_Template_Loader;
			$template_loader->set_template_data( 
			array( 
				'action' 		=> $this->get_action(),
				'fields' 		=> $this->fields,
				'form'      	=> $this->form_name,
				'post'      	=> $post,
				'property_id'   => $this->get_property_id(),
				'step'      	=> $this->get_step(),
				'submit_button_text' => apply_filters( 'submit_property_form_preview_button_text', __( 'Submit', 'realteo' ) )
				) 
			)->get_template_part( 'property-preview' );

			wp_reset_postdata();
		}
	}


	protected function get_posted_term_checkboxes_field( $key, $field ) {

		if ( isset( $_POST[ 'tax_input' ] ) && isset( $_POST[ 'tax_input' ][ $field['taxonomy'] ] ) ) {
			return array_map( 'absint', $_POST[ 'tax_input' ][ $field['taxonomy'] ] );
		} else {
			return array();
		}
	}


	function enable_paid_listings($steps){
 
		if(realteo_get_option_with_name('realteo_property_submit_option', 'realteo_new_property_requires_purchase' ) && !isset($_GET["action"]) || isset($_GET["action"]) && $_GET["action"] == 'renew' ){

		/*
		if(realteo_get_option_with_name('realteo_property_submit_option', 'realteo_new_property_requires_purchase' ) && !isset($_GET["action"])){*/
			$steps['package'] = array(
					'name'     => __( 'Choose a package', 'realteo' ),
					'view'     => array( $this, 'choose_package' ),
					'handler'  => array(  $this, 'choose_package_handler' ),
					'priority' => 5,
				);
			$steps['process-package'] = array(
					'name'     => '',
					'view'     => false,
					'handler'  => array( $this, 'choose_package_handler' ),
					'priority' => 25,
			);
		}
		return $steps;
	}

	/**
	 * Gets step key from outside of the class.
	 *
	 * @since 1.24.0
	 * @param string|int $step
	 * @return string
	 */
	public function get_step_key( $step = '' ) {
		if ( ! $step ) {
			$step = $this->step;
		}
		$keys = array_keys( $this->steps );
		return isset( $keys[ $step ] ) ? $keys[ $step ] : '';
	}


	/**
	 * Gets steps from outside of the class.
	 *
	 * @since 1.24.0
	 */
	public function get_steps() {
		return $this->steps;
	}

	/**
	 * Gets step from outside of the class.
	 */
	public function get_step() {
		return $this->step;
	}


	/**
	 * Decreases step from outside of the class.
	 */
	public function previous_step() {
		$this->step --;
	}

	/**
	 * Sets step from outside of the class.
	 *
	 * @since 1.24.0
	 * @param int $step
	 */
	public function set_step( $step ) {
		$this->step = absint( $step );
	}

	/**
	 * Increases step from outside of the class.
	 */
	public function next_step() {
		$this->step ++;
	}

	/**
	 * Displays errors.
	 */
	public function show_errors() {
		foreach ( $this->errors as $error ) {
			echo '<div class="property-manager-error">' . $error . '</div>';
		}
	}


	/**
	 * Gets the action (URL for forms to post to).
	 * As of 1.22.2 this defaults to the current page permalink.
	 *
	 * @return string
	 */
	public function get_action() {
		return esc_url_raw( $this->action ? $this->action : wp_unslash( $_SERVER['REQUEST_URI'] ) );
	}

	/**
	 * Gets the submitted property ID.
	 *
	 * @return int
	 */
	public function get_property_id() {
		return absint( $this->property_id );
	}

	/**
	 * Sorts array by priority value.
	 *
	 * @param array $a
	 * @param array $b
	 * @return int
	 */
	protected function sort_by_priority( $a, $b ) {
	    if ( $a['priority'] == $b['priority'] ) {
	        return 0;
	    }
	    return ( $a['priority'] < $b['priority'] ) ? -1 : 1;
	}

	/**
	 * Calls the view handler if set, otherwise call the next handler.
	 *
	 * @param array $atts Attributes to use in the view handler.
	 */
	public function output( $atts = array() ) {
		$step_key = $this->get_step_key( $this->step );
		$this->show_errors();

		if ( $step_key && is_callable( $this->steps[ $step_key ]['view'] ) ) {
			call_user_func( $this->steps[ $step_key ]['view'], $atts );
		}
	}

	/**
	 * Returns the form content.
	 *
	 * @param string $form_name
	 * @param array  $atts Optional passed attributes
	 * @return string|null
	 */
	public function get_form( $atts = array() ) {
			
			ob_start();
			$this->output( $atts );
			return ob_get_clean();
		
	}
	
	/**
	 * This filter insures users only see their own media
	 */
	function filter_media( $query ) {
		// admins get to see everything
		if ( ! current_user_can( 'manage_options' ) )
			$query['author'] = get_current_user_id();
		return $query;
	}

	function change_page_title( $title, $id = null ) {

	    if ( is_page( realteo_get_option( 'submit_property_page' ) ) && in_the_loop()) {
	       if($this->form_action == "editing") {
	       	$title = esc_html__('Edit Property', 'realteo');
	       };
	    }

	    return $title;
	}

	/**
	 * Creates a file attachment.
	 *
	 * @param  string $attachment_url
	 * @return int attachment id
	 */
	protected function create_attachment( $attachment_url ) {
		include_once( ABSPATH . 'wp-admin/includes/image.php' );
		include_once( ABSPATH . 'wp-admin/includes/media.php' );

		$upload_dir     = wp_upload_dir();
		$attachment_url = str_replace( array( $upload_dir['baseurl'], WP_CONTENT_URL, site_url( '/' ) ), array( $upload_dir['basedir'], WP_CONTENT_DIR, ABSPATH ), $attachment_url );

		if ( empty( $attachment_url ) || ! is_string( $attachment_url ) ) {
			return 0;
		}

		$attachment     = array(
			'post_title'   => get_the_title( $this->property_id ),
			'post_content' => '',
			'post_status'  => 'inherit',
			'post_parent'  => $this->property_id,
			'guid'         => $attachment_url
		);

		if ( $info = wp_check_filetype( $attachment_url ) ) {
			$attachment['post_mime_type'] = $info['type'];
		}

		$attachment_id = wp_insert_attachment( $attachment, $attachment_url, $this->property_id );

		if ( ! is_wp_error( $attachment_id ) ) {
			wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $attachment_url ) );
			return $attachment_id;
		}

		return 0;
	}


	/**
	 * Return packages
	 *
	 * @param array $post__in
	 * @return array
	 */
	public static function get_packages( $post__in = array() ) {
		return get_posts( array(
			'post_type'        => 'product',
			'posts_per_page'   => -1,
			'post__in'         => $post__in,
			'order'            => 'asc',
			'orderby'          => 'menu_order',
			'suppress_filters' => false,
			'tax_query'        => WC()->query->get_tax_query( array(
				'relation' => 'AND',
				array(
					'taxonomy' => 'product_type',
					'field'    => 'slug',
					'terms'    => array( 'property_package'),
					'operator' => 'IN',
				),
			) ),
			'meta_query'       => WC()->query->get_meta_query(),
		)  );
	}

	/**
	 * Change initial job status
	 *
	 * @param string  $status
	 * @param WP_Post $job
	 * @return string
	 */
	public static function submit_property_post_status( $status, $property ) {
		if(realteo_get_option_with_name('realteo_property_submit_option', 'realteo_new_property_requires_purchase' )){
			switch ( $property->post_status ) {
				case 'preview' :
					return 'pending_payment';
				break;
				case 'expired' :
					return 'expired';
				break;
				default :
					return $status;
				break;
			}
		} else {
			return $status;
		}

	}


	/**
	 * Handles the uploading of files.
	 *
	 * @param string $field_key
	 * @param array  $field
	 * @throws Exception When file upload failed
	 * @return  string|array
	 */
	protected function upload_file( $field_key, $field ) {
		if ( isset( $_FILES[ $field_key ] ) && ! empty( $_FILES[ $field_key ] ) && ! empty( $_FILES[ $field_key ]['name'] ) ) {
			if ( ! empty( $field['allowed_mime_types'] ) ) {
				$allowed_mime_types = $field['allowed_mime_types'];
			} else {
				$allowed_mime_types = realteo_get_allowed_mime_types();
			}

			$file_urls       = array();
			$files_to_upload = realteo_prepare_uploaded_files( $_FILES[ $field_key ] );

			foreach ( $files_to_upload as $file_to_upload ) {
				$uploaded_file = realteo_upload_file( $file_to_upload, array(
					'file_key'           => $field_key,
					'allowed_mime_types' => $allowed_mime_types,
					) );

				if ( is_wp_error( $uploaded_file ) ) {
					throw new Exception( $uploaded_file->get_error_message() );
				} else {
					$file_urls[] = $uploaded_file->url;
				}
			}

			if ( ! empty( $field['multiple'] ) ) {
				return $file_urls;
			} else {
				return current( $file_urls );
			}
		}
	}


}


