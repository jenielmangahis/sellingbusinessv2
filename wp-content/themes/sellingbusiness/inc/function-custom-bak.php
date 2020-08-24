<?php 
if ( !defined('ABSPATH')) exit;



function subscribe_user ($email, $first_name='', $last_name='') {

	$curl = curl_init();
	$r_id = '';

    curl_setopt_array($curl, array(

      CURLOPT_URL => "https://api.sendgrid.com/v3/contactdb/recipients",

      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => "",
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_TIMEOUT => 30,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => "POST",
      CURLOPT_POSTFIELDS => "[{\"email\": \"$email\"}]",
      CURLOPT_HTTPHEADER => array(
        "authorization: Bearer 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",
        "content-type: application/json"
      ),
    ));

    $response = curl_exec($curl);
    $err = curl_error($curl);

    curl_close($curl);

    if ($err) {
      //echo "cURL Error #:" . $err;
    } else {
      //echo $response;
        $a = json_decode($response);
        $b = $a->persisted_recipients; //get id of email 
        $r_id  = $b[0];                // store it

    }
}
//add_user_new('bonsai.christiand@gmail.com');
/**
	 * Handles the registration of a new user.
	 *
	 * Used through the action hook "login_form_register" activated on wp-login.php
	 * when accessed through the registration action.
	 */
function do_custom_register_user() {
		
		$custom_redirect_page = 'registration-successful';
		
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
				$phone = sanitize_text_field( $_POST['phone'] );
				$postcode = sanitize_text_field( $_POST['postcode'] );
				$sbscrb = $_POST['sbscrb'];

	            if($recaptcha_status) {
	            	if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])):
				        //your site secret key
				        $secret = realteo_get_option('realteo_recaptcha_secretkey');
				        //get verify response data
				        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
				        $responseData = json_decode($verifyResponse);
						if( $responseData->success ):
							//passed captcha, proceed to register
				            $result = custom_register_user( $email, $first_name, $last_name, $role, $phone, $postcode, $password );
								
				            if ( is_wp_error( $result ) ) {
				                // Parse errors into a string and append as parameter to redirect
				                $errors = join( ',', $result->get_error_codes() );
				                $redirect_url = add_query_arg( 'register-errors', $errors, $redirect_url );
				            } else {
								if($sbscrb)
									subscribe_user($email);
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
	            			$result = custom_register_user( $email, $first_name, $last_name, $role, $phone, $postcode, $password );
		            		if ( is_wp_error( $result ) ) {
				                // Parse errors into a string and append as parameter to redirect
				                $errors = join( ',', $result->get_error_codes() );
				                $redirect_url = add_query_arg( 'register-errors', $errors, $redirect_url );
				            } else {
								if($sbscrb)
									subscribe_user($email);
				                // Success, redirect to login page.
				                $redirect_url = get_permalink(realteo_get_option( 'my_account_page' ));
				                $redirect_url = add_query_arg( 'registered', $email, $redirect_url );
				            }
	            		else :
	            			$redirect_url = add_query_arg( 'register-errors', 'policy-fail', $redirect_url );
	            		endif;
	            	} else {


		            	$result = custom_register_user( $email, $first_name, $last_name, $role, $phone, $postcode, $password );
					 
			            if ( is_wp_error( $result ) ) {
			                // Parse errors into a string and append as parameter to redirect
			                $errors = join( ',', $result->get_error_codes() );
			                $redirect_url = add_query_arg( 'register-errors', $errors, $redirect_url );
			            } else {
							if($sbscrb)
								subscribe_user($email);
			                // Success, redirect to login page.
			                $redirect_url = get_permalink(realteo_get_option( 'my_account_page' ));
			                $redirect_url = add_query_arg( 'registered', $email, $redirect_url );
			            }
		            }
	            }
				
				
			    
	        }
			
			if($role=='agent' || $role=='broker'){
				$redirect_url = $custom_redirect_page;
			}
	 
	        wp_redirect( $redirect_url );
	        exit;
	    }
}

	/**
	 * Validates and then completes the new user signup process if all went well.
	 *
	 * @param string $email         The new user's email address
	 * @param string $first_name    The new user's first name
	 * @param string $last_name     The new user's last name
	 * @param string $role     The new user's role
	 * @param string $phone     	The new user's phone
	 * @param string $postcode     The new user's postcode
	 *
	 * @return int|WP_Error         The id of the user that was created, or error if failed.
	 */
function custom_register_user( $email, $first_name, $last_name, $role, $phone, $postcode, $password) {
	    $errors = new WP_Error();
	 	
	    // Email address is used as both username and email. It is also the only
	    // parameter we need to validate
	    if ( ! is_email( $email ) ) {
	        $errors->add( 'email', get_registration_error_message( 'email' ) );
	        return $errors;
	    }
	 
	    if ( username_exists( $email ) || email_exists( $email ) ) {
	        $errors->add( 'email_exists', get_registration_error_message( 'email_exists') );
	        return $errors;
	    }
		
	    if (!$phone) {
	        $errors->add( 'empty_phone', get_registration_error_message( 'empty_phone' ) );
	        return $errors;
	    }
		
	    if (!$postcode) {
	        $errors->add( 'empty_postcode', get_registration_error_message( 'empty_postcode' ) );
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
		
		// update phone and postcode
		update_user_meta( $user_id, 'phone', $phone );
		update_user_meta( $user_id, 'billing_phone', $phone );
		update_user_meta( $user_id, 'postcode', $postcode );
		update_user_meta( $user_id, 'billing_postcode', $postcode );
		update_user_meta( $user_id, 'shipping_postcode', $postcode );

	    //wp_set_password($user_id,$password);
	    wp_new_user_notification( $user_id, $password,'both' );
	 
	    return $user_id;
}
/**
	 * Finds and returns a matching error message for the given error code.
	 *
	 * @param string $error_code    The error code to look up.
	 *
	 * @return string               An error message.
	 */
function get_registration_error_message( $error_code ) {
	    switch ( $error_code ) {
	        case 'empty_username':
	            return __( 'You do have an email address, right?', 'realteo' );
	 
	        case 'empty_password':
	            return __( 'You need to enter a password to login.', 'realteo' );
	 
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
	 
	        case 'empty_phone':
	            $err = __(
	                "You need to enter your phone number.",
	                'realteo'
	            );
	            return sprintf( $err, wp_lostpassword_url() );
	 
	        case 'empty_postcode':
	            $err = __(
	                "You need to enter your postcode.",
	                'realteo'
	            );
	            return sprintf( $err, wp_lostpassword_url() );
	 
	        default:
	            break;
	    }
	     
	    return __( 'An unknown error occurred. Please try again later.', 'realteo' );
}

add_action( 'init', 'custom_registration');
function custom_registration() {
	global $realteo;
    remove_action( 'login_form_register', array( $realteo->agents, 'do_register_user' ) );
	add_action( 'login_form_register', 'do_custom_register_user');
}