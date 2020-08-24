<?php
add_action( 'init', 'ws_findeo_pricing_tables_wc_integrateWithVC' );
function ws_findeo_pricing_tables_wc_integrateWithVC() {
  vc_map( array(
    "name" => esc_html__("Pricing table (WC)", 'findeo'),
    "base" => "pricing-tables-wc",
    'icon' => 'findeo_icon',
    'description' => esc_html__( 'WooCommerce Pricing table', 'findeo' ),
    "category" => esc_html__('Findeo', 'findeo'),
    "params" => array(
         array(
          'type' => 'dropdown',
          'heading' => __( 'Order by', 'sphene' ),
          'param_name' => 'orderby',
          'value' => array(
            __( 'Price', 'sphene' ) => 'price',
            __( 'Price desc', 'sphene' ) => 'price-desc',
            __( 'Rating', 'sphene' ) => 'rating',
            __( 'Title', 'sphene' ) => 'title',
            __( 'Popularity', 'sphene' ) => 'popularity',
            __( 'Random', 'sphene' ) => 'random',
            ),
        ),
        array(
            'type' => 'from_vs_indicatior',
            'heading' => esc_html__( 'From Visual Composer', 'findeo' ),
            'param_name' => 'from_vs',
            'value' => 'yes',
            'save_always' => true,
          )
    ),
));
}


?>