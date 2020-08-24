<?php

class Advanced_Ads_Pro {

	/**
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * interal plugin options – set by the plugin
	 *
	 * @var     array (if loaded)
	 */
	protected $internal_options;

	/**
	 * Option name shared by child modules.
	 *
	 * @var string
	 */
	const OPTION_KEY = 'advanced-ads-pro';

	/**
	 *
	 * @var Advanced_Ads_Pro
	 */
	private static $instance;

	private function __construct() {
		// actually setup plugin once base plugin is available
		add_action( 'plugins_loaded', array( $this, 'init'), 11 );
	}

	/**
	 *
	 * @return Advanced_Ads_Pro
	 */
	public static function get_instance()
	{
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Must not be called before `plugins_loaded` hook.
	 *
	 */
	public function init() {

		if ( ! class_exists( 'Advanced_Ads', false ) ) {
			add_action( 'admin_notices', array( $this, 'missing_plugin_notice' ) );
			return ;
		}

		// load gettext domain
		$this->load_textdomain();

		// load config and modules
		$options = $this->get_options();
		Advanced_Ads_ModuleLoader::loadModules( AAP_PATH . '/modules/', isset( $options['modules'] ) ? $options['modules'] : array() );

		// load admin on demand
		if ( is_admin() ) {
			new Advanced_Ads_Pro_Admin;
			// run after the internal Advanced Ads version has been updated by the `Advanced_Ads_Upgrades`, because
			// the `Advanced_Ads_Admin_Notices` can update this version, and the `Advanced_Ads_Upgrades` will not be called
			add_action( 'init', array( $this, 'maybe_update_capabilities' ) );

			add_filter( 'advanced-ads-notices', array( $this, 'add_notices' ) );
			add_filter( 'advanced-ads-add-ons', array( $this, 'register_auto_updater' ), 10 );
		} else {
		    // force advanced js file to be attached
		    add_filter( 'advanced-ads-activate-advanced-js', '__return_true' );
		    // check autoptimize
		    if( Advanced_Ads_Checks::active_autoptimize() && ! isset ( $options['autoptimize-support-disabled'] ) ){
			    add_filter( 'advanced-ads-output-inside-wrapper', array( $this, 'autoptimize_support' ), 10, 2 );
		    }
		}
		new Advanced_Ads_Pro_Compatibility;

		// override shortcodes
		remove_shortcode( 'the_ad' );
		remove_shortcode( 'the_ad_group' );
		remove_shortcode( 'the_ad_placement' );		
		add_shortcode( 'the_ad', array( $this, 'shortcode_display_ad' ) );
		add_shortcode( 'the_ad_group', array( $this, 'shortcode_display_ad_group' ) );
		add_shortcode( 'the_ad_placement', array( $this, 'shortcode_display_ad_placement' ) );

		add_filter( 'advanced-ads-can-display', array( $this, 'can_display_by_display_limit' ), 10, 3 );
		add_filter( 'advanced-ads-placement-content-offsets', array( $this, 'placement_content_offsets' ), 10, 3 );
		add_filter( 'advanced-ads-output-inside-wrapper', array( $this, 'add_custom_code' ), 10, 2 );
	}

	/**
	 * fired when the plugin is activated.
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 *
	 * @since    1.2.5
	 */
	public static function activate( $network_wide ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {
				// Get all blog ids
				global $wpdb;
				$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );
				$original_blog_id = $wpdb->blogid;

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::single_activate();
				}

				switch_to_blog( $original_blog_id );
			} else {
				self::single_activate();
			}
		} else {
			self::single_activate();
		}
	}

	/**
	 * fired when the plugin is deactivated.
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 *
	 * @since    1.2.5
	 */
	public static function deactivate( $network_wide ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {
				// Get all blog ids
				global $wpdb;
				$blog_ids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" );
				$original_blog_id = $wpdb->blogid;

				foreach ( $blog_ids as $blog_id ) {
					switch_to_blog( $blog_id );
					self::single_deactivate();
				}

				switch_to_blog( $original_blog_id );
			} else {
				self::single_deactivate();
			}
		} else {
			self::single_deactivate();
		}
	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @param int $blog_id ID of the new blog.
	 */
	public static function activate_new_site( $blog_id ) {
		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();
	}

	public static function single_activate() {
		// create new user roles
		add_role( 'advanced_ads_admin', __( 'Ad Admin', 'advanced-ads-pro' ), array(
			'read' => true,
			'advanced_ads_manage_options' => true,
			'advanced_ads_see_interface' => true,
			'advanced_ads_edit_ads' => true,
			'advanced_ads_manage_placements' => true,
			'advanced_ads_place_ads' => true,
			'upload_files' => true,
			'unfiltered_html' => true
		) );
		add_role( 'advanced_ads_manager', __( 'Ad Manager', 'advanced-ads-pro' ), array(
			'read' => true,
			'advanced_ads_see_interface' => true,
			'advanced_ads_edit_ads' => true,
			'advanced_ads_manage_placements' => true,
			'advanced_ads_place_ads' => true,
			'upload_files' => true,
			'unfiltered_html' => true
		) );
		add_role( 'advanced_ads_user', __( 'Ad User', 'advanced-ads-pro' ), array(
			'read' => true,
			'advanced_ads_place_ads' => true,
		) );

		self::enable_placement_test_emails();
	}

	public static function single_deactivate() {
		// remove user roles
		remove_role( 'advanced_ads_admin' );
		remove_role( 'advanced_ads_manager' );
		remove_role( 'advanced_ads_user' );

		self::disable_placement_test_emails();
	}

	/**
	 * show warning if Advanced Ads js is not activated
	 */
	public function missing_plugin_notice(){
		$plugins = get_plugins();
		if( isset( $plugins['advanced-ads/advanced-ads.php'] ) ){ // is installed, but not active
			$link = '<a class="button button-primary" href="' . wp_nonce_url( 'plugins.php?action=activate&amp;plugin=advanced-ads/advanced-ads.php&amp', 'activate-plugin_advanced-ads/advanced-ads.php' ) . '">'. __('Activate Now', 'advanced-ads-pro') .'</a>';
		} else {
			$link = '<a class="button button-primary" href="' . wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . 'advanced-ads'), 'install-plugin_' . 'advanced-ads') . '">'. __('Install Now', 'advanced-ads-pro') .'</a>';
		}
		echo '
		<div class="error">
		  <p>'.sprintf(__('<strong>%s</strong> requires the <strong><a href="https://wpadvancedads.com" target="_blank">Advanced Ads</a></strong> plugin to be installed and activated on your site.', 'advanced-ads-pro'), 'Advanced Ads Pro') .
		     '&nbsp;' . $link . '</p></div>';
	}

	/**
	 *
	 * @return array
	 */
	public function get_options() {
		if ( ! isset( $this->options ) ) {
			$defaultOptions = array();
			$this->options = get_option( self::OPTION_KEY, $defaultOptions );
			// handle previous option key
			if( $this->options === array() ){
				$old_options = get_option( self::OPTION_KEY . '-modules', false );
				if( $old_options ){
					// update old options
					$this->update_options( $old_options );
					delete_option( self::OPTION_KEY . '-modules' );
				}
			}
		}

		return $this->options;
	}

	/**
	 *
	 * @param string $key
	 * @param mixed $value
	 */
	public function set_option( $key, $value ) {
		$options = $this->get_options();

		$options[$key] = $value;
		$this->update_options( $options );
	}

	/**
	 *
	 * @param array $options
	 */
	public function update_options( array $options ) {
		$updated = update_option( self::OPTION_KEY, $options );

		if ( $updated ) {
			$this->options = $options;
		}
	}

	public function load_textdomain() {
		load_plugin_textdomain( AAP_SLUG, false, AAP_BASE_DIR . '/languages' );
	}

	/**
	 * register plugin for the auto updater in the base plugin
	 *
	 * @param arr $plugins plugin that are already registered for auto updates
	 * @return arr $plugins
	 */
	public function register_auto_updater( array $plugins = array() ){

		$plugins['pro'] = array(
			'name' => AAP_PLUGIN_NAME,
			'version' => AAP_VERSION,
			'path' => AAP_BASE_PATH . 'advanced-ads-pro.php',
			'options_slug' => Advanced_Ads_Pro::OPTION_KEY,
		);
		return $plugins;
	}

	/**
	 * add autoptimize support
	 *
	 * @since 1.2.3
	 * @param str $ad_content ad content
	 * @param obj $ad Advanced_Ads_Ad
	 */
	public function autoptimize_support( $ad_content = '', Advanced_Ads_Ad $ad ){
		return "<!--noptimize-->" . $ad_content . "<!--/noptimize-->";
	}

	/**
	 * return internal plugin options, these are options set by the plugin
	 *
	 * @param bool $set_defaults
	 * @return array $options
	 */
	public function internal_options( $set_defaults = true ) {
		if ( ! $set_defaults ) {
			return get_option( AAP_PLUGIN_NAME . '-internal', array() );
		}

		if ( ! isset( $this->internal_options ) ) {
		    $defaults = array(
				'version' => AAP_VERSION,
		    );
		    $this->internal_options = get_option( AAP_PLUGIN_NAME . '-internal', array() );
		    // save defaults
		    if ( $this->internal_options === array() ) {
				$this->internal_options = $defaults;
				$this->update_internal_options( $this->internal_options );
		    }
		}

		return $this->internal_options;
	}

	/**
	 * update internal plugin options
	 *
	 * @param array $options new internal options
	 */
	public function update_internal_options( array $options ) {
		$this->internal_options = $options;
		update_option( AAP_PLUGIN_NAME . '-internal', $options );
	}

	/**
	 * Update capabilities and warn user if needed
	 *
	 */
	function maybe_update_capabilities() {
		$internal_options = $this->internal_options( false );
		if ( ! isset( $internal_options['version'] ) ) {
			$roles = array('advanced_ads_admin', 'advanced_ads_manager' );
			// add notice if there is at least 1 user with that role
			foreach ( $roles as $role ) {
				$users_query = new WP_User_Query( array(
					'fields' => 'ID',
					'number' => 1,
					'role' => $role,
				) );
				if ( count( $users_query->get_results() ) ) {
					Advanced_Ads_Admin_Notices::get_instance()->add_to_queue( 'pro_changed_caps' );
					break;
				}
			}

			if ( $role = get_role( 'advanced_ads_admin' ) ) {
				$role->add_cap( 'upload_files' );
				$role->add_cap( 'unfiltered_html' );
			}
			if ( $role = get_role( 'advanced_ads_manager' ) ) {
				$role->add_cap( 'upload_files' );
				$role->add_cap( 'unfiltered_html' );
			}

			// save new version
			$this->internal_options();
		}
	}

	function add_notices( $notices ) {
		$notices['pro_changed_caps'] = array(
	    'type' => 'update',
	    'text' => __( 'Please note, the “Ad Admin“ and the “Ad Manager“ roles have the “upload_files“ and the “unfiltered_html“ capabilities', 'advanced-ads-pro' ),
	    'global' => true
	    );
	    return $notices;
	}

	/**
	 * check if the ad can be displayed based on display limit
	 *
	 * @param bool $can_display existing value
	 * @param obj $ad Advanced_Ads_Ad object
	 * @param array $check_options
	 * @return bool true if limit is not reached, false otherwise
	 */
	public function can_display_by_display_limit( $can_display, Advanced_Ads_Ad $ad, $check_options ) {
		if ( ! $can_display ) {
			return false;
		}

		$output_options = $ad->options( 'output' );

		if ( empty( $check_options['passive_cache_busting'] ) && ! empty( $output_options['once_per_page'] ) ) {
			$current_ads = Advanced_Ads::get_instance()->current_ads;

			foreach ( $current_ads as $item ) {
				if ( $item['type'] === 'ad' && absint( $item['id'] ) === $ad->id ) {
					return false;
				}
			}
		}
		return true;
	}

	/**
	 * Get offsets for Content placement.
	 *
	 * @param array $offsets
	 * @param array $options Injection options.
	 * @param array $placement_opts Placement options.
	 * @return array $offsets
	 */
	public function placement_content_offsets( $offsets, $options, $placement_opts ) {
		if ( empty( $placement_opts['repeat'] )
			|| ! isset( $options['paragraph_id'] )
			|| ! isset( $options['paragraph_select_from_bottom'] )
			|| ! isset( $options['paragraph_count'] ) ) {
			return $offsets;
		}

		$offsets = array();
		for ( $i = $options['paragraph_id'] -1; $i < $options['paragraph_count']; $i++ ) {
			// Select every X number.
			if ( ( $i + 1 ) % $options['paragraph_id'] === 0 )  {
				$offsets[] = $options['paragraph_select_from_bottom'] ? $options['paragraph_count'] - 1 - $i : $i;
			}
		}
		return $offsets;
	}

	/**
	 * Add custom code after the ad.
	 *
	 * @param str $ad_content Ad content.
	 * @param obj $ad Advanced_Ads_Ad
	 * @return str $ad_content Ad content.
	 */
	public function add_custom_code( $ad_content, Advanced_Ads_Ad $ad ) {
		$options = $ad->options( 'output' );

		if ( ! empty( $options['custom-code'] ) ) {
			return $ad_content .= $options['custom-code'];
		}
		return $ad_content;
	}

	/**
	 * enable placement test emails
	 */
	public static function enable_placement_test_emails() {
		// only schedule if not yet scheduled
		if ( ! wp_next_scheduled( 'advanced-ads-placement-tests-emails' ) ) {
			wp_schedule_event( time(), 'daily', 'advanced-ads-placement-tests-emails' );
		}
	}

	/**
	 * disable placement test emails
	 */
	public static function disable_placement_test_emails() {
		wp_clear_scheduled_hook( 'advanced-ads-placement-tests-emails' );
	}

	/**
	 * shortcode to include ad in frontend
	 *
	 * @param arr $atts
	 */
	public function shortcode_display_ad( $atts ) {
		return $this->do_shortcode( $atts, 'shortcode_display_ad' );
	}

	/**
	 * shortcode to include ad from an ad group in frontend
	 *
	 * @param arr $atts
	 */
	public function shortcode_display_ad_group( $atts ) {
		return $this->do_shortcode( $atts, 'shortcode_display_ad_group' );
	}

	/**
	 * shortcode to display content of an ad placement in frontend
	 *
	 * @param arr $atts
	 */
	public function shortcode_display_ad_placement( $atts ) {
		return $this->do_shortcode( $atts, 'shortcode_display_ad_placement' );
	}

	/**
	 * Create shortcode output.
	 *
	 * @param arr $atts
	 * @param string $function name
	 */
	private function do_shortcode( $atts, $function_name ) {
		$blog_id = isset( $atts['blog_id'] ) ? absint( $atts['blog_id'] ) : 0;

		if ( $blog_id && $blog_id !== get_current_blog_id() && is_multisite() ) {
			// prevent database error
			if ( ! Advanced_Ads_Pro_Utils::blog_exists( $blog_id ) ) { return; }

			if ( method_exists( Advanced_Ads::get_instance(), 'switch_to_blog' ) ) {
				Advanced_Ads::get_instance()->switch_to_blog( $blog_id );
			}

			// use the public available function here
			$result = call_user_func( array( Advanced_Ads_Plugin::get_instance(), $function_name ), $atts );

			if ( method_exists( Advanced_Ads::get_instance(), 'restore_current_blog' ) ) {
				Advanced_Ads::get_instance()->restore_current_blog();
			}
			return $result;
		}

		// use the public available function here
		return call_user_func( array( Advanced_Ads_Plugin::get_instance(), $function_name ), $atts );
	}

}
