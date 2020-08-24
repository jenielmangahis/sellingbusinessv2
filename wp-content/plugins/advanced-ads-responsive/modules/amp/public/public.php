<?php
defined( 'WPINC' ) || exit;

class Advanced_Ads_Responsive_Amp {
	/**
	 * Css rules in header.
	 * 
	 * @var str
	 */
	public static $css = '';

	public static $supported_adsense_types = array(
		'normal',
		'responsive',
		'matched-content',
		'link',
		'link-responsive',
		'in-article',
		//'in-feed'
	);

	public function __construct() {
		if ( ! is_admin() ) {
			// Fires before:
			// - cache-busting frontend is initialized
			// - tracking method is set
			add_action( 'wp', array( $this, 'wp' ), 9 );
		}

		add_filter( 'advanced-ads-ad-types', array( $this, 'add_amp_ad_type' ) );
		add_filter( 'advanced-ads-display-conditions', array( $this, 'add_amp_display_condition' ) );
	}

	/**
	 * Load actions and filters.
	 */
	public function wp() {
		if ( ! class_exists( 'Advanced_Ads', false ) ) { return; }

		add_filter( 'advanced-ads-can-display', array( $this, 'can_display' ), 10, 2 );

		if ( ! function_exists( 'advads_is_amp' ) || ! advads_is_amp() ) { return; }

		// Disable cache-busting when AMP version of a post is being viewed.
		add_filter( 'advanced-ads-pro-cb-frontend-disable', '__return_true' );
		add_filter( 'advanced-ads-tracking-method', array( $this, 'set_tracking_method' ) );

		add_filter( 'advanced-ads-get-ad-placements-array', array( $this, 'exclude_placements' ) );
		add_filter( 'advanced-ads-gadsense-output', array( $this, 'prepare_gadsense_output' ), 10, 4 );

		// `AMP WP` based.
		if ( function_exists( 'is_amp_endpoint' ) ) {
			add_action( 'amp_post_template_data', array( $this, 'add_component_script' ) );
			add_action( 'amp_post_template_css', array( $this, 'add_amp_css' ) );

			if ( defined( 'AMPFORWP_PLUGIN_DIR' ) ) {
				// `Accelerated Mobile Pages`
				add_action( 'ampforwp_body_beginning', array( 'Advanced_Ads_Responsive_Amp', 'add_adsense_auto_ads' ), 0 );
			} elseif ( class_exists( 'Bunyad_Core' ) ) {
				// SmartMag theme (http://theme-sphere.com/smart-mag/documentation/)
				add_action( 'bunyad_amp_pre_main', array( 'Advanced_Ads_Responsive_Amp', 'add_adsense_auto_ads' ) );
			} else {
				// `AMP WP`
				add_action( 'amp_post_template_include_' . 'header', array( 'Advanced_Ads_Responsive_Amp', 'add_adsense_auto_ads' ) );
			}

		// other AMP plugins
		} else {
			require_once( AAR_AMP_PATH . 'include/compat.php' );
			new Advanced_Ads_Responsive_Amp_Compat();
		}
	}

	/**
	 * Set method that does not require js.
	 *
	 * @param str $method
	 * @return str $method
	 */
	public function set_tracking_method( $method ) {
		if ( in_array( $method, array( 'frontend' ) ) ) {
			$method = 'onrequest';
		}

		return $method;
	}

	/**
	 * Disable placements that do not make sense in AMP context.
	 *
	 * @param array $placements
	 * @return array $placements
	 */
	public function exclude_placements( $placements ) {
		foreach( $placements as $_k => $_placement ){
			if ( ! isset( $_placement['type'] ) 
				|| ! in_array( $_placement['type'], array( 'default', 'post_top', 'post_bottom', 'post_content' ) )
			) { unset( $placements[ $_k ] ); }
		}

		return $placements;
	}

	/**
	 * Check if the ad can be displayed.
	 *
	 * @param bool $can_display existing value
	 * @param obj $ad Advanced_Ads_Ad
	 * @return bool true if should be displayed, false otherwise
	 */
	public function can_display( $can_display, Advanced_Ads_Ad $ad ) {
		if ( ! $can_display ) {
			return false;
		}

		if ( ! advads_is_amp() ) {
			// disable ads with type 'amp'
			return $ad->type !== 'amp';
		}

		return true;
	}

