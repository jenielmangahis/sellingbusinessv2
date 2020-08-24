<?php

class Advanced_Ads_Geo_Admin {
    
    /**
     * stores the settings page hook
     *
     * @since   1.0.0
     * @var     string
     */
    protected $settings_page_hook = '';
    
    /**
     * link to plugin page
     *
     * @since    1.0.0
     * @const
     */
    const PLUGIN_LINK = 'https://wpadvancedads.com/add-ons/geo-targeting/';
    
    /**
     * holds base class
     *
     * @var Advanced_Ads_Geo_Plugin
     * @since 1.0.0
     */
    protected $plugin;
    
    
    /**
     * Initialize the plugin by loading admin scripts & styles and adding a
     * settings page and menu.
     *
     * @since     1.0.0
     */
    public function __construct() {
        
        $this->plugin = Advanced_Ads_Geo_Plugin::get_instance();
        
        add_action('plugins_loaded', array($this, 'wp_admin_plugins_loaded'));
        // Add assets.
        add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
    }
    
    /**
     * load actions and filters
     */
    public function wp_admin_plugins_loaded() {
        
        if (!class_exists('Advanced_Ads_Admin', false)) {
            // show admin notice
            add_action('admin_notices', array($this, 'missing_plugin_notice'));
            
            return;
        }
        
        add_action('advanced-ads-settings-init', array($this, 'settings_init'), 10, 1);
        
        // ajax request to download the database
        add_action('wp_ajax_advads_download_geolite_database', array($this, 'download_database') );
    }
    
    /**
     * show warning if Advanced Ads is not activated
     */
    public function missing_plugin_notice() {
        
        $plugin_data = get_plugin_data(__FILE__);
        $plugins = get_plugins();
        
        if( isset( $plugins['advanced-ads/advanced-ads.php'] ) ){ // is installed, but not active
            $link = '<a class="button button-primary" href="' . wp_nonce_url( 'plugins.php?action=activate&amp;plugin=advanced-ads/advanced-ads.php&amp', 'activate-plugin_advanced-ads/advanced-ads.php' ) . '">'. __('Activate Now', 'advanced-ads-geo') .'</a>';
        } else {
            $link = '<a class="button button-primary" href="' . wp_nonce_url(self_admin_url('update.php?action=install-plugin&plugin=' . 'advanced-ads'), 'install-plugin_' . 'advanced-ads') . '">'. __('Install Now', 'advanced-ads-geo') .'</a>';
        }
        
        echo '<div class="error"><p>' . sprintf(__('<strong>Advanced Ads – Geo Targeting</strong> is an extension for the free Advanced Ads plugin.', 'advanced-ads-geo'), 'https://wpadvancedads.com') . '&nbsp;' . $link . '</p></div>';
    }
    
    /**
     * render license key section
     *
     * @since 1.0.0
     */
    public function render_settings_license_callback() {
        
        $licenses = get_option(ADVADS_SLUG . '-licenses', array());
        $license_key = isset($licenses['geo']) ? $licenses['geo'] : '';
        $license_status = get_option($this->plugin->options_slug . '-license-status', false);
        $index = 'geo';
        $plugin_name = AAGT_PLUGIN_NAME;
        $options_slug = $this->plugin->options_slug;
        $plugin_url = self::PLUGIN_LINK;
        
        // template in main plugin
        include ADVADS_BASE_PATH . 'admin/views/setting-license.php';
    }
    
    /**
     * add settings to settings page
     *
     * @param string $hook settings page hook
     */
    public function settings_init($hook) {
        
        // don’t initiate if main plugin not loaded
        if (!class_exists('Advanced_Ads_Admin')) {
            return;
        }
        
        // add license key field to license section
        add_settings_field(
            'geo-license', __('Geo Targeting', 'advanced-ads-geo'), array($this, 'render_settings_license_callback'), 'advanced-ads-settings-license-page', 'advanced_ads_settings_license_section'
            );
        
        // add field for the targeting method
        if( 1 < count( Advanced_Ads_Geo_Plugin::get_instance()->get_targeting_methods() ) ){
            add_settings_field(
                'geo-license', __('Method', 'advanced-ads-geo'), array($this, 'render_settings_method_callback'), $hook, 'advanced_ads_geo_setting_section'
                );
        }
        
        // add assistant setting field
        add_settings_field(
            'geo-locale', __( 'Language of names', 'advanced-ads-geo'), array( $this, 'render_settings_locale_option_callback' ), $hook, 'advanced_ads_geo_setting_section' );
        
        // add new section
        add_settings_section(
            'advanced_ads_geo_setting_section', __('Geo Targeting', 'advanced-ads-geo'), array($this, 'render_settings_section_callback'), $hook
            );
    }
    
