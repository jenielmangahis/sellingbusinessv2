<?php 
$errors = array();

if ( isset( $_REQUEST['login'] ) ) {
    $error_codes = explode( ',', $_REQUEST['login'] );
 
    foreach ( $error_codes as $code ) {
       switch ( $code ) {
	        case 'empty_username':
	            $errors[] = esc_html__( 'You do have an email address, right?', 'realteo' );
	   		break;
	        case 'empty_password':
	            $errors[] =  esc_html__( 'You need to enter a password to login.', 'realteo' );
	   		break;
	        case 'invalid_username':
	            $errors[] =  esc_html__(
	                "We don't have any users with that email address. Maybe you used a different one when signing up?",
	                'realteo'
	            );
	   		break;
	        case 'incorrect_password':
	            $err = __(
	                "The password you entered wasn't quite right. <a href='%s'>Did you forget your password</a>?",
	                'realteo'
	            );
	            $errors[] =  sprintf( $err, wp_lostpassword_url() );
	 		break;
	        default:
	            break;
	    }
    }
} 
 // Retrieve possible errors from request parameters
if ( isset( $_REQUEST['register-errors'] ) ) {
    $error_codes = explode( ',', $_REQUEST['register-errors'] );
 
    foreach ( $error_codes as $error_code ) {
 		
         switch ( $error_code ) {
	        case 'email':
			     $errors[] = esc_html__( 'The email address you entered is not valid.', 'realteo' );
			   break;
			case 'email_exists':
			     $errors[] = esc_html__( 'An account exists with this email address.', 'realteo' );
			 	  break;
			case 'closed':
			     $errors[] = esc_html__( 'Registering new users is currently not allowed.', 'realteo' );
			     break;
	 		case 'captcha-no':
			     $errors[] = esc_html__( 'Please check reCAPTCHA checbox to register.', 'realteo' );
			     break;
			case 'captcha-fail':
			     $errors[] = esc_html__( "You're a bot, aren't you?.", 'realteo' );
			     break;
			case 'policy-fail':
			     $errors[] = esc_html__( "Please accept the Privacy Policy to register account.", 'realteo' );
			     break;
			case 'first_name':
			     $errors[] = esc_html__( "Please provide your first name", 'realteo' );
			     break;
			case 'last_name':
			     $errors[] = esc_html__( "Please provide your last name", 'realteo' );
			     break;
	 
	        case 'incorrect_password':
	            $err = esc_html__(
	                "The password you entered wasn't quite right. <a href='%s'>Did you forget your password</a>?",
	                'realteo'
	            );
	            $errors[] =  sprintf( $err, wp_lostpassword_url() );
	   			break;
	        default:
	            break;
	    }
    }
} ?>

	<div class="row">
	<div class="col-md-4 col-md-offset-4">
	
	<!--Tab -->
		<div class="my-account style-1 margin-top-5 margin-bottom-40">

				<?php if ( isset( $_REQUEST['registered'] ) ) : ?>
				    <div class="notification success closeable">
				    <p>
				        <?php
				        $password_field = realteo_get_option('realteo_generate_password');
				        if($password_field) {
							printf(
				                __( 'You have successfully registered to <strong>%s</strong>.', 'realteo' ),
				                get_bloginfo( 'name' )
				            );
				        } else {
				        	printf(
				                __( 'You have successfully registered to <strong>%s</strong>. We have emailed your password to the email address you entered.', 'realteo' ),
				                get_bloginfo( 'name' )
				            );
				        }
				            
				        ?>
				    </p></div>
				<?php endif; ?>
					<?php if ( count( $errors ) > 0 ) : ?>
					    <?php foreach ( $errors  as $error ) : ?>
					        <div class="notification error closeable">
								<p><?php echo $error; ?></p>
								<a class="close"></a>
							</div>
					    <?php endforeach; ?>
					<?php endif; ?>

			<?php do_action( 'wordpress_social_login' ); ?>
			<ul class="tabs-nav">
				<li class=""><a href="#tab1"><?php esc_html_e('Log In','realteo'); ?></a></li>
				<li><a href="#tab2"><?php esc_html_e('Register','realteo'); ?></a></li>
			</ul>

			<div class="tabs-container alt">
			
			<!-- Login -->
			<div class="tab-content" id="tab1" style="display: none;">
				<!--Tab -->

		
				<?php

				/*WPEngine compatibility*/
				if (defined('PWP_NAME')) { ?>
					<form method="post" class="login" action="<?php echo wp_login_url().'?wpe-login=';echo PWP_NAME;?>">
				<?php } else { ?>
					<form method="post" class="login" action="<?php echo wp_login_url(); ?>">
				<?php } ?>

				    <p class="form-row form-row-wide">
							<label for="username"><?php _e( 'Username/Email:', 'realteo' ); ?>
							<i class="im im-icon-Male"></i>
							<input type="text" class="input-text" name="log" id="user_login" value="" />
						</label>
					</p>
					<p class="form-row form-row-wide">
						<label for="password"><?php _e( 'Password:', 'realteo' ); ?>
							<i class="im im-icon-Lock-2"></i>
							<input class="input-text" type="password" name="pwd" id="user_pass"/>
						</label>
					</p>
				   <p class="form-row">
						<input type="submit" class="button border margin-top-10" name="login" value="<?php _e( 'Sign In', 'realteo' ); ?>" />

						<label for="rememberme" class="rememberme">
						<input name="rememberme" type="checkbox" id="rememberme" value="forever" /> <?php esc_html_e('Remember Me','realteo'); ?></label>
					</p>
				    <p class="lost_password">
						<a href="<?php echo wp_lostpassword_url(); ?>"> <?php esc_html_e('Lost Your Password?','realteo'); ?></a>
					</p>
				</form>
	
			</div>

			<!-- Register -->
			<div class="tab-content" id="tab2" style="display: none;">
				<?php 
				if ( is_user_logged_in() ) {
				    esc_html_e( 'You are already signed in.', 'realteo' );
				} elseif ( ! get_option( 'users_can_register' ) ) {
				    esc_html_e( 'Registering new users is currently not allowed.', 'realteo' );
				} else { ?>
     
		    	<?php
				/*WPEngine compatibility*/
				if (defined('PWP_NAME')) { ?>
					<form id="signupform" action="<?php echo wp_registration_url().'&wpe-login=';echo PWP_NAME; ?>" method="post">
				<?php } else { ?>
					<form id="signupform" action="<?php echo wp_registration_url(); ?>" method="post">
				<?php } ?>
			        <p class="form-row">
			            <label for="email"><?php esc_html_e( 'Email', 'realteo' ); ?> <strong>*</strong></label>
			            <input type="text" name="email" id="email">
			        </p>
			 		
			 		<?php if(realteo_get_option('realteo_generate_password')) : ?>
			        <p class="form-row">
			            <label for="password"><?php esc_html_e( 'Password', 'realteo' ); ?></label>
			            <input type="password" name="password" id="password">
			        </p>
			    	<?php endif; ?>

			        <p class="form-row">
			            <label for="first_name"><?php esc_html_e( 'First name', 'realteo' ); ?></label>
			            <input type="text" name="first_name" id="first-name">
			        </p>
			 
			        <p class="form-row">
			            <label for="last_name"><?php esc_html_e( 'Last name', 'realteo' ); ?></label>
			            <input type="text" name="last_name" id="last-name">
			        </p>

			        <?php do_action('realteo_registration_form') ?>

			        <?php $role_status = realteo_get_option('realteo_registration_role'); 
			        if($role_status) { ?>
						<input type="hidden" name="role" value="agent">
			        <?php } else { ?>
				 		<p class="form-row">
				 			<label for="role"><?php esc_html_e( 'User role', 'realteo' ); ?></label>
				 			<?php 
				 				global $wp_roles;
							    echo '<select name="role" class="chosen-select-no-single">';
							    foreach ( $wp_roles->roles as $key=>$value ) {
							       // Exclude default roles such as administrator etc. Add your own
							       if ( in_array( $value['name'],  array('Agent','Owner','Buyer' ) ) ) {
							          echo '<option value="'.$key.'">'.$value['name'].'</option>';
							       }
							    }
							    echo '</select>';
							?>
				 		</p>
			 		<?php } ?>
			        <p class="form-row margin-top-30 margin-bottom-30">
			            <?php esc_html_e( 'Note: Your password will be generated automatically and sent to your email address.', 'realteo' ); ?>
			        </p>
					<?php $recaptcha_status = realteo_get_option('realteo_recaptcha');
	            	if($recaptcha_status) { ?>
			        <p class="form-row captcha_wrapper">
						<div class="g-recaptcha" data-sitekey="<?php echo realteo_get_option('realteo_recaptcha_sitekey'); ?>"></div>
					</p>
			 		<?php } ?>
			 		<?php 
			 		 $privacy_policy_status = realteo_get_option('realteo_privacy_policy');

			 			if ( $privacy_policy_status && function_exists( 'the_privacy_policy_link' ) ) { ?>
							<p class="form-row margin-top-30 margin-bottom-30">
					            <label for="privacy_policy"><input type="checkbox" name="privacy_policy"><?php esc_html_e( 'I agree to the', 'realteo' ); ?> <a href="<?php echo get_privacy_policy_url(); ?>"><?php esc_html_e( 'Privacy Policy', 'realteo' ); ?></a>    </label>
					        
					        </p>
						        
						<?php } ?>
			        <p class="signup-submit">
			            <input type="submit" name="submit" class="register-button"  value="<?php esc_html_e( 'Register', 'realteo' ); ?>"/>
			        </p>
			    </form>
			
		    <?php } ?>
			</div>
			

		</div>
	</div>