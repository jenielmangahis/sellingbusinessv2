<?php

/**
 * CM Ad Changer
 *
 * @author CreativeMinds (http://ad-changer.cminds.com)
 * @copyright Copyright (c) 2013, CreativeMinds
 */
if ( !isset( $table_prefix ) ) {
	$table_prefix = '';
}

define( 'CMAC_DEBUG', '0' );
define( 'AC_UPLOAD_PATH', 'ac_uploads/' );
define( 'AC_TMP_UPLOAD_PATH', 'tmp/' );
define( 'CAMPAIGNS_TABLE', $table_prefix . 'cm_campaigns' ); // $table_prifix comes from Wordpress core
define( 'GROUPS_TABLE', $table_prefix . 'cm_campaign_groups' );
define( 'CATEGORIES_TABLE', $table_prefix . 'cm_campaign_categories' );
define( 'CAMPAIGN_CATEGORIES_REL_TABLE', $table_prefix . 'cm_campaign_categories_rel' );
define( 'ADS_TABLE', $table_prefix . 'cm_campaign_images' );
define( 'PERIODS_TABLE', $table_prefix . 'cm_campaign_periods' );
define( 'HISTORY_TABLE', $table_prefix . 'cm_campaign_history' );
define( 'MANAGERS_TABLE', $table_prefix . 'cm_campaign_managers' );

define( 'CAMPAIGNS_LIMIT', 100 );
define( 'BANNER_THUMB_WIDTH', 200 );
define( 'BANNER_THUMB_HEIGHT', 200 );
define( 'BANNER_VARIATION_THUMB_WIDTH', 50 );
define( 'BANNER_VARIATION_THUMB_HEIGHT', 50 );
define( 'BANNERS_PER_CAMPAIGN_LIMIT', '500' );
define( 'BANNER_VARIATIONS_LIMIT', '10' );
define( 'AC_HISTORY_PER_PAGE_LIMIT', '50' );
define( 'AC_ADVERTISERS_TAXONOMY', 'cmac_advertisers' );

define( 'AC_API_ERROR_1', 'Client host unknown' );
define( 'AC_API_ERROR_2', 'Campaign ID not set' );
define( 'AC_API_ERROR_3', 'Campaign not found' );
define( 'AC_API_ERROR_4', 'Client host is not registered' );
define( 'AC_API_ERROR_5', 'Campaign is inactive' );
define( 'AC_API_ERROR_6', 'There is no image to display' );
define( 'AC_API_ERROR_7', 'Unknown error' );
define( 'AC_API_ERROR_8', 'Unknown action' );
define( 'AC_API_ERROR_9', 'Server is inactive' );
define( 'AC_API_ERROR_10', 'Maximum impressions achieved' );
define( 'AC_API_ERROR_11', 'Maximum clicks achieved' );
define( 'AC_API_ERROR_12', 'Banner ID is not set' );
define( 'AC_API_ERROR_13', 'Campaign is not active today' );
define( 'AC_API_ERROR_14', 'Wrong container width' );

