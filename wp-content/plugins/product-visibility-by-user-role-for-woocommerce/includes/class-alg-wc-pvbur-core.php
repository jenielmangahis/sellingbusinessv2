<?php
/**
 * Product Visibility by User Role for WooCommerce - Core Class
 *
 * @version 1.4.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_PVBUR_Core' ) ) :

class Alg_WC_PVBUR_Core {

	/**
	 * Constructor.
	 *
	 * @version 1.4.0
	 * @since   1.0.0
	 */
	function __construct() {
		$this->is_wc_version_below_3 = version_compare( get_option( 'woocommerce_version', null ), '3.0.0', '<' );
		if ( 'yes' === get_option( 'alg_wc_pvbur_enabled', 'yes' ) ) {
			// Core
			if ( ! is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
				if ( 'yes' === get_option( 'alg_wc_pvbur_visibility', 'yes' ) ) {
					add_filter( 'woocommerce_product_is_visible', array( $this, 'product_by_user_role_visibility' ),  PHP_INT_MAX, 2 );
				}
				if ( 'yes' === get_option( 'alg_wc_pvbur_purchasable', 'no' ) ) {
					add_filter( 'woocommerce_is_purchasable',     array( $this, 'product_by_user_role_purchasable' ), PHP_INT_MAX, 2 );
				}
			}
			// Admin products list
			if ( 'yes' === get_option( 'alg_wc_pvbur_add_column_visible_user_roles', 'no' ) ) {
				add_filter( 'manage_edit-product_columns',        array( $this, 'add_product_columns' ),   PHP_INT_MAX );
				add_action( 'manage_product_posts_custom_column', array( $this, 'render_product_column' ), PHP_INT_MAX );
			}
			// Quick and bulk edit
			if ( 'yes' === apply_filters( 'alg_wc_pvbur', 'no', 'products_bulk_edit' ) || 'yes' === get_option( 'alg_wc_pvbur_add_to_quick_edit', 'no' ) ) {
				if ( 'yes' === apply_filters( 'alg_wc_pvbur', 'no', 'products_bulk_edit' ) ) {
					add_action( 'woocommerce_product_bulk_edit_end',   array( $this, 'add_bulk_and_quick_edit_fields' ),  PHP_INT_MAX );
				}
				if ( 'yes' === get_option( 'alg_wc_pvbur_add_to_quick_edit', 'no' ) ) {
					add_action( 'woocommerce_product_quick_edit_end',  array( $this, 'add_bulk_and_quick_edit_fields' ),  PHP_INT_MAX );
				}
				add_action( 'woocommerce_product_bulk_and_quick_edit', array( $this, 'save_bulk_and_quick_edit_fields' ), PHP_INT_MAX, 2 );
			}
			// Setups conditions where invisible products can be searched or prevented
			add_filter( 'alg_wc_pvbur_can_search', array( $this, 'setups_search_cases' ), 10, 2 );
			// Clears invisible products ids cache
			add_action( 'alg_wc_pvbur_save_metabox', array( $this, 'clear_invisible_product_ids_cache' ) );
			// Modify query
			if ( 'yes' === get_option( 'alg_wc_pvbur_query', 'no' ) ) {
				add_action( 'woocommerce_product_query', array( $this, 'pre_get_posts_hide_invisible_products' ), PHP_INT_MAX );
				add_action( 'pre_get_posts',             array( $this, 'pre_get_posts_hide_invisible_products' ), PHP_INT_MAX );
			}
		}
	}

	/**
	 * Clears invisible products ids cache on metabox saving
	 *
	 * @version 1.2.1
	 * @since   1.2.1
	 * @param $post_id
	 */
	function clear_invisible_product_ids_cache( $post_id ) {
		global $wpdb;
		$transient_like = '%_transient_awcpvbur_inv_pids_%';
		$sql            = $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name like %s", $transient_like );
		$results        = $wpdb->query( $sql );
	}

	/**
	 * Setups conditions where invisible products can be searched or prevented
	 *
	 * @version 1.2.2
	 * @since   1.2.1
	 *
	 * @param bool $can_search
	 * @param $query
	 *
	 * @return bool
	 */
	function setups_search_cases( $can_search = true, $query ) {
		$force_search = $query->get( 'alg_wc_pvbur_search' );
		if (
			! empty( $force_search ) &&
			filter_var( $force_search, FILTER_VALIDATE_BOOLEAN ) === true
		) {
			return true;
		}

		if (
			is_admin() ||
			( defined( 'DOING_AJAX' ) && DOING_AJAX ) ||
			( current_filter() == 'pre_get_posts' && ! $query->is_single() && ! $query->is_search() ) ||
			! is_main_query() ||
			empty( $query->query ) ||
			( isset( $query->query['post_type'] ) && $query->query['post_type'] == 'nav_menu_item' )
		) {
			return false;
		}

		return $can_search;
	}

	/**
	 * save_bulk_and_quick_edit_fields.
	 *
	 * @version 1.4.0
	 * @since   1.1.5
	 */
	function save_bulk_and_quick_edit_fields( $post_id, $post ) {

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

		// Don't save revisions and autosaves.
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) || 'product' !== $post->post_type || ! current_user_can( 'edit_post', $post_id ) ) {
			return $post_id;
		}

		// Check nonce.
		if ( ! isset( $_REQUEST['woocommerce_quick_edit_nonce'] ) || ! wp_verify_nonce( $_REQUEST['woocommerce_quick_edit_nonce'], 'woocommerce_quick_edit_nonce' ) ) { // WPCS: input var ok, sanitization ok.
			return $post_id;
		}

		// Check bulk or quick edit.
		if ( ! empty( $_REQUEST['woocommerce_quick_edit'] ) ) { // WPCS: input var ok.
			if ( 'no' === get_option( 'alg_wc_pvbur_add_to_quick_edit', 'no' ) ) {
				return $post_id;
			}
		} else {
			if ( 'no' === apply_filters( 'alg_wc_pvbur', 'no', 'products_bulk_edit' ) ) {
				return $post_id;
			}
		}

		// Save.
		if ( ! isset( $_REQUEST['alg_wc_pvbur_visible'] ) ) {
			update_post_meta( $post_id, '_' . 'alg_wc_pvbur_visible', array() );
		} elseif ( is_array( $_REQUEST['alg_wc_pvbur_visible'] ) && ! in_array( 'alg_wc_pvbur_no_change', $_REQUEST['alg_wc_pvbur_visible'] ) ) {
			if ( in_array( 'alg_wc_pvbur_clear', $_REQUEST['alg_wc_pvbur_visible'] ) ) {
				update_post_meta( $post_id, '_' . 'alg_wc_pvbur_visible', array() );
			} else {
				update_post_meta( $post_id, '_' . 'alg_wc_pvbur_visible', $_REQUEST['alg_wc_pvbur_visible'] );
			}
		}
		if ( ! isset( $_REQUEST['alg_wc_pvbur_invisible'] ) ) {
			update_post_meta( $post_id, '_' . 'alg_wc_pvbur_invisible', array() );
		} elseif ( is_array( $_REQUEST['alg_wc_pvbur_invisible'] ) && ! in_array( 'alg_wc_pvbur_no_change', $_REQUEST['alg_wc_pvbur_invisible'] ) ) {
			if ( in_array( 'alg_wc_pvbur_clear', $_REQUEST['alg_wc_pvbur_invisible'] ) ) {
				update_post_meta( $post_id, '_' . 'alg_wc_pvbur_invisible', array() );
			} else {
				update_post_meta( $post_id, '_' . 'alg_wc_pvbur_invisible', $_REQUEST['alg_wc_pvbur_invisible'] );
			}
		}

		return $post_id;
	}

	/**
	 * add_bulk_and_quick_edit_fields.
	 *
	 * @version 1.4.0
	 * @since   1.1.5
	 */
	function add_bulk_and_quick_edit_fields() {
		$all_roles_options = '';
		$all_roles_options .= '<option value="alg_wc_pvbur_no_change" selected>' . __( '— No change —', 'woocommerce' )                                 . '</option>';
		$all_roles_options .= '<option value="alg_wc_pvbur_clear">'              . __( '— Clear —', 'product-visibility-by-user-role-for-woocommerce' ) . '</option>';
		foreach ( alg_wc_pvbur_get_user_roles_for_settings() as $role_id => $role_desc ) {
			$all_roles_options .= '<option value="' . $role_id . '">' . $role_desc . '</option>';
		}
		?><br class="clear" />
		<label>
			<span class="title"><?php esc_html_e( 'User roles: Visible', 'product-visibility-by-user-role-for-woocommerce' ); ?></span>
			<select multiple id="alg_wc_pvbur_visible" name="alg_wc_pvbur_visible[]">
				<?php echo $all_roles_options; ?>
			</select>
		</label>
		<label>
			<span class="title"><?php esc_html_e( 'User roles: Invisible', 'product-visibility-by-user-role-for-woocommerce' ); ?></span>
			<select multiple id="alg_wc_pvbur_invisible" name="alg_wc_pvbur_invisible[]">
				<?php echo $all_roles_options; ?>
			</select>
		</label><?php
	}

	/**
	 * add_product_columns.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function add_product_columns( $columns ) {
		$columns[ 'alg_wc_pvbur_user_roles' ] = __( 'User Roles', 'product-visibility-by-user-role-for-woocommerce' );
		return $columns;
	}

	/**
	 * render_product_column.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 * @todo    [dev] (maybe) full role name (instead of ID)
	 * @todo    [dev] (maybe) display "bulk settings"
	 */
	function render_product_column( $column ) {
		if ( 'alg_wc_pvbur_user_roles' === $column ) {
			$html       = '';
			$product_id = get_the_ID();
			if ( $roles = get_post_meta( $product_id, '_' . 'alg_wc_pvbur_visible', true ) ) {
				if ( is_array( $roles ) && ! empty( $roles ) ) {
					$html .= '<span style="color:green;">' . implode( ', ', $roles ) . '</span>';
				}
			}
			if ( $roles = get_post_meta( $product_id, '_' . 'alg_wc_pvbur_invisible', true ) ) {
				if ( is_array( $roles ) && ! empty( $roles ) ) {
					if ( ! empty ( $html ) ) {
						$html .= '<br>';
					}
					$html .= '<span style="color:red;">' . implode( ', ', $roles ) . '</span>';
				}
			}
			echo $html;
		}
	}

	/**
	 * pre_get_posts_hide_invisible_products.
	 *
	 * @version 1.2.1
	 * @since   1.1.9
	 */
	function pre_get_posts_hide_invisible_products( $query ) {
		if ( false === filter_var( apply_filters( 'alg_wc_pvbur_can_search', true, $query ), FILTER_VALIDATE_BOOLEAN ) ) {
			return;
		}

		remove_action( 'woocommerce_product_query', array( $this, 'pre_get_posts_hide_invisible_products' ), PHP_INT_MAX );
		remove_action( 'pre_get_posts', array( $this, 'pre_get_posts_hide_invisible_products' ), PHP_INT_MAX );

		$post__not_in          = $query->get( 'post__not_in' );
		$post__not_in          = empty( $post__not_in ) ? array() : $post__not_in;
		$current_user_roles    = alg_wc_pvbur_get_current_user_all_roles();
		$invisible_product_ids = alg_wc_pvbur_get_invisible_products_ids( $current_user_roles,true );

		if ( is_array( $invisible_product_ids ) && count( $invisible_product_ids ) > 0 ) {
			foreach ( $invisible_product_ids as $invisible_product_id ) {
				$filter = apply_filters( 'alg_wc_pvbur_is_visible', false, $current_user_roles, $invisible_product_id );
				if ( ! filter_var( $filter, FILTER_VALIDATE_BOOLEAN ) ) {
					$post__not_in[] = $invisible_product_id;
				}
			}
		}

		$post__not_in = array_unique( $post__not_in );
		$query->set( 'post__not_in', apply_filters( 'alg_wc_pvbur_post__not_in', $post__not_in, $invisible_product_ids  ) );
		do_action( 'alg_wc_pvbur_hide_products_query', $query, $invisible_product_ids );

		add_action( 'pre_get_posts', array( $this, 'pre_get_posts_hide_invisible_products' ), PHP_INT_MAX );
		add_action( 'woocommerce_product_query', array( $this, 'pre_get_posts_hide_invisible_products' ), PHP_INT_MAX );
	}

	/**
	 * product_by_user_role_purchasable.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 */
	function product_by_user_role_purchasable( $purchasable, $_product ) {
		$current_user_roles = alg_wc_pvbur_get_current_user_all_roles();
		return ( ! alg_wc_pvbur_is_visible( $current_user_roles, $this->get_product_id_or_variation_parent_id( $_product ) ) ? false : $purchasable );
	}

	/**
	 * product_by_user_role_visibility.
	 *
	 * @version 1.1.0
	 * @since   1.0.0
	 */
	function product_by_user_role_visibility( $visible, $product_id ) {
		$current_user_roles = alg_wc_pvbur_get_current_user_all_roles();
		return ( ! alg_wc_pvbur_is_visible( $current_user_roles, $product_id ) ? false : $visible );
	}

	/**
	 * get_product_id_or_variation_parent_id.
	 *
	 * @version 1.0.0
	 * @since   1.0.0
	 */
	function get_product_id_or_variation_parent_id( $_product ) {
		if ( ! $_product || ! is_object( $_product ) ) {
			return 0;
		}
		if ( $this->is_wc_version_below_3 ) {
			return $_product->id;
		} else {
			return ( $_product->is_type( 'variation' ) ) ? $_product->get_parent_id() : $_product->get_id();
		}
	}

}

endif;

return new Alg_WC_PVBUR_Core();
