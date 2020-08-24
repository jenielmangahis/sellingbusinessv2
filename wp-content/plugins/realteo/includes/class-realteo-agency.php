<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Realteo_Agency class
 */
class Realteo_Agency extends Realteo_Form { 

	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since  1.26
	 */
	private static $_instance = null;
	
	/**
	 * Agency ID.
	 *
	 * @access protected
	 * @var int
	 */
	protected $agency_id;


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
	/**
	 * Dashboard message.
	 *
	 * @access private
	 * @var string
	 */
	private $dashboard_message = '';

	/**
	 * Allows for accessing single instance of class. Class should only be constructed once per call.
	 *
	 * @since  1.26
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
	 * Constructor
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'register_post_types' ), 5 );
		add_filter( 'manage_edit-agency_columns', array( $this, 'custom_columns' ) );
		add_action( 'manage_agency_posts_custom_column', array( $this, 'custom_columns_manage' ) );
		
		// Add custom meta boxes
		add_action( 'cmb2_admin_init', array( $this, 'add_agency_meta_boxes' ) );
		add_action( "cmb2_save_field_realteo-agents", array( $this, 'action_cmb2_save_realteo_agents'), 10, 3 ); 

		add_shortcode( 'realteo_agency_managment', array( $this, 'realteo_agency_managment' ) );
		add_shortcode( 'realteo_agency_submit', array( $this, 'realteo_agency_submit' ) );

		add_action( 'wp', array( $this, 'process' ) ); //process submit form
		add_action( 'wp', array( $this, 'action_handler' ) ); //process actions

		add_action( 'wp_ajax_user_search', array( $this, 'user_search' ));
		add_action( 'wp_ajax_nopriv_user_search', array( $this, 'user_search' ) );


		add_action( 'wp_ajax_invite_agent', array( $this, 'invite_agent' ));
		add_action( 'wp_ajax_nopriv_invite_agent', array( $this, 'invite_agent' ) );

		add_action( 'wp_ajax_remove_agent', array( $this, 'remove_agent' ));
		add_action( 'wp_ajax_nopriv_remove_agent', array( $this, 'remove_agent' ) );

		add_action( 'template_redirect', array( $this, 'agency_pagination_fix'),0 );

		$this->steps  = (array) apply_filters( 'submit_agency_steps', array(

			'submit' => array(
				'name'     => __( 'Submit Agency', 'realteo' ),
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

		if ( isset( $_POST['step'] ) ) {
			$this->step = is_numeric( $_POST['step'] ) ? max( absint( $_POST['step'] ), 0 ) : array_search( $_POST['step'], array_keys( $this->steps ) );
		} elseif ( ! empty( $_GET['step'] ) ) {
			$this->step = is_numeric( $_GET['step'] ) ? max( absint( $_GET['step'] ), 0 ) : array_search( $_GET['step'], array_keys( $this->steps ) );
		}

		$this->agency_id = ! empty( $_REQUEST[ 'agency_id' ] ) ? absint( $_REQUEST[ 'agency_id' ] ) : 0;
		
		if(isset($_GET["action"]) && $_GET["action"] == 'edit' ) {
		 	$this->form_action = "editing";
		 	$this->agency_id = ! empty( $_GET[ 'agency_id' ] ) ? absint( $_GET[ 'agency_id' ] ) : 0;
		} 

		$this->agency_edit = false;
		if ( ! isset( $_GET[ 'new' ] ) && ( ! $this->agency_id ) && ! empty( $_COOKIE['realteo-submitting-agency-id'] ) && ! empty( $_COOKIE['realteo-submitting-agency-key'] ) ) {
			$agency_id     = absint( $_COOKIE['realteo-submitting-agency-id'] );
			$agency_status = get_post_status( $agency_id );

			if ( ( 'preview' === $agency_status || 'draft' === $agency_status ) && get_post_meta( $agency_id, '_submitting_key', true ) === $_COOKIE['realteo-submitting-agency-key'] ) {
				$this->agency_id = $agency_id;
				$this->agency_edit = get_post_meta( $agency_id, '_submitting_key', true );
				
			}
		}


         
		
	}

// Add/remove agent on the save field for cmb2 action
	public function action_cmb2_save_realteo_agents( $updated, $action, $instance ) { 
	  

	    if(isset($instance->data_to_save['realteo-agents'])) {
	    	$agents = $instance->data_to_save['realteo-agents'];
	    	$agency_id = (int) $instance->data_to_save['post_ID'];

	    	if(is_array($agents)) {
	    	
	    		foreach ($agents as $key => $user) {

	    			$current_user_agencies = get_user_meta($user, 'agency_agent_of', true);
	    			if ( is_array($current_user_agencies) && in_array($agency_id,$current_user_agencies)) {
	    				
	    				
	    			} else {
	    				if(is_array($current_user_agencies)){
							$new_agencies = array_merge($current_user_agencies, array($agency_id));
						} else {
							$new_agencies[] = $agency_id;
						}
						update_user_meta($user, 'agency_agent_of',$new_agencies);

	    			}
	    			
					$new_agencies = false;

	    		}
	    		$users = get_users();
	    		
			    if ( $users ) {
			        foreach ( $users as $user ) {
			        	if( !in_array( $user->ID, $agents) ) {
			        		
							$user_agencies = get_user_meta($user->ID, 'agency_agent_of', true);

							foreach (array_keys($user_agencies, $agency_id, true) as $key) {
								
							  	unset($user_agencies[$key]);
							}
						
							update_user_meta($user->ID, 'agency_agent_of',$user_agencies);
			        	}
			         // $user_options[ $user->ID ] = $user->user_login;
			        }
			    }
	    	}


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
	 * Gets the submitted agency ID.
	 *
	 * @return int
	 */
	public function get_agency_id() {
		return absint( $this->agency_id );
	}


