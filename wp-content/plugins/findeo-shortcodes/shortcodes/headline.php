<?php 
	/**
	* Headline shortcode
	* Usage: [headline margin_top="10" margin_bottom="10" type="h3" size="small" centered="yes" with_border="yes"] test [/headline]
	*/

	function findeo_headline( $atts, $content ) {
	  extract(shortcode_atts(array(
		    'margin_top' 	=> 0,
		    'margin_bottom' => 25, 
		    'clearfix' 		=> '',
		    'type' 			=> 'h3',
		    'color' 		=> 'black',
		    'size' 			=> 'standard', //small
		    'font_weight' 	=> 'normal', //small
		    'subtitle' 		=> '', 
		    'boxed' 		=> '', 
		    'with_border'	=> '',
		    'section_title'	=> '', 
		    'custom_class'	=> '', 
		    'font_container'=> '', 
		    'url'			=> '', 
		    'target'		=> '', 
		    'from_vs'		=> '', 

	    ), $atts));
	  $font_container_data = vc_parse_multi_attribute($font_container);
	 
	  	if($from_vs == 'yes'){
	  		$tag = (isset($font_container_data['tag'])) ? $font_container_data['tag'] : $type ;
	  		$style = 'style="';
	  		$style .= (isset($font_weight)) ? 'font-weight:'.$font_weight.';' : '' ;
	  		$style .= (isset($font_container_data['font_size'])) ? 'font-size:'.$font_container_data['font_size'].';' : '' ;
			$style .= (isset($font_container_data['text_align'])) ? 'text-align:'.$font_container_data['text_align'].';' : '' ;
			$style .= (isset($font_container_data['color'])) ? 'color:'.$font_container_data['color'].';' : '' ;
			$style .= (isset($font_container_data['line_height'])) ? 'line-height:'.$font_container_data['line_height'].';' : '' ;
			$style .= '"';
			$css_class = ($boxed) ? 'headline-box ' : ' ' ;
		  	$css_class .= ($with_border) ? 'with-border ' : ' ' ;
		  	$css_class .= ($section_title) ? 'section-title ' : ' ' ;
			$css_class .= $custom_class;
			if(!empty($url)){
				$link = vc_build_link( $url );
		        $a_href = $link['url'];
		        $a_title = $link['title'];
		        $a_target = $link['target'];
			}
			
			$output = '<' . $tag . ' ' . $style . ' class="headline margin-top-'.$margin_top.' margin-bottom-'.$margin_bottom.' '.$css_class.' ">';
			
			if(!empty($url)){
				$output .= '<a class="posts-category-link" href="'.$a_href.'" title="'.esc_attr( $a_title ).'" target="'.$a_target.'">';
			}

			$output .= do_shortcode( $content );
			if(!empty($subtitle)) {
				$output .= '<span>'.$subtitle.'</span>';
			}
			if(!empty($url)){
				$output .= '</a>';
			}
			$output .= '</' .  $tag . '>';
	  	} else {
  	
		  	$css_class = ($centered) ? 'centered ' : ' ' ;
		  	$css_class .= ($with_border) ? 'with-border ' : ' ' ;
		  	$css_class .= ($section_title) ? 'section-title ' : ' ' ;
		  	$css_class .= $size;
		  	$css_class .= ' '.$color;
		  	$css_class .= ' '.$custom_class;
		

		  	$output = '<'.$type.' class="headline margin-top-'.$margin_top.' margin-bottom-'.$margin_bottom.' '.$css_class.' ">';
			if(!empty($url)){
				$output .= '<a class="posts-category-link" href="'.$url.'"  target="'.$target.'">';
			}
		  	$output .= do_shortcode( $content );
		  	if(!empty($url)){
				$output .= '</a>';
			}
		  	$output .= '</'.$type.'>';
		    if($clearfix == 1) {   $output .= '<div class="clearfix"></div>';}
		}
	    return $output;
	}
	
?>