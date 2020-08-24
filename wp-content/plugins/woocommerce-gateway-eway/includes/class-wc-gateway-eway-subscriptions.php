<?php
/**
 * WC_Gateway_EWAY_Subscriptions class.
 *
 * @extends WC_Gateway_EWAY
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'WC_Gateway_EWAY_Subscriptions' ) ) {
	class WC_Gateway_EWAY_Subscriptions extends WC_Gateway_EWAY {

		function __construct() {

			parent::__construct();

			add_action( 'woocommerce_scheduled_subscription_payment_' . $this->id, array( $this, 'scheduled_subscription_payment' ), 10, 2 );
			add_action( 'woocommerce_subscription_failing_payment_method_updated_' . $this->id, array( $this, 'update_failing_payment_method' ), 10, 2 );

			// display the current payment method used for a subscription in the "My Subscriptions" table
			add_filter( 'woocommerce_my_subscriptions_payment_method', array( $this, 'maybe_render_subscription_payment_method' ), 10, 2 );

			// allow store managers to manually set eWAY as the payment method on a subscription
			add_filter( 'woocommerce_subscription_payment_meta', array( $this, 'add_subscription_payment_meta' ), 10, 2 );
			add_filter( 'woocommerce_subscription_validate_payment_meta', array( $this, 'validate_subscription_payment_meta' ), 10, 2 );

		}

		/**
		 * Include the payment meta data required to process automatic recurring payments so that store managers can
		 * manually set up automatic recurring payments for a customer via the Edit Subscriptions screen in 2.0+.
		 *
		 * @param array $payment_meta associative array of meta data required for automatic payments
		 * @param WC_Subscription $subscription An instance of a subscription object
		 * @return array
		 */
		public function add_subscription_payment_meta( $payment_meta, $subscription ) {

			$payment_meta[ $this->id ] = array(
				'post_meta' => array(
					'_eway_token_customer_id' => array(
						'value' => get_post_meta( $subscription->id, '_eway_token_customer_id', true ),
						'label' => 'eWAY Token Customer ID',
					),
				),
			);

			return $payment_meta;
		}

		/**
		 * Returns the WC_Subscription(s) tied to a WC_Order, or a boolean false.
		 *
		 * @param  WC_Order $order
		 * @return bool|WC_Subscription
		 */
		protected function get_subscriptions_from_order( $order ) {

			if ( $this->order_contains_subscription( $order ) ) {

				$subscriptions = wcs_get_subscriptions_for_order( $order );

				if ( $subscriptions ) {

					return $subscriptions;

				}
			}

			return false;

		}

		/**
		 * Get the token customer id for an order
		 *
		 * @param WC_Order $order
		 * @return array|mixed
		 */
		protected function get_token_customer_id( $order ) {

			$subscriptions = $this->get_subscriptions_from_order( $order );

			if ( $subscriptions ) {

				$subscription = array_shift( $subscriptions );

				return get_post_meta( $subscription->id, '_eway_token_customer_id', true );

			}

			return parent::get_token_customer_id( $order );

		}

		/**
		 * Render the payment method used for a subscription in the "My Subscriptions" table
		 *
		 * @param string $payment_method_to_display the default payment method text to display
		 * @param object $subscription_details the subscription details
		 * @return string the subscription payment method
		 */
		public function maybe_render_subscription_payment_method( $payment_method_to_display, $subscription ) {

			// bail for other payment methods
			if ( ! is_a( $subscription->payment_gateway, get_class() ) || ! $subscription->customer_user ) {
				return $payment_method_to_display;
			}

			$order_token_id = get_post_meta( $subscription->id, '_eway_token_customer_id', true );
			$eway_cards     = get_user_meta( $subscription->customer_user, '_eway_token_cards', true );

			if ( $eway_cards && ! empty( $eway_cards ) ) {
				foreach ( $eway_cards as $card ) {
					if ( $card['id'] == $order_token_id ) {
						$payment_method_to_display = sprintf( __( 'Via card %s', 'wc-eway' ), $card['number'] );
						break;
					}
				}
			}

			return $payment_method_to_display;
		}

		/**
		 * Check if order contains subscriptions.
		 *
		 * @param  WC_Order $order_id
		 * @return bool
		 */
		protected function order_contains_subscription( $order ) {
			return function_exists( 'wcs_order_contains_subscription' ) && ( wcs_order_contains_subscription( $order ) );
		}


		/**
		 * process_subscription_payment function.
		 *
		 * @access public
		 * @param mixed $order
		 * @param int $amount (default: 0)
		 * @return void
		 */
		function process_subscription_payment( $order = '', $amount = 0 ) {
			$eway_token_customer_id = $this->get_token_customer_id( $order );

			if ( ! $eway_token_customer_id ) {
				return new WP_Error( 'eway_error', __( 'Token Customer ID not found', 'wc-eway' ) );
			}

			// Charge the customer
			try {
				return $this->process_payment_request( $order, $amount, $eway_token_customer_id );
			} catch ( Exception $e ) {
				return new WP_Error( 'eway_error', $e->getMessage() );
			}
		}

		/**
		 * API call to get eWay access call
		 *
		 * @param $order
		 *
		 * @return array|mixed|object
		 * @throws Exception
		 */
		protected function request_access_code( $order ) {

			// Check if order is for a subscription, if it is check for fee and charge that
			if ( $this->order_contains_subscription( $order ) || $this->is_subscription( $order ) ) {

				$method = 'TokenPayment';

				if ( 0 == $order->get_total() ) {
					$method = 'CreateTokenCustomer';
				}

				$order_total = $order->get_total() * 100;

				$result = json_decode( $this->get_api()->request_access_code( $order, $method, 'Recurring', $order_total ) );

				if ( isset( $result->Errors ) && ! is_null( $result->Errors ) ) {
					throw new Exception( $this->response_message_lookup( $result->Errors ) );
				}

				return $result;

			} else {

				return parent::request_access_code( $order );

			}

		}

		/**
		 * Wrapper for WooCommerce subscription function wc_is_subscription
		 *
		 * @param WC_Order
		 * @return bool
		 */
		public function is_subscription( $order ) {

			if ( function_exists( 'wcs_is_subscription' ) ) {
				return wcs_is_subscription( $order );
			} else {
				return false;
			}

		}

		/**
		 * scheduled_subscription_payment function.
		 *
		 * @param $amount_to_charge float The amount to charge.
		 * @param $order WC_Order The WC_Order object of the order which the subscription was purchased in.
		 * @access public
		 * @return void
		 */
		function scheduled_subscription_payment( $amount_to_charge, $order ) {

			$result = $this->process_subscription_payment( $order, $amount_to_charge );

			if ( is_wp_error( $result ) ) {

				$order->add_order_note( sprintf( __( 'eWAY subscription renewal failed - %s', 'wc-eway' ), $this->response_message_lookup( $result->get_error_message() ) ) );

			}

		}

		/**
		 * Save the token customer id on the subscription(s) being made
		 *
		 * @param  WC_Order $order
		 * @param  int      $token_customer_id
		 */
		protected function set_token_customer_id( $order, $token_customer_id ) {

			$subscriptions = $this->get_subscriptions_from_order( $order );

			if ( $subscriptions ) {

				foreach ( $subscriptions as $subscription ) {

					parent::set_token_customer_id( $subscription, $token_customer_id );

				}
			}

			parent::set_token_customer_id( $order, $token_customer_id );

		}

		/**
		 * Update the customer_id for a subscription after using eWAY to complete a payment to make up for
		 * an automatic renewal payment which previously failed.
		 *
		 * @param WC_Subscription $subscription The subscription for which the failing payment method relates.
		 * @param WC_Order $renewal_order The order which recorded the successful payment (to make up for the failed automatic payment).
		 */
		public function update_failing_payment_method( $subscription, $renewal_order ) {
			$renewal_order_id = version_compare( WC_VERSION, '3.0', '<' ) ? $renewal_order->id : $renewal_order->get_id();
			update_post_meta( $subscription->id, '_eway_token_customer_id', get_post_meta( $renewal_order_id, '_eway_token_customer_id', true ) );

		}

		/**
		 * Validate the payment meta data required to process automatic recurring payments so that store managers can
		 * manually set up automatic recurring payments for a customer via the Edit Subscriptions screen in 2.0+.
		 *
		 * @param string $payment_method_id The ID of the payment method to validate
		 * @param array $payment_meta associative array of meta data required for automatic payments
		 * @return array
		 */
		public function validate_subscription_payment_meta( $payment_method_id, $payment_meta ) {

			if ( $this->id === $payment_method_id ) {

				if ( ! isset( $payment_meta['post_meta']['_eway_token_customer_id']['value'] ) || empty( $payment_meta['post_meta']['_eway_token_customer_id']['value'] ) ) {

					throw new Exception( 'A "_eway_token_customer_id" value is required.' );

				} elseif ( ! is_numeric( $payment_meta['post_meta']['_eway_token_customer_id']['value'] ) ) {

					throw new Exception( 'Invalid Token Customer ID. A valid "_eway_token_customer_id" must be numeric.' );

				} elseif ( strlen( $payment_meta['post_meta']['_eway_token_customer_id']['value'] ) > 16 ) {

					throw new Exception( 'Invalid Token Customer ID. A valid "_eway_token_customer_id" must be 16 digits or less.' );

				}
			}
		}

	}
}