	public static function register_post_types() {
		$labels = array(
			'name'               => __( 'Agencies', 'realteo' ),
			'singular_name'      => __( 'Agency', 'realteo' ),
			'add_new'            => __( 'Add New Agency', 'realteo' ),
			'add_new_item'       => __( 'Add New Agency', 'realteo' ),
			'edit_item'          => __( 'Edit Agency', 'realteo' ),
			'new_item'           => __( 'New Agency', 'realteo' ),
			'all_items'          => __( 'Agencies', 'realteo' ),
			'view_item'          => __( 'View Agency', 'realteo' ),
			'search_items'       => __( 'Search Agency', 'realteo' ),
			'not_found'          => __( 'No agencies found', 'realteo' ),
			'not_found_in_trash' => __( 'No agencies found in Trash', 'realteo' ),
			'parent_item_colon'  => '',
			'menu_name'          => __( 'Agencies', 'realteo' ),
		);

		register_post_type( 'agency',
			array(
				'labels'          	=> $labels,
				'show_in_menu'	  	=> true,
				'supports'        	=> array( 'title', 'editor','excerpt', 'thumbnail','custom-fields', 'author' ),
				'public'          	=> true,
				'show_ui'         	=> true,
				'show_in_nav_menus' => true,
				'menu_icon'			=> 'dashicons-clipboard',
				'has_archive'     	=> true,
				'rewrite'         	=> array( 'slug' => __( 'agencies', 'realteo' ) ),
				'categories'      	=> array(),
				'show_in_rest'		=> true,
			)
		);
	}

		/**
	 * Custom admin columns for post type
	 *
	 * @access public
	 * @return array
	 */
	public static function custom_columns() {
		$fields = array(
			'cb' 				=> '<input type="checkbox" />',
			'title' 			=> __( 'Title', 'realia' ),
			'agency-thumbnail' 		=> __( 'Logo', 'realia' ),
			'email'      		=> __( 'E-mail', 'realia' ),
			'web'      		    => __( 'Web', 'realia' ),
			'phone'      		=> __( 'Phone', 'realia' ),
			'agents'         	=> __( 'Agents', 'realia' ),
			'author' 			=> __( 'Author', 'realia' ),
		);

		return $fields;
	}

	/**
	 * Custom admin columns implementation
	 *
	 * @access public
	 * @param string $column
	 * @return array
	 */
	public static function custom_columns_manage( $column ) {
		switch ( $column ) {
			case 'agency-thumbnail':
				if ( has_post_thumbnail() ) {
					the_post_thumbnail( array(100, 100)  );
				} else {
					echo '-';
				}
				break;
			case 'email':
				$email = get_post_meta( get_the_ID(), 'realteo_email', true );

				if ( ! empty( $email ) ) {
					echo esc_attr( $email );
				} else {
					echo '-';
				}
				break;
			case 'web':
				$web = get_post_meta( get_the_ID(), 'realteo_web', true );

				if ( ! empty( $web ) ) {
					echo esc_attr( $web );
				} else {
					echo '-';
				}
				break;
			case 'phone':
				$phone = get_post_meta( get_the_ID(),  'realteo_phone', true );

				if ( ! empty( $phone ) ) {
					echo esc_attr( $phone );
				} else {
					echo '-';
				}
				break;
			case 'agents':
				
				$authors_id = get_post_meta(get_the_ID(),'realteo-agents',true);
				
					if($authors_id){
						$args = array(
							'include'  => $authors_id      
						);
						$wp_user_query = new WP_User_Query( $args );

						// Get the results
						$authors = $wp_user_query->get_results();
						
						echo '<ul class="list-4 color">';
						foreach($authors as $agent) {

						    $agent_info = get_userdata( $agent->ID ); ?>
							
							<li>
								<?php echo esc_html($agent_info->first_name); ?> <?php echo esc_html($agent_info->last_name); ?>
								<?php if( isset( $agent_info->agent_title ) ) : ?>
									<small>(<?php echo esc_html($agent_info->agent_title); ?>)</small>
									
								<?php endif; ?>
							</li>

						<?php } //eof foreach ?>
						</ul>

					<?php } 
				break;
		}
	}

	public static function agency_general_fields(){
		$fields = array(
				
				'license' => array(
					'id'                => 'license',
					'name'              => __( 'License', 'realteo' ),
					'label'              => __( 'License', 'realteo' ),
					'type'              => 'text',
				),
				'tax' => array(
					'id'                =>  'tax',
					'name'              => __( 'Tax Number', 'realteo' ),
					'label'              => __( 'Tax Number', 'realteo' ),
					'type'              => 'text',
				),
				'header_location' => array(
					'label'       => __( 'Location', 'realteo' ),
					'name'       => __( 'Location', 'realteo' ),
					'type'        => 'map',
					'required'    => false,
					'id'		  => 'location',
				),
				'_address' => array(
					'label'       => __( 'Google Maps Address', 'realteo' ),
					'name'       => __( 'Google Maps Address', 'realteo' ),
					'type'        => 'text',
					'required'    => true,
					'id'        => '_address',
					'placeholder' => '',
					'class'		  => '',
					
				),				
				'_friendly_address' => array(
					'name'       => __( 'Friendly Address', 'realteo' ),
					'label'       => __( 'Friendly Address', 'realteo' ),
					'type'        => 'text',
					'required'    => false,
					'id'        => '_friendly_address',
					'placeholder' => '',
					'tooltip'	  => __('Human readable address, if not set, the Google address will be used', 'realteo'),
					'class'		  => '',
					
				),				
				'_geolocation_long' => array(
					'name'       => __( 'Longitude', 'realteo' ),
					'label'       => __( 'Longitude', 'realteo' ),
					'type'        => 'hidden',
					'required'    => true,
					'placeholder' => '',
					'id'        => '_geolocation_long',
					'class'		  => 'hidden',
					
					
				),				
				'_geolocation_lat' => array(
					'label'       => __( 'Latitude', 'realteo' ),
					'name'       => __( 'Latitude', 'realteo' ),
					'type'        => 'hidden',
					'required'    => true,
					'placeholder' => '',
					'id'        => '_geolocation_lat',
					'class'		  => 'hidden',
				
				),
		
				
			);
		$fields = apply_filters( 'realteo_agency_general_fields', $fields );
		
		// Set meta box
		return $fields;
	}