$label_descriptions = array( // SETTINGS
	'acs_active'						 => 'Server status, if set than server will accept connections from CM Ad Changer - Pro Clients',
	'acs_notification_email_tpl'		 => 'Email notification is sent when campaign stops working. Email notification can include the following fields: %campaign_name%, %campaign_id%, %reason%. ',
	'acs_inject_scripts'				 => 'Injecting scripts into all pages is needed in rare cases when campaign function or shortcode is called from an external plugin. This means that every page will enqueue the CSS and JS code of CM Ad Changer.',
	'acs_auto_deactivate_campaigns'		 => 'If you check this option the campaings which have had the activity dates set, will be automatically deactivated after the last period has passed.',
	'acs_script_in_footer'				 => 'Inject scripts in footer. Warning: theme must use wp_footer() function for this to work!',
	'acs_div_wrapper'					 => 'Div Wrapper (server side) - Will add div around banner on server side',
	'acs_class_name'					 => 'Class Name - Will set the class name for div',
	'acs_custom_css'					 => 'Custom CSS will be injected into body before banner is shown and only on post or pages where campaign is active. Example: #featured.has-badge {margin-bottom: 85px;}',
	'acs_geolocation_api_key'			 => 'Geolocation API Key. To receive API register at http://ipinfodb.com/register.php',
	'acs_slideshow_effect'				 => 'Rotating Banner effect',
	'acs_slideshow_interval'			 => 'Amount of time before one banner replaces the other (milliseconds)',
	'acs_slideshow_transition_time'		 => 'The amount of time each transition takes (milliseconds)',
	'acs_use_banner_variations'			 => 'If set, banner variations will be used when screen or container size is smaller than served banner.  ',
	'acs_banner_area'					 => 'Define based on what the variation size will be defined. Container means the size of the containing element while screen is the detected screen/device size.',
	'acs_resize_banner'					 => 'In case no banner variations exist allow to resize the existing banner to fit the screen/container size accordingly.',
	// CAMPAIGNS
	'title'								 => 'Campaign Name. For internal use only',
	'campaign_id'						 => 'Campaign ID. When referring to a campaign in the shortcode, only use Campaign ID',
	'comment'							 => 'Campaign Notes - This is for internal use only',
	'adsense_client'					 => 'Code of the AdSense client ID',
	'adsense_slot'						 => 'Code of the AdSense advertisement slot ID',
	'link'								 => 'Campaign Target URL - Target URL for all banners in the campaign. Target URL specified in banner will override this. WARNING: Clicks are counted only if it is set!',
	'status'							 => 'Campaign Status - if set campaign will be active ',
	'banner_url_in_new_window'			 => 'Should clicking on banner open new window ',
	'max_impressions'					 => 'Leave it 0 to remove limit or set to max number allowed',
	'max_clicks'						 => 'Leave it 0 to remove limit or set to max number allowed',
	'banner_display_method'				 => 'Display Banner Method (Selected - Will only serve selected banner or Random will serve random banner based on banner weight)',
	'categories'						 => 'Approved domains - List of URLs of approved clients. If not specified all clients will be served.',
	'advertiser'						 => 'Advertiser Name',
	'active_dates'						 => 'Activity Dates - List of dates when campaign is active. If not set than campaign is active on all dates.',
	'active_week_days'					 => 'Activity Days -List of days in the week when campaign is active. If not set than campaign is active on all days.',
	'campaign_images'					 => 'Campaign Images - All banners for this campaign. Variations are different size options for each banner. They are selected when client is set to work in responsive mode.',
	'banner_title'						 => 'Banner Name',
	'banner_title_tag'					 => 'Banner Title - Will appear in banner img title',
	'banner_alt_tag'					 => 'Banner Alt - Will appear in banner img alt',
	'banner_link'						 => 'Banner Target URL - Will override campaign target url if specified',
	'banner_weight'						 => 'Banner Weight - Will define what is the relative amount of impressions for this banner in compare to other banners in the campaign',
	'email_notifications'				 => 'Email of campaign manager. Notifications will be send to this email',
	'send_notifications'				 => 'Send Notifications when campaign stops to the email set for the campaign manager',
	'campaign_type_id'					 => 'Pick the type of the advertisements in the current campaign from the list currently supported types.',
	'campaign_html_ads'					 => 'Show the custom HTML code wherever you want.',
	'cloud_url'							 => 'Cloud Storage URL is where the campaign banners are stored. Make sure to specify the correct url of your cloud storage bucket. All Campaign images will be served from this location if Use Cloud Storage is set. All local campaign images are stored under WordPress upload directory in a sub-directory called ac_uploads. Make sure to upload them to cloud storage',
	'custom_js'							 => 'Custom JS will be injected into body before banner is shown and only on post or pages where campaign is active and if client explicitly allows to inject JS. Example: alert(&quot;test&quot;);',
	'group_order'						 => 'Select how the Campaigns of the groups should be ordered.',
	'group_id'							 => 'Select the group of the Campaign.',
	'group_priority'					 => 'Choose the priority of the Campaign within the group. Campaigns with high weight will have the precedense.',
	'group_campaigns'					 => 'The list of the Campaigns within the Group.',
	'campaign_video_ads'				 => 'Please click `Add video banner` button and paste the code with embedded iframe (eg. YouTube -> share on page).',
	'campaign_width'					 => 'Set this up to limit the width of the div containing the HTML ads. You may specify both number and the unit e.g. 100px 50% 30em (no unit defaults to &quot;px&quot;)',
	'campaign_height'					 => 'Set this up to limit the height of the div containing the HTML ads. You may specify both number and the unit e.g. 100px 50% 30em (no unit defaults to &quot;px&quot;)',
	'campaign_addesigner'				 => 'Use the &quot;Show AdDesigner&quot; button to open the CM AdDesigner window which contains the editor that can help you design your HTML ads in a WYSIWYG way.',
	'see_full'							 => 'See full size',
	'background'						 => 'Background of floating banner html content',
	'seconds_to_show'					 => 'Time between page load and appearance of the banner (in seconds). Empty or 0 means instantly.',
	'show_effect'						 => 'Visual Effect when banner is shown. Empty means no effect.',
	'banner_edges'						 => 'Please choose if banner should have rounded or sharp edges.',
	'show_effect'						 => 'Effect used when banner if shown to users.',
	'user_show_method'					 => 'Please choose whether banner should be displayed every time or only the first time page is loaded.',
	'reset_floating_banner_cookie_time'	 => 'After how many days after first impression banner should appear again. 7 days by default.',
	'underlay_type'						 => 'Shadow color of the underlay background `under` the banner.',
	'banner_name_for_statistics'		 => 'Name of the banner viewed in access log data table.',
	'acs_disable_history_table'			 => 'Select this option to turn off AdChanger functionality of tracking banner clicks and impressions. If this option is selected functionalities: statistics, banner max impressions and banner max clicks will not work. Selecting this option may speed up the site since AdChanger will not be using the history table.',
	'banner_custom_js'					 => 'Select this option to add custom JavaScript code or includes. This code will be added before banner html code. As those scripts are loaded asynchronously, scripts based on document.write functions will not work due to \'Failed to execute \'write\' on \'Document\' web browsers security precaution.',
	'custom_banner_new_window'			 => 'Choose custom banner link behavior.',
	'banner_expiration_date'			 => 'Choose banner expiration date (after this date the banner will not be displayed)',
);
