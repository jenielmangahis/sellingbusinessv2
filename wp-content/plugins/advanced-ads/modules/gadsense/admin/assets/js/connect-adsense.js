;(function($){
    
    // Unique instance of "advadsMapiConnectClass"
    var INSTANCE = null;
    
    var advadsMapiConnectClass = function( content, options ) {
        this.options = {};
        this.AUTH_WINDOW = null;
        this.modal = $( '#gadsense-modal' );
        this.frame = null;
        if ( 'undefined' == typeof content || ! content ) {
            content = 'confirm-code';
        }
        this.setOptions( options );
        this.init();
        

        this.show( content );
        return this;
    };
    
    advadsMapiConnectClass.prototype = {
        
        constructor: advadsMapiConnectClass,
        
        // Set options, for re-use of existing instance for a different purpose.
        setOptions: function( options ) {
            var defaultOptions = {
                autoads: false,
                onSuccess: false,
                onError: false,
            };
            if ( 'undefined' == typeof options ) {
                options = defaultOptions;
            }
            this.options = $.extend( {}, defaultOptions, options);
        },
        
        // Tasks to do after a successful connection.
        exit: function(){
            if ( this.options.onSuccess ) {
                if ( 'function' == typeof this.options.onSuccess ) {
                    this.options.onSuccess( this );
                }
            } else {
                window.location.reload();
            }
        },
        
        // Initialization - mostly binding events.
        init: function(){
            
            var that = this;
            
            // Close the modal.
            $( document ).on( 'click', '#gadsense-modal .dashicons-dismiss', function(){
                $( '#mapi-code' ).val( '' );
                $( '#gadsense-modal' ).css( 'display', 'none' );
            } );
            
            // Confirm code for account connection.
            $( document ).on( 'click', '#mapi-confirm-code', function(){
                
                var code = $( '#mapi-code' ).val();
                if ( '' == code ) return;
                $( '.gadsense-overlay' ).css( 'display', 'block' );
                var data = {
                    action: 'advads_gadsense_mapi_confirm_code',
                    code: code,
                    nonce: AdsenseMAPI.nonce,
                };
                
                $.ajax({
                    url: ajaxurl,
                    type: 'post',
                    data: data,
                    success:function(response, status, XHR){
                        if ( null !== that.AUTH_WINDOW ) {
                            that.AUTH_WINDOW.close();
                        }
                        $( '#mapi-code' ).val( '' );
                        if ( response.status && true === response.status && response['token_data'] ) {
                            that.getAccountDetails( response['token_data'] );
                        } else {
                            /**
                             * Connection error handling.
                             */
                            console.log( response );
                            $( '.gadsense-overlay' ).css( 'display', 'none' );
                            $( '#mapi-code' ).val( '' );
                            $( '#mapi-autoads' ).prop( 'checked', false );
                            $( '#gadsense-modal-content-inner .dashicons-dismiss' ).trigger( 'click' );
                        }
                    },
                    error:function(request, status, error){
                        $( '#gadsense-loading-overlay' ).css( 'display', 'none' );
                    },
                });
                
            } );
            
            // Account selection
            $( document ).on( 'click', '.gadsense-modal-content-inner[data-content="account-selector"] button', function( ev ) {
                var adsenseID = $( '#mapi-select-account' ).val();
                var tokenData = false;
                var tokenString = $( '.gadsense-modal-content-inner[data-content="account-selector"] input.token-data' ).val();
                var details = false;
                var detailsString = $( '.gadsense-modal-content-inner[data-content="account-selector"] input.accounts-details' ).val();
                
                try {
                    tokenData = JSON.parse( tokenString );
                } catch ( Ex ) {
                    console.error( 'Bad token data : ' + tokenString );
                }
                try {
                    details = JSON.parse( detailsString );
                } catch ( Ex ) {
                    console.error( 'Bad account details : ' + detailsString );
                }
                if ( details && JSON ) {
                    $( '.gadsense-overlay' ).css( 'display', 'block' );
                    var data = {
                        action: 'advads_gadsense_mapi_select_account',
                        nonce: AdsenseMAPI.nonce,
                        account : details[ adsenseID ],
                        'token_data': tokenData,
                        autoads: $( '#mapi-autoads' ).prop( 'checked' ),
                    };
                    
                    $.ajax({
                        url: ajaxurl,
                        type: 'post',
                        data: data,
                        success:function(response, status, XHR){
                            if ( response.status && true === response.status ) {
                                INSTANCE.exit();
                            } else {
                                console.log( response );
                            }
                        },
                        error:function(request, status, error){
                            $( '#gadsense-loading-overlay' ).css( 'display', 'none' );
                        },
                    });
                }
                
            } );
            
        },
        
        // Get account info based on a newly obtained token.
        getAccountDetails: function( tokenData ){
            var data = {
                action: 'advads_gadsense_mapi_get_details',
                nonce: AdsenseMAPI.nonce,
            };
            data['token_data'] = tokenData;
            if ( $( '#mapi-autoads' ).prop( 'checked' ) ) {
                data['autoads'] = true;
            }
            
            $.ajax({
                url: ajaxurl,
                type: 'post',
                data: data,
                success:function(response, status, XHR){
                    if ( response.status && true === response.status ) {
                        if ( response['adsense_id'] ) {
                            INSTANCE.exit();
                        } else if ( response['token_data'] ) {
                            INSTANCE.switchContent( 'account-selector' );
                            INSTANCE.frame.find( 'select' ).html( response.html );
                            INSTANCE.frame.find( 'input.token-data' ).val( JSON.stringify( response['token_data'] ) );
                            INSTANCE.frame.find( 'input.accounts-details' ).val( JSON.stringify( response['details'] ) );
                        } else {
                            INSTANCE.switchContent( 'error' );
                            INSTANCE.frame.find( '.error-message' ).text( JSON.stringify( response ) );
                        }
                    } else {
                        if ( response['raw']['errors'][0]['message'] ) {
                            INSTANCE.switchContent( 'error' );
                            INSTANCE.frame.find( '.error-message' ).text( response['raw']['errors'][0]['message'] );
                            if ( 'undefined' != typeof AdsenseMAPI.connectErrorMsg[response['raw']['errors'][0]['reason']] ) {
                                INSTANCE.frame.find( '.error-description' ).html( AdsenseMAPI.connectErrorMsg[response['raw']['errors'][0]['reason']] );
                            } else {
                                INSTANCE.frame.find( '.error-message' ).append( '&nbsp;<code>(' + response['raw']['errors'][0]['reason'] + ')</code>' );
                            }
                        } else if ( response['raw']['message'] ) {
                            INSTANCE.frame.find( '.error-message' ).text( response['raw']['errors'][0]['message'] );
                        }
                    }
                },
                error:function(request, status, error){
                    $( '#gadsense-loading-overlay' ).css( 'display', 'none' );
                },
            });
            
        },
        
        // Switch between frames in the modal container.
        switchContent: function( content ) {
            if ( this.modal.find( '.gadsense-modal-content-inner[data-content="' + content + '"]' ).length ) {
                this.modal.find( '.gadsense-modal-content-inner' ).css( 'display', 'none' );
                this.frame = this.modal.find( '.gadsense-modal-content-inner[data-content="' + content + '"]' );
                this.frame.css( 'display', 'block' );
                $( '.gadsense-overlay' ).css( 'display', 'none' );
            }
        },
        
        // Show the modal frame with a given content.
        show: function( content ) {
            if ( 'undefined' == typeof content ) {
                content = 'confirm-code';
            }
            this.switchContent( content );
            
            if ( 'confirm-code' == content ) {
                
                // Open Google authentication in a child window.
                this.modal.css( 'display', 'block' );
                
                var oW = $( window ).width(),
                oH = $( window ).height(),
                w = Math.min( oW, oH ) * 0.8,
                h = Math.min( oW, oH ) * 0.8,
                l = (oW - w) / 2,
                t = (oH - h) / 2,
                
                args = 'resize=1,titlebar=1,width=' + w + ',height=' + h + ',left=' + l + ',top=' + t;
                this.AUTH_WINDOW = window.open( AdsenseMAPI.oAuth2, 'advadsOAuth2', args );
                
            }
        },
        
        // Hide the modal frame
        hide: function(){
            this.switchContent( 'confirm-code' );
            this.modal.css( 'display', 'none' );
        },
        
    };
    
    window.advadsMapiConnectClass = advadsMapiConnectClass;
    
    // Shortcut function.
    window.advadsMapiConnect = function( content, options ) {
        if ( 'undefined' == typeof content || ! content ) {
            content = 'confirm-code';
        }
        if ( 'undefined' == typeof options ) {
            options = {};
        }
        if ( null === INSTANCE ) {
            INSTANCE = new advadsMapiConnectClass( content, options );
        } else {
            INSTANCE.show( content, options );
        }
    }
    
    $(function(){
        // Move the the pop-up outside of any form.
        $( '#wpwrap' ).append( $( '#gadsense-modal' ) );
    });
    
})(window.jQuery);