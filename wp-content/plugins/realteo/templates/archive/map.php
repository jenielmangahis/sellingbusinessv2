<?php 
$map_type = get_option('findeo_properties_map_type','dynamic'); 
if($map_type == 'static') { 

	echo '<div id="map-container">';
		$maps = new FindeoMaps;
		echo $maps->show_map('520');
	echo '</div>';

} else { ?><!-- Map
================================================== -->
<div id="map-container">

    <div id="map">
        <!-- map goes here -->
    </div>

    <!-- Map Navigation -->
	<a href="#" id="scrollEnabling" title="Enable or disable scrolling on map"><?php esc_html_e('Enable Scrolling','findeo'); ?></a>
	<ul id="mapnav-buttons">
	    <li><a href="#" id="prevpoint" title="Previous point on map"><?php esc_html_e('Prev','findeo'); ?></a></li>
	    <li><a href="#" id="nextpoint" title="Next point on mp"><?php esc_html_e('Next','findeo'); ?></a></li>
	</ul>

</div>
<?php } ?>