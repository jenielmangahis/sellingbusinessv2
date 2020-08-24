<?php 
	
	/**
	* Headline shortcode
	* Usage: [iconbox title="Service Title" url="#" icon="37"] test [/headline]
	*/
	function findeo_iconbox( $atts, $content ) {
	  extract(shortcode_atts(array(
		    'title' 		=> 'Service Title',
		    'url' 			=> '',
		    'url_title' 	=> '',
		    'url2' 			=> '',
		    'url2_title'	=> '',
		   	'icon'          => 'im im-icon-Office',
		    'type'			=> 'box-1', // 'box-1, box-1 rounded, box-2, box-3, box-4'
		    'from_vs' 		=> 'no',
	    ), $atts));

        ob_start();

  		if(!empty($url)) {
  			if($from_vs="yes") { 
		  		$link = vc_build_link( $url );
		        $a_href = $link['url']; 
		        $a_title = $link['title']; 
		        $a_target = $link['target'];
		        $link_1 = '<a href="'.esc_url( $a_href ).'" title="'.esc_attr( $a_title ).'"'; 
		        if(!empty($a_target)) { 
		        	$link_1 .= 'target="'.esc_attr($a_target).'"'; 
		        } 
		        $link_1 .= '>'.esc_attr( $a_title ).'</a>';
		    } else {
		    	$link_1 = '<a href="'.esc_url( $url ).'">';	
		    }
		}
		if(!empty($url2)) {
  			if($from_vs = "yes") { 
		  		$link = vc_build_link( $url2 );
		        $a_href = $link['url']; 
		        $a_title = $link['title']; 
		        $a_target = $link['target']; 
		        $link_2 = '<a href="'.esc_url( $a_href ).'" title="'.esc_attr( $a_title ).'"'; 
		        if(!empty($a_target)) { 
		        	$link_2 .= 'target="'.esc_attr($a_target).'"'; 
		        } 
		        $link_2 .= '>'.esc_attr( $a_title ).'</a>';
		    } else {
		    	$link_2 = '<a href="'.esc_url( $url2 ).'">'.esc_html($url2_title).'</a>';	
		    }
		}

		 ?>
		<div class="icon-<?php echo esc_attr($type); ?>">
			<div class="icon-container">
				<i class="<?php echo esc_attr($icon); ?>"></i>
				<?php if($type == 'box-1') : ?>
					<div class="icon-links">
						<?php if(!empty($url)) { echo $link_1; } ?>
						<?php if(!empty($url2)) { echo $link_2; } ?>
					</div>
				<?php endif; ?>
			</div>

			<h3><?php echo esc_html( $title ); ?></h3>
			<p><?php echo do_shortcode( $content ); ?></p>
		</div>

	    <?php
	    $output =  ob_get_clean() ;
       	return  $output ;
	}

?>