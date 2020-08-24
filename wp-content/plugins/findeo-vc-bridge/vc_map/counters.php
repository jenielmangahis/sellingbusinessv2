<?php

/*
 * Counters for Visual Composer
 *
 */

add_action( 'init', 'findeo_counterbox_integrateWithVC' );
  function findeo_counterbox_integrateWithVC() {

    vc_map( array(
      "name" => esc_html__("Counters wrapper", "findeo"),
      "base" => "counters",
      "as_parent" => array('only' => 'counter'), // Use only|except attributes to limit child shortcodes (separate multiple values with comma)
      "content_element" => true,
      "category" => esc_html__('Findeo', 'findeo'),
      'icon' => 'findeo_icon',
      "show_settings_on_create" => false,
      "params" => array(
          // add params same as with any other content element
        array(
          "type" => "textfield",
          "heading" => esc_html__("Title", "findeo"),
          "param_name" => "title",
          "description" => esc_html__("Title of the box", "findeo")
          ),
        array(
          'type' => 'attach_image',
          'heading' => esc_html__( 'Background image', 'findeo' ),
          'param_name' => 'background',
          'value' => '',
          'description' => esc_html__( 'Select image from media library.', 'findeo' )
        ),
        array(
          'type' => 'from_vs_indicatior',
          'heading' => esc_html__( 'From Visual Composer', 'findeo' ),
          'param_name' => 'from_vs',
          'value' => 'yes',
          'save_always' => true,
          )
        ),
      "js_view" => 'VcColumnView'
      ));


    vc_map( array(
      "name" => esc_html__("Counter box", 'findeo'),
      "base" => "counter",
      'icon' => 'findeo_icon',
      'description' => esc_html__( 'Animated number counter', 'findeo' ),
      "category" => esc_html__('Findeo', 'findeo'),
      "params" => array(
        array(
          'type' => 'textfield',
          'heading' => esc_html__( 'Title', 'findeo' ),
          'param_name' => 'title',
          'description' => esc_html__( 'Enter text which will be used as title.', 'findeo' )
          ),
       
        array(
          'type' => 'textfield',
          'heading' => esc_html__( 'Value', 'findeo' ),
          'param_name' => 'number',
          'description' => esc_html__( 'Only number (for example 2,147).', 'findeo' )
          ),      

        array(
          'type' => 'textfield',
          'heading' => esc_html__( 'Scale', 'findeo' ),
          'param_name' => 'value',
          'description' => esc_html__( 'Optional. For example %, degrees, k, etc.', 'findeo' )
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
          'type' => 'checkbox',
          'heading' => esc_html__( 'Featured?', 'findeo' ),
          'param_name' => 'colored',
          'description' => esc_html__( 'If checked the box will have current main color background.', 'findeo' ),
          'value' => array( esc_html__( 'Yes', 'findeo' ) => 'yes' ),
          'default' => 'yes',
          'save_always' => true,
        ), 
         array(
          'type' => 'checkbox',
          'heading' => esc_html__( 'Used in Counter wrapper?', 'findeo' ),
          'param_name' => 'in_full_width',
          'description' => esc_html__( 'Please check this box if counter is inside the "Counter wrapper" element', 'findeo' ),
          'value' => array( esc_html__( 'Yes', 'findeo' ) => 'yes' ),
          'default' => 'yes',
          'save_always' => true,
        ), 
        array(
          'type' => 'dropdown',
          'heading' => esc_html__( 'Width of the box', 'findeo' ),
          'param_name' => 'width',
          'description' => esc_html__( 'Applicable if the element is inside the "Counter wrapper" element', 'findeo' ),
          'value' => array(
            esc_html__('Two','findeo') => '2',
            esc_html__('Three','findeo') => '3',
            esc_html__('Four','findeo') => '4',
            ),
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

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
    class WPBakeryShortCode_Counters extends WPBakeryShortCodesContainer {
    }
}
if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_Counter extends WPBakeryShortCode {
    }
}
?>