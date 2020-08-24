<?php 
// File Security Check
if ( ! defined( 'ABSPATH' ) ) { exit; }


/* Widgets list */
$findeo_vc_maps = array(
  'vc_map/headline.php', 
  'vc_map/iconbox.php',
  'vc_map/imagebox.php',
  'vc_map/posts_carousel.php',
  'vc_map/flip_banner.php',
  'vc_map/testimonials.php',
  'vc_map/recent_properties.php',
  'vc_map/pricing_table.php',
  'vc_map/logo_slider.php', 
  'vc_map/fullwidth_property_slider.php', 
  'vc_map/counters.php',
  'vc_map/agents.php',
  'vc_map/agents_carousel.php',
  'vc_map/address_box.php',
  'vc_map/button.php', 
  'vc_map/alertbox.php', 
  'vc_map/list.php', 
  'vc_map/pricing_tables_wc.php', 
  'vc_map/properties.php', 
 
  
);

$findeo_vc_maps = apply_filters( 'findeo_vc_maps', $findeo_vc_maps );
foreach ( $findeo_vc_maps as $findeo_vc_map ) {
  include_once wp_normalize_path( dirname( __FILE__ ) .'/'. $findeo_vc_map );
  
}


/* Adding overlay for vc_row parallax */

vc_add_param('vc_row',array(
  'type' => 'dropdown',
  'heading' => __('Enable Overlay', 'sphene'),
  'param_name' => 'enable_overlay',
   'value' => array( 
        'Off' => 'off', 
        'On' => 'on', 
  ),
  
));
vc_add_param('vc_row',array(
  'type' => 'colorpicker',
  'heading' => __('Color', 'sphene'),
  'param_name' => 'overlay_color',
  'value' => '',
  'dependency' => Array('element' => 'enable_overlay', 'value' => array('on')),
  'description' => __('Select RGBA values or opacity will be set to 20% by default.','sphene')
));

/* from_vs attribute */

function from_vs_indicatior_settings_field($settings, $value) {
  
  return '<div class="from_vs_indicatior_block" >'
  .'<input type="hidden" name="from_vs" class="wpb_vc_param_value wpb-checkboxes '.$settings['param_name'].' '.$settings['type'].'_field" value="yes" /></div>';
}

vc_add_shortcode_param('from_vs_indicatior', 'from_vs_indicatior_settings_field');


/**
 * Add file picker shartcode param.
 *
 * @param array $settings   Array of param seetings.
 * @param int   $value      Param value.
 */
function file_picker_settings_field( $settings, $value ) {
  $output = '';
  $select_file_class = '';
  $remove_file_class = ' hidden';
  $attachment_url = wp_get_attachment_url( $value );
  if ( $attachment_url ) {
    $select_file_class = ' hidden';
    $remove_file_class = '';
  }
  $output .= '<div class="file_picker_block">
                <div class="' . esc_attr( $settings['type'] ) . '_display">' .
                  $attachment_url .
                '</div>
                <input type="hidden" name="' . esc_attr( $settings['param_name'] ) . '" class="wpb_vc_param_value wpb-textinput ' .
                 esc_attr( $settings['param_name'] ) . ' ' .
                 esc_attr( $settings['type'] ) . '_field" value="' . esc_attr( $value ) . '" />
                <button class="button file-picker-button' . $select_file_class . '">Select File</button>
                <button class="button file-remover-button' . $remove_file_class . '">Remove File</button>
              </div>
              ';
  return $output;
}
vc_add_shortcode_param( 'file_picker', 'file_picker_settings_field', get_template_directory_uri() . '/vc_templates/js/file_picker.js' );


/* Adding findeo icons pack to Icon element */

