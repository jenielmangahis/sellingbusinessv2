<?php

/*
 * Iconbox for Visual Composer
 *
 */
add_action( 'init', 'pp_iconbox_integrateWithVC' );
function pp_iconbox_integrateWithVC() {
  vc_map( array(
    "name" => esc_html__("Iconbox","findeo"),
    "base" => "iconbox",
    'icon' => 'findeo_icon',
    'description' => esc_html__( 'Small content box with icon', 'findeo' ),
    "category" => esc_html__('Findeo',"findeo"),
    "params" => array(
        array(
          'type' => 'textfield',
          'heading' => esc_html__( 'Title', 'findeo' ),
          'param_name' => 'title',
          'description' => esc_html__( 'Enter text which will be used as title', 'findeo' ),
          'save_always' => true,
          ),      
        array(
          'type' => 'textarea_html',
          'heading' => esc_html__( 'Content', 'findeo' ),
          'param_name' => 'content',
          'description' => esc_html__( 'Enter iconbox content.', 'findeo' ),
          'save_always' => true,
        ),
        array(
          'type' => 'vc_link',
          'heading' => esc_html__( 'URL', 'findeo' ),
          'param_name' => 'url',
          'description' => esc_html__( 'Iconbox 1st link', 'findeo' ),
          'save_always' => true,
        ),            
        array(
          'type' => 'vc_link',
          'heading' => esc_html__( 'URL #2', 'findeo' ),
          'param_name' => 'url2',
          'description' => esc_html__( 'Iconbox 2nd link', 'findeo' ),
          'save_always' => true,
        ),      
        array(
          'type' => 'iconpicker',
          'heading' => esc_html__( 'Icon', 'findeo' ),
          'param_name' => 'icon',
            'settings' => array(
              'type' => 'iconsmind',
              'emptyIcon' => false,
              'iconsPerPage' => 50
              ),
          'description' => esc_html__( 'Icon', 'findeo' ),

        ),
        array(
          'type' => 'dropdown',
          'heading' => esc_html__( 'Type', 'findeo' ),
          'param_name' => 'type',
          'description' => esc_html__( 'Choose size', 'findeo' ),
          'value' => array(
            'box-1'         => 'box-1', 
            'box-1 alternative' => 'box-1 alternative', 
          
            ),
          'std' => 'box-1',
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