	/**
	 * Prepare gadsense frontend output for showing on AMP page.
	 *
	 * @param str/bool $output existing output
	 * @param obj $ad Advanced_Ads_Ad
	 * @param str $pub_id publisher ID
	 * @param obj $content ad content
	 * @return str new output
	 */
	public function prepare_gadsense_output( $output, Advanced_Ads_Ad $ad, $pub_id, $content ) {
		global $gadsense;

		if ( ! self::is_supported_adsense_type( $content ) ) {
			return '';
		}

		$count = $gadsense['adsense_count'];
		$selector = 'gadsense_slot_' . $count;
		$width = absint( $ad->width );
		$height = absint( $ad->height );

		$options = $ad->options( 'amp', array() );
		$layout = ! empty( $options['layout'] ) ? $options['layout'] : 'default';

		$output_part = sprintf( '<amp-ad type="adsense" data-ad-client="ca-%s" data-ad-slot="%s" ', $pub_id, $content->slotId );

		switch ( $layout ) {
			case 'default':
				switch ( $content->unitType ) {
					case 'normal':
					case 'link':
						// Fixed width and height with no responsiveness supported.
						if ( $width > 0 && $height > 0  ) {
							$ad->wrapper['class'][] = $selector;
							return $output_part . sprintf( 'layout="fixed" width="%s" height="%s"></amp-ad>', $width, $height );
						}
						break;
					case 'responsive':
						if ( isset( $content->resize ) && 'manual' === $content->resize
							// This plugin sometimes outputs CSS too early.
							// Skipped because we cannot use inline CSS styles on AMP pages.
							&& ! defined( 'AMPFORWP_PLUGIN_DIR' )
						) {
							// Process 'advanced' resizing.
							self::$css .= $this->get_adsense_manual_css( $ad, $selector, $content );
							return sprintf( '<div class="%s">%slayout="fill"></amp-ad></div>', $selector, $output_part );
						}
						break;
				}

				$adsense_options = Advanced_Ads_AdSense_Data::get_instance()->get_options();
				if ( ! empty( $adsense_options['amp']['convert'] ) ){
					$width = ! empty( $adsense_options['amp']['width'] ) ? absint( $adsense_options['amp']['width'] ) : 300;
					$height = ! empty( $adsense_options['amp']['height'] ) ? absint( $adsense_options['amp']['height'] ) : 250;
					return $output_part . sprintf( 'layout="responsive" width="%s" height="%s"></amp-ad>', $width, $height );
				}
				break;
			case 'responsive':
				$width = ! empty( $options['width'] ) ? absint( $options['width'] ) : ( $width ? $width : 300 );
				$height = ! empty( $options['height'] ) ? absint( $options['height'] ) : ( $height ? $height : 250 );
				return $output_part . sprintf( 'layout="responsive" width="%s" height="%s"></amp-ad>', $width, $height );
				break;
			case 'fixed_height':
				$fixed_height = ! empty( $options['fixed_height'] ) ? absint( $options['fixed_height'] ) : ( $height ? $height : 250 );
				return $output_part . sprintf( 'layout="fixed-height" height="%s"></amp-ad>', $fixed_height );
				break;
			case 'hide':
				return '';
				break;
		}

		// completely disable the ad
		return '';
	}

	/**
	 * Add js to the header.
	 */
	public function add_component_script( $data ) {
		if ( ! defined( 'ADVANCED_ADS_AMP_DISABLE_AD_SCRIPT' ) ) {
			$data['amp_component_scripts']['amp-ad'] = 'https://cdn.ampproject.org/v0/amp-ad-0.1.js';
		}
		if ( ! defined( 'ADVANCED_ADS_AMP_DISABLE_AUTO_AD_SCRIPT' ) ) {
			$adsense_data = Advanced_Ads_AdSense_Data::get_instance();
			$adsense_options = $adsense_data->get_options();
			if ( ! empty( $adsense_options['amp']['auto_ads_enabled'] ) ) {
				$data['amp_component_scripts']['amp-auto-ads'] = 'https://cdn.ampproject.org/v0/amp-auto-ads-0.1.js';
			}
		}

		return $data;
	}

	/**
	 * Add css rules to header.
	 */
	public function add_amp_css() {
		echo self::$css;
	}

