<div id="advanced-ads-selling-setup-head">
	    <div id="advanced-ads-selling-order-details">
                <h3><?php printf( __( 'Setup page for order #%d, %s', 'advanced-ads-selling' ), $order_id, sprintf(__( '%d item(s)', 'advanced-ads-selling' ), count( $items ) ) );?></h3>
		<div class="address"><?php
		if ( $order->get_formatted_billing_address() ) {
			echo '<p><strong>' . __( 'purchased by', 'advanced-ads-selling' ) . ':</strong><br/>' . wp_kses( $order->get_formatted_billing_address(), array( 'br' => array() ) ) . '</p>';
		} ?>
		</div>
            </div>
        </div>
	<div id="advanced-ads-selling-wrapper">
	    <?php // iterate through all ads
	    $item_count = 1;
	    foreach( $items as $_item_key => $_item ) :
		// check if this is an ad product or not
		if( ! isset( $_item['product_id'] ) ){
		    continue;
		}
		$product	= wc_get_product( $_item['product_id'] ); // Get product_details
		if( 'advanced_ad' !== $product->get_type()) {
		    continue;
		}
		
		
		?><div class="advanced-ads-selling-setup-ad-details">
			<h3><?php printf( __( 'Item #%d', 'advanced-ads-selling' ), $item_count++ ); ?></h3>
			<label><?php _e( 'Pricing option', 'advanced-ads-selling' ); ?></label><span><?php echo isset( $_item[ 'ad_pricing_label' ] ) ? $_item[ 'ad_pricing_label' ] : ''; ?></span>
			<?php if( isset( $_item[ 'ad_placement' ] ) ) : ?>
			<label><?php _e( 'Placement', 'advanced-ads-selling' ); ?></label><span><?php echo $_item[ 'ad_placement' ]; ?></span>
			<?php endif; ?>
			<label><?php _e( 'Status', 'advanced-ads-selling' ); ?></label>
		<?php $ad_status = get_post_status( Advanced_Ads_Selling_Order::order_item_id_to_ad_id( $_item_key ) );
		    if( 'publish' === $ad_status ) : ?>
			<p style="color:green;"><?php _e( 'The content of this ad was already accepted and can no longer be changed.', 'advanced-ads-selling' ); ?></p>
		<?php elseif( 'pending' === $ad_status ) : ?>
			<p style="color:orange;"><?php _e( 'This ad is currently in review.', 'advanced-ads-selling' ); ?></p>
		<?php else : ?>
			<p style="color:red;"><?php _e( 'Please complete the ad details so that we can process your order.', 'advanced-ads-selling' ); ?></p>
		    <form enctype="multipart/form-data" method="POST" style="clear: both;">
		    	<?php
		    		if ( !empty( $_POST['errors'] ) ) {
		    			echo '<label class="advanced-ads-selling-error">' . $_POST['errors'] . '</label>';
		    		}
		    	?>
		    	<input type="hidden" value="advanced-ads-selling-upload-ad" name="advanced-ads-selling-upload-ad">
			    <?php $ad_types = isset( $_item[ 'ad_types' ] ) ? explode( ', ', $_item[ 'ad_types' ] ) : array( 'plain' ); ?>
			    <label><?php _e( 'Ad Type', 'advanced-ads-selling' ); ?></label><?php
				if( 1 === count( $ad_types ) ){
				    echo '<div><label><input type="radio" class="advanced-ads-selling-setup-ad-type" name="advads_selling_ad_type" value="' . trim( $ad_types[0] ) . '" checked="checked" />' . $ad_types[ 0 ] . '</label></div>';
				} elseif( count( $ad_types ) ) {
				    echo '<div>';
				    foreach( $ad_types as $_key => $_type ){
					?><label><input type="radio" class="advanced-ads-selling-setup-ad-type" <?php checked( $_key, 0 ); ?> name="advads_selling_ad_type" value="<?php echo trim( $_type ); ?>"/><?php echo $_type; ?></label><?php
				    }
				    echo '</div>';
				}
			    ?>
			    <label id="advanced-ads-selling-setup-ad-details-html-label" class="advanced-ads-selling-setup-ad-details-content"><?php _e( 'Ad Code', 'advanced-ads-selling' ); ?></label>
			    <?php
			    if ( in_array ( 'plain', $ad_types ) ) { ?>
			    <div id="advanced-ads-selling-setup-ad-details-html" class="advanced-ads-selling-setup-ad-details-content">
				<p><?php _e( 'Please enter the ad code. HTML, JavaScript, CSS and plain text are allowed.', 'advanced-ads-selling' ); ?></p>
				<textarea name="advads_selling_ad_content"></textarea>
			    </div>
			    <?php 
			    }
			    if ( in_array ( 'image', $ad_types ) ) {
			    ?>
			    <label id="advanced-ads-selling-setup-ad-details-upload-label" class="advanced-ads-selling-setup-ad-details-content" for="advanced-ads-selling-setup-ad-details-upload-input"><?php _e( 'Image Upload', 'advanced-ads-selling' ); ?></label>
			    <div id="advanced-ads-selling-setup-ad-details-image-upload" class="advanced-ads-selling-setup-ad-details-content">
				<input id="advanced-ads-selling-setup-ad-details-upload-input" type="file" name="advads_selling_ad_image"/>
				<span class="advanced-ads-selling-dile-upload-instrct"><?php _e( 'Max File Size : 1Mb', 'advanced-ads-selling' ); ?></span>
			    </div>
			    <label id="advanced-ads-selling-setup-ad-details-url" class="advanced-ads-selling-setup-ad-details-content" for="advanced-ads-selling-setup-ad-details-url-input"><?php _e( 'Target URL', 'advanced-ads-selling' ); ?></label>
			    <input id="advanced-ads-selling-setup-ad-details-url-input" class="advanced-ads-selling-setup-ad-details-content" type="url" name="advads_selling_ad_url"/>
			    <?php } 
			    do_action( 'advanced-ads-selling-ad-setup-form-types-after', $ad_types, $_item );
			    ?>
			    <?php wp_nonce_field( 'advanced-ads-ad-setup-order-item-' . $_item_key, 'advads_selling_nonce' ); ?>
			    <input type="hidden" name="advads_selling_order_item" value="<?php echo $_item_key; ?>"/>
			    <input type="submit" class="advanced-ads-selling-setup-ad-details-submit button button-primary" value="<?php _e( 'submit this ad', 'advanced-ads-selling' ); ?>"/>
		    </form>
		    <p class="advanced-ads-selling-setup-submit-error message" style="color: red; display: none;"><?php _e( 'The ad could not be submitted. Please try later or contact the site admin.', 'advanced-ads-selling' ); ?></p>
		    <p class="advanced-ads-selling-setup-submit-success message" style="color: green; display: none;"><?php _e( 'The ad was successfully submitted for review.', 'advanced-ads-selling' ); ?></p>
	    <?php endif; ?>
		</div>
	    <?php endforeach; ?>
	</div>