$vc_icon_attributes = array(
        'type' => 'dropdown',
        'heading' => __( 'Icon library', 'findeo' ),
        'value' => array(
          __( 'Simple Line', 'findeo' ) => 'simpleline',
          __( 'Icons Mind', 'findeo' ) => 'iconsmind',
          __( 'Font Awesome', 'findeo' ) => 'fontawesome',
          __( 'Open Iconic', 'findeo' ) => 'openiconic',
          __( 'Typicons', 'findeo' ) => 'typicons',
          __( 'Entypo', 'findeo' ) => 'entypo',
          __( 'Linecons', 'findeo' ) => 'linecons',
          __( 'Mono Social', 'findeo' ) => 'monosocial',
          __( 'Findeo Icons', 'findeo' ) => 'iconsmind',
        ),
        'param_name' => 'type',
        'description' => __( 'Select icon library.', 'findeo' ),
      );

$vc_iconsmind_attributes = 
      array(
        'type' => 'iconpicker',
        'heading' => __( 'Icon', 'findeo' ),
        'param_name' => 'icon_findeo',
       
        'settings' => array(
          'emptyIcon' => false, // default true, display an "EMPTY" icon?
          'type' => 'iconsmind',
          'iconsPerPage' => 100, // default 100, how many icons per/page to display
        ),
        'dependency' => array(
          'element' => 'type',
          'value' => 'iconsmind',
        ),
        'description' => __( 'Select icon from library.', 'findeo' ),
      );
vc_add_param( 'vc_icon', $vc_icon_attributes ); // 
vc_add_param( 'vc_icon', $vc_iconsmind_attributes ); // 


add_filter( 'vc_iconpicker-type-iconsmind', 'vc_iconpicker_type_iconsmind' );
add_filter( 'vc_iconpicker-type-simpleline', 'vc_iconpicker_type_simpleline' );

/* Iconpicker findeo compatibility */

function findeo_vc_icon_style( $font ){
  switch ( $font ) {
    case 'iconsmind':
       wp_enqueue_style( 'findeo-icons' );
      break;
    case 'simpleline':
       wp_enqueue_style( 'findeo-icons' );
      break;
  }
  return $font;
}

add_action('vc_enqueue_font_icon_element','findeo_vc_icon_style');

add_action( 'vc_base_register_front_css', 'findeo_vc_iconpicker_base_register_css' );
add_action( 'vc_base_register_admin_css', 'findeo_vc_iconpicker_base_register_css' );
function findeo_vc_iconpicker_base_register_css(){
    wp_register_style('findeo-icons',  get_template_directory_uri(). '/css/icons.css' );
}

/**
 * Enqueue Backend and Frontend CSS Styles
 */
add_action( 'vc_backend_editor_enqueue_js_css', 'findeo_vc_iconpicker_editor_jscss' );
add_action( 'vc_frontend_editor_enqueue_js_css', 'findeo_vc_iconpicker_editor_jscss' );
function findeo_vc_iconpicker_editor_jscss(){
    wp_enqueue_style( 'findeo-icons' );
}


function custom_taxonomy_list_settings_field($settings, $value) {

  /* setup the post types */
  $taxname = $settings['taxonomy'];
  $value_arr = $value;
  if ( !is_array($value_arr) ) {
    $value_arr = array_map( 'trim', explode(',', $value_arr) );
  }
  $output = '';
  /* query posts array */
  $terms = get_terms( $taxname );
  if ( $terms && !is_wp_error($terms) ) {
    foreach( $terms as $term ) {

      $output .= '<p>';
      $output .= '<input id="'.$settings['param_name'] . '-' . $term->slug.'" class="'. $settings['param_name'].' '.$settings['type'].'" type="checkbox" name="'.$settings['param_name'].'" value="'. $term->slug.'" '.checked( in_array( $term->slug, $value_arr ), true, false ).' />';
      $output .= '<label for="' . $settings['param_name'] . '-' . esc_attr( $term->slug ) . '">' . $term->name . '</label>';
      $output .= '</p>';
    }
  } else {
   $output .= '<p>' . __( 'Nothing Found', 'findeo' ) . '</p>';
 }
 return '
 <div class="custom_taxonomy_list_block">
   <input type="hidden" name="'.$settings['param_name'].'" class="wpb_vc_param_value wpb-checkboxes '.$settings['param_name'].' '.$settings['type'].'_field" value="'.$value.'"  />
   <div class="custom_taxonomy_list_items">'.$output.'</div>
 </div>';
}

