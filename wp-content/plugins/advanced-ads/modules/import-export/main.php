<?php

class_exists( 'Advanced_Ads', false ) || exit();

if ( is_admin() ) {
	add_action( 'advanced-ads-submenu-pages', 'advads_add_import_export_submenu' );
    Advanced_Ads_Export::get_instance();

	/**
	 * Add import & export page
	 *
	 */
	function advads_add_import_export_submenu( $plugin_slug ) {
		add_submenu_page(
			'options.php', __( 'Import &amp; Export', 'advanced-ads' ), __( 'Import &amp; Export', 'advanced-ads' ), Advanced_Ads_Plugin::user_cap( 'advanced_ads_manage_options' ), $plugin_slug . '-import-export', 'advads_display_import_export_page'
		);
	}

	/**
	 * Render the import & export page
	 *
	 */
	function advads_display_import_export_page() {
		Advanced_Ads_Import::get_instance()->dispatch();
		$messages = array_merge( Advanced_Ads_Import::get_instance()->get_messages(), Advanced_Ads_Export::get_instance()->get_messages() );

		include ADVADS_BASE_PATH . 'modules/import-export/views/page.php';
	}
}

add_action( 'advanced-ads-cleanup-import-file', 'advads_delete_old_import_file' );

/**
 * Delete old import file via cron
 *
 */
function advads_delete_old_import_file( $path ) {
	//error_log( 'delete_old_xml_file ' . $path );
	if ( file_exists( $path ) ) {
		@unlink( $path );
	}
}



