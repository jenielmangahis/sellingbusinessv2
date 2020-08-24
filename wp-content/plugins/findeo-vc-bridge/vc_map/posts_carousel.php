<?php
/*
 * Recent project for Visual Composer
 *

 */
add_action( 'init', 'posts_carousel_integrateWithVC' );
function posts_carousel_integrateWithVC() {
  vc_map( array(
    "name" => __("Posts Grid", 'sphene'),
    "base" => "posts-carousel",
    'icon' => 'findeo_icon',
    'description' => __( 'Carousel with posts ', 'sphene' ),
    "category" => __('Findeo',"sphene"),
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
        'std' => '3'
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
        'std' => '4'
        ),
      array(
        'type' => 'custom_posts_list',
        'heading' => __( 'Posts to include', 'sphene' ),
        'param_name' => 'include_posts',
        'settings' => array(
          'post_type' => 'post',
          ),
        'description' => __( 'Select items, leave empty to use all.', 'sphene' )
        ),
      array(
        'type' => 'custom_posts_list',
        'heading' => __( 'Posts to exclude', 'sphene' ),
        'param_name' => 'exclude_posts',
        'settings' => array(
          'post_type' => 'post',
          ),
        'description' => __( 'Select items to exclude from list.', 'sphene' )
        ),

      array(
        'type' => 'custom_taxonomy_list_by_ids',
        'heading' => __( 'Project categories', 'sphene' ),
        'param_name' => 'categories',
        'taxonomy' => 'category',
        'description' => __( 'Select categories from which posts items will be taken.', 'sphene' )
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