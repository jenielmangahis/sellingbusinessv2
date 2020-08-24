<?php
/**
 * CM Ad Changer
 *
 * @author CreativeMinds (http://ad-changer.cminds.com)
 * @copyright Copyright (c) 2013, CreativeMinds
 */
if ( !defined( 'KB' ) ) {
	define( 'KB', 1024 );
}
if ( !defined( 'MB' ) ) {
	define( 'MB', 1048576 );
}
if ( !defined( 'GB' ) ) {
	define( 'GB', 1073741824 );
}
if ( !defined( 'TB' ) ) {
	define( 'TB', 1099511627776 );
}
if ( !defined( 'HOURINSECONDS' ) ) {
	define( 'HOURINSECONDS', 3600 );
}

/**
 * Plugin activation
 *
 */
function ac_activate( $networkwide ) {
	global $wpdb;

	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
		/*
		 * Check if it is a network activation - if so, run the activation function for each blog id
		 */
		if ( $networkwide ) {
			/*
			 * Get all blog ids
			 */
			$blogids = $wpdb->get_col( $wpdb->prepare( "SELECT blog_id FROM {$wpdb->blogs}" ) );
			foreach ( $blogids as $blog_id ) {
				switch_to_blog( $blog_id );
				cmacs__install();
			}
			restore_current_blog();
			return;
		}
	}

	cmacs__install();
}

