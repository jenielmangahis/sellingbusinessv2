<?php

add_action( 'init', 'sphene_edge_content_integrateWithVC' );

function sphene_edge_content_integrateWithVC() {

  vc_map( array(
    "name" => esc_html__("Edge content", 'sphene'),
    "base" => "edge-content",
    'icon' => 'sphene_icon',
    'description' => esc_html__( 'Content with map/image on the side', 'sphene' ),
    "category" => esc_html__('Sphene', 'sphene'),
    "params" => array(
      array(
      'type' => 'textarea_html',
      'heading' => esc_html__( 'Content', 'sphene' ),
      'param_name' => 'content',
      'description' => esc_html__( 'Put here any content', 'sphene' )
      ),    
      array(
        'type' => 'dropdown',
        'heading' => esc_html__( 'Color', 'sphene' ),
        'param_name' => 'color',
        'value' => array(
          'dark' => 'dark',
          'white' => 'white',
          ),
        'std' => 'white',
        'save_always' => true,
        'description' => esc_html__( 'Content background color', 'sphene' )
        ),      
      array(
        'type' => 'dropdown',
        'heading' => esc_html__( 'Image/Map side', 'sphene' ),
        'param_name' => 'side',
        'value' => array(
          'left'  => 'left',
          'right' => 'right',
          ),
        'std' => 'left',
        'save_always' => true,
        'description' => esc_html__( 'On which side the image/map will be displayed', 'sphene' )
        ),
      array(
        'type' => 'checkbox',
        'heading' => esc_html__( 'Use Map instead of image?', 'sphene' ),
        'param_name' => 'map',
        'description' => esc_html__( 'If checked the box will have map.', 'sphene' ),
        'value' => array( esc_html__( 'Yes', 'sphene' ) => 'yes' ),
        'save_always' => true,
      ), 
      array(
        'type' => 'textfield',
        'heading' => esc_html__( 'Address', 'sphene' ),
        'param_name' => 'address',
        'value' => '', // default value
        'description' => '',
      ), 

      array(
        'type' => 'attach_image',
        'heading' => esc_html__( 'Side image', 'sphene' ),
        'param_name' => 'image',
        'value' => '',
        'description' => esc_html__( 'Select image from media library.', 'sphene' )
      ),      
      array(
        'type' => 'attach_image',
        'heading' => esc_html__( 'Content Background image', 'sphene' ),
        'param_name' => 'bg',
        'value' => '',
        'description' => esc_html__( 'Select image from media library.', 'sphene' )
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