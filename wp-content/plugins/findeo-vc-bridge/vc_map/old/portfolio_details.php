<?php 

add_action( 'init', 'ws_sphene_portfolio_details_integrateWithVC' );
function ws_sphene_portfolio_details_integrateWithVC() {

  vc_map( array(
    "name" => esc_html__("Portfolio Details","sphene"),
    "base" => "portfolio-details",
    'icon' => 'sphene_icon',
    'description' => esc_html__( 'Shows list with pf details', 'sphene' ),
    "category" => esc_html__('Sphene',"sphene"),
    "params" => array(

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