    /**
     * Render settings section
     */
    public function render_settings_section_callback() {
        
        // check when the last update happened
        $last_update = get_option( AAGT_SLUG . '-last-update-geolite2', false );
        $next_update = $this->get_next_first_tuesday_timestamp();
        
        // render download of the geo database
        include AAGT_BASE_PATH . 'admin/views/setting-download.php';
    }
    
    /**
     * render option for the geo targeting method
     */
    public function render_settings_method_callback(){
        
        $options = Advanced_Ads_Geo_Plugin::get_instance()->options();
        $method = isset( $options[ AAGT_SLUG ]['method'] ) ? $options[ AAGT_SLUG ]['method'] : 'default';
        
        $methods = Advanced_Ads_Geo_Plugin::get_instance()->get_targeting_methods();
        include AAGT_BASE_PATH . 'admin/views/setting-method.php';
    }
    
    /**
     * render option for language of the geo information
     */
    public function render_settings_locale_option_callback(){
        
        $options = Advanced_Ads_Geo_Plugin::get_instance()->options();
        $locale = isset( $options[ AAGT_SLUG ]['locale'] ) ? $options[ AAGT_SLUG ]['locale'] : 'en';
        
        include AAGT_BASE_PATH . 'admin/views/setting-locale.php';
    }
    
