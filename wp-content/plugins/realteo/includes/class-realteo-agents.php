<?php
// Exit if accessed directly
// https://github.com/jarkkolaine/personalize-login-tutorial-part-3
if ( ! defined( 'ABSPATH' ) )
	exit;
/**
 * Realteo_Property class
 */
class Realteo_Agents {

	/**
	 * Dashboard message.
	 *
	 * @access private
	 * @var string
	 */
	private $dashboard_message = '';

	/**
	 * Constructor
	 */
	public function __construct() {
		
		//add_action( 'user_contactmethods', array( $this, 'modify_contact_methods' ), 10 );
		add_action( 'init', array( $this, 'submit_my_account_form' ), 10 );
		add_action( 'init', array( $this, 'submit_change_password_form' ), 10 );
		add_action( 'init',  array( $this, 'remove_filter_lostpassword' ), 10 );

		add_action( 'wp', array( $this, 'dashboard_action_handler' ) );
		add_filter( 'pre_get_posts',  array( $this,  'author_archive_properties' ));
 


		add_action( 'show_user_profile', array( $this, 'extra_profile_fields' ), 10 );
		add_action( 'edit_user_profile', array( $this, 'extra_profile_fields' ), 10 );
		add_action( 'personal_options_update', array( $this, 'save_extra_profile_fields' ));
		add_action( 'edit_user_profile_update', array( $this, 'save_extra_profile_fields' ));
		 
		add_filter( 'wpua_is_author_or_above', '__return_true' ); /*fix to apply for agents only*/

		add_shortcode( 'realteo_my_properties', array( $this, 'realteo_my_properties' ) );
		add_shortcode( 'realteo_my_packages', array( $this, 'realteo_my_packages' ) );
		add_shortcode( 'realteo_my_account', array( $this, 'my_account' ) );
		add_shortcode( 'realteo_my_orders', array( $this, 'my_orders' ) );
		add_shortcode( 'realteo_change_password', array( $this, 'change_password' ) );
		add_shortcode( 'realteo_lost_password', array( $this, 'lost_password' ) );
		add_shortcode( 'realteo_reset_password', array( $this, 'reset_password' ) );

		add_filter( 'woocommerce_login_redirect', array( $this, 'redirect_woocommerce' ) ,10, 2);
		add_action('template_redirect', array( $this,  'woocommerce_account_redirect') );
		$front_login = realteo_get_option_with_name('realteo_general_options', 'realteo_front_end_login' );
		if($front_login == 'on') {
			
			add_action( 'login_form_login', array( $this, 'redirect_to_custom_login' ) );
			add_filter( 'login_redirect', array( $this, 'redirect_after_login' ), 10, 3 );\
			
			add_action( 'login_form_lostpassword', array( $this, 'redirect_to_custom_lostpassword' ) );

			add_action( 'login_form_rp', array( $this, 'redirect_to_custom_password_reset' ) );
			add_action( 'login_form_resetpass', array( $this, 'redirect_to_custom_password_reset' ) );

			add_action( 'login_form_register', array( $this, 'redirect_to_custom_register' ) );

			add_action( 'login_form_rp', array( $this, 'do_password_reset' ) );
			add_action( 'login_form_resetpass', array( $this, 'do_password_reset' ) );
			add_action( 'login_form_lostpassword', array( $this, 'do_password_lost' ) );

		}
		
		add_action( 'login_form_register', array( $this, 'do_register_user' ) );
	
		add_filter( 'authenticate', array( $this, 'maybe_redirect_at_authenticate' ), 101, 3 );
		
		add_filter('get_avatar', array( $this, 'realteo_gravatar_filter' ), 10, 6);
		//add_filter( 'woocommerce_prevent_admin_access', '__return_false' );
		//add_filter( 'woocommerce_disable_admin_bar', '__return_false' );
		//
		//dropdown
		//1. Add a new form element...
		add_action( 'register_form', array( $this, 'realteo_register_form' ),5);

	}


	function realteo_gravatar_filter($avatar, $id_or_email, $size, $default, $alt, $args) {
		
		if(is_object($id_or_email)) {
	      // Checks if comment author is registered user by user ID
	      
	      if($id_or_email->user_id != 0) {
	        $email = $id_or_email->user_id;
	      // Checks that comment author isn't anonymous
	      } elseif(!empty($id_or_email->comment_author_email)) {
	        // Checks if comment author is registered user by e-mail address
	        $user = get_user_by('email', $id_or_email->comment_author_email);
	        // Get registered user info from profile, otherwise e-mail address should be value
	        $email = !empty($user) ? $user->ID : $id_or_email->comment_author_email;
	      }
	      $alt = $id_or_email->comment_author;
	    } else {
	      if(!empty($id_or_email)) {
	        // Find user by ID or e-mail address
	        $user = is_numeric($id_or_email) ? get_user_by('id', $id_or_email) : get_user_by('email', $id_or_email);
	      } else {
	        // Find author's name if id_or_email is empty
	        $author_name = get_query_var('author_name');
	        if(is_author()) {
	          // On author page, get user by page slug
	          $user = get_user_by('slug', $author_name);
	        } else {
	          // On post, get user by author meta
	          $user_id = get_the_author_meta('ID');
	          $user = get_user_by('id', $user_id);
	        }
	      }
	      // Set user's ID and name
	      if(!empty($user)) {
	        $email = $user->ID;
	        $alt = $user->display_name;
	      }
	    }
		if( is_email( $email ) && ! email_exists( $email ) ) {
			return $avatar;
		}
	

		$class = array( 'avatar', 'avatar-' . (int) $args['size'], 'photo' );

		if ( ! $args['found_avatar'] || $args['force_default'] ) {
			$class[] = 'avatar-default';
		}

		if ( $args['class'] ) {
			if ( is_array( $args['class'] ) ) {
				$class = array_merge( $class, $args['class'] );
			} else {
				$class[] = $args['class'];
			}
		}

		$custom_avatar_id = get_user_meta($email, 'realteo_avatar_id', true); 
		$custom_avatar = wp_get_attachment_image_src($custom_avatar_id,'realteo-avatar');
		if ($custom_avatar)  {
			$return = '<img src="'.$custom_avatar[0].'" class="'.esc_attr( join( ' ', $class ) ).'" width="'.$size.'" height="'.$size.'" alt="'.$alt.'" />';
		} elseif ($avatar) {
			$return = $avatar;
		} else {
			$return = '<img src="'.$default.'" class="'.esc_attr( join( ' ', $class ) ).'" width="'.$size.'" height="'.$size.'" alt="'.$alt.'" />';
		}
		
		return $return;
		
	}
	
