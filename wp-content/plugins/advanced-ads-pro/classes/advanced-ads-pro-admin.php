<?php

/**
 *
 * NOTE: can not rely actively on base plugin without prior test for existence (only after plugins_loaded hook)
 */
class Advanced_Ads_Pro_Admin {

    /**
     * link to plugin page
     *
     * @since 1.1
     * @const
     */
    const PLUGIN_LINK = 'https://wpadvancedads.com/add-ons/advanced-ads-pro/';

    /**
     * field name of the user role
     * 
     * @since 1.2.5
     * @const
     */
    const ROLE_FIELD_NAME = 'advanced-ads-role';

    /**
     * Initialize the plugin
     *
     * @since   1.0.0
     */
    public function __construct() {

	// add add-on settings to plugin settings page
	add_action('advanced-ads-settings-init', array($this, 'settings_init'), 9, 1);
	add_filter('advanced-ads-setting-tabs', array($this, 'setting_tabs'));

	// add user role selection to users page
	add_action('show_user_profile', array($this, 'add_user_role_fields'));
	add_action('edit_user_profile', array($this, 'add_user_role_fields'));

	add_action( 'profile_update', array( $this, 'save_user_role' ) );
	
	// display warning if advanced visitor conditions are not active
	add_action( 'advanced-ads-visitor-conditions-after', array( $this, 'show_condition_notice' ), 10, 2 );
	// display "once per page" field
	add_action( 'advanced-ads-output-metabox-after', array( $this, 'render_ad_output_options' ) );
	// Load admin style sheet
	add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
	// Render repeat option for Content placement.
	add_action( 'advanced-ads-placement-post-content-position', array( $this, 'render_placement_repeat_option' ), 10, 2 );
    }

    /**
     * add settings to settings page
     *
     * @param string $hook settings page hook
     * @since 1.0.0
     */
    public function settings_init($hook) {

	register_setting(Advanced_Ads_Pro::OPTION_KEY, Advanced_Ads_Pro::OPTION_KEY);
	register_setting(Advanced_Ads_Pro::OPTION_KEY . '-license', Advanced_Ads_Pro::OPTION_KEY . '-license');

	// add license key field to license section
	add_settings_field(
		'pro-license', 'Pro' , array($this, 'render_settings_license_callback'), 'advanced-ads-settings-license-page', 'advanced_ads_settings_license_section'
	);

	// add new section
	add_settings_section(
		Advanced_Ads_Pro::OPTION_KEY . '_modules-enable', '', array($this, 'render_modules_enable'), Advanced_Ads_Pro::OPTION_KEY . '-settings'
	);

	// add new section
	add_settings_section(
		'advanced_ads_pro_settings_section', '', array($this, 'render_other_settings'), Advanced_Ads_Pro::OPTION_KEY . '-settings'
	);
	// setting for Autoptimize support
	if (Advanced_Ads_Checks::active_autoptimize()) {
	    add_settings_field(
		    'autoptimize-support', __('Disable Autoptimize support', 'advanced-ads-pro'), array($this, 'render_settings_autoptimize'), Advanced_Ads_Pro::OPTION_KEY . '-settings', 'advanced_ads_pro_settings_section'
	    );
	}
    }

    public function render_modules_enable() {
	
    }

    /**
     * render additional pro settings
     *
     * @since 1.1
     */
    public function render_other_settings() {
	
    }

    /**
     * Render tooltip_option settings field
     *
     * @since 1.2.3
     */
    public function render_settings_autoptimize() {
	$options = Advanced_Ads_Pro::get_instance()->get_options();
	$autoptimize_support_disabled = isset($options['autoptimize-support-disabled']) ? $options['autoptimize-support-disabled'] : false;
	require AAP_BASE_PATH . '/views/setting_autoptimize.php';
    }

    /**
     * register license
     */
    public function render_settings_license_callback() {
	$licenses = get_option(ADVADS_SLUG . '-licenses', array());
	$license_key = isset($licenses['pro']) ? $licenses['pro'] : '';
	$license_status = get_option(Advanced_Ads_Pro::OPTION_KEY . '-license-status', false);

	// get license status for old key
	if (!$license_status) {
	    $old_license_status = get_option(Advanced_Ads_Pro::OPTION_KEY . '-modules-license-status', false);
	    if ($old_license_status) {
		update_option(Advanced_Ads_Pro::OPTION_KEY . '-license-status', $old_license_status);
		delete_option(Advanced_Ads_Pro::OPTION_KEY . '-modules-license-status', $old_license_status);
	    }
	}

	$index = 'pro';
	$plugin_name = AAP_PLUGIN_NAME;
	$options_slug = Advanced_Ads_Pro::OPTION_KEY;
	$plugin_url = self::PLUGIN_LINK;

	// template in main plugin
	include ADVADS_BASE_PATH . 'admin/views/setting-license.php';
    }

    /**
     * add tracking settings tab
     *
     * @since 1.2.0
     * @param arr $tabs existing setting tabs
     * @return arr $tabs setting tabs with AdSense tab attached
     */
    public function setting_tabs(array $tabs) {

	$tabs['pro'] = array(
	    // TODO abstract string
	    'page' => Advanced_Ads_Pro::OPTION_KEY . '-settings',
	    'group' => Advanced_Ads_Pro::OPTION_KEY,
	    'tabid' => 'pro',
	    'title' => 'Pro'
	);

	return $tabs;
    }