	public static function agency_contact_fields() {
		$fields = array(
				'realteo_email' => array(
					'id'                =>  'realteo_email',
					'name'              => __( 'Email', 'realteo' ),
					'label'             => __( 'Email', 'realteo' ),
					'type'              => 'text',
					'icon_class'        => 'fa fa-envelope-o',
				),
				'realteo_phone' => array(
					'id'                => 'realteo_phone',
					'name'              => __( 'Phone', 'realteo' ),
					'label'             => __( 'Phone', 'realteo' ),
					'type'              => 'text',
					'icon_class'        => 'sl sl-icon-call-in',
				),
				'realteo_mobile' => array(
					'id'                => 'realteo_mobile',
					'name'              => __( 'Mobile', 'realteo' ),
					'label'             => __( 'Mobile', 'realteo' ),
					'type'              => 'text',
				),
				'realteo_fax' => array(
					'id'                => 'realteo_fax',
					'name'              => __( 'Fax', 'realteo' ),
					'label'             => __( 'Fax', 'realteo' ),
					'type'              => 'text',
				),				
				'realteo_website' => array(
					'id'                => 'realteo_website',
					'name'              => __( 'Website URL', 'realteo' ),
					'label'             => __( 'Website URL', 'realteo' ),
					'type'              => 'text',
					'icon_class'        => 'fa fa-external-link',
				),
				
				
			);
		$fields = apply_filters( 'realteo_agency_contact_fields', $fields );
		
		// Set meta box
		return $fields;
	}

	public static function agency_social_fields(){

		$fields = array(
				'facebook' => array(
					'id'                =>  'facebook',
					'name'              => __( 'Facebook URL', 'realteo' ),
					'label'              => __( 'Facebook URL', 'realteo' ),
					'type'              => 'text',
				),
				'twitter' => array(
					'id'                => 'twitter',
					'name'              => __( 'Twitter URL', 'realteo' ),
					'label'              => __( 'Twitter URL', 'realteo' ),
					'type'              => 'text',
				),
				'gplus' => array(
					'id'                => 'gplus',
					'name'              => __( 'Google Plus URL', 'realteo' ),
					'label'              => __( 'Google Plus URL', 'realteo' ),
					'type'              => 'text',
				),
				'instagram' => array(
					'id'                => 'instagram',
					'name'              => __( 'Instagram URL', 'realteo' ),
					'label'              => __( 'Instagram URL', 'realteo' ),
					'type'              => 'text',
				),					
			);
		$fields = apply_filters( 'realteo_agency_social_fields', $fields );
		
		// Set meta box
		return $fields;
	}

	/**
	 * Creates cmb2 meta boxes for agency custom post type
	 */
	public function add_agency_meta_boxes( ) {

		// contact fields
		$agency_general_options = array(
				'id'           => 'realteo_agency_general_metaboxes',
				'title'        => __( 'Agency General Informations', 'realteo' ),
				'object_types' => array( 'agency' ),
				'show_names'   => true,

		);
		$cmb_agency_general = new_cmb2_box( $agency_general_options );

		
		$general_fields = $this->agency_general_fields();
 		if ( $general_fields ) :
            foreach ( $general_fields as $field ){
            	if( !in_array( $field['type'], array('map','header') ) ) {
	                $cmb_agency_general->add_field( array(
	                    'name'      => __( $field['name'], 'cmb2' ),
	                    'id'        =>  $field['id'],
	                    'type'      => $field['type']
	                ) );
                }
            }
        endif;

        // contact fields
		$agency_options = array(
				'id'           => 'realteo_agency_metaboxes',
				'title'        => __( 'Agency Fields', 'realteo' ),
				'object_types' => array( 'agency' ),
				'show_names'   => true,

		);
		$cmb_agency = new_cmb2_box( $agency_options );

		$contact_fields = $this->agency_contact_fields();

 		if ( $contact_fields ) :
            foreach ( $contact_fields as $field ){
                $cmb_agency->add_field( array(
                    'name'      => __( $field['name'], 'cmb2' ),
                    'id'        =>  $field['id'],
                    'type'      => $field['type']
                ) );
            }
        endif;


		// social fields
		$social_fields = $this->agency_social_fields();


        $agency_social_options = array(
				'id'           => 'realteo_agency_social_metaboxes',
				'title'        => __( 'Agency Social Fields', 'realteo' ),
				'object_types' => array( 'agency' ),
				'show_names'   => true,

		);
		$cmb_agency_social = new_cmb2_box( $agency_social_options );

		if ( $social_fields ) :
            foreach ( $social_fields as $field ){
                $cmb_agency_social->add_field( array(
                    'name'      => __( $field['name'], 'cmb2' ),
                    'id'        =>  $field['id'],
                    'type'      => $field['type']
                ) );
            }
        endif;


         $agency_agents_options = array(
				'id'           => 'realteo_agency_agents',
				'title'        => __( 'Assigned Agents', 'realteo' ),
				'object_types' => array( 'agency' ),
				'show_names'   => true,

		);

		$cmb_agency_agents_options = new_cmb2_box( $agency_agents_options );

        $cmb_agency_agents_options->add_field(
        	array(
			    'name'    => __( 'Select Users', 'cmb2' ),
			    'desc'    => __( 'field description (optional)', 'cmb2' ),
			    'id'      => 'realteo-agents',
			    'type'    => 'multicheck',
			    'options' => realteo_cmb2_get_user_options( array( 'fields' => array( 'user_login' ) ) ),
			)
        );

	}

