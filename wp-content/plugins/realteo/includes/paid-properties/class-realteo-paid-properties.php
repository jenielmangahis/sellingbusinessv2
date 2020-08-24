<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;

/**
 *  class
 */
class Realteo_Paid_Properties {
	
	/**
	 * Returns static instance of class.
	 *
	 * @return self
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}


	/**
	 * Constructor
	 */
	public function __construct() {

		/* Hooks */
		add_action( 'woocommerce_product_options_general_product_data', array( $this,  'realteo_add_custom_settings' ) );
		add_action( 'woocommerce_process_product_meta_property_package', array( $this, 'save_package_data' ) );
		
		/* Includes */
		include_once( 'class-realteo-paid-properties-orders.php' );
		include_once( 'class-realteo-paid-properties-package.php' );
		include_once( 'class-realteo-paid-properties-cart.php' );

	}


	function realteo_add_custom_settings() {
	    global $woocommerce, $post;
	    echo '<div class="options_group show_if_property_package">';

	    // Create a number field, for example for UPC
	     woocommerce_wp_text_input( array(
			'id' 				=> '_property_limit',
			'label' 			=> __( 'Property limit', 'realteo' ),
			'description' 		=> __( 'The number of properties a user can post with this package.', 'realteo' ),
			'value' 			=> ( $limit = get_post_meta( $post->ID, '_property_limit', true ) ) ? $limit : '',
			'placeholder' 		=> __( 'Unlimited', 'realteo' ),
			'type' 				=> 'number',
			'desc_tip' 			=> true,
			'custom_attributes' => array(
			'min'   			=> '',
			'step' 				=> '1',
			),
		) ); 

	    woocommerce_wp_text_input( array(
			'id' 				=> '_property_duration',
			'label' 			=> __( 'Property duration', 'realteo' ),
			'description' 		=> __( 'The number of days that the property will be active.', 'realteo' ),
			'value' 			=> get_post_meta(  $post->ID, '_property_duration', true ),
			'placeholder' 		=> get_option( 'job_manager_submission_duration' ),
			'desc_tip' 			=> true,
			'type' 				=> 'number',
			'custom_attributes' => array(
			'min'  				=> '',
			'step' 				=> '1',
			),
		) );

		 woocommerce_wp_checkbox( array(
			'id' => '_property_featured',
			'label' => __( 'Feature Property?', 'realteo' ),
			'description' => __( 'Feature this property - it will have a badge and sticky status.', 'realteo' ),
			'value' => get_post_meta(  $post->ID, '_property_featured', true ),
		) ); 
	    echo '</div>';
	    ?>
	    <script type="text/javascript">
		jQuery(function(){
			jQuery('#product-type').change( function() {
				jQuery('#woocommerce-product-data').removeClass(function(i, classNames) {
					var classNames = classNames.match(/is\_[a-zA-Z\_]+/g);
					if ( ! classNames ) {
						return '';
					}
					return classNames.join(' ');
				});
				jQuery('#woocommerce-product-data').addClass( 'is_' + jQuery(this).val() );
			} );
			jQuery('.pricing').addClass( 'show_if_property_package' );
			jQuery('._tax_status_field').closest('div').addClass( 'show_if_property_package' );
			
			
			jQuery('#product-type').change();
			
		});
	</script>
	<?php
	}

	/**
	 * Save Job Package data for the product
	 *
	 * @param  int $post_id
	 */
	public function save_package_data( $post_id ) {
		global $wpdb;

		// Save meta
		$meta_to_save = array(
			'_property_duration'             => '',
			'_property_limit'                => 'int',
			'_property_featured'             => 'yesno',
		);

		foreach ( $meta_to_save as $meta_key => $sanitize ) {
			$value = ! empty( $_POST[ $meta_key ] ) ? $_POST[ $meta_key ] : '';
			switch ( $sanitize ) {
				case 'int' :
					$value = absint( $value );
					break;
				case 'float' :
					$value = floatval( $value );
					break;
				case 'yesno' :
					$value = $value == 'yes' ? 'yes' : 'no';
					break;
				default :
					$value = sanitize_text_field( $value );
			}
			update_post_meta( $post_id, $meta_key, $value );
		}

	}

}

new Realteo_Paid_Properties();



