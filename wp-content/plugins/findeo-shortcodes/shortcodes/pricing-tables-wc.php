<?php


function findeo_pricing_tables_wc($atts, $content) {
    extract(shortcode_atts(array(
        "orderby" => "price",
        "from_vs" => 'no'
        ), $atts));
    ob_start();
    global $wp_query;

    $args = array(
        'post_type'  => 'product',
        'limit'      => -1,
        'tax_query'  => array(
            array(
                'taxonomy' => 'product_type',
                'field'    => 'slug',
                'terms'    => array( 'property_package')
            )
        ));
    switch ($orderby){
        case 'price':
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = '_price';
            $args['order'] = 'asc';
            break;

        case 'price-desc':
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = '_price';
            $args['order'] = 'desc';
            break;

        case 'rating':
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = '_wc_average_rating';
            $args['order'] = 'desc';
            break;

        case 'popularity':
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = 'total_sales';
            $args['order'] = 'desc';
            break;

        case 'random':
            $args['orderby'] = 'rand';
            $args['order'] = '';
            $args['meta_key'] = '';
            break;    
        case 'title':
            $args['orderby'] = 'title';
            $args['order'] = 'ASC';
            $args['meta_key'] = '';
            break;
    }
    
    
    $products = new WP_Query($args);
?>
    <div class="pricing-container margin-top-30">

    <?php
    while ( $products->have_posts() ) : $products->the_post(); 
            
        $product = wc_get_product( get_post()->ID ); 
        
            if ( ! $product->is_type( array( 'property_package' ) ) || ! $product->is_purchasable() ) {
                    continue;
             }
            ?>
            <div class="plan <?php echo ($product->is_featured()) ? 'featured' : '' ; ?>">
                <?php if( $product->is_featured() ) : ?>
                    <div class="listing-badges">
                        <span class="featured"><?php esc_html_e('Featured','findeo-shortcodes') ?></span>
                    </div>
                <?php endif; ?>

                <div class="plan-price">

                    <h3><?php echo $product->get_title();?></h3>
                    <span class="value"> <?php echo $product->get_price_html(); ?></span>
                    <span class="period"><?php echo $product->get_short_description(); ?></span>
                </div>

                <div class="plan-features">
                    <ul>
                        <?php 
                        $propertieslimit = $product->get_limit();
                        if(!$propertieslimit){
                            echo "<li>";
                             esc_html_e('Unlimited number of properties','findeo-shortcodes'); 
                             echo "</li>";
                        } else { ?>
                            <li>
                                <?php esc_html_e('This plan includes ','findeo-shortcodes'); printf( _n( '%d property', '%s properties', $propertieslimit, 'findeo-shortcodes' ) . ' ', $propertieslimit ); ?>
                            </li>
                        <?php } ?>
                        <?php if( $product->get_duration() ) { ?>
                        <li>
                            <?php esc_html_e('Properties are visible ','findeo-shortcodes'); printf( _n( 'for %s day', 'for %s days', $product->get_duration(), 'findeo-shortcodes' ), $product->get_duration() ); ?>
                        </li>
                        <?php } ?>
                    </ul>
                    <?php 
                       
                        echo $product->get_description();
                        $link   = $product->add_to_cart_url();
                        $label  = apply_filters( 'add_to_cart_text', esc_html__( 'Add to cart', 'findeo-shortcodes' ) );
                
                    ?>
                    <a href="<?php echo esc_url( $link ); ?>" class="button"><i class="fa fa-shopping-cart"></i> <?php echo esc_html($label); ?></a>
           
              
                </div>  
                       
                </div>
          
        <?php endwhile; ?>
        </div>
    <?php $pricing__output =  ob_get_clean();
    wp_reset_postdata();
    return $pricing__output;
}

?>