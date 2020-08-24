<?php 

add_action( 'init', 'clients_carousel_integrateWithVC' );
function clients_carousel_integrateWithVC() {

  vc_map( array(
    "name" => esc_html__("Client logos carousel", 'sphene'),
    "base" => "clients-carousel",
    'icon' => 'sphene_icon',
    'description' => esc_html__( 'Carousel with logo images', 'sphene' ),
    "category" => esc_html__('Sphene', 'sphene'),
    "params" => array(
     array(
      'type' => 'attach_images',
      'heading' => esc_html__( 'Clients logos', 'sphene' ),
      'param_name' => 'logos',
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