/*
 * global js functions for Advanced Ads
 */
jQuery( document ).ready(function () {

	/**
	 * ADMIN NOTICES
	 */
	// close button
	// .advads-notice-dismiss class can be used to add a custom close button (e.g., link)
	jQuery(document).on('click', '.advads-admin-notice button.notice-dismiss, .advads-admin-notice .advads-notice-dismiss', function(){
	    var messagebox = jQuery(this).parents('.advads-admin-notice');
	    if( messagebox.attr('data-notice') === undefined) return;

	    var query = {
		action: 'advads-close-notice',
		notice: messagebox.attr('data-notice'),
		nonce: advadsglobal.ajax_nonce
	    };
	    // send query
	    jQuery.post(ajaxurl, query, function (r) {
		messagebox.fadeOut();
	    });
	});
	// hide notice for 7 days
	jQuery(document).on('click', '.advads-admin-notice .advads-notice-hide', function(){
	    var messagebox = jQuery(this).parents('.advads-admin-notice');
	    if( messagebox.attr('data-notice') === undefined) return;

	    var query = {
		action: 'advads-hide-notice',
		notice: messagebox.attr('data-notice'),
		nonce: advadsglobal.ajax_nonce
	    };
	    // send query
	    jQuery.post(ajaxurl, query, function (r) {
		messagebox.fadeOut();
	    });
	});
	// autoresponder button
	jQuery('.advads-notices-button-subscribe').click(function(){
	    if(this.dataset.notice === undefined) return;
	    var messagebox = jQuery(this).parents('.advads-admin-notice');
	    messagebox.find('p').append( '<span class="spinner advads-spinner"></span>' );

	    var query = {
		action: 'advads-subscribe-notice',
		notice: this.dataset.notice,
		nonce: advadsglobal.ajax_nonce
	    };
	    // send and close message
	    jQuery.post(ajaxurl, query, function (r) {
		if(r === '1'){
		    messagebox.fadeOut();
		} else {
		    messagebox.find('p').html(r);
		    messagebox.removeClass('updated').addClass('error');
		}
	    });

	});
	/**
	 * Functions for Ad Health Notifications in the backend
	 */
	// hide button (adds item to "ignore" list)
	jQuery(document).on('click', '.advads-ad-health-notice-hide', function(){
	    var notice = jQuery(this).parents('li');
	    if( notice.attr('data-notice') === undefined) return;
	    // var list = notice.parent( 'ul' );
	    var remove = jQuery( this ).hasClass( 'remove' );
	    
	    // fix height to prevent the box from going smaller first, then show the "show" link and grow again
	    jQuery( '#advads_overview_notices' ).css( 'height', jQuery( '#advads_overview_notices' ).height() + 'px' );

	    var query = {
		action: 'advads-ad-health-notice-hide',
		notice: notice.attr('data-notice'),
		nonce: advadsglobal.ajax_nonce
	    };
	    // fade out first or remove, so users can’t click twice
	    if( remove ){
		notice.remove();
	    } else {
		notice.hide();
	    }
	    advads_ad_health_maybe_remove_list();
	    // send query
	    jQuery.post(ajaxurl, query, function (r) {
		    // update number in menu
		    advads_ad_health_reload_number_in_menu();
		    // update show button
		    advads_ad_health_reload_show_link();
		    // remove the fixed height
		    jQuery( '#advads_overview_notices' ).css( 'height', '' );
	    });
	});
	// show all hidden notices
	jQuery(document).on('click', '.adsvads-ad-health-notices-show-hidden', function(){
		advads_ad_health_show_hidden();
	});
	
	/**
	 * DEACTIVATION FEEDBACK FORM
	 */
	// show overlay when clicked on "deactivate"
	advads_deactivate_link = jQuery('.wp-admin.plugins-php tr[data-slug="advanced-ads"] .row-actions .deactivate a');
	advads_deactivate_link_url = advads_deactivate_link.attr( 'href' );
	advads_deactivate_link.click(function ( e ) {
		e.preventDefault();
		// only show feedback form once per 30 days
		var c_value = advads_admin_get_cookie( "advads_hide_deactivate_feedback" );
		if (c_value === undefined){
		    jQuery( '#advanced-ads-feedback-overlay' ).show();
		} else {
		    // click on the link
		    window.location.href = advads_deactivate_link_url;
		}
	});
	// show text fields
	jQuery('#advanced-ads-feedback-content input[type="radio"]').click(function () {
		// show text field if there is one
		jQuery(this).parents('li').next('li').children('input[type="text"], textarea').show();
	});
	// handle technical issue feedback in particular
	jQuery('#advanced-ads-feedback-content .advanced_ads_disable_help_text').focus(function () {
		// show text field if there is one
		jQuery(this).parents('li').siblings('.advanced_ads_disable_reply').show();
	});
	// send form or close it
	jQuery('#advanced-ads-feedback-content .button').click(function ( e ) {
		e.preventDefault();
		var self = jQuery( this );
		// set cookie for 30 days
		var exdate = new Date();
		exdate.setSeconds( exdate.getSeconds() + 2592000 );
		document.cookie = "advads_hide_deactivate_feedback=1; expires=" + exdate.toUTCString() + "; path=/";
		// save if plugin should be disabled
		var disable_plugin = self.hasClass('advanced-ads-feedback-not-deactivate') ? false : true;
			
		// hide the content of the feedback form
		jQuery( '#advanced-ads-feedback-content form' ).hide();
		if ( self.hasClass('advanced-ads-feedback-submit') ) {
			// show feedback message
			jQuery( '#advanced-ads-feedback-after-submit-waiting' ).show();
			if( disable_plugin ){
				jQuery( '#advanced-ads-feedback-after-submit-disabling-plugin' ).show();
			}
			jQuery.ajax({
			    type: 'POST',
			    url: ajaxurl,
			    dataType: 'json',
			    data: {
				action: 'advads_send_feedback',
				feedback: self.hasClass('advanced-ads-feedback-not-deactivate') ? true : false,
				formdata: jQuery( '#advanced-ads-feedback-content form' ).serialize()
			    },
			    complete: function (MLHttpRequest, textStatus, errorThrown) {
				    // deactivate the plugin and close the popup with a timeout
				    setTimeout( function(){
					    jQuery( '#advanced-ads-feedback-overlay' ).remove();
				    }, 2000 )
				    if( disable_plugin ){
					window.location.href = advads_deactivate_link_url;
				    }

			    }
			});
		} else { // currently not reachable
			jQuery( '#advanced-ads-feedback-overlay' ).remove();
			window.location.href = advads_deactivate_link_url;
		}
	});
	// close form and disable the plugin without doing anything
	jQuery('.advanced-ads-feedback-only-deactivate').click(function ( e ) {
		// hide the content of the feedback form
		jQuery( '#advanced-ads-feedback-content form' ).hide();
		// show feedback message
		jQuery( '#advanced-ads-feedback-after-submit-goodbye' ).show();
		jQuery( '#advanced-ads-feedback-after-submit-disabling-plugin' ).show();
		// wait 3 seconds
		setTimeout(function(){
			jQuery( '#advanced-ads-feedback-overlay' ).hide();
			window.location.href = advads_deactivate_link_url;
		}, 3000);
	});
	// close button for feedback form
	jQuery('#advanced-ads-feedback-overlay-close-button').click(function ( e ) {
		jQuery( '#advanced-ads-feedback-overlay' ).hide();
	});
});

