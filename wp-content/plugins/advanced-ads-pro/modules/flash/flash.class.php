<?php

class Advanced_Ads_Pro_Module_Flash {

    	protected $options = array();

	public function __construct() {
		// load options (and only execute when enabled)
		$options = Advanced_Ads_Pro::get_instance()->get_options();
		if ( isset( $options['flash'] ) ) {
			$this->options = $options['flash'];
		}

		// only execute when enabled
		if ( ! isset( $this->options['enabled'] ) || ! $this->options['enabled'] ) {
			return ;
		}

		// register flash ad type
		add_filter( 'advanced-ads-ad-types', array( $this, 'register_flash_ad_types' ), 15 );
		// allow to upload swf files into media library
		add_filter( 'upload_mimes',array( $this, 'allow_swf_upload') );

		$is_admin = is_admin();
		$is_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX;

		if ( $is_admin && ! $is_ajax ) {
			// add referrer check to visitor conditions
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10, 2 );
		}
	}

	/**
	 * register flash ad type
	 *
	 * @since 1.0.0
	 * @param arr $types already registered ad types
	 * @return arr $types registered ad types
	 */
	function register_flash_ad_types( array $types ){
	    $types['flash'] = new Advanced_Ads_Ad_Type_Flash();

	    return $types;
	}

	/**
	 * allow upload of flash files into media library
	 *
	 * @since 1.0.0
	 * @param arr $mimes allowed mime types
	 * @return arr $mimes allowed mime types including swf
	 * @todo add a better check to only allow upload of swf files for ads
	 */
	public function allow_swf_upload( $mimes ) {

		// only allow for ad edit screen
		/*if( 'advanced_ads' !== get_current_screen()->post_type ) {
		    return $mimes;
		}*/
		$mimes['swf'] = 'application/x-shockwave-flash';
		return $mimes;
	}

	public function enqueue_scripts() {
		// add own code
		wp_register_style( 'advanced_ads_pro/flash', plugin_dir_url( __FILE__ ) . 'inc/flash.css', null, AAP_VERSION );
		wp_enqueue_style( 'advanced_ads_pro/flash' );
		wp_register_script( 'advanced_ads_pro/flash', plugin_dir_url( __FILE__ ) . 'inc/flash.js', array( 'jquery' ), AAP_VERSION );
		wp_enqueue_script( 'advanced_ads_pro/flash' );
	}
}