    /**
     * Add visitor condition box
     *
     * @since    1.0.0
     */
    static function metabox_geo($options, $index = 0) {
        
        if (!isset($options['type']) || '' === $options['type']) {
            return;
        }
        
        $type_options = Advanced_Ads_Visitor_Conditions::get_instance()->conditions;
        
        if (!isset($type_options[$options['type']])) {
            return;
        }
        
        $method = Advanced_Ads_Geo_Plugin::get_current_targeting_method();
        $countries = Advanced_Ads_Geo_Api::get_countries();
        
        switch( $method ){
            case 'sucuri' :
                $my_country_isoCode = Advanced_Ads_Geo_Plugin::get_sucuri_country();
                $my_country = isset( $countries[ $my_country_isoCode ] ) ? $countries[ $my_country_isoCode ] : ' – ';
                $current_location = sprintf( '%s (%s)', $my_country, $my_country_isoCode );
                break;
            default :
                // get information from the current user to help him debugging issues
                $api = Advanced_Ads_Geo_Api::get_instance();
                $ip = $api->get_real_IP_address();
                $error = false;
                $my_country = '';
                $my_country_isoCode = '';
                $my_city = '';
                $my_lat = 0.0;
                $my_lon = 0.0;
                
                // get locale
                $plugin_options = Advanced_Ads_Geo_Plugin::get_instance()->options();
                $locale = isset( $plugin_options[ AAGT_SLUG ]['locale'] ) ? $plugin_options[ AAGT_SLUG ]['locale'] : 'en';
                
                if( $ip ){
                    try {
                        $reader = $api->get_GeoIP2_city_reader();
                        // $reader = $api->get_GeoIP2_country_reader();
                        
                        if( $reader ){
                            // Look up the IP address
                            $record = $reader->city($ip);
                            if ( ! empty( $record ) ) {
                                $my_city = ( $record->city->name ) ? $record->city->name : __( '(unknown city)', 'advanced-ads-geo' );
                                if( isset( $record->city->names[ $locale ] ) && $record->city->names[ $locale ] ) {
                                    $my_city = $record->city->names[ $locale ];
                                }
                                
                                $my_country = ( isset( $record->country->names[ $locale ] ) && $record->country->names[ $locale ] ) ? $record->country->names[ $locale ] : $record->country->names[ 'en' ];
                                $my_country_isoCode = $record->country->isoCode;
                                
                                // get first subdivision (region/state)
                                $my_region = ( isset( $record->subdivisions[0] ) && $record->subdivisions[0]->name ) ? $record->subdivisions[0]->name : __( '(unknown region)', 'advanced-ads-geo' );
                                if( isset( $record->subdivisions[0] ) && isset( $record->subdivisions[0]->names[ $locale ] ) && $record->subdivisions[0]->names[ $locale ] ) {
                                    $my_region = $record->subdivisions[0]->names[ $locale ];
                                }
                                if (isset($record->location) && isset($record->location->latitude) && isset($record->location->longitude)){
                                    $my_lat = $record->location->latitude;
                                    $my_lon = $record->location->longitude;
                                }
                            }
                        } else {
                            $error = sprintf( __( "Geo Database not found, you can download it in the <a href='%s'>settings</a>.", 'advanced-ads-geo' ), admin_url('admin.php?page=advanced-ads-settings') );
                        }
                        
                    } catch (\GeoIp2\Exception\AddressNotFoundException $e) {
                        $error = $e->getMessage() . ' ' . __( "Maybe you are working on a local or secured environment.", 'advanced-ads-geo' );
                    }
                } else {
                    $raw_IP = $api->get_raw_IP_address();
                    $error = '<span class="advads-error-message">' . __( 'Your IP address format is incorrect', 'advanced-ads-geo' ) . ' (' . $raw_IP . ')</span>';
                }
                
                if ( $error ){
                    $current_location = '<span class="advads-error-message">' . $error . '</span>';
                } else {
                    $current_location = $ip . ', ' . $my_country . ', ' . $my_region . ', ' . $my_city;
                    $current_location .= '<br/>' . __( 'Coordinates', 'advanced-ads-geo' ) . ': (' . $my_lat . ' / ' . $my_lon . ')';
                }
        }
        
        
        // form name basis
        $name = Advanced_Ads_Visitor_Conditions::FORM_NAME . '[' . $index . ']';
        $operator = isset($options['operator']) ? $options['operator'] : 'is';
        $geo_mode = isset($options['geo_mode']) ? $options['geo_mode'] : 'classic';
        ?><input type="hidden" name="<?php echo $name; ?>[type]" value="<?php echo $options['type']; ?>"/>
        
        <style type="text/css">
            span.geomode { display: inline-block; clear: both; margin: 0px 0px 5px 0px; }
            span.geomode input[type=radio] { display: block; float: left; margin: 0px 5px 0px 5px; }
        </style>

        <div style="margin-bottom: 10pt;">
        	<span class="geomode">
            	<input id="radio_classic_<?php echo $index?>" type="radio" name="<?php echo $name; ?>[geo_mode]" <?php checked($geo_mode, "classic")?> value="classic" onclick="advads_geo_admin.set_mode(<?php echo $index?>, this.value);">
            	<label for="radio_classic_<?php echo $index?>"><?php _e( 'by specific location', 'advanced-ads-geo' ); ?></label>
            </span>
            <span class="geomode">
            	<input class="geomode" id="radio_latlon_<?php echo $index?>" type="radio" name="<?php echo $name; ?>[geo_mode]" <?php checked($geo_mode, "latlon")?> value="latlon" onclick="advads_geo_admin.set_mode(<?php echo $index?>, this.value);">
            	<label for="radio_latlon_<?php echo $index?>"><?php _e( 'by radius', 'advanced-ads-geo' ); ?></label>
        	</span>
        </div>
        
        
        <div class="geomode" id="advads_geo_classic_<?php echo $index?>"<?php if ($geo_mode != 'classic') echo ' style="display:none;"'?>>
        	<select name="<?php echo $name; ?>[operator]">
        		<option value="is" <?php selected( 'is', $operator ); ?>><?php _e( 'is', 'advanced-ads-geo' ); ?></option>
        		<option value="is_not" <?php selected( 'is_not', $operator ); ?>><?php _e( 'is not', 'advanced-ads-geo' ); ?></option>
        	</select><?php
        
        	switch( $method ) :
        		case 'sucuri' :
        			$country = isset($options['country']) ? $options['country'] : $my_country_isoCode;
        			?><select name="<?php echo $name; ?>[country]"><?php foreach ($countries as $_code => $_title) : ?>
        				<option value="<?php echo $_code; ?>" <?php selected($_code, $country); ?>><?php echo $_title; ?></option>
        			    <?php endforeach;
        			?></select><?php
        		    break;
        		default :
        			$country = isset($options['country']) ? $options['country'] : $my_country_isoCode;
        			$region = isset($options['region']) ? $options['region'] : '';
        			$city = isset($options['city']) ? $options['city'] : '';
        			?><select name="<?php echo $name; ?>[country]"><?php foreach ($countries as $_code => $_title) : ?>
        				<option value="<?php echo $_code; ?>" <?php selected($_code, $country); ?>><?php echo $_title; ?></option>
        			    <?php endforeach; ?>
        			</select> <?php _e( 'or', 'advanced-ads-geo' ); ?>
        			<input type="text" name="<?php echo $name; ?>[region]" value="<?php echo $region; ?>" placeholder="<?php _e( 'State/Region', 'advanced-ads-geo' ); ?>"/>
        			<?php _e( 'or', 'advanced-ads-geo' ); ?>
        			<input type="text" name="<?php echo $name; ?>[city]" value="<?php echo $city; ?>" placeholder="<?php _e( 'City', 'advanced-ads-geo' ); ?>"/>
                    <?php
        		break;
        	endswitch;?>
        </div>
        <div id="advads_geo_latlon_<?php echo $index?>"<?php if ($geo_mode != 'latlon') echo ' style="display:none;"'?>>
        	<?php 
        	switch( $method ) :
        		case 'sucuri' :
        			_e( 'This option can’t be used if your site is using sucuri.net.', 'advanced-ads-geo' );
        		    break;
        		default :
                    $lat = isset($options['lat']) ? $options['lat'] : '';
                    $lon = isset($options['lon']) ? $options['lon'] : '';
                    $distance = isset($options['distance']) ? $options['distance'] : '';
                    $distance_condition = isset($options['distance_condition']) ? $options['distance_condition'] : '';
                    $distance_unit = isset($options['distance_unit']) ? $options['distance_unit'] : '';
                    ?><select name="<?php echo $name; ?>[distance_condition]">
                        <option value="lte" <?php selected($distance_condition, 'lte')?>><?php _e( 'Distance is less than or equal to', 'advanced-ads-geo' ); ?></option>
                        <option value="gt" <?php selected($distance_condition, 'gt')?>><?php _e( 'Distance is greater than', 'advanced-ads-geo' ); ?></option>
                    </select>
                    <input type="number" name="<?php echo $name; ?>[distance]" maxlength="8" style="width:80pt;" value="<?php echo $distance; ?>" placeholder="<?php _e( 'Distance', 'advanced-ads-geo' ); ?>"/>
                    <select name="<?php echo $name; ?>[distance_unit]">
                        <option value="km" <?php selected($distance_unit, 'km')?>><?php _e('kilometers (km)', AAGT_SLUG) ?></option>
                        <option value="mi" <?php selected($distance_unit, 'mi')?>><?php _e('miles (mi)', AAGT_SLUG) ?></option>
                    </select><br/><?php _e( 'from', 'advanced-ads-geo' ); ?><br/>
                    <button type="button" class="button" onclick="advads_geo_admin.click_locname(<?php echo $index?>)"><?php _e('get coordinates', 'advanced-ads-geo')?></button>
                    <input type="number" step="any" id="advads_geo_input_lat_<?php echo $index?>" style="width:80pt; text-align:right;" name="<?php echo $name; ?>[lat]" value="<?php echo $lat; ?>" placeholder="<?php _e( 'Latitude', 'advanced-ads-geo' ); ?>" title="<?php _e( 'Latitude', 'advanced-ads-geo' ); ?>"/> /
                    <input type="number" step="any" id="advads_geo_input_lon_<?php echo $index?>" style="width:80pt; text-align:right;" name="<?php echo $name; ?>[lon]" value="<?php echo $lon; ?>" placeholder="<?php _e( 'Longitude', 'advanced-ads-geo' ); ?>" title="<?php _e( 'Longitude', 'advanced-ads-geo' ); ?>"/>
                    <br>
                    <?php
                    if ($lat !== '' && $lon !== '' && $my_lat && $my_lon){
                        $distance = Advanced_Ads_Geo::calculate_distance($my_lat, $my_lon, $lat, $lon, $distance_unit);
                        $current_location .= '<br/>' . __('Distance to center', 'advanced-ads-geo') . ' ( ' . $lat . ' / ' . $lon . ' ): ' . round($distance,1) . ' ' . $distance_unit;
                    }
        		break;
        	endswitch;?>
        </div><?php 
        $latlon_city = isset($options['latlon_city']) ? $options['latlon_city'] : '';
        ?><div id="advads_geo_latlon_by_city_<?php echo $index?>" style="display: none; margin:3pt; padding:3pt;">
        	<input id="advads_geo_input_search_city_<?php echo $index?>" type="text" name="<?php echo $name; ?>[latlon_city]" value="<?php echo $latlon_city; ?>" placeholder="<?php _e( 'City', 'advanced-ads-geo' ); ?>">
        	<button type="button" class="button button-primary" onclick="advads_geo_admin.search_loc(<?php echo $index?>)"><?php _e( 'Search', 'advanced-ads-geo' ); ?></button>
        	<button type="button" class="button" onclick="advads_geo_admin.search_loc_close(<?php echo $index?>)"><?php _e( 'Cancel', 'advanced-ads-geo' ); ?></button>
        	<p class="description"><?php _e('Enter the name of the city, click the search button and pick one of the results to set the coordinates of the center.', 'advanced-ads-geo') ?></p>
        	<div id="advads_geo_latlon_loading_<?php echo $index?>" class="spinner is-active" style="display:none; float: none;"></div>
        	<div id="advads_geo_latlon_results_<?php echo $index?>"></div>
        </div>
    <p class="description"><br/><?php echo $type_options[$options['type']]['description']; ?></p>
	<p class="description"><?php _e( 'Your location', 'advanced-ads-geo' )?>: <?php echo $current_location; ?></p><?php
    }

