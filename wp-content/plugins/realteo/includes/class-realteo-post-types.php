<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * WP_property_Manager_Content class.
 */
class Realteo_Post_Types {

	/**
	 * The single instance of the class.
	 *
	 * @var self
	 * @since  1.26
	 */
	private static $_instance = null;

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
		add_action( 'manage_property_posts_custom_column', array( $this, 'custom_columns' ), 2 );
		add_filter( 'manage_edit-property_columns', array( $this, 'columns' ) );

		add_action( 'pending_to_publish', array( $this, 'set_expiry' ) );
		add_action( 'pending_payment_to_publish', array( $this, 'set_expiry' ) );
		add_action( 'preview_to_publish', array( $this, 'set_expiry' ) );
		add_action( 'draft_to_publish', array( $this, 'set_expiry' ) );
		add_action( 'auto-draft_to_publish', array( $this, 'set_expiry' ) );
		add_action( 'expired_to_publish', array( $this, 'set_expiry' ) );

		add_action( 'realteo_check_for_expired_properties', array( $this, 'check_for_expired' ) );

		add_action( 'admin_init', array( $this, 'approve_property' ) );
		add_action( 'admin_notices', array( $this, 'action_notices' ) );

		add_action( 'bulk_actions-edit-property', array( $this, 'add_bulk_actions' ) );
		add_action( 'handle_bulk_actions-edit-property', array( $this, 'do_bulk_actions' ), 10, 3 );


		//permalinks mod
		// Add our custom permastructures for custom taxonomy and post
		
