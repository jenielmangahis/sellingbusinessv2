<!-- Main Details -->
<?php 
$details_list = Realteo_Meta_Boxes::meta_boxes_main_details(); 

$class = (isset($data->class)) ? $data->class : 'property-main-features' ;
?>
<ul class="<?php esc_attr_e($class); ?>">
<?php 

foreach ($details_list['fields'] as $detail => $value) {

	if( in_array($value['type'], array('select_multiple','multicheck_split')) ) {
		$meta_value = get_post_meta($post->ID, $value['id'],false);
	
	} else {
		$meta_value = get_post_meta($post->ID, $value['id'],true);	
	}


	if(in_array($value['id'], array('_id','_ID','_Id'))){
		$meta_value = apply_filters('realteo_property_id',$post->ID);
	}
	
	if(!empty($meta_value)) {
		if($value['id'] == '_area'){
			$scale = realteo_get_option( 'scale', 'sq ft' );			
			if( isset($value['invert']) && $value['invert'] == true ) {				
				echo '<li class="main-detail-'.$value['id'].'">'.apply_filters('realteo_scale',$scale).' <span>'.$meta_value.'</span> </li>';
			} else {
				echo '<li class="main-detail-'.$value['id'].'"><span>'.$meta_value.'</span> '.apply_filters('realteo_scale',$scale).' </li>';	
			}
			
		} else {
			if(isset($details_list['fields'][$detail]['options']) && !empty($details_list['fields'][$detail]['options'])) {

				if(is_array($meta_value)) {
							
					if( isset($value['invert']) && $value['invert'] == true ) {		
						echo '<li class="main-detail-'.$value['id'].'">'. $value['name'].' <span>';
							$i=0;
							$last = count($meta_value);
							foreach ($meta_value as $key => $saved_value) {	
								$i++;

								if(isset($details_list['fields'][$detail]['options'][$saved_value]))
								echo $details_list['fields'][$detail]['options'][$saved_value];
								if($i >= 1 && $i < $last) : echo ", "; endif;
							}
						echo '</span></li>';
					} else {						
							echo '<li class="main-detail-'.$value['id'].'"><span>';
							$i=0;
							$last = count($meta_value);
							foreach ($meta_value as $key => $saved_value) {	
								$i++;
								if(isset($details_list['fields'][$detail]['options'][$saved_value]))
									echo $details_list['fields'][$detail]['options'][$saved_value];
									if($i >= 1 && $i < $last) : echo ", "; endif;
								
								
							}
							echo '</span> '. $value['name'].' </li>';			
						
					}
					

				} else {

					if( isset($value['invert']) && $value['invert'] == true ) {		
						echo '<li class="main-detail-'.$value['id'].'">'. $value['name'].' <span>'.$details_list['fields'][$detail]['options'][$meta_value].'</span></li>';
					} else {
						if(isset($details_list['fields'][$detail]['options'][$meta_value])){
							echo '<li class="main-detail-'.$value['id'].'"><span>'.$details_list['fields'][$detail]['options'][$meta_value].'</span> '. $value['name'].' </li>';			
						}
					}

				}
				
				
			} else {

				if( isset($value['invert']) && $value['invert'] == true ) {	
					echo '<li class="main-detail-'.$value['id'].'">'. $value['name'].' <span>';
					echo (is_array($meta_value)) ? implode(",", $meta_value) : $meta_value ;
					echo '</span></li>';	
				} else {
					echo '<li class="main-detail-'.$value['id'].'"><span>';
					echo (is_array($meta_value)) ? implode(",", $meta_value) : $meta_value ;
					echo '</span> '. $value['name'].' </li>';		
				}
				
			}
			
		}
		
	}
}
?>
</ul>