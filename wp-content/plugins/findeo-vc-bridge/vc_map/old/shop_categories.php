<?php
/*
 * Recent project for Visual Composer
 *
 */
add_action( 'init', 'shop_categories_integrateWithVC' );
function shop_categories_integrateWithVC() {
  vc_map( array(
    "name" => __("Shop Categories", 'sphene'),
    "base" => "shop-categories",
    'icon' => 'sphene_icon',
    'description' => __( 'Grid with projects items ', 'sphene' ),
    "category" => __('Sphene',"sphene"),
    "params" => array(
         array(
        'type' => 'dropdown',
        'heading' => __( 'Order by', 'sphene' ),
        'param_name' => 'orderby',
        'value' => array(
          __( 'Date', 'sphene' ) => 'date',
          __( 'Preserve selected categories order', 'sphene' ) => 'include',
          __( 'ID', 'sphene' ) => 'ID',
          __( 'Author', 'sphene' ) => 'author',
          __( 'Title', 'sphene' ) => 'title',
          __( 'Modified', 'sphene' ) => 'modified',
          __( 'Random', 'sphene' ) => 'rand',
          __( 'Comment count', 'sphene' ) => 'comment_count',
          __( 'Menu order', 'sphene' ) => 'menu_order'
          ),
        ),
      array(
        'type' => 'dropdown',
        'heading' => __( 'Order', 'sphene' ),
        'param_name' => 'order',
        'value' => array(
          __( 'Descending', 'sphene' ) => 'DESC',
          __( 'Ascending', 'sphene' ) => 'ASC'
          ),
        'save_always' => true,
        ),
      array(
        'type' => 'dropdown',
        'heading' => __( 'Elements to show', 'sphene' ),
        'param_name' => 'per_page',
        'value' => array(
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
        'save_always' => true,
        'std' => '6'
        ),
array(
        'type' => 'dropdown',
        'heading' => __( 'Thumbnail hover style', 'sphene' ),
        'param_name' => 'style',
        'value' => array(
          '1' => '1',
          '2' => '2',
          '3' => '3',
          '4' => '4',
          '5' => '5',
          '6' => '6',
          '7' => '7',
        
          ),
        'std' => '7'
        ),

      array(
              'type' => 'autocomplete',
              'heading' => __( 'Categories', 'sphene' ),
              'param_name' => 'ids',
              'settings' => array(
                'multiple' => true,
                'sortable' => true,
              ),
              'save_always' => true,
              'description' => __( 'List of product categories', 'sphene' ),
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

add_filter( 'vc_autocomplete_shop-categories_ids_callback',
  'vc_include_product_cat_search', 10, 1 ); // Get suggestion(find). Must return an array

 add_filter( 'vc_autocomplete_shop-categories_ids_render',
  'vc_include_product_cat_render', 10, 1 ); // Render exact product. Must return an array (label,value)



/**
 * @param $search_string
 *
 * @return array
 */
function vc_include_product_cat_search( $search_string ) {

  $data = array();

  $terms = get_terms( 'product_cat',  array(
    'hide_empty' => false,
    'search' => $search_string
  ) );
  if ( is_array( $terms ) && ! empty( $terms ) ) {
    foreach ( $terms as $term ) {
      $data[] = array(
        'value' => $term->term_id,
        'label' => $term->name,
      );
    }
  }

  return $data;
}

/**
 * @param $value
 *
 * @return array|bool
 */
function vc_include_product_cat_render( $value ) {
  $term = get_term( $value['value'],'product_cat' );

  return is_null( $term ) ? false : array(
    'label' => $term->name,
    'value' => $term->term_id,
  );
}

?>