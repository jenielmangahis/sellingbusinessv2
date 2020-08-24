<?php
class Advanced_Ads_Responsive_Amp_Compat {
	public function __construct(  ) {
		// `WP AMP` plugin.
		if ( function_exists( 'is_wp_amp' ) ) {
			add_filter( 'amphtml_the_content', array( $this, 'amphtml_add_the_content' ) );
			add_filter( 'advanced-ads-output-wrapper-options', array( $this, 'gather_css' ), 10, 2 );
			add_action( 'amphtml_template_css', array( $this, 'add_amp_css' ) );
			add_action( 'amphtml_after_footer', array( 'Advanced_Ads_Responsive_Amp', 'add_adsense_auto_ads' ) );
		}
		// WP AMP Ninja.
		if ( defined( 'WPAMP_PLUGIN_PATH' ) ) {
			add_action( 'wpamp_custom_script', array( $this, 'wpamp_custom_script' ) );
			add_action( 'wpamp_google_analytics_code', array( 'Advanced_Ads_Responsive_Amp', 'add_adsense_auto_ads' ) );
		}
	}

	/**
	 * `WP AMP`: update list of allowed the_content filters.
	 *
	 * @param arr $hooks
	 * @return arr $hooks
	 */
	public function amphtml_add_the_content( $hooks ) {
		if ( class_exists( 'Advanced_Ads_Plugin', false )
			&& method_exists( Advanced_Ads_Plugin::get_instance(), 'get_content_injection_priority' ) 
		) {
			$priority = Advanced_Ads_Plugin::get_instance()->get_content_injection_priority();
			// only method name, since `WP AMP` plugin does not support class name
			$hooks[ $priority ][] = 'inject_content';
		}

		return $hooks;
	}

	/**
	 * `WP AMP`: gather css rules, since `WP AMP` does not allow inline css
	 *
	 * @param arr $wrapper_options
	 * @return obj Advanced_Ads_Ad $ad
	 */
	public function gather_css( $wrapper_options, Advanced_Ads_Ad $ad ) {
		if ( ! isset( $wrapper_options['id'] ) ) { return $wrapper_options; }

		if ( isset( $wrapper_options['style'] ) ) {
			$_style_values_string = '';
			foreach ( $wrapper_options['style'] as $_style_attr => $_style_values ){
				if ( is_array( $_style_values ) ) {
					$_style_values_string .= $_style_attr . ': ' .implode( ' ', $_style_values ). '; '; }
				else {
					$_style_values_string .= $_style_attr . ': ' .$_style_values. '; '; }
			}
			Advanced_Ads_Responsive_Amp::$css .= sprintf( '#%s{ %s }', $wrapper_options['id'], $_style_values_string );
		}

		return $wrapper_options;
	}

	/**
	 * Add css rules to header.
	 */
	public function add_amp_css() {
		echo Advanced_Ads_Responsive_Amp::$css;
	}

	/**
	 * WP AMP Ninja: add JS for Adsense Auto Ads.
	 */
	public function wpamp_custom_script() {
		if ( ! defined( 'ADVANCED_ADS_AMP_DISABLE_AUTO_AD_SCRIPT' ) ) {
			$adsense_data = Advanced_Ads_AdSense_Data::get_instance();
			$adsense_options = $adsense_data->get_options();
			if ( ! empty( $adsense_options['amp']['auto_ads_enabled'] ) ) {
				echo '<script async custom-element="amp-auto-ads" src="https://cdn.ampproject.org/v0/amp-auto-ads-0.1.js"></script>';
			}
		}
	}

}