    /**
     * form field for user role selection
     * 
     * @since 1.2.5
     * @param array $user user data
     */
    public function add_user_role_fields($user) {

	if ( ! current_user_can( 'edit_users' ) ) {
	    return;
	}

	$roles = array(
	    'advanced_ads_admin' => __('Ad Admin', 'advanced-ads-pro'),
	    'advanced_ads_manager' => __('Ad Manager', 'advanced-ads-pro'),
	    'advanced_ads_user' => __('Ad User', 'advanced-ads-pro'),
	    '' => __('--no role--', 'advanced-ads-pro'),
	);

	$role = get_user_meta( $user->ID, self::ROLE_FIELD_NAME, true );
	?>
	<h3><?php _e('Advanced Ads User Role', 'advanced-ads-pro'); ?></h3>
	<table class="form-table">
	    <tr>
		<th><label for="advads_pro_role"><?php _e('Ad User Role', 'advanced-ads-pro'); ?></label></th>
		<td><select name="<?php echo self::ROLE_FIELD_NAME; ?>" id="advads_pro_role">
			<?php foreach ($roles as $_slug => $_name) :
			    ?><option value="<?php echo $_slug; ?>" <?php selected($role, $_slug); ?>><?php echo $_name; ?></option><?php
			endforeach;
			?>
		</select>
		<p class="description"><?php _e( 'Please note, with the last update, the “Ad Admin“ and “Ad Manager“ roles have the “upload_files“ and the “unfiltered_html“ capabilities.', 'advanced-ads-pro' ); ?></p>
		</td>
	    </tr>
	</table>
	<?php
    }

    /**
     * update the user role
     * @since 1.2.5
     * @param int $user_id
     * @return boolean
     */
    public function save_user_role($user_id) {

	if ( ! current_user_can( 'edit_users' ) ) {
	    return false;
	}

	// save user role
	if (isset( $_POST[ self::ROLE_FIELD_NAME ] )) {
	    // get user object
	    $user = new WP_User( $user_id );
	    
	    // remove previous role
	    $prev_role = get_user_meta( $user_id, self::ROLE_FIELD_NAME, true ); 
	    $user->remove_role( $prev_role );
	    
	    // save new role as user meta
	    update_user_meta( $user_id, self::ROLE_FIELD_NAME, $_POST[ self::ROLE_FIELD_NAME ] );
	    
	    if( $_POST[ self::ROLE_FIELD_NAME ] ){
		// add role
		$user->add_role( $_POST[ self::ROLE_FIELD_NAME ] );
	    }
	    
	}
	
    }
    
    	
	/**
	 * show a notice if advanced visitor conditions are not enabled. Maybe some users are looking for it
	 * 
	 * @since 1.3.2
	 * @param obj $ad Advanced_Ads_Ad
	 */
	public function show_condition_notice( $ad ){
		$options = Advanced_Ads_Pro::get_instance()->get_options();
		
		if( ! isset( $options['advanced-visitor-conditions']['enabled'] ) ){
		    echo '<p>' . sprintf(__( 'Enable the Advanced Visitor Conditions <a href="%s" target="_blank">in the settings</a>.', 'advanced-ads-pro' ), admin_url( 'admin.php?page=advanced-ads-settings#top#pro' ) ) . '</p>';
		}
		
	}

    /**
     * add output options to ad edit page
     *
     * @param obj $ad Advanced_Ads_Ad object
     */
    public function render_ad_output_options( Advanced_Ads_Ad $ad ) {
        $output_options = $ad->options( 'output' );
        $once_per_page = ! empty( $output_options['once_per_page'] ) ? 1 : 0;

        require AAP_BASE_PATH . '/views/setting_output_once.php';

		// Get CodeMirror setting for Custom code textarea.
		$settings = $this->get_code_editor_settings();
		$custom_code = ! empty( $output_options['custom-code'] ) ? esc_textarea( $output_options['custom-code'] ) : '';
		require AAP_BASE_PATH . '/views/setting_custom_code.php';
    }

	/**
	 * Render repeat option for Content placement.
	 *
	 * @param string $_placement_slug id of the placement
	 * @param array $placement
	 */
	public function render_placement_repeat_option( $_placement_slug, $_placement ) {
		require AAP_BASE_PATH . '/views/setting_repeat.php';
	}

	/**
	 * Get CodeMirror settings.
	 */
	public function get_code_editor_settings() {
		global $wp_version;
		if ( 'advanced_ads' !== get_current_screen()->id 
			|| defined( 'ADVANCED_ADS_DISABLE_CODE_HIGHLIGHTING' )
			|| -1 === version_compare( $wp_version, '4.9' ) ) {
			return;
		}

		// Enqueue code editor and settings for manipulating HTML.
		$settings = wp_enqueue_code_editor( array( 'type' => 'text/html' ) );

		if ( ! $settings ) {
			$settings = false;
		}

		return $settings;
	}

	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 */
	public function enqueue_admin_styles() {
		wp_enqueue_style( AAP_SLUG . '-admin-styles', AAP_BASE_URL . 'assets/admin.css', array(), AAP_VERSION );
	}
}