    /**
     * download database
     *
     * @since 1.0.0
     */
    public function download_database() {
	// $scheme = 'https' . (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] === 'on' ? 's' : '');
	$scheme = 'https'; // https only now

	// download source
	$download_urls = array(
	    'city' => $scheme . '://geolite.maxmind.com/download/geoip/database/GeoLite2-City.mmdb.gz',
	    'country' => $scheme . '://geolite.maxmind.com/download/geoip/database/GeoLite2-Country.mmdb.gz'
	);

	$upload_sub_directory = Advanced_Ads_Geo_Plugin::get_instance()->get_upload_dir();

	$filenames = array(
	    'city' => $upload_sub_directory. '/GeoLite2-City.mmdb',
	    'country' => $upload_sub_directory. '/GeoLite2-Country.mmdb'
	);

	$upload_dir = wp_upload_dir();

	// create upload directory if not exists yet
	if (!file_exists($upload_dir['basedir'] . $upload_sub_directory )) {
	    mkdir($upload_dir['basedir'] . $upload_sub_directory );
	}

	$was_success = true;

	foreach ($download_urls as $key => $download_url) {
	    // variable with the name of the database file to download.
	    $db_file = $upload_dir['basedir'] . $filenames[$key];

	    $result = $this->download_geolite2_database($download_url, $db_file);
	    if (!isset($result['state']) || !$result['state']) {
		echo $result['message'];
		$was_success = false;
		exit();
	    }

	    echo __( 'Database updated successfully!', 'advanced-ads-geo' );
	}

	if( $was_success ){
	    update_option( AAGT_SLUG . '-last-update-geolite2', time() );
	}

	exit();
    }

