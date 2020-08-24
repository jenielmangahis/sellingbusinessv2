<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

?>

<div class="checkout-tabs">
    <ul>
        <li class="checkout-tab tab-active">1. Terms & Conditions</li>
        <li class="checkout-tab">2. Order Summary</li>
        <li class="checkout-tab">3. Order Add-Ons</li>
        <li class="checkout-tab">4. Payment & Shipping</li>
    </ul>
</div>

<div class="t-c">
    <div class="tc-text">
        <h3 style="text-align: center;">Terms &amp; Conditions</h3>
	<?php
$id = 1176;
$p = get_page($id);
echo apply_filters('the_content', $p->post_content);
?>
</div>
<div class="tc text-center">
    <div class="col-xs-12">
        <input id="checkout-tc" class="checkout-tc" type="checkbox" name="agree-tc" value="Agree">
        <label for="checkout-tc" style="display:inline">I understand and accept these terms and conditions.</label>
    </div>
    <div class="agree-tc col-xs-12 col-sm-2 col-sm-offset-5">
        <div class="btn tc-checked" style="display:none">Next ></div>
    </div>
</div>
</div>

<div class="payment-shipping">
<form name="checkout" method="post" class="checkout woocommerce-checkout abc" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">
	
	<div class="bill-ship" style="display:none;">
	    <?php if ( $checkout->get_checkout_fields() ) : ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

		<div class="col2-set" id="customer_details">
			<div class="col-xs-12">
				<?php do_action( 'woocommerce_checkout_billing' ); ?>
			</div>

			<!--<div class="col-2">
				<?php do_action( 'woocommerce_checkout_shipping' ); ?>
			</div>-->
		</div>

		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

	    <?php endif; ?>
	</div>
	
	<div class="order-sum" style="display:none;">

	    <?php do_action( 'woocommerce_checkout_before_order_review' ); ?>
	        
	    <h3 id="order_review_heading"><?php esc_html_e( 'Your order', 'woocommerce' ); ?></h3>

	    <div id="order_review" class="woocommerce-checkout-review-order">
	    
		    <?php do_action( 'woocommerce_checkout_order_review' ); ?>
	
            <div class="agree-sum col-xs-12 col-sm-2 col-sm-offset-5">
                <div class="btn text-center" style="background: #29aaee;">Next ></div>
            </div>
	    </div>
	</div>

	<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>

</div>

<style>
    #customer_details{display:none}
    label.checkbox input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}
label.checkbox {
    display: block;
    position: relative;
    padding-left: 35px;
    margin-bottom: 12px;
    cursor: pointer;
    font-size: 18px;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
}
.checkstyle {
    position: absolute;
    top: 0;
    left: 0;
    height: 25px;
    width: 25px;
    background-color: #fff;
    border: 1px solid #ccc;
}
label.checkbox:hover input ~ .checkstyle {
    background-color: #f0faff;
}
.checkstyle:after {
    content: "";
    position: absolute;
    display: none;
}
label.checkbox input:checked ~ .checkstyle:after {
    display: block;
}
label.checkbox .checkstyle:after {
    left: 7px;
    top: 2px;
    width: 9px;
    height: 15px;
    border: solid #0171c8;
    border-width: 0 3px 3px 0;
    -webkit-transform: rotate(45deg);
    -ms-transform: rotate(45deg);
    transform: rotate(45deg);
}
</style>

<script>
    jQuery('.woocommerce-form-coupon-toggle').hide();
    jQuery('.payment-shipping').hide();
    jQuery('#wc_checkout_add_ons').prepend('<h3 style="margin-bottom: 25px;">Select Optional Order Add-Ons</h3>');
    jQuery('#wc_checkout_add_ons').append('<div class="addon btn text-center" style="background: #29aaee;display:inline;">Next ></div>');
jQuery(document).ready(function(){
    jQuery('input.checkout-tc').change(function(){
        if(this.checked)
            jQuery('.tc-checked').show();
        else
            jQuery('.tc-checked').hide();
        });
    });
    jQuery('.tc-checked').click(function(){
            jQuery('.checkout-tabs .checkout-tab:first-child').removeClass('tab-active');
            jQuery('.checkout-tabs .checkout-tab:nth-child(2)').addClass('tab-active');
            jQuery('.t-c').slideUp();
            jQuery('#payment').hide();
            jQuery('.order-sum').show();
            setTimeout(function(){
                jQuery('.payment-shipping').slideDown();
            },500);
    });
    jQuery('.agree-sum .btn').click(function(){
        jQuery('.checkout-tabs .checkout-tab:nth-child(2)').removeClass('tab-active');
        jQuery('.checkout-tabs .checkout-tab:nth-child(3)').addClass('tab-active');
        jQuery('.agree-sum').slideUp();
        jQuery('.order-sum').slideUp();
        setTimeout(function(){
            jQuery('.bill-ship').slideDown();
        },500);
    });
    jQuery('.addon.btn').click(function(){
        jQuery('.checkout-tabs .checkout-tab:nth-child(3)').removeClass('tab-active');
        jQuery('.checkout-tabs .checkout-tab:last-child').addClass('tab-active');
        jQuery('#wc_checkout_add_ons').slideUp();
        setTimeout(function(){
            jQuery('#customer_details').slideDown();
            jQuery('.order-sum').slideDown();
            jQuery('#payment').show();
        },500);
    });
    jQuery('<span class="checkstyle"></span>').insertAfter('label.checkbox .input-checkbox');
</script>