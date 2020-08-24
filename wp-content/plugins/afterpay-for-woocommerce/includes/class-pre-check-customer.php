<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Runs PreCheckCustomer for AfterPay payment methods
 *
 * @class    WC_AfterPay_Pre_Check_Customer
 * @version  1.0.0
 * @package  WC_Gateway_AfterPay/Classes
 * @category Class
 * @author   Krokedil
 */
class WC_AfterPay_Pre_Check_Customer {

	/** @var bool */
	private $testmode = false;

	/**
	 * WC_AfterPay_Pre_Check_Customer constructor.
	 */
	public function __construct() {
		$afterpay_settings = get_option( 'woocommerce_afterpay_invoice_settings' );
		$this->testmode    = 'yes' == $afterpay_settings['testmode'] ? true : false;

		// Enqueue JS file
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Register AJAX callback
		add_action( 'wp_ajax_afterpay_pre_check_customer', array( $this, 'pre_check_customer' ) );
		add_action( 'wp_ajax_nopriv_afterpay_pre_check_customer', array( $this, 'pre_check_customer' ) );

		add_action( 'woocommerce_before_checkout_billing_form', array( $this, 'display_pre_check_form' ) );
		add_action( 'woocommerce_checkout_init', array( $this, 'maybe_pre_check_customer' ) );

		// Check if PreCheckCustomer was performed and successful
		add_action( 'woocommerce_before_checkout_process', array( $this, 'confirm_pre_check_customer' ) );

		// Filter checkout billing fields
		add_filter( 'woocommerce_process_checkout_field_billing_first_name', array(
			$this,
			'filter_pre_checked_value'
		) );
		add_filter( 'woocommerce_process_checkout_field_billing_last_name', array(
			$this,
			'filter_pre_checked_value'
		) );
		add_filter( 'woocommerce_process_checkout_field_billing_address_1', array(
			$this,
			'filter_pre_checked_value'
		) );
		add_filter( 'woocommerce_process_checkout_field_billing_address_2', array(
			$this,
			'filter_pre_checked_value'
		) );
		add_filter( 'woocommerce_process_checkout_field_billing_postcode', array( $this, 'filter_pre_checked_value' ) );
		add_filter( 'woocommerce_process_checkout_field_billing_city', array( $this, 'filter_pre_checked_value' ) );
		add_filter( 'woocommerce_process_checkout_field_billing_company', array(
			$this,
			'filter_pre_checked_value'
		) );
		// Filter checkout shipping fields
		add_filter( 'woocommerce_process_checkout_field_shipping_first_name', array(
			$this,
			'filter_pre_checked_value'
		) );
		add_filter( 'woocommerce_process_checkout_field_shipping_last_name', array(
			$this,
			'filter_pre_checked_value'
		) );
		add_filter( 'woocommerce_process_checkout_field_shipping_address_1', array(
			$this,
			'filter_pre_checked_value'
		) );
		add_filter( 'woocommerce_process_checkout_field_shipping_address_2', array(
			$this,
			'filter_pre_checked_value'
		) );
		add_filter( 'woocommerce_process_checkout_field_shipping_postcode', array(
			$this,
			'filter_pre_checked_value'
		) );
		add_filter( 'woocommerce_process_checkout_field_shipping_city', array(
            $this,
            'filter_pre_checked_value'
        ) );
		add_filter( 'woocommerce_process_checkout_field_shipping_company', array(
			$this,
			'filter_pre_checked_value'
		) );
	}

	/**
	 * Run PreCheckCustomer on checkout load if we have a personal number and an AfterPay method is selected
	 */
	public function maybe_pre_check_customer() {
		
		WC()->cart->calculate_totals();
		if ( WC()->session->get( 'afterpay_cart_total' ) == WC()->cart->total ) {
			return;
		}
		
		// Only perform PreCheckCustomer on checkout_init for Swedish customers
		if( 'SE' != WC()->customer->get_country() ) {
			return;
		}

		$chosen_payment_method = WC()->session->chosen_payment_method;
		if ( strpos( $chosen_payment_method, 'afterpay' ) !== false ) {
			$personal_no = '';

			if ( WC()->session->get( 'afterpay_personal_no' ) ) {
				$personal_no = WC()->session->get( 'afterpay_personal_no' );
			} else if ( is_user_logged_in() ) {
				$user = wp_get_current_user();
				if ( get_user_meta( $user->ID, '_afterpay_personal_no', true ) ) {
					$personal_no = get_user_meta( $user->ID, '_afterpay_personal_no', true );
				}
			}
			
			if ( '' != $personal_no ) {
				$this->pre_check_customer_request( $personal_no, $chosen_payment_method, 'Person', WC()->customer->get_country() );
			}
		}
	}

