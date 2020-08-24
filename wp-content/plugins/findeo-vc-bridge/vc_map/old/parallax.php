<?php 

add_action( 'init', 'ws_sphene_parallax_integrateWithVC' );
function ws_sphene_parallax_integrateWithVC() {

  vc_map( array(
    "name" => esc_html__("Parallax","sphene"),
    "base" => "parallax",
    'icon' => 'sphene_icon',
    'description' => esc_html__( 'Shows call-to-action box', 'sphene' ),
    "category" => esc_html__('Sphene',"sphene"),
    "params" => array(

     
      array(
        'type' => 'dropdown',
        'heading' => __( 'Parallax type', 'sphene' ),
        'param_name' => 'type',
        'value' => array(
          __( 'regular', 'sphene' ) => 'regular',
          __( 'video', 'sphene' ) => 'video',
          __( 'photography', 'sphene' ) => 'photography',
          __( 'resume', 'sphene' ) => 'resume',
          ),
        'save_always' => true,
        ),
    
    array(
          'type' => 'textarea_html',
          'heading' => esc_html__( 'Content', 'sphene' ),
          'param_name' => 'content',
          'description' => esc_html__( 'Enter content.', 'sphene' )
        ),
      array(
        'type' => 'textfield',
        'heading' => esc_html__( '"Typed" animated text', 'sphene' ),
        'param_name' => 'typed_text',
        'value' => 'Web Designer, Photographer, Web Developer', // default value
        'description' => '',
        'save_always' => true,
      ),       
      array(
        'type' => 'textfield',
        'heading' => esc_html__( '"Typed" title text', 'sphene' ),
        'param_name' => 'typed_title',
        'value' => 'Professional Freelance', // default value
        'description' => '',
        'save_always' => true,
      ),      

      array(
        'type' => 'attach_image',
        'heading' => esc_html__( 'Background image', 'sphene' ),
        'param_name' => 'background',
        'value' => '',
        'description' => esc_html__( 'Select image from media library.', 'sphene' )
      ),

      array(
        'type' => 'colorpicker',
        'heading' => esc_html__( 'Background color', 'sphene' ),
        'param_name' => 'color',
        'value' => '',
        'description' => esc_html__( 'Select color from background.', 'sphene' )
      ),
      array(
        'type' => 'textfield',
        'heading' => esc_html__( 'Opacity', 'sphene' ),
        'param_name' => 'opacity',
        'value' => '0.92', // default value
        'description' => '',
         'save_always' => true,
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