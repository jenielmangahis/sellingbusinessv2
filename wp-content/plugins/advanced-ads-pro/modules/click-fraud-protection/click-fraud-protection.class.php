<?php
class Advanced_Ads_Pro_Module_CFP
{
	
	public $module_enabled = false;
	
	public function __construct() {
		$options = Advanced_Ads_Pro::get_instance()->get_options();
		$this->module_enabled = isset( $options['cfp']['enabled'] ) && $options['cfp']['enabled'];
		
		add_filter( 'advanced-ads-output-wrapper-options', array( $this, 'add_wrapper' ), 25, 2 );
		add_action( 'wp_enqueue_scripts', array( $this, 'wp_enqueue_script' ) );
		add_filter( 'advanced-ads-ad-output', array( $this, 'ad_output' ), 99, 2 );
		add_action( 'wp_head', array( $this, 'wp_head' ) );
		add_filter( 'advanced-ads-can-display', array( $this, 'can_display' ), 10, 2 );
		
		// add visitor conditions
		add_filter( 'advanced-ads-visitor-conditions', array( $this, 'add_visitor_conditions' ) );
		add_filter( 'advanced-ads-js-visitor-conditions', array( $this, 'add_to_passive_cache_busting' ) );

		//Add new node to 'Ad health' if the user is banned.
		add_filter( 'advanced-ads-ad-health-nodes', array( $this, 'add_ad_health_node' ) );
		
		// delete ban cookie before any output
		add_action( 'init', array( $this, 'delete_ban_cookie' ), 50 );
	}
	
	/**
	 * determine cookies parameter (domain or path) 
	 */
	private function get_path_and_domain() {
		$path = '';
		$site_url = site_url();
		$domain = '';
		
		// get host name form server first before getting client side value
		$host_name = !empty( $_SERVER['SERVER_NAME'] )? $_SERVER['SERVER_NAME'] : $_SERVER['HTTP_HOST'];
		
		$expl_url = explode( '.', $site_url );
		
		if ( 2 < count( $expl_url ) ) {
			// using subdomain
			$domain = $host_name;
		}
		$path = explode( $host_name, $site_url );
		if ( isset( $path[1] ) ) {
			$path = $path[1];
		} else {
			$path = $path[0];
		}
		return array(
			'path' => $path,
			'domain' => $domain,
		);
	}
	
	private function get_conditions_options( $ad ) {
		$options = $ad->options();
		if ( !isset( $options['visitors'] ) ) return array();
		$results = array();
		foreach( $options['visitors'] as $cond ) {
			if ( isset( $cond['type'] ) && 'ad_clicks' == $cond['type'] ) {
				$results[] = $cond;
			}
		}
		return $results;
	}
	
	/**
	 *  Notify passive cache-busting that this condition can be checked using JavaScript.
	 */
	public function add_to_passive_cache_busting( $conditions ) {
		$conditions[] = 'ad_clicks';
		return $conditions;
	}
	
	/**
	 * add the visitor condition 
	 */
	public function add_visitor_conditions( $conditions ) {
		$conditions['ad_clicks'] = array(
			'label' => __( 'max. ad clicks', 'advanced-ads-pro' ),
			'description' => __( 'Display ad only if a click limit has not been reached', 'advanced-ads-pro' ),
			'metabox' => array( $this, 'visitor_conditions_metabox' ), // callback to generate the metabox
			'check' => array( $this, 'visitor_conditions_check' ) // callback for frontend check
		);
		return $conditions;
	}
	
	/**
	 * render the markup for visitor condition 
	 */
	public function visitor_conditions_metabox( $options, $index = 0 ) {
		if ( ! isset ( $options['type'] ) || '' === $options['type'] ) { return; }
		
		$type_options = Advanced_Ads_Visitor_Conditions::get_instance()->conditions;
		if ( ! isset( $type_options[ $options['type'] ] ) ) {
			return;
		}
		
		// form name basis
		$name = Advanced_Ads_Visitor_Conditions::FORM_NAME . '[' . $index . ']';
		// options
		$limit = isset( $options['limit']  ) ? $options['limit'] : '' ;
		$expiration = isset( $options['expiration']  ) ? $options['expiration'] : '' ;

		?><input type="number" name="<?php echo $name; ?>[limit]" value="<?php echo $limit; ?>" min="1" step="1" />
		<?php _e( 'within', 'advanced-ads-pro' ); ?>
		<input type="number" name="<?php echo $name; ?>[expiration]" value="<?php echo $expiration; ?>" />
		<?php _e( 'hours', 'advanced-ads-pro' ); ?>
        <input type="hidden" name="<?php echo $name; ?>[type]" value="<?php echo $options['type']; ?>"/>
        <p class="description"><?php echo $type_options[ $options['type'] ]['description']; ?></p>
        <?php
	}
	
	/**
	 *  delete ban cookie
	 */
	public function delete_ban_cookie() {
		if ( !$this->module_enabled && isset( $_COOKIE['advads_pro_cfp_ban'] ) && !headers_sent() ) {
			$pnd = $this->get_path_and_domain();
			$path = '' != $pnd['path'] ? $pnd['path'] : '/';
			if ( '' != $pnd['domain'] ) {
				setcookie( 'advads_pro_cfp_ban', '0', time() - 3600, $path, $pnd['domain'] );
			} else {
				setcookie( 'advads_pro_cfp_ban', '0', time() - 3600, $path );
			}
		}
	}
	
