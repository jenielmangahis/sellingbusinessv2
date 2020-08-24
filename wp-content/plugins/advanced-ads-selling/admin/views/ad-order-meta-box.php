<ul>
	<li><a href="<?php echo get_edit_post_link( $order_id ); ?>"><?php printf( __( 'Order #%d', 'advanced-ads-selling' ), $order_id ); ?></a></li>
	<li><a  href="<?php echo Advanced_Ads_Selling_Plugin::get_instance()->get_ad_setup_url( $hash ); ?>"><?php _e( 'Public Ad Setup URL', 'advanced-ads-selling' ); ?></a></li>
	<?php if( $product->get_id() ) : ?><li><?php _e( 'Product', 'advanced-ads-selling' ); echo ': '; ?><a href="<?php echo get_edit_post_link( $product->get_id() ); ?>"><?php echo $product->get_title(); ?></a></li><?php endif; ?>
	<li><?php _e( 'Placement', 'advanced-ads-selling' ); echo ': ' . wc_get_order_item_meta( $item_id, '_ad_placement' ); ?></li>
	<li><?php _e( 'Sales Type', 'advanced-ads-selling' ); echo ': ' . wc_get_order_item_meta( $item_id, '_ad_sales_type' ); ?></li>
	<li><?php _e( 'Pricing Value', 'advanced-ads-selling' ); echo ': ' . wc_get_order_item_meta( $item_id, '_ad_pricing_option' ); ?></li>
	<li><?php _e( 'Pricing Label', 'advanced-ads-selling' ); echo ': ' . wc_get_order_item_meta( $item_id, '_ad_pricing_label' ); ?></li>
</ul>