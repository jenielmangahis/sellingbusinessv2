<?php
add_action( 'init', 'ws_sphene_pricing_table_integrateWithVC' );
function ws_sphene_pricing_table_integrateWithVC() {
  vc_map( array(
    "name" => esc_html__("Pricing table", 'sphene'),
    "base" => "pricing-table",
    'icon' => 'sphene_icon',
    'description' => esc_html__( 'Pricing table', 'sphene' ),
    "category" => esc_html__('Sphene', 'sphene'),
    "params" => array(
    array(
        'type' => 'dropdown',
        'heading' => esc_html__( 'Type of table', 'sphene' ),
        'param_name' => 'type',
        'save_always' => true,
        'value' => array(
          esc_html__('Standard','sphene') => 'color-1',
          esc_html__('Featured','sphene') => 'color-2',
          ),
        ),
    array(
      'type' => 'colorpicker',
      'heading' => esc_html__( 'Custom color', 'sphene' ),
      'param_name' => 'color',
      'description' => esc_html__( 'Select custom background color for table.', 'sphene' ),
      //'dependency' => array( 'element' => 'bgcolor', 'value' => array( 'custom' ) )
    ),
    array(
      'type' => 'textfield',
      'heading' => esc_html__( 'Title', 'sphene' ),
      'param_name' => 'title',
      'description' => esc_html__( 'Enter text which will be used as widget title. Leave blank if no title is needed.', 'sphene' ),
      'save_always' => true,
      ),
    array(
      'type' => 'textfield',
      'heading' => esc_html__( 'Currency', 'sphene' ),
      'param_name' => 'currency',
      'value' => '$',
      'save_always' => true,
      'description' => esc_html__( 'Enter text which will be used as widget title. Leave blank if no title is needed.', 'sphene' )
      ),
    array(
      'type' => 'textfield',
      'heading' => esc_html__( 'Price', 'sphene' ),
      'param_name' => 'price',
      'value' => '30',
      'save_always' => true,
      ),
    array(
      'type' => 'textfield',
      'heading' => esc_html__( 'Per', 'sphene' ),
      'param_name' => 'per',
      'value' => 'per month',
      'save_always' => true,
      ),
      array(
      'type' => 'textarea_html',
      'heading' => esc_html__( 'Content', 'sphene' ),
      'param_name' => 'content',
      'description' => esc_html__( 'Put here simple UL list', 'sphene' )
      ),
    array(
      'type' => 'textfield',
      'heading' => esc_html__( 'Button URL', 'sphene' ),
      'param_name' => 'buttonlink',
      'value' => ''
      ),
    array(
      'type' => 'textfield',
      'heading' => esc_html__( 'Button text', 'sphene' ),
      'param_name' => 'buttontext',
      'value' => ''
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