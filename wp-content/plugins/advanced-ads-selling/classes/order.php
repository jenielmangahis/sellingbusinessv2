<?php

/** 
 * ad order handling logic
 */

class Advanced_Ads_Selling_Order {
    
	/**
	 *
	 * @since     1.0.0
	 */
	public function __construct() {

	    add_action('plugins_loaded', array($this, 'wp_plugins_loaded'));
	}

	/**
	 * load actions and filters
	 */
	public function wp_plugins_loaded() {

	    if (!class_exists('Advanced_Ads', false)) {
		return;
	    }
	    
	    // create the ad when the order is marked as completed
	    // add_action( 'woocommerce_order_status_completed', array( $this, 'process_order' ), 10, 2 );	
	    add_action( 'woocommerce_checkout_update_order_meta', array( $this, 'process_order' ), 10, 2 );	
	    
	}
	
	/**
	 * process the order
	 * create the ads
	 * 
	 * @param int $order_id
	 */
	public function process_order( $order_id ){
	    
		// don’t process the ads if they were already created
		/*if( get_post_meta( $order_id, 'advanced_ads_selling_processed_order', true )){
		    return;
		}*/
		
		$order = new WC_Order( $order_id );
		$items = $order->get_items();
		
		$_item_key = 1;
		$has_ad_item = false; // flag if at least one item is an ad product
		foreach ($items as $_item_id => $_item) {
			$product = wc_get_product( $_item['product_id'] );
			if ( $product->is_type( 'advanced_ad' ) ) {
				// create import xml
				$xml = $this->create_import_xml( $_item, $order, $_item_id, $_item_key );
				// create the ad
				Advanced_Ads_Import::get_instance()->import( $xml );
				$has_ad_item = true;
			}
			$_item_key++;
		}
		
		// save hash for ad setup page
		if( $has_ad_item) {
			update_post_meta( $order_id, "advanced_ads_selling_setup_hash", wp_generate_password( 48, false ) ); // Ad Setup Hash
		}
		
		// notify client after the purchase
		Advanced_Ads_Selling_Notifications::notify_client_after_purchase( $order_id );
		
		// mark this order as processed with a custom value
		// update_post_meta( $order_id, 'advanced_ads_selling_processed_order', 1 );
	    
	}
	
	/**
	 * create import xml for the ad
	 * 
	 * @param arr $params order item params
	 * @param obj $order WP_Order
	 * @param int $item_id	id of the item in the order
	 * @param int $item_key index of the item in the order
	 * @return $xml import xml
	 */
	private function create_import_xml( $params, $order, $item_id, $item_key ){

		$xml = '';
		$type = 'plain'; // default ad type
		$product_id = wc_get_order_item_meta( $item_id, '_product_id', true);
		$sales_type = get_post_meta($product_id, '_ad_sales_type', true);

		// handle plain text ads

		// add item index if more than one ad is in the order
		$item_key_text = $item_key > 1 ? ' / ' . $item_key : '';
		
		// get order ID based on version of WooCommerce
		if ( Advanced_Ads_Selling_Plugin::version_check() ) {
		    $order_id = $order->get_id();
		} else {
		    $order_id = $order->id;
		}

		$xml_array[] = '<ads type="array">';
		$xml_array[] = '<item key="0" type="array">';
		$xml_array[] = '<ID type="string">' . $order_id . '</ID>';
		$xml_array[] = '<post_status>draft</post_status>';
		$xml_array[] = '<post_title>Order #' . $order_id . $item_key_text . '</post_title>';
		/*if( isset( $params['ad_html'] ) && trim( $params['ad_html'] ) ){
			$xml_array[] = '<post_content type="string"><![CDATA[' . $params['ad_html'] . ']]></post_content>';
		}*/

		// meta data
		$xml_array[] = '<meta_input type="array">';
		$xml_array[] = '<advanced_ads_ad_options type="array">';
		// add impression limit
		if( defined( 'AAT_VERSION') && in_array( $sales_type, array( 'impressions', 'clicks' ) ) && $limit = wc_get_order_item_meta( $item_id, '_ad_pricing_option', true ) ){
			$xml_array[] = '<tracking type="array">';
			switch( $sales_type ) :
			    case 'impressions' :
				    $xml_array[] = '<impression_limit type="numeric">' . absint( $limit ) . '</impression_limit>';
				break;
			    case 'clicks' :
				    $xml_array[] = '<click_limit type="numeric">' . absint( $limit ) . '</click_limit>';
				break;
			endswitch;
			$xml_array[] = '</tracking>';
		}
		$xml_array[] = '<type type="string">' . $type . '</type>';
		$xml_array[] = '</advanced_ads_ad_options>';
		$xml_array[] = '<advanced_ads_selling_order>' . $order_id . '</advanced_ads_selling_order>';
		$xml_array[] = '<advanced_ads_selling_order_item>' . $item_id . '</advanced_ads_selling_order_item>';
		$xml_array[] = '</meta_input>';
		$xml_array[] = '</item>';
		$xml_array[] = '</ads>';
		

		return '<advads-export>' . implode( '', $xml_array ) . '</advads-export>';
	}
	
	/**
	 * convert order item id into ad id
	 * 
	 * @param   int		$item_id id of the order item
	 * @return  int|book	$ad_id	id of the ad created from that order; false if no ad was found
	 */
	public static function order_item_id_to_ad_id( $item_id ){
	    
	    if( ! $item_id = absint( $item_id ) ){
		return false;
	    }
	    
	    $args = array(
		    'post_type' => Advanced_Ads::POST_TYPE_SLUG,
		    'posts_per_page' => 1,
		    'post_status' => 'any',
		    'meta_query' => array(
			    array(
				'key'     => 'advanced_ads_selling_order_item',
				'value'   => $item_id,
			    )
		    )
	    );
	    
	    $ad = new WP_Query( $args );
	    
	    return isset( $ad->post->ID ) ? $ad->post->ID : false;
	    
	}
	
	/**
	 * check if an order contains ad products
	 * by checking for the existance of the setup page hash
	 * 
	 * @param   int	    $order_id post id of the order
	 * @return  bool    true, if order contains ad products or false if not
	 */
	public static function has_ads( $order_id = 0 ){		
		return ( get_post_meta( $order_id, 'advanced_ads_selling_setup_hash', true ) );
	}
}