	/**
	 * Check if customer has used PreCheckCustomer and received a positive response (if AfterPay method is selected)
	 */
	public function confirm_pre_check_customer() {
		$chosen_payment_method = WC()->session->chosen_payment_method;
		if ( strpos( $chosen_payment_method, 'afterpay' ) !== false ) {
			// Check if personal/organization number field is empty
			if ( empty( $_POST['afterpay-pre-check-customer-number'] ) ) {
				wc_add_notice( __( 'Personal/organization number is a required field.', 'woocommerce-gateway-afterpay' ), 'error' );
			} // Check if PreCheckCustomer was performed
			elseif ( ! WC()->session->get( 'afterpay_allowed_payment_methods' ) && 'SE' == WC()->customer->get_country() ) {
				wc_add_notice( __( 'Please use get address feature first, before using one of AfterPay payment methods.', 'woocommerce-gateway-afterpay' ), 'error' );
			}
		}
	}

	/**
	 * Display AfterPay PreCheckCustomer fields
	 */
	public static function display_pre_check_form() {
		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();
			if ( get_user_meta( $user->ID, '_afterpay_personal_no', true ) ) {
				$personal_number = get_user_meta( $user->ID, '_afterpay_personal_no', true );
			}
		} else {
			$personal_number = WC()->session->get( 'afterpay_personal_no' ) ? WC()->session->get( 'afterpay_personal_no' ) : '';
		} ?>
		<div id="afterpay-pre-check-customer" style="display:none">
            <?php
                // Check settings for what customer type is wanted, and print the form according to that.
                $afterpay_settings = get_option( 'woocommerce_afterpay_invoice_settings' );
                $customer_type = $afterpay_settings['customer_type'];
                $separate_shipping_companies = $afterpay_settings['separate_shipping_companies'];
                if ( $customer_type === 'both' ) {
            ?>
                    <p>
                        <label for="klarna_invoice_pno"><?php _e( 'Personal/organization number', 'woocommerce-gateway-afterpay' ); ?> <span class="required">*</span></label><br/>
                        <input type="radio" class="input-radio" value="Person" name="afterpay_customer_category"
                               id="afterpay-customer-category-person" checked/>
                        <label for="afterpay-customer-category-person"><?php _e( 'Person', 'woocommerce-gateway-afterpay' ); ?></label>
                        <input type="hidden" id="separate_shipping_companies" value="<?php echo $separate_shipping_companies ?>">
                        <input type="radio" class="input-radio" value="Company" name="afterpay_customer_category"
                               id="afterpay-customer-category-company"/>
                        <label
                            for="afterpay-customer-category-company"><?php _e( 'Company', 'woocommerce-gateway-afterpay' ); ?></label>
                    </p>
                <?php } else if ( $customer_type === 'private' ) {?>
                    <p>
                        <label for="klarna_invoice_pno"><?php _e( 'Personal number', 'woocommerce-gateway-afterpay' ); ?> <span class="required">*</span></label><br/>
                        <input type="radio" value="Person" name="afterpay_customer_category"
                               id="afterpay-customer-category-person" checked style="display:none;"/>
                    </p>
                    <style> #billing_company_field{ display:none; } </style>
                <?php } else if ( $customer_type === 'company' ) { ?>
                    <p>
                        <label for="klarna_invoice_pno"><?php _e( 'Organization number', 'woocommerce-gateway-afterpay' ); ?> <span class="required">*</span></label><br/>
                        <input type="radio" value="Company" name="afterpay_customer_category"
                               id="afterpay-customer-category-company" checked style="display:none;"/>
                        <input type="hidden" id="separate_shipping_companies" value="<?php echo $separate_shipping_companies ?>">
                    </p>
                <?php } ?>
			<p class="form-row form-row-wide validate-required">
				<input type="text" name="afterpay-pre-check-customer-number" id="afterpay-pre-check-customer-number"
				       class="afterpay-pre-check-customer-number"
				       placeholder="<?php _e( 'YYMMDDNNNN', 'woocommerce-gateway-afterpay' ); ?>"
				       value="<?php echo $personal_number; ?>"/>
				<button type="button" style="margin-top:0.5em"
				        class="afterpay-get-address-button button"><?php _e( 'Get address', 'woocommerce-gateway-afterpay' ); ?></button>
			</p>
		</div>
		<?php
	}

	/**
	 * Load the JS file(s).
	 */
	public function enqueue_scripts() {
		wp_register_script( 'afterpay_pre_check_customer', plugins_url( 'assets/js/pre-check-customer.js', __DIR__ ), array( 'jquery' ), false, true );
		wp_localize_script( 'afterpay_pre_check_customer', 'WC_AfterPay', array(
			'ajaxurl'                           => admin_url( 'admin-ajax.php' ),
			'afterpay_pre_check_customer_nonce' => wp_create_nonce( 'afterpay_pre_check_customer_nonce' ),
		) );
		wp_enqueue_script( 'afterpay_pre_check_customer' );
	}

	/**
	 * AJAX PreCheckCustomer for AfterPay payment methods.
	 */
	public function pre_check_customer() {
		if ( ! wp_verify_nonce( $_REQUEST['nonce'], 'afterpay_pre_check_customer_nonce' ) ) {
			exit( 'Nonce can not be verified.' );
		}

		$data = array();

		$personal_number   = $_REQUEST['personal_number'];
		$payment_method    = $_REQUEST['payment_method'];
		$customer_category = $_REQUEST['customer_category'];
		$billing_country   = $_REQUEST['billing_country'];

		if ( $customer_category != 'Company' ) {
			$customer_category = 'Person';
		}

		$pre_check_customer_response = $this->pre_check_customer_request( $personal_number, $payment_method, $customer_category, $billing_country );
		$data['response']            = $pre_check_customer_response;

		if ( $pre_check_customer_response->IsSuccess ) {
			$data['message'] = __(
				'Address found and added to checkout form.',
				'woocommerce-gateway-afterpay'
			);
			wp_send_json_success( $data );
		} else {
			$data['message'] = __(
				'No address was found. Please check your personal number or choose another payment method.',
				'woocommerce-gateway-afterpay'
			);
			wp_send_json_error( $data );
		}

		wp_die();
	}

	/**
	 * Check billing fields against shipping fields for differences
	 *
	 * @param $order
	 *
	 * @return bool
	 */
	public function check_against_fields($order){
		$return = false;
	    if($order->shipping_address_1 != '' || $order->shipping_postcode != '' || $order->shipping_city != '' || $order->shipping_first_name != '' || $order->shipping_last_name != '' ){
	        if($order->billing_address_1 != $order->shipping_address_1){
	            $return = true;
            }
		    if($order->billing_postcode != $order->shipping_postcode){
			    $return = true;
		    }
		    if($order->billing_city != $order->shipping_city){
			    $return = true;
		    }
		    if($order->billing_first_name != $order->shipping_first_name){
			    $return = true;
		    }
		    if($order->billing_last_name != $order->shipping_last_name){
			    $return = true;
		    }
		    return $return;
        }else {
	        return $return;
        }
    }

	/**
	 * AfterPay PreCheckCustomer request
	 *
	 * @param $personal_number
	 * @param $payment_method
	 * @param string $customer_category
	 *
	 * @return bool
	 */
	public function pre_check_customer_request( $personal_number, $payment_method, $customer_category, $billing_country, $order = false ) {

		// Prepare order lines for AfterPay
		$order_lines_processor = new WC_AfterPay_Process_Order_Lines();
		$order_lines           = $order_lines_processor->get_order_lines();

		$payment_method_settings = get_option( 'woocommerce_' . $payment_method . '_settings' );
		

		// Live or test checkout endpoint, based on payment gateway settings
		$checkout_endpoint = $this->testmode ? ARVATO_CHECKOUT_TEST : ARVATO_CHECKOUT_LIVE;

		// PreCheckCustomer
		$soap_client = new SoapClient( $checkout_endpoint );
		$args        = array(
			'User'         => array(
				'ClientID' => $payment_method_settings['client_id_' . strtolower($billing_country)],
				'Username' => $payment_method_settings['username_' . strtolower($billing_country)],
				'Password' => $payment_method_settings['password_' . strtolower($billing_country)]
			),
			'Customer'     => array(
				'Address'                 => array(
					'CountryCode' 	=> $billing_country,
				),
				'CustomerCategory'        => $customer_category,
				'Organization_PersonalNo' => $personal_number,
			),
			'OrderDetails' => array(
				'Amount'            => WC()->cart->total,
				'CurrencyCode'      => get_woocommerce_currency(),
				'OrderChannelType'  => 'Internet',
				'OrderDeliveryType' => 'Normal',
				'OrderLines'        => $order_lines
			)
		);
		
		// Send customer address for all countries except Sweden
		if( 'SE' !== $billing_country && $order ) {
			$args['Customer']['Address']['Street'] = $order->billing_address_1;
			$args['Customer']['Address']['PostalCode'] = $order->billing_postcode;
			$args['Customer']['Address']['PostalPlace'] = $order->billing_city;
			
			$args['Customer']['Email'] = $order->billing_email;
			$args['Customer']['FirstName'] = $order->billing_first_name;
			$args['Customer']['LastName'] = $order->billing_last_name;
			$args['Customer']['MobilePhone'] = $order->billing_phone;
		}
		// Send delivery address if the option is selected and fields are different
		if( $order && $this->check_against_fields($order) ) {
			$args['DeliveryCustomer']['Address']['Street'] = $order->shipping_address_1;
			$args['DeliveryCustomer']['Address']['PostalCode'] = $order->shipping_postcode;
			$args['DeliveryCustomer']['Address']['PostalPlace'] = $order->shipping_city;
			$args['DeliveryCustomer']['Address']['CountryCode'] = $order->shipping_country;

			$args['DeliveryCustomer']['FirstName'] = $order->shipping_first_name;
			$args['DeliveryCustomer']['LastName'] = $order->shipping_last_name;
		}

		WC_Gateway_AfterPay_Factory::log( 'AfterPay PreCheckCustomer request: ' . var_export( $args, true ) );
		
		try {
			$response = $soap_client->PreCheckCustomer( $args );
			WC_Gateway_AfterPay_Factory::log( 'AfterPay PreCheckCustomer response: ' . var_export( $response, true ) );
			if ( $response->IsSuccess ) {
				// If only invoice is returned, response is an object, not a one element array
				if ( is_array( $response->AllowedPaymentMethods->AllowedPaymentMethod ) ) {
					$allowed_payment_methods = $response->AllowedPaymentMethods->AllowedPaymentMethod;
				} else {
					$allowed_payment_methods   = array();
					$allowed_payment_methods[] = $response->AllowedPaymentMethods->AllowedPaymentMethod;
				}

				// Customer information
				$afterpay_customer_details = array(
					'first_name' => $response->Customer->FirstName,
					'last_name'  => $response->Customer->LastName,
					'address_1'  => $response->Customer->AddressList->Address->Street,
					'address_2'  => $response->Customer->AddressList->Address->StreetNumber,
					'postcode'   => $response->Customer->AddressList->Address->PostalCode,
					'city'       => $response->Customer->AddressList->Address->PostalPlace,
				);
				error_log(var_export($response, true));
				// Set session data
				WC()->session->set( 'afterpay_checkout_id', $response->CheckoutID );
				WC()->session->set( 'afterpay_customer_no', $response->Customer->CustomerNo );
				WC()->session->set( 'afterpay_personal_no', $personal_number );
				WC()->session->set( 'afterpay_allowed_payment_methods', $allowed_payment_methods );
				WC()->session->set( 'afterpay_customer_details', $afterpay_customer_details );
				WC()->session->set( 'afterpay_cart_total', WC()->cart->total );
				
				// Capture user's personal number as meta field, if logged in and is from Sweden
				if ( is_user_logged_in() && 'SE' == $billing_country ) {
					$user = wp_get_current_user();
					add_user_meta( $user->ID, '_afterpay_personal_no', $personal_number, true );
				}

				// Send success
				return $response;
			} else {
				WC()->session->__unset( 'afterpay_checkout_id' );
				WC()->session->__unset( 'afterpay_customer_no' );
				WC()->session->__unset( 'afterpay_personal_no' );
				WC()->session->__unset( 'afterpay_allowed_payment_methods' );
				WC()->session->__unset( 'afterpay_customer_details' );
				WC()->session->__unset( 'afterpay_cart_total' );
			
				$error_message = WC_AfterPay_Error_Notice::get_error_message( $response->ResultCode, 'pre_check_customer' );
				return new WP_Error( 'failure', __( $error_message, 'woocommerce-gateway-afterpay' ) );
				
			}
		} catch ( Exception $e ) {
			WC_Gateway_AfterPay_Factory::log( $e->getMessage() );
			echo '<div class="woocommerce-error">';
			echo $e->getMessage();
			echo '</div>';
		}
	}

	/**
	 * Filter checkout fields so they use data retrieved by PreCheckCustomer
	 *
	 * @param $value
	 *
	 * @return mixed
	 */
	public function filter_pre_checked_value( $value ) {
	    // Only do this for AfterPay methods
		$chosen_payment_method = WC()->session->get( 'chosen_payment_method' );
		if ( strpos( $chosen_payment_method, 'afterpay' ) !== false ) {
			$current_filter = current_filter();
			$current_field  = str_replace( array(
				'woocommerce_process_checkout_field_billing_',
				'woocommerce_process_checkout_field_shipping_'
			), '', $current_filter );

			if ( strpos($value, '**') !== false ) {
                $customer_details = WC()->session->get( 'afterpay_customer_details' );
                if ($current_field === 'company'){
	                return $customer_details[ 'last_name' ];
				} elseif ( isset( $customer_details[ $current_field ] ) && '' != $customer_details[ $current_field ] ) {
                    return $customer_details[ $current_field ];
                } else {
                    return $value;
                }
            } else {
                return $value;
            }
		}
		return $value;
	}
}

$wc_afterpay_pre_check_customer = new WC_AfterPay_Pre_Check_Customer();