vc_add_shortcode_param('custom_taxonomy_list', 'custom_taxonomy_list_settings_field', get_template_directory_uri() . '/vc_templates/js/vc_findeo_vc_scripts.js');



function custom_taxonomy_list_by_ids_settings_field($settings, $value) {

  /* setup the post types */
  $taxname = $settings['taxonomy'];
  $value_arr = $value;
  if ( !is_array($value_arr) ) {
    $value_arr = array_map( 'trim', explode(',', $value_arr) );
  }
  $output = '';
  /* query posts array */
  $terms = get_terms( $taxname );
  if ( $terms && !is_wp_error($terms) ) {
    foreach( $terms as $term ) {

      $output .= '<p>';
      $output .= '<input id="'.$settings['param_name'] . '-' . $term->slug.'" class="'. $settings['param_name'].' '.$settings['type'].'" type="checkbox" name="'.$settings['param_name'].'" value="'. $term->slug.'" '.checked( in_array( $term->slug, $value_arr ), true, false ).' />';
      $output .= '<label for="' . $settings['param_name'] . '-' . esc_attr( $term->slug ) . '">' . $term->name . '</label>';
      $output .= '</p>';
    }
  } else {
   $output .= '<p>' . __( 'Nothing Found', 'findeo' ) . '</p>';
 }
 return '
 <div class="custom_taxonomy_list_block">
   <input type="hidden" name="'.$settings['param_name'].'" class="wpb_vc_param_value wpb-checkboxes '.$settings['param_name'].' '.$settings['type'].'_field" value="'.$value.'"  />
   <div class="custom_taxonomy_list_items">'.$output.'</div>
 </div>';
}

vc_add_shortcode_param('custom_taxonomy_list_by_ids', 'custom_taxonomy_list_by_ids_settings_field', get_template_directory_uri() . '/vc_templates/js/vc_findeo_vc_scripts.js');

/* Google Fonts */

class WPBakeryShortCode_vc_custom_google_fonts extends WPBakeryShortCode {
  /**
   * @param $atts
   * @param null $content
   *
   * @return mixed|void
   */
  protected function content( $atts, $content = null ) {
    // Merge defaults + given attributes
    $atts = vc_map_get_attributes( $this->getShortcode(), $atts );
    $fontsData = $this->getFontsData( $atts, 'google_fonts' );

    $googleFontsStyles = $this->googleFontsStyles( $fontsData );
    $this->enqueueGoogleFonts( $fontsData );

    $style = esc_attr( implode( ';', $googleFontsStyles ) );
    $template = <<<HTML
    <h2 style="$style">$content</h2>
HTML;

    return $template;
  }

  protected function getFontsData( $atts, $paramName ) {
    $googleFontsParam = new Vc_Google_Fonts();
    $field = WPBMap::getParam( $this->shortcode, $paramName );
    $fieldSettings = isset( $field['settings'], $field['settings']['fields'] ) ? $field['settings']['fields'] : array();
    $fontsData = strlen( $atts[ $paramName ] ) > 0 ? $googleFontsParam->_vc_google_fonts_parse_attributes( $fieldSettings, $atts[ $paramName ] ) : '';

    return $fontsData;
  }

  protected function googleFontsStyles( $fontsData ) {
    // Inline styles
    $fontFamily = explode( ':', $fontsData['values']['font_family'] );
    $styles[] = 'font-family:' . $fontFamily[0];
    $fontStyles = explode( ':', $fontsData['values']['font_style'] );
    $styles[] = 'font-weight:' . $fontStyles[1];
    $styles[] = 'font-style:' . $fontStyles[2];

    return $styles;
  }

