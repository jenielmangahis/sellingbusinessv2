<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Realteo {

	/**
	 * The single instance of Realteo.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Settings class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	/**
	 * Suffix for Javascripts.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $script_suffix;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct ( $file = '', $version = '1.0.0' ) {
		$this->_version = $version;
		$this->_token = 'realteo';

		// Load plugin environment variables
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		register_activation_hook( $this->file, array( $this, 'install' ) );

		include( 'abstracts/abstract-realteo-form.php' );

		include( 'class-realteo-post-types.php' );
		include( 'class-realteo-meta-boxes.php' );
		include( 'class-realteo-property.php' );
		include( 'class-realteo-submit.php' );
		include( 'class-realteo-shortcodes.php' );
		include( 'class-realteo-search.php' );
		include( 'class-realteo-agents.php' );
		include( 'class-realteo-bookmarks.php' );
		include( 'class-realteo-compare.php' );
		include( 'class-realteo-emails.php' );
		include( 'class-realteo-geocode.php' );
		include( 'class-realteo-agency.php' );
	
	
		// Load frontend JS & CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

		// Load admin JS & CSS
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );

		add_action( 'wp_ajax_handle_dropped_media', array( $this, 'realteo_handle_dropped_media' ));
		add_action( 'wp_ajax_nopriv_handle_dropped_media', array( $this, 'realteo_handle_dropped_media' ));
		add_action( 'wp_ajax_nopriv_handle_delete_media',  array( $this, 'realteo_handle_delete_media' ));
		add_action( 'wp_ajax_handle_delete_media',  array( $this, 'realteo_handle_delete_media' ));

		// Load API for generic admin functions
		if ( is_admin() ) {
			$this->admin = new Realteo_Admin_API();
		}
		
		$this->post_types 	= Realteo_Post_Types::instance();
		$this->meta_boxes 	= new Realteo_Meta_Boxes();
		$this->property 	= new Realteo_Property();
		$this->search 		= new Realteo_Search();
		$this->agents 		= new Realteo_Agents();
		$this->bookmarks 	= new Realteo_Bookmarks();
		$this->compare 		= Realteo_Compare::instance();
		$this->submit 		= Realteo_Submit::instance();
		$this->emails 		= Realteo_Emails::instance();
		$this->geocode 		= Realteo_Geocode::instance();
		$this->agency 		= Realteo_Agency::instance();
		
		// Handle localisation
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );
		add_action( 'init', array( $this, 'image_size' ) );
		add_action( 'init', array( $this, 'register_sidebar' ) );
		
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );
		
		add_action( 'after_setup_theme', array( $this, 'include_template_functions' ), 11 );

		add_filter( 'template_include', array( $this, 'property_templates' ) );

		// Schedule cron jobs
		self::maybe_schedule_cron_jobs();
		

	} // End __construct ()
	  
	/**
	 * Widgets init
	 */
	public function widgets_init() {
		include( 'class-realteo-widgets.php' );
	}


	public function include_template_functions() {
		include( REALTEO_PLUGIN_DIR.'/realteo-template-functions.php' );
		include( REALTEO_PLUGIN_DIR.'/includes/paid-properties/realteo-paid-properties-functions.php' );
	}

	/* handles single property and archive property view */
	public static function property_templates( $template ) {
		$post_type = get_post_type();  
		$custom_post_types = array( 'property','agency' );
		
		$template_loader = new Realteo_Template_Loader;
		if ( in_array( $post_type, $custom_post_types ) ) {
			
			if ( is_archive() && !is_author() ) {
				$template = $template_loader->locate_template('archive-' . $post_type . '.php');
				return $template;
			}

			if ( is_single() ) {
				$template = $template_loader->locate_template('single-' . $post_type . '.php');
				return $template;
			}
		}
		if( is_post_type_archive( 'property' ) ){

			$template = $template_loader->locate_template('archive-property.php');

		}

		return $template;
	}

	/**
	 * Load frontend CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function enqueue_styles () {
		wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-frontend' );

	} // End enqueue_styles ()



	/**
	 * Load frontend Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function enqueue_scripts () {
		wp_register_script( 'owl-carousel-min', esc_url( $this->assets_url ) . 'js/owl.carousel.min.js', array( 'jquery' ), $this->_version );
		wp_register_script( 'chosen-min', esc_url( $this->assets_url ) . 'js/chosen.min.js', array( 'jquery' ), $this->_version );
		wp_register_script( 'slick-min', esc_url( $this->assets_url ) . 'js/slick.min.js', array( 'jquery' ), $this->_version );
		wp_register_script( 'masonry-min', esc_url( $this->assets_url ) . 'js/masonry.min.js', array( 'jquery' ), $this->_version );
		wp_register_script(	'infobox-min', esc_url( $this->assets_url ) . 'js/infobox.min.js', array( 'jquery' ), $this->_version );
		wp_register_script(	'gmaps-api-v3-min', esc_url( $this->assets_url ) . 'js/gmaps_api_v3.min.js', array( 'jquery' ), $this->_version );
		wp_register_script(	'markerclusterer', esc_url( $this->assets_url ) . 'js/markerclusterer.js', array( 'jquery' ), $this->_version );
		wp_register_script(	'dropzone', esc_url( $this->assets_url ) . 'js/dropzone.js', array( 'jquery' ), $this->_version, true );
		wp_register_script(	'uploads', esc_url( $this->assets_url ) . 'js/uploads.min.js', array( 'jquery' ), $this->_version, true );
		wp_register_script(	$this->_token . '-maps', esc_url( $this->assets_url ) . 'js/maps.min.js', array( 'jquery' ), $this->_version , array('infobox-min','gmaps-api-v3-min','markerclusterer'));
		
		$maps_api_key = realteo_get_option( 'realteo_maps_api' );

		if($maps_api_key) {
			wp_enqueue_script( 'google-maps', 'https://maps.google.com/maps/api/js?key='.$maps_api_key.'&libraries=places' );
			wp_enqueue_script( 'infobox-min');	
			wp_enqueue_script( 'gmaps-api-v3-min');	
			wp_enqueue_script( 'markerclusterer');	
			wp_enqueue_script( $this->_token . '-maps');	
		}
		wp_enqueue_script( 'google-recaptcha', 'https://www.google.com/recaptcha/api.js');
		wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/frontend' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		
		

		$_price_min =  $this->get_min_all_property_price('');
		$_price_max =  $this->get_max_all_property_price('');

		$rent_price_min =  $this->get_min_property_price('rent');
		$rent_price_max =  $this->get_max_property_price('rent');

		$sale_price_min =  $this->get_min_property_price('sale');
		$sale_price_max =  $this->get_max_property_price('sale');


		$ajax_url = admin_url( 'admin-ajax.php', 'relative' );
		wp_localize_script(  $this->_token . '-frontend', 'realteo', array(
				'ajax_url'                	=> $ajax_url,
				'is_rtl'                  	=> is_rtl() ? 1 : 0,
				'lang'                    	=> defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : '', // WPML workaround until this is standardized
				/*'area_min'		      		=> $area_min,
				'area_max'		      		=> $area_max,*/
				'_price_min'		    	=> $_price_min,
				'_price_max'		    	=> $_price_max,
				'sale_price_min'		    => $sale_price_min,
				'sale_price_max'		    => $sale_price_max,				
				'rent_price_min'		    => $rent_price_min,
				'rent_price_max'		    => $rent_price_max,
				'currency'		      		=> realteo_get_option( 'currency' ),
				'submitCenterPoint'		      	=> realteo_get_option( 'realteo_submit_center_point','52.2296756,21.012228700000037' ),
				'centerPoint'		      	=> realteo_get_option( 'realteo_map_center_point','52.2296756,21.012228700000037' ),
				'country'		      		=> realteo_get_option( 'realteo_maps_limit_country' ),
				'upload'					=> admin_url( 'admin-ajax.php?action=handle_dropped_media' ),
  				'delete'					=> admin_url( 'admin-ajax.php?action=handle_delete_media' ),
  				'color'						=> get_option('pp_main_color','#274abb' ), 
  				'dictDefaultMessage'		=> esc_html__("Drop files here to upload","realteo"),
				'dictFallbackMessage' 		=> esc_html__("Your browser does not support drag'n'drop file uploads.","realteo"),
				'dictFallbackText' 			=> esc_html__("Please use the fallback form below to upload your files like in the olden days.","realteo"),
				'dictFileTooBig' 			=> esc_html__("File is too big ({{filesize}}MiB). Max filesize: {{maxFilesize}}MiB.","realteo"),
				'dictInvalidFileType' 		=> esc_html__("You can't upload files of this type.","realteo"),
				'dictResponseError'		 	=> esc_html__("Server responded with {{statusCode}} code.","realteo"),
				'dictCancelUpload' 			=> esc_html__("Cancel upload","realteo"),
				'dictCancelUploadConfirmation' => esc_html__("Are you sure you want to cancel this upload?","realteo"),
				'dictRemoveFile' 			=> esc_html__("Remove file","realteo"),
				'dictMaxFilesExceeded' 		=> esc_html__("You can not upload any more files.","realteo"),
				'areyousure' 				=> esc_html__("Are you sure?","realteo"),
				'maxFiles' 					=> realteo_get_option('realteo_max_files',10),
				'maxFilesize' 				=> realteo_get_option('realteo_max_filesize',2),
				'available_for_rental'		=>  get_available_for_rental_period()

			) );
		

		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-slider' );
		wp_enqueue_script( 'jquery-ui-autocomplete' );

		wp_enqueue_script( 'slick-min' );
		wp_enqueue_script( 'masonry-min' );
		wp_enqueue_script( 'chosen-min' );
		wp_enqueue_script( 'owl-carousel-min' );
		wp_enqueue_script( 'dropzone' );
		wp_enqueue_script( 'jquery-ui-sortable' );
		wp_enqueue_script( 'uploads' );
		//wp_enqueue_media();
		wp_enqueue_script( $this->_token . '-frontend' );
		
	} // End enqueue_scripts ()

	/**
	 * Load admin CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_styles ( $hook = '' ) {
		$maps_api_key = realteo_get_option( 'realteo_maps_api' );
		if($maps_api_key) {
			wp_enqueue_script( 'google-maps', 'https://maps.google.com/maps/api/js?key='.$maps_api_key.'&libraries=places&v=3.30' );	
		}

		wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-admin' );
	} // End admin_enqueue_styles ()

	/**
	 * Load admin Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function admin_enqueue_scripts ( $hook = '' ) {
		$maps_api_key = realteo_get_option( 'realteo_maps_api' );
		if($maps_api_key) {
			wp_register_script( $this->_token . '-admin', esc_url( $this->assets_url ) . 'js/admin' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
			wp_enqueue_script( $this->_token . '-admin' );
		}
	} // End admin_enqueue_scripts ()

	/**
	 * Load plugin localisation
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'realteo', false, dirname( plugin_basename( $this->file ) ) . '/languages/' );

	} // End load_localisation ()

	/**
	 * Adds image sizes
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function image_size () {
		add_image_size('findeo-gallery', 1200, 0, true);
		add_image_size('findeo-property-grid', 520, 397, true);
		add_image_size('realteo-avatar', 590, 590, true);

	} // End load_localisation ()

	public function register_sidebar () {

		register_sidebar( array(
			'name'          => esc_html__( 'Single property sidebar', 'realteo' ),
			'id'            => 'sidebar-property',
			'description'   => esc_html__( 'Add widgets here.', 'realteo' ),
			'before_widget' => '<div id="%1$s" class="property-widget widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title margin-bottom-35">',
			'after_title'   => '</h3>',
		) );

		register_sidebar( array(
			'name'          => esc_html__( 'Properties sidebar', 'realteo' ),
			'id'            => 'sidebar-properties',
			'description'   => esc_html__( 'Add widgets here.', 'realteo' ),
			'before_widget' => '<div id="%1$s" class="property-widget widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title margin-bottom-35">',
			'after_title'   => '</h3>',
		) );		



	} // End load_localisation ()


	function get_min_property_price($type) {
		global $wpdb;
		$result = $wpdb->get_var(
	    $wpdb->prepare("
	            SELECT min(m2.meta_value + 0)
	            FROM $wpdb->posts AS p
	            INNER JOIN $wpdb->postmeta AS m1 ON ( p.ID = m1.post_id )
				INNER JOIN $wpdb->postmeta AS m2  ON ( p.ID = m2.post_id )
				WHERE
				p.post_type = 'property'
				AND p.post_status = 'publish'
				AND ( m1.meta_key = '_offer_type' AND m1.meta_value = %s )
				AND ( m2.meta_key = '_price'  ) AND m2.meta_value != ''
	        ", $type )
	    ) ;

	    return $result;
	}	

	function get_max_property_price($type) {
		global $wpdb;
		$result = $wpdb->get_var(
	    $wpdb->prepare("
	            SELECT max(m2.meta_value + 0)
	            FROM $wpdb->posts AS p
	            INNER JOIN $wpdb->postmeta AS m1 ON ( p.ID = m1.post_id )
				INNER JOIN $wpdb->postmeta AS m2  ON ( p.ID = m2.post_id )
				WHERE
				p.post_type = 'property'
				AND p.post_status = 'publish'
				AND ( m1.meta_key = '_offer_type' AND m1.meta_value = %s )
				AND ( m2.meta_key = '_price'  ) AND m2.meta_value != ''
	        ", $type )
	    ) ;
	   

	    return $result;
	}	

	function get_min_all_property_price() {
		global $wpdb;
		$result = $wpdb->get_var(
	    "
	            SELECT min(m2.meta_value + 0)
	            FROM $wpdb->posts AS p
	            INNER JOIN $wpdb->postmeta AS m1 ON ( p.ID = m1.post_id )
				INNER JOIN $wpdb->postmeta AS m2  ON ( p.ID = m2.post_id )
				WHERE
				p.post_type = 'property'
				AND p.post_status = 'publish'
				AND ( m2.meta_key = '_price'  ) AND m2.meta_value != ''
	        "
	    ) ;

	    return $result;
	}	

	function get_max_all_property_price() {
		global $wpdb;
		$result = $wpdb->get_var(
	   "
	            SELECT max(m2.meta_value + 0)
	            FROM $wpdb->posts AS p
	            INNER JOIN $wpdb->postmeta AS m1 ON ( p.ID = m1.post_id )
				INNER JOIN $wpdb->postmeta AS m2  ON ( p.ID = m2.post_id )
				WHERE
				p.post_type = 'property'
				AND p.post_status = 'publish'
				AND ( m2.meta_key = '_price'  ) AND m2.meta_value != ''
	        "
	    ) ;
	   

	    return $result;
	}




	function realteo_handle_delete_media(){

	    if( isset($_REQUEST['media_id']) ){
	        $post_id = absint( $_REQUEST['media_id'] );
	        $status = wp_delete_attachment($post_id, true);
	        if( $status )
	            echo json_encode(array('status' => 'OK'));
	        else
	            echo json_encode(array('status' => 'FAILED'));
	    }
	    wp_die();
	}


	function realteo_handle_dropped_media() {
	    status_header(200);

	    $upload_dir = wp_upload_dir();
	    $upload_path = $upload_dir['path'] . DIRECTORY_SEPARATOR;
	    $num_files = count($_FILES['file']['tmp_name']);

	    $newupload = 0;
	    if(!empty($_POST)) {
	    	$property_id = $_POST['data'];
	    }

	    if ( !empty($_FILES) ) {
	        $files = $_FILES;
	        foreach($files as $file) {
	            $newfile = array (
	                    'name' => $file['name'],
	                    'type' => $file['type'],
	                    'tmp_name' => $file['tmp_name'],
	                    'error' => $file['error'],
	                    'size' => $file['size']
	            );

	            $_FILES = array('upload'=>$newfile);
	            foreach($_FILES as $file => $array) {
	            	if(isset($property_id) && $property_id != 0) {
						$newupload = media_handle_upload( $file, $property_id );
	            	} else {
	            		$newupload = media_handle_upload( $file, 0 );	
	            	}
	                
	            }
	        }
	    }

	    echo $newupload;    
	    wp_die();
	}


	/**
	 * Load plugin textdomain
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function load_plugin_textdomain () {
	    $domain = 'realteo';

	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain ()

	/**
	 * Main Realteo Instance
	 *
	 * Ensures only one instance of Realteo is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see Realteo()
	 * @return Main Realteo instance
	 */
	public static function instance ( $file = '', $version = '1.0.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}
		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __wakeup ()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install () {
		$this->_log_version_number();
		$this->init_user_roles();
	} // End install ()

	/**
	 * Log the plugin version number.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number () {
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number ()

	/**
	* Schedule cron jobs for Realteo events.
	*/
	public static function maybe_schedule_cron_jobs() {
	if ( ! wp_next_scheduled( 'realteo_check_for_expired_properties' ) ) {
		wp_schedule_event( time(), 'hourly', 'realteo_check_for_expired_properties' );
		}
	}

	function init_user_roles(){
		global $wp_roles;

		if ( class_exists( 'WP_Roles' ) && ! isset( $wp_roles ) ) {
			$wp_roles = new WP_Roles();
		}
 
		if ( is_object( $wp_roles ) ) {
				remove_role( 'agent' );
				add_role( 'agent', __( 'Agent', 'realteo' ), array(
					  	'level_1' => true,
						'read'                 => true,
						'upload_files'         => true,
						'edit_property'         => true,
						'read_property'         => true,
						'delete_property'       => true,
						'edit_properties'        => true,
						'delete_properties'      => true,
						'edit_properties'        => true,
						'assign_property_terms' 	=> true,
						
				) );

				remove_role( 'owner' );
				add_role( 'owner', __( 'Owner', 'realteo' ), array(
					  	'level_1' => true,
						'read'                 => true,
						'upload_files'         => true,
						'edit_property'         => true,
						'read_property'         => true,
						'delete_property'       => true,
						'edit_properties'        => true,
						'delete_properties'      => true,
						'edit_properties'        => true,
						'assign_property_terms' 	=> true,
				) );

				remove_role( 'buyer' );
				add_role( 'buyer', __( 'Buyer', 'realteo' ), array(
						'read'                 => true,
				) );

			$capabilities = array(
				'core' => array(
					'manage_properties'
				),
				'property' => array(
					"edit_property",
					"read_property",
					"delete_property",
					"edit_properties",
					"edit_others_properties",
					"publish_properties",
					"read_private_properties",
					"delete_properties",
					"delete_private_properties",
					"delete_published_properties",
					"delete_others_properties",
					"edit_private_properties",
					"edit_published_properties",
					"manage_property_terms",
					"edit_property_terms",
					"delete_property_terms",
					"assign_property_terms"
				));

			foreach ( $capabilities as $cap_group ) {
				foreach ( $cap_group as $cap ) {
					$wp_roles->add_cap( 'administrator', $cap );
				}
			}
		}

	}
	
}