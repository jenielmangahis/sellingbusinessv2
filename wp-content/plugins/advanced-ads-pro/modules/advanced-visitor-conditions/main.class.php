<?php

// -TODO should use a constant for option key as it is shared at multiple positions
class Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions {

	protected $options = array();
	protected $is_ajax;

	// Note: hard-coded in JS
	const REFERRER_COOKIE_NAME = 'advanced_ads_pro_visitor_referrer';

	// page impression counter
	const PAGE_IMPRESSIONS_COOKIE_NAME = 'advanced_ads_page_impressions';
	
	// ad impression cookie name basis
	const AD_IMPRESSIONS_COOKIE_NAME = 'advanced_ads_ad_impressions';

	public function __construct() {
		// load options (and only execute when enabled)
		$options = Advanced_Ads_Pro::get_instance()->get_options();
		if ( isset( $options['advanced-visitor-conditions'] ) ) {
			$this->options = $options['advanced-visitor-conditions'];
		}

		// only execute when enabled
		if ( ! isset( $this->options['enabled'] ) || ! $this->options['enabled'] ) {
			return ;
		}

		$is_admin = is_admin();
		$this->is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

		add_filter( 'advanced-ads-visitor-conditions', array( $this, 'visitor_conditions' ) );
		// action after ad output is created; used for js injection
		add_filter( 'advanced-ads-ad-output', array( $this, 'after_ad_output' ), 10, 2 );
		if ( $is_admin ) {
			// add referrer check to visitor conditions
			// add_action( 'advanced-ads-visitor-conditions-after', array( $this, 'referrer_check_metabox' ), 10, 2 );

			/*if ( $this->is_ajax ) {
				add_action( 'advanced-ads-ajax-ad-select-init', array( $this, 'ajax_init_ad_select' ) );
			}*/
		// wp ajax is admin but this will allow other ajax callbacks to avoid setting the referrer
		} elseif ( ! $this->is_ajax ) {
			// save referrer url in session for visitor referrer url feature
			$this->save_first_referrer_url();
			// count page impression
			$this->count_page_impression();

			// register js script to set cookie for cached pages
			add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

			// enable common frontend logic
			// $this->init_common_frontend();
		}
	}

	/**
	 * Specially prepare for ajax ad select calls.
	 *
	 */
	public function ajax_init_ad_select() {
		$this->init_common_frontend();
	}

	/**
	 * Init for any frontend action (including ajax ad select calls)
	 *
	 */
	public function init_common_frontend() {
		// check the url referrer condition
		// add_filter( 'advanced-ads-can-display', array( $this, 'can_display_by_url_referrer' ), 10, 2 );
	}

	/**
	 * Add scripts to non-ajax frontend calls.
	 */
	public function enqueue_scripts() {
		// add dependency to manipulate cookies easily
		/*wp_enqueue_script( 'jquery' );
		wp_enqueue_script(
			'js.cookie',
			'//cdnjs.cloudflare.com/ajax/libs/js-cookie/1.5.1/js.cookie.min.js',
			array( 'jquery' ),
			'1.5.1',
			true
		);*/

		// add own code
		wp_register_script( 'advanced_ads_pro/visitor_conditions', plugin_dir_url( __FILE__ ) . 'inc/conditions.min.js', array( ADVADS_SLUG . '-advanced-js' ), AAP_VERSION );

		// 1 year by default
		$referrer_exdays = ( defined( 'ADVANCED_ADS_PRO_REFERRER_EXDAYS' ) && absint( ADVANCED_ADS_PRO_REFERRER_EXDAYS ) > 0 ) ? absint( ADVANCED_ADS_PRO_REFERRER_EXDAYS ) : 365;
		// 10 years by default
		$page_impressions_exdays = ( defined( 'ADVANCED_ADS_PRO_PAGE_IMPR_EXDAYS' ) && absint( ADVANCED_ADS_PRO_PAGE_IMPR_EXDAYS ) > 0 ) ? absint( ADVANCED_ADS_PRO_PAGE_IMPR_EXDAYS ) : 3650;

		wp_localize_script( 'advanced_ads_pro/visitor_conditions', 'advanced_ads_pro_visitor_conditions', array(
			'referrer_cookie_name' => self::REFERRER_COOKIE_NAME,
			'referrer_exdays' => $referrer_exdays,
			'page_impr_cookie_name' => self::PAGE_IMPRESSIONS_COOKIE_NAME,
			'page_impr_exdays' => $page_impressions_exdays
		));

		wp_enqueue_script( 'advanced_ads_pro/visitor_conditions' );
	}

