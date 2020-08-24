<?php

/*
 * Team section for Visual Composer
 *
 */

add_action( 'init', 'sphene_team_integrateWithVC' );
function sphene_team_integrateWithVC() {
  vc_map( array(
    "name" => __("Team section", 'sphene'),
    "base" => "team",
    'icon' => 'sphene_icon',
    "category" => __('Sphene', 'sphene'),
    "params" => array(
      array(
        'type' => 'dropdown',
        'heading' => __( 'Elements to show', 'sphene' ),
        'param_name' => 'per_page',
        'value' => array('1','2','3','4','5','6','7','8','9','10','11','12'),
        'std' => '3'
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
        'std' => '5'
        ),
      array(
        'type' => 'custom_posts_list',
        'heading' => __( 'Include this members', 'sphene' ),
        'param_name' => 'include_posts',
        'settings' => array(
          'post_type' => 'team',
          ),
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
        'type' => 'from_vs_indicatior',
        'heading' => __( 'From Visual Composer', 'sphene' ),
        'param_name' => 'from_vs',
        'value' => 'yes',
        'save_always' => true,
        )
      ),
));
}

?>