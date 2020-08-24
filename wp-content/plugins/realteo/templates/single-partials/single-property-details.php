<!-- Details -->
<?php 
$details_list = Realteo_Meta_Boxes::meta_boxes_details();
$output = '';
foreach ($details_list['fields'] as $detail => $value) {

	if($value['type'] == 'select_multiple') {
		
		$meta_value = get_post_meta($post->ID, $value['id'],false);
	
	} else {

		$meta_value = get_post_meta($post->ID, $value['id'],true);	

	}

	if(in_array($value['id'], array('_id','_ID','_Id'))){
		$meta_value = apply_filters('realteo_property_id',$post->ID);
	}

	if(!empty($meta_value)) {

		if($meta_value == 'check_on') {
			$output .= '<li class="checkboxed single-property-detail-'.$value['id'].'">'. $value['name'].'</li>';	
		} else {
			if(isset($details_list['fields'][$detail]['options']) && !empty($details_list['fields'][$detail]['options'])) {
				if(is_array($meta_value)) {
					if( isset($value['invert']) && $value['invert'] == true ) {		
						$output .= '<li class="single-property-detail-'.$value['id'].'"><span>';
						$i=0;
						$last = count($meta_value);
						foreach ($meta_value as $key => $saved_value) {	
							$i++;
							$output .=  $details_list['fields'][$detail]['options'][$saved_value];
							if($i >= 1 && $i < $last) : $output .= ", "; endif;
						}
						$output .=  '</span>: '. $value['name'].'</li>';
					} else {
						$output .= '<li class="single-property-detail-'.$value['id'].'">'. $value['name'].': <span>';
						
						$i=0;
						$last = count($meta_value);
						foreach ($meta_value as $key => $saved_value) {	
							$i++;
							$output .=  $details_list['fields'][$detail]['options'][$saved_value];
							if($i >= 1 && $i < $last) : $output .= ", "; endif;
						}
						$output .=  '</span></li>';		
					}
				} else {
					if( isset($value['invert']) && $value['invert'] == true ) {		
						$output .= '<li class="single-property-detail-'.$value['id'].'"><span>'.$details_list['fields'][$detail]['options'][$meta_value].'</span>: '. $value['name'].'</li>';
					} else {
						$output .= '<li class="single-property-detail-'.$value['id'].'">'. $value['name'].': <span>'.$details_list['fields'][$detail]['options'][$meta_value].'</span></li>';		
					}
				}
				
			} else {
				if( isset($value['invert']) && $value['invert'] == true ) {		
					$output .= '<li class="single-property-detail-'.$value['id'].'"> <span>';
					$output .= (is_array($meta_value)) ? implode(",", $meta_value) : $meta_value ;
					$output .=  '</span> '. $value['name'].'</li>';
				} else {
					$output .= '<li class="single-property-detail-'.$value['id'].'">'. $value['name'].': <span>';
					$output .= (is_array($meta_value)) ? implode(",", $meta_value) : $meta_value ;
					$output .= '</span></li>';
				}
				
			}	
		}
		
	}
}
if(!empty($output)) : ?>
<h3 class="desc-headline"><?php esc_html_e('Details','realteo'); ?></h3>
<ul class="property-features margin-top-0">
<?php echo $output; ?>
</ul>
<?php endif; ?>