	/**
	 * add visitor condition
	 *
	 * @since 1.0.1
	 * @param arr $conditions visitor conditions of the main plugin
	 * @return arr $conditions new global visitor conditions
	 */
	public function visitor_conditions( $conditions ){

		// referrer url
		$conditions['referrer_url'] = array(
			'label' => __( 'referrer url', 'advanced-ads-pro' ),
			'description' => __( 'Display ads based on the referrer url.', 'advanced-ads-pro' ),
			'metabox' => array( 'Advanced_Ads_Visitor_Conditions', 'metabox_string' ), // callback to generate the metabox
			'check' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'check_referrer_url' ) // callback for frontend check
		);

		// user_agent
		$conditions['user_agent'] = array(
			'label' => __( 'user agent', 'advanced-ads-pro' ),
			'description' => __( 'Display ads based on the user agent. <a href="http://www.useragentstring.com/pages/useragentstring.php" target="_blank">List of user agents</a>.', 'advanced-ads-pro' ),
			'metabox' => array( 'Advanced_Ads_Visitor_Conditions', 'metabox_string' ), // callback to generate the metabox
			'check' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'check_user_agent' ) // callback for frontend check
		);

		// current uri
		// @deprecated
		$conditions['request_uri'] = array(
			'label' => __( 'url parameters', 'advanced-ads-pro' ),
			'description' => sprintf(__( 'Display ads based on the current url parameters (everything following %s).', 'advanced-ads-pro' ), home_url()),
			'metabox' => array( 'Advanced_Ads_Visitor_Conditions', 'metabox_string' ), // callback to generate the metabox
			'check' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'check_request_uri' ), // callback for frontend check
			'disabled' => true
		);

		// capabilities
		$conditions['capability'] = array(
			'label' => __( 'user can (capabilities)', 'advanced-ads-pro' ),
			'description' => __( 'Display ads based on the users capabilities. See <a href="https://codex.wordpress.org/Roles_and_Capabilities" target="_blank">List of capabilities in WordPress</a>.', 'advanced-ads-pro' ),
			'metabox' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'metabox_capabilities' ), // callback to generate the metabox
			'check' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'check_capabilities' ) // callback for frontend check
		);

		// browser lang
		$conditions['browser_lang'] = array(
			'label' => __( 'browser language', 'advanced-ads-pro' ),
			'description' => __( 'Display ads based on the visitors browser language.', 'advanced-ads-pro' ),
			'metabox' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'metabox_browser_lang' ), // callback to generate the metabox
			'check' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'check_browser_lang' ) // callback for frontend check
		);

		// has cookie
		$conditions['cookie'] = array(
			'label' => __( 'cookie', 'advanced-ads-pro' ),
			'description' => __( 'Display ads based on the value of a cookie.', 'advanced-ads-pro' ),
			'metabox' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'metabox_cookie' ), // callback to generate the metabox
			'check' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'check_cookie' ) // callback for frontend check
		);

		// page impressions
		$conditions['page_impressions'] = array(
			'label' => __( 'page impressions', 'advanced-ads-pro' ),
			'description' => __( 'Display ads based on the number of page impressions the user already made (before the current on).', 'advanced-ads-pro' ),
			'metabox' => array( 'Advanced_Ads_Visitor_Conditions', 'metabox_number' ), // callback to generate the metabox
			'check' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'check_page_impressions' ) // callback for frontend check
		);
		// page impressions in given time frame
		$conditions['ad_impressions'] = array(
			'label' => __( 'max. ad impressions', 'advanced-ads-pro' ),
			'description' => __( 'Display the ad only for a few impressions in a given period per user.', 'advanced-ads-pro' ),
			'metabox' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'metabox_ad_impressions' ), // callback to generate the metabox
			'check' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'check_ad_impressions' ) // callback for frontend check
		);
		// new visitor
		$conditions['new_visitor'] = array(
			'label' => __( 'new visitor', 'advanced-ads-pro' ),
			'description' => __( 'Display ads to new or returning visitors only.', 'advanced-ads-pro' ),
			'metabox' => array( 'Advanced_Ads_Visitor_Conditions', 'metabox_is_or_not' ), // callback to generate the metabox
			'check' => array( 'Advanced_Ads_Pro_Module_Advanced_Visitor_Conditions', 'check_new_visitor' ) // callback for frontend check
		);

		return $conditions;
	}
	
	/**
	 * save the first referrer url submitted. Cookies is set using JavaScript
	 *
	 * @since 1.1.0
	 */
	protected function save_first_referrer_url(){
		if ( ! isset( $_COOKIE[ self::REFERRER_COOKIE_NAME ] ) ) {
			if ( isset( $_SERVER['HTTP_REFERER'] ) && ! empty( $_SERVER['HTTP_REFERER'] ) ) {
				// make cookies directly available to current request
				$_COOKIE[ self::REFERRER_COOKIE_NAME ] = $_SERVER['HTTP_REFERER'];
			}
		}
	}


	/**
	 * save page impressions in cookie. Cookies is set using JavaScript
	 *
	 * @since 1.1.0
	 */
	protected function count_page_impression(){
		if ( ! $this->is_ajax ) {
			// make cookies directly available to current request
			if ( isset( $_COOKIE[ self::PAGE_IMPRESSIONS_COOKIE_NAME ] ) ) {
				$impressions = absint( $_COOKIE[ self::PAGE_IMPRESSIONS_COOKIE_NAME ] );
				$new_impressions = $impressions + 1;
				$_COOKIE[ self::PAGE_IMPRESSIONS_COOKIE_NAME ] = $new_impressions;
			} else {
				$_COOKIE[ self::PAGE_IMPRESSIONS_COOKIE_NAME ] = 1;
			}
		}
	}

	/**
	 * callback to display the "capabilities" condition
	 *
	 * @param arr $options options of the condition
	 * @param int $index index of the condition
	 */
	static function metabox_capabilities( $options, $index = 0 ){

	    if ( ! isset ( $options['type'] ) || '' === $options['type'] ) { return; }

	    $type_options = Advanced_Ads_Visitor_Conditions::get_instance()->conditions;

	    if ( ! isset( $type_options[ $options['type'] ] ) ) {
		    return;
	    }

	    // form name basis
	    $name = Advanced_Ads_Visitor_Conditions::FORM_NAME . '[' . $index . ']';

	    // options
	    $value = isset( $options['value'] ) ? $options['value'] : '';
	    $operator = isset( $options['operator'] ) ? $options['operator'] : 'can';

	    // load capabilities
	    global $wp_roles;
	    $roles = $wp_roles->roles;

	    // loop through all roles in order to get registered capabilities
	    $capabilities = array();
	    foreach ( $roles as $_role ){
		    if( isset( $_role['capabilities'] )){
			$capabilities += $_role['capabilities'];
		    }
	    }

	    // sort keys by alphabet
	    ksort( $capabilities );

	    ?><input type="hidden" name="<?php echo $name; ?>[type]" value="<?php echo $options['type']; ?>"/>
		<select name="<?php echo $name; ?>[operator]">
		    <option value="can" <?php selected( 'can', $operator ); ?>><?php _e( 'can', 'advanced-ads-pro' ); ?></option>
		    <option value="can_not" <?php selected( 'can_not', $operator ); ?>><?php _e( 'can not', 'advanced-ads-pro' ); ?></option>
		</select>
		<select name="<?php echo $name; ?>[value]">
			<option><?php _e( '-- choose one --', 'advanced-ads-pro' ); ?></option>
			<?php foreach( $capabilities as $cap => $_val ) : ?>
				<option value="<?php echo $cap; ?>" <?php selected( $cap, $value ); ?>><?php echo $cap; ?></option>
			<?php endforeach; ?>
		</select>
	    <p class="description"><?php echo $type_options[ $options['type'] ]['description']; ?></p><?php
	}

	/**
	 * callback to display the "browser language" condition
	 *
	 * @param arr $options options of the condition
	 * @param int $index index of the condition
	 */
	static function metabox_browser_lang( $options, $index = 0 ){

	    if ( ! isset ( $options['type'] ) || '' === $options['type'] ) { return; }

	    $type_options = Advanced_Ads_Visitor_Conditions::get_instance()->conditions;

	    if ( ! isset( $type_options[ $options['type'] ] ) ) {
		    return;
	    }

	    // form name basis
	    $name = Advanced_Ads_Visitor_Conditions::FORM_NAME . '[' . $index . ']';

	    // options
	    $operator = isset( $options['operator'] ) ? $options['operator'] : 'is';
	    $value = isset( $options['value'] ) ? $options['value'] : '';

	    // load browser languages
	    include plugin_dir_path( __FILE__ ) . 'inc/browser_langs.php';
	    if( isset( $advads_browser_langs )){
		asort( $advads_browser_langs );
	    }

	    ?><input type="hidden" name="<?php echo $name; ?>[type]" value="<?php echo $options['type']; ?>"/>
		<select name="<?php echo $name; ?>[operator]">
			<option value="is" <?php selected( 'is', $operator ); ?>><?php _e( 'is', 'advanced-ads-pro' ); ?></option>
			<option value="is_not" <?php selected( 'is_not', $operator ); ?>><?php _e( 'is not', 'advanced-ads-pro' ); ?></option>
	    </select>
		<select name="<?php echo $name; ?>[value]">
			<option><?php _e( '-- choose one --', 'advanced-ads-pro' ); ?></option>
			<?php if( isset( $advads_browser_langs )) :
			    foreach( $advads_browser_langs as $_key => $_title ) : ?>
				<option value="<?php echo $_key; ?>" <?php selected( $_key, $value ); ?>><?php echo $_title; ?></option>
			<?php endforeach;
			endif; ?>
		</select>
	    <p class="description"><?php echo $type_options[ $options['type'] ]['description']; ?></p><?php
	}


	/**
	 * callback to display the "cookie" condition
	 *
	 * @param arr $options options of the condition
	 * @param int $index index of the condition
	 */
	static function metabox_cookie( $options, $index = 0 ){

	    if ( ! isset ( $options['type'] ) || '' === $options['type'] ) { return; }

	    $type_options = Advanced_Ads_Visitor_Conditions::get_instance()->conditions;

	    if ( ! isset( $type_options[ $options['type'] ] ) ) {
		    return;
	    }

	    // form name basis
	    $name = Advanced_Ads_Visitor_Conditions::FORM_NAME . '[' . $index . ']';
	    $operator = isset( $options['operator'] ) ? $options['operator'] : 'show';

	    // options
	    $cookie = isset( $options['cookie'] ) ? $options['cookie'] : ''; // cookie name
	    $value = isset( $options['value'] ) ? $options['value'] : ''; // cookie value

	    ?><input type="hidden" name="<?php echo $name; ?>[type]" value="<?php echo $options['type']; ?>"/>
		<?php $operatoroption = '<select name="' . $name . '[operator]">
		    <option value="show" ' . selected( 'show', $operator, false ). '>' . _x( 'is', 'visitor condition operator for cookies', 'advanced-ads-pro' ) . '</option>
		    <option value="hide" ' . selected( 'hide', $operator, false ). '>' . _x( 'is not', 'visitor condition operator for cookies', 'advanced-ads-pro' ) . '</option>
		</select>';
		$cookieoption = '<input type="text" name="' . $name . '[cookie]" value="' . $cookie . '" placeholder="' . __( 'Cookie Name', 'advanced-ads-pro' ) . '"/>';
		$valueoption = '<input type="text" name="' . $name . '[value]" value="' . $value . '" placeholder="' . __( 'Cookie Value', 'advanced-ads-pro' ) . '"/>';
		printf(_x( '%1$s if %2$s is %3$s', 'order of cookie visitor condition string', 'advanced-ads-pro' ), $operatoroption, $cookieoption, $valueoption ); ?>
		<div class="clear"></div>
		<p class="description"><?php echo $type_options[ $options['type'] ]['description']; ?> <?php _e( 'Leave the value empty to check only the existance of the cookie.', 'advanced-ads-pro' ); ?></p><?php
	}
	
	/**
	 * callback to display the condition for ad impressions in a specific time frame
	 *
	 * @param arr $options options of the condition
	 * @param int $index index of the condition
	 */
	static function metabox_ad_impressions( $options, $index = 0 ){

	    if ( ! isset ( $options['type'] ) || '' === $options['type'] ) { return; }

	    $type_options = Advanced_Ads_Visitor_Conditions::get_instance()->conditions;

	    if ( ! isset( $type_options[ $options['type'] ] ) ) {
		    return;
	    }

	    // form name basis
	    $name = Advanced_Ads_Visitor_Conditions::FORM_NAME . '[' . $index . ']';

	    // options
	    $value = isset( $options['value'] ) ? absint( $options['value'] ) : 0;
	    $timeout = isset( $options['timeout'] ) ? absint( $options['timeout'] ) : 0;

	    ?><input type="hidden" name="<?php echo $name; ?>[type]" value="<?php echo $options['type']; ?>"/>
		<input type="number" name="<?php echo $name; ?>[value]" value="<?php echo absint( $value ); ?>"/>
		<?php 
		$impressions_field = '<input type="number" name="' . $name . '[timeout]" value="' . $timeout . '"/>';
		printf( __( 'within %s seconds', 'advanced-ads-pro' ), $impressions_field ); ?>
	    <p class="description"><?php echo $type_options[ $options['type'] ]['description']; ?></p><?php
	}


	/**
	 * check referrer url in frontend
	 *
	 * @since 1.0.1
	 * @param arr $options options of the condition
	 * @return bool true if ad can be displayed
	 */
	static function check_referrer_url( $options = array() ){

		// check if session variable is set
		if ( ! isset( $_COOKIE[ self::REFERRER_COOKIE_NAME ] ) ) {
			return false;
		}
		$referrer = $_COOKIE[ self::REFERRER_COOKIE_NAME ];

		return Advanced_Ads_Visitor_Conditions::helper_check_string( $referrer, $options );
	}

	/**
	 * check user agent in frontend
	 *
	 * @since 1.0.1
	 * @param arr $options options of the condition
	 * @return bool true if ad can be displayed
	 */
	static function check_user_agent( $options = array() ){

		// check if session variable is set
		$user_agent = isset( $_SERVER[ 'HTTP_USER_AGENT' ] ) ? $_SERVER[ 'HTTP_USER_AGENT' ] : '';

		return Advanced_Ads_Visitor_Conditions::helper_check_string( $_SERVER[ 'HTTP_USER_AGENT' ], $options );
	}

	/**
	 * check user capabilities in frontend
	 *
	 * @since 1.0.1
	 * @param arr $options options of the condition
	 * @return bool true if ad can be displayed
	 */
	static function check_capabilities( $options = array() ){

		if ( ! isset( $options['value'] ) || '' === $options['value'] || ! isset( $options['operator'] ) ){
			return true;
		}

		switch ( $options['operator'] ){
		    case 'can' :
			    return ( current_user_can( $options['value'] ) );
			    break;
		    case 'can_not' :
			    return ( ! current_user_can( $options['value'] ) );
		}

		return true;
	}

	/**
	 * check browser language
	 *
	 * @since 1.0.1
	 * @param arr $options options of the condition
	 * @return bool true if ad can be displayed
	 */
	static function check_browser_lang( $options = array() ){

		if ( ! isset( $options['value'] ) || '' === $options['value'] ){
			return true;
		}

		if ( ! isset( $_SERVER[ 'HTTP_ACCEPT_LANGUAGE' ] ) || '' === $_SERVER[ 'HTTP_ACCEPT_LANGUAGE' ] ) {
			return false;
		}

		// check if the browser lang is within the accepted language string
		$regex = "@\b" . $options['value'] . "\b@i"; // \b checks for "whole words"
		$result = preg_match( $regex, $_SERVER[ 'HTTP_ACCEPT_LANGUAGE' ] ) === 1;

		if ( isset( $options['operator'] ) && $options['operator'] === 'is_not' ) {
			return ! $result;
		} else {
			return $result;
		}
	}

	/**
	 * check request_uri in frontend
	 *
	 * @since 1.0.1
	 * @param arr $options options of the condition
	 * @return bool true if ad can be displayed
	 * 
	 * @deprecated since version 1.4, moved to display conditions
	 */
	static function check_request_uri( $options = array(), $ad ){

		// check if session variable is set
		if ( isset( $ad->args['url_parameter'] ) ) {
			$uri_string = $ad->args['url_parameter'];
		} else {
			$uri_string = isset( $_SERVER[ 'REQUEST_URI' ] ) ? $_SERVER[ 'REQUEST_URI' ] : '';
			// only consider QUERY_STRING, if not already included in REQUEST_URI
			if ( !empty( $_SERVER[ 'QUERY_STRING' ] ) && false === strpos( $_SERVER[ 'REQUEST_URI' ], $_SERVER[ 'QUERY_STRING' ] ) ) {
				$uri_string .= $_SERVER[ 'QUERY_STRING' ];
			}
		}

		return Advanced_Ads_Visitor_Conditions::helper_check_string( $uri_string, $options );
	}

	/**
	 * check cookie value in frontend
	 *
	 * @since 1.1.1
	 * @param arr $options options of the condition
	 * @return bool true if ad can be displayed
	 */
	static function check_cookie( $options = array() ){

		$must_be_set = ! isset( $options['operator'] ) || 'hide' !== $options['operator'];

		// check if cookie exists
		if ( ! isset( $options['cookie'] ) || '' === $options['cookie'] ){
			return $must_be_set;
		}

		if( ! isset( $_COOKIE[ $options['cookie'] ] )) {
			return ! $must_be_set;
		}

		// return true if value is empty or equals the value
		if ( ! isset( $options['value'] ) || '' === $options['value'] ||
			$options['value'] === $_COOKIE[ $options['cookie'] ] ) {
			return $must_be_set;
		}

		return ! $must_be_set;
	}

	/**
	 * check page_impressions in frontend
	 *
	 * @since 1.1.1
	 * @param arr $options options of the condition
	 * @return bool true if ad can be displayed
	 */
	static function check_page_impressions( $options = array() ){
	    if ( ! isset( $options['operator'] ) || ! isset( $options['value'] ) ) {
			return true;
	    }

	    $impressions = 0;
	    if ( isset( $_COOKIE[ self::PAGE_IMPRESSIONS_COOKIE_NAME ] ) ) {
		$impressions = absint( $_COOKIE[ self::PAGE_IMPRESSIONS_COOKIE_NAME ] );
	    } else {
		return false;
	    }

	    $value = absint( $options['value'] );

	    switch ( $options['operator'] ){
		    case 'is_equal' :
			    if ( $value !== $impressions ) { return false; }
			    break;
		    case 'is_higher' :
			    if ( $value > $impressions ) { return false; }
			    break;
		    case 'is_lower' :
			    if ( $value < $impressions ) { return false; }
			    break;
	    }

	    return true;
	}
	
	/**
	 * check ad impressions limit for the ad in frontend
	 *
	 * @since 1.2.4
	 * @param arr $options options of the condition
	 * @param obj $ad Advanced_Ads_Ad
	 * @return bool true if ad can be displayed
	 */
	static function check_ad_impressions( $options = array(), $ad = false ){

	    if ( ! $ad instanceof Advanced_Ads_Ad || ! isset( $options['value'] ) || ! isset( $options['timeout'] ) ) {
			return true;
	    }

	    $value = absint( $options['value'] );
	    $impressions = 0;
	    $cookie_name = self::AD_IMPRESSIONS_COOKIE_NAME . '_' . $ad->id;
	    $cookie_timeout_name = $cookie_name . '_timeout';

	    if ( isset( $_COOKIE[ $cookie_name ] ) && isset( $_COOKIE[ $cookie_timeout_name ] )) {
		$impressions = absint( $_COOKIE[ $cookie_name ]  );
		if ( $value <= $impressions ) { return false; }
	    }

	    return true;
	}

	/**
	 * check new_visitor in frontend
	 *
	 * @since 1.1.1
	 * @param arr $options options of the condition
	 * @return bool true if ad can be displayed
	 */
	static function check_new_visitor( $options = array() ){
	    if ( ! isset( $options['operator'] ) ) {
			return true;
	    }

	    $impressions = 0;
	    if ( isset( $_COOKIE[ self::PAGE_IMPRESSIONS_COOKIE_NAME ] ) ) {
		$impressions = absint( $_COOKIE[ self::PAGE_IMPRESSIONS_COOKIE_NAME ] );
	    }

	    switch ( $options['operator'] ){
		    case 'is' :
			    return 1 === $impressions;
			    break;
		    case 'is_not' :
			    return 1 < $impressions;
			    break;
	    }

	    return true;
	}
	
	/**
	 * inject ad output and js code
	 *
	 * @since 1.2.4
	 * @param str $content ad content
	 * @param obj $ad ad object
	 */
	public function after_ad_output( $content = '', Advanced_Ads_Ad $ad ){
		$options = $ad->options( 'visitors' );
		if( is_array( $options )) foreach( $options as $_visitor_condition ){
			if( isset( $_visitor_condition['type'] )){
				switch( $_visitor_condition['type'] ){
					// set limit and timeout for max_ad_impressions visitor condition
					case 'ad_impressions' :
					    $limit = isset( $_visitor_condition['value'] ) ? $_visitor_condition['value'] : '';
					    $timeout = isset( $_visitor_condition['timeout'] ) ? $_visitor_condition['timeout'] : '';
					    $timeout = ( $timeout ) ? $timeout : '""';
					    // cookie names
					    $cookie_name = self::AD_IMPRESSIONS_COOKIE_NAME . '_' . $ad->id;
					    $cookie_timeout_name = $cookie_name . '_timeout';
					    // get current count, if timeout not reached yet
					    $count = ( isset( $_COOKIE[ $cookie_name ] ) && isset( $_COOKIE[ $cookie_timeout_name ] ) ) ? $_COOKIE[ $cookie_name ] : 1;

					    $content .= '<script>( window.advanced_ads_ready || jQuery( document ).ready ).call( null, function() {'
						    . 'if( advads.get_cookie( "' . $cookie_timeout_name . '" ) ) {'
						    . 'if( advads.get_cookie( "' . $cookie_name . '" ) ) {'
						    . 'var ' . $cookie_name . ' = parseInt( advads.get_cookie( "' . $cookie_name . '" ) ) + 1;'
						    . '} else { var ' . $cookie_name . ' = 1; }'
						    . '} else {'
						    . 'var ' . $cookie_name . ' = 1;'
						    . 'advads.set_cookie_sec("' . $cookie_timeout_name . '", "true", ' . $timeout . ' );'
						    . '}'
						    . 'advads.set_cookie_sec("' . $cookie_name . '", ' . $cookie_name . ', ' . $timeout . ' );';
					    $content .= '});</script>';
					    break;
				}
			}
		}
		return $content;
	}
}
