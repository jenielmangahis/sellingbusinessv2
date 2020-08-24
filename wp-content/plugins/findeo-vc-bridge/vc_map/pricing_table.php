<?php
add_action( 'init', 'ws_findeo_pricing_table_integrateWithVC' );
function ws_findeo_pricing_table_integrateWithVC() {
  vc_map( array(
    "name" => esc_html__("Pricing table", 'findeo'),
    "base" => "pricing-table",
    'icon' => 'findeo_icon',
    'description' => esc_html__( 'Pricing table', 'findeo' ),
    "category" => esc_html__('Findeo', 'findeo'),
    "params" => array(
        array(
            'type' => 'dropdown',
            'heading' => esc_html__( 'Type of table', 'findeo' ),
            'param_name' => 'type',
            'save_always' => true,
            'value' => array(
              esc_html__('Standard','findeo') => 'color-1',
              esc_html__('Featured','findeo') => 'featured',
              ),
            ),
        array(
          'type' => 'colorpicker',
          'heading' => esc_html__( 'Custom color', 'findeo' ),
          'param_name' => 'color',
          'description' => esc_html__( 'Select custom background color for table.', 'findeo' ),
          //'dependency' => array( 'element' => 'bgcolor', 'value' => array( 'custom' ) )
        ),
        array(
          'type' => 'textfield',
          'heading' => esc_html__( 'Title', 'findeo' ),
          'param_name' => 'title',
          'description' => esc_html__( 'Enter text which will be used as widget title. Leave blank if no title is needed.', 'findeo' ),
          'save_always' => true,
          ),       
        array(
          'type' => 'textfield',
          'heading' => esc_html__( 'Subtitle', 'findeo' ),
          'param_name' => 'subtitle',
          'description' => esc_html__( 'Enter text which will be used as a subtitle. Leave blank if not needed.', 'findeo' ),
          'save_always' => true,
          ),
        
        array(
          'type' => 'textfield',
          'heading' => esc_html__( 'Price', 'findeo' ),
          'param_name' => 'price',
          'value' => '30',
          'save_always' => true,
          ),
        array(
          'type' => 'textfield',
          'heading' => esc_html__( 'Per', 'findeo' ),
          'param_name' => 'per',
          'value' => 'per month',
          'save_always' => true,
          ),
        array(
          'type' => 'textarea_html',
          'heading' => esc_html__( 'Content', 'findeo' ),
          'param_name' => 'content',
          'description' => esc_html__( 'Put here simple UL list', 'findeo' )
          ),
        array(
          'type' => 'textfield',
          'heading' => esc_html__( 'Button URL', 'findeo' ),
          'param_name' => 'buttonlink',
          'value' => ''
          ),
        array(
          'type' => 'textfield',
          'heading' => esc_html__( 'Button text', 'findeo' ),
          'param_name' => 'buttontext',
          'value' => ''
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


add_action( 'init', 'findeo_pricingwrapper_integrateWithVC' );
  function findeo_pricingwrapper_integrateWithVC() {

    vc_map( array(
      "name" => esc_html__("Pricing Table wrapper", "findeo"),
      "base" => "pricingwrapper",
      "as_parent" => array('only' => 'pricing-table'), // Use only|except attributes to limit child shortcodes (separate multiple values with comma)
      "content_element" => true,
      "category" => esc_html__('Findeo', 'findeo'),
      "show_settings_on_create" => false,

      "js_view" => 'VcColumnView'
      ));

}

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
    class WPBakeryShortCode_Pricingwrapper extends WPBakeryShortCodesContainer {
    }
}
if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_Pricing_Table extends WPBakeryShortCode {
    }
}
?>