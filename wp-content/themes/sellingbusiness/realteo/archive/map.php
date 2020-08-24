<?php 
exit();
$map_type = get_option('findeo_properties_map_type','dynamic'); 
if($map_type == 'static') { 

	echo '<div id="map-container">';
		$maps = new FindeoMaps;
		echo $maps->show_map('520');
	echo '</div>';
} else { 
	
	if(isset($_GET['_offer_type']) && isset($_GET['_property_type'])){
		
		$output ='';
		$property_type = ($_GET['_property_type'])?$_GET['_property_type'].'_image':'default_property_banner_image';
		$bannerbg = get_field($property_type, 'option');
		
		$properties =  get_option('realteo_property_types_fields');
		$search_title = 'Search result';
		foreach ($properties as $indx=>$key ) {
			$id = sanitize_title($key);	
			if($id==$_GET['_property_type']){
				$search_title = $key;
				break;
			}
		}
		if(!$bannerbg){
			$bannerbg = get_field('default_property_banner_image', 'option');
		}
	
		if(!empty($bannerbg)) { 
			$opacity = get_option('findeo_search_bg_opacity',0.45);
			$color = get_option('findeo_search_color','#36383e');
			$output = 'data-background="'.esc_attr($bannerbg['url']).'" data-img-width="'.esc_attr($bannerbg['width']).'" data-img-height="'.esc_attr($bannerbg['height']).'" 
			data-diff="300"	data-color="'.esc_attr($color).'" data-color-opacity="'.esc_attr($opacity).'"';
		}
?>
	<div class="parallax margin-bottom-40" <?=$output?>>
    	<div class="container">
			<div class="row"><div class="col-md-12">
				<div class="search-container">
                	<h1 class="search-title" style="color:#fff"><?=$search_title?></h1>
                </div>
            </div></div>
        </div>
	</div>
<?php
	}else{ // else load default map
?><!-- Map ================================================== -->
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
<?php
	} // end search condition
}
?>