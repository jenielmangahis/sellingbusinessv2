<?php 
function findeo_logo_slider($atts) {
		 extract(shortcode_atts(array(
		 	'images' 	=> '', //id or url
	        'from_vs'  	=> '',
	        ), $atts));
	 	 
	
	 	$output = '<div class="logo-carousel dot-navigation">';
 	   	if(!empty($images)){
    		$images = explode(',', $images);
	        foreach ($images as $image) {
	            $logosrc = wp_get_attachment_url( $image );
	            $output .= '<div class="item"><img src="'.$logosrc.'" alt=""/></div>';
	        }
	    }
		$output .= '
		</div>';

	
	  
		return $output;

	} ?>