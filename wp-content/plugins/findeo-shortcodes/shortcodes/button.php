<?php 

	function findeo_button($atts, $content = null) {
        extract(shortcode_atts(array(
            "url" => '',
            "color" => 'color',  //gray color light
            "customcolor" => '',
            "iconcolor" => 'white',
            "icon" => '',
            "size" => '',
            "target" => '',
            "customclass" => '',
            "from_vs" => 'no',
            ), $atts));
       if($from_vs == 'yes') {
	        $link = vc_build_link( $url );
	        $a_href = $link['url'];
	        $a_title = $link['title'];
	        $a_target = $link['target'];
	        $output = '<a class="button '.$color.' '.$size.' '.$customclass.'" href="'.$a_href.'" title="'.esc_attr( $a_title ).'"';
	        if(!empty($a_target)){
	        	$output .= 'target="'.$a_target.'"';
	        }
	        
	        if(!empty($customcolor)) { $output .= 'style="background-color:'.$customcolor.'"'; }
	        $output .= '>';
	        if(!empty($icon)) { $output .= '<i class="'.$icon.'  '.$iconcolor.'"></i> '; }
	        $output .= $a_title.'</a>';
	    } else {
	        $output = '<a class="button '.$color.'  '.$size.' '.$customclass.'" href="'.$url.'" ';
	        if(!empty($target)) { $output .= 'target="'.$target.'"'; }
	        if(!empty($customcolor)) { $output .= 'style="background-color:'.$customcolor.'"'; }
	        $output .= '>';
	        if(!empty($icon)) { $output .= '<i class="fa fa-'.$icon.'  '.$iconcolor.'"></i> '; }
	        $output .= $content.'</a>';
	    }

        return $output;  
    }
?>