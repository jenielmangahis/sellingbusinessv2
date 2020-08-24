<?php 
	function findeo_properties_map( $atts, $content ) {
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
			ob_start(); ?>
				    <!-- Map
			================================================== -->
			<div id="map-container">

			    <?php 
					$maps = new FindeoMaps;
					echo $maps->show_map();
			  	?>

			    <!-- Map Navigation -->
				<a href="#" id="scrollEnabling" title="<?php esc_attr_e('Enable or disable scrolling on map','findeo-shortcodes'); ?>"><?php esc_html_e('Enable Scrolling','findeo-shortcodes'); ?></a>
				<ul id="mapnav-buttons">
				     <li><a href="#" id="prevpoint" title="<?php esc_attr_e('Previous point on map','findeo-shortcodes'); ?>"><?php esc_html_e('Prev','findeo'); ?></a></li>
				    <li><a href="#" id="nextpoint" title="<?php esc_attr_e('Next point on map','findeo-shortcodes'); ?>"><?php esc_html_e('Next','findeo'); ?></a></li>
				</ul>

			</div>
		<?php
	 	return ob_get_clean();
	}

?>