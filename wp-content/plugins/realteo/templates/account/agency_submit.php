<?php
/* Get user info. */
global $wp_roles;
$current_user = wp_get_current_user();

$template_loader = new Realteo_Template_Loader; 

$fields	 	= $data->fields;

if(isset($_GET["action"])) {
	$form_type = $_GET["action"];
} else {
	$form_type = 'submit';
}

?>

<div class="col-md-8 agency-submit-edit">
	<div>
	<?php if ( $form_type === 'edit-agency') { ?>
		<div class="notification closeable notice"><p><?php esc_html_e('You are currently editing:' , 'realteo'); if(isset($data->agency_id) && $data->agency_id != 0) {   $property = get_post( $data->agency_id ); echo ' <a href="'.get_permalink( $data->agency_id ).'">'.$property->post_title .'</a>';  }?></p></div> 
	<?php } ?>
	<?php
		if ( isset( $data->agency_edit ) && $data->agency_edit ) { ?>
		<div class="notification closeable notice">
		<?php printf( '<p><strong>' . __( "You are editing an existing agency. %s", 'realteo' ) . '</strong></p>', '<a href="?new=1&key=' . $data->agency_edit . '">' . __( 'Add a New Agency', 'realteo' ) . '</a>' ); ?>
		</div>
	<?php } ?>
	
		<form action="<?php  echo esc_url( wp_unslash( $_SERVER['REQUEST_URI'] ) ); ?>" method="post" id="submit-agency-form" class="property-manager-form" enctype="multipart/form-data">
		<?php 
		
			foreach ( $fields as $key => $field ) : 

			if( $field['type'] == 'header') { ?>
				<h3 class="submit-section-header"><?php echo $field['label']; ?></h3>
				
			<?php } else if( $field['type'] == "map" ) { ?>
					<h3 class="submit-section-header"><?php echo $field['label']; ?></h3>
					<div id="submit_map"></div>
			<?php } else {

				if( isset($field['before_row']) ) : 
					echo $field['before_row'];
				endif; 
				?>
				<?php 
				if( isset($field['render_row_col']) && !empty($field['render_row_col']) ) : 
					realteo_render_column( $field['render_row_col'] ); 
				endif; ?>
					<?php if($field['type'] != 'hidden'): ?>
					<label class="label-<?php echo esc_attr( $key ); ?>" for="<?php echo esc_attr( $key ); ?>">
						<?php echo $field['label'] . apply_filters( 'submit_property_form_required_label', isset($field['required']) ? '' : ' <small>' . esc_html__( '(optional)', 'workscout' ) . '</small>', $field ); ?>
						<?php if( isset($field['tooltip']) && !empty($field['tooltip']) ) { ?>
							<i class="tip" data-tip-content="<?php esc_attr_e( $field['tooltip'] ); ?>"></i>
						<?php } ?>
					</label>
					<?php endif; ?>
					
					<?php
						$template_loader = new Realteo_Template_Loader;

						// fix the name/id mistmatch
						if(isset($field['id'])){
							$field['name'] = $field['id'];
	 					}
						

						$template_loader->set_template_data( array( 'key' => $key, 'field' => $field,	) )->get_template_part( 'form-fields/' . $field['type'] );
					?>

				<?php 
				if( isset($field['render_row_col']) && !empty($field['render_row_col']) ) : 
					echo "</div>";
				endif; ?>
				<?php 
				if( isset($field['after_row']) ) : 
					echo $field['after_row'];
				endif; 
				?>
			<?php } 
			endforeach; ?>
				<div class="divider margin-top-40"></div>
				<p>
					<input type="hidden" 	name="agency_form" value="submit-agency" />
					<input type="hidden" 	name="step" value="submit" />
					<div class="input-with-icon margin-top-25 margin-bottom-30 big">
						<i class="sl sl-icon-arrow-right-circle" style="margin-top: 1px;"></i>
						<input type="submit" name="submit_agency" class="button" value="<?php
						if($form_type === 'edit-agency') { esc_attr_e('Save Changes','realteo'); } else { esc_attr_e('Submit Agency','realteo'); } ?>"/></div>
				</p>
			</form>		
	</div>
</div>