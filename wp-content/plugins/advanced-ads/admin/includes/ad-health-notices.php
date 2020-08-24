<?php
/**
 * Array with ad health messages
 *
 * attribute: type
 * - "notice" (default, recommendation, etc.)
 * - "problem" (critical)
 *
 * attribute: can_hide
 * (user can hide this warning, default: true)
 *
 * attribute: hide
 * (how to handle click on "hide" button)
 * - true (default, hide the item)
 * - false (remove the item completely from list of notifications)
 */
$advanced_ads_ad_health_notices = apply_filters( 'advanced-ads-ad-health-notices', array(
		// old PHP version
		// checked using Advanced_Ads_Checks::php_version_minimum().
		'old_php'                                       => array(
			'text' => sprintf(
			// translators: %1$s is a version number.
				__( 'Your <strong>PHP version (%1$s) is too low</strong>. Advanced Ads is built for PHP %2$s and higher. It might work, but updating PHP is highly recommended. Please ask your hosting provider for more information.', 'advanced-ads' ),
				phpversion(),
				Advanced_Ads_Checks::MINIMUM_PHP_VERSION
			),
			'type' => 'problem',
		),
		// cache enabled, but not Advanced Ads Pro
		// checked using Advanced_Ads_Checks::cache() && ! defined( 'AAP_VERSION' ).
		'cache_no_pro'                                  => array(
			'text' => sprintf(
			// translators: %s is a target URL.
				__( 'Your <strong>website uses cache</strong>. Some dynamic features like ad rotation or visitor conditions might not work properly. Use the cache-busting feature of <a href="%s" target="_blank">Advanced Ads Pro</a> to load ads dynamically.', 'advanced-ads' ),
				ADVADS_URL . 'add-ons/advanced-ads-pro/#utm_source=advanced-ads&utm_medium=link&utm_campaign=error-cache'
			),
			'type' => 'problem',
		),
		// updates for Advanced Ads plugins are available
		// checked using Advanced_Ads_Checks::plugin_updates_available().
		'plugin_updates_available'                      => array(
			'text' => __( 'There are <strong>plugin updates available</strong>. Please update.', 'advanced-ads' ),
			'type' => 'problem',
			'hide' => false,
		),
		// Autoptimize found, but no Advanced Ads Pro
		// Advanced_Ads_Checks::active_autoptimize() && ! defined( 'AAP_VERSION' ) ).
		'autoptimize_no_pro'                            => array(
			'text' => sprintf(
			// translators: %s is a target URL.
				__( '<strong>Autoptimize plugin detected</strong>. While this plugin is great for site performance, it is known to alter code, including scripts from ad networks. <a href="%s" target="_blank">Advanced Ads Pro</a> has a build-in support for Autoptimize.', 'advanced-ads' ),
				ADVADS_URL . 'add-ons/advanced-ads-pro/#utm_source=advanced-ads&utm_medium=link&utm_campaign=error-autoptimize'
			),
			'type' => 'problem',
		),
		// conflicting plugins found
		// Advanced_Ads_Checks::conflicting_plugins().
		'conflicting_plugins'                           => array(
			'text' => sprintf(
			// translators: %1$s is a list of plugin names; %2$s a target URL.
				__( 'Plugins that are known to cause (partial) problems: <strong>%1$s</strong>. <a href="%2$s" target="_blank">Learn more</a>.', 'advanced-ads' ),
				implode( ', ', Advanced_Ads_Checks::conflicting_plugins() ),
				ADVADS_URL . 'manual/known-plugin-conflicts/#utm_source=advanced-ads&utm_medium=link&utm_campaign=error-plugin-conflicts'
			),
			'type' => 'problem',
		),
		// PHP extensions missing
		// Advanced_Ads_Checks::php_extensions().
		'php_extensions_missing'                        => array(
			'text' => sprintf(
			// translators: %s is a list of PHP extensions.
				__( 'Missing PHP extensions could cause issues. Please ask your hosting provider to enable them: %s', 'advanced-ads' ),
				implode( ", ", Advanced_Ads_Checks::php_extensions() )
			),
			'type' => 'problem',
		),
		// ads are disabled
		// Advanced_Ads_Checks::ads_disabled().
		'ads_disabled'                                  => array(
			'text' => sprintf(
			// translators: %s is a target URL.
				__( 'Ads are disabled for all or some pages. See "disabled ads" in <a href="%s">settings</a>.', 'advanced-ads' ),
				admin_url( 'admin.php?page=advanced-ads-settings#top#general' )
			),
			'type' => 'problem',
		),
		// user is on wordpress.com
		// defined( 'IS_WPCOM' ).
		'wp_com'                                        => array(
			'text' => __( 'This site is hosted on wordpress.com.', 'advanced-ads' ),
			'type' => 'problem',
		),
		// check if Advanced Ads related constants are enabled
		// Advanced_Ads_Checks::get_defined_constants().
		'constants_enabled'                             => array(
			'text' => '<a href="' . admin_url( 'admin.php?page=advanced-ads-settings#top#support' ) . '">' . esc_html__( 'Advanced Ads related constants enabled', 'advanced-ads' ) . '</a>',
			'type' => 'notice',
		),
		// possible JavaScript conflicts
		// Advanced_Ads_Checks::jquery_ui_conflict().
		'jquery_ui_conflict'                            => array(
			'text' => sprintf(
			// translators: %s is a target URL.
				__( 'Possible conflict between jQueryUI library, used by Advanced Ads and other libraries (probably <a href="%s">Twitter Bootstrap</a>). This might lead to misfortunate formats in forms, but should not damage features.', 'advanced-ads' ),
				'http://getbootstrap.com/javascript/#js-noconflict'
			),
			'type' => 'problem',
		),
		// adblocker assets expired
		// Advanced_Ads_Checks::assets_expired().
		'assets_expired'                                => array(
			'text' => sprintf(
			// translators: %s is a target URL.
				__( 'Some assets were changed. Please <strong>rebuild the asset folder</strong> in the <a href="%s">Advanced Ads settings</a> to update the ad blocker disguise.', 'advanced-ads' ),
				admin_url( 'admin.php?page=advanced-ads-settings' )
			),
			'type' => 'problem',
			'hide' => true,
		),
		// missing license codes
		// Advanced_Ads_Checks::license_invalid().
		'license_invalid'                               => array(
			'text'     => __( 'One or more license keys for <strong>Advanced Ads add-ons are invalid or missing</strong>.', 'advanced-ads' ) . ' '
			              . sprintf(
			              // translators: %s is a target URL.
				              __( 'Please add valid license keys <a href="%s">here</a>.', 'advanced-ads' ),
				              get_admin_url( 1, 'admin.php?page=advanced-ads-settings#top#licenses' )
			              ),
			'type'     => 'problem',
			'can_hide' => false,
		),
		// an individual ad expired.
		'ad_expired'                                    => array(
			'text' => __( 'Ad expired', 'advanced-ads' ) . ': ',
			'type' => 'notice',
			'hide' => false,
		),
		// an individual ad expired
		// is checked in the frontend by Ad Health in Advanced_Ads_Frontend_Checks::can_use_head_placement().
		'ad_with_output_in_head'                        => array(
			// we keep the %s here and replace it with an empty string, because we use it somewhere else and don’t want to create a new string that is basically the same.
			'text' => sprintf(
			// translators: %s is empty here, but the string will be followed by a name of an ad unit.
				__( 'Visible ads should not use the Header placement: %s', 'advanced-ads' ),
				''
			),
			'type' => 'notice',
			'hide' => false,
			'get_help_link' => ADVADS_URL . 'manual/ad-health/?utm_source=advanced-ads&utm_medium=link&utm_campaign=error-visible-ad-in-header#header-ads',
		),
		// ad AdSense ad was hidden in the frontend using CSS
		// check in Ad Health in frontend.
		'adsense_hidden'                                => array(
			// we keep the %s here and replace it with an empty string, because we use it somewhere else and don’t want to create a new string that is basically the same.
			'text' => sprintf(
				'%s: %s.',
				__( 'AdSense violation', 'advanced-ads' ),
				__( 'Ad is hidden', 'advanced-ads' )
			),
			'type' => 'problem',
			'hide' => false,
			'get_help_link' => ADVADS_URL . 'adsense-errors/?utm_source=advanced-ads&utm_medium=link&utm_campaign=error-adsense-hidden#AdSense_hidden',
		),
		// Ad has HTTP, but site uses HTTPS
		// check in Ad Health in frontend.
		'ad_has_http'                                   => array(
			'text' => __( 'Your website is using HTTPS, but the ad code contains HTTP and might not work.', 'advanced-ads' ),
			'type' => 'notice',
			'hide' => false,
			'get_help_link' => ADVADS_URL . 'manual/ad-health/?utm_source=advanced-ads&utm_medium=link&utm_campaign=error-https-ads#https-ads',
		),
		// dummy text for general AdSense issue.
		'adsense_issue'                                 => array(
			// we keep the %s here and replace it with an empty string, because we use it somewhere else and don’t want to create a new string that is basically the same.
			'text' => __( 'AdSense issue' ),
			'type' => 'problem',
		),
		// no ads created, yet.
		'no_ads'                                        => array(
			// we keep the %s here and replace it with an empty string, because we use it somewhere else and don’t want to create a new string that is basically the same.
			'text' => '<a href="' . admin_url( 'post-new.php?post_type=' . Advanced_Ads::POST_TYPE_SLUG ) . '">' . __( 'Create your first ad', 'advanced-ads' ) . '</a>.',
			'type' => 'notice',
		),
		// AdSense connection error: disapproved account.
		'adsense_connect_disapprovedAccount'            => array(
			'text' => __( 'Last AdSense account connection attempt failed.', 'advanced-ads' ) . '&nbsp;' . __( 'Your account was not approved by AdSense.', 'advance-ads' ) . ' ' . Advanced_Ads_Ad_Health_Notices::get_adsense_error_link( 'disapprovedAccount' ),
			'type' => 'problem',
			'hide' => false,
		),
		// AdSense connection error: no adsense account.
		'adsense_connect_noAdSenseAccount'              => array(
			'text' => sprintf(
				__( 'Last AdSense account connection attempt failed.', 'advanced-ads' ) . '&nbsp;' . __( 'Create a new AdSense account %1$shere%2$s.', 'advance-ads' ) . ' ' . Advanced_Ads_Ad_Health_Notices::get_adsense_error_link( 'noAdSenseAccount' ),
				'<a href="https://www.google.com/adsense/start/?utm_source=AdvancedAdsPlugIn&utm_medium=partnerships&utm_campaign=AdvancedAdsPartner" target="_blank">',
				'</a>'
			),
			'type' => 'problem',
			'hide' => false,
		),
		// AdSense account alert.
		'adsense_alert_ALERT_TYPE_ADS_TXT_UNAUTHORIZED' => array(
			'text' => __( 'One of your sites is missing the AdSense publisher ID in the ads.txt file.', 'advanced-ads' ) . ' ' . Advanced_Ads_Ad_Health_Notices::get_adsense_error_link( 'ALERT_TYPE_ADS_TXT_UNAUTHORIZED' ),
			'type' => 'problem',
		),
	)
);
