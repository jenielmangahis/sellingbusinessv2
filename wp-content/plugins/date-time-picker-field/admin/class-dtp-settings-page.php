<?php

/**
 * WordPress settings
 *
 * @author Carlos Moreira
 */
if ( ! class_exists( 'dtpicker_Settings_API_Test' ) ) :
	class DTP_Settings_Page {

		private $settings_api;

		public function __construct() {
			$this->settings_api = new WeDevs_Settings_API();

			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
		}

		public function admin_init() {

			// set the settings
			$this->settings_api->set_sections( $this->get_settings_sections() );
			$this->settings_api->set_fields( $this->get_settings_fields() );

			// initialize settings
			$this->settings_api->admin_init();
		}

		public function admin_menu() {
			add_options_page( __( 'DateTime Picker', 'date-time-picker-field' ), __( 'DateTime Picker', 'date-time-picker-field' ), 'manage_options', 'dtp_settings', array( $this, 'plugin_page' ) );
		}

		public function get_settings_sections() {
			$sections = array(
				array(
					'id'    => 'dtpicker',
					'title' => __( 'Basic Settings', 'date-time-picker-field' ),
				),

				array(
					'id'    => 'dtpicker_advanced',
					'title' => __( 'Advanced Settings', 'date-time-picker-field' ),
				),
			);
			return $sections;
		}

		/**
		 * Returns all the settings fields
		 *
		 * @return array settings fields
		 */
		public function get_settings_fields() {

			$settings_fields = array(
				'dtpicker_advanced' => array(
					array(
						'name'    => 'disabled_days',
						'label'   => __( 'Disable Days', 'date-time-picker-field' ),
						'desc'    => __( 'Select days you want to <strong>disable</strong>', 'date-time-picker-field' ),
						'type'    => 'multicheck',
						'default' => array(),
						'options' => array(
							'0' => __( 'Sunday', 'date-time-picker-field' ),
							'1' => __( 'Monday', 'date-time-picker-field' ),
							'2' => __( 'Tuesday', 'date-time-picker-field' ),
							'3' => __( 'Wednesday', 'date-time-picker-field' ),
							'4' => __( 'Thursday', 'date-time-picker-field' ),
							'5' => __( 'Friday', 'date-time-picker-field' ),
							'6' => __( 'Saturday', 'date-time-picker-field' ),
						),
					),

					array(
						'name'    => 'allowed_times',
						'label'   => __( 'Default List of allowed times' ),
						'desc'    => __( 'Write the allowed times to <strong>override</strong> the time step and serve as default if you use the options below.<br> Values still need to be within minimum and maximum times defined in the basic settings.<br> Use the time format separated by commas. Example: 09:00,11:00,12:00,21:00<br>You need to list all the options', 'date-time-picker-field' ),
						'default' => '',
					),

					array(
						'name'    => 'sunday_times',
						'label'   => __( 'Allowed times for Sunday' ),

						'default' => '',
					),

					array(
						'name'    => 'monday_times',
						'label'   => __( 'Allowed times for Monday' ),

						'default' => '',
					),

					array(
						'name'    => 'tuesday_times',
						'label'   => __( 'Allowed times for Tuesday' ),

						'default' => '',
					),

					array(
						'name'    => 'wednesday_times',
						'label'   => __( 'Allowed times for Wednesday' ),

						'default' => '',
					),
					array(
						'name'    => 'thursday_times',
						'label'   => __( 'Allowed times for Thursday' ),

						'default' => '',
					),
					array(
						'name'    => 'friday_times',
						'label'   => __( 'Allowed times for Friday' ),

						'default' => '',
					),
					array(
						'name'    => 'saturday_times',
						'label'   => __( 'Allowed times for Saturday' ),

						'default' => '',
					),

				),

				'dtpicker'          => array(
					array(
						'name'              => 'selector',
						'label'             => __( 'CSS Selector', 'date-time-picker-field' ),
						'desc'              => __( 'Selector of the input field you want to target and transform into a picker. You can enter multiple selectors separated by commas.', 'date-time-picker-field' ),
						'placeholder'       => __( '.class_name or #field_id', 'date-time-picker-field' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					),
					array(
						'name'    => 'locale',
						'label'   => __( 'Language', 'date-time-picker-field' ),
						'desc'    => __( 'Language to display the month and day labels', 'date-time-picker-field' ),
						'type'    => 'select',
						'default' => 'en',
						'options' => array(
							'ar'    => __( 'Arabic', 'date-time-picker-field' ),
							'az'    => __( 'Azerbaijanian (Azeri)', 'date-time-picker-field' ),
							'bg'    => __( 'Bulgarian', 'date-time-picker-field' ),
							'bs'    => __( 'Bosanski', 'date-time-picker-field' ),
							'ca'    => __( 'Català', 'date-time-picker-field' ),
							'ch'    => __( 'Simplified Chinese', 'date-time-picker-field' ),
							'cs'    => __( 'Čeština', 'date-time-picker-field' ),
							'da'    => __( 'Dansk', 'date-time-picker-field' ),
							'de'    => __( 'German', 'date-time-picker-field' ),
							'el'    => __( 'Ελληνικά', 'date-time-picker-field' ),
							'en'    => __( 'English', 'date-time-picker-field' ),
							'en-GB' => __( 'English (British)', 'date-time-picker-field' ),
							'es'    => __( 'Spanish', 'date-time-picker-field' ),
							'et'    => __( 'Eesti', 'date-time-picker-field' ),
							'eu'    => __( 'Euskara', 'date-time-picker-field' ),
							'fa'    => __( 'Persian', 'date-time-picker-field' ),
							'fi'    => __( 'Finnish (Suomi)', 'date-time-picker-field' ),
							'fr'    => __( 'French', 'date-time-picker-field' ),
							'gl'    => __( 'Galego', 'date-time-picker-field' ),
							'he'    => __( 'Hebrew (עברית)', 'date-time-picker-field' ),
							'hr'    => __( 'Hrvatski', 'date-time-picker-field' ),
							'hu'    => __( 'Hungarian', 'date-time-picker-field' ),
							'id'    => __( 'Indonesian', 'date-time-picker-field' ),
							'it'    => __( 'Italian', 'date-time-picker-field' ),
							'ja'    => __( 'Japanese', 'date-time-picker-field' ),
							'ko'    => __( 'Korean (한국어)', 'date-time-picker-field' ),
							'kr'    => __( 'Korean', 'date-time-picker-field' ),
							'lt'    => __( 'Lithuanian (lietuvių)', 'date-time-picker-field' ),
							'lv'    => __( 'Latvian (Latviešu)', 'date-time-picker-field' ),
							'mk'    => __( 'Macedonian (Македонски)', 'date-time-picker-field' ),
							'mn'    => __( 'Mongolian (Монгол)', 'date-time-picker-field' ),
							'nl'    => __( 'Dutch', 'date-time-picker-field' ),
							'no'    => __( 'Norwegian', 'date-time-picker-field' ),
							'pl'    => __( 'Polish', 'date-time-picker-field' ),
							'pt'    => __( 'Portuguese', 'date-time-picker-field' ),
							'pt-BR' => __( 'Português(Brasil)', 'date-time-picker-field' ),
							'ro'    => __( 'Romanian', 'date-time-picker-field' ),
							'ru'    => __( 'Russian', 'date-time-picker-field' ),
							'se'    => __( 'Swedish', 'date-time-picker-field' ),
							'sk'    => __( 'Slovenčina', 'date-time-picker-field' ),
							'sl'    => __( 'Slovenščina', 'date-time-picker-field' ),
							'sq'    => __( 'Albanian (Shqip)', 'date-time-picker-field' ),
							'sr'    => __( 'Serbian Cyrillic (Српски)', 'date-time-picker-field' ),
							'sr-YU' => __( 'Serbian (Srpski)', 'date-time-picker-field' ),
							'sv'    => __( 'Svenska', 'date-time-picker-field' ),
							'th'    => __( 'Thai', 'date-time-picker-field' ),
							'tr'    => __( 'Turkish', 'date-time-picker-field' ),
							'uk'    => __( 'Ukrainian', 'date-time-picker-field' ),
							'vi'    => __( 'Vietnamese', 'date-time-picker-field' ),
							'zh'    => __( 'Simplified Chinese (简体中文)', 'date-time-picker-field' ),
							'zh-TW' => __( 'Traditional Chinese (繁體中文)', 'date-time-picker-field' ),
						),
					),

					array(
						'name'    => 'theme',
						'label'   => __( 'Theme', 'date-time-picker-field' ),
						'desc'    => __( 'calendar visual style', 'date-time-picker-field' ),
						'type'    => 'select',
						'default' => 'default',
						'options' => array(
							'default' => __( 'Default', 'date-time-picker-field' ),
							'dark'    => __( 'Dark', 'date-time-picker-field' ),
						),
					),

					array(
						'name'    => 'datepicker',
						'label'   => __( 'Display Calendar', 'date-time-picker-field' ),
						'desc'    => __( 'Display date picker', 'date-time-picker-field' ),
						'type'    => 'checkbox',
						'value'   => '1',
						'default' => 'on',
					),

					array(
						'name'    => 'timepicker',
						'label'   => __( 'Display Time', 'date-time-picker-field' ),
						'desc'    => __( 'Display time picker', 'date-time-picker-field' ),
						'type'    => 'checkbox',
						'value'   => '1',
						'default' => 'on',
					),

					array(
						'name'    => 'placeholder',
						'label'   => __( 'Keep Placeholder', 'date-time-picker-field' ),
						'desc'    => __( 'If enabled, original placeholder will be kept. If disabled it will be replaced with current date.', 'date-time-picker-field' ),
						'type'    => 'checkbox',
						'value'   => '1',
						'default' => 'off',
					),

					array(
						'name'    => 'preventkeyboard',
						'label'   => __( 'Prevent Keyboard Edit', 'date-time-picker-field' ),
						'desc'    => __( 'If enabled, it wont be possible to edit the text. This will also prevent the keyboard on mobile devices to display when selecting the date.', 'date-time-picker-field' ),
						'type'    => 'checkbox',
						'value'   => 'on',
						'default' => 'off',
					),

					array(
						'name'    => 'minDate',
						'label'   => __( 'Disable Past Dates', 'date-time-picker-field' ),
						'desc'    => __( 'If enabled, past dates can\'t be selected', 'date-time-picker-field' ),
						'type'    => 'checkbox',
						'value'   => 'on',
						'default' => 'off',
					),

					array(
						'name'              => 'step',
						'label'             => __( 'Time Step', 'date-time-picker-field' ),
						'desc'              => __( 'Time interval in minutes for time picker options', 'date-time-picker-field' ),
						'type'              => 'text',
						'default'           => '60',
						'sanitize_callback' => 'sanitize_text_field',
					),

					array(
						'name'              => 'minTime',
						'label'             => __( 'Minimum Time', 'date-time-picker-field' ),
						'desc'              => __( 'Time options will start from this. Leave empty for none. Use the format you selected for the time. For example: 08:00 AM', 'date-time-picker-field' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					),

					array(
						'name'              => 'maxTime',
						'label'             => __( 'Maximum Time', 'date-time-picker-field' ),
						'desc'              => __( 'Time options will not be later than this specified time. Leave empty for none. Use the format you selected for the time. For example: 08:00 PM', 'date-time-picker-field' ),
						'type'              => 'text',
						'default'           => '',
						'sanitize_callback' => 'sanitize_text_field',
					),

					array(
						'name'    => 'dateformat',
						'label'   => __( 'Date Format', 'date-time-picker-field' ),
						'desc'    => __( 'Date format', 'date-time-picker-field' ),
						'type'    => 'radio',
						'options' => array(
							'YYYY-MM-DD' => __( 'Year-Month-Day', 'date-time-picker-field' ) . ' ' . date( 'Y-m-d' ),
							'YYYY/MM/DD' => __( 'Year/Month/Day', 'date-time-picker-field' ) . ' ' . date( 'Y/m/d' ),
							'DD-MM-YYYY' => __( 'Day-Month-Year', 'date-time-picker-field' ) . ' ' . date( 'd-m-Y' ),
							'DD/MM/YYYY' => __( 'Day/Month/Year', 'date-time-picker-field' ) . ' ' . date( 'd/m/Y' ),
							'MM-DD-YYYY' => __( 'Month-Day-Year', 'date-time-picker-field' ) . ' ' . date( 'm-d-Y' ),
							'MM/DD/YYYY' => __( 'Month/Day/Year', 'date-time-picker-field' ) . ' ' . date( 'm/d/Y' ),
							'DD.MM.YYYY' => __( 'Day.Month.Year', 'date-time-picker-field' ) . ' ' . date( 'd.m.Y' ),
						),
						'default' => 'YYYY-MM-DD',
					),

					array(
						'name'    => 'hourformat',
						'label'   => __( 'Hour Format', 'date-time-picker-field' ),
						'desc'    => __( 'Hour format', 'date-time-picker-field' ),
						'type'    => 'radio',
						'options' => array(
							'HH:mm'   => 'H:M ' . date( 'H:i' ),
							'hh:mm A' => 'H:M AM/PM ' . date( 'h:i A' ),
						),
						'default' => 'hh:mm A',
					),
					array(
						'name'    => 'load',
						'label'   => __( 'When to Load', 'date-time-picker-field' ),
						'desc'    => __( 'Choose to search for the selector across the website or only when the shortcode [datetimepicker] exists on a page.<br> Use the shortcode to prevent the script from loading across all pages', 'date-time-picker-field' ),
						'type'    => 'radio',
						'options' => array(
							'full'      => __( 'Across the full website', 'date-time-picker-field' ),
							'admin'     => __( 'Admin panel only', 'date-time-picker-field' ),
							'fulladmin' => __( 'Full website including admin panel', 'date-time-picker-field' ),
							'shortcode' => __( 'Only when shortcode [datetimepicker] exists on a page.', 'date-time-picker-field' ),
						),
						'default' => 'full',
					),
				),
			);

			return $settings_fields;
		}

		public function plugin_page() {
			echo '<div class="wrap">';

			echo '<h2>' . __( 'Date & Time Picker Settings', 'dtp' ) . '</h2>';

			$this->settings_api->show_navigation();
			$this->settings_api->show_forms();

			echo '</div>';
		}

		/**
		 * Get all the pages
		 *
		 * @return array page names with key value pairs
		 */
		public function get_pages() {
			$pages         = get_pages();
			$pages_options = array();
			if ( $pages ) {
				foreach ( $pages as $page ) {
					$pages_options[ $page->ID ] = $page->post_title;
				}
			}

			return $pages_options;
		}

	}
endif;
