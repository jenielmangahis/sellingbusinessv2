<?php

function findeo_pricingwrapper( $atts, $content ) { 

	ob_start();	?>
 		<div class="pricing-container margin-top-40">
 			<?php echo do_shortcode( $content ); ?>
 		</div>
 	<?php
 	return ob_get_clean();
 	}
?>