function cmacs__install() {
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	global $table_prefix; // have to use $table_prefix

	dbDelta( 'CREATE TABLE ' . $table_prefix . CAMPAIGNS_TABLE . ' (
			  campaign_id int(11) NOT NULL AUTO_INCREMENT,
			  title varchar(100) NOT NULL,
			  link varchar(200) NOT NULL,
			  group_id int(11) NOT NULL DEFAULT "0",
			  group_priority int(11) NOT NULL DEFAULT "0",
			  selected_banner int(11) NOT NULL DEFAULT "0",
			  banner_display_method enum("random","selected","all") NOT NULL,
			  max_clicks bigint(20) NOT NULL,
			  max_impressions bigint(20) NOT NULL,
			  comment text NOT NULL,
			  custom_js text NOT NULL,
			  adsense_client text NOT NULL,
			  adsense_slot text NOT NULL,
			  active_week_days varchar(30) NOT NULL,
			  status tinyint(4) NOT NULL,
                          banner_new_window tinyint(4) NOT NULL,
			  campaign_type_id tinyint(4) NOT NULL,
			  send_notifications tinyint(4) NOT NULL,
			  cloud_url varchar(150) NOT NULL DEFAULT "",
			  use_cloud tinyint(4) NOT NULL DEFAULT 0,
                          meta text NULL DEFAULT "",
                          clicks_count int(11) NULL DEFAULT 0,
                          impressions_count int(11) NULL DEFAULT 0,
			  PRIMARY KEY  (campaign_id)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;' );

	dbDelta( 'CREATE TABLE ' . $table_prefix . CATEGORIES_TABLE . ' (
				  category_id int(11) NOT NULL AUTO_INCREMENT,
				  category_title varchar(150) NOT NULL,
				  PRIMARY KEY  (category_id)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;' );

	dbDelta( 'CREATE TABLE ' . $table_prefix . CAMPAIGN_CATEGORIES_REL_TABLE . ' (
				  campaign_cat_rel_id int(11) NOT NULL AUTO_INCREMENT,
				  campaign_id int(11) NOT NULL,
				  category_id int(11) NOT NULL,
				  PRIMARY KEY  (campaign_cat_rel_id)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;' );

	dbDelta( 'CREATE TABLE ' . $table_prefix . GROUPS_TABLE . ' (
			  group_id int(11) NOT NULL AUTO_INCREMENT,
			  description varchar(100) NOT NULL,
			  group_order tinyint(4) NOT NULL,
			  created_on timestamp NOT NULL default CURRENT_TIMESTAMP,
			  PRIMARY KEY  (group_id)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;' );

	dbDelta( 'CREATE TABLE ' . $table_prefix . HISTORY_TABLE . ' (
			  event_id bigint(20) NOT NULL AUTO_INCREMENT,
			  event_type enum("click","impression") NOT NULL,
			  group_id int(11) DEFAULT NULL,
			  campaign_id int(11) DEFAULT NULL,
			  banner_id int(11) DEFAULT NULL,
			  referer_url varchar(150) NOT NULL,
			  remote_ip varchar(20) NOT NULL,
			  webpage_url varchar(200) NOT NULL,
			  remote_country varchar(20) NOT NULL,
			  remote_city varchar(30) NOT NULL DEFAULT "",
			  regdate timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			  campaign_type varchar(20) NOT NULL,
			  PRIMARY KEY  (event_id)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;' );

	dbDelta( 'CREATE TABLE ' . $table_prefix . ADS_TABLE . ' (
			  image_id int(11) NOT NULL AUTO_INCREMENT,
			  type int(4) NOT NULL DEFAULT  "0",
			  status int(1) NOT NULL DEFAULT "1",
			  campaign_id int(11) NOT NULL,
			  parent_image_id int(11) NULL DEFAULT "0",
			  title varchar(50) NULL DEFAULT "",
			  title_tag varchar(200) NULL DEFAULT "",
			  alt_tag varchar(200) NULL DEFAULT "",
			  link varchar(150) NULL DEFAULT "",
			  weight tinyint(4) NULL DEFAULT 0,
			  filename varchar(50) NULL DEFAULT "",
			  meta text NULL DEFAULT "",
                          clicks_count int(11) NULL DEFAULT 0,
                          impressions_count int(11) NULL DEFAULT 0,
                          banner_custom_js text NULL DEFAULT "",
                          custom_banner_new_window varchar(50) NULL DEFAULT "",
			  PRIMARY KEY  (image_id)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;' );

	dbDelta( 'CREATE TABLE ' . $table_prefix . PERIODS_TABLE . ' (
				  period_id int(11) NOT NULL AUTO_INCREMENT,
				  campaign_id int(11) NOT NULL,
				  date_from datetime NOT NULL,
				  date_till datetime NOT NULL,
				  PRIMARY KEY  (period_id)
				) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;' );

	dbDelta( 'CREATE TABLE IF NOT EXISTS ' . $table_prefix . MANAGERS_TABLE . ' (
			  manager_id int(11) NOT NULL AUTO_INCREMENT,
			  campaign_id int(11) NOT NULL,
			  manager_name varchar(100) NOT NULL DEFAULT "",
			  manager_email varchar(100) NOT NULL,
			  PRIMARY KEY  (manager_id)
			) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;' );

	/**
	 * Prepare the uploads folder WP way
	 * However this may fail if there's a general problem with uploads
	 * @author Marcin
	 */
	$uploadDir	 = wp_upload_dir();
	$baseDir	 = $uploadDir[ 'basedir' ] . '/' . AC_UPLOAD_PATH;
	$tmpDir		 = $baseDir . AC_TMP_UPLOAD_PATH;
	if ( !is_dir( $tmpDir ) ) {
		if ( !wp_mkdir_p( $tmpDir ) ) {
			echo 'Error: Your WP uploads folder is not writable! The plugin requires a writable uploads folder in order to work.';
			exit;
		}
	}

	update_option( 'cm-ad-changer-server-active', '1' );
}

/**
 * Returns the upload dir
 * @return string
 */
function cmac_get_upload_dir() {
	static $cmacUpladDir = null;

	if ( !$cmacUpladDir ) {
		$uploadDir		 = wp_upload_dir();
		$cmacUploadDir	 = $uploadDir[ 'basedir' ] . '/' . AC_UPLOAD_PATH;
	}
	if ( !is_dir( $cmacUploadDir ) ) {
		mkdir( $cmacUploadDir );
	}
	return $cmacUploadDir;
}

/**
 * Returns the upload url
 * @return string
 */
function cmac_get_upload_url() {
	static $cmacUpladDir = null;

	if ( !$cmacUpladDir ) {
		$uploadDir		 = wp_upload_dir();
		$cmacUploadDir	 = set_url_scheme( $uploadDir[ 'baseurl' ] ) . '/' . AC_UPLOAD_PATH;
	}
	return $cmacUploadDir;
}

/**
 * Plugin deactivation
 */
function ac_deactivate( $networkwide ) {
	global $wpdb;

	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
		/*
		 * Check if it is a network activation - if so, run the activation function for each blog id
		 */
		if ( $networkwide ) {
			/*
			 * Get all blog ids
			 */
			$blogids = $wpdb->get_col( $wpdb->prepare( "SELECT blog_id FROM {$wpdb->blogs}" ) );
			foreach ( $blogids as $blog_id ) {
				switch_to_blog( $blog_id );
				update_option( 'cm-ad-changer-server-active', '0' );
			}
			restore_current_blog();
			return;
		}
	}

	update_option( 'cm-ad-changer-server-active', '0' );
}

/**
 * Top menu rendering
 */
function ac_top_menu() {
	global $submenu;
	$current_slug = $_GET[ 'page' ];
	?>
	<style type="text/css">
		.subsubsub li+li:before {content:'| ';}
	</style>
	<ul class="subsubsub">
		<?php foreach ( $submenu[ 'ac_server' ] as $menu ): ?>
			<?php
			$isExternalPage	 = strpos( $menu[ 2 ], 'http' ) !== FALSE || strpos( $menu[ 2 ], '.php' ) !== FALSE;
			$targetUrl		 = $isExternalPage ? $menu[ 2 ] : 'admin.php?page=' . $menu[ 2 ];
			$newTab			 = $isExternalPage ? 'target="_blank"' : '';
			?>
			<li><a href="<?php echo $targetUrl ?>" <?php echo ($_GET[ 'page' ] == $targetUrl) ? 'class="current"' : ''; ?> <?php echo $newTab; ?>><?php echo $menu[ 0 ]; ?></a></li>
		<?php endforeach; ?>
	</ul>
	<?php
}

add_action( "in_plugin_update_message-" . basename( __FILE__ ) . "/" . basename( dirname( __FILE__ ) ), 'red_warnOnUpgrade' );

function red_warnOnUpgrade() {
	?>
	<div style="margin-top: 1em"><span style="color: red; font-size: larger">STOP!</span> Do <em>not</em> click &quot;update automatically&quot; as you will be <em>downgraded</em> to the free version of Tooltip Glossary. Instead, download the Pro update directly from <a href="http://www.cminds.com/downloads/cm-enhanced-tooltip-glossary-premium-version/">http://www.cminds.com/downloads/cm-enhanced-tooltip-glossary-premium-version/</a>.</div>
	<div style="font-size: smaller">Tooltip Glossary Pro does not use WordPress's standard update mechanism. We apologize for the inconvenience!</div>
	<?php
}

/* * ******************** */
/* AJAX */
/* * ******************** */

add_action( 'wp_ajax_ac_upload_image', 'ac_upload_image' );

/**
 * Uploading the images to tmp folder
 */
function ac_upload_image() {
	$maxSize = 2 * MB;
	if ( !isset( $_GET[ 'pic_type' ] ) ) // pic type is required to set the thumb dimensions
		die( 'Error: pic type not set' );

	$uploadedfile		 = $_FILES[ 'file' ];
	$upload_overrides	 = array( 'test_form' => false );

	$validate = wp_check_filetype_and_ext( $uploadedfile[ 'tmp_name' ], $uploadedfile[ 'name' ], array( 'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'gif' => 'image/gif' ) );
	if ( !$validate[ 'ext' ] ) {
		die( __( 'Error: Invalid file extension!' ) );
	}
	if ( preg_match( "/gif/", $uploadedfile[ 'type' ] ) ) {
		$maxSize = 5 * MB;
	}
	if ( (int) $uploadedfile[ 'size' ] > $maxSize ) {
		die( __( 'Error: File too big!' ) );
	}

	$uploadDir	 = wp_upload_dir();
	$baseDir	 = $uploadDir[ 'basedir' ] . '/' . AC_UPLOAD_PATH;
	$tmpDir		 = $baseDir . AC_TMP_UPLOAD_PATH;

	if ( ($handle = opendir( $baseDir )) !== FALSE ) {
		$existing_files	 = array();
		while ( false !== ($entry			 = readdir( $handle )) ) {
			$existing_files[] = $entry;
		}

		do {
			$new_filename = rand( 1000000, 999999999 ) . '.' . $validate[ 'ext' ];
		} while ( in_array( $new_filename, $existing_files ) );

		move_uploaded_file( $uploadedfile[ 'tmp_name' ], $tmpDir . $new_filename );


		$info	 = pathinfo( $new_filename );
		$image	 = new Image( $tmpDir . $new_filename );

		$ret_array = array( 'info' => array( 'width' => $image->info[ 'width' ], 'height' => $image->info[ 'height' ] ) );

		// creating resized thumbs
		switch ( $_GET[ 'pic_type' ] ) {
			case 'banner':
				$thumb_filename	 = $info[ 'filename' ] . BANNER_THUMB_WIDTH . 'x' . BANNER_THUMB_HEIGHT . '.' . $info[ 'extension' ];
				$thumb_image	 = $tmpDir . $thumb_filename;
				$image->resize( BANNER_THUMB_WIDTH );
				$image->save( $thumb_image );
				break;
			case 'banner_variation':
				$thumb_filename	 = $info[ 'filename' ] . BANNER_VARIATION_THUMB_WIDTH . 'x' . BANNER_VARIATION_THUMB_HEIGHT . '.' . $info[ 'extension' ];
				$thumb_image	 = $tmpDir . $thumb_filename;
				$image->resize( BANNER_VARIATION_THUMB_WIDTH );
				$image->save( $thumb_image );
				break;
			default:
				die( 'Error: pic type unknown' );
		}

		$ret_array[ 'image_filename' ] = $new_filename;

		if ( isset( $thumb_filename ) )
			$ret_array[ 'thumb_filename' ]	 = $thumb_filename;
		else
			$ret_array[ 'thumb_filename' ]	 = $new_filename;

		echo json_encode( $ret_array );
	}
	else {
		die( __( 'Error: Could not open the uploads folder! Please ensure the WP uploads folder is present and writable!' ) );
	}
	exit;
}

add_action( 'wp_ajax_ac_add_advertiser', 'acs_add_advertiser' );

/**
 * Adding advertiser
 */
function acs_add_advertiser() {
	$res = AC_Data::handle_advertiser_post( $_POST );

	if ( isset( $res[ 'success' ] ) ) {
		$ret_res = array( 'success' => 'Advertiser added', 'advertiser_id' => $res[ 'advertiser_id' ] );
	} else {
		$ret_res = $res;
	}

	echo json_encode( $ret_res );
	exit;
}

add_action( 'wp_ajax_ac_edit_advertiser', 'acs_edit_advertiser' );

/**
 * Updating advertiser
 */
function acs_edit_advertiser() {
	$res = AC_Data::handle_advertiser_post( $_POST );

	if ( isset( $res[ 'success' ] ) )
		$ret_res = array( 'success' => 'Advertiser updated' );
	else
		$ret_res = $res;

	echo json_encode( $ret_res );
	exit;
}

add_action( 'wp_ajax_ac_delete_advertiser', 'acs_delete_advertiser' );

/**
 * Updating advertiser
 */
function acs_delete_advertiser() {

	$res = AC_Data::delete_advertiser( $_POST[ 'advertiser_id' ] );

	if ( isset( $res[ 'success' ] ) )
		$ret_res = array( 'success' => 'Advertiser deleted' );
	else
		$ret_res = $res;

	echo json_encode( $ret_res );
	exit;
}

add_action( 'wp_ajax_acs_get_month_report', 'acs_get_month_report' );

/**
 * Loaging page with month report
 */
function acs_get_month_report() {
	ac_load_page( 'ac_server_month_report' );
	exit;
}

add_action( 'wp_ajax_acs_get_day_report', 'acs_get_day_report' );

/**
 * Loaging page with month report
 */
function acs_get_day_report() {
	ac_load_page( 'ac_server_day_report' );
	exit;
}

add_action( 'wp_ajax_acs_get_group_report', 'acs_get_group_report' );

/**
 * Loaging page with group report
 */
function acs_get_group_report() {
	ac_load_page( 'ac_server_group_report' );
	exit;
}

/**
 * Bug fixing
 * Update Data
 */

add_action( 'wp_ajax_update_data', 'update_data' );

function update_data() {
	global $wpdb;
	$group_id = intval($_POST['group_id'] );
	// $result = $wpdb->query( 'UPDATE ' . HISTORY_TABLE . ',' . CAMPAIGNS_TABLE . ' SET ' . HISTORY_TABLE . '.group_id=' . $group_id . ' WHERE ' . HISTORY_TABLE . '.campaign_id=' . CAMPAIGNS_TABLE . '.campaign_id AND ' . CAMPAIGNS_TABLE . '.group_id=' . $group_id );
	$result = $wpdb->query( 
		'UPDATE ' . HISTORY_TABLE . ' 
		INNER JOIN ' . CAMPAIGNS_TABLE . ' 
		SET ' . HISTORY_TABLE . '.group_id=' . $group_id .
		' WHERE ' . HISTORY_TABLE . '.campaign_id = ' . CAMPAIGNS_TABLE . '.campaign_id AND ' . CAMPAIGNS_TABLE . '.group_id=' . $group_id 
	);
	wp_die();
}

add_action( 'wp_ajax_acs_get_clients_logs', 'acs_get_clients_logs' );

/**
 * Loading page with clients logs
 */
function acs_get_clients_logs() {
	ac_load_page( 'ac_server_clients_logs' );
	exit;
}

add_action( 'wp_ajax_acs_get_history', 'acs_get_history' );

/**
 * Getting paged history
 */
function acs_get_history() {
	ac_load_page( 'ac_server_history' );
	exit;
}

add_action( 'wp_ajax_acs_export_history', 'acs_export_history' );

/**
 * Exporting whole history
 */
function acs_export_history() {
	header( "Content-Type: text/csv" );
	header( 'Content-Disposition: attachment; filename="file.csv"' );
	$history = AC_Data::get_history( 0 );

	$out_history		 = array();
	$out_history[ 0 ]	 = array( 'Event', 'Campaign Name', 'Campaign Type', 'Advertiser', 'Banner Name', 'Referer URL', 'Remote IP', 'Webpage URL', 'Remote Country', 'Date' );
	foreach ( $history as $index => $rec ) {
		switch ( $rec->campaign_type ) {
			case 'selected':
				$campaign_type	 = 'Selected';
				break;
			case 'random':
				$campaign_type	 = 'Random';
				break;
			case 'all':
				$campaign_type	 = 'Rotated';
				break;
		}

		if ( (int) $rec->parent_image_id != 0 ) {
			$parent_banner	 = AC_Data::get_banner( $rec->parent_image_id );
			$rec->title		 = $parent_banner[ 'title' ];
		}

		if ( isset( $rec->title ) ) {
			$filename = cmac_get_upload_dir() . $rec->filename;
			if ( is_file( $filename ) ) {
				$image_size	 = getimagesize( $filename );
				$title		 = $rec->title . '(' . $image_size[ 0 ] . 'x' . $image_size[ 1 ] . ')';
			} else {
				$title = $rec->title;
			}
		} else
			$title = '';

		$out_history[ $index + 1 ][ 'event_type' ]		 = $rec->event_type;
		$out_history[ $index + 1 ][ 'campaign_name' ]	 = $rec->campaign_title;
		$out_history[ $index + 1 ][ 'campaign_type' ]	 = $campaign_type;
		$out_history[ $index + 1 ][ 'advertiser' ]		 = $rec->advertiser_name;
		$out_history[ $index + 1 ][ 'banner_name' ]		 = $title;
		$out_history[ $index + 1 ][ 'client_domain' ]	 = $rec->referer_url;
		$out_history[ $index + 1 ][ 'remote_ip' ]		 = $rec->remote_ip;
		$out_history[ $index + 1 ][ 'webpage_url' ]		 = $rec->webpage_url;
		$out_history[ $index + 1 ][ 'remote_country' ]	 = $rec->remote_country;
		$out_history[ $index + 1 ][ 'regdate' ]			 = $rec->regdate;
	}
	ac_outputCSV( $out_history );
	exit;
}

add_action( 'wp_ajax_acs_empty_history', 'acs_empty_history' );

/**
 * Emptying history
 */
function acs_empty_history() {
	AC_Data::empty_history();
	ac_load_page( 'ac_server_history' );
	exit;
}

add_action( 'wp_ajax_acs_empty_history_by_date', 'acs_empty_history_by_date' );

/**
 * Emptying history by date
 */
function acs_empty_history_by_date() {
	echo AC_Data::empty_history_by_date();
	exit;
}

add_action( 'wp_ajax_acs_get_server_load', 'acs_get_server_load' );

/**
 * Getting server load
 */
function acs_get_server_load() {
	ac_load_page( 'ac_server_load' );
	exit;
}

/* * ******************** */
/* EVENTS */
/* * ******************** */

/**
 * Events trigger
 * @return Boolean
 * @param String   $event_name  Event name
 * @param Array   $args  Arguments
 */
function ac_trigger_event( $event_name, $args ) { 
	$historyDisabled = get_option( 'acs_disable_history_table', null );
	if ( $historyDisabled == 1 ) {
		return false;
	}
	global $wpdb;

	$country		 = ac_get_country_by_ip( $args[ 'remote_ip' ] );
	$country_name	 = $country[ 'countryName' ];

	cmac_log( 'Triggering ' . $event_name . ' event' );
	if ( empty( $args[ 'group_id' ] ) ) {
		$args[ 'group_id' ] = null;
	}
	$getGroupID = $wpdb->get_var('SELECT group_id FROM ' . CAMPAIGNS_TABLE .' WHERE campaign_id=' . intval($args[ 'campaign_id' ]) .  '');

	switch ( $event_name ) {
		case 'new_impression':
			if ( !isset( $args[ 'campaign_id' ] ) || !is_numeric( $args[ 'campaign_id' ] ) || !isset( $args[ 'banner_id' ] ) || !is_numeric( $args[ 'banner_id' ] ) || !isset( $args[ 'http_referer' ] ) )
				return false;

			$wpdb->query( $wpdb->prepare( 'INSERT INTO ' . HISTORY_TABLE . ' SET event_type="impression", group_id=%d,  campaign_id=%d, banner_id=%d, referer_url=%s, webpage_url=%s, remote_ip=%s, remote_country=%s, campaign_type=%s', $getGroupID, $args[ 'campaign_id' ], $args[ 'banner_id' ], $args[ 'http_referer' ], $args[ 'webpage_url' ], $args[ 'remote_ip' ], $country_name, $args[ 'campaign_type' ] ) );
			return true;
		case 'new_click':
			if ( !isset( $args[ 'campaign_id' ] ) || !is_numeric( $args[ 'campaign_id' ] ) || !isset( $args[ 'banner_id' ] ) || !is_numeric( $args[ 'banner_id' ] ) || !isset( $args[ 'http_referer' ] ) )
				return false;

			$wpdb->query( $wpdb->prepare( 'INSERT INTO ' . HISTORY_TABLE . ' SET event_type="click", group_id=%d, campaign_id=%d, banner_id=%d, referer_url=%s, webpage_url=%s, remote_ip=%s, remote_country=%s, campaign_type=%s', $getGroupID, $args[ 'campaign_id' ], $args[ 'banner_id' ], $args[ 'http_referer' ], $args[ 'webpage_url' ], $args[ 'remote_ip' ], $country_name, $args[ 'campaign_type' ] ) );
			return true;
	}
	return false;
}

if ( !wp_next_scheduled( 'send_campaign_notifications' ) ) {
	wp_schedule_event( time(), 'hourly', 'send_campaign_notifications' );
}

add_action( 'send_campaign_notifications', 'ac_send_campaign_notifications' );

/**
 * Cronjob, sending notifications
 */
function ac_send_campaign_notifications() {
	global $wpdb;
	$campaigns = AC_Data::get_campaigns();
	if ( empty( $campaigns ) )
		return;

	$tpl_vars		 = array();
	$email_template	 = get_option( 'acs_notification_email_tpl', "Hi,\n\nCampaign '%campaign_name%'(Campaign ID: %campaign_id%) stopped working\nReason: %reason%\n\nBest Regards,\nCampaign Manager" );

	foreach ( $campaigns as $campaign_el ) {
		$email_content					 = $email_template;
		$campaign						 = AC_Data::get_campaign( $campaign_el->campaign_id, true );
		$campaign[ 'impressions_cnt' ]	 = $campaign_el->impressions_cnt;
		$campaign[ 'clicks_cnt' ]		 = $campaign_el->clicks_cnt;

		if ( $campaign[ 'status' ] == '0' )
			continue;
		if ( empty( $campaign[ 'manager_email' ] ) )
			continue;
		if ( (int) $campaign[ 'send_notifications' ] == '0' )
			continue;

		$tpl_vars[ 'campaign_name' ] = $campaign[ 'title' ];
		$tpl_vars[ 'campaign_id' ]	 = $campaign[ 'campaign_id' ];

		if ( $campaign[ 'impressions_cnt' ] >= $campaign[ 'max_impressions' ] && (int) $campaign[ 'max_impressions' ] > 0 ) {
			$tpl_vars[ 'reason' ]	 = 'Max impressions achieved';
			foreach ( $tpl_vars as $index => $var )
				$email_content			 = str_replace( '%' . $index . '%', $var, $email_content );

			wp_mail( $campaign[ 'manager_email' ], 'Campaign stopped working', $email_content );
			$wpdb->query( 'UPDATE ' . CAMPAIGNS_TABLE . ' SET status="0" WHERE campaign_id="' . $campaign[ 'campaign_id' ] . '"' );
			continue;
		}

		if ( $campaign[ 'clicks_cnt' ] >= $campaign[ 'max_clicks' ] && (int) $campaign[ 'max_clicks' ] > 0 ) {
			$tpl_vars[ 'reason' ]	 = 'Max clicks achieved';
			foreach ( $tpl_vars as $index => $var )
				$email_content			 = str_replace( '%' . $index . '%', $var, $email_content );

			wp_mail( $campaign[ 'manager_email' ], 'Campaign stopped working', $email_content );
			$wpdb->query( 'UPDATE ' . CAMPAIGNS_TABLE . ' SET status="0" WHERE campaign_id="' . $campaign[ 'campaign_id' ] . '"' );
			continue;
		}

		if ( !isset( $campaign[ 'date_till' ] ) || empty( $campaign[ 'date_till' ] ) )
			continue;

		$activity_end_dates = array();
		foreach ( $campaign[ 'date_till' ] as $period_index => $date_till ) {
			$date_till				 = new DateTime( $date_till );
			$activity_end_dates[]	 = strtotime( $date_till->format( 'Y-m-d' ) . ' ' . $campaign[ 'hours_to' ][ $period_index ] . ':' . $campaign[ 'mins_to' ][ $period_index ] . ':00' );
		}

		$last_activity_end_date = max( $activity_end_dates );

		if ( time() > $last_activity_end_date && ((time() - $last_activity_end_date) / 60 / 60 < 1.7) ) { // if campaign is inactive less then 1.7 hours (because cron launches ~ every hour)
			$tpl_vars[ 'reason' ]	 = 'Campaign activity finished';
			foreach ( $tpl_vars as $index => $var )
				$email_content			 = str_replace( '%' . $index . '%', $var, $email_content );

			wp_mail( $campaign[ 'manager_email' ], 'Campaign stopped working', $email_content );

			$wpdb->query( 'UPDATE ' . CAMPAIGNS_TABLE . ' SET status="0" WHERE campaign_id="' . $campaign[ 'campaign_id' ] . '"' );
			continue;
		}
	}
//  wp_mail( 'semion.co@gmail.com', 'Automatic email', 'Automatic scheduled email from WordPress.');
}

function ac_get_country_by_ip( $ip ) {
	if ( empty( $ip ) )
		return array( 'countryName' => '' );

	$ip_parts = explode( '.', $ip );
	if ( count( $ip_parts ) != 4 )
		return array( 'countryName' => '' );

	foreach ( $ip_parts as $part ) {
		if ( !is_numeric( $part ) )
			return array( 'countryName' => '' );
		if ( (int) $part < 0 || (int) $part > 256 )
			return array( 'countryName' => '' );
	}

	$api_key = get_option( 'acs_geolocation_api_key' );
	if ( !$api_key )
		return false;
	$ipLite	 = new ip2location_lite;
	$ipLite->setKey( $api_key );
	$country = $ipLite->getCountry( $ip );
	$errors	 = $ipLite->getError();
	return $country;
}

/**
 * Pagination rendering
 * @return String
 * @param Int   $current_page  Page number, min 1
 */
function ac_pagination( $current_page = 1, $where = "1" ) {
	global $wpdb;

	$uri_params		 = array();
	if ( isset( $_REQUEST[ 'events_filter' ] ) && $_REQUEST[ 'events_filter' ] != 'all' )
		$uri_params[]	 = 'event_type=' . $_REQUEST[ 'events_filter' ];

	if ( isset( $_REQUEST[ 'campaign_name' ] ) && !empty( $_REQUEST[ 'campaign_name' ] ) )
		$uri_params[] = 'campaign_name=' . $_REQUEST[ 'campaign_name' ];

	if ( isset( $_REQUEST[ 'advertiser_id' ] ) && !empty( $_REQUEST[ 'advertiser_id' ] ) && (int) $_REQUEST[ 'advertiser_id' ] != 0 )
		$uri_params[] = 'advertiser_id=' . $_REQUEST[ 'advertiser_id' ];

	if ( !empty( $uri_params ) )
		$uri_query_string	 = '&' . implode( '&', $uri_params );
	else
		$uri_query_string	 = '';

	$radius		 = 3;
//	$base_url = get_bloginfo('wpurl').'/wp-admin/admin.php?page=ac_server_history'; // not ajax
	$base_url	 = get_bloginfo( 'wpurl' ) . '/wp-admin/admin-ajax.php?action=acs_get_history'; // ajax
	$total		 = $wpdb->get_var( 'SELECT count(*) FROM ' . HISTORY_TABLE . ' h
									LEFT JOIN ' . CAMPAIGNS_TABLE . ' c ON c.campaign_id=h.campaign_id
									LEFT JOIN ' . $wpdb->term_relationships . ' tr ON tr.object_id = c.campaign_id
									LEFT JOIN ' . $wpdb->term_taxonomy . ' tt ON tt.term_taxonomy_id=tr.term_taxonomy_id
									LEFT JOIN ' . $wpdb->terms . ' t ON t.term_id = tt.term_id
									WHERE ' . $where );

	$total_pages = floor( $total / AC_HISTORY_PER_PAGE_LIMIT );
	if ( $total_pages == 1 )
		return '';
	$html		 = '<div class="asc_pagination">';

	// Before current page
	if ( $current_page > 1 ) {
		$html .= '<a href="' . $base_url . '&acs_page=1' . $uri_query_string . '">First</a>';
		$html .= '<a href="' . $base_url . '&acs_page=' . ($current_page - 1) . $uri_query_string . '">Previous</a>';
		for ( $i = ($current_page <= $radius ? 1 : $current_page - $radius); $i < $current_page; $i++ )
			$html .= '<a href="' . $base_url . '&acs_page=' . $i . $uri_query_string . '">' . $i . '</a>';
	}

	// Current page
	$html .= '<span class="acs_current_page">' . $current_page . '</span>';

	// After current page
	if ( $current_page < $total_pages ) {
		for ( $i = $current_page + 1; $i <= ($total_pages - $current_page < $radius ? $total_pages : $current_page + $radius); $i++ )
			$html .= '<a href="' . $base_url . '&acs_page=' . $i . $uri_query_string . '">' . $i . '</a>';

		$html .= '<a href="' . $base_url . '&acs_page=' . ($current_page + 1) . $uri_query_string . '">Next</a>';
		$html .= '<a href="' . $base_url . '&acs_page=' . $total_pages . $uri_query_string . '">Last</a>';
	}
	$html .= '</div>';

	return $html;
}

/**
 * Random weighted key finder
 * @return Int
 * @param Array   $weights  Array of positive integers
 */
function ac_get_random_banner_index( $weights = array() ) {
	if ( !is_array( $weights ) ) {
		$weights = array( $weights );
	}

	asort( $weights );

	$weights_sum = array_sum( $weights );

	if ( $weights_sum == 0 )
		return array_rand( $weights, 1 );

	$rand_num = rand( 1, $weights_sum );

	$diapasons			 = array();
	$weights_sum		 = 0;
	$prev_weights_sum	 = 0;
	$res				 = array();
	foreach ( $weights as $cur_key => $weight ) {
		$weights_sum += $weight;
		$diapasons[ $cur_key ]	 = array( $prev_weights_sum + 1, $weights_sum );
		$prev_weights_sum		 = $weights_sum;
		if ( $rand_num <= $diapasons[ $cur_key ][ 1 ] && $rand_num >= $diapasons[ $cur_key ][ 0 ] )
			$res[]					 = $cur_key;
	}

	$res_rand_key = array_rand( $res, 1 );

	return $res[ $res_rand_key ];
}

/**
 * Normalizing weights, till sum = 100
 * @return Array
 * @param Array   $weights  Array of positive integers
 */
function ac_normalize_weights( $weights ) {
	$sum = array_sum( $weights );
	if ( $sum == 0 ) {
		return $weights;
	}

	$rates = array();
	foreach ( $weights as $index => $weight ) {
		$weights[ $index ] = round( $weight / $sum * 100 );
	}

	$new_sum = array_sum( $weights );

	$rand_key = array_rand( $weights, 1 );

	if ( $new_sum != 100 ) {
		$weights[ $rand_key ] += 100 - $new_sum;
	}
	return $weights;
}

/**
 * Converter of array to html list
 * @return String
 * @param Array   $data  Array of strings
 * @param String  $title Title of list
 * @param String   $class  CSS class of list
 */
function ac_format_list( $data, $title = '', $class = '' ) {
	$ret_html = '';

	if ( !is_array( $data ) ) {
		$ret_html .= 'Data format is wrong';
		return $ret_html;
	}
	if ( !empty( $title ) ) {
		$ret_html = '<strong>' . $title . '</strong><br/>';
	}
	$ret_html .= '<ul ' . (!empty( $class ) ? 'class="' . $class . '"' : '') . ' >';
	foreach ( $data as $field => $value ) {
		$ret_html .= '<li> <strong>' . $field . ':</strong> <pre>' . (!empty( $value ) ? var_export( $value, true ) : '- empty -') . '</pre></li>';
	}
	$ret_html .= '</ul>';
	return $ret_html;
}

// function from http://us1.php.net/manual/ru/function.fputcsv.php
/**
 * Outputs array to csv file
 * @param Array
 */
function ac_outputCSV( $data ) {

	$outstream = fopen( "php://output", 'w' );

	function __outputCSV( &$vals, $key, $filehandler ) {
		fputcsv( $filehandler, $vals, ';', '"' );
	}

	array_walk( $data, '__outputCSV', $outstream );

	fclose( $outstream );
}

/**
 * Chooses responsive banner from array
 * @param Array   $data  Array
 */
function ac_get_responsive_banner( $banner_variants, $container_width ) {

	if ( !is_numeric( $container_width ) || (int) $container_width <= 0 ) {
		// main banner
		return AC_Data::get_banner( $banner_variants[ count( $banner_variants ) - 1 ] );
	}

	$widths					 = array();
	$max_responsive_width	 = 0;
	$min_responsive_width	 = 9999999;
	$responsive_width_index	 = -1;
	$min_width_index		 = -1;

	foreach ( $banner_variants as $index => $banner ) {
		$info = getimagesize( cmac_get_upload_dir() . $banner );

		if ( !$info ) {
			continue;
		}

		$widths[ $banner ] = $info[ 0 ];

		if ( $info[ 0 ] < $min_responsive_width ) { // getting min width, for case if container is too small
			$min_responsive_width	 = $info[ 0 ];
			$min_width_index		 = $index;
		}

		if ( $info[ 0 ] > $container_width ) {
			continue;
		} // only banners which fit into container are responsive


		if ( $info[ 0 ] > $max_responsive_width ) {
			$max_responsive_width	 = $info[ 0 ];
			$responsive_width_index	 = $index;
		}
	}

	if ( $responsive_width_index == -1 ) {
		if ( $min_width_index != -1 ) {
			return AC_Data::get_banner( $banner_variants[ $min_width_index ] );
		} else {
			return AC_Data::get_banner( $banner_variants[ count( $banner_variants ) - 1 ] );
		} // main banner
	}

	return AC_Data::get_banner( $banner_variants[ $responsive_width_index ] );
}

function ac_check_gd() {
	@$ini = ini_get_all( 'gd' );
	return $ini !== false;
}

if ( !function_exists( 'parse_php_info' ) ) {

	function parse_php_info() {
		ob_start();
		phpinfo( INFO_MODULES );
		$s			 = ob_get_contents();
		ob_end_clean();
		$s			 = strip_tags( $s, '<h2><th><td>' );
		$s			 = preg_replace( '/<th[^>]*>([^<]+)<\/th>/', "<info>\\1</info>", $s );
		$s			 = preg_replace( '/<td[^>]*>([^<]+)<\/td>/', "<info>\\1</info>", $s );
		$vTmp		 = preg_split( '/(<h2>[^<]+<\/h2>)/', $s, -1, PREG_SPLIT_DELIM_CAPTURE );
		$vModules	 = array();
		for ( $i = 1; $i < count( $vTmp ); $i++ ) {
			if ( preg_match( '/<h2>([^<]+)<\/h2>/', $vTmp[ $i ], $vMat ) ) {
				$vName	 = trim( $vMat[ 1 ] );
				$vTmp2	 = explode( "\n", $vTmp[ $i + 1 ] );
				foreach ( $vTmp2 AS $vOne ) {
					$vPat	 = '<info>([^<]+)<\/info>';
					$vPat3	 = "/$vPat\s*$vPat\s*$vPat/";
					$vPat2	 = "/$vPat\s*$vPat/";
					if ( preg_match( $vPat3, $vOne, $vMat ) ) { // 3cols
						$vModules[ $vName ][ trim( $vMat[ 1 ] ) ] = array( trim( $vMat[ 2 ] ), trim( $vMat[ 3 ] ) );
					} elseif ( preg_match( $vPat2, $vOne, $vMat ) ) { // 2cols
						$vModules[ $vName ][ trim( $vMat[ 1 ] ) ] = trim( $vMat[ 2 ] );
					}
				}
			}
		}
		return $vModules;
	}

}

if ( !function_exists( 'cminds_units2bytes' ) ) {

	function cminds_units2bytes( $str ) {
		$units		 = array( 'B', 'K', 'M', 'G', 'T' );
		$unit		 = preg_replace( '/[0-9]/', '', $str );
		$unitFactor	 = array_search( strtoupper( $unit ), $units );
		if ( $unitFactor !== false ) {
			return preg_replace( '/[a-z]/i', '', $str ) * pow( 2, 10 * $unitFactor );
		}
	}

}

if ( !function_exists( 'cmac_log' ) ) {

	function cmac_log( $message ) {
		if ( CMAC_DEBUG != '1' ) {
			return;
		}

		$f = fopen( ACS_PLUGIN_PATH . '/log.txt', 'a' );
		fwrite( $f, date( 'Y-m-d H:i:s' ) . ': ' . $message . "\n" );
		fclose( $f );
	}

}

if ( !function_exists( 'cminds_show_message' ) ) {

	/**
	 * Generic function to show a message to the user using WP's
	 * standard CSS classes to make use of the already-defined
	 * message colour scheme.
	 *
	 * @param $message The message you want to tell the user.
	 * @param $errormsg If true, the message is an error, so use
	 * the red message style. If false, the message is a status
	 * message, so use the yellow information message style.
	 */
	function cminds_show_message( $message, $errormsg = false ) {
		if ( $errormsg ) {
			echo '<div id="message" class="error">';
		} else {
			echo '<div id="message" class="updated fade">';
		}

		echo "<p><strong>$message</strong></p></div>";
	}

}
