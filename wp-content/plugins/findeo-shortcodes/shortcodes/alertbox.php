<?php 
   function findeo_alertbox($atts, $content = null) {
        extract(shortcode_atts(array(
            "type" 		=> 'notice',
            'closeable' => '',
            ), $atts));
        $output = '<div class="notification closeable '.$type.'"><p>'.do_shortcode( $content ).'</p>';
        if($closeable) {
    		$output .= '<a class="close" href="#"></a>';
        }
		$output .= '</div>';
        
        return $output;
    }
    ?>