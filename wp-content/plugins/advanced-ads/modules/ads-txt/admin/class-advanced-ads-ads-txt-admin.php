<?php
/**
 * User interface for managing the 'ads.txt' file.
 */
class Advanced_Ads_Ads_Txt_Admin {
	/**
	 * AdSense network ID.
	 */
	const adsense = 'adsense';

	/**
	 * Constructor
	 *
	 * @param obj $strategy Advanced_Ads_Ads_Txt_Strategy.
	 */
	public function __construct( $strategy ) {
		$this->strategy = $strategy;

		add_filter( 'advanced-ads-sanitize-settings', array( $this, 'toggle' ), 10, 1 );
		add_action( 'update_option_advanced-ads-adsense', array( $this, 'update_adsense_option' ), 10, 2 );
		add_action( 'advanced-ads-settings-init', array( $this, 'add_settings' ) );
		add_action( 'wp_ajax_advads-ads-txt', array( $this, 'ajax_refresh_notices' ) );
	}


	/**
	 * Toggle ads.txt and add additional content.
	 *
	 * @param array $options Options.
	 * @return array $options Options.
	 */
	public function toggle( $options ) {
		$create = ! empty( $_POST['advads-ads-txt-create'] );
		$all_network = ! empty( $_POST['advads-ads-txt-all-network'] );
		$additional_content = ! empty( $_POST['advads-ads-txt-additional-content'] ) ? $_POST['advads-ads-txt-additional-content'] : '';

		$this->strategy->toggle( $create, $all_network, $additional_content );
		$content = $this->get_adsense_blog_data();
		$this->strategy->add_network_data( self::adsense, $content );
		$r = $this->strategy->save_options();

		if ( is_wp_error( $r ) ) {
			add_settings_error(
				'advanced-ads-adsense',
				'adsense-ads-txt-created',
				$r->get_error_message(),
				'error'
			);
		}

		return $options;
	}

	/**
	 * Update the 'ads.txt' file every time the AdSense settings are saved.
	 *
	 * @param array $prev Previous options.
	 * @return array $new New options.
	 */
	public function update_adsense_option( $prev, $new ) {
		$content = $this->get_adsense_blog_data( $new );
		$this->strategy->add_network_data( self::adsense, $content );
		$r = $this->strategy->save_options();

		if ( is_wp_error( $r ) ) {
			add_settings_error(
				'advanced-ads-adsense',
				'adsense-ads-txt-created',
				$r->get_error_message(),
				'error'
			);
		}
	}

	/**
	 * Add setting fields.
	 *
	 * @param string $hook The slug-name of the settings page.
	 */
	public function add_settings( $hook ) {

		$adsense_data = Advanced_Ads_AdSense_Data::get_instance();
		$adsense_id   = $adsense_data->get_adsense_id();

		add_settings_section(
			'advanced_ads_ads_txt_setting_section',
			'ads.txt',
			array( $this, 'render_ads_txt_section_callback' ),
			$hook
		);

		add_settings_field(
			'adsense-ads-txt-enable',
			'',
			array( $this, 'render_setting_toggle' ),
			$hook,
			'advanced_ads_ads_txt_setting_section'
		);

		add_settings_field(
			'adsense-ads-txt-content',
			'',
			array( $this, 'render_setting_additional_content' ),
			$hook,
			'advanced_ads_ads_txt_setting_section'
		);

	}

	public function render_ads_txt_section_callback() {
	}

	/**
	 * Render toggle settings.
	 */
	public function render_setting_toggle() {
		global $current_blog;
		$domain = isset( $current_blog->domain ) ? $current_blog->domain : '';

		$can_process_all_network = $this->can_process_all_network();
		$is_all_network = $this->strategy->is_all_network();

		$is_enabled = $this->strategy->is_enabled();
		include dirname( __FILE__ ) . '/views/setting-create.php';
	}

	/**
	 * Render additional content settings.
	 */
	public function render_setting_additional_content() {
		$content = $this->strategy->get_additional_content();
		$notices = $this->get_notices();
		include dirname( __FILE__ ) . '/views/setting-additional-content.php';
	}

	/**
	 * Check if other sites of the network can be processed by the user.
	 *
	 * @return bool
	 */
	private function can_process_all_network() {
		return ! Advanced_Ads_Ads_Txt_Utils::is_subdir()
			&& is_super_admin()
			&& is_multisite();
	}

