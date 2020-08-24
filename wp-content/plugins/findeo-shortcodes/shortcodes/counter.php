<?php 
	function findeo_counter( $atts, $content ) {
	   extract(shortcode_atts(array(
	            'title' => 'Resumes Posted',
	            'number' => '768',
	            'scale' => '',
	            'colored' => '',
	            'icon' => '',
	            'from_vs' => '',
	            'width' => '3',
	            'in_full_width' => 'yes',

	    ), $atts));
	    $output = '';

	    if($from_vs === 'yes' && $in_full_width === 'yes') {
	        $output .= '<div class="col-md-'.$width.'">';
	    }
	    $output .= '<div class="counter-box">
						<div class="counter-box-icon';
						if($colored) {
							$output .= " colored ";
						}
						$output .= '">';
	     if($icon) { $output .= '<i class="'.esc_attr($icon).'"></i>'; }           		
	    $output .= '		<span class="counter">'.$number.'</span>';
	    					if(!empty($scale)) { $output .= '<i>'.$scale.'</i>';}
	    		$output .= ' <p>'.$title.'</p>
	    					</div>
	           			</div>';
	 if($from_vs === 'yes' && $in_full_width === 'yes') {
	        $output .= '</div>';
	    }
	    return $output;
	}

?>