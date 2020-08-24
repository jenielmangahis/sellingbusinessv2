<?php 

add_action( 'init', 'ws_sphene_info_banner_integrateWithVC' );
function ws_sphene_info_banner_integrateWithVC() {

  vc_map( array(
    "name" => esc_html__("Info Banner","sphene"),
    "base" => "infobanner",
    'icon' => 'sphene_icon',
    'description' => esc_html__( 'Shows call-to-action box', 'sphene' ),
    "category" => esc_html__('Sphene',"sphene"),
    "params" => array(

      array(
        'type' => 'textfield',
        'heading' => esc_html__( 'Title', 'sphene' ),
        'param_name' => 'title',
        'value' => 'Start Building Your Own Job Board Now ', // default value
        'description' => '',
      ),      
      array(
        'type' => 'vc_link',
        'heading' => esc_html__( 'Button text and URL', 'sphene' ),
        'param_name' => 'url',
        'description' => esc_html__( 'Where button will link.', 'sphene' )
      ),
     array(
        'type' => 'checkbox',
        'heading' => esc_html__( 'Light version', 'sphene' ),
        'param_name' => 'light',
        'description' => esc_html__( 'If checked the text will be displayed on white background with box shadow', 'sphene' ),
        'value' => array( esc_html__( 'Yes', 'sphene' ) => 'light' )
      ),   

      array(
        'type' => 'attach_image',
        'heading' => esc_html__( 'Background image', 'sphene' ),
        'param_name' => 'background_image',
        'value' => '',
        'description' => esc_html__( 'Select image from media library.', 'sphene' )
      ),
      array(
        'type' => 'colorpicker',
        'heading' => esc_html__( 'Background color', 'sphene' ),
        'param_name' => 'background_color',
        'value' => '',
        'description' => esc_html__( 'Select color from background.', 'sphene' )
      ),
      array(
        'type' => 'textfield',
        'heading' => esc_html__( 'Opacity', 'sphene' ),
        'param_name' => 'opacity',
        'value' => '0.7', // default value
        'description' => 'Use value between 0 and 1',
        'save_always' => true,
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