<?php
class Advanced_Ads_Ad_Blocker
{
	/**
	 * Singleton instance of the plugin
	 *
	 * @var     Advanced_Ads_Ad_Blocker
	 */
	protected static $instance;

	/**
	 * module options
	 *
	 * @var     array (if loaded)
	 */
	protected $options;

	/**
	 * plugins directory URL
	 *
	 * @var     string
	 */
	protected $plugins_url;

	/**
	 * Initialize the module
	 */
	private function __construct() {
		$options = $this->options();
		if ( ! empty ( $options['use-adblocker'] ) &&
			 ! empty ( $options['folder_name'] ) &&
			 ! empty ( $options['module_can_work'] ) &&
			 $options['upload_dir']
		) {
			$this->plugins_url = plugins_url();
			add_action( 'plugins_loaded', array( $this, 'wp_plugins_loaded' ) );
		}
	}

	/**
	 * Return an instance of Advanced_Ads_Ad_Blocker
	 *
	 * @return  Advanced_Ads_Ad_Blocker
	 * @since   1.0.0
	 */
	public static function get_instance() {
		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance )
		{
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Add actions/filters/hooks and localisation after module have been loaded
	 *
	 * @since   1.0.0
	 */
	public function wp_plugins_loaded() {
		add_action( 'wp_enqueue_scripts', array( $this, 'edit_script_output' ), 101 );
	}

	/**
	 * Edit the script output (URL's) for all advanced-ads plugins
	 *
	 * @since   1.0.0
	 */
	public function edit_script_output() {
		global $wp_scripts, $wp_styles;

		// Get all plugin options
		$options = $this->options();

		// Check if the asset folder is set (check if this is installed yet)
		if( isset( $options['folder_name'] ) && $options['folder_name'] != '' )
		{
			// Loop through all script files and change the URL from which they are loaded
			if( is_object( $wp_scripts ) && is_array( $wp_scripts->registered ) ) foreach( $wp_scripts->registered as $script )
			{
				if( strpos( $script->src, 'advanced-ads' ) !== false )
				{
					$script->src = $this->clean_up_filename( $script->src );
				}
			}

			// Loop through all style files and change the URL from which they are loaded
			if( is_array( $wp_styles->registered ) ) foreach( $wp_styles->registered as $style )
			{
				if( strpos( $style->src, 'advanced-ads' ) !== false )
				{
					$style->src = $this->clean_up_filename( $style->src );
				}
			}
		}
	}

	public function clean_up_filename( $file ) {
		$options = $this->options();
		$upload_dir = $options['upload_dir'];
		$url = str_replace( $this->plugins_url, '', $file );

		if ( isset( $options['lookup_table'][ $url ] ) && is_array( $options['lookup_table'][ $url ] ) && isset( $options['lookup_table'][ $url ]['path'] ) ) {
			return trailingslashit( $upload_dir['baseurl'] ) . trailingslashit( $options['folder_name'] ) . $options['lookup_table'][ $url ]['path'];
		} elseif ( isset( $options['lookup_table'][ $url ] ) ) {
			return trailingslashit( $upload_dir['baseurl'] ) . trailingslashit( $options['folder_name'] ) . $options['lookup_table'][ $url ];
		}
		return $file;
	}

	/**
	 * Return module options
	 *
	 * @return  array $options
	 */
	public function options() {
		if ( ! isset( $this->options ) ) {
			if ( function_exists( 'is_multisite' ) && is_multisite() ) {
				global $current_site;
				// Switch to main blog.
				switch_to_blog( $current_site->blog_id );

				$this->options = get_option( ADVADS_AB_SLUG, array() );
				// Do not init options in the 'Advanced_Ads_Plugin' class.
				$advads_options = (array) get_option( ADVADS_SLUG, array() );
				$upload_dir = wp_upload_dir();

				restore_current_blog();
			} else {
				$this->options = get_option( ADVADS_AB_SLUG, array() );
				$advads_options = Advanced_Ads::get_instance()->options();
				$upload_dir = wp_upload_dir();
			}

			if ( ! $this->options ) {
				$this->options = array();
			}

			$this->options['use-adblocker'] = ! empty( $advads_options['use-adblocker'] );
			if ( $upload_dir['error'] ) {
				$this->options['upload_dir'] = false;
			} else {
				$upload_dir['url'] = set_url_scheme( $upload_dir['url'] );
				$upload_dir['baseurl'] = set_url_scheme( $upload_dir['baseurl'] );
				// array, that has indices 'basedir' and 'baseurl'
				$this->options['upload_dir'] = $upload_dir;
			}
		}
		return $this->options;
	}
}
