<?php

/*
 * Iconbox for Visual Composer
 *
 */
add_action( 'init', 'pp_imagebox_integrateWithVC' );
function pp_imagebox_integrateWithVC() {

$categories =  get_terms( 'region', array(
    'hide_empty' => false,
) );  
$options = array();
if ( ! empty( $categories ) && ! is_wp_error( $categories ) ){
  $options['Select region'] = '';
  foreach ($categories as $cat) {
    $options[$cat->name] = $cat->term_id;
  }
}

$property_features =  get_terms( 'property_feature', array(
    'hide_empty' => false,
) );	
$property_features_options = array();
if ( ! empty( $property_features ) && ! is_wp_error( $property_features ) ){
  $property_features_options['or select feature'] = '';
  foreach ($property_features as $feature) {
  	$property_features_options[$feature->name] = $feature->term_id;
  }
}
  vc_map( array(
    "name" => esc_html__("Imagebox","findeo"),
    "base" => "imagebox",
    'icon' => 'findeo_icon',
    'description' => esc_html__( 'Box displaying custom taxonomy', 'findeo' ),
    "category" => esc_html__('Findeo',"findeo"),
    "params" => array(
     
        array(
          'type' => 'dropdown',
          'heading' => esc_html__( 'category', 'findeo' ),
          'param_name' => 'category',
          'description' => esc_html__( 'Choose region', 'findeo' ),
          'value' => $options,
          'std' => '',
          'save_always' => true,
        ),
        array(
          'type' => 'dropdown',
          'heading' => esc_html__( 'Or Property Feature', 'findeo' ),
          'param_name' => 'property_feature',
          'description' => esc_html__( 'Or Choose Feature', 'findeo' ),
          'value' => $property_features_options,
          'std' => '',
          'save_always' => true,
        ),        
        array(
          'type' => 'vc_link',
          'heading' => esc_html__( 'Or Use Custom Link', 'findeo' ),
          'param_name' => 'url',
          
          'std' => '',
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
          'type' => 'checkbox',
          'heading' => esc_html__( 'Add Featured badge?', 'findeo' ),
          'param_name' => 'featured',
          'save_always' => true,
        ),      
          
        array(
          'type' => 'checkbox',
          'heading' => esc_html__( 'Show counter?', 'findeo' ),
          'param_name' => 'show_counter',
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