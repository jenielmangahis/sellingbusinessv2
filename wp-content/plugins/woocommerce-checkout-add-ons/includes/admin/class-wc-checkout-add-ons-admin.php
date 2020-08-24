<?php
/**
 * WooCommerce Checkout Add-Ons
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Checkout Add-Ons to newer
 * versions in the future. If you wish to customize WooCommerce Checkout Add-Ons for your
 * needs please refer to http://docs.woocommerce.com/document/woocommerce-checkout-add-ons/ for more information.
 *
 * @package     WC-Checkout-Add-Ons/Classes
 * @author      SkyVerge
 * @copyright   Copyright (c) 2014-2018, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

defined( 'ABSPATH' ) or exit;

/**
 * Admin class
 *
 * @since 1.0
 */
class WC_Checkout_Add_Ons_Admin {


	/** @var string page suffix ID */
	public $page_id;

	/** @var \WC_Checkout_Add_Ons_Shop_Order_CPT instance */
	public $cpt;


	/**
	 * Init the class
	 *
	 * @since 1.0
	 * @return \WC_Checkout_Add_Ons_Admin
	 */
	public function __construct() {

		/**
		 * Filter the valid add-on types.
		 *
		 * @since 1.0
		 * @param array $add_on_types The valid add-on types.
		 */
		$this->add_on_types = apply_filters( 'wc_checkout_add_ons_add_on_types', array(
			'text'          => __( 'Text', 'woocommerce-checkout-add-ons' ),
			'textarea'      => __( 'Text Area', 'woocommerce-checkout-add-ons' ),
			'select'        => __( 'Select', 'woocommerce-checkout-add-ons' ),
			'multiselect'   => __( 'Multiselect', 'woocommerce-checkout-add-ons' ),
			'radio'         => __( 'Radio', 'woocommerce-checkout-add-ons' ),
			'checkbox'      => __( 'Checkbox', 'woocommerce-checkout-add-ons' ),
			'multicheckbox' => __( 'Multi-checkbox', 'woocommerce-checkout-add-ons' ),
			'file'          => __( 'File', 'woocommerce-checkout-add-ons' ),
		) );

		$add_on_attributes = array(
			'required'   => __( 'Required', 'woocommerce-checkout-add-ons' ),
			'listable'   => __( 'Display in View Orders screen', 'woocommerce-checkout-add-ons' ),
			'sortable'   => __( 'Allow Sorting on View Orders screen', 'woocommerce-checkout-add-ons' ),
			'filterable' => __( 'Allow Filtering on View Orders screen', 'woocommerce-checkout-add-ons' ),
		);

		if ( wc_checkout_add_ons()->is_subscriptions_active() ) {
			$add_on_attributes['subscriptions_renewable'] = __( 'Renew with Subscriptions', 'woocommerce-checkout-add-ons' );
		}

		/**
		 * Filter the valid add-on attributes.
		 *
		 * @since 1.0.0
		 * @param array $add_on_attributes The valid add-on attributes.
		 */
		$this->add_on_attributes = apply_filters( 'wc_checkout_add_ons_add_on_attributes', $add_on_attributes );

		// load view order list table / edit order screen customizations
		$this->cpt = wc_checkout_add_ons()->load_class( '/includes/admin/post-types/class-wc-checkout-add-ons-shop-order-cpt.php', 'WC_Checkout_Add_Ons_Shop_Order_CPT' );

		// load styles/scripts
		add_action( 'admin_enqueue_scripts', array( $this, 'load_styles_scripts' ) );

		// load WC styles / scripts on editor screen
		add_filter( 'woocommerce_screen_ids', array( $this, 'load_wc_scripts' ) );

		// add 'checkout add-ons' link under WooCommerce menu
		add_action( 'admin_menu', array( $this, 'add_menu_link' ) );

		// Add the custom settings.
		add_filter( 'woocommerce_payment_gateways_settings', array( $this, 'add_settings' ) );
	}