	/**
	 * front end check for the visitor condition
	 */
	public function visitor_conditions_check( $options = array(), $ad = false ) {
		if ( isset( $_COOKIE['advads_pro_cfp_ban'] ) ) {
			// banned user
			return false;
		}
		if ( isset( $_COOKIE['advanced_ads_ad_clicks_' . $ad->id] ) ) {
			$cval = json_decode( stripslashes( $_COOKIE['advanced_ads_ad_clicks_' . $ad->id] ), true );
			
			$update_cookie = false;
			
			$now = time();
			
			foreach( $cval as $key => $value ) {
				// check against the right value in case of multiple ad clicks conditions
				if ( '_' . $options['expiration'] == $key ) {
					if ( $now >= absint( $value['ttl'] ) ) {
						// expired TTL - just skip, cookie will be updated client side
						return true;
					} else {
						if ( absint( $value['count'] ) >= absint( $options['limit'] ) ) {
							return false;
						}
					}
				}
			}
			
		}
		return true;
	}
	
	/**
	 *  determine if ads should be hidden
	 */
	public function can_display( $can_display, $ad ) {
		// banned user && module enabled
		if ( $this->module_enabled && isset( $_COOKIE['advads_pro_cfp_ban'] ) ) {
			return false;
		}
		return $can_display;
	}
	
	/**
	 * create early the constructor used in ad output 
	 */
	public function wp_head() {
		$options = Advanced_Ads_Pro::get_instance()->get_options();
		$exphours = isset( $options['cfp']['cookie_expiration'] )? $options['cfp']['cookie_expiration'] : 3;
		$ban_duration = isset( $options['cfp']['ban_duration'] )? $options['cfp']['ban_duration'] : 7;
		$click_limit = isset( $options['cfp']['click_limit'] )? absint( $options['cfp']['click_limit'] ) : 3;
		
		$path = '';
		$domain = '';
		
		$pnd = $this->get_path_and_domain();
		
		$path = $pnd['path'];
		$domain = $pnd['domain'];
		
		?><script type="text/javascript">
		;var advadsCfpQueue = [], advadsCfpExpHours = <?php echo $exphours; ?>;
		var advadsCfpClickLimit = <?php echo $click_limit; ?>;
		<?php if ( $this->module_enabled ) : ?>var advadsCfpBan = <?php echo $ban_duration; endif; ?>;
		var advadsCfpPath = '<?php echo $path; ?>';
		var advadsCfpDomain = '<?php echo $domain; ?>';
		var advadsCfpAd = function( adID ){
			if ( 'undefined' == typeof advadsProCfp ) { advadsCfpQueue.push( adID ) } else { advadsProCfp.addElement( adID ) }
		};
		</script><?php
	}
	
	/**
	 * add the JS that adds this ad to the list of CFP
	 */
	public function ad_output( $output, $ad ) {
		$cond = $this->get_conditions_options( $ad );
		if ( !empty( $cond ) || $this->module_enabled ) {
			$output .= '<script type="text/javascript">;new advadsCfpAd( ' . $ad->id . ' );</script>';
		}
		return $output;
	}
	
	/**
	 * enqueue front end scripts 
	 */
	public function wp_enqueue_script() {
		$in_footer = defined( 'AUTOPTIMIZE_PLUGIN_DIR' );
		$uriRelPath = plugin_dir_url( __FILE__ );
		
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			wp_enqueue_script( 'advanced-ads-pro/cfp', $uriRelPath . 'assets/js/cfp.js', array( 'jquery', ADVADS_SLUG . '-advanced-js' ), AAP_VERSION, $in_footer );
		} else {
			wp_enqueue_script( 'advanced-ads-pro/cfp', $uriRelPath . 'assets/js/cfp.min.js', array( 'jquery', ADVADS_SLUG . '-advanced-js' ), AAP_VERSION, $in_footer );
		}
	}
	
	/**
	 * add the HTML attribute for front end JS 
	 */
	public function add_wrapper( $wrapper, $ad ) {
		$cond = $this->get_conditions_options( $ad );
		if ( !empty( $cond ) || $this->module_enabled ) {
			$wrapper['data-cfpa'] = $ad->id;
			
			// print all expiration hours
			if ( !empty( $cond ) ) {
				$hours = array();
				foreach( $cond as $_c ) {
					$hours[] = floatval( $_c['expiration'] );
				}
				$wrapper['data-cfph'] = implode( '_', $hours );
			}
		}
		return $wrapper;
	}

	/**
	 * Add new node to 'Ad health' if the user is banned.
	 *
	 * @param array $nodes.
	 * @return bool array $nodes.
	 */
	public function add_ad_health_node( $nodes ) {
		if ( isset( $_COOKIE['advads_pro_cfp_ban'] ) ) {
			$nodes[] = array( 'type' => 1, 'data' => array(
				'parent' => 'advanced_ads_ad_health',
				'id'    => 'advanced_ads_ad_health_cfp_enabled',
				'title' => __( 'Click Fraud Protection enabled', 'advanced-ads-pro' ),
				'href'  => 'https://wpadvancedads.com/manual/click-fraud-protection',
				'meta'   => array(
					'class' => 'advanced_ads_ad_health_warning',
					'target' => '_blank'
				)
			) );
		}
		return $nodes;
	}
}
