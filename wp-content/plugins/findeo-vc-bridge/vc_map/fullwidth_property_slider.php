<?php 
if(!function_exists('realteo_get_offer_types_flat')){
  return;
}
add_action( 'init', 'fullwidth_property_slider_integrateWithVC' );
function fullwidth_property_slider_integrateWithVC() {

  $choose_empty = array (__( '-Choose option-', 'findeo' ) => '',);
  vc_map( array(
    "name" => esc_html__("Fullwidth Property Slider", 'findeo'),
    "base" => "fullwidth-property-slider",
    'icon' => 'findeo_icon',
    'description' => esc_html__( 'Properties slider', 'findeo' ),
    "category" => esc_html__('Findeo', 'findeo'),
    "params" => array(
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
        ),
        array(
          'type' => 'dropdown',
          'heading' => __( 'Elements to show', 'sphene' ),
          'param_name' => 'limit',
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
          'heading' => __( 'Order', 'sphene' ),
          'param_name' => 'order',
          'value' => array(
            __( 'Descending', 'sphene' ) => 'DESC',
            __( 'Ascending', 'sphene' ) => 'ASC'
            ),
        ),
      array(
          'type' => 'checkbox',
          'heading' => __( 'Featured properties', 'findeo' ),
          'param_name' => 'featured',
          'description' => __( 'Show only featured properties.', 'findeo' )
        ), 
        array(
          'type' => 'dropdown',
          'heading' => __( 'Offer type', 'findeo' ),
          'param_name' => '_offer_type',
          'value' => $choose_empty + array_flip(realteo_get_offer_types_flat(true)),
        ),        
        array(
          'type' => 'dropdown',
          'heading' => __( 'Property type', 'findeo' ),
          'param_name' => '_property_type',
          'value' => $choose_empty + array_flip(realteo_get_property_types()),

        ),        
      array(
          'type' => 'custom_taxonomy_list',
          'heading' => __( 'Property Feature', 'findeo' ),
          'param_name' => 'feature',
          'taxonomy' => 'property_feature',
          'description' => __( 'Show properties by feature.', 'findeo' )
        ),
      array(
            'type' => 'custom_taxonomy_list',
            'heading' => __( 'Region', 'findeo' ),
            'param_name' => 'region',
            'taxonomy' => 'region',
            'description' => __( 'Show properties from a region.', 'findeo' )
          ),
      array(
          'type' => 'custom_posts_list',
          'heading' => __( 'Properties to include', 'sphene' ),
          'param_name' => 'include_posts',
          'settings' => array(
            'post_type' => 'post',
            ),
          'description' => __( 'Select items, leave empty to use all.', 'sphene' )
        ),
      
      array(
          'type' => 'custom_posts_list',
          'heading' => __( 'Properties to exclude', 'sphene' ),
          'param_name' => 'exclude_posts',
          'settings' => array(
            'post_type' => 'post',
            ),
          'description' => __( 'Select items to exclude from list.', 'sphene' )
        ),

    ),
    ));
}

?>