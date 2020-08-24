<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 * Init the plugin when all plugins are loaded
 */

// add a product type
add_filter( 'product_type_selector', 'realteo_add_property_product_type' );
function realteo_add_property_product_type( $types ){
    $types[ 'property_package' ] = __( 'Property Package' );
    return $types;
}

function realteo_create_property_product_type() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	class WC_Product_Property_Package extends WC_Product {

	    public function __construct( $product ) {
	       $this->product_type = 'property_package';
	       parent::__construct( $product );
	       // add additional functions here
	    }

		/**
		 * Compatibility function for `get_id()` method
		 *
		 * @return int
		 */
		public function get_id() {
			
			return parent::get_id();
		}

		/**
		 * Get product id
		 *
		 * @return int
		 */
		public function get_product_id() {
			return $this->get_id();
		}

		/**
		 * Get the product's title. For products this is the product name.
		 *
		 * @return string
		 */
		public function get_title() {
			return apply_filters( 'woocommerce_product_title', parent::get_name(), $this );
		}

    	/**
		 * Get internal type.
		 *
		 * @return string
		 */
		public function get_type() {
			return 'property_package';
		}

		/**
		 *
		 * @return boolean
		 */
		public function is_sold_individually() {
			return true;
		}

		/**
		 * Get the add to url used mainly in loops.
		 *
		 * @access public
		 * @return string
		 */
		public function add_to_cart_url() {
			$url = $this->is_in_stock() ? remove_query_arg( 'added-to-cart', add_query_arg( 'add-to-cart', $this->get_id() ) ) : get_permalink( $this->get_id() );

			return apply_filters( 'woocommerce_product_add_to_cart_url', $url, $this );
		}

		/**
		 * Get the add to cart button text
		 *
		 * @access public
		 * @return string
		 */
		public function add_to_cart_text() {
			$text = $this->is_purchasable() && $this->is_in_stock() ? __( 'Add to cart', 'realteo' ) : __( 'Read More', 'realteo' );

			return apply_filters( 'woocommerce_product_add_to_cart_text', $text, $this );
		}

		/**
		 *
		 * @return boolean
		 */
		public function is_purchasable() {
			return true;
		}

		/**
		 *
		 * @return boolean
		 */
		public function is_virtual() {
			return true;
		}

		/**
		 * Return  duration granted
		 *
		 * @return int
		 */
		public function get_duration() {
			$property_duration = $this->get_property_duration();
			if ( $property_duration ) {
				return $property_duration;
			} else {
				return get_option( 'realteo_submission_duration' );
			}
		}


		/**
		 * Return listing limit
		 *
		 * @return int 0 if unlimited
		 */
		public function get_limit() {
			$property_limit = $this->get_property_limit();
			if ( $property_limit ) {
				return $property_limit;
			} else {
				return 0;
			}
		}


		/**
		 * Return if featured
		 *
		 * @return bool true if featured
		 */
		public function is_property_featured() {
			return 'yes' === $this->get_property_featured();
		}

		/**
		 * Returns whether or not the product is featured.
		 *
		 * @return bool
		 */
		public function is_featured() {
			return true === $this->get_featured();
		}
		/**
		 * Get job listing featured flag
		 *
		 * @return string
		 */
		public function get_property_featured() {
			return $this->get_product_meta( 'property_featured' );
		}

		/**
		 * Get job listing limit
		 *
		 * @return int
		 */
		public function get_property_limit() {
			return $this->get_product_meta( 'property_limit' );
		}

		/**
		 * Get job listing duration
		 *
		 * @return int
		 */
		public function get_property_duration() {
			return $this->get_product_meta( 'property_duration' );
		}

		public function get_product_meta( $key ) {
			return $this->get_meta( '_' . $key );
		}


	}

	
}

add_action( 'plugins_loaded', 'realteo_create_property_product_type' );