    /**
     * get css used in manual (advanced) resizing
     *
     * @param obj Advanced_Ads_Ad
     * @param str $selector css selector
     * @param obj $content
     * @return str
     */
	private function get_adsense_manual_css( Advanced_Ads_Ad $ad, $selector, $content ) {
		$output = '.' . $selector . '{ position: relative; }' . "\n";
		// The last rule hide the ad
		$last_rule_hidden = null;

		if (isset($content->defaultHidden) && true == $content->defaultHidden) {
			$output .= '.' . $selector . '{display: none;}' . "\n";
			$last_rule_hidden = true;
		} else {
			if (!empty($ad->width) || !empty($ad->height)) {
				$w = (!empty($ad->width)) ? 'width: ' . $ad->width . 'px;' : '';
				$h = (!empty($ad->height)) ? 'height: ' . $ad->height . 'px;' : '';
				$output .= '.' . $selector . '{ display: inline-block; ' . $w . ' ' . $h . '}' . "\n";
			}
		}
		if (!empty($content->media)) {
			foreach ($content->media as $value) {

				$rule = explode(':', $value);
				$hidden = (isset($rule[3]) && '1' == $rule[3])? true : false;

				if ($hidden) {
					// the ad is hidden for this min-width
					$output .= '@media (min-width:' . $rule[0] . 'px) { .' . $selector . ' { display: none;} }' . "\n";

					// Mark this flag to true, so on the next iteration, the display attribute can be set to inline-block (if not hidden)
					$last_rule_hidden = true;

				} else {
					/**
					 * Not hidden, but firstly check if the lastly defined rule hide the ad
					 */
					if ($last_rule_hidden) {
						$output .= '@media (min-width:' . $rule[0] . 'px) { .' . $selector . ' { display: inline-block; width: ' . $rule[1] . 'px; height: ' . $rule[2] . 'px; } }' . "\n";
						$last_rule_hidden = false;
					} else {
						// do not touch the $last_rule_hidden var, it is already FALSE or NULL
						$output .= '@media (min-width:' . $rule[0] . 'px) { .' . $selector . ' { width: ' . $rule[1] . 'px; height: ' . $rule[2] . 'px; } }' . "\n";
					}
				}

			}
		}

		return $output;
	}

	/**
	 * Initialize ad type and add it to the plugins ad types.
	 *
	 * @param arr $types
	 * @return arr $types
	 */
	public function add_amp_ad_type( $types ) {
		require_once AAR_AMP_PATH . 'include/class-ad-type-amp.php';
		$types['amp'] = new Advanced_Ads_Ad_Type_Amp();
		return $types;
	}

	/**
	 * Add AMP display condition.
	 *
	 * @param arr $conditions display conditions of the main plugin
	 * @return arr $conditions new display conditions
	 */
	public function add_amp_display_condition( $conditions ){
		$conditions['amp'] = array(
			'label' => __( 'Accelerated Mobile Pages', 'advanced-ads-responsive' ),
			'description' => __( 'Display ads on Accelerated Mobile Pages', 'advanced-ads-responsive' ),
			'metabox' => array( 'Advanced_Ads_Responsive_Amp_Admin', 'metabox_amp' ), // callback to generate the metabox
			'check' => array( 'Advanced_Ads_Responsive_Amp', 'check_amp_display_condition' ) // callback for frontend check
		);

		return $conditions;
	}

	/**
	 * Check AMP display condition in frontend.
	 *
	 * @param arr $options options of the condition
	 * @param obj $ad Advanced_Ads_Ad
	 * @return bool true if can be displayed
	 */
	public static function check_amp_display_condition( $options = array(), Advanced_Ads_Ad $ad ) {
	    if ( ! isset( $options['operator'] ) ) {
			return true;
	    }

	    switch ( $options['operator'] ){
		    case 'is' :
			    if ( ! advads_is_amp() ) { return false; }
			    break;
		    case 'is_not' :
			    if ( advads_is_amp() ) { return false; }
			    break;
	    }

	    return true;
	}


	/**
	 * Check if a type of adsense ad is supported.
	 *
	 * @param obj $content, the ad content object
	 * @return bool
	 */
	public static function is_supported_adsense_type( $content ) {
		if ( ! isset( $content->unitType ) ) {
			return false;
		}
		return in_array( $content->unitType, self::$supported_adsense_types );
	}

	/**
	 * Add Adsense Auto Ads.
	 */
	public static function add_adsense_auto_ads() {
		$adsense_data = Advanced_Ads_AdSense_Data::get_instance();
		$adsense_options = $adsense_data->get_options();

		if ( ! empty( $adsense_options['amp']['auto_ads_enabled'] ) ) {
			if ( $pub_id = $adsense_data->get_adsense_id() ) {
				printf( '<amp-auto-ads type="adsense" data-ad-client="ca-%s"></amp-auto-ads>', $pub_id );
			}
		}
	}
}
