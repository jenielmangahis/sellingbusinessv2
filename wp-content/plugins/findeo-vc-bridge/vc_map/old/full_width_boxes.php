<?php

/*
 * Counters for Visual Composer
 *
 */

add_action( 'init', 'sphene_fullwidthboxcontainer_integrateWithVC' );
  function sphene_fullwidthboxcontainer_integrateWithVC() {

    vc_map( array(
      "name" => esc_html__("Full-width boxes wraper", "sphene"),
      "base" => "fwbc",
      "as_parent" => array('only' => 'fwbctext,fwbcimage'), // Use only|except attributes to limit child shortcodes (separate multiple values with comma)
      "content_element" => true,
      "category" => esc_html__('Sphene', 'sphene'),
      'icon' => 'sphene_icon',
      "show_settings_on_create" => false,
      "params" => array(
          // add params same as with any other content element
         array(
              'type' => 'dropdown',
              'heading' => esc_html__( 'Top margin', 'sphene' ),
              'param_name' => 'margin_top',
              'value' => array(
                '0' => '0',
                '10' => '10',
                '15' => '15',
                '20' => '20',
                '25' => '25',
                '30' => '30',
                '35' => '35',
                '40' => '40',
                '45' => '45',
                '50' => '50',
                ),
              'std' => '15',
              'save_always' => true,
              'description' => esc_html__( 'Choose top margin (in px)', 'sphene' )
              ),
            array(
              'type' => 'dropdown',
              'heading' => esc_html__( 'Bottom margin', 'sphene' ),
              'param_name' => 'margin_bottom',
              'value' => array(
                '0' => '0',
                '10' => '10',
                '15' => '15',
                '20' => '20',
                '25' => '25',
                '30' => '30',
                '35' => '35',
                '40' => '40',
                '45' => '45',
                '50' => '50',
                ),
              'std' => '35',
              'save_always' => true,
              'description' => esc_html__( 'Choose bottom margin (in px)', 'sphene' )
              ),
        array(
          'type' => 'from_vs_indicatior',
          'heading' => esc_html__( 'From Visual Composer', 'sphene' ),
          'param_name' => 'from_vs',
          'value' => 'yes',
          'save_always' => true,
          )
        ),
      "js_view" => 'VcColumnView'
      ));


    vc_map( array(
      "name" => esc_html__("FW Box - Text", 'sphene'),
      "base" => "fwbctext",
      'icon' => 'sphene_icon',
      'description' => esc_html__( 'Text box', 'sphene' ),
      "category" => esc_html__('Sphene', 'sphene'),
      "params" => array(
          array(
          'type' => 'textarea_html',
          'heading' => esc_html__( 'Content', 'sphene' ),
          'param_name' => 'content',
          'description' => esc_html__( 'Put here any content', 'sphene' )
          ),  
        array(
        'type' => 'dropdown',
        'heading' => esc_html__( 'Text Color', 'sphene' ),
        'param_name' => 'textcolor',
        'value' => array(
          'dark' => 'dark',
          'light' => 'light',
          ),
        'std' => 'dark',
        'save_always' => true,
        
        ),      

         array(
        'type' => 'colorpicker',
        'heading' => __( 'Background color', 'sphene' ),
        'param_name' => 'bgcolor',
        'value' => '#f6f6f6',
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
    vc_map( array(
      "name" => esc_html__("FW Box - Image", 'sphene'),
      "base" => "fwbcimage",
      'icon' => 'sphene_icon',
      'description' => esc_html__( 'Image box', 'sphene' ),
      "category" => esc_html__('Sphene', 'sphene'),
      "params" => array(
           array(
            'type' => 'attach_image',
            'heading' => esc_html__( 'Side image', 'sphene' ),
            'param_name' => 'image',
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

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
    class WPBakeryShortCode_Fwbc extends WPBakeryShortCodesContainer {
    }
}
if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_Fwbctext extends WPBakeryShortCode {}
    class WPBakeryShortCode_Fwbcimage extends WPBakeryShortCode {}
}
?>