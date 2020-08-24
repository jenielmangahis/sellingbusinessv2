<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Gateway_EWAY_Saved_Cards class.
 */
class WC_Gateway_EWAY_Saved_Cards {

	/**
	 * Constructor
	 */
	public function __construct() {
		add_action( 'wp', array( $this, 'delete_card' ) );
		add_action( 'woocommerce_after_my_account', array( $this, 'output' ) );
	}

	/**
	 * Display saved cards
	 */
	public function output() {
		if ( ! is_user_logged_in() ) {
			return;
		}

		$eway_cards = get_user_meta( get_current_user_id(), '_eway_token_cards', true );

		if ( ! $eway_cards ) {
			return;
		}

		wc_get_template( 'saved-cards.php', array( 'cards' => $eway_cards ), 'woocommerce-gateway-eway/', WC_EWAY_TEMPLATE_PATH );
	}

	/**
	 * Delete a card
	 */
	public function delete_card() {
		if ( ! isset( $_POST['eway_delete_card'] ) || ! is_account_page() ) {
			return;
		}

		$user_logged_in = is_user_logged_in();
		$eway_cards     = $user_logged_in ? get_user_meta( get_current_user_id(), '_eway_token_cards', true ) : false;

		if (
			! $user_logged_in ||
			! $eway_cards ||
			! isset( $_POST['_wpnonce'] ) ||
			! wp_verify_nonce( sanitize_key( $_POST['_wpnonce'] ), 'eway_del_card' )
		) {
			wp_die( esc_html__( 'Unable to verify deletion, please try again', 'wc-eway' ) );
		}

		// Delete card here
		foreach ( $eway_cards as $card ) {
			if ( sanitize_text_field( wp_unslash( $_POST['eway_delete_card'] ) ) == $card['id'] ) {
				unset( $eway_cards[ $card['id'] ] );
				break;
			}
		}
		$result = update_user_meta( get_current_user_id(), '_eway_token_cards', $eway_cards );

		if ( is_wp_error( $result ) ) {
			wc_add_notice( __( 'Unable to delete card.', 'wc-eway' ), 'error' );
		} else {
			wc_add_notice( __( 'Card deleted.', 'wc-eway' ), 'success' );
		}

		wp_safe_redirect( apply_filters( 'wc_eway_manage_saved_cards_url', get_permalink( wc_get_page_id( 'myaccount' ) ) ) );
		exit;
	}
}
new WC_Gateway_EWAY_Saved_Cards();
