<?php 

add_action( 'init', 'clients_card_integrateWithVC' );
function clients_card_integrateWithVC() {

  vc_map( array(
    "name" => esc_html__("Client card", 'sphene'),
    "base" => "client-card",
    'icon' => 'sphene_icon',
    'description' => esc_html__( 'Testiomonial box from client', 'sphene' ),
    "category" => esc_html__('Sphene', 'sphene'),
    "params" => array(
     array(
      'type' => 'custom_posts_list',
      'heading' => esc_html__( 'Testimony', 'sphene' ),
      'param_name' => 'id',
      'value' => '',
        'settings' => array(
          'post_type' => 'testimonial',
          ),
      'description' => esc_html__( 'Select testimony.', 'sphene' )
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