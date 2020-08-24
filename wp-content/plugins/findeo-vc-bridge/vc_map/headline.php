<?php 


/*
 * Headline for Visual Composer
 *
 */
add_action( 'init', 'pp_headline_integrateWithVC' );
function pp_headline_integrateWithVC() {

  vc_map( array(
    "name" => esc_html__("Headline","findeo"),
    "base" => "headline",
    'icon' => 'findeo_icon',
    'admin_enqueue_js' => array(get_template_directory_uri().'/vc_templates/js/vc_image_caption_box.js'),
    'description' => esc_html__( 'Header', 'findeo' ),
    "category" => esc_html__('Findeo',"findeo"),
    'js_view' => 'VcHeadlineView',
    "params" => array(
      array(
        'type' => 'textfield',
        'heading' => esc_html__( 'Title', 'findeo' ),
        'param_name' => 'content',
        'description' => esc_html__( 'Enter text which will be used as title', 'findeo' )
        ), 
      array(
        'type' => 'textfield',
        'heading' => esc_html__( 'Subtitle', 'findeo' ),
        'param_name' => 'subtitle',
        'description' => esc_html__( 'Optional  subtitle', 'findeo' )
        ),
      array(
          'type' => 'vc_link',
          'heading' => __( 'URL (Link)', 'findeo' ),
          'param_name' => 'url',
          'description' => __( 'Button link.', 'findeo' )
      ),


      array(
        'type' => 'font_container',
        'param_name' => 'font_container',
        'value' => '',
        'settings'=>array(
             'fields'=>array(
                 'tag'=>'h3',
                 'text_align',
                 'font_size',
                 'line_height',
                 'color',
 
                 'tag_description' => __('Select element tag.','findeo'),
                 'text_align_description' => __('Select text alignment.','findeo'),
                 'font_size_description' => __('Enter font size (add scale like px, %, em etc).','findeo'),
                 'line_height_description' => __('Enter line height (add scale like px, %, em etc).','findeo'),
                 'color_description' => __('Select color for your element.','findeo'),
             ),
         ),
      ),
      array(
        'type' => 'dropdown',
        'heading' => esc_html__( 'Font weight', 'findeo' ),
        'param_name' => 'font_weight',
        'value' => array(
          'normal' => 'normal',
          'bold' => 'bold',
          'bolder' => 'bolder',
          'lighter' => 'lighter',
          '100' => '100',
          '200' => '200',
          '300' => '300',
          '400' => '400',
          '500' => '500',
          '600' => '600',
          '700' => '700',
          '800' => '800',
          '900' => '900',
          ),
        'std' => 'normal',
        'save_always' => true,
        'description' => esc_html__( 'Select font-weight', 'findeo' )
        ), 

      array(
        'type' => 'dropdown',
        'heading' => esc_html__( 'Top margin', 'findeo' ),
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
          '55' => '55',
          '60' => '60',
          '65' => '65',
          '70' => '70',
          ),
        'std' => '15',
        'save_always' => true,
        'description' => esc_html__( 'Choose top margin (in px)', 'findeo' )
        ),
      array(
        'type' => 'dropdown',
        'heading' => esc_html__( 'Bottom margin', 'findeo' ),
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
          '55' => '55',
          '60' => '60',
          '65' => '65',
          '70' => '70',
          ),
        'std' => '35',
        'save_always' => true,
        'description' => esc_html__( 'Choose bottom margin (in px)', 'findeo' )
        ),
      array(
        'type' => 'checkbox',
        'heading' => esc_html__( 'Center as a box?', 'findeo' ),
        'param_name' => 'boxed',
        'description' => esc_html__( 'If checked the text will be centered.', 'findeo' ),
        'value' => array( esc_html__( 'Yes', 'findeo' ) => 'yes' )
      ),   
       
      array(
        'type' => 'textfield',
        'heading' => esc_html__( 'Custom Class', 'findeo' ),
        'param_name' => 'custom_class',
        'description' => esc_html__( 'Add Custom Class for headline element', 'findeo' ),
      ),
      array(
        'type' => 'dropdown',
        'heading' => esc_html__( 'Clearfix after?', 'findeo' ),
        'param_name' => 'clearfix',
        'description' => esc_html__( 'Add clearfix after headline, you might want to disable it for some elements, like the recent products carousel.', 'findeo' ),
        'value' => array(
          esc_html__( 'Yes, please', 'findeo' ) => '1',
          esc_html__( 'No, thank you', 'findeo' ) => 'no',
          ),
        'save_always' => true,
        'std' => '1',
        ),
      array(
          'type' => 'from_vs_indicatior',
          'heading' => esc_html__( 'From Visual Composer', 'findeo' ),
          'param_name' => 'from_vs',
          'value' => 'yes',
          'save_always' => true,
          ),    
      ),
  ));
}

?>