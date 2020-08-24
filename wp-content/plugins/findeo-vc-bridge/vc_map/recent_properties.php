<?php
/*
 * Recent project for Visual Composer
 *

 */
if(!function_exists('realteo_get_offer_types_flat')){
  return;
}

add_action( 'init', 'recent_properties_integrateWithVC' );
function recent_properties_integrateWithVC() {
  
  $choose_empty = array (__( '-Choose option-', 'findeo' ) => '',);
  vc_map( array(
    "name" => __("Recent Properties", 'findeo'),
    "base" => "recent-properties",
    'icon' => 'findeo_icon',
    'description' => __( 'Carousel with posts ', 'findeo' ),
    "category" => __('Findeo',"findeo"),
    "params" => array(
        array(
          'type' => 'dropdown',
          'heading' => __( 'Grid style', 'findeo' ),
          'param_name' => 'layout',
          'value' => array(
            __( 'Standard', 'findeo' ) => 'standard',
            __( 'Compact', 'findeo' ) => 'compact',
            
            ),
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
          'heading' => __( 'Elements to show', 'findeo' ),
          'param_name' => 'limit',
          'value' => array(
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
          'save_always' => true,
          'std' => '6'
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
          'type' => 'autocomplete',
          'heading' => __( 'Properties to include', 'findeo' ),
          'param_name' => 'include_posts',
          'settings'    => array(
            'multiple' => true,
            'sortable' => true,
            'no_hide' => true, // In UI after select doesn't hide an select list
            'groups' => true, // In UI show results grouped by groups
            'unique_values' => true, // In UI show results except selected. NB! You should manually check values in backend
            'display_inline' => true, // In UI show results inline view
          ),
          'description' => __( 'Select items, leave empty to use all.', 'findeo' )
        ),
      
      array(
          'type' => 'autocomplete',
          'heading' => __( 'Properties to exclude', 'findeo' ),
          'param_name' => 'exclude_posts',
          'settings'    => array(
            'multiple' => true,
            'sortable' => true,
            'no_hide' => true, // In UI after select doesn't hide an select list
            'groups' => true, // In UI show results grouped by groups
            'unique_values' => true, // In UI show results except selected. NB! You should manually check values in backend
            'display_inline' => true, // In UI show results inline view
          ),
          'description' => __( 'Select items to exclude from list.', 'findeo' )
        ),
/*type*/
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
          'type' => 'custom_taxonomy_list_by_ids',
          'heading' => __( 'Property Feature', 'findeo' ),
          'param_name' => 'feature',
          'taxonomy' => 'property_feature',
          'description' => __( 'Show properties by feature.', 'findeo' )
        ),
      array(
            'type' => 'custom_taxonomy_list_by_ids',
            'heading' => __( 'Region', 'findeo' ),
            'param_name' => 'region',
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

                        // 
add_filter( 'vc_autocomplete_recent-properties_include_posts_callback',
  'vc_get_properties_search', 10, 1 ); // Get suggestion(find). Must return an array
                                       // 

 add_filter( 'vc_autocomplete_recent-properties_include_posts_render',
  'vc_get_properties_render', 10, 1 ); // Render exact product. Must return an array (label,value)
                                   
add_filter( 'vc_autocomplete_recent-properties_exclude_posts_callback',
  'vc_get_properties_search', 10, 1 ); // Get suggestion(find). Must return an array

 add_filter( 'vc_autocomplete_recent-properties_exclude_posts_render',
  'vc_get_properties_render', 10, 1 ); // Render exact product. Must return an array (label,value)

?>