<?php
/* Get user info. */
global $wp_roles;
$current_user = wp_get_current_user();

$template_loader = new Realteo_Template_Loader; 
$template_loader->set_template_data( array( 'current' => 'profile' ) )->get_template_part( 'account/navigation' ); 
?>

<div class="col-md-8">
	<div class="row">

<?php 
$roles = $current_user->roles;
$role = array_shift( $roles ); ?>
		<div class="col-md-8 my-profile">

			<?php if ( isset($_GET['updated']) && $_GET['updated'] == 'true' ) : ?> 
				<div class="notification success closeable margin-bottom-35"><p><?php esc_html_e('Your profile has been updated.', 'realteo'); ?></p><a class="close" href="#"></a></div> 
			<?php endif; ?>
                 
			<h4 class="margin-top-0 margin-bottom-30"> <?php esc_html_e('My Account', 'realteo'); ?></h4>
	
			<?php if ( !is_user_logged_in() ) : ?>
                <p class="warning">
                    <?php esc_html_e('You must be logged in to edit your profile.', 'realteo'); ?>
                </p><!-- .warning -->
            <?php else : ?>
			<form method="post" id="edit_user" action="<?php the_permalink(); ?>">

			  	<label for="first-name"><?php esc_html_e('First Name', 'realteo'); ?></label>
                <input class="text-input" name="first-name" type="text" id="first-name" value="<?php  echo $current_user->user_firstname; ?>" />
			  	
			  	<label for="last-name"><?php esc_html_e('Last Name', 'realteo'); ?></label>
                <input class="text-input" name="last-name" type="text" id="last-name" value="<?php echo $current_user->user_lastname; ?>" />
			
            <?php if($role!='buyer'): ?>
				<h4 class="margin-top-50 margin-bottom-25"><?php esc_html_e( 'About Me', 'realteo' ); ?></h4>
				<?php 
					$user_desc = get_the_author_meta( 'description' , $current_user->ID);
					$user_desc_stripped = strip_tags($user_desc, '<p>'); //replace <p> and <a> with whatever tags you want to keep after the strip
				?>
                <textarea name="description" id="description" cols="30" rows="10"><?php echo $user_desc_stripped; ?></textarea>
           <?php endif; ?>
              <?php 
	              switch ($role) {
	              	case 'owner':
	              		$fields = Realteo_Meta_Boxes::meta_boxes_user_owner();
	              		break;
	              	case 'buyer':
	              		$fields = Realteo_Meta_Boxes::meta_boxes_user_buyer();
	              		break;              	
	              	case 'agent':
	              		$fields = Realteo_Meta_Boxes::meta_boxes_user_agent();
	              		break;
	              	
	              	default:
	              		$fields = Realteo_Meta_Boxes::meta_boxes_user_agent();
	              		break;
	              }
					foreach ( $fields as $key => $field ) : 
					
						$field['value'] = $current_user->$key;
						
						if( $field['type'] == 'header') { ?>
							<!--<h4 class="submit-section-header"><?php echo $field['label']; ?></h4>-->
							
						<?php } else if( $field['type'] == "map" ) { ?>
								<h4 class="submit-section-header"><?php echo $field['label']; ?></h4>
								<div id="submit_map"></div>
						<?php } else {

							 if($field['type'] != 'hidden'): ?>
								<label class="label-<?php echo esc_attr( $key ); ?>" for="<?php echo esc_attr( $key ); ?>">
									<?php echo $field['label'];?>
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
							 } 
					endforeach; ?>


				<input type="hidden" name="my-account-submission" value="1" />
				<button type="submit" form="edit_user" value="<?php esc_html_e( 'Submit', 'realteo' ); ?>" class="button margin-top-20 margin-bottom-20"><?php esc_html_e('Save Changes', 'realteo'); ?></button>
		
			<?php endif; ?>
		</div>
		<?php if ( is_user_logged_in() ) : ?>
		<div class="col-md-4 avatar-contain">
			<!-- Avatar -->
			<h4 class="margin-top-0 margin-bottom-30"> <?php esc_html_e('Logo / Profile Photo', 'realteo'); ?></h4>
			
					<?php 
					$custom_avatar = $current_user->realteo_avatar_id;
					$custom_avatar = wp_get_attachment_url($custom_avatar); 
					if(!empty($custom_avatar)) { ?>
					<div 
					data-photo="<?php echo $custom_avatar; ?>" 
					data-name="<?php esc_html_e('Your Avatar', 'realteo'); ?>" 
					data-size="<?php echo filesize( get_attached_file( $current_user->realteo_avatar_id ) ); ?>" class="edit-profile-photo">
					
					<?php } ?>
					<div id="avatar-uploader" class="dropzone">
						<div class="dz-message" data-dz-message><span><?php esc_html_e('Upload Profile Photo', 'realteo'); ?></span></div>
					</div>
					
					<input class="hidden" name="realteo_avatar_id" type="text" id="avatar-uploader-id" value="<?php echo $current_user->realteo_avatar_id; ?>" />
			</div>
			
		</div>
		<?php endif; ?>
		</form>
	</div>
</div>