	/**
	 * Actions in dashboard
	 */
	public function dashboard_action_handler() {
		global $post;

		if ( is_page(realteo_get_option( 'my_properties_page' ) ) ) {
			if ( ! empty( $_REQUEST['action'] ) && ! empty( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], 'realteo_my_properties_actions' ) ) {

			$action = sanitize_title( $_REQUEST['action'] );
			$property_id = absint( $_REQUEST['property_id'] );

			try {
				// Get Job
				$property    = get_post( $property_id );
				$property_data = get_post( $property );
				if ( ! $property_data || 'property' !== $property_data->post_type ) {
					$title = false;
				} else {
					$title = esc_html( get_the_title( $property_data ) );	
				}

				
				switch ( $action ) {
					
					case 'delete' :
						// Trash it
						wp_trash_post( $property_id );

						// Message
						$this->dashboard_message =  '<div class="notification closeable success"><p>' . sprintf( __( '%s has been deleted', 'realteo' ), $title ) . '</p><a class="close" href="#"></a></div>';

						break;
					
					default :
						do_action( 'realteo_dashboard_do_action_' . $action );
						break;
				}

				do_action( 'realteo_my_property_do_action', $action, $property_id );

			} catch ( Exception $e ) {
				$this->dashboard_message = '<div class="notification closeable error">' . $e->getMessage() . '</div>';
			}
		}
		}
	}


	function author_archive_properties( $query ) {
	 
		if ( $query->is_author() && $query->is_archive() ) {
	        $query->set( 'post_type', array('property') );
	    }
		 
		return $query;

	}

	function modify_contact_methods($profile_fields) {

		// Add new fields
		$profile_fields['phone'] 	= 'Phone';
		$profile_fields['twitter'] 	= 'Twitter ';
		$profile_fields['facebook'] = 'Facebook URL';
		$profile_fields['gplus'] 	= 'Google+ URL';
		$profile_fields['linkedin'] = 'Linkedin';

		return $profile_fields;
	}

	function submit_my_account_form() {
		global $blog_id, $wpdb;
		if ( isset( $_POST['my-account-submission'] ) && '1' == $_POST['my-account-submission'] ) {
			$current_user = wp_get_current_user();
			$error = array();  

			if ( !empty( $_POST['url'] ) ) {
		       	wp_update_user( array ('ID' => $current_user->ID, 'user_url' => esc_attr( $_POST['url'] )));
			}

		    if ( !empty( $_POST['email'] ) ){
		        if (!is_email(esc_attr( $_POST['email'] )))
		            $error[] = __('The Email you entered is not valid.  please try again.', 'profile');
		        elseif(email_exists(esc_attr( $_POST['email'] )) != $current_user->ID)
		            $error[] = __('This email is already used by another user.  try a different one.', 'profile');
		        else{
		            wp_update_user( array ('ID' => $current_user->ID, 'user_email' => esc_attr( $_POST['email'] )));
		        }
		    }

		    if ( !empty( $_POST['first-name'] ) ) {
		        update_user_meta( $current_user->ID, 'first_name', esc_attr( $_POST['first-name'] ) );
		    }
		    
		    if ( !empty( $_POST['last-name'] ) ){
		        update_user_meta($current_user->ID, 'last_name', esc_attr( $_POST['last-name'] ) );
		    }		    
		    
		    if ( !empty( $_POST['display_name'] ) ) {
		        wp_update_user(array('ID' => $current_user->ID, 'display_name' => esc_attr( $_POST['display_name'] )));
		     	update_user_meta($current_user->ID, 'display_name' , esc_attr( $_POST['display_name'] ));
		    }
		    if ( !empty( $_POST['description'] ) ) {
		        update_user_meta( $current_user->ID, 'description', sanitize_textarea_field( $_POST['description'] ) );
		    }

		    if ( !empty( $_POST['realteo_avatar_id'] ) ) {
		        update_user_meta( $current_user->ID, 'realteo_avatar_id', esc_attr( $_POST['realteo_avatar_id'] ) );
		    }

		    $roles = $current_user->roles;
			$role = array_shift( $roles ); 
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
				
				if ( isset($_POST[$field['id']]) && !empty( $_POST[$field['id']] ) ){

			        update_user_meta($current_user->ID, $field['id'], esc_attr( $_POST[$field['id']] ) );
			    }
			endforeach;
	    	
		    
		   

			if ( count($error) == 0 ) {
		        //action hook for plugins and extra fields saving
		        do_action('edit_user_profile_update', $current_user->ID);
		        wp_redirect( get_permalink().'?updated=true' ); 
		        exit;
		    }       
		} // end if

	} // end 
	
	public function submit_change_password_form(){
		$error = '';
		if ( isset( $_POST['realteo-password-change'] ) && '1' == $_POST['realteo-password-change'] ) {
			$current_user = wp_get_current_user();
			if ( !empty($_POST['current_pass']) && !empty($_POST['pass1'] ) && !empty( $_POST['pass2'] ) ) {

				if ( !wp_check_password( $_POST['current_pass'], $current_user->user_pass, $current_user->ID) ) {
					/*$error = 'Your current password does not match. Please retry.';*/
					$error = 'error_1';
				} elseif ( $_POST['pass1'] != $_POST['pass2'] ) {
					/*$error = 'The passwords do not match. Please retry.';*/
					$error = 'error_2';
				} elseif ( strlen($_POST['pass1']) < 4 ) {
					/*$error = 'A bit short as a password, don\'t you think?';*/
					$error = 'error_3';
				} elseif ( false !== strpos( wp_unslash($_POST['pass1']), "\\" ) ) {
					/*$error = 'Password may not contain the character "\\" (backslash).';*/
					$error = 'error_4';
				} else {
					$user_id  = wp_update_user( array( 'ID' => $current_user->ID, 'user_pass' => esc_attr( $_POST['pass1'] ) ) );
					
					if ( is_wp_error( $user_id ) ) {
						/*$error = 'An error occurred while updating your profile. Please retry.';*/
						$error = 'error_5';
					} else {
						$error = false;
						do_action('edit_user_profile_update', $current_user->ID);
				        wp_redirect( get_permalink().'?updated=true' ); 
				        exit;
					}
				}
			
				if ( count($error) == 0 ) {
					do_action('edit_user_profile_update', $current_user->ID);
			        wp_redirect( get_permalink().'?updated=true' ); 
			        exit;
				} else {
					wp_redirect( get_permalink().'?err='.$error ); 
					exit;
					 
				}
				
			}
		} // end if
	}

	public function  extra_profile_fields( $user ) { ?>
		 
		<h3><?php esc_html_e('Realteo Avatar' , 'realteo' ); ?></h3>
		 
		<table class="form-table">
		<?php wp_enqueue_media(); ?>
		 
		<tr>
		<th><label for="image">Agent Avatar</label></th>
		 
		<td>
			<?php 
				$custom_avatar_id = get_the_author_meta( 'realteo_avatar_id', $user->ID ) ;
				$custom_avatar = wp_get_attachment_image_src($custom_avatar_id,'realteo-avatar');
				if ($custom_avatar)  {
					echo '<img src="'.$custom_avatar[0].'" style="width:100px;height: auto;"/><br>';
				} 
			?>
		<input type="text" name="realteo_avatar_id" id="agent-avatar" value="<?php echo esc_attr( get_the_author_meta( 'realteo_avatar_id', $user->ID ) ); ?>" class="regular-text" />
		<input type='button' class="realteo-additional-user-image button-primary" value="<?php _e( 'Upload Image', 'realteo' ); ?>" id="uploadimage"/><br />
		<span class="description"><?php esc_html_e('This avatar will be displayed instead of default one','realteo'); ?></span>
		</td>
		</tr>
		 
		</table>

		<h3><?php esc_html_e('Extra profile information' , 'realteo' ); ?></h3>
		 
		<table class="form-table">
		 
		<tr>
			<th><label for="image">Title</label></th>	 
			<td>
				<input type="text" name="agent_title" id="agent-title" value="<?php echo esc_attr( get_the_author_meta( 'agent_title', $user->ID ) ); ?>" class="regular-text" />
				<span class="description"><?php esc_html_e('This text will be displayed below your Name on Agents list','realteo'); ?></span>
			</td>
		</tr>
		<?php 
			
			$fields_owner = Realteo_Meta_Boxes::meta_boxes_user_owner();
			$fields_buyer = Realteo_Meta_Boxes::meta_boxes_user_buyer();
			$fields_agent = Realteo_Meta_Boxes::meta_boxes_user_agent();;
			$allfields = $fields_agent; //$fields_owner+$fields_buyer+
			foreach ( $allfields as $key => $field ) : 
			//	var_dump($field); ?>
	<!-- 			// name' => string 'testowy field' (length=13)
  // 'label' => string 'testowy field' (length=13)
  // 'id' => string '_testowy_field' (length=14)
  // 'type' => string 'text' (length=4)
  // 'invert' => boolean false
  // 'desc' => string '' (length=0)
  // 'options_source' => string '' (length=0)
  // 'options_cb' => string '' (length=0)
  // 'options' => --> 
		<tr>
			<th><label for="image"><?php echo $field['name']; ?></label></th>	 
			<td>

				<?php
				
				 switch ($field['type']) {
					case 'text':
						?>
						<input type="text" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>" value="<?php echo esc_attr( get_the_author_meta( $field['id'], $user->ID ) ); ?>" class="regular-text" />
				
						<?php
						break;
					
					case 'wp-editor':

							$editor = apply_filters( 'realteo_user_data_wp_editor_args', array(
								'textarea_name' => isset( $field['id'] ) ? $field['id'] : '',
								'media_buttons' => false,
								'textarea_rows' => 8,
								'quicktags'     => false,
								'tinymce'       => array(
									'plugins'                       => 'lists,paste,tabfocus,wplink,wordpress',
									'paste_as_text'                 => true,
									'paste_auto_cleanup_on_paste'   => true,
									'paste_remove_spans'            => true,
									'paste_remove_styles'           => true,
									'paste_remove_styles_if_webkit' => true,
									'paste_strip_class_attributes'  => true,
									'toolbar1'                      => 'bold,italic,|,bullist,numlist,|,link,unlink,|,undo,redo',
									'toolbar2'                      => '',
									'toolbar3'                      => '',
									'toolbar4'                      => ''
								),
							) );
							$field['value'] = get_the_author_meta( $field['id'], $user->ID );
							wp_editor( isset( $field['value'] ) ? wp_kses_post( $field['value'] ) : '', $key, $editor );
					break;
					case 'select':
						$field['value'] = get_the_author_meta( $field['id'], $user->ID );
						if(isset( $field['options_cb'] ) && !empty($field['options_cb'])){
							switch ($field['options_cb']) {
								case 'realteo_get_offer_types_flat':
									$field['options'] = realteo_get_offer_types_flat(false);
									break;

								case 'realteo_get_property_types':
									$field['options'] = realteo_get_property_types();
									break;

								case 'realteo_get_rental_period':
									$field['options'] = realteo_get_rental_period();
									break;
								
								default:
									# code...
									break;
							}	
						} ?>
						<select name="<?php echo esc_attr( isset( $field['id'] ) ? $field['id'] : $key ); ?>" id="<?php echo esc_attr( $key ); ?>"><?php if(isset($field['placeholder']) && !empty($field['placeholder'])) : ?>
								<option value=""><?php echo esc_attr($field['placeholder']);?></option>
							<?php endif ?>
							<?php foreach ( $field['options'] as $key => $value ) : ?>	
							<option value="<?php echo esc_attr( $key ); ?>" <?php 
								if ( isset( $field['value'] ) || isset( $field['default'] ) ) 
									if(isset( $field['value']) && is_array($field['value'])){
										if( in_array($key,$field['value']) ) {
											echo "selected='selected'";
										}
									} else {
										selected( isset( $field['value'] ) ? $field['value'] : $field['default'], $key );
									}
									 ?> >
								<?php echo esc_html( $value ); ?></option>
							<?php endforeach; ?>
						</select>
					<?php 
					break;

					case 'select_multiple':
					$field['value'] = get_the_author_meta( $field['id'], $user->ID );
					if(isset( $field['options_cb'] ) && !empty($field['options_cb'])){
							switch ($field['options_cb']) {
								case 'realteo_get_offer_types_flat':
									$field['options'] = realteo_get_offer_types_flat(false);
									break;

								case 'realteo_get_property_types':
									$field['options'] = realteo_get_property_types();
									break;

								case 'realteo_get_rental_period':
									$field['options'] = realteo_get_rental_period();
									break;
								
								default:
									# code...
									break;
							}	
						} ?>
							<select multiple name="<?php echo esc_attr($field['id']);?>[]" id="<?php echo esc_attr( $key ); ?>"><?php if(isset($field['placeholder']) && !empty($field['placeholder'])) : ?>
									<option value=""><?php echo esc_attr($field['placeholder']);?></option>
								<?php endif ?>
								<?php foreach ( $field['options'] as $key => $value ) : ?>	
								<option value="<?php echo esc_attr( $key ); ?>" <?php 
									if ( isset( $field['value'] ) || isset( $field['default'] ) ) 
										if(isset( $field['value']) && is_array($field['value'])){
											if( in_array($key,$field['value']) ) {
												echo "selected='selected'";
											}
										} else {
											selected( isset( $field['value'] ) ? $field['value'] : $field['default'], $key );
										}
										 ?> >
									<?php echo esc_html( $value ); ?></option>
								<?php endforeach; ?>
							</select>
						<?php 
					break;

					case 'checkbox':
					$field['value'] = get_the_author_meta( $field['id'], $user->ID );?>

						<input type="checkbox" name="<?php echo $field['id'] ?>"  value="<?php echo esc_attr($slug); ?>" <?php if(isset( $field['value']) && is_array($field['value'])){
							if( in_array($slug,$field['value']) ) {
								echo "checked";
							}
						}  ?>>
						
						
					<?php
					break;

					case 'multicheck_split':
					
					default:
						?>
						<input type="text" name="<?php echo $field['id']; ?>" id="<?php echo $field['id']; ?>" value="<?php echo esc_attr( get_the_author_meta( $field['id'], $user->ID ) ); ?>" class="regular-text" />
				
						<?php
						break;
						break;
				} ?>
				
			</td>
		</tr>
			
			<?php endforeach;
		?>
		
		
			
		  
		</table>
	<?php }


	function save_extra_profile_fields( $user_id ) {

		if ( !current_user_can( 'edit_user', $user_id ) )
		return false;
		if(isset($_POST['agent_title'])){
			update_user_meta( $user_id, 'agent_title', $_POST['agent_title'] );
		}
		$fields_owner = Realteo_Meta_Boxes::meta_boxes_user_owner();
		$fields_buyer = Realteo_Meta_Boxes::meta_boxes_user_buyer();
		$fields_agent = Realteo_Meta_Boxes::meta_boxes_user_agent();;
		$allfields = $fields_owner+$fields_buyer+$fields_agent;
		foreach ( $allfields as $key => $field ) {
		
			$id = $field['id'];
			if(isset($_POST[$id])){
				update_user_meta( $user_id, $field['id'],  $_POST[$field['id']] );
			}
		}
		
		update_user_meta( $user_id, 'realteo_avatar_id', $_POST['realteo_avatar_id'] );

	}

	public function my_account( $atts = array() ) {
		$template_loader = new Realteo_Template_Loader;
		ob_start();
		if ( is_user_logged_in() ) : 
		$template_loader->get_template_part( 'my-account' ); 
		else :
		$template_loader->get_template_part( 'account/login' ); 
		endif;
		return ob_get_clean();
	}		


	public function my_orders( $atts = array() ) {
		$template_loader = new Realteo_Template_Loader;
		ob_start();
		if ( is_user_logged_in() ) : 
		$template_loader->set_template_data( array( 'current' => 'my_properties' ) )->get_template_part( 'account/navigation' ); 
		$template_loader->get_template_part( 'account/my_orders' ); 
		else :
		$template_loader->get_template_part( 'account/login' ); 
		endif;
		return ob_get_clean();
	}	

	


	public function change_password( $atts = array() ) {
		$template_loader = new Realteo_Template_Loader;
		ob_start();
		$template_loader->set_template_data( array( 'current' => 'password' ) )->get_template_part( 'account/navigation' );
		$template_loader->get_template_part( 'account/change_password' ); 
		return ob_get_clean();
	}	

	public function lost_password( $atts = array() ) {
		$template_loader = new Realteo_Template_Loader;
		$errors = array();
		if ( isset( $_REQUEST['errors'] ) ) {
			$error_codes = explode( ',', $_REQUEST['errors'] );
			foreach ( $error_codes as $error_code ) {
				$errors[]= $this->get_error_message( $error_code );
			}
		} 
		ob_start();
		$template_loader->set_template_data( array( 'errors' => $errors ) )->get_template_part( 'account/lost_password' ); 
		return ob_get_clean();
	}


	public function reset_password( $atts = array() ) {
		$template_loader = new Realteo_Template_Loader;
		$attributes = array();
		if ( is_user_logged_in() ) {
			return __( 'You are already signed in.', 'realteo' );
		} else {
			if ( isset( $_REQUEST['login'] ) && isset( $_REQUEST['key'] ) ) {
				$attributes['login'] = $_REQUEST['login'];
				$attributes['key'] = $_REQUEST['key'];
				// Error messages
				$errors = array();
				if ( isset( $_REQUEST['error'] ) ) {
					$error_codes = explode( ',', $_REQUEST['error'] );
					foreach ( $error_codes as $code ) {
						$errors []= $this->get_error_message( $code );
					}
				}
				$attributes['errors'] = $errors;
				ob_start();
				$template_loader->set_template_data( array( 'attributes' => $attributes ) )->get_template_part( 'account/reset_password' ); 
				return ob_get_clean();
			} else if(isset( $_GET['password'] ) ) {
				return __( 'Password has been changed.', 'realteo' );
			} else if(isset( $_GET['checkemail'] ) ) {
				return __( 'A confirmation link has been sent to your email address.', 'realteo' );

			} else {
				return __( 'Invalid password reset link.', 'realteo' );
			}
		}
		
	}


	/**
	 * User properties shortcode
	 */
	public function realteo_my_properties( $atts ) {
		
		if ( ! is_user_logged_in() ) {
			return __( 'You need to be signed in to manage your properties.', 'realteo' );
		}

		extract( shortcode_atts( array(
			'posts_per_page' => '25',
		), $atts ) );

		ob_start();
		$template_loader = new Realteo_Template_Loader;

		$template_loader->set_template_data( array( 'current' => 'my_properties' ) )->get_template_part( 'account/navigation' ); 
		$template_loader->set_template_data( 
			array( 
				'message' => $this->dashboard_message, 
				'ids' => $this->get_agent_properties() 
			) )->get_template_part( 'account/my_properties' ); 


		return ob_get_clean();
	}	

	/**
	 * User properties shortcode
	 */
	public function realteo_my_packages( $atts ) {
		
		if ( ! is_user_logged_in() ) {
			return __( 'You need to be signed in to manage your packages.', 'realteo' );
		}

		extract( shortcode_atts( array(
			'posts_per_page' => '25',
		), $atts ) );

		ob_start();
		$template_loader = new Realteo_Template_Loader;

		$template_loader->set_template_data( array( 'current' => 'my_packages' ) )->get_template_part( 'account/navigation' ); 
		$template_loader->set_template_data( array( 'ids' => $this->get_agent_properties() ) )->get_template_part( 'account/my_packages' ); 


		return ob_get_clean();
	}


	/**
	 * Function to get ids added by the user/agent
	 * @return array array of property ids
	 */
	public function get_agent_properties(){
		$current_user = wp_get_current_user();
		
		return get_posts(array(
			'author'        =>  $current_user->ID,
		    'fields'          => 'ids', // Only get post IDs
		    'posts_per_page'  => -1,
		    'post_type'		  => 'property',
		    'post_status'	  => array('publish','pending_payment','expired','draft','pending'),
		));
	}

	/**
	 * Redirects the user to the correct page depending on whether he / she
	 * is an admin or not.
	 *
	 * @param string $redirect_to   An optional redirect_to URL for admin users
	 */
	private function redirect_logged_in_user( $redirect_to = null ) {
	    $user = wp_get_current_user();
	    if ( user_can( $user, 'manage_options' ) ) {
	        if ( $redirect_to ) {
	            wp_safe_redirect( $redirect_to );
	        } else {
	            wp_redirect( admin_url() );
	        }
	    } else {
	        wp_redirect( home_url( get_permalink(realteo_get_option( 'my_account_page' )) ) );
	    }
	}


	public function woocommerce_account_redirect() {
		if(class_exists( 'WooCommerce' )) {
			if (is_user_logged_in() && is_account_page() && !is_view_order_page() ) {
					wp_redirect( get_permalink( realteo_get_option( 'my_account_page' )) );
					exit;
			}
		} 
		
	}

	public function redirect_woocommerce( $redirect_to ) {
	
	    $redirect_to = get_permalink(realteo_get_option( 'my_account_page' ));	 
    	return $redirect_to;
	}
	
	/**
	 * Redirect the user to the custom login page instead of wp-login.php.
	 */
	function redirect_to_custom_login() {
	    if ( $_SERVER['REQUEST_METHOD'] == 'GET' ) {
	        $redirect_to = isset( $_REQUEST['redirect_to'] ) ? $_REQUEST['redirect_to'] : null;
	     
	        if ( is_user_logged_in() ) {
	            $this->redirect_logged_in_user( $redirect_to );
	            exit;
	        }
	 
	        // The rest are redirected to the login page
	        $login_url = get_permalink(realteo_get_option( 'my_account_page' ));
	        if ( ! empty( $redirect_to ) ) {
	            $login_url = add_query_arg( 'redirect_to', $redirect_to, $login_url );
	        }
	 
	        wp_redirect( $login_url );
	        exit;
	    }
	}

	/**
	 * Redirects the user to the custom "Forgot your password?" page instead of
	 * wp-login.php?action=lostpassword.
	 */
	public function redirect_to_custom_lostpassword() {

	    if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
	        if ( is_user_logged_in() ) {
	            $this->redirect_logged_in_user();
	            exit;
	        }
	 
	 		$lost_password_page = realteo_get_option( 'lost_password_page' );
	 		if(!empty($lost_password_page)) {
	 			wp_redirect(get_permalink($lost_password_page ));	
	 		} else {
	 			echo "Please set a Lost Password Page in Realteo Options -> Pages";
	 		}
	        
	        exit;
	    }
	}

	/**
	 * Initiates password reset.
	 */
	public function do_password_lost() {
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			$errors = retrieve_password();
			if ( is_wp_error( $errors ) ) {
				// Errors found
				$redirect_url = get_permalink(realteo_get_option( 'reset_password_page' ));
				$redirect_url = add_query_arg( 'errors', join( ',', $errors->get_error_codes() ), $redirect_url );
			} else {
				// Email sent
				$redirect_url = get_permalink(realteo_get_option( 'reset_password_page' ));
				$redirect_url = add_query_arg( 'checkemail', 'confirm', $redirect_url );
				if ( ! empty( $_REQUEST['redirect_to'] ) ) {
					$redirect_url = $_REQUEST['redirect_to'];
				}
			}
			wp_safe_redirect( $redirect_url );
			exit;
		}
	}

	/**
	 * Redirects to the custom password reset page, or the login page
	 * if there are errors.
	 */
	public function redirect_to_custom_password_reset() {
		if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
			// Verify key / login combo
			$user = check_password_reset_key( $_REQUEST['key'], $_REQUEST['login'] );
			if ( ! $user || is_wp_error( $user ) ) {
				if ( $user && $user->get_error_code() === 'expired_key' ) {
					wp_redirect( get_permalink(realteo_get_option( 'lost_password_page' )).'?login=expiredkey' );
				} else {
					wp_redirect( get_permalink(realteo_get_option( 'lost_password_page' )).'?login=invalidkey');
				}
				exit;
			}
			$redirect_url = get_permalink(realteo_get_option( 'reset_password_page' ));
			$redirect_url = add_query_arg( 'login', esc_attr( $_REQUEST['login'] ), $redirect_url );
			$redirect_url = add_query_arg( 'key', esc_attr( $_REQUEST['key'] ), $redirect_url );
			wp_redirect( $redirect_url );
			exit;
		}
	}


	/**
	 * Redirects the user to the custom registration page instead
	 * of wp-login.php?action=register.
	 */
	public function redirect_to_custom_register() {
	    if ( 'GET' == $_SERVER['REQUEST_METHOD'] ) {
	        if ( is_user_logged_in() ) {
	            $this->redirect_logged_in_user();
	        } else {
	            wp_redirect( get_permalink(realteo_get_option( 'my_account_page' )) );
	        }
	        exit;
	    }
	}

	/**
	 * Redirect the user after authentication if there were any errors.
	 *
	 * @param Wp_User|Wp_Error  $user       The signed in user, or the errors that have occurred during login.
	 * @param string            $username   The user name used to log in.
	 * @param string            $password   The password used to log in.
	 *
	 * @return Wp_User|Wp_Error The logged in user, or error information if there were errors.
	 */
	function maybe_redirect_at_authenticate( $user, $username, $password ) {
	    // Check if the earlier authenticate filter (most likely, 
	    // the default WordPress authentication) functions have found errors
	    if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
	        if ( is_wp_error( $user ) ) {
	            $error_codes = join( ',', $user->get_error_codes() );
	 
	            	$login_url = get_permalink(realteo_get_option( 'my_account_page' ));
	            	$login_url = add_query_arg( 'login', $error_codes, $login_url );
	 
	            wp_redirect( $login_url );
	            exit;
	        }
	    }
	 
	    return $user;
	}


	/**
	 * Finds and returns a matching error message for the given error code.
	 *
	 * @param string $error_code    The error code to look up.
	 *
	 * @return string               An error message.
	 */
	private function get_error_message( $error_code ) {
	    switch ( $error_code ) {
	        case 'empty_username':
	            return __( 'You do have an email address, right?', 'realteo' );
	 
	        case 'empty_password':
	            return __( 'You need to enter a password to login.', 'realteo' );
	    	
			case 'first_name':
	            return __( 'You need to provide your first name.', 'realteo' );
	 
			case 'last_name':
	            return __( 'You need to provide your last name.', 'realteo' );
	 
	        case 'invalid_username':
	            return __(
	                "We don't have any users with that email address. Maybe you used a different one when signing up?",
	                'realteo'
	            );
	 
	        case 'incorrect_password':
	            $err = __(
	                "The password you entered wasn't quite right. <a href='%s'>Did you forget your password</a>?",
	                'realteo'
	            );
	            return sprintf( $err, wp_lostpassword_url() );
	 
	        default:
	            break;
	    }
	     
	    return __( 'An unknown error occurred. Please try again later.', 'realteo' );
	}


	/**
	 * Returns the URL to which the user should be redirected after the (successful) login.
	 *
	 * @param string           $redirect_to           The redirect destination URL.
	 * @param string           $requested_redirect_to The requested redirect destination URL passed as a parameter.
	 * @param WP_User|WP_Error $user                  WP_User object if login was successful, WP_Error object otherwise.
	 *
	 * @return string Redirect URL
	 */
	public function redirect_after_login( $redirect_to, $requested_redirect_to, $user ) {
	    $redirect_url = home_url();
	 
	    if ( ! isset( $user->ID ) ) {
	        return $redirect_url;
	    }
	 
	    if ( user_can( $user, 'manage_options' ) ) {
	        // Use the redirect_to parameter if one is set, otherwise redirect to admin dashboard.
	        if ( $requested_redirect_to == '' ) {
	            $redirect_url = admin_url();
	        } else {
	            $redirect_url = $requested_redirect_to;
	        }
	    } else {
	        // Non-admin users always go to their account page after login
	        $redirect_url = get_permalink(realteo_get_option( 'my_account_page' ));
	    }
	 
	    return wp_validate_redirect( $redirect_url, home_url() );
	}

	/**
	 * Validates and then completes the new user signup process if all went well.
	 *
	 * @param string $email         The new user's email address
	 * @param string $first_name    The new user's first name
	 * @param string $last_name     The new user's last name
	 *
	 * @return int|WP_Error         The id of the user that was created, or error if failed.
	 */
	private function register_user( $email, $first_name, $last_name, $role,$password ) {
	    $errors = new WP_Error();
	 
	    // Email address is used as both username and email. It is also the only
	    // parameter we need to validate
	    if ( ! is_email( $email ) ) {
	        $errors->add( 'email', $this->get_error_message( 'email' ) );
	        return $errors;
	    }
		
		if ( empty( $first_name ) ) {
	        $errors->add( 'first_name', $this->get_error_message( 'first_name' ) );
	        return $errors;
	    }
		
		if ( empty( $last_name ) ) {
	        $errors->add( '$last_name', $this->get_error_message( 'last_name' ) );
	        return $errors;
	    }
	 
	    if ( username_exists( $email ) || email_exists( $email ) ) {
	        $errors->add( 'email_exists', $this->get_error_message( 'email_exists') );
	        return $errors;
	    }
	 
	    // Generate the password so that the subscriber will have to check email...
	    if(!$password) {  
		    $password = wp_generate_password( 12, false );
		}

	    $user_data = array(
	        'user_login'    => $email,
	        'user_email'    => $email,
	        'user_pass'     => $password,
	        'first_name'    => $first_name,
	        'last_name'     => $last_name,
	        'nickname'      => $first_name,
	        'role'			=> $role
	    );
	 
	    $user_id = wp_insert_user( $user_data );
	    //update_user_meta( $user_id, 'zip_code', $input['zip_code'] );

	    //wp_set_password($user_id,$password);
	    wp_new_user_notification( $user_id, $password,'both' );
	 
	    return $user_id;
	}


	/**
	 * Handles the registration of a new user.
	 *
	 * Used through the action hook "login_form_register" activated on wp-login.php
	 * when accessed through the registration action.
	 */
	public function do_register_user() {
	    if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
	        $redirect_url = get_permalink(realteo_get_option( 'my_account_page' )).'#tab2';
	 
	        if ( ! get_option( 'users_can_register' ) ) {
	            // Registration closed, display error
	            $redirect_url = add_query_arg( 'register-errors', 'closed', $redirect_url );
	        } else {
	            $email = $_POST['email'];
	            $first_name = sanitize_text_field( $_POST['first_name'] );
	            $last_name = sanitize_text_field( $_POST['last_name'] );
	            $role = sanitize_text_field( $_POST['role'] );
	            $password = (!empty($_POST['password'])) ? sanitize_text_field( $_POST['password'] ) : false ;
	            $recaptcha_status = realteo_get_option('realteo_recaptcha');
	            $privacy_policy_status = realteo_get_option('realteo_privacy_policy');

	            if($recaptcha_status) {
	            	if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])):
				        //your site secret key
				        $secret = realteo_get_option('realteo_recaptcha_secretkey');
				        //get verify response data
				        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
				        $responseData = json_decode($verifyResponse);
						if( $responseData->success ):
							//passed captcha, proceed to register
				            $result = $this->register_user( $email, $first_name, $last_name, $role, $password );
				 
				            if ( is_wp_error( $result ) ) {
				                // Parse errors into a string and append as parameter to redirect
				                $errors = join( ',', $result->get_error_codes() );
				                $redirect_url = add_query_arg( 'register-errors', $errors, $redirect_url );
				            } else {
				                // Success, redirect to login page.
				                $redirect_url = get_permalink(realteo_get_option( 'my_account_page' ));
				                $redirect_url = add_query_arg( 'registered', $email, $redirect_url );
				            }
			        	else:
			        		$redirect_url = add_query_arg( 'register-errors', 'captcha-fail', $redirect_url );
		        		endif;
		        	else:
		        		$redirect_url = add_query_arg( 'register-errors', 'captcha-no', $redirect_url );
	        		endif;
	            } else {
	            	if($privacy_policy_status) {
	            		if(isset($_POST['privacy_policy']) && !empty($_POST['privacy_policy'])):
	            			$result = $this->register_user( $email, $first_name, $last_name, $role, $password );
		            		if ( is_wp_error( $result ) ) {
				                // Parse errors into a string and append as parameter to redirect
				                $errors = join( ',', $result->get_error_codes() );
				                $redirect_url = add_query_arg( 'register-errors', $errors, $redirect_url );
				            } else {
				                // Success, redirect to login page.
				                $redirect_url = get_permalink(realteo_get_option( 'my_account_page' ));
				                $redirect_url = add_query_arg( 'registered', $email, $redirect_url );
				            }
	            		else :
	            			$redirect_url = add_query_arg( 'register-errors', 'policy-fail', $redirect_url );
	            		endif;
	            	} else {


		            	$result = $this->register_user( $email, $first_name, $last_name, $role, $password );
					 
			            if ( is_wp_error( $result ) ) {
			                // Parse errors into a string and append as parameter to redirect
			                $errors = join( ',', $result->get_error_codes() );
			                $redirect_url = add_query_arg( 'register-errors', $errors, $redirect_url );
			            } else {
			                // Success, redirect to login page.
			                $redirect_url = get_permalink(realteo_get_option( 'my_account_page' ));
			                $redirect_url = add_query_arg( 'registered', $email, $redirect_url );
			            }
		            }
	            }
			    
	        }
	 
	        wp_redirect( $redirect_url );
	        exit;
	    }
	}

	/**
	 * Resets the user's password if the password reset form was submitted.
	 */
	public function do_password_reset() {
		if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
			$rp_key = $_REQUEST['rp_key'];
			$rp_login = $_REQUEST['rp_login'];
			$user = check_password_reset_key( $rp_key, $rp_login );
			if ( ! $user || is_wp_error( $user ) ) {
				if ( $user && $user->get_error_code() === 'expired_key' ) {
					wp_redirect( home_url( 'member-login?login=expiredkey' ) );
				} else {
					wp_redirect( home_url( 'member-login?login=invalidkey' ) );
				}
				exit;
			}
			if ( isset( $_POST['pass1'] ) ) {
				if ( $_POST['pass1'] != $_POST['pass2'] ) {
					// Passwords don't match
					$redirect_url = get_permalink(realteo_get_option( 'reset_password_page' ));
					$redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
					$redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
					$redirect_url = add_query_arg( 'error', 'password_reset_mismatch', $redirect_url );
					wp_redirect( $redirect_url );
					exit;
				}
				if ( empty( $_POST['pass1'] ) ) {
					// Password is empty
					$redirect_url = get_permalink(realteo_get_option( 'reset_password_page' ));
					$redirect_url = add_query_arg( 'key', $rp_key, $redirect_url );
					$redirect_url = add_query_arg( 'login', $rp_login, $redirect_url );
					$redirect_url = add_query_arg( 'error', 'password_reset_empty', $redirect_url );
					wp_redirect( $redirect_url );
					exit;
				}
				// Parameter checks OK, reset password
				reset_password( $user, $_POST['pass1'] );
				$redirect_url = get_permalink(realteo_get_option( 'reset_password_page' ));
				$redirect_url = add_query_arg( 'password', 'changed', $redirect_url );
				wp_redirect(  $redirect_url );
			} else {
				echo "Invalid request.";
			}
			exit;
		}
	}

	function remove_filter_lostpassword() {
	  remove_filter( 'lostpassword_url', 'wc_lostpassword_url', 10 );
	}

	function realteo_register_form() {

		global $wp_roles;
	    echo '<select name="role" class="input">';
	    foreach ( $wp_roles->roles as $key=>$value ) {
	       // Exclude default roles such as administrator etc. Add your own
	       if ( in_array( $value['name'],  array('Agent','Owner','Buyer' ) ) ) {
	          echo '<option value="'.$key.'">'.$value['name'].'</option>';
	       }
	    }
	    echo '</select>';

	}


}