	/**
	 * Load admin styles and scripts
	 *
	 * @since 1.0
	 * @param string $hook_suffix the current URL filename, ie edit.php, post.php, etc
	 */
	public function load_styles_scripts( $hook_suffix ) {
		global $post_type, $wp_scripts;

		$is_coa_page = $this->page_id === $hook_suffix;

		// load admin css only on view orders / edit order screens
		if ( $is_coa_page || ( 'shop_order' === $post_type && in_array( $hook_suffix, array( 'edit.php', 'post.php', 'post-new.php' ), true ) ) ) {

			// admin CSS
			wp_enqueue_style( 'wc-checkout-add-ons-admin', wc_checkout_add_ons()->get_plugin_url() . '/assets/css/admin/wc-checkout-add-ons.min.css', array( 'woocommerce_admin_styles' ), WC_Checkout_Add_Ons::VERSION );

			// admin JS
			wp_enqueue_script( 'wc-checkout-add-ons-admin', wc_checkout_add_ons()->get_plugin_url() . '/assets/js/admin/wc-checkout-add-ons.min.js', array( 'jquery', 'jquery-ui-sortable', 'woocommerce_admin' ), WC_Checkout_Add_Ons::VERSION );

			$params = array(
				'new_row'                             => str_replace( array( "\n", "\t" ), '', $this->get_row_html() ),
				'name_required_text'                  => __( 'Name is a required field', 'woocommerce-checkout-add-ons' ),
				'name_not_unique_text'                => __( 'Name must be unique', 'woocommerce-checkout-add-ons' ),
				'option_required_text'                => __( 'A select/multiselect/checkbox/radio field must have at least one option', 'woocommerce-checkout-add-ons' ),
				'options_costs_placeholder_text'      => __( 'Pipe (|) separates options', 'woocommerce-checkout-add-ons' ),
				'options_costs_placeholder_text_cost' => __( 'Cost of add-on', 'woocommerce-checkout-add-ons' ),
				'select_placeholder_single'           => __( 'Select an Option', 'woocommerce-checkout-add-ons' ),
				'select_placeholder_multi'            => __( 'Select Some Options', 'woocommerce-checkout-add-ons' ),
				'select_no_results_text'              => __( 'No results match', 'woocommerce-checkout-add-ons' ),
			);

			// add HTML for adding new fields
			wp_localize_script( 'wc-checkout-add-ons-admin', 'wc_checkout_add_ons_params', $params );

			// Load jQuery UI only on editor screen
			if ( $is_coa_page ) {

				// get jQuery UI version
				$jquery_version = isset( $wp_scripts->registered['jquery-ui-core']->ver ) ? $wp_scripts->registered['jquery-ui-core']->ver : '1.9.2';

				// enqueue UI CSS
				wp_enqueue_style( 'jquery-ui-style', '//ajax.googleapis.com/ajax/libs/jqueryui/' . $jquery_version . '/themes/smoothness/jquery-ui.css' );
			}
		}
	}


	/**
	 * Add  screen ID to the list of pages for WC to load its JS on
	 *
	 * @since 1.0
	 * @param array $screen_ids
	 * @return array
	 */
	public function load_wc_scripts( $screen_ids ) {

		// sub-menu page screen ID
		$screen_ids[] = SV_WC_Plugin_Compatibility::normalize_wc_screen_id( 'wc_checkout_add_ons' );

		return $screen_ids;
	}


	/**
	 * Add 'Order add-ons' sub-menu link under 'WooCommerce' top level menu
	 *
	 * @since 1.0
	 */
	public function add_menu_link() {

		$this->page_id = add_submenu_page(
			'woocommerce',
			__( 'Checkout Add-Ons', 'woocommerce-checkout-add-ons' ),
			__( 'Checkout Add-Ons', 'woocommerce-checkout-add-ons' ),
			'manage_woocommerce',
			'wc_checkout_add_ons',
			array( $this, 'render_editor_screen' )
		);
	}


	/**
	 * Render the checkout add-ons editor
	 *
	 * @since 1.0
	 */
	public function render_editor_screen() {

		?>
		<div class="wrap woocommerce">
			<form method="post" id="mainform" action="" enctype="multipart/form-data" class="wc-checkout-add-ons">
				<div id="icon-woocommerce" class="icon32"><br /></div>
				<h2><?php esc_html_e( 'Checkout Add-Ons Editor', 'woocommerce-checkout-add-ons' ); ?></h2> <?php

				// save add-ons
				if ( ! empty( $_POST ) ) {
					$this->save_add_ons();
				}

				// show add-on editor
				$this->render_editor();

				?>
			</form>
		</div><?php
	}