  protected function enqueueGoogleFonts( $fontsData ) {
    // Get extra subsets for settings (latin/cyrillic/etc)
    $settings = get_option( 'wpb_js_google_fonts_subsets' );
    if ( is_array( $settings ) && ! empty( $settings ) ) {
      $subsets = '&subset=' . implode( ',', $settings );
    } else {
      $subsets = '';
    }

    // We also need to enqueue font from googleapis
    if ( isset( $fontsData['values']['font_family'] ) ) {
      wp_enqueue_style( 'vc_google_fonts_' . vc_build_safe_css_class( $fontsData['values']['font_family'] ), '//fonts.googleapis.com/css?family=' . $fontsData['values']['font_family'] . $subsets );
    }
  }
}


vc_add_shortcode_param('custom_posts_list', 'custom_posts_list_settings_field', get_template_directory_uri() . '/vc_templates/js/vc_findeo_vc_scripts.js');
function custom_posts_list_settings_field($settings, $value) {

  /* setup the post types */
  $post_type = $settings['settings']['post_type'];

  /* query posts array */
  $my_posts = get_posts( array( 'post_type' => $post_type, 'posts_per_page' => -1, 'orderby' => 'title', 'order' => 'ASC', 'post_status' => 'any' ) );
  $value_arr = $value;
  if ( !is_array($value_arr) ) {
    $value_arr = array_map( 'trim', explode(',', $value_arr) );
  }

  $output = '';
  if ( is_array( $my_posts ) && ! empty( $my_posts ) ) {
    foreach( $my_posts as $my_post ) {
      $post_title = '' != $my_post->post_title ? $my_post->post_title : 'Untitled';

      $output .= '<p>';
      $output .= '<input id="'.$settings['param_name'] . '-' . $my_post->ID.'" class="'. $settings['param_name'].' '.$settings['type'].'" type="checkbox" name="'.$settings['param_name'].'" value="'. $my_post->ID.'" '.checked( in_array( $my_post->ID, $value_arr ), true, false ).' />';
      $output .= '<label for="' . $settings['param_name'] . '-' . esc_attr( $my_post->ID ) . '">' . $my_post->post_title . '</label>';
      $output .= '</p>';
    }
  } else {
   $output .= '<p>' . __( 'No Posts Found', 'findeo' ) . '</p>';
 }

 return '<div class="custom_posts_list_block">'
 .'<input type="hidden" name="'.$settings['param_name'].'" class="wpb_vc_param_value wpb-checkboxes '.$settings['param_name'].' '.$settings['type'].'_field" value="'.$value.'"  />'
 .'<div class="custom_posts_list_items">'.$output.'</div></div>';
}

$tabs_style_attributes = array(
    'type' => 'dropdown',
    'heading' => "Style",
    'param_name' => 'style',
    'value' => array( 
        'Findeo 1' => 'tabs-style-1', 
        'Findeo 2' => 'tabs-style-2', 
  
        'Flat' => 'flat', 
        'Classic' => 'classic', 
        'Modern' => 'modern', 
        'Outline' => 'outline', 
    ),
    'description' => __( "New style attribute", "sphene" )
);
vc_add_param( 'vc_tta_tabs', $tabs_style_attributes ); 
     