/* TODO: move it to the class*/
function realteo_avatar_filter() {

  // Add to edit_user_avatar hook
  add_action('edit_user_avatar', array('wp_user_avatar', 'wpua_action_show_user_profile'));
  add_action('edit_user_avatar', array('wp_user_avatar', 'wpua_media_upload_scripts'));
}

// Loads only outside of administration panel
if(!is_admin()) {
  add_action('init','realteo_avatar_filter');
}

// Redefine user notification function
if ( !function_exists('wp_new_user_notification') ) {
    function wp_new_user_notification( $user_id, $plaintext_pass = '' ) {
        $user = new WP_User($user_id);
 
        $user_login = stripslashes($user->user_login);
        $user_email = stripslashes($user->user_email);
 
        $message  = sprintf(__('New user registration on your blog %s:','realteo'), get_option('blogname')) . "\r\n\r\n";
        $message .= sprintf(__('Username: %s','realteo'), $user_login) . "\r\n\r\n";
        $message .= sprintf(__('E-mail: %s','realteo'), $user_email) . "\r\n";
 
        @wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration','realteo'), get_option('blogname')), $message);
 
        if ( empty($plaintext_pass) )
            return;

		if( function_exists('realteo_get_option') && get_option('findeo_submit_display',true) ) {
		 	$login_url = get_permalink( realteo_get_option( 'my_account_page' ) );
		} else {
		 	$login_url = wp_login_url();
		}
        $message  = __('Hi,','realteo') . "\r\n\r\n";
        $message .= sprintf(__("Welcome to %s! You can log in:",'realteo'), get_option('blogname')) . "\r\n\r\n";
        $message .= $login_url . "\r\n";
        $message .= sprintf(__('Username: %s','realteo'), $user_login) . "\r\n";
        $message .= sprintf(__('Password: %s','realteo'), $plaintext_pass) . "\r\n\r\n";
        $message .= sprintf(__('If you have any problems, please contact me at %s.','realteo'), get_option('admin_email')) . "\r\n\r\n";
        $message .= __('Thank you!','realteo');
 
        wp_mail($user_email, sprintf(__('[%s] Your username and password','realteo'), get_option('blogname')), $message);
 
    }
}