	/**
	 * Render the checkout add-ons editor table
	 *
	 * @since 1.0
	 */
	private function render_editor() {
		?>
			<div class="wc-checkout-add-ons-editor-content">
				<table class="widefat wc-checkout-add-ons-editor">
					<thead>
						<tr>
							<th class="check-column">
								<input type="checkbox" />
							</th>
							<th class="wc-checkout-add-on-name">
								<?php esc_html_e( 'Name', 'woocommerce-checkout-add-ons' ); ?> <abbr class="required" title="required">*</abbr>
								<?php echo wc_help_tip( __( 'Add-on name displayed in the admin and order details, e.g., "Gift Message"', 'woocommerce-checkout-add-ons' ) ); ?>
							</th>
							<th class="wc-checkout-add-on-label">
								<?php esc_html_e( 'Checkout Label', 'woocommerce-checkout-add-ons' ); ?>
								<?php echo wc_help_tip( __( 'Optional descriptive label shown on checkout, e.g., "Add a Gift Message?". This will default to Name if blank.', 'woocommerce-checkout-add-ons' ) ); ?>
							</th>
							<th class="wc-checkout-add-on-type">
								<?php esc_html_e( 'Type', 'woocommerce-checkout-add-ons' ); ?>
							</th>
							<th class="wc-checkout-add-on-options-costs">
								<?php esc_html_e( 'Options / Costs', 'woocommerce-checkout-add-ons' ); ?>
								<?php echo wc_help_tip( __( 'Use Pipe (|) to separate options and surround options with double stars (**) to set as a default. To set a price for an option, append it to the option with an equation mark. Surround all HTML with double braces. Example: Yes=5.00|No|{{html}}', 'woocommerce-checkout-add-ons' ) ); ?>
							</th>
							<th class="wc-checkout-add-on-attributes">
								<?php esc_html_e( 'Attributes', 'woocommerce-checkout-add-ons' ); ?>
							</th>
							<?php if ( 'yes' === get_option( 'woocommerce_calc_taxes' ) ) : ?>
								<th class="wc-checkout-add-on-taxes">
									<?php esc_html_e( 'Taxes', 'woocommerce-checkout-add-ons' ); ?>
								</th>
							<?php endif; ?>
							<th class="js-wc-checkout-add-on-draggable"></th>
						</tr>
					</thead>
					<tfoot>
					<tr>
						<th colspan="3">
							<button type="button" class="button button-secondary js-wc-checkout-add-ons-add-new">&nbsp;&#43; <?php esc_html_e( 'New Add-On', 'woocommerce-checkout-add-ons' ); ?></button>
							<button type="button" class="button button-secondary js-wc-checkout-add-ons-remove"><?php esc_html_e( 'Remove Selected', 'woocommerce-checkout-add-ons' ); ?></button>
						</th>
						<th colspan="5"><input type="submit" class="button-primary" value="<?php esc_html_e( 'Save Add-Ons', 'woocommerce-checkout-add-ons' ); ?>"/></th>
					</tr>
					</tfoot>
					<tbody>
						<?php
						$index = 0;

						foreach ( wc_checkout_add_ons()->get_add_ons() as $add_on_id => $add_on ) {

							echo $this->get_row_html( $index, $add_on_id, $add_on );

							$index++;
						}
						?>
					</tbody>
				</table>
			</div>
		<?php

		wp_nonce_field( __FILE__ );
	}


	/**
	 * Return the HTML for a new add-on row
	 *
	 * @since 1.0
	 * @param int $index Optional. the row index
	 * @param int $add_on_id Optional. the ID of the add-on
	 * @param array $add_on Optional. the add-on data
	 * @return string the HTML
	 */
	private function get_row_html( $index = null, $add_on_id = null, $add_on = null ) {

		// used in table row view
		$add_on_types      = $this->add_on_types;
		$add_on_attributes = $this->add_on_attributes;

		// Get tax class options
		$tax_classes = array_filter( array_map( 'trim', explode( "\n", get_option( 'woocommerce_tax_classes' ) ) ) );
		$classes_options = array();
		$classes_options[''] = __( 'Standard', 'woocommerce-checkout-add-ons' );
		if ( $tax_classes ) {
			foreach ( $tax_classes as $class ) {
				$classes_options[ sanitize_title( $class ) ] = esc_html( $class );
			}
		}

		if ( is_object( $add_on ) ) {

			// convert options and costs back into a simple string
			if ( $add_on->has_options() ) {

				$values = array();

				foreach ( $add_on->get_options( false ) as $option ) {

					// skip blank option added for non-required select
					// check explicitly for 0, which is a valid option label
					if ( '0' !== $option['label'] && empty( $option['label'] ) ) {
						continue;
					}

					// add back default indicators
					$value = $option['selected'] ? '**' . $option['label'] . '**' : $option['label'];

					// Format the percent if saved as such.
					if ( isset( $option['cost_type'] ) && 'percent' === $option['cost_type'] ) {
						$option['cost'] = isset( $option['cost'] ) &&  '' !== $option['cost'] ? $option['cost'] * 100 . '%' : '';
					}

					// add cost
					$value = $value . ( isset( $option['cost'] ) && '' !== $option['cost'] ? '=' . $option['cost'] : '' );

					$values[] = $value;
				}

				$add_on->options_costs = implode( ' | ', $values );
			} else {

				// Format the percent if saved as such.
				if ( 'percent' === $add_on->cost_type ) {
					$add_on->options_costs = $add_on->cost * 100 . '%';
				} else {
					$add_on->options_costs = $add_on->cost;
				}
			}
		}

		ob_start();

		require( wc_checkout_add_ons()->get_plugin_path() . '/includes/admin/views/html-add-on-editor-table-row.php' );

		return ob_get_clean();
	}


