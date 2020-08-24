<?php 

add_action( 'init', 'before_after_integrateWithVC' );
function before_after_integrateWithVC() {

  vc_map( array(
    "name" => esc_html__("Before/after slider", 'sphene'),
    "base" => "before-after",
    'icon' => 'sphene_icon',
    "category" => esc_html__('Sphene', 'sphene'),
    "params" => array(
     array(
        'type' => 'attach_image',
        'heading' => esc_html__( '"Before" image', 'sphene' ),
        'param_name' => 'before',
        'value' => '',
        'description' => esc_html__( 'Select image from media library.', 'sphene' )
      ),     
     array(
        'type' => 'attach_image',
        'heading' => esc_html__( '"After" image', 'sphene' ),
        'param_name' => 'after',
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

?>