<?php
/**
 * property Submission Form
 */
if ( ! defined( 'ABSPATH' ) ) exit;

if(isset($_GET["action"]) && $_GET["action"] == 'edit' && !realteo_if_can_edit_property($data->property_id) ){ ?>
	<div class="notification closeable notice">
		<?php esc_html_e('You can\'t edit that property' , 'realteo');?>
	</div>
<?php 
		return;
	}	


/* Get the form fields */
$fields = array();
if(isset($data)) :
	$fields	 	= (isset($data->fields)) ? $data->fields : '' ;
endif;

/* Determine the type of form */
	if(isset($_GET["action"])) {
		$form_type = $_GET["action"];
	} else {
		$form_type = 'submit';
	}
?>



<div class="submit-page">
<?php if ( $form_type === 'edit') { 
	?>
	<div class="notification closeable notice"><p><?php esc_html_e('You are currently editing:' , 'realteo'); if(isset($data->property_id) && $data->property_id != 0) {   $property = get_post( $data->property_id ); echo ' <a href="'.get_permalink( $data->property_id ).'">'.$property->post_title .'</a>';  }?></p></div> 
<?php } ?>
<?php
	if ( isset( $data->property_edit ) && $data->property_edit ) {
		?>
		<div class="notification closeable notice">
		<?php printf( '<p><strong>' . __( "You are editing an existing property. %s", 'realteo' ) . '</strong></p>', '<a href="?new=1&key=' . $data->property_edit . '">' . __( 'Add A New Property', 'realteo' ) . '</a>' ); ?>
		</div>
	<?php }
	?>
<form action="<?php  echo esc_url( $data->action ); ?>" method="post" id="submit-property-form" class="property-manager-form" enctype="multipart/form-data">
	
	<?php foreach ( $fields['property'] as $key => $field ) : ?>
		
		<?php if( $field['type'] == 'header') { ?>
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
		<input type="hidden" 	name="realteo_form" value="<?php echo $data->form; ?>" />
		<input type="hidden" 	name="property_id" value="<?php echo esc_attr( $data->property_id ); ?>" />
		<input type="hidden" 	name="step" value="<?php echo esc_attr( $data->step ); ?>" />
		<div class="input-with-icon margin-top-25 margin-bottom-30 big"><i class="sl sl-icon-arrow-right-circle" style="margin-top: 1px;"></i><input type="submit" name="submit_property" class="button" value="<?php echo esc_attr( $data->submit_button_text ); ?>" /></div>
	</p>
	
</form>
</div>