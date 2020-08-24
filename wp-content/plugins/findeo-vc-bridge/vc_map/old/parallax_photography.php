<?php 

add_action( 'init', 'ws_sphene_parallax_photography_integrateWithVC' );
function ws_sphene_parallax_photography_integrateWithVC() {

  vc_map( array(
    "name" => esc_html__("Parallax photography","sphene"),
    "base" => "parallax-photography",
    'icon' => 'sphene_icon',
    'description' => esc_html__( 'add desc', 'sphene' ),
    "category" => esc_html__('Sphene',"sphene"),
    "params" => array(

      array(
        'type' => 'textfield',
        'heading' => esc_html__( 'Title', 'sphene' ),
        'param_name' => 'title',
        'value' => ' ', // default value
        'description' => '',
      ),      

      array(
        'type' => 'textfield',
        'heading' => esc_html__( 'Subtitle', 'sphene' ),
        'param_name' => 'subtitle',
        'value' => '', // default value
        'description' => '',
      ),            
 

      array(
        'type' => 'attach_image',
        'heading' => esc_html__( 'Background image', 'sphene' ),
        'param_name' => 'poster',
        'value' => '',
        'description' => esc_html__( 'Select image from media library.', 'sphene' )
      ),
      array(
        'type' => 'file_picker',
        'heading' => esc_html__( 'Webm file', 'sphene' ),
        'param_name' => 'webm',
        'value' => '',
        'description' => esc_html__( 'Select image from media library.', 'sphene' )
      ),
      array(
        'type' => 'file_picker',
        'heading' => esc_html__( 'MP4 file', 'sphene' ),
        'param_name' => 'mp4',
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