	/**
	 * Save the add-ons
	 *
	 * @since 1.0
	 */
	private function save_add_ons() {

		if ( ! wp_verify_nonce( $_POST['_wpnonce'], __FILE__ ) ) {
			wp_die( __( 'Action failed. Please refresh the page and retry.', 'woocommerce-checkout-add-ons' ) );
		}

		$add_ons = array();

		if ( ! empty( $_POST['wc-checkout-add-on-id'] ) ) {

			// get the stored add-ons that have previously been saved
			$existing_addons = get_option( 'wc_checkout_add_ons', array() );

			for ( $index = 0; $index < count( $_POST['wc-checkout-add-on-id'] ); $index++ ) {

				// ID - assigned if empty
				$add_on_id = ( empty( $_POST['wc-checkout-add-on-id'][ $index ] ) ) ? $this->get_next_add_on_id() : absint( $_POST['wc-checkout-add-on-id'][ $index ] );

				// get the stored version of the current add-on if it exists
				$existing_addon = ( isset( $existing_addons[ $add_on_id ] ) ) ? $existing_addons[ $add_on_id ] : array();

				$add_ons[ $add_on_id ] = array();

				// name
				$add_ons[ $add_on_id ]['name'] = sanitize_text_field( stripslashes( $_POST['wc-checkout-add-on-name'][ $index ] ) );

				// label
				$add_ons[ $add_on_id ]['label'] = sanitize_text_field( stripslashes( $_POST['wc-checkout-add-on-label'][ $index ] ) );

				// type
				$add_ons[ $add_on_id ]['type'] = ( in_array( $_POST['wc-checkout-add-on-type'][ $index ], array_keys( $this->add_on_types ) ) ) ? $_POST['wc-checkout-add-on-type'][ $index ] : 'text';

				// options / costs
				if ( isset( $_POST['wc-checkout-add-on-options-costs'][ $index ] ) ) {

					switch ( $add_ons[ $add_on_id ]['type'] ) {

						// text/textarea/file/chekbox fields have simple costs and no options
						case 'text':
						case 'textarea':
						case 'file':
						case 'checkbox':
							$cost = $_POST['wc-checkout-add-on-options-costs'][ $index ];

							$cost_type = 'fixed';

							if ( '' !== $cost ) {

								if ( SV_WC_Helper::str_exists( $cost, '%' ) ) {
									$cost = floatval( $cost ) / 100;
									$cost_type = 'percent';
								} else {
									$cost = floatval( $cost );
								}
							}

							$add_ons[ $add_on_id ]['cost']      = $cost;
							$add_ons[ $add_on_id ]['cost_type'] = $cost_type;
						break;

						// select/multiselect/multicheckbox/radio fields have multiple options and a single default, multiselect/multicheckbox have multiple options and multiple defaults
						case 'select':
						case 'multiselect':
							$options = array_map( 'sanitize_text_field', explode( '|', $_POST['wc-checkout-add-on-options-costs'][ $index ] ) );

							foreach ( $options as $option ) {

								$cost      = '';
								$cost_type = 'fixed';

								$default = SV_WC_Helper::str_exists( $option, '**' );
								$option  = str_replace( '**', '', $option );

								// break each option label in two: option label and cost associated with that option
								$label_parts = explode( '=', $option );

								if ( isset( $label_parts[1] ) ) {

									$cost = floatval( $label_parts[1] );

									if ( SV_WC_Helper::str_exists( $label_parts[1], '%' ) ) {
										$cost      = $cost / 100;
										$cost_type = 'percent';
									}
								}

								$add_ons[ $add_on_id ]['options'][] = array(
									'default'   => $default,
									'label'     => stripslashes( trim( $label_parts[0] ) ),
									'value'     => sanitize_key( $label_parts[0] ),
									'cost'      => $cost,
									'cost_type' => $cost_type,
								);
							}

						break;

						// multicheckbox/radio fields accept HTML in their options
						case 'multicheckbox':
						case 'radio':
							// explode on '|' but not when it occurs within double curly braces
							preg_match_all( '/(?:[^|]*{{.*?}})+[^|]*|[^|]+/', $_POST['wc-checkout-add-on-options-costs'][ $index ], $options );
							$options = $options[0];
							$options = array_map( array( $this, 'sanitize_text_field_exclude_braced' ), $options );

							foreach ( $options as $option ) {

								$cost      = '';
								$cost_type = 'fixed';

								$default = SV_WC_Helper::str_exists( $option, '**' );
								$option  = str_replace( '**', '', $option );

								// break each option label in two: option label and cost associated with that option
								preg_match( '/^(.+)=(.+)$/', $option, $label_parts );

								if ( isset( $label_parts[2] ) && ! ( SV_WC_Helper::str_exists( $label_parts[2], '=' )  ||
								                                     SV_WC_Helper::str_exists( $label_parts[2], '}}' ) ||
								                                     SV_WC_Helper::str_exists( $label_parts[2], '{{' ) ) ) {
									$label = $label_parts[1];
									$cost  = floatval( $label_parts[2] );

									if ( SV_WC_Helper::str_exists( $label_parts[2], '%' ) ) {
										$cost      = $cost / 100;
										$cost_type = 'percent';
									}

								} else {

									$label = $option;
								}

								$add_ons[ $add_on_id ]['options'][] = array(
									'default'   => $default,
									'label'     => stripslashes( trim( $label ) ),
									'value'     => sanitize_key( $label ),
									'cost'      => $cost,
									'cost_type' => $cost_type,
								);
							}

						break;

						// allow custom add-on types
						default:

							/**
							 * Filter the costs of custom add-on types
							 *
							 * @since 1.0
							 * @param stirng $add_on_costs The add-on cost.
							 * @param int $add_on_id The add-on id.
							 */
							$add_ons[ $add_on_id ]['cost']    = apply_filters( 'wc_checkout_add_ons_' . $add_ons[ $add_on_id ]['type'] . '_cost',    '',      $_POST['wc-checkout-add-on-options-costs'][ $index ], $add_on_id );

							/**
							 * Filter the options of custom add-on types
							 *
							 * @since 1.0
							 * @param stirng $add_on_options The add-on options.
							 * @param int $add_on_id The add-on id.
							 */
							$add_ons[ $add_on_id ]['options'] = apply_filters( 'wc_checkout_add_ons_' . $add_ons[ $add_on_id ]['type'] . '_options', array(), $_POST['wc-checkout-add-on-options-costs'][ $index ], $add_on_id );
					}

				} else {

					$add_ons[ $add_on_id ]['cost'] = null;
				}

				// attributes - true/false for each
				if ( ! empty( $_POST['wc-checkout-add-on-attributes'][ $index ] ) ) {

					foreach ( array( 'required', 'listable', 'sortable', 'filterable', 'subscriptions_renewable' ) as $attribute ) {
						$add_ons[ $add_on_id ][ $attribute ] = ( in_array( $attribute, $_POST['wc-checkout-add-on-attributes'][ $index ] ) );
					}

					// add the listable attribute if either sortable or filterable were added
					if ( ! $add_ons[ $add_on_id ]['listable'] && ( $add_ons[ $add_on_id ]['sortable'] || $add_ons[ $add_on_id ]['filterable'] ) ) {
						$add_ons[ $add_on_id ]['listable'] = true;
					}
				}

				// if taxes are enabled globally, save the tax settings
				if ( 'yes' === get_option( 'woocommerce_calc_taxes' ) ) {

					$add_ons[ $add_on_id ]['tax_status'] = sanitize_text_field( $_POST['wc-checkout-add-on-tax_status'][ $index ] );
					$add_ons[ $add_on_id ]['tax_class']  = sanitize_text_field( $_POST['wc-checkout-add-on-tax_class'][ $index ] );

				// otherwise if there are saved tax settings for this add-on, use those so they aren't lost
				} elseif ( isset( $existing_addon['tax_status'] ) && isset( $existing_addon['tax_class'] ) ) {

					$add_ons[ $add_on_id ]['tax_status'] = $existing_addon['tax_status'];
					$add_ons[ $add_on_id ]['tax_class']  = $existing_addon['tax_class'];
				}

				// scope (not exposed in editor right now)
				$add_ons[ $add_on_id ]['scope'] = 'order';
			}
		}

		if ( true === update_option( 'wc_checkout_add_ons', $add_ons ) ) {
			echo '<div class="updated"><p>' . __( 'Add-Ons Saved', 'woocommerce-checkout-add-ons' ) . '</p></div>';
		}
	}