    /**
     * download GeoLite2 databases
     * @param str $download_url download url
     * @param str $db_file target file
     * @return boolean
     */
    private function download_geolite2_database( $download_url, $db_file ) {
	// download the file from MaxMind, this places into temporary location.
	$temp_file = download_url($download_url);
	$result['state'] = false;
	$result['message'] = "Unknown error";

	// If we failed, through a message, otherwise proceed.
	if (is_wp_error($temp_file )) {
	    $message = 'Advanced Ads Geo: ' . sprintf(__('Error downloading database from: %s - %s', 'advanced-ads-geo' ), $download_url, $temp_file->get_error_message());
	    error_log( $message );
	} else {
	    // Open the downloaded file to unzip it.
	    $zip_handle = gzopen($temp_file, 'rb');

	    // Create the new file to unzip to.
	    $new_file = fopen($db_file, 'wb');

	    // If we failed to open the downloaded file, throw an error and remove the temporary file. Otherwise unzip.
	    if (!$zip_handle) {
		$result['message'] = 'Advanced Ads Geo: ' . sprintf(__( 'Could not open downloaded database for reading: %s', 'advanced-ads-geo' ), $temp_file);
		error_log($result['message']);
		unlink($temp_file);
	    } else {
		// If we failed to open the new file, throw and error and remove the temporary file. Otherwise unzip.
		if (!$new_file) {
		    $result['message'] = 'Advanced Ads Geo: ' .sprintf(__( 'Could not open database for writing %s', 'advanced-ads-geo' ), $db_file);
		    error_log($result['message']);
		    unlink($temp_file);
		} else {
		    while (( $data = gzread($zip_handle, 4096) ) != false) {
			fwrite($new_file, $data);
		    }

		    // close the files.
		    gzclose($zip_handle);
		    fclose($new_file);

		    // delete temporary file.
		    unlink($temp_file);

		    // display success message.
		    $result['message'] = "";
		    $result['state'] = true;
		}
	    }
	}

	return $result;
    }

