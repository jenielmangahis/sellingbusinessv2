<?php 

 /*
 * Button Block Visual Composer
 *
 */

add_action( 'init', 'findeo_button_integrateWithVC' );
function findeo_button_integrateWithVC() {
  
  vc_map( array(
    "name" => __("Button (findeo)", 'findeo'),
    "base" => "button",
    'icon' => 'findeo_icon',
    'description' => __( 'findeo styled button', 'findeo' ),
    "category" => __('Findeo', 'findeo'),
    "params" => array(

        array(
          'type' => 'vc_link',
          'heading' => __( 'URL (Link)', 'findeo' ),
          'param_name' => 'url',
          'description' => __( 'Button link.', 'findeo' )
        ),
      array(
        'type' => 'dropdown',
        'heading' => __( 'Button color', 'findeo' ),
        'param_name' => 'color',
        'save_always' => true,
        'value' => array(
          __( 'Current main color', 'findeo' ) => 'color',
          __( 'Border color', 'findeo' )  => 'border',
          ),
        ),      

      array(
        'type' => 'colorpicker',
        'heading' => __( 'Custom color', 'findeo' ),
        'param_name' => 'customcolor',
        ),
      array(
        'type' => 'dropdown',
        'heading' => __( 'Icon color', 'findeo' ),
        'param_name' => 'color',
        'value' => array(
          __( 'White', 'findeo' ) => 'white',
          __( 'Black', 'findeo' ) => 'black',
          ),
        'save_always' => true,
        ),      

      array(
          'type' => 'iconpicker',
          'heading' => esc_html__( 'Icon', 'findeo' ),
          'param_name' => 'icon',
          'description' => esc_html__( 'Icon', 'findeo' ),
      ),
      array(
        'type' => 'textfield',
        'heading' => __( 'Custom class', 'findeo' ),
        'param_name' => 'customclass',
        'description' =>  __( 'Optional', 'findeo' ),
        ),
      array(
        'type' => 'from_vs_indicatior',
        'heading' => __( 'From Visual Composer', 'findeo' ),
        'param_name' => 'from_vs',
        'value' => 'yes',
        'save_always' => true,
        )
      ),
));
}

?>