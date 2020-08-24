<?php
/**
 * Prepare content of the 'ads.txt' file.
 *
 * @package Advanced_Ads_Ads_Txt
 */

class_exists( 'Advanced_Ads', false ) || exit();

if (
	! is_multisite()
	|| ( function_exists( 'is_site_meta_supported' ) && is_site_meta_supported() )
) {
	if ( is_admin() ) {
		new Advanced_Ads_Ads_Txt_Admin( new Advanced_Ads_Ads_Txt_Strategy() );
	} else {
		new Advanced_Ads_Ads_Txt_Public( new Advanced_Ads_Ads_Txt_Strategy() );
	}
}