/**
 * Register exporter for Realteo user data.
 *
 * @see https://github.com/allendav/wp-privacy-requests/blob/master/EXPORT.md
 *
 * @param $exporters
 *
 * @return array
 */
function realteo_register_exporters( $exporters ) {
	$exporters[] = array(
		'exporter_friendly_name' => __( 'Realteo Properties & Accounts' ),
		'callback'               => 'realteo_user_data_exporter',
	);

	return $exporters;
}

add_filter( 'wp_privacy_personal_data_exporters', 'realteo_register_exporters' );

/**
 * Exporter for Plugin user data.
 *
 * @see https://github.com/allendav/wp-privacy-requests/blob/master/EXPORT.md
 *
 * @param     $email_address
 * @param int $page
 *
 * @return array
 */
function realteo_user_data_exporter( $email_address, $page = 1 ) {
	$export_items = array();

	$user = get_user_by( 'email', $email_address );

	if ( $user && $user->ID ) {
		
		// Plugins can add as many items in the item data array as they want
		$data = array();

		$args = array(
		    'author' => $user->ID ,
		    'post_type' => 'property',
		);
		$author_posts = new WP_Query( $args );
		if ($author_posts->have_posts()):
			while ($author_posts->have_posts()) : 
				$author_posts->the_post();

				$data = array(
					array(
			          'name' => __( 'Property Title' ),
			          'value' => get_the_title(),
			        ),
			        array(
			          'name' => __( 'Property URL' ),
			          'value' => get_permalink(),
			        )
				);
				$post_id = get_the_ID();
				$item_id = "realteo-properties-{$post_id}";

				// Core group IDs include 'comments', 'posts', etc.
				// But you can add your own group IDs as needed
				$group_id = 'realteo';

				// Optional group label. Core provides these for core groups.
				// If you define your own group, the first exporter to
				// include a label will be used as the group label in the
				// final exported report
				$group_label = __( 'Realteo Properties' );

		     	$export_items[] = array(
					'group_id'    => $group_id,
					'group_label' => $group_label,
					'item_id'     => $item_id,
					'data'        => $data,
				);

		endwhile; endif;
		wp_reset_postdata();
		wp_reset_query();

		$data = array();
		$group_id = 'agent';
		$group_label = __( 'Agent Data' );
		$fields = Realteo_Meta_Boxes::meta_boxes_user_agent();
		foreach ( $fields as $key => $field ) : 
	
			$data[] = 
					
			        array(
			          'name' =>  $field['label'],
			          'value' => $user->$key,
			        
				);
		endforeach;
		$export_items[] = array(
			'group_id'    => $group_id,
			'group_label' => $group_label,
			'item_id'     => $item_id,
			'data'        => $data,
		);
		// Add this group of items to the exporters data array.
		

	}

	// Returns an array of exported items for this pass, but also a boolean whether this exporter is finished.
	//If not it will be called again with $page increased by 1.
	return array(
		'data' => $export_items,
		'done' => true,
	);
}