<?php 

add_action( 'init', 'logo_slider_integrateWithVC' );
function logo_slider_integrateWithVC() {

  vc_map( array(
    "name" => esc_html__("Logo Slider", 'findeo'),
    "base" => "logo-slider",
    'icon' => 'findeo_icon',
    'description' => esc_html__( 'Logo images slider', 'findeo' ),
    "category" => esc_html__('Findeo', 'findeo'),
    "params" => array(
      array(
        'type' => 'attach_images',
        'heading' => esc_html__( 'Images', 'findeo' ),
        'param_name' => 'images',
        'value' => '',
        'description' => esc_html__( 'Select images from media library.', 'findeo' )
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