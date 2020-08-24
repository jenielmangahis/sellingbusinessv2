<?php 


/*
 * Headline for Visual Composer
 *
 */
add_action( 'init', 'findeo_list_integrateWithVC' );
function findeo_list_integrateWithVC() {

 vc_map( array(
  "name" => esc_html__("List with icons", 'findeo'),
  "base" => "list",
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
      "heading" => esc_html__("Icon", 'findeo'),
      "param_name" => "icon",
      'save_always' => true,
      "value" => array(
        'Square'    => 'list-1',
        'Arrow'   => 'list-2',
        'Arrow 2' => 'list-3',
        'Circle'    => 'list-4',
        ),
      'save_always' => true,
      "description" => ""
    ),

    array(
        'type' => 'checkbox',
        'heading' => esc_html__( 'Colored?', 'findeo' ),
        'param_name' => 'color',
        'description' => esc_html__( 'If checked the icon will have current theme color.', 'findeo' ),
        'value' => array( esc_html__( 'Yes', 'findeo' ) => 'yes' )
      ),     
    array(
        'type' => 'checkbox',
        'heading' => esc_html__( 'Numbered?', 'findeo' ),
        'param_name' => 'numbered',
        'description' => esc_html__( 'If checked the list  will have numbers instead of icons.', 'findeo' ),
        'value' => array( esc_html__( 'Yes', 'findeo' ) => 'yes' )
      ),     
    array(
        'type' => 'checkbox',
        'heading' => esc_html__( 'Filled?', 'findeo' ),
        'param_name' => 'filled',
        'description' => esc_html__( 'If checked the number icon  will have current theme color.', 'findeo' ),
        'value' => array( esc_html__( 'Yes', 'findeo' ) => 'yes' )
      ), 

    ),

));
}

?>