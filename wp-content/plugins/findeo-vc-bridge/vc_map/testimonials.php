<?php 

/*
 * Testimonials for Visual Composer
 *
 */
add_action( 'init', 'findeo_testimonials_wide_integrateWithVC' );
function findeo_testimonials_wide_integrateWithVC() {
  vc_map( array(
    "name" => __("Testimonials", 'findeo'),
    "base" => "testimonials",
    'icon' => 'findeo_icon',
    'description' => __( 'Testimonials carousel', 'findeo' ),
    "category" => __('Findeo',"findeo"),
    "params" => array(
         array(
          'type' => 'textfield',
          'heading' => esc_html__( 'Title', 'findeo' ),
          'param_name' => 'title',
          'description' => esc_html__( 'Enter text which will be used as title', 'findeo' ),
          'save_always' => true,
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
        'save_always' => true,
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
        'std' => '6'
        ),
      array(
        'type' => 'dropdown',
        'heading' => __( 'Text color', 'findeo' ),
        'param_name' => 'textcolor',
        'value' => array(
          __( 'Light', 'findeo' ) => 'light',
          __( 'Dark', 'findeo' ) => 'dark'
          ),
        'save_always' => true,
        ),
      

      array(
        'type' => 'custom_posts_list',
        'heading' => __( 'Testimonials items to include', 'findeo' ),
        'param_name' => 'include_posts',
        'settings' => array(
          'post_type' => 'testimonial',
          ),
        'description' => __( 'Select items, leave empty to use all.', 'findeo' )
        ),
      array(
        'type' => 'custom_posts_list',
        'heading' => __( 'Testimonials to exclude', 'findeo' ),
        'param_name' => 'exclude_posts',
        'settings' => array(
          'post_type' => 'testimonial',
          ),
        'description' => __( 'Select items to exclude from list.', 'findeo' )
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