		if(realteo_get_option_with_name('realteo_general_options', 'region_in_links' )) {
			add_action( 'wp_loaded', array( $this, 'add_properties_permastructure' ) );
			add_filter( 'post_type_link', array( $this,'property_permalinks' ), 10, 2 );
			add_filter( 'term_link', array( $this,'add_term_parents_to_permalinks'), 10, 2 );
		}
		
	}


	function add_properties_permastructure() {
		global $wp_rewrite;
		$slug = apply_filters( 'realteo_rewrite_property_slug', 'property' );
		add_permastruct( 'region', $slug.'/%region%', false );
		add_permastruct( 'property', $slug.'/%region%/%property%', false );
	}

	function property_permalinks( $permalink, $post ) {
		if ( $post->post_type !== 'property' )
			return $permalink;
		$terms = get_the_terms( $post->ID, 'region' );
		
		if ( ! $terms )
			return str_replace( '%region%/', '', $permalink );
		$post_terms = array();
		foreach ( $terms as $term )
			$post_terms[] = $term->slug;
		return str_replace( '%region%', implode( ',', $post_terms ) , $permalink );
	}

	// Make sure that all term links include their parents in the permalinks
	
	function add_term_parents_to_permalinks( $permalink, $term ) {
		$term_parents = $this->get_term_parents( $term );
		foreach ( $term_parents as $term_parent )
			$permlink = str_replace( $term->slug, $term_parent->slug . ',' . $term->slug, $permalink );
		return $permalink;
	}

	function get_term_parents( $term, &$parents = array() ) {
		$parent = get_term( $term->parent, $term->taxonomy );
		
		if ( is_wp_error( $parent ) )
			return $parents;
		
		$parents[] = $parent;
		if ( $parent->parent )
			get_term_parents( $parent, $parents );
	    return $parents;
	}

	/**
	 * register_post_types function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_post_types() {
	/*
		if ( post_type_exists( "property" ) )
			return;*/

		// Custom admin capability
		$admin_capability = 'edit_properties';
	
		// Set labels and localize them
	
		$property_name		= apply_filters( 'realteo_taxonomy_property_name', __( 'Properties', 'realteo' ) );
		$property_singular	= apply_filters( 'realteo_taxonomy_property_singular', __( 'Property', 'realteo' ) );
	
		register_post_type( "property",
			apply_filters( "register_post_type_property", array(
				'labels' => array(
					'name'					=> $property_name,
					'singular_name' 		=> $property_singular,
					'menu_name'             => esc_html__( 'Properties', 'realteo' ),
					'all_items'             => sprintf( esc_html__( 'All %s', 'realteo' ), $property_name ),
					'add_new' 				=> esc_html__( 'Add New', 'realteo' ),
					'add_new_item' 			=> sprintf( esc_html__( 'Add %s', 'realteo' ), $property_singular ),
					'edit' 					=> esc_html__( 'Edit', 'realteo' ),
					'edit_item' 			=> sprintf( esc_html__( 'Edit %s', 'realteo' ), $property_singular ),
					'new_item' 				=> sprintf( esc_html__( 'New %s', 'realteo' ), $property_singular ),
					'view' 					=> sprintf( esc_html__( 'View %s', 'realteo' ), $property_singular ),
					'view_item' 			=> sprintf( esc_html__( 'View %s', 'realteo' ), $property_singular ),
					'search_items' 			=> sprintf( esc_html__( 'Search %s', 'realteo' ), $property_name ),
					'not_found' 			=> sprintf( esc_html__( 'No %s found', 'realteo' ), $property_name ),
					'not_found_in_trash' 	=> sprintf( esc_html__( 'No %s found in trash', 'realteo' ), $property_name ),
					'parent' 				=> sprintf( esc_html__( 'Parent %s', 'realteo' ), $property_singular ),
				),
				'description' => sprintf( esc_html__( 'This is where you can create and manage %s.', 'realteo' ), $property_name ),
				'public' 				=> true,
				'show_ui' 				=> true,
				'capability_type' 		=> array( 'property', 'properties' ),
				'map_meta_cap'          => true,
				'publicly_queryable' 	=> true,
				'exclude_from_search' 	=> false,
				'hierarchical' 			=> false,
				'menu_icon'           => 'dashicons-admin-multisite',
				'rewrite' 				=> array(
						'slug'       => apply_filters( 'realteo_rewrite_property_slug', 'property' ),
						'with_front' => true,
						'feeds'      => true,
						'pages'      => true
					),
				'query_var' 			=> true,
				'supports' 				=> array( 'title', 'author','editor', 'custom-fields', 'publicize', 'thumbnail','comments' ),
				'has_archive' 			=> apply_filters( 'realteo_rewrite_property_archive_slug', 'properties' ),
				'show_in_nav_menus' 	=> true
			) )
		);


		register_post_status( 'preview', array(
			'label'                     => _x( 'Preview', 'post status', 'realteo' ),
			'public'                    => false,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Preview <span class="count">(%s)</span>', 'Preview <span class="count">(%s)</span>', 'realteo' ),
		) );

		register_post_status( 'expired', array(
			'label'                     => _x( 'Expired', 'post status', 'realteo' ),
			'public'                    => false,
			'protected'                 => true,
			'exclude_from_search'       => true,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Expired <span class="count">(%s)</span>', 'Expired <span class="count">(%s)</span>', 'realteo' ),
		) );

		register_post_status( 'pending_payment', array(
			'label'                     => _x( 'Pending Payment', 'post status', 'realteo' ),
			'public'                    => false,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => false,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Pending Payment <span class="count">(%s)</span>', 'Pending Payment <span class="count">(%s)</span>', 'realteo' ),
		) );


		
		// Register taxonomy "Features"
		$singular  = __( 'Feature', 'realteo' );
		$plural    = __( 'Features', 'realteo' );	
		$rewrite   = array(
			'slug'         => _x( 'property-feature', 'Feature slug - resave permalinks after changing this', 'realteo' ),
			'with_front'   => false,
			'hierarchical' => false
		);
		$public    = true;
		register_taxonomy( "property_feature",
			apply_filters( 'register_taxonomy_property_features_object_type', array( 'property' ) ),
       	 	apply_filters( 'register_taxonomy_property_features_args', array(
	            'hierarchical' 			=> true,
	            /*'update_count_callback' => '_update_post_term_count',*/
	            'label' 				=> $plural,
	            'labels' => array(
					'name'              => $plural,
					'singular_name'     => $singular,
					'menu_name'         => ucwords( $plural ),
					'search_items'      => sprintf( __( 'Search %s', 'realteo' ), $plural ),
					'all_items'         => sprintf( __( 'All %s', 'realteo' ), $plural ),
					'parent_item'       => sprintf( __( 'Parent %s', 'realteo' ), $singular ),
					'parent_item_colon' => sprintf( __( 'Parent %s:', 'realteo' ), $singular ),
					'edit_item'         => sprintf( __( 'Edit %s', 'realteo' ), $singular ),
					'update_item'       => sprintf( __( 'Update %s', 'realteo' ), $singular ),
					'add_new_item'      => sprintf( __( 'Add New %s', 'realteo' ), $singular ),
					'new_item_name'     => sprintf( __( 'New %s Name', 'realteo' ),  $singular )
            	),
	            'show_ui' 				=> true,
	            'show_tagcloud'			=> false,
	            'public' 	     		=> $public,
	            /*'capabilities'			=> array(
	            	'manage_terms' 		=> $admin_capability,
	            	'edit_terms' 		=> $admin_capability,
	            	'delete_terms' 		=> $admin_capability,
	            	'assign_terms' 		=> $admin_capability,
	            ),*/
	            'rewrite' 				=> $rewrite,
	        ) )
	    );		

	    // Register taxonomy "Region"
		$singular  = __( 'Region', 'realteo' );
		$plural    = __( 'Regions', 'realteo' );	
		$rewrite   = array(
			'slug'         => _x( 'region', 'Region slug - resave permalinks after changing this', 'realteo' ),
			'with_front'   => true,
			'hierarchical' => false
		);
		$public    = true;
		register_taxonomy( "region",
			apply_filters( 'register_taxonomy_region_object_type', array( 'property' ) ),
       	 	apply_filters( 'register_taxonomy_region_args', array(
	            'hierarchical' 			=> true,
	            'update_count_callback' => '_update_post_term_count',
	            'label' 				=> $plural,
	            'labels' => array(
					'name'              => $plural,
					'singular_name'     => $singular,
					'menu_name'         => ucwords( $plural ),
					'search_items'      => sprintf( __( 'Search %s', 'realteo' ), $plural ),
					'all_items'         => sprintf( __( 'All %s', 'realteo' ), $plural ),
					'parent_item'       => sprintf( __( 'Parent %s', 'realteo' ), $singular ),
					'parent_item_colon' => sprintf( __( 'Parent %s:', 'realteo' ), $singular ),
					'edit_item'         => sprintf( __( 'Edit %s', 'realteo' ), $singular ),
					'update_item'       => sprintf( __( 'Update %s', 'realteo' ), $singular ),
					'add_new_item'      => sprintf( __( 'Add New %s', 'realteo' ), $singular ),
					'new_item_name'     => sprintf( __( 'New %s Name', 'realteo' ),  $singular )
            	),
	            'show_ui' 				=> true,
	            'show_tagcloud'			=> false,
	            'public' 	     		=> $public,
	           /* 'capabilities'			=> array(
	            	'manage_terms' 		=> $admin_capability,
	            	'edit_terms' 		=> $admin_capability,
	            	'delete_terms' 		=> $admin_capability,
	            	'assign_terms' 		=> $admin_capability,
	            ),*/
	            'rewrite' 				=> $rewrite,
	        ) )
	    );
			
		
	} /* eof register*/

	/**
	 * Adds columns to admin listing of property Listings.
	 *
	 * @param array $columns
	 * @return array
	 */
	public function columns( $columns ) {
		if ( ! is_array( $columns ) ) {
			$columns = array();
		}
		
		$columns["property_type"]     	 	= __( "Type", 'realteo');
		$columns["property_region"]      	= __( "Region", 'realteo');
		$columns["property_address"]     	= __( "Address", 'realteo');
		$columns["property_posted"]      	= __( "Posted", 'realteo');
		$columns["expires"]           		= __( "Expires", 'realteo');
		$columns['featured_property']       = '<span class="tips" data-tip="' . __( "Featured?", 'realteo') . '">' . __( "Featured?", 'realteo') . '</span>';
		$columns['property_actions']          = __( "Actions", 'realteo');
		return $columns;
	}

	/**
	 * Displays the content for each custom column on the admin list for property Listings.
	 *
	 * @param mixed $column
	 */
	public function custom_columns( $column ) {
		global $post;

		switch ( $column ) {
			case "property_type" :
				the_property_offer_type($post);
			break;
			
			case "property_address" :
				the_property_address( $post );
			break;

			case "property_region" :
				$terms = get_the_terms( $post->ID, 'region');
				if ( ! $terms ) {
					echo '<span class="na">&ndash;</span>'; 
				} else {
					if ( $terms && ! is_wp_error( $terms ) ) {
						$i = 0;
						$output = array();
						foreach ($terms as $term) {
							
							$url = add_query_arg( array( 'post_type' => 'property', 'region'=> $term->slug ), 'edit.php' );
							$output[] = '<a href="'.$url.'">'.$term->name.'</a>';
							
						}
						if(!empty($output)){
							echo implode(', ',$output);
						}
					}
				} 
				//http://findeo.local/wp-admin/edit.php?post_type=property&region=las-vegas
			break;

			case "expires" :
			$expires = get_post_meta( $post->ID, '_property_expires', true );

			if($this->is_timestamp($expires)){
				echo realteo_get_expiration_date($post->ID);
			} else {
				echo $expires;
			}
			break;

			case "featured_property" :
				if ( realteo_is_featured( $post->ID ) ) echo '&#10004;'; else echo '&ndash;';
			break;
			case "property_posted" :
				echo '<strong>' . date_i18n( __( 'M j, Y', 'realteo'), strtotime( $post->post_date ) ) . '</strong><span> ';
				echo ( empty( $post->post_author ) ? __( 'by a guest', 'realteo') : sprintf( __( 'by %s', 'realteo'), ' <a href="' . esc_url( add_query_arg( 'author', $post->post_author ) ) . '">' . get_the_author() . '</a>' ) ) . '</span>';
			break;
			
			case "property_actions" :
				echo '<div class="actions">';

				$admin_actions = apply_filters( 'realteo_post_row_actions', array(), $post );

				if ( in_array( $post->post_status, array( 'pending', 'preview', 'pending_payment' ) ) && current_user_can ( 'publish_post', $post->ID ) ) {
					$admin_actions['approve']   = array(
						'action'  => 'approve',
						'name'    => __( 'Approve', 'realteo'),
						'url'     =>  wp_nonce_url( add_query_arg( 'approve_property', $post->ID ), 'approve_property' )
					);
				}
/*				if ( $post->post_status !== 'trash' ) {
					if ( current_user_can( 'read_post', $post->ID ) ) {
						$admin_actions['view']   = array(
							'action'  => 'view',
							'name'    => __( 'View', 'realteo'),
							'url'     => get_permalink( $post->ID )
						);
					}
					if ( current_user_can( 'edit_post', $post->ID ) ) {
						$admin_actions['edit']   = array(
							'action'  => 'edit',
							'name'    => __( 'Edit', 'realteo'),
							'url'     => get_edit_post_link( $post->ID )
						);
					}
					if ( current_user_can( 'delete_post', $post->ID ) ) {
						$admin_actions['delete'] = array(
							'action'  => 'delete',
							'name'    => __( 'Delete', 'realteo'),
							'url'     => get_delete_post_link( $post->ID )
						);
					}
				}*/

				$admin_actions = apply_filters( 'property_manager_admin_actions', $admin_actions, $post );

				foreach ( $admin_actions as $action ) {
					if ( is_array( $action ) ) {
						printf( '<a class="button button-icon tips icon-%1$s" href="%2$s" data-tip="%3$s">%4$s</a>', $action['action'], esc_url( $action['url'] ), esc_attr( $action['name'] ), esc_html( $action['name'] ) );
					} else {
						echo str_replace( 'class="', 'class="button ', $action );
					}
				}

				echo '</div>';

			break;
		}
	}


	/**
	 * Sets expiry date when status changes.
	 *
	 * @param WP_Post $post
	 */
	public function set_expiry( $post ) {
		if ( $post->post_type !== 'property' ) {
			return;
		}

		// See if it is already set
		if ( get_post_meta( $post->ID, '_property_expires', true ) ) {
			$expires =  get_post_meta( $post->ID, '_property_expires', true );
			if($this->is_timestamp($expires)){
				if ( $expires && $expires < current_time( 'timestamp' ) ) {
					update_post_meta( $post->ID, '_property_expires', '' );
				} else {
					update_post_meta( $post->ID, '_property_expires', $expires );
				}
			} else {
				$timestamp = strtotime($expires);
				update_post_meta( $post->ID, '_property_expires', $timestamp );
				if ( $expires && strtotime( $expires ) < current_time( 'timestamp' ) ) {
					update_post_meta( $post->ID, '_property_expires', '' );
				} 
			}
			
		}

		// See if the user has set the expiry manually:
		if ( ! empty( $_POST[ '_property_expires' ] ) ) {
			update_post_meta( $post->ID, '_property_expires',  $_POST[ '_property_expires' ]  );

		// No manual setting? Lets generate a date if there isn't already one
		} elseif ( false == isset( $expires ) ) {
			$expires = calculate_property_expiry( $post->ID );
			update_post_meta( $post->ID, '_property_expires', $expires );

			// In case we are saving a post, ensure post data is updated so the field is not overridden
			if ( isset( $_POST[ '_property_expires' ] ) ) {
				$_POST[ '_property_expires' ] = $expires;
			}
		}
	}

	public function is_timestamp($timestamp) {

		$check = (is_int($timestamp) OR is_float($timestamp))
			? $timestamp
			: (string) (int) $timestamp;
		return  ($check === $timestamp)
	        	AND ( (int) $timestamp <=  PHP_INT_MAX)
	        	AND ( (int) $timestamp >= ~PHP_INT_MAX);
	}
	/**
	 * Maintenance task to expire propertys.
	 */
	public function check_for_expired() {
		global $wpdb;
		$date_format = get_option('date_format');
		// Change status to expired
		$property_ids = $wpdb->get_col( $wpdb->prepare( "
			SELECT postmeta.post_id FROM {$wpdb->postmeta} as postmeta
			LEFT JOIN {$wpdb->posts} as posts ON postmeta.post_id = posts.ID
			WHERE postmeta.meta_key = '_property_expires'
			AND postmeta.meta_value > 0
			AND postmeta.meta_value < %s
			AND posts.post_status = 'publish'
			AND posts.post_type = 'property'
		", strtotime(date( $date_format, current_time( 'timestamp' ) ) ) ) );

		if ( $property_ids ) {
			foreach ( $property_ids as $property_id ) {
				$property_data       = array();
				$property_data['ID'] = $property_id;
				$property_data['post_status'] = 'expired';
				wp_update_post( $property_data );
				do_action('realteo_expired_property',$property_id);
			}
		}

		// Notifie expiring in 5 days
		$property_ids = $wpdb->get_col( $wpdb->prepare( "
			SELECT postmeta.post_id FROM {$wpdb->postmeta} as postmeta
			LEFT JOIN {$wpdb->posts} as posts ON postmeta.post_id = posts.ID
			WHERE postmeta.meta_key = '_property_expires'
			AND postmeta.meta_value > 0
			AND postmeta.meta_value < %s
			AND posts.post_status = 'publish'
			AND posts.post_type = 'property'
		", strtotime( date( $date_format, strtotime('+5 days') ) ) ) );

		if ( $property_ids ) {
			foreach ( $property_ids as $property_id ) {
				$property_data['ID'] = $property_id;
				do_action('realteo_expiring_soon_property',$property_id);
			}
		}

		// Delete old expired propertys
		if ( apply_filters( 'realteo_delete_expired_properties', false ) ) {
			$property_ids = $wpdb->get_col( $wpdb->prepare( "
				SELECT posts.ID FROM {$wpdb->posts} as posts
				WHERE posts.post_type = 'property'
				AND posts.post_modified < %s
				AND posts.post_status = 'expired'
			", strtotime( date( $date_format, strtotime( '-' . apply_filters( 'realteo_delete_expired_properties_days', 30 ) . ' days', current_time( 'timestamp' ) ) ) ) ) );

			if ( $property_ids ) {
				foreach ( $property_ids as $property_id ) {
					wp_trash_post( $property_id );
				}
			}
		}
	}



/*
	/**
	 * Adds bulk actions to drop downs on Job Listing admin page.
	 *
	 * @param array $bulk_actions
	 * @return array
	 */
	public function add_bulk_actions( $bulk_actions ) {
		global $wp_post_types;

		foreach ( $this->get_bulk_actions() as $key => $bulk_action ) {
			if ( isset( $bulk_action['label'] ) ) {
				$bulk_actions[ $key ] = sprintf( $bulk_action['label'], $wp_post_types['property']->labels->name );
			}
		}
		return $bulk_actions;
	}


	/**
	 * Performs bulk actions on Job Listing admin page.
	 *
	 * @since 1.27.0
	 *
	 * @param string $redirect_url The redirect URL.
	 * @param string $action       The action being taken.
	 * @param array  $post_ids     The posts to take the action on.
	 */
	public function do_bulk_actions( $redirect_url, $action, $post_ids ) {
		$actions_handled = $this->get_bulk_actions();
		if ( isset ( $actions_handled[ $action ] ) && isset ( $actions_handled[ $action ]['handler'] ) ) {
			$handled_jobs = array();
			if ( ! empty( $post_ids ) ) {
				foreach ( $post_ids as $post_id ) {
					if ( 'property' === get_post_type( $post_id )
					     && call_user_func( $actions_handled[ $action ]['handler'], $post_id ) ) {
						$handled_jobs[] = $post_id;
					}
				}
				wp_redirect( add_query_arg( 'handled_jobs', $handled_jobs, add_query_arg( 'action_performed', $action, $redirect_url ) ) );
				exit;
			}
		}
	}

	/**
	 * Returns the list of bulk actions that can be performed on job listings.
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		$actions_handled = array();
		$actions_handled['approve_properties'] = array(
			'label' => __( 'Approve %s', 'realteo' ),
			'notice' => __( '%s approved', 'realteo' ),
			'handler' => array( $this, 'bulk_action_handle_approve_property' ),
		);
		$actions_handled['expire_properties'] = array(
			'label' => __( 'Expire %s', 'realteo' ),
			'notice' => __( '%s expired', 'realteo' ),
			'handler' => array( $this, 'bulk_action_handle_expire_property' ),
		);
	

		return apply_filters( 'realteo_bulk_actions', $actions_handled );
	}

	/**
	 * Performs bulk action to approve a single job listing.
	 *
	 * @param $post_id
	 *
	 * @return bool
	 */
	public function bulk_action_handle_approve_property( $post_id ) {
		$job_data = array(
			'ID'          => $post_id,
			'post_status' => 'publish',
		);
		if ( in_array( get_post_status( $post_id ), array( 'pending', 'pending_payment' ) )
		     && current_user_can( 'publish_post', $post_id )
		     && wp_update_post( $job_data )
		) {
			return true;
		}
		return false;
	}

	/**
	 * Performs bulk action to expire a single job listing.
	 *
	 * @param $post_id
	 *
	 * @return bool
	 */
	public function bulk_action_handle_expire_property( $post_id ) {
		$job_data = array(
			'ID'          => $post_id,
			'post_status' => 'expired',
		);
		if ( current_user_can( 'manage_properties', $post_id )
		     && wp_update_post( $job_data )
		) {
			return true;
		}
		return false;
	}


	/**
	 * Approves a single property.
	 */
	public function approve_property() {
		if ( ! empty( $_GET['approve_property'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'approve_property' ) && current_user_can( 'publish_post', $_GET['approve_property'] ) ) {
			$post_id = absint( $_GET['approve_property'] );
			$property_data = array(
				'ID'          => $post_id,
				'post_status' => 'publish'
			);
			wp_update_post( $property_data );
			wp_redirect( remove_query_arg( 'approve_property', add_query_arg( 'handled_properties', $post_id, add_query_arg( 'action_performed', 'approve_propertys', admin_url( 'edit.php?post_type=property' ) ) ) ) );
			exit;
		}
	}

	/**
	 * Shows a notice if we did a bulk action.
	 */
	public function action_notices() {
		global $post_type, $pagenow;

		$handled_jobs = isset ( $_REQUEST['handled_properties'] ) ? $_REQUEST['handled_properties'] : false;
		$action = isset ( $_REQUEST['action_performed'] ) ? $_REQUEST['action_performed'] : false;
		$actions_handled = $this->get_bulk_actions();

		if ( $pagenow == 'edit.php'
			 && $post_type == 'property'
			 && $action
			 && ! empty( $handled_jobs )
			 && isset ( $actions_handled[ $action ] )
			 && isset ( $actions_handled[ $action ]['notice'] )
		) {
			if ( is_array( $handled_jobs ) ) {
				$handled_jobs = array_map( 'absint', $handled_jobs );
				$titles       = array();
				foreach ( $handled_jobs as $job_id ) {
					$titles[] = realteo_get_the_property_title( $job_id );
				}
				echo '<div class="updated"><p>' . sprintf( $actions_handled[ $action ]['notice'], '&quot;' . implode( '&quot;, &quot;', $titles ) . '&quot;' ) . '</p></div>';
			} else {
				
				echo '<div class="updated"><p>' . sprintf( $actions_handled[ $action ]['notice'], '&quot;' . realteo_get_the_property_title(absint( $handled_jobs )) . '&quot;' ) . '</p></div>';
			}
		}
	}


} //eof class