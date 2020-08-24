<?php 
   function findeo_list($atts, $content = null) {
        extract(shortcode_atts(array(
            "icon" 		=> 'list-1',
            'color' => '',
            'numbered' => '',
            'filled' => '',

            ), $atts));

            $css_class = ($color) ? 'color ' : ' ' ;
            $css_class .= ($numbered) ? 'numbered ' : ' ' ;
            $css_class .= ($filled) ? 'filled ' : ' ' ;
            if(empty($numbered)){
                $css_class .= $icon;    
            }
            
            
            $output = '<div class="'.$css_class.'">'.do_shortcode( $content ).'</div>';
        return $output;
    }
    ?>