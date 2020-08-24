<?php 

	function findeo_counters( $atts, $content ) {
    	extract(shortcode_atts(array(
    	'from_vs' => '',
    	'title'   => '',
    	'background' => ''
    	), $atts));

   
		ob_start();	
  
	    if($from_vs=='yes') {
	    	$bg_src = wp_get_attachment_url( $background ); ?>          
 			<!-- Parallax Counters -->
 			<div class="fullscreen not-fullscreen background parallax margin-top-0" data-background="<?php echo esc_url($bg_src); ?>" data-img-width="1677" data-img-height="1119" data-diff="300" data-color="#303133" data-color-opacity="0.9">
				<div id="counters">
					<div class="container">
						<div class="row">
							<div class="counter-boxes-inside-parallax">

	    	<?php } else { ?>
		    
			<div class="fullscreen not-fullscreen background parallax margin-top-0" data-background="<?php echo esc_url($bg_src); ?>" data-img-width="1677" data-img-height="1119" data-diff="300" data-color="#303133" data-color-opacity="0.9">
					<div id="counters">
					<div class="container">
						<div class="row">
							<div class="counter-boxes-inside-parallax">
			<?php } 
							echo do_shortcode($content);
			?>
							</div>
						</div>
					</div>
				</div>
				<!-- Counters / End -->
			</div>
			<!-- Parallax Counters / End -->
			<?php


	    return ob_get_clean();
	}

	?>