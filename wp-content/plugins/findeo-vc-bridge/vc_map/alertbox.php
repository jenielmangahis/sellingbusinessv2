<?php 


/*
 * Headline for Visual Composer
 *
 */
add_action( 'init', 'findeo_alertbox_integrateWithVC' );
function findeo_alertbox_integrateWithVC() {

 vc_map( array(
  "name" => esc_html__("Notification box", 'findeo'),
  "base" => "alertbox",
  'icon' => 'findeo_icon',
  "category" => esc_html__('Findeo', 'findeo'),
  "params" => array(
    array(
      'type' => 'textarea_html',
      'heading' => esc_html__( 'Content', 'findeo' ),
      'param_name' => 'content',
      'description' => esc_html__( 'Enter message content.', 'findeo' )
      ),

    array(
      "type" => "dropdown",
      "class" => "",
      "heading" => esc_html__("Box type", 'findeo'),
      "param_name" => "type",
      'save_always' => true,
      "value" => array(
        'Error' => 'error',
        'Success' => 'success',
        'Warning' => 'warning',
        'Notice' => 'notice',
        ),
      "description" => "",
      'save_always' => true,
    ),
    array(
        'type' => 'checkbox',
        'heading' => esc_html__( 'Closeable?', 'findeo' ),
        'param_name' => 'closeable',
        'description' => esc_html__( 'If checked the box will have close button.', 'findeo' ),
        'value' => array( esc_html__( 'Yes', 'findeo' ) => 'yes' )
      ), 

    ),

));
}

?>