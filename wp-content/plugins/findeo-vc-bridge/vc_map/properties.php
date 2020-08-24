<?php
/*
 * Recent project for Visual Composer
 *

 */
if(!function_exists('realteo_get_offer_types_flat')){
  return;
}

add_action( 'init', 'properties_integrateWithVC' );
function properties_integrateWithVC() {

  
  $choose_empty = array (__( '-Choose option-', 'findeo' ) => '',);
  vc_map( array(
    "name" => __("Properties", 'findeo'),
    "base" => "properties",
    'icon' => 'findeo_icon',
    'description' => __( 'Properties list', 'findeo' ),
    "category" => __('Findeo',"findeo"),
    "params" => array(
        array(
          'type' => 'dropdown',
          'heading' => __( 'Style', 'findeo' ),
          'param_name' => 'list_style',
          'value' => array(
            __( 'List layout', 'findeo' )     => 'list-layout',
            __( 'Grid layout 2 columns', 'findeo' )     => 'grid-layout',
            __( 'Grid layout 3 columns', 'findeo' )     => 'grid-layout-three',
            __( 'Compact layout', 'findeo' )  => 'compact-layout',
            
            ),
        ),   
        array(
          'type' => 'dropdown',
          'heading' => __( 'Layout Switch icons', 'findeo' ),
          'param_name' => 'layout_switch',
          'value' => array(
            __( 'Show', 'findeo' ) => 'on',
            __( 'Hide', 'findeo' ) => 'off'
            ),
        ),
        array(
          'type' => 'textfield',
          'heading' => __( 'Keyword', 'findeo' ),
          'param_name' => 'keyword',
          'description' => __( 'Search by keyword.', 'findeo' ),

        ),   
        array(
          'type' => 'dropdown',
          'heading' => __( 'Order by', 'findeo' ),
          'param_name' => 'orderby',
          'value' => array(
            __( 'Date', 'findeo' ) => 'date',
            __( 'ID', 'findeo' ) => 'ID',
            __( 'Author', 'findeo' ) => 'author',
            __( 'Title', 'findeo' ) => 'title',
            __( 'Modified', 'findeo' ) => 'modified',
            __( 'Random', 'findeo' ) => 'rand',
            __( 'Comment count', 'findeo' ) => 'comment_count',
            __( 'Menu order', 'findeo' ) => 'menu_order'
            ),
        ),
        array(
          'type' => 'dropdown',
          'heading' => __( 'Order', 'findeo' ),
          'param_name' => 'order',
          'value' => array(
            __( 'Descending', 'findeo' ) => 'DESC',
            __( 'Ascending', 'findeo' ) => 'ASC'
            ),
        ),
        array(
          'type' => 'dropdown',
          'heading' => __( 'Elements to show', 'findeo' ),
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
          'type' => 'vc_link',
          'heading' => __( 'Show more button (Link)', 'findeo' ),
          'param_name' => 'more_button',
          'description' => __( 'Setting this option will replace pagination with a button.', 'findeo' )
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
     

/*type*/
      array(
          'type' => 'checkbox',
          'heading' => __( 'Featured properties', 'findeo' ),
          'param_name' => 'featured',
          'description' => __( 'Show only featured properties.', 'findeo' )
        ),      
      array(
          'type' => 'custom_taxonomy_list_by_ids',
          'heading' => __( 'Property Feature', 'findeo' ),
          'param_name' => 'tax-property_feature',
          'taxonomy' => 'property_feature',
          'description' => __( 'Show properties by feature.', 'findeo' )
        ),
      array(
            'type' => 'custom_taxonomy_list_by_ids',
            'heading' => __( 'Region', 'findeo' ),
            'param_name' => 'tax-region',
            'taxonomy' => 'region',
            'description' => __( 'Show properties from a region.', 'findeo' )
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