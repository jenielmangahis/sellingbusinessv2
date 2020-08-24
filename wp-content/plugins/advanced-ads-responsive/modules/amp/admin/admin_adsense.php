<?php
defined( 'WPINC' ) || exit;

class Advanced_Ads_Responsive_Amp_Adsense_Admin {
	public function __construct() {
		add_filter( 'advanced-ads-ad-notices', array( $this, 'ad_notices' ), 10, 3 );
		add_action( 'advanced-ads-gadsense-extra-ad-param', array( $this, 'extra_template' ), 10, 3 );
		add_action( 'advanced-ads-adsense-settings-init', array( $this, 'add_adsense_amp_setting' ) );
		add_filter( 'advanced-ads-save-options', array( $this, 'save_ad_options' ), 10, 2 );
	}

	/**
	 * Show warning if a non-AMP compatible option is selected.
	 *
	 * @param array $notices Notices.
	 * @return array $notices Notices.
	 */
	public function ad_notices( $notices, $box, $post ) {
		if ( function_exists( 'is_amp_endpoint' ) || function_exists( 'is_wp_amp' ) ) {
			switch ($box['id']){
				case 'ad-parameters-box' :
					// add warning if this is an non-AMP compatible AdSense ad
					// hidden by default and made visible with JS
					$notices[] = array(
						'text' => __( 'This ad type is not supported on AMP pages', 'advanced-ads-responsive' ),
						'class' => 'advanced-ads-adsense-amp-warning hidden',
					);
				    break;
			}
		}

		return $notices;
	}

	/**
	 * Shows AMP related fields/inputs in adsense ad param meta box.
	 *
	 * @param array $extra_params, array of extra parameters;
	 * @param obj   $content, the ad content object
	 * @param obj   $ad Advanced_Ads_Ad.
	 */
	public function extra_template( $extra_params, $content, $ad = 0 ) {
		if ( ! $ad ) { return; }

		$is_supported = Advanced_Ads_Responsive_Amp::is_supported_adsense_type( $content );
		$options = $ad->options( 'amp', array() );
		$option_name = 'advanced_ad[amp]';
		$width = absint( $ad->width );
		$height = absint( $ad->height );

		$layout = ! empty( $options['layout'] ) ? $options['layout'] : 'default';
		$width = ! empty( $options['width'] ) ? absint( $options['width'] ) : ( $width ? $width : 300 );
		$height = ! empty( $options['height'] ) ? absint( $options['height'] ) : ( $height ? $height : 250 );
		$fixed_height = ! empty( $options['fixed_height'] ) ? absint( $options['fixed_height'] ) : ( $height ? $height : 250 );

		include AAR_BASE_PATH . '/modules/amp/admin/views/adsense-size.php';
	}

	/**
	 * Add Adsense AMP setting.
	 *
	 * @param string $hook The slug-name of the settings page.
	 */
	public function add_adsense_amp_setting( $hook ) {
		add_settings_field(
			'adsense-amp',
			'AMP', // no translation needed, since AMP is the official acronym
			array( $this, 'render_settings_adsense_amp' ),
			$hook,
			'advanced_ads_adsense_setting_section'
		);
	}

	/**
	 * Render Adsense AMP setting fields.
	 */
	public function render_settings_adsense_amp() {
		$adsense_options = Advanced_Ads_AdSense_Data::get_instance()->get_options();
		$convert = ! empty( $adsense_options['amp']['convert'] );
		$width = ! empty( $adsense_options['amp']['width'] ) ? absint( $adsense_options['amp']['width'] ) : 300;
		$height = ! empty( $adsense_options['amp']['height'] ) ? absint( $adsense_options['amp']['height'] ) : 250;
		$auto_ads_enabled = ! empty( $adsense_options['amp']['auto_ads_enabled'] );
		$option_name = GADSENSE_OPT_NAME . '[amp]';
		include AAR_BASE_PATH . '/modules/amp/admin/views/setting-page-adsense.php';
	}

	/**
	 * Sanitize and save ad options.
	 *
	 * @param arr $options
	 * @param obj $ad Advanced_Ads_Ad.
	 * @return arr $options
	 */
	public function save_ad_options( array $options, Advanced_Ads_Ad $ad ) {
		if ( $ad->type === 'adsense' && isset( $_POST['advanced_ad']['amp'] ) ) {
			foreach ( (array) $_POST['advanced_ad']['amp'] as $_field => $_data ) {
				$options['amp'][ sanitize_key( $_field ) ] = sanitize_key( $_data );
			}
		}
		return $options;
	}


}