	/**
	 * Get the next available add-on ID
	 *
	 * @since 1.0
	 * @return int the next available add-on ID
	 */
	private function get_next_add_on_id() {

		$next_add_on_id = get_option( 'wc_checkout_add_ons_next_add_on_id' );

		update_option( 'wc_checkout_add_ons_next_add_on_id', ++$next_add_on_id );

		return $next_add_on_id;
	}


	/**
	 * Applies sanitize_text_field() to a string, but passes over the parts of
	 * the string surrounded by double curly braces, sanitizing these only
	 * minimally.
	 *
	 * @since 1.12.1
	 *
	 * @param string $str string to sanitize
	 * @return string sanitized string
	 */
	private function sanitize_text_field_exclude_braced( $str ) {

		preg_match_all( '/{{.*?}}/', $str, $braced_strings );
		$braced_strings = $braced_strings[0];

		if ( $braced_strings ) {
			$str = preg_replace( '/{{.*?}}/', '{{}}', $str );
		}

		$str = sanitize_text_field( $str );

		if ( $braced_strings ) {

			$braced_strings = array_map( 'wp_kses_post', $braced_strings );
			$braced_strings = array_map( 'trim', $braced_strings );

			foreach ( $braced_strings as $braced_string ) {
				$str = preg_replace( '/{{}}/', $braced_string, $str, 1 );
			}

			// remove all remaining empty sets of braces
			$str = str_replace( '{{}}', '', $str );
		}

		return $str;
	}


