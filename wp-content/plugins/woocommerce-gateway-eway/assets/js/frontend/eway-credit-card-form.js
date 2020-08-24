jQuery(
	function( $ ) {

			$( 'input.wc-credit-card-form-card-number' ).payment( 'formatCardNumber' );
			$( 'input.wc-credit-card-form-card-expiry' ).payment( 'formatCardExpiry' );
			$( 'input.wc-credit-card-form-card-cvc' ).payment( 'formatCardCVC' );

			$( '#eway_credit_card_form' ).submit(
				function( e, options ) {

					var formErrorElement = $( this ).find( '.woocommerce-error' );
					formErrorElement.hide();
					formErrorElement.html( '' );

					options = options || {};
					if ( 'undefined' !== typeof options.readyToSubmit && true === options.readyToSubmit ) {
						// Confirm that the Form is ready for submission
						/// Card details are in the hidden fields
						expiryMonthReady = false != $( '#EWAY_CARDEXPIRYMONTH' ).val();
						expiryYearReady  = false != $( '#EWAY_CARDEXPIRYYEAR' ).val();
						cardNumberReady  = false != $( '#EWAY_CARDNUMBER' ).val();
						cvnReady         = false != $( '#EWAY_CARDCVN' ).val();
						formUrlReady     = false != $( e.currentTarget ).attr( 'action' );

						// this means that the verification has succeeded and we may proceed
						if ( expiryMonthReady && expiryYearReady && cardNumberReady && cvnReady && formUrlReady ) {
							return true;
						} else {
							// something not quite right
							e.preventDefault();
							$( this ).find( 'input[type="submit"]' ).removeAttr( 'disabled' );
							$( this ).css( { opacity: 1 } );

							// show user error
							formErrorElement.show();
							formErrorElement.html( '<li>' + eway_settings.formErrors.anotherPaymentMethod + '</li>' );
							return false;
						}

					}

					e.preventDefault();

					// Clear validation classes
					$( '#EWAY_TEMPCARDNUMBER' ).parent().removeClass( 'validate-required' ).removeClass( 'woocommerce-invalid' );
					$( '#EWAY_EXPIRY' ).parent().removeClass( 'validate-required' ).removeClass( 'woocommerce-invalid' );
					$( '#EWAY_CARDCVN' ).parent().removeClass( 'validate-required' ).removeClass( 'woocommerce-invalid' );

					// Validation
					if ( ! $.payment.validateCardNumber( $( '#EWAY_TEMPCARDNUMBER' ).val() ) ) {
						$( '#EWAY_TEMPCARDNUMBER' ).parent().addClass( 'validate-required' ).addClass( 'woocommerce-invalid' );
						return false;
					}

					// Card Type
					if ( '' === eway_settings.card_types ) {
						return false;
					}

					var card_type       = $.payment.cardType( $( '#EWAY_TEMPCARDNUMBER' ).val() );
					var found_card_type = false;
					for ( var i = 0; i < eway_settings.card_types.length; i++ ) {
						if ( card_type == eway_settings.card_types[i] ) {
							found_card_type = true;
						}
					}
					if ( ! found_card_type ) {
						$( '#EWAY_TEMPCARDNUMBER' ).parent().addClass( 'validate-required' ).addClass( 'woocommerce-invalid' );
						return false;
					}

					var expiry = $( '#EWAY_EXPIRY' ).payment( 'cardExpiryVal' );
					if ( ! $.payment.validateCardExpiry( expiry.month, expiry.year ) ) {
						$( '#EWAY_EXPIRY' ).parent().addClass( 'validate-required' ).addClass( 'woocommerce-invalid' );
						return false;
					}
					if ( ! $.payment.validateCardCVC( $( '#EWAY_CARDCVN' ).val() ) ) {
						$( '#EWAY_CARDCVN' ).parent().addClass( 'validate-required' ).addClass( 'woocommerce-invalid' );
						return false;
					}
					$( '#EWAY_CARDEXPIRYMONTH' ).val( expiry.month );
					$( '#EWAY_CARDEXPIRYYEAR' ).val( expiry.year );
					$( '#EWAY_CARDNUMBER' ).val( $( '#EWAY_TEMPCARDNUMBER' ).val().replace( / /g,'' ) );

					//Set the form URL
					eWayApiFormUrl = $( '#EWAY_SUBMIT_URL' ).val();
					$( e.currentTarget ).attr( 'action', eWayApiFormUrl );

					// Disable the submit button after clicking and validation passes
					$( this ).find( 'input[type="submit"]' ).attr( 'disabled', 'disabled' );
					$( this ).css( { opacity: 0.5 } );

					// Trigger submit again after all is verified and the form url has been updated.
					$( e.currentTarget ).trigger( 'submit', { 'readyToSubmit': true } );
				}
			);
	}
);
