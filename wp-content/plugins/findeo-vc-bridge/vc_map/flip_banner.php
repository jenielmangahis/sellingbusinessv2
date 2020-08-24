<?php

/*
 * Iconbox for Visual Composer
 *
 */
add_action( 'init', 'pp_flipbanner_integrateWithVC' );
function pp_flipbanner_integrateWithVC() {
  vc_map( array(
    "name" => esc_html__("Flip Banner","findeo"),
    "base" => "flip-banner",
    'icon' => 'findeo_icon',
    'description' => esc_html__( 'Banner with text on hover', 'findeo' ),
    "category" => esc_html__('Findeo',"findeo"),
    "params" => array(
        array(
          'type' => 'textfield',
          'heading' => esc_html__( 'Visible text', 'findeo' ),
          'param_name' => 'text_visible',
          'description' => esc_html__( '', 'findeo' ),
          'save_always' => true,
          ),          
        array(
          'type' => 'textfield',
          'heading' => esc_html__( 'Text displayed on hover', 'findeo' ),
          'param_name' => 'text_hidden',
          'description' => esc_html__( '', 'findeo' ),
          'save_always' => true,
          ),        
        array(
          'type' => 'textfield',
          'heading' => esc_html__( 'Banner url', 'findeo' ),
          'param_name' => 'url',
          'description' => esc_html__( '', 'findeo' ),
          'save_always' => true,
          ),      
        array(
          'type' => 'attach_image',
          'heading' => esc_html__( 'Background image', 'sphene' ),
          'param_name' => 'background',
          'value' => '',
          'description' => esc_html__( 'Select image from media library.', 'sphene' )
        ),  
        array(
          'type' => 'colorpicker',
          'heading' => esc_html__( 'Overlay color', 'sphene' ),
          'param_name' => 'color',
          'value' => '#274abb',
          'description' => esc_html__( 'Select color.', 'sphene' )
        ),
        array(
          'type' => 'textfield',
          'heading' => esc_html__( 'Opacity', 'sphene' ),
          'param_name' => 'opacity',
          'value' => '0.92', // default value
          'description' => '',
           'save_always' => true,
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