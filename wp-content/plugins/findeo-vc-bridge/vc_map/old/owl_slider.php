<?php 

add_action( 'init', 'owl_slider_integrateWithVC' );
function owl_slider_integrateWithVC() {

  vc_map( array(
    "name" => esc_html__("Owl Slider", 'sphene'),
    "base" => "owl-slider",
    'icon' => 'sphene_icon',
    'description' => esc_html__( 'Images slider', 'sphene' ),
    "category" => esc_html__('Sphene', 'sphene'),
    "params" => array(
     array(
      'type' => 'attach_images',
      'heading' => esc_html__( 'Images', 'sphene' ),
      'param_name' => 'images',
      'value' => '',
      'description' => esc_html__( 'Select images from media library.', 'sphene' )
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