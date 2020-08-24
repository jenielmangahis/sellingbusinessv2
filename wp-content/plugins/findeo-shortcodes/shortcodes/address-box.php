<?php 
	function findeo_address_box($atts,$content) {
		 extract(shortcode_atts(array(
		 	'latitude' 	=> '', 
		 	'longitude' 	=> '', 
		 	'background' => '',
	        'from_vs'  	=> '',
	        ), $atts));

	    if($from_vs=='yes') {
	    	$background = wp_get_attachment_url( $background );
		}
		
		$output = '<!-- Map Container -->
		<div class="contact-map margin-bottom-55">

			<!-- Map -->
			<div class="google-map-container">
				<div id="propertyMap" data-latitude="'.esc_attr($latitude).'" data-longitude="'.esc_attr($longitude).'"></div>
				<a href="#" id="streetView">Street View</a>
			</div>

			<!-- Office -->
			<div class="address-box-container">
				<div class="address-container" data-background-image="'.esc_url($background).'">
					<div class="office-address">
						'.do_shortcode( $content ).'
					</div>
				</div>
			</div>

		</div>
<div class="clearfix"></div>
<!-- Map Container / End -->
		';
	 	return $output;
	}
?>