$vc_toggle_style_attributes = array(
      array(
        'type' => 'dropdown',
        'heading' => __( 'Style', 'sphene' ),
        'param_name' => 'style',
        'value' => array(
          __( 'Default', 'sphene' ) => 'default',
          __( 'Style 1', 'sphene' ) => 'style-1',
          __( 'Style 2', 'sphene' ) => 'style-2',
          __( 'Simple', 'sphene' ) => 'simple',
          __( 'Round', 'sphene' ) => 'round',
          __( 'Round Outline', 'sphene' ) => 'round_outline',
          __( 'Rounded', 'sphene' ) => 'rounded',
          __( 'Rounded Outline', 'sphene' ) => 'rounded_outline',
          __( 'Square', 'sphene' ) => 'square',
          __( 'Square Outline', 'sphene' ) => 'square_outline',
          __( 'Arrow', 'sphene' ) => 'arrow',
          __( 'Text Only', 'sphene' ) => 'text_only',


        ),
        'description' => __( 'Select style.', 'sphene' ),
      ),

      array(
        'type' => 'dropdown',
        'heading' => __( 'Icon library', 'sphene' ),
        'value' => array(
          __( 'Simple Line', 'findeo' ) => 'simpleline',
          __( 'Icons Mind', 'findeo' ) => 'iconsmind',
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
    ); 
vc_add_params( 'vc_toggle', $vc_toggle_style_attributes ); // 



/**
 * @param $search_string
 *
 * @return array
 */
function vc_get_agents_search( $search_string ) {

  $wp_user_query = new WP_User_Query( array( 'search' => $search_string, 'search_columns' => array( 'user_login', 'user_email', 'user_nicename') ) );
  $data = array();
  // Get the results
  $authors = $wp_user_query->get_results();

  if ( ! empty( $authors ) ) {
    foreach ( $authors as $user ) {
      $author_info = get_userdata( $user->ID );
      $data[] = array(
        'value' => $user->ID,
        'label' => $author_info->first_name . ' ' . $author_info->last_name ,
      );
    }
  }

  return $data;
}

/**
 * @param $value
 *
 * @return array|bool
 */
function vc_get_agents_render( $value ) {
  
return ! empty( $value ) ? $value : false;
  
}

function vc_get_properties_search( $search_string ) {
  $query = $search_string;
  $data = array();
  $args = array( 's' => $query, 'post_type' => 'property' );
  $args['vc_search_by_title_only'] = true;
  $args['numberposts'] = - 1;
  if ( strlen( $args['s'] ) === 0 ) {
  unset( $args['s'] );
  }
  add_filter( 'posts_search', 'vc_search_by_title_only', 500, 2 );
  $posts = get_posts( $args );

  if ( is_array( $posts ) && ! empty( $posts ) ) {
  foreach ( $posts as $post ) {
    $data[] = array(
      'value' => $post->ID,
      'label' => $post->post_title,
      'group' => $post->post_type,
    );
  }
  }

  return $data;
}

function vc_get_properties_render( $value  ) {
  $post = get_post( $value['value'] );

  return is_null( $post ) ? false : array(
    'label' => $post->post_title,
    'value' => $post->ID,
    'group' => $post->post_type
  );
}


function vc_iconpicker_type_simpleline( $icons ){
$simpleline_icons = array(
    array( 'sl sl-icon-empty' => 'empty' ),
     array( 'sl sl-icon-user' => 'user'),
     array( 'sl sl-icon-people' => 'people'),
      array( 'sl sl-icon-user-female' => 'user-female'),
      array( 'sl sl-icon-user-follow' => 'user-follow'),
      array( 'sl sl-icon-user-following' => 'user-following'),
      array( 'sl sl-icon-user-unfollow' => 'user-unfollow'),
      array( 'sl sl-icon-login' => 'login'),
      array( 'sl sl-icon-logout' => 'logout'),
      array( 'sl sl-icon-emotsmile' => 'emotsmile'),
      array( 'sl sl-icon-phone' => 'phone'),
      array( 'sl sl-icon-call-end' => 'call-end'),
      array( 'sl sl-icon-call-in' => 'call-in'),
      array( 'sl sl-icon-call-out' => 'call-out'),
      array( 'sl sl-icon-map' => 'map'),
      array( 'sl sl-icon-location-pin' => 'location-pin'),
      array( 'sl sl-icon-direction' => 'direction'),
      array( 'sl sl-icon-directions' => 'directions'),
      array( 'sl sl-icon-compass' => 'compass'),
      array( 'sl sl-icon-layers' => 'layers'),
      array( 'sl sl-icon-menu' => 'menu'),
      array( 'sl sl-icon-list' => 'list'),
      array( 'sl sl-icon-options-vertical' => 'options-vertical'),
      array( 'sl sl-icon-options' => 'options'),
      array( 'sl sl-icon-arrow-down' => 'arrow-down'),
      array( 'sl sl-icon-arrow-left' => 'arrow-left'),
      array( 'sl sl-icon-arrow-right' => 'arrow-right'),
      array( 'sl sl-icon-arrow-up' => 'arrow-up'),
      array( 'sl sl-icon-arrow-up-circle' => 'arrow-up-circle'),
      array( 'sl sl-icon-arrow-left-circle' => 'arrow-left-circle'),
      array( 'sl sl-icon-arrow-right-circle' => 'arrow-right-circle'),
      array( 'sl sl-icon-arrow-down-circle' => 'arrow-down-circle'),
      array( 'sl sl-icon-check' => 'check'),
      array( 'sl sl-icon-clock' => 'clock'),
      array( 'sl sl-icon-plus' => 'plus'),
      array( 'sl sl-icon-minus' => 'minus'),
      array( 'sl sl-icon-close' => 'close'),
      array( 'sl sl-icon-exclamation' => 'exclamation'),
      array( 'sl sl-icon-organization' => 'organization'),
      array( 'sl sl-icon-trophy' => 'trophy'),
      array( 'sl sl-icon-screen-smartphone' => 'screen-smartphone'),
      array( 'sl sl-icon-screen-desktop' => 'screen-desktop'),
      array( 'sl sl-icon-plane' => 'plane'),
      array( 'sl sl-icon-notebook' => 'notebook'),
      array( 'sl sl-icon-mustache' => 'mustache'),
      array( 'sl sl-icon-mouse' => 'mouse'),
      array( 'sl sl-icon-magnet' => 'magnet'),
      array( 'sl sl-icon-energy' => 'energy'),
      array( 'sl sl-icon-disc' => 'disc'),
      array( 'sl sl-icon-cursor' => 'cursor'),
      array( 'sl sl-icon-cursor-move' => 'cursor-move'),
      array( 'sl sl-icon-crop' => 'crop'),
      array( 'sl sl-icon-chemistry' => 'chemistry'),
      array( 'sl sl-icon-speedometer' => 'speedometer'),
      array( 'sl sl-icon-shield' => 'shield'),
      array( 'sl sl-icon-screen-tablet' => 'screen-tablet'),
      array( 'sl sl-icon-magic-wand' => 'magic-wand'),
      array( 'sl sl-icon-hourglass' => 'hourglass'),
      array( 'sl sl-icon-graduation' => 'graduation'),
      array( 'sl sl-icon-ghost' => 'ghost'),
      array( 'sl sl-icon-game-controller' => 'game-controller'),
      array( 'sl sl-icon-fire' => 'fire'),
      array( 'sl sl-icon-eyeglass' => 'eyeglass'),
      array( 'sl sl-icon-envelope-open' => 'envelope-open'),
      array( 'sl sl-icon-envelope-letter' => 'envelope-letter'),
      array( 'sl sl-icon-bell' => 'bell'),
      array( 'sl sl-icon-badge' => 'badge'),
      array( 'sl sl-icon-anchor' => 'anchor'),
      array( 'sl sl-icon-wallet' => 'wallet'),
      array( 'sl sl-icon-vector' => 'vector'),
      array( 'sl sl-icon-speech' => 'speech'),
      array( 'sl sl-icon-puzzle' => 'puzzle'),
      array( 'sl sl-icon-printer' => 'printer'),
      array( 'sl sl-icon-present' => 'present'),
      array( 'sl sl-icon-playlist' => 'playlist'),
      array( 'sl sl-icon-pin' => 'pin'),
      array( 'sl sl-icon-picture' => 'picture'),
      array( 'sl sl-icon-handbag' => 'handbag'),
      array( 'sl sl-icon-globe-alt' => 'globe-alt'),
      array( 'sl sl-icon-globe' => 'globe'),
      array( 'sl sl-icon-folder-alt' => 'folder-alt'),
      array( 'sl sl-icon-folder' => 'folder'),
      array( 'sl sl-icon-film' => 'film'),
      array( 'sl sl-icon-feed' => 'feed'),
      array( 'sl sl-icon-drop' => 'drop'),
      array( 'sl sl-icon-drawer' => 'drawer'),
      array( 'sl sl-icon-docs' => 'docs'),
      array( 'sl sl-icon-doc' => 'doc'),
      array( 'sl sl-icon-diamond' => 'diamond'),
      array( 'sl sl-icon-cup' => 'cup'),
      array( 'sl sl-icon-calculator' => 'calculator'),
      array( 'sl sl-icon-bubbles' => 'bubbles'),
      array( 'sl sl-icon-briefcase' => 'briefcase'),
      array( 'sl sl-icon-book-open' => 'book-open'),
      array( 'sl sl-icon-basket-loaded' => 'basket-loaded'),
      array( 'sl sl-icon-basket' => 'basket'),
      array( 'sl sl-icon-bag' => 'bag'),
      array( 'sl sl-icon-action-undo' => 'action-undo'),
      array( 'sl sl-icon-action-redo' => 'action-redo'),
      array( 'sl sl-icon-wrench' => 'wrench'),
      array( 'sl sl-icon-umbrella' => 'umbrella'),
      array( 'sl sl-icon-trash' => 'trash'),
      array( 'sl sl-icon-tag' => 'tag'),
      array( 'sl sl-icon-support' => 'support'),
      array( 'sl sl-icon-frame' => 'frame'),
      array( 'sl sl-icon-size-fullscreen' => 'size-fullscreen'),
      array( 'sl sl-icon-size-actual' => 'size-actual'),
      array( 'sl sl-icon-shuffle' => 'shuffle'),
      array( 'sl sl-icon-share-alt' => 'share-alt'),
      array( 'sl sl-icon-share' => 'share'),
      array( 'sl sl-icon-rocket' => 'rocket'),
      array( 'sl sl-icon-question' => 'question'),
      array( 'sl sl-icon-pie-chart' => 'pie-chart'),
      array( 'sl sl-icon-pencil' => 'pencil'),
      array( 'sl sl-icon-note' => 'note'),
      array( 'sl sl-icon-loop' => 'loop'),
      array( 'sl sl-icon-home' => 'home'),
      array( 'sl sl-icon-grid' => 'grid'),
      array( 'sl sl-icon-graph' => 'graph'),
      array( 'sl sl-icon-microphone' => 'microphone'),
      array( 'sl sl-icon-music-tone-alt' => 'music-tone-alt'),
      array( 'sl sl-icon-music-tone' => 'music-tone'),
      array( 'sl sl-icon-earphones-alt' => 'earphones-alt'),
      array( 'sl sl-icon-earphones' => 'earphones'),
      array( 'sl sl-icon-equalizer' => 'equalizer'),
      array( 'sl sl-icon-like' => 'like'),
      array( 'sl sl-icon-dislike' => 'dislike'),
      array( 'sl sl-icon-control-start' => 'control-start'),
      array( 'sl sl-icon-control-rewind' => 'control-rewind'),
      array( 'sl sl-icon-control-play' => 'control-play'),
      array( 'sl sl-icon-control-pause' => 'control-pause'),
      array( 'sl sl-icon-control-forward' => 'control-forward'),
      array( 'sl sl-icon-control-end' => 'control-end'),
      array( 'sl sl-icon-volume-1' => 'volume-1'),
      array( 'sl sl-icon-volume-2' => 'volume-2'),
      array( 'sl sl-icon-volume-off' => 'volume-off'),
      array( 'sl sl-icon-calendar' => 'calendar'),
      array( 'sl sl-icon-bulb' => 'bulb'),
      array( 'sl sl-icon-chart' => 'chart'),
      array( 'sl sl-icon-ban' => 'ban'),
      array( 'sl sl-icon-bubble' => 'bubble'),
      array( 'sl sl-icon-camrecorder' => 'camrecorder'),
      array( 'sl sl-icon-camera' => 'camera'),
      array( 'sl sl-icon-cloud-download' => 'cloud-download'),
      array( 'sl sl-icon-cloud-upload' => 'cloud-upload'),
      array( 'sl sl-icon-envelope' => 'envelope'),
      array( 'sl sl-icon-eye' => 'eye'),
      array( 'sl sl-icon-flag' => 'flag'),
      array( 'sl sl-icon-heart' => 'heart'),
      array( 'sl sl-icon-info' => 'info'),
      array( 'sl sl-icon-key' => 'key'),
      array( 'sl sl-icon-link' => 'link'),
      array( 'sl sl-icon-lock' => 'lock'),
      array( 'sl sl-icon-lock-open' => 'lock-open'),
      array( 'sl sl-icon-magnifier' => 'magnifier'),
      array( 'sl sl-icon-magnifier-add' => 'magnifier-add'),
      array( 'sl sl-icon-magnifier-remove' => 'magnifier-remove'),
      array( 'sl sl-icon-paper-clip' => 'paper-clip'),
      array( 'sl sl-icon-paper-plane' => 'paper-plane'),
      array( 'sl sl-icon-power' => 'power'),
      array( 'sl sl-icon-refresh' => 'refresh'),
      array( 'sl sl-icon-reload' => 'reload'),
      array( 'sl sl-icon-settings' => 'settings'),
      array( 'sl sl-icon-star' => 'star'),
      array( 'sl sl-icon-symbol-female' => 'symbol-female'),
      array( 'sl sl-icon-symbol-male' => 'symbol-male'),
      array( 'sl sl-icon-target' => 'target'),
      array( 'sl sl-icon-credit-card' => 'credit-card'),
      array( 'sl sl-icon-paypal' => 'paypal'),
      array( 'sl sl-icon-social-tumblr' => 'social-tumblr'),
      array( 'sl sl-icon-social-twitter' => 'social-twitter'),
      array( 'sl sl-icon-social-facebook' => 'social-facebook'),
      array( 'sl sl-icon-social-instagram' => 'social-instagram'),
      array( 'sl sl-icon-social-linkedin' => 'social-linkedin'),
      array( 'sl sl-icon-social-pinterest' => 'social-pinterest'),
      array( 'sl sl-icon-social-github' => 'social-github'),
      array( 'sl sl-icon-social-google' => 'social-google'),
      array( 'sl sl-icon-social-reddit' => 'social-reddit'),
      array( 'sl sl-icon-social-skype' => 'social-skype'),
      array( 'sl sl-icon-social-dribbble' => 'social-dribbble'),
      array( 'sl sl-icon-social-behance' => 'social-behance'),
      array( 'sl sl-icon-social-foursqare' => 'social-foursqare'),
      array( 'sl sl-icon-social-soundcloud' => 'social-soundcloud'),
      array( 'sl sl-icon-social-spotify' => 'social-spotify'),
      array( 'sl sl-icon-social-stumbleupon' => 'social-stumbleupon'),
      array( 'sl sl-icon-social-youtube' => 'social-youtube'),
      array( 'sl sl-icon-social-dropbox' => 'social-dropbox'),
  );
  return array_merge( $icons, $simpleline_icons );
}