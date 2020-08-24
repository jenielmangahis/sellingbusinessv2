jQuery( document ).ready(function( $ ){
    $( '.advads-option-group-refresh input:checkbox:checked' ).each( function() {
        var number_option = $( this ).parents( '.advads-ad-group-form' ).find( '.advads-option-group-number' );
        number_option.val( 'all' ).hide();
    });

    $( '.advads-option-group-refresh input:checkbox' ).click( function() {
        var number_option = $( this ).parents( '.advads-ad-group-form' ).find( '.advads-option-group-number' );
        if ( this.checked ) {
            number_option.val( 'all' ).hide();
        } else {
            number_option.show();
        }
    });

    jQuery( '.advads-option-placement-cache-busting input' ).on( 'change', function() {
        var cb_state = jQuery( this ).val(),
        $inputs = jQuery( this ).closest( '.advads-placements-table-options' ).find( '.advanced-ads-inputs-dependent-on-cb' );

        if ( 'off' === cb_state ) {
            // Hide UI elements that work only with cache-busting.
            $inputs.hide().next().show();
        }
        else {
            $inputs.show().next().hide();
        }
    });
});




function advads_cb_check_set_status( status, msg ) {
    if ( status === true ) {
        jQuery( '#advads-cache-busting-possibility' ).val( true );
    } else {
        jQuery( '#advads-cache-busting-possibility' ).val( false );
        jQuery( '#advads-cache-busting-error-result' ).append( msg ? '<br />' + msg : '' ).show();
    }
}

function advads_cb_check_ad_markup( ad_content ) {
    if ( ! ad_content ) {
        return;
    }

    // checks whether the ad contains the jQuery.document.ready() and document.write(ln) functions
    if ( ( /\)\.ready\(/.test( ad_content ) || /(\$|jQuery)\(\s*?function\(\)/.test( ad_content ) ) && /document\.write/.test( ad_content ) ) {
        advads_cb_check_set_status( false );
        return;
    }

	var search_str = 'cache_busting_test';
	var source = ad_content += search_str;
	var parser = htmlParser( source, { autoFix: true } );
	var tok, result = '';

	while ( ( tok = parser.readToken() ) ) {
		if (tok) {
			result += htmlParser.tokenToString(tok);
		}
	}
	advads_cb_check_set_status( ( result.substr( - search_str.length ) === search_str ) ? true : false );
}