    /**
     * get timestamp of the next first Tuesday of a month (either this month or the next)
     *
     * @since 1.0.0
     * @param str $time timestamp from which to get the next available Tuesday
     * @return timestamp of the next Tuesday (midnight GMT)
     */
    public function get_next_first_tuesday_timestamp( $time = 0 ){

	    // we actually use Wednesday since it returns midnight
	    if ( ! $time ){
		$time = time();
	    }

	    // current month
	    $month = date( 'F', $time );
	    $year = date( 'Y', $time );
	    $next_tuesday_t = strtotime("first Wednesday of $month $year");

	    // if this is the past, get first Tuesday of next month
	    if( $next_tuesday_t < time() ){
		$next_month_t = strtotime('next month');
		$month = date( 'F', $next_month_t );
		$year = date( 'Y', $next_month_t );
		$next_tuesday_t = strtotime("first Tuesday of $month $year");
	    }

	    return $next_tuesday_t;
    }

    /**
     * check if the databases are working
     */
    public function check_database(){
	    $api = Advanced_Ads_Geo_Api::get_instance();
	    return $api->get_GeoLite_country_filename() && $api->get_GeoLite_city_filename();
    }

    /**
     * check if an updated version of the database might already be available
     *  we use the timestamp of the last update that actually happened and check,
     * if the next first Tuesday of a month that followed is already in the past
     *
     * @since 1.0.0
     * @return bool true, if update should be available
     */
    public function is_update_available( ){

	// check if database is available
	if( ! $this->check_database() ){
	    return true;
	}

	// current time
	$now_t = time();

	// get last update from the database
	$last_update_t = get_option( AAGT_SLUG . '-last-update-geolite2', $now_t );
	// return true, if last update is more than 31 days ago
	$month_in_seconds = 31 * DAY_IN_SECONDS;
	if( $month_in_seconds <= $now_t - $last_update_t ){
	    return true;
	}

	// get next Tuesday following the update
	$next_tuesday_t = $this->get_next_first_tuesday_timestamp( $last_update_t );

	if( $next_tuesday_t < $now_t ){
	    return true;
	}

	return false;
    }

    /**
     * check if license is valid
     *
     * @since 1.0.0
     * @return bool true if license is valid
     */
    public function license_valid(){
	    $status = Advanced_Ads_Admin_Licenses::get_instance()->get_license_status( 'advanced-ads-geo' );
	    if( 'valid' === $status ){
		return true;
	    }
	    return false;
    }
    
    /**
     * Register and enqueue admin-specific scripts.
     */
    public function enqueue_admin_scripts() {
        $handle = AAGT_SLUG . '-admin-script';
        $translation = array(
            'found_results' => __('Found $1 results. Please pick the one, you want to use.', AAGT_SLUG),
            'no_results' => __('Your search did not return any results.', AAGT_SLUG),
            'could_not_retrieve_city' => __('There was an error connecting to the search service.', AAGT_SLUG),
        );
        wp_register_script( $handle, AAGT_BASE_URL . 'admin/assets/admin.js' );
        wp_localize_script( $handle, 'advads_geo_translation', $translation );
        wp_enqueue_script($handle);
    }

}
