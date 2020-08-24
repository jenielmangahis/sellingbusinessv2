<?php
add_action( 'init', 'sphene_iconboxwrapper_integrateWithVC' );
  function sphene_iconboxwrapper_integrateWithVC() {

    vc_map( array(
      "name" => esc_html__("Icon Boxes wrapper", "sphene"),
      "base" => "iconboxwrapper",
      "as_parent" => array('only' => 'iconbox'), // Use only|except attributes to limit child shortcodes (separate multiple values with comma)
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
          ),     
        array(
          'type' => 'textfield',
          'heading' => esc_html__( 'Iconbox type', 'sphene' ),
          'param_name' => 'icon',
          'value' => '4',
          'save_always' => true,
          )
        ),
      "js_view" => 'VcColumnView'
      ));

}

if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
    class WPBakeryShortCode_Iconboxwrapper extends WPBakeryShortCodesContainer {
    }
}
if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_Iconbox extends WPBakeryShortCode {
    }
}
?>