	/**
	 * Add the custom settings.
	 *
	 * @since 1.6.0
	 * @param array $settings The default WooCommerce checkout settings.
	 * @return array The WooCommerce checkout settings.
	 */
	public function add_settings( $settings ) {

		$updated_settings = array();

		$new_settings = array(

			// Begin the Checkout Add-Ons section.
			array(
				'title' => __( 'Checkout Add-Ons', 'woocommerce-checkout-add-ons' ),
				'type'  => 'title',
				'id'    => 'checkout_add_on_options',
			),

			// Add the Display Position setting.
			array(
				'title'   => __( 'Display Position', 'woocommerce-checkout-add-ons' ),
				'desc'    => __( 'This controls where on the Checkout page your custom add-ons will be displayed.', 'woocommerce-checkout-add-ons' ),
				'id'      => 'wc_checkout_add_ons_position',
				'class'   => 'wc-enhanced-select',
				'css'     => 'min-width:300px;',
				'default' => 'woocommerce_checkout_after_customer_details',
				'type'    => 'select',
				'options' => array(
					'woocommerce_checkout_after_customer_details'  => __( 'Before Order Summary', 'woocommerce-checkout-add-ons' ),
					'woocommerce_checkout_before_customer_details' => __( 'Before Billing Details', 'woocommerce-checkout-add-ons' ),
					'woocommerce_checkout_billing'                 => __( 'After Billing Details', 'woocommerce-checkout-add-ons' ),
				),
				'desc_tip' => true,
			),

			// End the Checkout Add-Ons section.
			array(
				'type' => 'sectionend',
				'id'   => 'checkout_add_on_options',
			),
		);

		// WooCommerce 3.4 changed the settings layout, so our settings need to be placed differently on a per-version basis.
		if ( SV_WC_Plugin_Compatibility::is_wc_version_gte( '3.4' ) ) {
			$place_after_setting = 'payment_gateways_options';
		} else {
			$place_after_setting = 'checkout_process_options';
		}

		foreach ( $settings as $setting ) {

			$updated_settings[] = $setting;

			// Add our settings after the "Checkout Process" section
			if ( isset( $setting['id'] ) && $place_after_setting === $setting['id'] && 'sectionend' === $setting['type'] ) {
				$updated_settings = array_merge( $updated_settings, $new_settings );
			}
		}

		return $updated_settings;
	}


} // end \WC_Checkout_Add_Ons_Admin class