	/**
	 * Initializes the fields used in the form.
	 */
	public function init_fields() {

		if ( $this->fields ) {
			return;
		}
		
		$general_fields = $this->agency_general_fields();
		$contact_fields = $this->agency_contact_fields();
		$social_fields = $this->agency_social_fields();

		$main_fields = array(
			/**/
			'header1' => array(
					'label'       => __( 'Basic Information', 'realteo' ),
					'type'        => 'header',
				),
			'agency_title' => array(
						'label'       => __( 'Agency Name ', 'realteo' ),
						'type'        => 'text',
						'name'       => 'agency_title',
						'required'    => true,
						'placeholder' => '',
						'class'		  => '',
						'priority'    => 1
					),
			'_description' => array(
						'label'       => __( 'Description', 'realteo' ),
						'type'        => 'wp-editor',
						'required'    => true,
						
						'priority'    => 2
					),
			'_logo' => array(
						'label'       => __( 'Agency Logo', 'realteo' ),
						'type'        => 'file',
						'name'        => '_logo',
						'class'		  => '',
						'priority'    => 1,
						'required'    => false,
					),
			
		);


		$this->fields = array_merge($main_fields,$general_fields,$contact_fields,$social_fields);
		
	}

	/**
	 * Processes the form result and can also change view if step is complete.
	 */
	public function process() {

		// reset cookie
		if (
			isset( $_GET[ 'new' ] ) &&
			isset( $_COOKIE[ 'realteo-submitting-agency-id' ] ) &&
			isset( $_COOKIE[ 'realteo-submitting-agency-key' ] ) &&
			get_post_meta( $_COOKIE[ 'realteo-submitting-agency-id' ], '_submitting_key', true ) == $_COOKIE['realteo-submitting-agency-key']
		) {
			delete_post_meta( $_COOKIE[ 'realteo-submitting-agency-id' ], '_submitting_key' );
			setcookie( 'realteo-submitting-agency-id', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, false );
			setcookie( 'realteo-submitting-agency-key', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, false );

			wp_redirect( remove_query_arg( array( 'new', 'key' ), $_SERVER[ 'REQUEST_URI' ] ) );

		}

		$step_key = $this->get_step_key( $this->step );

		if(isset( $_POST[ 'agency_form' ] )) {
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
	public function realteo_agency_managment(){

		if ( ! is_user_logged_in() ) {
			return __( 'You need to be signed in to manage your agency.', 'realteo' );
		}
		
		
		
		ob_start();
		$template_loader = new Realteo_Template_Loader;
		$template_loader->set_template_data( array( 'current' => 'agency' ) )->get_template_part( 'account/navigation' ); 

		$current_user = wp_get_current_user();
		$args = array(
		  'author'        =>  $current_user->ID, 
		  'orderby'       =>  'post_date',
		  'order'         =>  'ASC',
		  'fields'        => 'ids',
		  'posts_per_page' => -1, // no limit
		  'post_type'	  => 'agency',
		  'post_status'	  => array('publish', 'pending', 'draft') 
		);
		$result_query = new WP_Query( $args );
		global $wpdb;
		$results  = $wpdb->get_col( $wpdb->prepare( "
			SELECT $wpdb->posts.ID FROM $wpdb->posts  
			WHERE 1=1 
			AND $wpdb->posts.post_author IN (%d) 
			AND $wpdb->posts.post_type = 'agency'
			AND (( $wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'draft' OR $wpdb->posts.post_status = 'pending'))  
			ORDER BY $wpdb->posts.post_date ASC
			", $current_user->ID ) );
		
  		$is_admin = $results;
  		
		//$is_admin = get_user_meta( $current_user->ID, 'agency_admin_of', true );
		$is_temp_agent = get_user_meta( $current_user->ID, 'agency_temp_agent_of', true );
		$is_agent = get_user_meta( $current_user->ID, 'agency_agent_of', true );

		$template_loader->set_template_data( 
			array( 
				'message' 		=> $this->dashboard_message, 
				'is_admin' 		=> $is_admin,
				'is_temp_agent' => $is_temp_agent,
				'is_agent' 		=> $is_agent,
			) 
		)->get_template_part( 'account/my_agency' ); 
		// jesli ma agencje -> edytor opcji agencji/ dodawniae nowych agentow
		// jelsi nie ma agencji -> formularz dodawnia agencji
		// jesli jest agentem w czyjejs agencji -> inforamcja o tym, mozliwosc usuniecia sie z agencji

		return ob_get_clean();

	}

	public function realteo_agency_submit(){
		if ( ! is_user_logged_in() ) {
			return __( 'You need to be signed in to manage your agency.', 'realteo' );
		}

			ob_start();
			$template_loader = new Realteo_Template_Loader;
			$template_loader->set_template_data( array( 'current' => 'agency' ) )->get_template_part( 'account/navigation' ); 
			$step_key = $this->get_step_key( $this->step );
			?>
			
				<div class="col-md-8">
					<div class="row"><?php $this->show_errors(); ?></div>
				</div>
			
			<?php
			

			if ( $step_key && is_callable( $this->steps[ $step_key ]['view'] ) ) {
				call_user_func( $this->steps[ $step_key ]['view'] );
			}	

		return ob_get_clean();
	}

	/**
	 * Displays the submit form.
	 */
	public function submit() {
		
		
		$this->init_fields();
		
		$template_loader = new Realteo_Template_Loader;
		if ( is_user_logged_in() && $this->agency_id ) {
			$agency = get_post( $this->agency_id );

			if($agency){
				foreach ( $this->fields as $key => $field ) {
						switch ( $key ) {
							case 'agency_title' :
								$this->fields[ $key ]['value'] = $agency->post_title;
							break;
							case '_description' :
								$this->fields[ $key ]['value'] = $agency->post_content;
							break;
						
							default:
								$this->fields[ $key ]['value'] = get_post_meta( $agency->ID, $key, true );
								
							break;
						}
					}
				
			}
			
		}  elseif ( is_user_logged_in() && empty( $_POST['submit_agency'] ) ) {
			$this->fields = apply_filters( 'submit_agency_form_fields_get_user_data', $this->fields, get_current_user_id() );

		}
		$template_loader->set_template_data( 
			array( 
				'action' 		=> $this->get_action(),
				'fields' 		=> $this->fields,
				'form'      	=> $this->form_name,
				'agency_edit' 	=> $this->agency_edit,
				'agency_id'   	=> $this->get_agency_id(),
				'step'      	=> $this->get_step(),
				'fields' 		=> $this->fields ) 
		)->get_template_part( 'account/agency_submit' ); 

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
		

			 if ( empty( $_POST['submit_agency'] ) ) {
			 	return;
			 }

			// // Validate required
			if ( is_wp_error( ( $return = $this->validate_fields( $values ) ) ) ) {
			 	throw new Exception( $return->get_error_message() );
			}


			if ( ! is_user_logged_in() ) {
				throw new Exception( __( 'You must be signed in to post a new agency.', 'realteo' ) );
			}

			// Update the agency
			$this->save_agency( $values['agency_title'], $values['_description'], $this->agency_id ? '' : 'draft', $values );
			$this->update_agency_data( $values );

			// Successful, show next step
			$this->step++;


		} catch ( Exception $e ) {

			$this->add_error( $e->getMessage() );
			return;
		}
	}

	/**
	 * Displays preview of agency Listing.
	 */
	public function preview() {
		global $post, $agency_preview;
		
		if ( $this->agency_id ) {
			$agency_preview       = true;
			$post              = get_post( $this->agency_id );
			$post->post_status = 'preview';

			setup_postdata( $post );

			$template_loader = new Realteo_Template_Loader;
			$template_loader->set_template_data( 
			array( 
				'action' 		=> $this->get_action(),
				'fields' 		=> $this->fields,
				'form'      	=> $this->form_name,
				'post'      	=> $post,
				'agency_id'   => $this->get_agency_id(),
				'step'      	=> $this->get_step(),
				'submit_button_text' => apply_filters( 'submit_agency_form_preview_button_text', __( 'Submit', 'realteo' ) )
				) 
			)->get_template_part( 'account/agency_preview' );

			wp_reset_postdata();
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
			throw new Exception( __( 'You must be signed in to post a new agency.', 'realteo' ) );
		}

		// Edit = show submit form again
		if ( ! empty( $_POST['edit_agency'] ) ) {
			$this->step --;
		}

		// Continue = change agency status then show next screen
		if ( ! empty( $_POST['continue'] ) ) {

			$agency = get_post( $this->agency_id );

			if ( in_array( $agency->post_status, array( 'draft','preview', 'expired' ) ) ) {
				// Reset expiry
			
				// Update agency listing
				$update_agency                  = array();
				$update_agency['ID']            = $agency->ID;
				if($this->form_action == "editing") {
					$update_agency['post_status'] == $agency->post_status;
				} else {
					$update_agency['post_status']   = 'pending';
				}
				$update_agency['post_date']     = current_time( 'mysql' );
				$update_agency['post_date_gmt'] = current_time( 'mysql', 1 );
				$update_agency['post_author']   = get_current_user_id();
				wp_update_post( $update_agency );
			}

			$this->step ++;
		}
	}

	/**
	 * Validates the posted fields.
	 *
	 * @param array $values
	 * @throws Exception Uploaded file is not a valid mime-type or other validation error
	 * @return bool|WP_Error True on success, WP_Error on failure
	 */
	protected function validate_fields( $values ) {	


			foreach ( $this->fields as $key => $field ) {
				if ( $field['type'] != 'header' && isset($field['required'])  && $field['required'] && empty( $values[ $key ] ) ) {
					return new WP_Error( 'validation-error', sprintf( __( '%s is a required field', 'realteo' ), $field['label'] ) );
				}
				if ( ! empty( $field['taxonomy'] ) && in_array( $field['type'], array( 'term-checkboxes', 'term-select', 'term-multiselect' ) ) ) {
					if ( is_array( $values[ $key ] ) ) {
						$check_value = $values[ $key ];
					} else {
						$check_value = empty( $values[ $key ] ) ? array() : array( $values[ $key ] );
					}

					foreach ( $check_value as $term ) {
						if ( ! term_exists( (int) $term, $field['taxonomy'] ) ) {
							return new WP_Error( 'validation-error', sprintf( __( '%s is invalid', 'realteo' ), $field['label'] ) );
						}
					}
				}
				if ( 'file' === $field['type'] && ! empty( $field['allowed_mime_types'] ) ) {
					if ( is_array( $values[ $key ] ) ) {
						$check_value = array_filter( $values[ $key ] );
					} else {
						$check_value = array_filter( array( $values[ $key ] ) );
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
		
	
		return apply_filters( 'submit_agency_form_validate_fields', true, $this->fields, $values );
	}

	/**
	 * Updates or creates a agency listing from posted data.
	 *
	 * @param  string $post_title
	 * @param  string $post_content
	 * @param  string $status
	 * @param  array  $values
	 * @param  bool   $update_slug
	 */
	protected function save_agency( $post_title, $post_content, $status = 'pending', $values = array(), $update_slug = true ) {
		$agency_data = array(
			'post_title'     => $post_title,
			'post_content'   => $post_content,
			'post_type'      => 'agency',
			'comment_status' => 'closed'
		);

		if ( $update_slug ) {
			$agency_slug   = array();

			$agency_slug[]            = $post_title;
			$agency_data['post_name'] = sanitize_title( implode( '-', $agency_slug ) );
		}

		if ( $status ) {
			$agency_data['post_status'] = $status;
		}

		$agency_data = apply_filters( 'submit_agency_form_save_agency_data', $agency_data, $post_title, $post_content, $status, $values );

		if ( $this->agency_id ) {
			$agency_data['ID'] = $this->agency_id;
			wp_update_post( $agency_data );
		} else {
			$this->agency_id = wp_insert_post( $agency_data );
			$current_user = wp_get_current_user();

		
			$new_agencies = array();
			$current_user_agencies = get_user_meta($current_user->ID, 'agency_admin_of', true);
			if(is_array($current_user_agencies)){
				$new_agencies = array_merge($current_user_agencies, array($this->agency_id));
			} else {
				$new_agencies[] = $this->agency_id;
			}
			update_user_meta( $current_user->ID, 'agency_admin_of', $new_agencies);


			if ( ! headers_sent() ) {
				$submitting_key = uniqid();

				setcookie( 'realteo-submitting-agency-id', $this->agency_id, false, COOKIEPATH, COOKIE_DOMAIN, false );
				setcookie( 'realteo-submitting-agency-key', $submitting_key, false, COOKIEPATH, COOKIE_DOMAIN, false );

				update_post_meta( $this->agency_id, '_submitting_key', $submitting_key );
			}
		}
	}

	/**
	 * Sets agency meta and terms based on posted values.
	 *
	 * @param  array $values
	 */
	protected function update_agency_data( $values ) {
		// Set defaults

		$maybe_attach = array();

		// Loop fields and save meta and term data

		foreach ($this->fields as $key => $field ) {
			// Save taxonomies
			
			if ( ! empty( $field['taxonomy'] ) ) {
				if ( is_array( $values[ $key ] ) ) {

					/*TODO - fix the damn region string*/
					wp_set_object_terms( $this->agency_id, $values[ $key ], $field['taxonomy'], false );
				} else {
					wp_set_object_terms( $this->agency_id, array( intval($values[ $key ]) ), $field['taxonomy'], false );
				}

			// Company logo is a featured image
			} elseif ( '_logo' === $key ) {
			
				$attachment_id = is_numeric( $values[ $key ] ) ? absint( $values[ $key ] ) : $this->create_attachment( $values[ $key ] );
				if ( empty( $attachment_id ) ) {
					delete_post_thumbnail( $this->agency_id );
				} else {
					set_post_thumbnail( $this->agency_id, $attachment_id );
				}
				update_post_meta( $this->agency_id, $key, $values[ $key ] );

			// Save meta data
			} else {
				update_post_meta( $this->agency_id, $key, $values[ $key ] );
				
				if($key == '_gallery') {
					$ids = $values[ $key ];
					if(is_array($ids) && !empty($ids)){
						foreach ($ids as $key => $value) {
							$attachment = array(
				                'ID' => $key,
				                'post_parent' => $this->agency_id,
				            );

				            $res = wp_update_post($attachment);
						}
					}
				}

				// Handle attachments
				if ( 'file' === $field['type'] ) {
					$attachment_id = is_numeric( $values[ $key ] ) ? absint( $values[ $key ] ) : $this->create_attachment( $values[ $key ] );
			
					update_post_meta( $this->agency_id, $key.'_id', $attachment_id  );
	
				}
			}
			
		}

		
		

		do_action( 'realteo_update_agency_data', $this->agency_id, $values );
	}

	/**
	 * Displays the final screen after a agency listing has been submitted.
	 */
	public function done() {
		do_action( 'realteo_agency_submitted', $this->agency_id );
		$template_loader = new Realteo_Template_Loader;

		$template_loader->set_template_data( 
			array( 
				'agency' 	=>  get_post( $this->agency_id ),
				'id' 		=> 	$this->agency_id,
				) 
			)->get_template_part( 'account/agency_submitted' );
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
			'post_title'   => get_the_title( $this->agency_id ),
			'post_content' => '',
			'post_status'  => 'inherit',
			'post_parent'  => $this->agency_id,
			'guid'         => $attachment_url
		);

		if ( $info = wp_check_filetype( $attachment_url ) ) {
			$attachment['post_mime_type'] = $info['type'];
		}

		$attachment_id = wp_insert_attachment( $attachment, $attachment_url, $this->agency_id );

		if ( ! is_wp_error( $attachment_id ) ) {
			wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $attachment_url ) );
			return $attachment_id;
		}

		return 0;
	}

	public function user_search() {

		$search 	= $_POST['search'];
		$agency_id 	= $_POST['agency_id'];

		if(empty($search)){
			echo esc_html__('Please provide search term','realteo');
			die();
		}
		$agents_of = get_post_meta($agency_id, 'realteo-agents', true);
		$temp_agents_of = get_post_meta($agency_id, 'realteo-temp-agents', true);
		$exclude = array_merge($agents_of,$temp_agents_of);
		$suggestions = array();

		$args = array(
			'search'         => $search,
			'search_columns' => array( 'user_login', 'user_email','user_nicename','display_name' ),
			'exclude'		=> $exclude
		);
		$wp_user_query = new WP_User_Query( $args );
		
		// Get the results
		$agents = $wp_user_query->get_results();
		$output = '';
		if ( ! empty( $agents ) ) {
			$output .= '<ul class="list-1">';
			foreach ( $agents as $author ) {
				$author_info = get_userdata( $author->ID );
				$author_name =  (empty($author_info->first_name) && empty($author_info->last_name)) ? $author_info->user_login : $author_info->first_name . ' ' . $author_info->last_name;
				$output .= '<li><a class="invite-agent-link" data-agency="'.$agency_id.'" data-agent="'.$author->ID.'"  href="">Invite '.$author_name.'</a></li>';	
			}
			$output .= "</ul>";
		} else {
			$output = esc_html__('There are no agents matching this criteria','realteo');
		}

		echo $output;
    	die();
	}

	// Same handler function...
	// AJAX function run on inviting agents
	function invite_agent() {

		$agent_id = intval( $_POST['agent_id'] );
		$agency_id = intval( $_POST['agency_id'] );
		
		//get current agents and check if this one is there
		$agents_of = get_post_meta($agency_id, 'realteo-agents', true);

		if( is_array($agents_of) && in_array($agent_id,$agents_of) ) {

			$result['type'] = 'error';
			$result['message'] = __( 'This agent is already assigned to your agency' , 'realteo' );	
			$result = json_encode($result);
		    echo $result;

			wp_die();
		} 

		//get invited not confirmed agents and check if this one is there
		$temp_agents_of = get_post_meta($agency_id, 'realteo-temp-agents', true);
		if( is_array($agents_of) && in_array($agent_id,$temp_agents_of) ) {
			$result['type'] = 'error';
			$result['message'] = __( 'This agent is already invited to your agency. Please wait for his confirmation' , 'realteo' );
			$result = json_encode($result);
		    echo $result;

			wp_die();
		} 

		//both false, so add
		if(is_array($temp_agents_of)){
			$new_temp_agents = array_merge($temp_agents_of, array($agent_id));
		} else {
			$new_temp_agents[] = $agent_id;
		}
		
		$action = update_post_meta( $agency_id, 'realteo-temp-agents', $new_temp_agents );
		if($action === false) {
			$result['type'] = 'error';
			$result['message'] = __( 'Oops, something went wrong, please try again' , 'realteo' );
		} else {
			$result['type'] = 'success';
			$result['message'] = __( 'Invitation was sent ' , 'realteo' );
			//action to set email.
			$new_temp_agencies = array();
	
			$current_user_temp_agencies = get_user_meta($agent_id, 'agency_temp_agent_of', true);
			if(is_array($current_user_temp_agencies)){
				$new_temp_agencies = array_merge($current_user_temp_agencies, array($agency_id));
			} else {
				$new_temp_agencies[] = $agency_id;
			}
			update_user_meta($agent_id,'agency_temp_agent_of', $new_temp_agencies ) ;
			do_action( 'realteo_agent_invited', $agent_id, $agency_id );
		}

		$result = json_encode($result);
	    echo $result;

		wp_die();
	}

	function remove_agent() {
	

		$agent_id = intval( $_POST['agent_id'] );
		$agency_id = intval( $_POST['agency_id'] );
		$result = array();
		//get current agents and check if this one is there
		$agents_of = get_post_meta($agency_id, 'realteo-agents', true);

		if( is_array($agents_of) && in_array($agent_id,$agents_of) ) {
		
			foreach (array_keys($agents_of, $agent_id, true) as $key) {
				unset($agents_of[$key]);
			}

			//agent temp agencies (hold IDS of agency CPT)
			$current_user_agencies = get_user_meta($agent_id, 'agency_agent_of', true);
			// remove agency from temp agent
			foreach (array_keys($current_user_agencies, $agency_id, true) as $key) {
			  unset($current_user_agencies[$key]);
			}
			update_user_meta($agent_id, 'agency_agent_of',$current_user_agencies);
			
			$action = update_post_meta( $agency_id, 'realteo-agents', $agents_of );
			if($action === false) {
				$result['type'] = 'error';
				$result['message'] = __( 'Oops, something went wrong, please try again' , 'realteo' );
			} else {
				$result['type'] = 'success';
				$result['message'] = __( 'Agent was removed from your agency' , 'realteo' );
			}

			
		} 
		$result = json_encode($result);
	    echo $result;

		wp_die();
		

	}

	/**
	 * Actions in dashboard
	 */
	public function action_handler() {
		global $post;

		if ( is_page(realteo_get_option( 'agency_page' ) ) ) {
			if ( ! empty( $_REQUEST['action'] ) && ! empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'realteo_my_agency_actions' ) ) {

			$action = sanitize_title( $_REQUEST['action'] );
			$agency_id = absint( $_REQUEST['agency_id'] );
			$current_user = wp_get_current_user();

			try {
				// Get Job
				$agency    = get_post( $agency_id );
				if($agency){
					$title = $agency->post_title;
				} else {
					$title = false;
				}
				
				$current_user = wp_get_current_user();
				switch ( $action ) {
					case 'delete' :
						// Trash it
						wp_trash_post( $agency_id );

						// Message
						$this->dashboard_message =  '<div class="notification closeable success"><p>' . sprintf( __( '%s has been deleted', 'realteo' ), $title ) . '</p><a class="close" href="#"></a></div>';

						break;

					case 'reject' :
						// Trash it
						//agency temp agents (hold IDS of users)
						$temp_agents_of = get_post_meta($agency_id, 'realteo-temp-agents', true);
						// remove agency from temp agent
						foreach (array_keys($temp_agents_of, $current_user->ID, true) as $key) {
						  unset($temp_agents_of[$key]);
						}   
						update_post_meta($agency_id, 'realteo-temp-agents', $temp_agents_of);
						
						//agent temp agencies (hold IDS of agency CPT)
						$current_user_temp_agencies = get_user_meta($current_user->ID, 'agency_temp_agent_of', true);
						// remove agency from temp agent
						foreach (array_keys($current_user_temp_agencies, $agency_id, true) as $key) {
						  unset($current_user_temp_agencies[$key]);
						}
						update_user_meta($current_user->ID, 'agency_temp_agent_of',$current_user_temp_agencies);
				
						// Message
						$this->dashboard_message =  '<div class="notification closeable success"><p>' . sprintf( __( 'Invitation to agency %s has been rejected', 'realteo' ), $title ) . '</p><a class="close" href="#"></a></div>';

					break;
				
					case 'confirm' :
						// check if already is agent?
						$agents_of = get_post_meta($agency_id, 'realteo-agents', true);
						if(is_array($agents_of) && in_array($current_user->ID,$agents_of)) {
							//already an agent so stop it
							break;
						}
						//agency temp agents (hold IDS of users)
						$temp_agents_of = get_post_meta($agency_id, 'realteo-temp-agents', true);
						// remove agency from temp agent
						foreach (array_keys($temp_agents_of, $current_user->ID, true) as $key) {
						  unset($temp_agents_of[$key]);
						}   
						update_post_meta($agency_id, 'realteo-temp-agents', $temp_agents_of);
						
						//agent temp agencies (hold IDS of agency CPT)
						$current_user_temp_agencies = get_user_meta($current_user->ID, 'agency_temp_agent_of', true);
						// remove agency from temp agent
						foreach (array_keys($current_user_temp_agencies, $agency_id, true) as $key) {
						  unset($current_user_temp_agencies[$key]);
						}
						update_user_meta($current_user->ID, 'agency_temp_agent_of',$current_user_temp_agencies);

						// add user as agency meta
						$agents_of = get_post_meta($agency_id, 'realteo-agents', true);
						if(is_array($agents_of)){
							$new_agents = array_merge($agents_of, array($current_user->ID));
						} else {
							$new_agents[] = $current_user->ID;
						}
						update_post_meta($agency_id,'realteo-agents', $new_agents );
						
						//add agency to user meta

						$current_user_agencies = get_user_meta($current_user->ID, 'agency_agent_of', true);
						if(is_array($current_user_agencies)){
							$new_agencies = array_merge($current_user_agencies, array($agency_id));
						} else {
							$new_agencies[] = $agency_id;
						}
						update_user_meta($current_user->ID, 'agency_agent_of',$new_agencies);

						// Message
						$this->dashboard_message =  '<div class="notification closeable success"><p>' . sprintf( __( 'You are now agent in %s', 'realteo' ), $title ) . '</p><a class="close" href="#"></a></div>';

					break;

					case 'remove' :
						// Trash it
						//agency agents (hold IDS of users)
						$agents_of = get_post_meta($agency_id, 'realteo-agents', true);
						// remove agency from  agent

						if(is_array($agents_of)){
							foreach (array_keys($agents_of, $current_user->ID, true) as $key) {
							  unset($agents_of[$key]);
							}   
							update_post_meta($agency_id, 'realteo-agents', $agents_of);
						}
						//agent temp agencies (hold IDS of agency CPT)
						$current_user_agencies = get_user_meta($current_user->ID, 'agency_agent_of', true);
						// remove agency from temp agent
						foreach (array_keys($current_user_agencies, $agency_id, true) as $key) {
						  	unset($current_user_agencies[$key]);
						}
						update_user_meta($current_user->ID, 'agency_agent_of',$current_user_agencies);

						// Message
						$this->dashboard_message =  '<div class="notification closeable success"><p>' . sprintf( __( 'You have been removed from agency %s', 'realteo' ), $title ) . '</p><a class="close" href="#"></a></div>';

					break;

					case 'remove_admin' :
						// Trash it
						//agency agents (hold IDS of users)
						
						
						//agent temp agencies (hold IDS of agency CPT)
						$current_admin_agencies = get_user_meta($current_user->ID, 'agency_admin_of', true);
						// remove agency from temp agent
						foreach (array_keys($current_admin_agencies, $agency_id, true) as $key) {
						  	unset($current_admin_agencies[$key]);
						}
						update_user_meta($current_user->ID, 'agency_admin_of',$current_admin_agencies);

						// Message
						$this->dashboard_message =  '<div class="notification closeable success"><p>' . sprintf( __( 'You have been removed from agency %s', 'realteo' ), $title ) . '</p><a class="close" href="#"></a></div>';

					break;
					
					default :
						do_action( 'realteo_dashboard_do_action_' . $action );
						break;
				}

				do_action( 'realteo_my_agency_do_action', $action, $current_user->ID );

			} catch ( Exception $e ) {
				$this->dashboard_message = '<div class="notification closeable error">' . $e->getMessage() . '</div>';
			}
		}
		}
	}

	function agency_pagination_fix() {
	    if ( is_singular( 'agency' ) ) {
	        global $wp_query;
	        $page = ( int ) $wp_query->get( 'page' );
	        if ( $page > 1 ) {
	            // convert 'page' to 'paged'
	            $query->set( 'page', 1 );
	            $query->set( 'paged', $page );
	        }
	        // prevent redirect
	        remove_action( 'template_redirect', 'redirect_canonical' );
	    }
	}

}