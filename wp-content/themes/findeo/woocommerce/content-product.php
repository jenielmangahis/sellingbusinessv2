<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}


// Extra post classes
$classes = array();



if($product->is_featured()) { 
	$classes[] = "featured"; 
} 
		
if($product->get_type() == "property_package" ) {
	$classes[] = "plan"; 
}

		
if($product->get_type() == "property_package" ) { ?>

	<li <?php post_class( $classes ); ?>>
		<?php
		if ( has_post_thumbnail() ) {
			$attachment_count = count( $product->get_gallery_attachment_ids() );
			$gallery          = $attachment_count > 0 ? '[product-gallery]' : '';
			$props            = wc_get_product_attachment_props( get_post_thumbnail_id(), $post );
			$image            = get_the_post_thumbnail( $post->ID, apply_filters( 'single_product_large_thumbnail_size', 'shop_single' ), array(
				'title'	 => $props['title'],
				'alt'    => $props['alt'],
			) );
			echo apply_filters(
				'woocommerce_single_product_image_html',
				sprintf(
					'%s',
					
					$image
				),
				$post->ID
			);
		}
		?>
		<?php if( $product->is_featured() ) : ?>
			<div class="listing-badges">
				<span class="featured"><?php esc_html_e('Featured','findeo'); ?></span>
			</div>
		<?php endif; ?>
		<div class="plan-price">
            <h3><?php the_title(); ?></h3>
           	<span class="value"> <?php echo $product->get_price_html(); ?></span>
			<span class="period"><?php echo $product->get_short_description(); ?></span>
        </div>

			

			<div class="plan-features">
                    <ul>
                        <?php 
                        $propertieslimit = $product->get_limit();
                        if(!$propertieslimit){
                            echo "<li>";
                             esc_html_e('Unlimited number of properties','findeo'); 
                             echo "</li>";
                        } else { ?>
                            <li>
                                <?php esc_html_e('This plan includes ','findeo'); printf( _n( '%d property', '%s properties', $propertieslimit, 'findeo' ) . ' ', $propertieslimit ); ?>
                            </li>
                        <?php } ?>
                        <?php if( $product->get_duration() ) { ?>
                        <li>
                            <?php esc_html_e('Properties are visible ','findeo'); printf( _n( 'for %s day', 'for %s days', $product->get_duration(), 'findeo' ), $product->get_duration() ); ?>
                        </li>
                    	<?php } ?>

                    </ul>
                    <?php 
                       
                    	echo $product->get_description();
                   
                    ?>
	
				<?php 
					
					$link 	= $product->add_to_cart_url();
					$label 	= apply_filters( 'add_to_cart_text', esc_html__( 'Add to cart', 'findeo' ) );
				?>
				<a href="<?php echo esc_url( $link ); ?>" class="button"><i class="fa fa-shopping-cart"></i> <?php echo $label; ?></a>
				
			</div>
		
	</li>

	<?php
	} // eof resume or job package 
	else {
		$classes[] = 'regular-product'; ?>
		<li <?php post_class( $classes ); ?>>
		
		<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
		
			<div class="mediaholder">
				<?php
					/**
					 * woocommerce_before_shop_loop_item_title hook
					 *
					 * @hooked woocommerce_show_product_loop_sale_flash - 10
					 * @hooked woocommerce_template_loop_product_thumbnail - 10
					 */
					do_action( 'woocommerce_before_shop_loop_item_title' ); ?>
			</div>

			<section>
				<span class="product-category">
				<?php
					$product_cats = wp_get_post_terms( get_the_ID(), 'product_cat' );
					if ( $product_cats && ! is_wp_error ( $product_cats ) ){
					$single_cat = array_shift( $product_cats );
					echo $single_cat->name;
					} ?>
				</span>

				<h5><?php the_title(); ?></h5>
				<?php
					/**
					 * woocommerce_after_shop_loop_item_title hook
					 *
					 * @hooked woocommerce_template_loop_rating - 5
					 * @hooked woocommerce_template_loop_price - 10
					 */
					do_action( 'woocommerce_after_shop_loop_item_title' );
				?>
			</section>
	

		<?php

			/**
			 * woocommerce_after_shop_loop_item hook
			 *
			 * @hooked woocommerce_template_loop_add_to_cart - 10
			 */
			do_action( 'woocommerce_after_shop_loop_item' );

		?>

	</li>
<?php }
