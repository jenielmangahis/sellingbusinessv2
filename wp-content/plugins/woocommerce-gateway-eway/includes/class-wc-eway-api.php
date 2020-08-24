<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_EWAY_API' ) ) {
	class WC_EWAY_API {

		const PRODUCTION_ENDPOINT = 'https://api.ewaypayments.com';

		const TEST_ENDPOINT = 'https://api.sandbox.ewaypayments.com';

		public $endpoint;

		public $api_key;

		public $api_password;

		public $debug_mode;

		public function __construct( $api_key, $api_password, $environment, $debug_mode ) {
			$this->api_key      = $api_key;
			$this->api_password = $api_password;
			$this->endpoint     = ( 'production' === $environment ) ? WC_EWAY_API::PRODUCTION_ENDPOINT : WC_EWAY_API::TEST_ENDPOINT;
			$this->debug_mode   = $debug_mode;
		}

		private function perform_request( $endpoint, $json ) {
			$args = array(
				'timeout'     => apply_filters( 'wc_eway_api_timeout', 45 ), // default to 45 seconds
				'redirection' => 0,
				'httpversion' => '1.0',
				'sslverify'   => false,
				'blocking'    => true,
				'headers'     => array(
					'accept'        => 'application/json',
					'content-type'  => 'application/json',
					'authorization' => 'Basic ' . base64_encode( $this->api_key . ':' . $this->api_password ),
				),
				'body'        => $json,
				'cookies'     => array(),
				'user-agent'  => 'PHP ' . PHP_VERSION . '/WooCommerce ' . get_option( 'woocommerce_db_version' ),
			);

			$this->debug_message( json_decode( $json ) );

			$response = wp_remote_post( $this->endpoint . $endpoint, $args );

			$this->debug_message( $response );

			if ( is_wp_error( $response ) ) {
				throw new Exception( $response->get_error_message() );
			}

			if ( 200 !== $response['response']['code'] ) {
				throw new Exception( $response['response']['message'] );
			}

			$this->debug_message( json_decode( $response['body'] ) );

			return $response['body'];
		}

		private function perform_get_request( $endpoint ) {
			$args = array(
				'timeout'     => apply_filters( 'wc_eway_api_timeout', 45 ), // default to 45 seconds
				'redirection' => 0,
				'httpversion' => '1.0',
				'sslverify'   => false,
				'blocking'    => true,
				'headers'     => array(
					'authorization' => 'Basic ' . base64_encode( $this->api_key . ':' . $this->api_password ),
				),
				'cookies'     => array(),
				'user-agent'  => 'PHP ' . PHP_VERSION . '/WooCommerce ' . get_option( 'woocommerce_db_version' ),
			);

			$response = wp_remote_get( $this->endpoint . $endpoint, $args );

			$this->debug_message( $response );

			if ( is_wp_error( $response ) ) {
				throw new Exception( $response->get_error_message() );
			}

			if ( 200 !== $response['response']['code'] ) {
				throw new Exception( $response['response']['message'] );
			}

			$this->debug_message( json_decode( $response['body'] ) );

			return $response['body'];
		}

		/**
		 * Request an access code for use in an eWAY Transparent Redirect payment
		 * See: https://eway.io/api-v3/#transparent-redirect
		 *
		 * @param WC_Order $order
		 * @param string   $method       The "Method" parameter, see: https://eway.io/api-v3/#payment-methods
		 * @param string   $trx_type     The "TransactionType" parameter, see: https://eway.io/api-v3/#transaction-types
		 * @param mixed    $order_total  The amount to charge for this transaction
		 *
		 * @return mixed     JSON response from /CreateAccessCode.json on success
		 * @throws Exception Thrown on failure
		 */
		public function request_access_code( $order, $method = 'ProcessPayment', $trx_type = 'Purchase', $order_total = null ) {
			$pre_wc_30 = version_compare( WC_VERSION, '3.0', '<' );
			$order_id  = $pre_wc_30 ? $order->id : $order->get_id();
			$order_key = $pre_wc_30 ? $order->order_key : $order->get_order_key();

			$customer_ip = get_post_meta( $order_id, '_customer_ip_address', true );

			// If an order total isn't provided (in the case of a subscription), grab it from the Order itself
			if ( is_null( $order_total ) ) {
				$order_total = $order->get_total() * 100.00;
			}

			// set up request object
			$request = array(
				'Method'          => $method,
				'TransactionType' => $trx_type,
				'RedirectUrl'     => str_replace(
					'https:', 'http:', add_query_arg(
						array(
							'wc-api'    => 'WC_Gateway_EWAY',
							'order_id'  => $order_id,
							'order_key' => $order_key,
							'sig_key'   => md5( $order_key . 'WOO' . $order_id ),
						), home_url( '/' )
					)
				),
				'CustomerIP'      => $customer_ip,
				'DeviceID'        => '0b38ae7c3c5b466f8b234a8955f62bdd',
				'PartnerID'       => '0b38ae7c3c5b466f8b234a8955f62bdd',
				'Payment'         => array(
					'TotalAmount'        => $order_total,
					'CurrencyCode'       => $pre_wc_30 ? $order->get_order_currency() : $order->get_currency(),
					'InvoiceDescription' => apply_filters( 'woocommerce_eway_description', '', $order ),
					'InvoiceNumber'      => ltrim( $order->get_order_number(), _x( '#', 'hash before order number', 'woocommerce' ) ),
					'InvoiceReference'   => $order_id,
				),
				'Customer'        => array(
					'FirstName'   => $pre_wc_30 ? $order->billing_first_name : $order->get_billing_first_name(),
					'LastName'    => $pre_wc_30 ? $order->billing_last_name : $order->get_billing_last_name(),
					'CompanyName' => substr( $pre_wc_30 ? $order->billing_company : $order->get_billing_company(), 0, 50 ),
					'Street1'     => $pre_wc_30 ? $order->billing_address_1 : $order->get_billing_address_1(),
					'Street2'     => $pre_wc_30 ? $order->billing_address_2 : $order->get_billing_address_2(),
					'City'        => $pre_wc_30 ? $order->billing_city : $order->get_billing_city(),
					'State'       => $pre_wc_30 ? $order->billing_state : $order->get_billing_state(),
					'PostalCode'  => $pre_wc_30 ? $order->billing_postcode : $order->get_billing_postcode(),
					'Country'     => strtolower( $pre_wc_30 ? $order->billing_country : $order->get_billing_country() ),
					'Email'       => $pre_wc_30 ? $order->billing_email : $order->get_billing_email(),
					'Phone'       => $pre_wc_30 ? $order->billing_phone : $order->get_billing_phone(),
				),
			);

			// Add customer ID if logged in
			if ( is_user_logged_in() ) {
				$request['Options'][] = array( 'customerID' => get_current_user_id() );
			}
			return $this->perform_request( '/CreateAccessCode.json', json_encode( $request ) );
		}

		public function get_access_code_result( $access_code ) {
			$request = array(
				'AccessCode' => $access_code,
			);

			return $this->perform_request( '/GetAccessCodeResult.json', json_encode( $request ) );
		}

		public function direct_payment( $order, $token_customer_id, $amount = 0 ) {
			$pre_wc_30 = version_compare( WC_VERSION, '3.0', '<' );
			$order_id  = $pre_wc_30 ? $order->id : $order->get_id();
			$order_key = $pre_wc_30 ? $order->order_key : $order->get_order_key();
			$amount    = intval( $amount );
			$customer_ip = get_post_meta( $order_id, '_customer_ip_address', true );

			// Check for 0 value order
			if ( 0 === $amount ) {
				$return_object = array(
					'Payment'         => array(
						'InvoiceReference' => $order_id,
					),
					'ResponseMessage' => 'A2000',
					'TransactionID'   => '',
				);
				return json_encode( $return_object );
			}
			$request = array(
				'DeviceID'        => '0b38ae7c3c5b466f8b234a8955f62bdd',
				'PartnerID'       => '0b38ae7c3c5b466f8b234a8955f62bdd',
				'TransactionType' => 'Recurring',
				'Method'          => 'TokenPayment',
				'CustomerIP'      => $customer_ip,
				'Customer'        => array(
					'TokenCustomerID' => $token_customer_id,
				),
				'Payment'         => array(
					'TotalAmount'        => $amount,
					'CurrencyCode'       => get_woocommerce_currency(),
					'InvoiceDescription' => apply_filters( 'woocommerce_eway_description', '', $order ),
					'InvoiceNumber'      => ltrim( $order->get_order_number(), _x( '#', 'hash before order number', 'woocommerce' ) ),
					'InvoiceReference'   => $order_id,
				),
				'Options'         => array(
					array( 'OrderID' => $order_id ),
					array( 'OrderKey' => $order_key ),
					array( 'SigKey' => md5( $order_key . 'WOO' . $order_id ) ),
				),
			);
			return $this->perform_request( '/DirectPayment.json', json_encode( $request ) );
		}

		public function direct_refund( $order, $transaction_id, $amount = 0, $reason = '' ) {
			$request = array(
				'DeviceID'  => '0b38ae7c3c5b466f8b234a8955f62bdd',
				'PartnerID' => '0b38ae7c3c5b466f8b234a8955f62bdd',
				'Refund'    => array(
					'TotalAmount'        => $amount,
					'TransactionID'      => $transaction_id,
					'InvoiceNumber'      => ltrim( $order->get_order_number(), _x( '#', 'hash before order number', 'woocommerce' ) ),
					'InvoiceReference'   => version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id(),
					'InvoiceDescription' => $reason,
				),
			);
			return $this->perform_request( '/DirectRefund.json', json_encode( $request ) );
		}

		public function debug_message( $message ) {
			if ( 'on' === $this->debug_mode ) {
				if ( is_array( $message ) || is_object( $message ) ) {
					$message = print_r( $message, true );
				}

				error_log( $message );

				if ( function_exists( 'wc_add_notice' ) ) {
					wc_add_notice( $message );
				}
			}
		}

		public function lookup_customer( $token_customer_id ) {
			return $this->perform_get_request( '/Customer/' . $token_customer_id );
		}
	}
}
