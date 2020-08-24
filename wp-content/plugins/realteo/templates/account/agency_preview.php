
<div class="col-md-8">
	<div class="row">

		<div class="notification closeable notice"><p><strong><?php esc_html_e('Notice!','realteo');?></strong> <?php esc_html_e("This is preview of agency you've submitted, please confirm or edit your submission using buttons at the end of that page.",'realteo'); ?></p><a class="close" href="#"></a></div>
		
		<form method="post" id="agency_preview" ><!-- Agency -->
		
			<?php $template_loader = new Realteo_Template_Loader; 
			$template_loader->get_template_part( 'archive-agency/content-agency' );  ?>

			<div class="row margin-bottom-30">
				<div class="col-md-8">
					<div class="input-with-icon"><i class="sl sl-icon-check"></i><input type="submit" name="continue" id="agency_preview_submit_button" class="button realteo-button-submit-listing" value="<?php echo apply_filters( 'submit_agency_step_preview_submit_text', __( 'Submit', 'realteo' ) ); ?>" /></div>
					<div class="input-with-icon grey"><i class="sl sl-icon-note"></i><input type="submit" name="edit_agency" class="button realteo-button-edit-listing" value="<?php esc_attr_e( 'Edit', 'realteo' ); ?>" /></div>
					<input type="hidden" 	name="agency_id" value="<?php echo esc_attr( $data->agency_id ); ?>" />
					<input type="hidden" 	name="step" value="<?php echo esc_attr( $data->step ); ?>" />
					<input type="hidden" 	name="agency_form" value="<?php echo $data->form; ?>" />
				</div>
			</div>
		</form>
	</div>
</div>