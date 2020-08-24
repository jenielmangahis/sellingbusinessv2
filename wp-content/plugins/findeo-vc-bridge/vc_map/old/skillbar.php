<?php

/*
 * Counters for Visual Composer
 *
 */

add_action( 'init', 'sphene_skillbarwrapper_integrateWithVC' );
  function sphene_skillbarwrapper_integrateWithVC() {

    vc_map( array(
      "name" => esc_html__("Skillbars wrapper", "sphene"),
      "base" => "skillbars",
      "as_parent" => array('only' => 'skillbar'), // Use only|except attributes to limit child shortcodes (separate multiple values with comma)
      "content_element" => true,
      "category" => esc_html__('Sphene', 'sphene'),
      'icon' => 'sphene_icon',
      "show_settings_on_create" => false,
      "params" => array(
          // add params same as with any other content element
         array(
              'type' => 'dropdown',
              'heading' => esc_html__( 'Style', 'sphene' ),
              'param_name' => 'style',
              'value' => array(
                'style-1' => 'style-1',
                'style-2' => 'style-2',
                ),
              'std' => 'style-1',
              'save_always' => true,
              'description' => esc_html__( 'Choose top margin (in px)', 'sphene' )
              ),
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
      "name" => esc_html__("Skillbar", 'sphene'),
      "base" => "skillbar",
      'icon' => 'sphene_icon',
      'description' => esc_html__( 'Skill bar', 'sphene' ),
      "category" => esc_html__('Sphene', 'sphene'),
      "params" => array(
       array(
            'type' => 'textfield',
            'heading' => __( 'Title', 'sphene' ),
            'param_name' => 'title',
            'description' => __( 'Enter text which will be used as title.', 'sphene' )
            ),
       
          array(
            'type' => 'dropdown',
            'heading' => __( 'Skill Level', 'sphene' ),
            'param_name' => 'value',
            'value' => array('0','5','10','15','20','25','30','35','40','45','50','55','60','65','70','75','80','85','90','95','100'),
            'std' => '90'
            ),
 array(
        'type' => 'dropdown',
        'heading' => __( 'Style', 'sphene' ),
        'param_name' => 'style',
        'value' => array(
          __( 'Style 1', 'sphene' ) => 'style-1',
          __( 'Style 2', 'sphene' ) => 'style-2',
        ),
        'description' => __( 'Select style.', 'sphene' ),
      ),

      array(
        'type' => 'dropdown',
        'heading' => __( 'Icon library', 'sphene' ),
        'value' => array(
          __( 'Simple Line', 'sphene' ) => 'simpleline',
          __( 'Icons Mind', 'sphene' ) => 'iconsmind',
          __( 'Font Awesome', 'sphene' ) => 'fontawesome',
          __( 'Open Iconic', 'sphene' ) => 'openiconic',
          __( 'Typicons', 'sphene' ) => 'typicons',
          __( 'Entypo', 'sphene' ) => 'entypo',
          __( 'Linecons', 'sphene' ) => 'linecons',
          __( 'Mono Social', 'sphene' ) => 'monosocial',
          __( 'Material', 'sphene' ) => 'material',
          __( 'No Icon', 'sphene' ) => '',
        ),
        'admin_label' => true,
        'param_name' => 'type',
        'save_always' => true,
        'description' => __( 'Select icon library.', 'sphene' ),
      ),
      array(
        'type' => 'iconpicker',
        'heading' => __( 'Icon', 'sphene' ),
        'param_name' => 'icon_simpleline',
        'value' => '',
        'settings' => array(
          'emptyIcon' => false,
          'iconsPerPage' => 100,
          'type' => 'simpleline',
        ),
        'dependency' => array(
          'element' => 'type',
          'value' => 'simpleline',
        ),
        'description' => __( 'Select icon from library.', 'sphene' ),
      ),     
      array(
        'type' => 'iconpicker',
        'heading' => __( 'Icon', 'sphene' ),
        'param_name' => 'icon_iconsmind',
        'value' => '',
        'settings' => array(
          'emptyIcon' => false,
          'iconsPerPage' => 100,
          'type' => 'iconsmind',
        ),
        'dependency' => array(
          'element' => 'type',
          'value' => 'iconsmind',
        ),
        'description' => __( 'Select icon from library.', 'sphene' ),
      ),     
     array(
        'type' => 'iconpicker',
        'heading' => __( 'Icon', 'sphene' ),
        'param_name' => 'icon_fontawesome',
        'value' => 'fa fa-adjust',
        // default value to backend editor admin_label
        'settings' => array(
          'emptyIcon' => false,
          // default true, display an "EMPTY" icon?
          'iconsPerPage' => 4000,
          // default 100, how many icons per/page to display, we use (big number) to display all icons in single page
        ),
        'dependency' => array(
          'element' => 'type',
          'value' => 'fontawesome',
        ),
        'description' => __( 'Select icon from library.', 'sphene' ),
      ),
      array(
        'type' => 'iconpicker',
        'heading' => __( 'Icon', 'sphene' ),
        'param_name' => 'icon_openiconic',
        'value' => 'vc-oi vc-oi-dial',
        // default value to backend editor admin_label
        'settings' => array(
          'emptyIcon' => false,
          // default true, display an "EMPTY" icon?
          'type' => 'openiconic',
          'iconsPerPage' => 4000,
          // default 100, how many icons per/page to display
        ),
        'dependency' => array(
          'element' => 'type',
          'value' => 'openiconic',
        ),
        'description' => __( 'Select icon from library.', 'sphene' ),
      ),
      array(
        'type' => 'iconpicker',
        'heading' => __( 'Icon', 'sphene' ),
        'param_name' => 'icon_typicons',
        'value' => 'typcn typcn-adjust-brightness',
        // default value to backend editor admin_label
        'settings' => array(
          'emptyIcon' => false,
          // default true, display an "EMPTY" icon?
          'type' => 'typicons',
          'iconsPerPage' => 4000,
          // default 100, how many icons per/page to display
        ),
        'dependency' => array(
          'element' => 'type',
          'value' => 'typicons',
        ),
        'description' => __( 'Select icon from library.', 'sphene' ),
      ),
      array(
        'type' => 'iconpicker',
        'heading' => __( 'Icon', 'sphene' ),
        'param_name' => 'icon_entypo',
        'value' => 'entypo-icon entypo-icon-note',
        // default value to backend editor admin_label
        'settings' => array(
          'emptyIcon' => false,
          // default true, display an "EMPTY" icon?
          'type' => 'entypo',
          'iconsPerPage' => 4000,
          // default 100, how many icons per/page to display
        ),
        'dependency' => array(
          'element' => 'type',
          'value' => 'entypo',
        ),
      ),
      array(
        'type' => 'iconpicker',
        'heading' => __( 'Icon', 'sphene' ),
        'param_name' => 'icon_linecons',
        'value' => 'vc_li vc_li-heart',
        // default value to backend editor admin_label
        'settings' => array(
          'emptyIcon' => false,
          // default true, display an "EMPTY" icon?
          'type' => 'linecons',
          'iconsPerPage' => 4000,
          // default 100, how many icons per/page to display
        ),
        'dependency' => array(
          'element' => 'type',
          'value' => 'linecons',
        ),
        'description' => __( 'Select icon from library.', 'sphene' ),
      ),
      array(
        'type' => 'iconpicker',
        'heading' => __( 'Icon', 'sphene' ),
        'param_name' => 'icon_monosocial',
        'value' => 'vc-mono vc-mono-fivehundredpx',
        // default value to backend editor admin_label
        'settings' => array(
          'emptyIcon' => false,
          // default true, display an "EMPTY" icon?
          'type' => 'monosocial',
          'iconsPerPage' => 4000,
          // default 100, how many icons per/page to display
        ),
        'dependency' => array(
          'element' => 'type',
          'value' => 'monosocial',
        ),
        'description' => __( 'Select icon from library.', 'sphene' ),
      ),
      array(
        'type' => 'iconpicker',
        'heading' => __( 'Icon', 'sphene' ),
        'param_name' => 'icon_material',
        'value' => 'vc-material vc-material-cake',
        // default value to backend editor admin_label
        'settings' => array(
          'emptyIcon' => false,
          // default true, display an "EMPTY" icon?
          'type' => 'material',
          'iconsPerPage' => 4000,
          // default 100, how many icons per/page to display
        ),
        'dependency' => array(
          'element' => 'type',
          'value' => 'material',
        ),
        'description' => __( 'Select icon from library.', 'sphene' ),
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
    class WPBakeryShortCode_Skillbars extends WPBakeryShortCodesContainer {
    }
}
if ( class_exists( 'WPBakeryShortCode' ) ) {
    class WPBakeryShortCode_Skillbar extends WPBakeryShortCode {}

}
?>