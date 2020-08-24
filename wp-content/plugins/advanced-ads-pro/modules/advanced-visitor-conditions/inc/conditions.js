if ( typeof advanced_ads_pro_visitor_conditions === 'object' ) {
	// set cookie for referrer visitor condition
	if ( advads.get_cookie( advanced_ads_pro_visitor_conditions.referrer_cookie_name ) === undefined && document.referrer !== '' ) {
		advads.set_cookie( advanced_ads_pro_visitor_conditions.referrer_cookie_name, document.referrer, advanced_ads_pro_visitor_conditions.referrer_exdays ); 
	}
	// set cookie with page impressions
	if ( advads.get_cookie( advanced_ads_pro_visitor_conditions.page_impr_cookie_name ) === undefined ) {
		advads.set_cookie( advanced_ads_pro_visitor_conditions.page_impr_cookie_name, '1', advanced_ads_pro_visitor_conditions.page_impr_exdays );
	} else {
		var num = parseInt( advads.get_cookie( advanced_ads_pro_visitor_conditions.page_impr_cookie_name ));
		num = num + 1;
		advads.set_cookie( advanced_ads_pro_visitor_conditions.page_impr_cookie_name, num, advanced_ads_pro_visitor_conditions.page_impr_exdays );
	}
}
