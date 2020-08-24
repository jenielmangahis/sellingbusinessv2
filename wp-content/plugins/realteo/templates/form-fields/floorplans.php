<!-- Section -->
<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
	$field = $data->field;
	$key = $data->key;


$floorplans = get_post_meta( $key, '_floorplans', true ); 
$scale = realteo_get_option( 'scale', 'sq ft' ); ?>


				<table id="floorplans-submit-container">
					<?php 
					if(isset($field['value']) && is_array(($field['value']))){ 
						$i = 0;
						foreach($field['value'] as $key => $plan) { ?>
							<tr class="floorplans-submit-item ">
								<td>
									<div class="fm-move">
										<i class="fa fa-reorder"></i>
										<div class="floorplan-title"><!-- Text from input value or placeholder goes here --></div>
										<span class="fp-btn remove"><i class="fa fa-trash-o"></i></span>
										<span class="fp-btn edit"><i class="sl sl-icon-note"></i><?php esc_attr_e('Edit','realteo');  ?></span>
									</div>
									<div class="fm-inner-container">
										<div class="floor_dropzone dropzone "><div class="dz-default dz-message"><span><i class="sl sl-icon-picture"></i><?php esc_html_e('Upload Image','realteo') ?></span></div></div>
										<div class="fm-inputs">
											<div class="fm-input floorplans-name"><input value="<?php echo esc_attr( $plan['floorplan_title'] ); ?>" class="floorplans_title" name="_floorplans[<?php echo esc_attr($i); ?>][floorplan_title]" type="text" placeholder="<?php esc_attr_e('Floorplan Title','realteo')?>" /></div>

											<div class="fm-input floorplans-area"><input value="<?php echo esc_attr( $plan['floorplan_area'] ); ?>" name="_floorplans[<?php echo esc_attr($i); ?>][floorplan_area]" type="text" placeholder="<?php  esc_attr_e('Area','realteo'); ?>" data-fp-unit="<?php echo apply_filters('realteo_scale',$scale); ?>" /></div>

											<div class="fm-input floorplans-description"><textarea name="_floorplans[<?php echo esc_attr($i); ?>][floorplan_desc]" type="text" placeholder="<?php  esc_attr_e('Description','realteo'); ?>"><?php echo $plan['floorplan_desc']; ?></textarea></div>

											<input type="hidden"  data-size="<?php if( isset($plan['floorplan_image_id']) && !empty($plan['floorplan_image_id']) ) { echo filesize( get_attached_file( $plan['floorplan_image_id'] )); } ?>" name="_floorplans[<?php echo esc_attr($i); ?>][floorplan_image]" class="floorplans_image"
											<?php if( isset($plan['floorplan_image_id']) && !empty($plan['floorplan_image_id']) ) { ?>
												value="<?php echo wp_get_attachment_url( $plan['floorplan_image_id']  ); ?>"
											<?php } ?> >
											<?php if(isset($plan['floorplan_image_id']) && !empty($plan['floorplan_image_id'])) : ?>
												<input type="hidden" value="<?php echo esc_attr( $plan['floorplan_image_id'] ); ?>" name="_floorplans[<?php echo esc_attr($i); ?>][floorplan_image_id]" class="floorplans_image_id">
											<?php endif; ?>
										</div>
									</div>
								</td>
							</tr>
						<?php 
						$i++;
						} 
					}
					?>
				</table>
				<a href="#"  class="button add-floorplans-submit-item" data-toclone="<?php echo esc_html('<tr class="floorplans-submit-item">
						<td>
							<div class="fm-move">
								<i class="fa fa-reorder"></i>
								<div class="floorplan-title"><!-- Text from input value or placeholder goes here --></div>
								<span class="fp-btn remove"><i class="fa fa-trash-o"></i></span>
								<span class="fp-btn edit"><i class="sl sl-icon-note"></i> '.esc_attr__('Edit','realteo').'</span>
							</div>
							<div class="fm-inner-container">
								<div class="floor_dropzone dropzone"><div class="dz-default dz-message"><span><i class="sl sl-icon-picture"></i>'. esc_html__('Upload Image','realteo'). '</span></div></div>
								<div class="fm-inputs">
									<div class="fm-input floorplans-name"><input name="_floorplans[-1][floorplan_title]" type="text" placeholder="'. esc_attr__('Floorplan Title','realteo').'" /></div>
									<div class="fm-input floorplans-area"><i class="data-fp-unit">'. apply_filters('realteo_scale',$scale).'</i><input name="_floorplans[-1][floorplan_area]" type="text" placeholder="'. esc_attr__('Area','realteo').'" data-fp-unit="'. apply_filters('realteo_scale',$scale).'" /></div>
									<div class="fm-input floorplans-description"><textarea name="_floorplans[-1][floorplan_desc]" type="text" placeholder="'. esc_attr__('Description','realteo').'" /></textarea></div>
									<input type="hidden" name="_floorplans[-1][floorplan_image]" class="floorplans_image">
									<input type="hidden" name="_floorplans[-1][floorplan_image_id]" class="floorplans_image_id">
								</div>
							</div>
						</td>
					</tr>'); ?>">
					<?php

					$button = (isset($field['placeholder']) && !empty($field['placeholder'])) ? $field['placeholder'] : esc_html_e('Add Floorplan','realteo') ;?>


					<i class="fa fa-plus-circle"></i> <?php echo $button; ?></a>
			

<!-- Section / End -->