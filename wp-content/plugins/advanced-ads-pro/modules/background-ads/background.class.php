<?php

class Advanced_Ads_Pro_Module_Background_Ads {
    
	public function __construct() {
		add_action( 'wp_footer', array( $this, 'footer_injection' ), 20 );
	}

	public function footer_injection(){
		// stop, if main plugin doesn’t exist
		if ( ! class_exists( 'Advanced_Ads', false ) ) {
		    return;
		}

		// get placements
		$placements = get_option( 'advads-ads-placements', array() );
		if( is_array( $placements ) ){
			foreach ( $placements as $_placement_id => $_placement ){
				if ( isset($_placement['type']) && 'background' == $_placement['type'] ){
					// register output change hook
					add_action( 'advanced-ads-output-final', array( $this, 'ad_output' ), 20, 3 );
					// display the placement content with placement options
					$_options = isset( $_placement['options'] ) ? $_placement['options'] : array();
					echo Advanced_Ads_Select::get_instance()->get_ad_by_method( $_placement_id, 'placement', $_options );
					// unregister hook
					remove_action( 'advanced-ads-output-final', array( $this, 'ad_output' ), 20, 3 );
				}
			}
		}
	}
	
	/**
	 * change ad output
	 */
	public function ad_output( $output, $ad, $output_options ){

		if( !isset( $ad->type ) || 'image' !== $ad->type ){
			return $output;
		}
		
		// get background color
		$bg_color = isset( $ad->args['bg_color'] ) ? sanitize_text_field( $ad->args['bg_color'] ) : false;
		
		// get prefix and generate new body class
		$prefix = Advanced_Ads_Plugin::get_instance()->get_frontend_prefix();
		$class = $prefix . 'body-background';
		
		// get correct link
		if ( class_exists( 'Advanced_Ads_Tracking' ) && method_exists( 'Advanced_Ads_Tracking', 'build_click_tracking_url' ) ) {
		    $link = Advanced_Ads_Tracking::build_click_tracking_url( $ad );
		} elseif( !empty( $ad->url ) ) {
		    $link = $ad->url;
		} elseif( !empty( $ad->output['url'] ) ) { // might no longer be needed
		    $link = $ad->output['url'];
		} else {
		    $link = false;
		}
		
		// get image
		if( isset( $ad->output['image_id'] ) ){
		    $image = wp_get_attachment_image_src( $ad->output['image_id'], 'full' );
		    if ( $image ) {
			list( $image_url, $image_width, $image_height ) = $image;
		    }
		}
		
		if( empty( $image_url ) ){
		    return $output;
		}
	    
		$selector = apply_filters( 'advanced-ads-pro-background-selector', 'body' );
		
		ob_start();
		?><style><?php echo $selector; ?> {
			    background: url(<?php echo $image_url; ?>) no-repeat fixed; 
			    background-size: 100% auto;
			<?php if( $bg_color ) : ?>
			    background-color: <?php echo $bg_color; ?>;
			<?php endif; ?>    
		    }
		<?php if( $link ) : ?>
		    <?php /**
		    * We should not use links and other tags that should have cursor: pointer as direct childs of the $selector.
		    * That is, we need a nested container (e.g. body > div > a) to make it work corretly. */
		    echo $selector; ?> { cursor: pointer; } <?php echo $selector; ?> > * { cursor: auto; }
		<?php endif; ?>
		</style>
		<script>
		<?php if( $link ) : ?>
		jQuery('<?php echo $selector; ?>').click(function(e){
		    if (e.target === this) {
			window.open('<?php echo $link; ?>', '_blank');
		    }
		});
		<?php endif; ?>
		jQuery('<?php echo $selector; ?>').addClass('<?php echo $class; ?>');
		</script><?php
		return ob_get_clean();

		//return $output;
		
	}
}

