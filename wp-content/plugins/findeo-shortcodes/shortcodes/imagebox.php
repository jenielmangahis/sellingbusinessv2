<?php 
	
	/**
	* Headline shortcode
	* Usage: [iconbox title="Service Title" url="#" icon="37"] test [/headline]
	*/
	function findeo_imagebox( $atts, $content ) {
	  extract(shortcode_atts(array(
		    'category' 		=> '',/*it's region but it's too late to rename it */
		    'property_feature' => '',
		    'url' 			=> '',
		    'featured' 		=> '',
		    'show_counter' 	=> '',
	        'background'	=> '',
		    'from_vs' 		=> 'no',
	    ), $atts));
 		if($from_vs=='yes') {
	    	$background = wp_get_attachment_url( $background );
		}
		if(!class_exists('Realteo')){ return ;  }
		if($category) {
			$term = get_term_by( 'id', $category, 'region' );
			$term_url = get_term_link((int) $category,'region');
		}
		if($property_feature) {
			$term = get_term_by( 'id', $property_feature, 'property_feature' );
			$term_url = get_term_link((int) $property_feature,'property_feature');
		}
		if($url){
			
			$link = vc_build_link( $url );
	        $a_href = $link['url'];
	        $a_title = $link['title'];
	        $a_target = $link['target']; 

	        ob_start(); ?>
				 <!-- Image Box -->
				<a href="<?php echo esc_url($a_href); ?>" class="img-box" data-background-image="<?php echo esc_attr($background); ?>" <?php  if(!empty($a_target)){ echo 'target="'.$a_target.'"';  } ?> >
					
					<?php if($featured) : ?>
					<!-- Badge -->
					<div class="listing-badges">
						<span class="featured"><?php esc_html_e('Featured','findeo-shortcodes') ?></span>
					</div>
					<?php endif; ?>

					<div class="img-box-content visible">
						<h4><?php echo $a_title; ?></h4>						
					</div>
				</a>


			    <?php
			    $output =  ob_get_clean() ;
		} else {
		
			if( is_wp_error( $term ) || $term == false)   {
				return;
			} 
			if( is_wp_error( $term_url ) || $term_url == false)   {
				return;
			} 
	        ob_start(); ?>
			 <!-- Image Box -->
			<a href="<?php echo esc_url($term_url); ?>" class="img-box" data-background-image="<?php echo esc_attr($background); ?>">
				
				<?php if($featured) : ?>
				<!-- Badge -->
				<div class="listing-badges">
					<span class="featured"><?php esc_html_e('Featured','findeo-shortcodes') ?></span>
				</div>
				<?php endif; ?>

				<div class="img-box-content visible">
					<h4><?php echo $term->name; ?></h4>
					<?php if($show_counter) : ?><span><?php echo $term->count; ?> <?php esc_html_e('Properties','findeo-shortcodes') ?></span> <?php endif; ?>
				</div>
			</a>


		    <?php
		    $output =  ob_get_clean() ;

	    }
       	return  $output ;
	}

?>