function advads_admin_get_cookie (name) {
	var i, x, y, ADVcookies = document.cookie.split( ";" );
	for (i = 0; i < ADVcookies.length; i++)
	{
		x = ADVcookies[i].substr( 0, ADVcookies[i].indexOf( "=" ) );
		y = ADVcookies[i].substr( ADVcookies[i].indexOf( "=" ) + 1 );
		x = x.replace( /^\s+|\s+$/g, "" );
		if (x === name)
		{
			return unescape( y );
		}
	}
}

/**
 * load RSS widget on dashboard page using AJAX to not block rendering the rest of the page
 */
function advads_load_dashboard_rss_widget_content(){
	jQuery.ajax({
		type: 'POST',
		url: ajaxurl,
		data: {
		    action: 'advads_load_rss_widget_content',
		    nonce: advadsglobal.ajax_nonce
		},
		success: function (data, textStatus, XMLHttpRequest) {
			if (data) {
				jQuery( '#advads-dashboard-widget-placeholder' ).before( data );
			}
		},
		complete: function (MLHttpRequest, textStatus, errorThrown) {
			// remove the placeholder
			jQuery( '#advads-dashboard-widget-placeholder' ).remove();

		}
	});
}

/**
 * Ad Health Notices in backend
 */
// display notices list
function advads_display_ad_health_notices(){

	var query = {
	    action: 'advads-ad-health-notice-display',
	    nonce: advadsglobal.ajax_nonce
	};
	
	var widget = jQuery( '#advads_overview_notices .main' );
	
	// add loader icon to the widget
	widget.html( '<span class="advads-loader"></span>' );
	// send query
	jQuery.post(ajaxurl, query, function (r) {
		widget.html( r );
		
		// update number in menu
		advads_ad_health_reload_number_in_menu();
		// update list headlines
		advads_ad_health_maybe_remove_list();
		
		// remove widget, if return is empty
		if( r === '' ){
			jQuery( '#advads_overview_notices' ).remove();
		}
	});
}
// push a notice to the queue
function advads_push_notice( key, attr = '' ){

	var query = {
	    action: 'advads-ad-health-notice-push',
	    key: key,
	    attr: attr,
	    nonce: advadsglobal.ajax_nonce
	};
	// send query
	jQuery.post(ajaxurl, query, function (r) {});
}
// show notices of a given type again
function advads_ad_health_show_hidden(){
	var query = {
	    action: 'advads-ad-health-notice-unignore',
	    nonce: advadsglobal.ajax_nonce
	};
	// show all hidden
	jQuery( document ).find( '#advads_overview_notices .advads-ad-health-notices > li:hidden' ).show();
	// update the button
	advads_ad_health_reload_show_link();
	advads_ad_health_maybe_remove_list();
	// send query
	jQuery.post(ajaxurl, query, function (r) {
		// update issue count
		advads_ad_health_reload_number_in_menu();
	});
};
// hide list fragments if last item was hidden/removed
function advads_ad_health_maybe_remove_list(){
	// get all lists
    	var lists = jQuery( document ).find( '#advads_overview_notices .advads-ad-health-notices' );

	// check each list separately
	lists.each( function( index ) { 
		var list = jQuery( this );
		// check if there are visible items in the list
		if( list.find( 'li:visible' ).length ){
			// show parent headline
			list.prev( 'h3' ).show();
		} else {
			// hide parent headline
			list.prev( 'h3' ).hide();

		}
	});

}
// reload number of notices shown in the sidebar based on element in the problems list
function advads_ad_health_reload_number_in_menu(){
	// get number of notices
	var number = jQuery( document ).find( '#advads_overview_notices .advads-ad-health-notices > li:visible' ).length;
	jQuery( '#toplevel_page_advanced-ads .update-count').html( number );
}
// update show X issues link – number and visibility
function advads_ad_health_reload_show_link(){
	// get number of invisible elements
	var number = jQuery( document ).find( '#advads_overview_notices .advads-ad-health-notices > li:hidden' ).length;
	var show_link = jQuery( '.adsvads-ad-health-notices-show-hidden' );
	// update number in the link
	jQuery( '.adsvads-ad-health-notices-show-hidden span.count' ).html( number );
	// hide of show, depending on number
	if( 0 === number ){
		show_link.hide();
	} else {
		show_link.show();
	}
}