	/**
	 * Get notices.
	 *
	 * @return string/bool Html markup on success or false if no cached data exists.
	 *                     In the latter case, an ajax request will fetch the data.
	 */
	public function get_notices() {
		$url = home_url( '/' );
		$parsed_url = wp_parse_url( $url );
		if ( ! isset( $parsed_url['scheme'] ) || ! isset ( $parsed_url['host'] ) ) {
			return;
		}

		$notices = array();
		$link = sprintf( '<a href="%1$s" target="_blank">%1$s</a>', esc_url( $url . 'ads.txt' ) );

		if ( Advanced_Ads_Ads_Txt_Utils::is_subdir() ) {
			$notices[] = array( 'advads-error-message', sprintf(
				esc_html__( 'The ads.txt file cannot be placed because the URL contains a subdirectory. You need to make the file available at %s', 'advanced-ads' ),
				sprintf( '<a href="%1$s" target="_blank">%1$s</a>', esc_url( $parsed_url['scheme'] . '://' . $parsed_url['host'] ) )
			) );
		} elseif ( $this->strategy->is_enabled() ) {
			if ( null === ( $file = $this->get_notice( 'get_file_info', $url ) ) ) {
				return false;
			}

			if ( ! is_wp_error( $file )) {
				if ( $file['exists'] ) {
					$notices[] = array( '', sprintf(
						esc_html__( 'The file is available on %s.', 'advanced-ads' ),
						$link
					) );
				} else {
					$notices[] = array( '', esc_html__( 'The file was not created.', 'advanced-ads' ) );
				}

				if ( $file['is_third_party'] ) {
					$notices[] = array( 'advads-error-message', sprintf(
						esc_html__( 'A third-party file exists: %s' ),
						$link
					) );
				}
			} else {
				$notices[] = array( 'advads-error-message', sprintf(
					esc_html__( 'An error occured: %s.', 'advanced-ads' ),
					esc_html( $file->get_error_message() ) )
				);
			}


			if ( null === ( $need_file_on_root_domain = $this->get_notice( 'need_file_on_root_domain', $url ) ) ) {
				return false;
			}

			if ( $need_file_on_root_domain ) {
				$notices[] = array( 'advads-ads-txt-nfor', sprintf(
					/* translators: %s the line that may need to be added manually */
					esc_html__( 'If your site is located on a subdomain, you need to add the following line to the ads.txt file of the root domain: %s', 'advanced-ads' ),
					// Without http://.
					'<code>subdomain=' . esc_html( $parsed_url['host'] ) . '</code>'
				) );
			}
		}

		$r = '<ul id="advads-ads-txt-notices">';
		foreach( $notices as $notice ) {
			$r .= sprintf( '<li class="%s">%s</li>', $notice[0], $notice[1] );
		}
		$r .= '</ul>';

		return $r;
	}

	/**
	 * Get a notice.
	 *
	 * @return null/bool Boolean on success or null if no cached data exists.
	 *                   In the latter case, this function should be called using AJAX
	 *                   to get fresh data.
	 */
	public static function get_notice( $func, $url ) {
		if ( ! method_exists( 'Advanced_Ads_Ads_Txt_Utils', $func ) ) {
			return false;
		}

		$url = $url ? $url : home_url( '/' );
		$is_ajax = defined( 'DOING_AJAX') && DOING_AJAX;
		$key = self::get_transient_key();
		$transient = get_transient( $key );

		if ( ! $is_ajax ) {
			return isset( $transient[ $func ] ) ? $transient[ $func ] : null;
		}

		$r = call_user_func( array( 'Advanced_Ads_Ads_Txt_Utils', $func ), $url );

		$transient[ $func ] = $r;
		set_transient( $key, $transient, WEEK_IN_SECONDS );
		return $r;
	}



	/**
	 * Get Adsense data.
	 *
	 * @return string
	 */
	public function get_adsense_blog_data( $new = null ) {
		if ( null === $new ) {
			$new = Advanced_Ads_AdSense_Data::get_instance()->get_options();
		}

		$adsense_id = ! empty( $new['adsense-id'] ) ? trim( $new['adsense-id'] ) : '';
		if ( ! $adsense_id ) {
			return '';
		}

		$data   = array(
			'domain'                  => 'google.com',
			'account_id'              => $adsense_id,
			'account_type'            => 'DIRECT',
			'certification_authority' => 'f08c47fec0942fa0'
		);
		$result = implode( ', ', $data );

		return $result;
	}

	/**
	 * Check if a third-party ads.txt file exists.
	 */
	public function ajax_refresh_notices() {

		check_ajax_referer( 'advanced-ads-admin-ajax-nonce', 'nonce' );

		if ( ! current_user_can( Advanced_Ads_Plugin::user_cap( 'advanced_ads_manage_options') ) ) {
			return;
		}

		echo $this->get_notices();
		exit;
	}

	/**
	 * Get transient key.
	 */
	public static function get_transient_key() {
		return 'advanced_ads_ads_txt_ctp' . home_url( '/') ;
	}

}


