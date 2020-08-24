<?php 

add_action( 'init', 'address_box_integrateWithVC' );
function address_box_integrateWithVC() {

  vc_map( array(
    "name" => esc_html__("Map address box", 'findeo'),
    "base" => "address-box",
    'admin_enqueue_css' => array(get_template_directory_uri().'/vc_templates/css/findeo_vc_css.css'),
    
    'icon' => 'findeo_icon',
    'description' => esc_html__( 'Full width map with address', 'findeo' ),
    "category" => esc_html__('Findeo', 'findeo'),
    "params" => array(

      array(
        'type' => 'attach_image',
        'heading' => esc_html__( 'Background image for address box', 'findeo' ),
        'param_name' => 'background',
        'value' => '',
        'description' => esc_html__( 'Select image from media library.', 'findeo' )
      ),
      array(
        'type' => 'textfield',
        'heading' => esc_html__( 'Latitude', 'findeo' ),
        'param_name' => 'latitude',
        'value' => '', // default value
        'description' => '',
      ),      
      array(
        'type' => 'textfield',
        'heading' => esc_html__( 'Longitude', 'findeo' ),
        'param_name' => 'longitude',
        'value' => '', // default value
        'description' => '',
      ), 
      array(
          'type' => 'textarea_html',
          'heading' => esc_html__( 'Content', 'findeo' ),
          'param_name' => 'content',
          'description' => esc_html__( 'Enter content.', 'findeo' )
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