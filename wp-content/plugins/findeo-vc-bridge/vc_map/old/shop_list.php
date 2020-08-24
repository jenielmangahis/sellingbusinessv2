<?php
/*
 * Recent project for Visual Composer
 *
 */
add_action( 'init', 'shop_list_integrateWithVC' );
function shop_list_integrateWithVC() {
  vc_map( array(
    "name" => __("Shop List", 'sphene'),
    "base" => "shop-list",
    'icon' => 'sphene_icon',
    'description' => __( 'List with  products items ', 'sphene' ),
    "category" => __('Sphene',"sphene"),
    "params" => array(
      array(
        'type' => 'dropdown',
        'heading' => __( 'Type', 'sphene' ),
        'param_name' => 'type', //'recent', //featured // best-sellers //top-rated  // on sale
        'value' => array(
          __( 'Recent', 'sphene' ) => 'recent',
          __( 'Featured', 'sphene' ) => 'featured',
          __( 'Best Sellers', 'sphene' ) => 'best-sellers',
          __( 'Top Rated', 'sphene' ) => 'top-rated',
          __( 'On sale', 'sphene' ) => 'on-sale',
          ),
        'save_always' => true,
        ),
      array(
        'type' => 'dropdown',
        'heading' => __( 'Order by', 'sphene' ),
        'param_name' => 'orderby',
        'value' => array(
          __( 'Date', 'sphene' ) => 'date',
          __( 'ID', 'sphene' ) => 'ID',
          __( 'Author', 'sphene' ) => 'author',
          __( 'Title', 'sphene' ) => 'title',
          __( 'Modified', 'sphene' ) => 'modified',
          __( 'Random', 'sphene' ) => 'rand',
          __( 'Comment count', 'sphene' ) => 'comment_count',
          __( 'Menu order', 'sphene' ) => 'menu_order'
          ),
        'save_always' => true,
        ),
      array(
        'type' => 'dropdown',
        'heading' => __( 'Order', 'sphene' ),
        'param_name' => 'order',
        'value' => array(
          __( 'Descending', 'sphene' ) => 'DESC',
          __( 'Ascending', 'sphene' ) => 'ASC'
          ),
        ),
      array(
        'type' => 'dropdown',
        'heading' => __( 'Elements to show', 'sphene' ),
        'param_name' => 'per_page',
        'value' => array(
          '1' => '1',
          '2' => '2',
          '3' => '3',
          '4' => '4',
          '5' => '5',
          '6' => '6',
          '7' => '7',
          '8' => '8',
          '9' => '9',
          '10' => '10',
          '11' => '11',
          '12' => '12',
          ),
        'std' => '6'
        ),

     
      array(
        'type' => 'custom_taxonomy_list',
        'heading' => __( 'Product categories', 'sphene' ),
        'param_name' => 'ids',
        'taxonomy' => 'product_cat',
        'description' => __( 'Select categories from which portfolio items will be taken.', 'sphene' )
        ),
       array(
          'type' => 'from_vs_indicatior',
          'heading' => esc_html__( 'From Visual Composer', 'sphene' ),
          'param_name' => 'from_vs',
          'value' => 'yes',
          'save_always' => true,
        )
      ),

));
}
?>