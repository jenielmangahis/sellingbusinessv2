<?php
$output = $title = $el_class = $open = $css_animation = '';
extract(shortcode_atts(array(
    'title' => __("Click to toggle", "findeo"),
    'el_class' => '',
    'type' => '',
    'icon_simpleline' => '',
    'icon_iconsmind' => '',
    'icon_fontawesome' => '',
    'icon_openiconic' => '',
    'icon_typicons' => '',
    'icon_entypo' => '',
    'icon_linecons' => '',
    'icon_monosocial' => '',
    'icon_material' => '',
    'open' => 'false',
    'style' => 'style-1',
    'css_animation' => ''
), $atts));

$el_class = $this->getExtraClass($el_class);
$open = ( $open == 'true' ) ? 'opened' : '';
switch ($type) {
	case 'simpleline':	$icon = $icon_simpleline; break;
	case 'iconsmind':	$icon = $icon_iconsmind; break;
	case 'fontawesome':	$icon = $icon_fontawesome; break;
	case 'openiconic':	$icon = $icon_openiconic; break;
	case 'typicons':	$icon = $icon_typicons; break;
	case 'entypo':		$icon = $icon_entypo; break;
	case 'linecons':	$icon = $icon_linecons; break;
	case 'monosocial':	$icon = $icon_monosocial; break;
	case 'material':	$icon = $icon_material; break;
	
	default:
		# code...
		break;
}

//$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'wpb_toggle' . $open, $this->settings['base'], $atts );
$css_class = $this->getCSSAnimation($css_animation);

if($style == 'style-1'){
	$output .= '<div class="toggle-wrap '.$style.'  '.$css_class.'"><span class="trigger '.$open.'"><a href="#">';
	if(!empty($type) && !empty($icon)){

		$output .= '<i class="'.$icon.'"></i>'; 
	}
	$output .= $title.'</a></span>';

} else {
	$output .= '<div class="toggle-wrap '.esc_attr($style).'  '.esc_attr($css_class).'"><span class="trigger '.esc_attr($open).'"><a href="#">';
		if(!empty($type) && !empty($icon)){
		$output .= '<i class="'.esc_attr($icon).'"></i>'; 
	}
	$output .= $title.'<i class="sl sl-icon-plus"></i></a></span>';
}


$output .= '<div class="toggle-container">'.wpb_js_remove_wpautop($content, true).'</div></div>'.$this->endBlockComment('toggle')."\n";

echo $output; //XSS ok, escaped above 


