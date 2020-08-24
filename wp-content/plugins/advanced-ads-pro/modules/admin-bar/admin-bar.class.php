<?php

class Advanced_Ads_Pro_Module_Admin_Bar {

	public function __construct() {
		// TODO load options
		// add admin bar item with current ads
		if ( ! is_admin() ) {
			add_action( 'admin_bar_menu', array( $this, 'admin_bar_current_ads' ), 999 );
		}
	}

	/**
	 * add admin bar menu with current displayed ads and ad groups
	 *
	 * @since 1.0.0
	 * @param arr $wp_admin_bar
	 */
	public function admin_bar_current_ads( $wp_admin_bar ) {

		$cap = method_exists( 'Advanced_Ads_Plugin', 'user_cap' ) ?  Advanced_Ads_Plugin::user_cap( 'advanced_ads_edit_ads') : 'manage_options';
	    
		if( ! current_user_can( $cap ) ) {
			return;
		}

		// add main menu item
		$args = array(
			'id'    => 'advads_current_ads',
			'title' => __( 'Ads', 'advanced-ads-pro' ),
			'href'  => false,
		);
		$wp_admin_bar->add_node( $args );

		// add item for each ad
		$ads = Advanced_Ads::get_instance()->current_ads;

		// we need it for admin_bar.js. WP will write js for us
		if ( ! $ads ) {
			$args = array(
				'parent' => 'advads_current_ads',
				'id'     => 'advads_no_ads_found',
				'title'  => __( 'No Ads found', 'advanced-ads-pro' ),
				'href'   => false,
			);
			$wp_admin_bar->add_node( $args );
		}

		foreach ( $ads as $_key => $_ad ){
		  // -TODO $type not used // TODO types are extendable through Advanced_Ads_Select
			$type = '';
			switch ( $_ad['type'] ){
				case 'ad' : $type = __( 'ad', 'advanced-ads-pro' ); break;
				case 'group' : $type = __( 'group', 'advanced-ads-pro' ); break;
				case 'placement' : $type = __( 'placement', 'advanced-ads-pro' ); break;
			}

			$args = array(
					'parent' => 'advads_current_ads',
					'id'    => 'advads_current_ad_' . $_key,
					'title' => $_ad['title'] . ' ('. $_ad['type'] .')',
					'href'  => false,
			);
			$wp_admin_bar->add_node( $args );
		}
	}
}
