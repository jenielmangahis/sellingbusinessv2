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

function sendpulse_add($email, $name, $phone, $role) {
	$apipath = dirname( __FILE__ ).DIRECTORY_SEPARATOR.'sendpulse-rest-api'.DIRECTORY_SEPARATOR;	
	require_once($apipath."src/ApiInterface.php");
	require_once($apipath."src/ApiClient.php");
	require_once($apipath."src/Storage/TokenStorageInterface.php");
	require_once($apipath."src/Storage/FileStorage.php");
	require_once($apipath."src/Storage/SessionStorage.php");
	require_once($apipath."src/Storage/MemcachedStorage.php");
	require_once($apipath."src/Storage/MemcacheStorage.php");
	
	
	define('API_USER_ID', '5bbe0dce484e00e88c8ebe6abe42ec4d');
	define('API_SECRET', 'f5e7b0417d05656f82dbd400e7475694');
	
	//use Sendpulse\RestApi\ApiClient;
	//use Sendpulse\RestApi\Storage\FileStorage;
	
	switch($role){
		case 'agent':
			$bookID = 40345;
		break;
		case 'broker':
			$bookID = 43461;
		break;
		default:
			$bookID = 43467;
		break;
	}
	
	$SPApiClient = new ApiClient(API_USER_ID, API_SECRET, new FileStorage());
	$emails = array(
		array(
			'email' => $email,
			'variables' => array(
				'phone' => $phone,
				'name' => $name,
			)
		)
	);
	$ret = $SPApiClient->addEmails($bookID, $emails);
	return $ret->result;
}



function cm_add($email, $name, $phone, $postcode, $role) {
	
	//$result = $wrap->get_clients();
	//var_dump($result->response);
	
	require_once 'createsend-php-master/csrest_subscribers.php';
	
	$key = 'g3yT7bD7b4v2izhINqkvCvhheM62UW6ycy/OlyKWNOf7SXkyT6lCFpFeAZdVeY0EhU1p+tQGjkdnRdGP6vztDiB34QgfwPkCiBt6KqVs/J7Ot+h+42gEu5IPmHL72UUtGobn4fUbW/9ppvV79Vat0g==';	
	$listID = '';
	switch($role){
		case 'agent':
		//Selling Businesses Australia - Liquidation Agent
			$listID = '71bfd0660d8385176cd5b2ab5835db6b';
		break;
		case 'broker':
		//Selling Businesses Australia - Buyers
			$listID = 'e64968f97fa4e82017028d0afaffd332';
			/*
			SBA Broker - QLD = 085e31e7e877556c1e96ef11fdc999db
			SBA Broker - NSW = 23e270afe5b2989368a0b09a54418826
			SBA Broker - NT = 15482d3da022e30a16d73b08df921fbd
			SBA Broker - SA = 21d62925d32de1590127aed2bf00f861
			SBA Broker - TAS = 7d5e824a5670dcb04c53a63562a0141f
			SBA Broker - VIC = b3ebfcbc77187ca8be4525972e57074d
			SBA Broker - WA = 4c67f28318be0397eb90d740d0d58e10
			
			$state = findState($postcode);
			switch($state){
				case 'QLD':
					$listID = '71bfd0660d8385176cd5b2ab5835db6b';
				break;
				case 'NSW':
					$listID = '23e270afe5b2989368a0b09a54418826';
				break;
				case 'NT':
					$listID = '15482d3da022e30a16d73b08df921fbd';
				break;
				case 'SA':
					$listID = '21d62925d32de1590127aed2bf00f861';
				break;
				case 'TAS':
					$listID = '7d5e824a5670dcb04c53a63562a0141f';
				break;
				case 'VIC':
					$listID = 'b3ebfcbc77187ca8be4525972e57074d';
				break;
				case 'WA':
					$listID = '4c67f28318be0397eb90d740d0d58e10';
				break;
				default:
					$listID = 'e64968f97fa4e82017028d0afaffd332';
				break;
			}			
			*/
		break;
		default:
		//Selling Businesses Australia - Brokers
			$listID = 'd9eba399cb72148023622a6d4c2988a7';
		break;
	}
	
	
	$customFieldValAr = array(
							array( "Key" => 'Phone', "Value" => $phone ),
							array( "Key" => 'Postcode', "Value" => $postcode )
							);
	$dataAr = array(
			"EmailAddress" => $email,
			"Name" => $name,
			"CustomFields" => $customFieldValAr,
			"Resubscribe" => true,
			//"RestartSubscriptionBasedAutoresponders" => true,
			'ConsentToTrack' => 'yes',
		);	
	$CampaignMonitor = new CS_REST_Subscribers($listID, $key);
	$result = $CampaignMonitor->add($dataAr);
	
	if($result->was_successful()) {
		return true;
	}else{
		echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
		var_dump($result->response);
		echo '</pre>';
	}
}

//pr(sendpulse_add('bonsai.christiand@gmail.com', 'christian', '123676'));
//exit();

//add_user_new('bonsai.christiand@gmail.com');
/**
	 * Handles the registration of a new user.
	 *
	 * Used through the action hook "login_form_register" activated on wp-login.php
	 * when accessed through the registration action.
	 */
function do_custom_register_user() {
		
		$custom_redirect_page = 'registration-successful';
		//pr($_POST);
		//exit();
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
									cm_add($email, $first_name.' '.$last_name, $phone, $postcode, $role);
									//subscribe_user($email);
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
									cm_add($email, $first_name.' '.$last_name, $phone, $postcode, $role);
									//subscribe_user($email);
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
								cm_add($email, $first_name.' '.$last_name, $phone, $postcode, $role);
								//subscribe_user($email);
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
		
		$user_data['phone'] = $phone;
		$user_data['postcode'] = $phone;
		$RecordID = send_to_method_clients($user_data);
		
		// update phone and postcode
		update_user_meta( $user_id, 'RecordID', $RecordID );
		update_user_meta( $user_id, 'phone', $phone );
		update_user_meta( $user_id, 'billing_phone', $phone );
		update_user_meta( $user_id, 'postcode', $postcode );
		update_user_meta( $user_id, 'billing_postcode', $postcode );
		update_user_meta( $user_id, 'shipping_postcode', $postcode );

	    //wp_set_password($user_id,$password);
	    if($role=='agent' || $role=='broker'){
			
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			$message  = sprintf( __( 'New user registration on your site %s:' ), $blogname ) . "\r\n\r\n";
			$message .= sprintf( __( 'First Name: %s' ), $first_name ) . "\r\n\r\n";
			$message .= sprintf( __( 'Last Name: %s' ), $last_name ) . "\r\n\r\n";
			$message .= sprintf( __( 'Phone: %s' ), $phone ) . "\r\n\r\n";
			$message .= sprintf( __( 'Postcode: %s' ), $postcode ) . "\r\n\r\n";
			$message .= sprintf( __( 'Username: %s' ), $email ) . "\r\n\r\n";
			$message .= sprintf( __( 'Email: %s' ), $email ) . "\r\n\r\n";
			$message .= sprintf( __( 'Password: %s' ), $password ) . "\r\n\r\n";
			$message .= sprintf( __( 'Role: %s' ), $role ) . "\r\n";
	 
			$wp_new_user_notification_email_admin = array(
				'to' => get_option( 'admin_email' ),
				'subject' => __( 'New User Registration' ),
				'message' => $message,
				'headers' => '',
			);
			
			$mail = wp_mail($wp_new_user_notification_email_admin['to'],
				wp_specialchars_decode( sprintf( $wp_new_user_notification_email_admin['subject'], $blogname ) ),
				$wp_new_user_notification_email_admin['message'],
				$wp_new_user_notification_email_admin['headers']
			);			
			if (!$mail) {
				$errors->add( '', 'Error sending admin notification email' );
				return $errors;
            }
			
		}else
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
function custom_import_func( $atts ){
	$import_folder = 'import-xml-feed';
	//ob_start();
		
	$assets = '/inc/import-xml-feed';
	
	wp_enqueue_script( 'moove_importer_backend', get_stylesheet_directory_uri().DIRECTORY_SEPARATOR.$assets. '/assets/js/moove_importer_backend.js', array( 'jquery' ), '1', true );
	wp_enqueue_style( 'moove_importer_backend', get_stylesheet_directory_uri().DIRECTORY_SEPARATOR.$assets. '/assets/css/moove_importer_backend.css', '', '1' );
	
	include_once( dirname( __FILE__ ).DIRECTORY_SEPARATOR.$import_folder.DIRECTORY_SEPARATOR.'moove-view.php' );
	include( dirname( __FILE__ ).DIRECTORY_SEPARATOR.$import_folder.DIRECTORY_SEPARATOR.'moove-options.php' );
	//include( dirname( __FILE__ ).DIRECTORY_SEPARATOR.$import_folder.DIRECTORY_SEPARATOR.'moove-controller.php' );
	
}
add_shortcode( 'sequenceone_import_feed', 'custom_import_func' );
include( dirname( __FILE__ ).DIRECTORY_SEPARATOR.'import-xml-feed'.DIRECTORY_SEPARATOR.'moove-controller.php' );
include( dirname( __FILE__ ).DIRECTORY_SEPARATOR.'import-xml-feed'.DIRECTORY_SEPARATOR.'moove-actions.php' );	

add_action( 'wp_head', 'remove_my_action' );
function remove_my_action(){
	remove_action('findeo_page_subtitle', 'realteo_my_account_hello');
}
add_action('findeo_page_subtitle','change_welcome');
function change_welcome(){
	$my_account_page = realteo_get_option( 'my_account_page');
	if(is_user_logged_in() && !empty($my_account_page) && is_page($my_account_page)){
		$current_user = wp_get_current_user();
		if(!empty($current_user->user_firstname)){
			$name = $current_user->user_firstname.' '.$current_user->user_lastname;
		} else {
			$name = $current_user->display_name;
		}
		echo "<span>" . esc_html__('Welcome, ','realteo') . $name.'</span>';
	}
}

function addPaymentRecords($id, $data){
	
	$fields = array('AccountTypeName');
	$values = array('Site Payment');
	//$res = insertMethod('AccountAccountType', $fields, $values);
	//pr($res);
	//exit();
	
	$full_name = $data['first_name'] . " " . $data['last_name'];
	$method_array = array( 
		'strCompanyAccount' => 'sellingbusinessesaustraliacomau',
		'strLogin' => 'steffi@rankfirst.com.au',
		'strPassword'  => 'Seoheroes@123!',
		'strSessionID' => '',
		'strTable' => 'Account',
		//'intRecordID' => $id
	 );	 
	 
	$fields = array('TxnRecordID','Amount');
	$values = array(1229,101);
	$res = updateMethod('ReceivePaymentAppliedToTxn', $fields, $values, $id);
	pr($res);
	exit();
	
	$fields = array(
		'ARAccount',
		//'Customer',
		'AssignedTo',
		'Currency',
		'TxnNumber',
		'TotalAmount',
		'RecordID',
	);
	$values = array(
		$full_name,
		//$full_name,
		'STEFFI PEREIRA',
		'AUD',
		29,
		'101.00',
		$id
	);
	echo $id;
	$res = updateMethod('ReceivePayment', $fields, $values, $id);
	pr($res);
	exit();
	
	$method_array = array( 
		'strCompanyAccount' => 'sellingbusinessesaustraliacomau',
		'strLogin' => 'steffi@rankfirst.com.au',
		'strPassword'  => 'Seoheroes@123!',
		'strSessionID' => '',
		'strTable' => 'ReceivePayment',
		'intRecordID' => $id
	 );
	$method_array['arrInsertFieldsArray'] = array('string'=>array(
		//'RecordID',
		'ARAccount',
		'Customer',
		'AssignedTo',
		'Currency',
		'TxnNumber',
		'TotalAmount',
	));
	$method_array['arrInsertValueArray'] = array('string'=>array(
		//$id,
		$full_name,
		$full_name,
		'STEFFI PEREIRA',
		'AUD',
		29,
		'101.00'
	));
				
	$result =  $client-> call('MethodAPIInsertV2', $method_array, array("soapaction"=>"https://www.methodintegration.com/MethodAPI/service.asmx/MethodAPIInsertV2"));
		//$result =  $client-> call('MethodAPIUpdateV2', $method_array, array("soapaction"=>"https://www.methodintegration.com/MethodAPI/service.asmx/MethodAPIUpdateV2")); 
	$api_results = new SimpleXMLElement($result["MethodAPIInsertV2Result"]); 
	/*
	$method_array = array( 
		'strCompanyAccount' => 'sellingbusinessesaustraliacomau',
		'strLogin' => 'steffi@rankfirst.com.au',
		'strPassword'  => 'Seoheroes@123!',
		'strSessionID' => '',
		'strTable' => 'Contacts',
		'intRecordID' => $id
	 );     
	$result =  $client-> call('MethodAPIDeleteV2', $method_array, array("soapaction"=>"https://www.methodintegration.com/MethodAPI/service.asmx/MethodAPIDeleteV2")); 
	$api_results = new SimpleXMLElement($result["MethodAPIDeleteV2Result"]); 
	*/
	pr($api_results);
	exit();
}
function send_to_method_clients( $data ) {   
	
	// nusoap 
	require_once(dirname( __FILE__ ).DIRECTORY_SEPARATOR. 'nusoap.php'); 
	
	$ns="urn:servicename";
	$client = new nusoap_client('http://www.methodintegration.com/MethodAPI/service.asmx?wsdl', true); 
		 	
	// array of required field names
	$arrFields = array(
		'AssignedTo',
		'IsLeadStatusOnly',
		'Name',
		'FirstName',
		'LastName',
		//'BillAddressAddr1',
		//'BillAddressCity',
		//'BillAddressState',
		'BillAddressPostalCode',
		'Phone',
		'Email',
		'Notes'
	);	
	
	// concat full name
	$full_name = $data['first_name'] . " " . $data['last_name'];
	
	// frequency interest options -- need to be set to Yes/No for Method
	$IsFrequencyWeekly = "No";
	$IsFrequency2Weeks = "No";
	$IsFrequency4Weeks = "No";
	
	// array of required field values
	$arrValues = array(
		'Paul Findlay',
		'Yes', // IsLeadStatusOnly
		$full_name, // Name
		$data['first_name'], // FirstName
		$data['last_name'],  // LastName
		//'', // BillAddressAddr1
		//'', // BillAddressCity
		//'', // BillAddressState
		$data['postcode'], // BillAddressPostalCode
		$data['phone'], // Phone
		$data['user_email'], // Email
		$data['role'], // role
	);
	
	// only insert the optional fields below if there are values
		 
	// now complete the full arrays we'll pass into the main method array
	$arrInsertFieldsArray = array(
		'string' => $arrFields																									
	 );			
	$arrInsertValueArray = array(
		'string' => $arrValues
	 );		 	 
	 	 	 	
	// set up array of method data						 		 
	$method_array = array( 
		'strCompanyAccount' => 'sellingbusinessesaustraliacomau',
		'strLogin' => 'accounts@sellingbusinessesaustralia.com.au',
		'strPassword'  => 'Selling1!',
		'strSessionID' => '',
		'strTable' => 'Customer',
		'arrInsertFieldsArray' => $arrInsertFieldsArray,
		'arrInsertValueArray' => $arrInsertValueArray
	 );     
	$result =  $client-> call('MethodAPIInsertV2', $method_array, array("soapaction"=>"https://www.methodintegration.com/MethodAPI/service.asmx/MethodAPIInsertV2"));  	
	//print_r($result);
		
	// parse results
	$api_results = new SimpleXMLElement($result["MethodAPIInsertV2Result"]);
	//pr($api_results);
	// get individual values from results
	//$response = $api_results["response"];
	$RecordID = xml_attribute($api_results, "RecordID");
	// if a RecordID is returned, the first insert succeeded.  
	// we now also insert info into the contact table -- the fullname field being the unique identifier
	if($RecordID) {
		
		$arrInsertFieldsArray = array(
			'string' => array(
				'Name',
				'FirstName',
				'LastName',
				'Phone',
				'Email',
			)																										
		 );
		 
		 $arrInsertValueArray = array(
			'string' => array (
				$full_name, // Name
				$data['first_name'], // FirstName
				$data['last_name'],  // LastName
				$data['phone'], // Phone
				$data['user_email'], // Email
			)
		 );	
		 
		$method_array = array( 
			'strCompanyAccount' => 'sellingbusinessesaustraliacomau',
			'strLogin' => 'accounts@sellingbusinessesaustralia.com.au',
			'strPassword'  => 'Selling1!',
			'strSessionID' => '',
			'strTable' => 'Contacts',
			'arrInsertFieldsArray' => $arrInsertFieldsArray,
			'arrInsertValueArray' => $arrInsertValueArray, 
		 );     
		$result =  $client-> call('MethodAPIInsertV2', $method_array, array("soapaction"=>"https://www.methodintegration.com/MethodAPI/service.asmx/MethodAPIInsertV2"));  	
		//print_r($result);
			
		//$api_results = new SimpleXMLElement($result["MethodAPIInsertV2Result"]);
		
		/*
		$method_array['strTable'] = 'LeadForms';
		$method_array['arrInsertFieldsArray'] = array('string'=>array('Name'));
		$method_array['arrInsertValueArray'] = array('string'=>array($Name));
		$method_array['intRecordID'] =  $RecordID;
				
		$result =  $client-> call('MethodAPIInsertV2', $method_array, array("soapaction"=>"https://www.methodintegration.com/MethodAPI/service.asmx/MethodAPIInsertV2"));
		//$result =  $client-> call('MethodAPIUpdateV2', $method_array, array("soapaction"=>"https://www.methodintegration.com/MethodAPI/service.asmx/MethodAPIUpdateV2")); 
		$api_results = new SimpleXMLElement($result["MethodAPIInsertV2Result"]);  
		*/
		// parse results
	//$response = $api_results["response"];
	//kill the client
		unset($client);
		return $RecordID;
	}	
	//pr($response);
	//exit();			    
}

function xml_attribute($object, $attribute){
    if(isset($object[$attribute]))
        return (string) $object[$attribute];
}

function recordPayment( $order_id ) {

    $order = new WC_Order( $order_id );
	
	// return if not completed
	if($order->post_status != 'wc-completed'){
		return false;
	}
	if(!$order->user_id){
		return false;
	}
	
	// get the first item
	//$items = $order->get_items();	
    //$user = new WP_User( $order->user_id );
	 $first_name = get_user_meta( $order->user_id, 'first_name', true );
	 $last_name = get_user_meta( $order->user_id, 'last_name', true );
	
	$full_name = $first_name . " " . $last_name;
	$fields = array(
		'Customer',
		'AssignedTo',
		'TxnDate',
		'TotalAmount',
		'DepositToAccount',
	);
	$values = array(
		$full_name,
		'Paul Findlay',
		strtoupper(date('M')).date('-j-Y'),
		$order->total,
		'Inventory',
	);	 
	$res = insertMethod('ReceivePayment', $fields, $values);
	
	$response = xml_attribute($res, "response");
	$RecordID = xml_attribute($res, "RecordID");
}
add_action( 'woocommerce_order_status_completed', 'recordPayment' );



/*
add_action( 'admin_menu', 'wpse_91693_register' );

function wpse_91693_register()
{
    add_menu_page(
        'Custom Package',     // page title
        'Custom Package',     // menu title
        'manage_options',   // capability
        'custom-package',     // menu slug
        'wpse_91693_render' // callback function
    );
}
function wpse_91693_render()
{
   
   global $wpdb;
  echo '<form method="POST" action="?page=add_data">
 <label>Team Name: </label><input type="text" name="team_name" /><br />
 <label>Team City: </label><input type="text" name="team_city" /><br />
 <label>Team State: </label><input type="text" name="team_state" /><br />
 <label>Team Stadium: </label><input type="text" name="team_stadium" /><br />
<input type="submit" value="submit" />
</form>';

  $default_row = $wpdb->get_row( "SELECT * FROM $table_name ORDER BY team_id DESC LIMIT 1" );
if ( $default_row != null ) {
 $id = $default_row->team_id + 1;
} else {
 $id = 1;
}
 $default = array(
 'team_id' => $id,
 'team_name' => '',
 'team_city' => '',
 'team_state' => '',
 'team_stadium' => '',
);
$item = shortcode_atts( $default, $_REQUEST );

 $wpdb->insert( $table_name, $item );



}
*/



function removeRecords($id){
	require_once(dirname( __FILE__ ).DIRECTORY_SEPARATOR. 'nusoap.php'); 
	$client = new nusoap_client('http://www.methodintegration.com/MethodAPI/service.asmx?wsdl', true); 
	$method_array = array( 
		'strCompanyAccount' => 'sellingbusinessesaustraliacomau',
		'strLogin' => 'accounts@sellingbusinessesaustralia.com.au',
		'strPassword'  => 'Selling1!',
		'strSessionID' => '',
		'strTable' => 'Contacts',
		'intRecordID' => $id
	 );     
	$result =  $client-> call('MethodAPIDeleteV2', $method_array, array("soapaction"=>"https://www.methodintegration.com/MethodAPI/service.asmx/MethodAPIDeleteV2")); 
	$api_results = new SimpleXMLElement($result["MethodAPIDeleteV2Result"]); 
	pr($api_results);
	exit();
}
//removeRecords(31);
$user_data = array(
	        'user_login'    => 'bonsai.christiand@gmail.com',
	        'user_email'    => 'bonsai.christiand@gmail.com',
	        'first_name'    => 'John 1',
	        'last_name'     => 'Doe 1',
	        'role'			=> 'broker',
			'phone'			=> '1234567',
			'postcode'		=> '1900'
	    );
//echo send_to_method_clients($user_data);
//exit();
//addPaymentRecords(34, $user_data);
//addPaymentRecordsV2(60, $user_data);
function addPaymentRecordsV2($id, $data){
	
	
	$full_name = $data['first_name'] . " " . $data['last_name'];
	$fields = array(
		'Customer',
		'AssignedTo',
		'TxnDate',
		'TotalAmount',
		'DepositToAccount',
	);
	$values = array(
		$full_name,
		'STEFFI PEREIRA',
		'FEB-11-2019',
		'101.00',
		'Inventory',
	);
	 
	$res = insertMethod('ReceivePayment', $fields, $values);
	pr($res);
	exit();
	
	$fields = array('Name','AccountNumber');
	$values = array('John 1 Doe 1','12345');
	 
	$res = insertMethod('Account', $fields, $values);
	pr($res);
	exit();
	
	
	// concat full name
	$full_name = $data['first_name'] . " " . $data['last_name'];
	$fields = array(
		'ARAccount',
		'AssignedTo',
		'TxnDate',
		//'TxnNumber',
		'TotalAmount',
		'DepositToAccount',
		'RecordID',
	);
	$values = array(
		'John 1 Doe 1',
		'STEFFI PEREIRA',
		'FEB-11-2019',
		//29,
		'101.00',
		'Inventory',
		$id
	);
	echo $id;
	$fields = array('AccountTypeName');
	$values = array('Site Payment');
	//$res = insertMethod('AccountAccountType', $fields, $values);
	//pr($res);
	//exit();
	
	$full_name = $data['first_name'] . " " . $data['last_name'];
	$method_array = array( 
		'strCompanyAccount' => 'sellingbusinessesaustraliacomau',
		'strLogin' => 'accounts@sellingbusinessesaustralia.com.au',
		'strPassword'  => 'Selling1!',
		'strSessionID' => '',
		'strTable' => 'Account',
		//'intRecordID' => $id
	 );	 
	 
	$fields = array('TxnRecordID','Amount');
	$values = array(1229,101);
	$res = updateMethod('ReceivePaymentAppliedToTxn', $fields, $values, $id);
	
	$res = insertMethod('ReceivePayment', $fields, $values);
	//$res = updateMethod('ReceivePayment', $fields, $values, $id);
	pr($res);
	exit();
	
	$method_array = array( 
		'strCompanyAccount' => 'sellingbusinessesaustraliacomau',
		'strLogin' => 'accounts@sellingbusinessesaustralia.com.au',
		'strPassword'  => 'Selling1!',
		'strSessionID' => '',
		'strTable' => 'ReceivePayment',
		'intRecordID' => $id
	 );
	$method_array['arrInsertFieldsArray'] = array('string'=>array(
		//'RecordID',
		'ARAccount',
		'Customer',
		'AssignedTo',
		'Currency',
		'TxnNumber',
		'TotalAmount',
	));
	$method_array['arrInsertValueArray'] = array('string'=>array(
		//$id,
		$full_name,
		$full_name,
		'STEFFI PEREIRA',
		'AUD',
		29,
		'101.00'
	));
				
	$result =  $client-> call('MethodAPIInsertV2', $method_array, array("soapaction"=>"https://www.methodintegration.com/MethodAPI/service.asmx/MethodAPIInsertV2"));
		//$result =  $client-> call('MethodAPIUpdateV2', $method_array, array("soapaction"=>"https://www.methodintegration.com/MethodAPI/service.asmx/MethodAPIUpdateV2")); 
	$api_results = new SimpleXMLElement($result["MethodAPIInsertV2Result"]); 
	/*
	$method_array = array( 
		'strCompanyAccount' => 'sellingbusinessesaustraliacomau',
		'strLogin' => 'accounts@sellingbusinessesaustralia.com.au',
		'strPassword'  => 'Selling1!',
		'strSessionID' => '',
		'strTable' => 'Contacts',
		'intRecordID' => $id
	 );     
	$result =  $client-> call('MethodAPIDeleteV2', $method_array, array("soapaction"=>"https://www.methodintegration.com/MethodAPI/service.asmx/MethodAPIDeleteV2")); 
	$api_results = new SimpleXMLElement($result["MethodAPIDeleteV2Result"]); 
	*/
	pr($api_results);
	exit();
}
function insertMethod($table, $fields, $values){
	require_once(dirname( __FILE__ ).DIRECTORY_SEPARATOR. 'nusoap.php'); 
	$client = new nusoap_client('http://www.methodintegration.com/MethodAPI/service.asmx?wsdl', true); 
	
	$method_array = array( 
		'strCompanyAccount' => 'sellingbusinessesaustraliacomau',
		'strLogin' => 'accounts@sellingbusinessesaustralia.com.au',
		'strPassword'  => 'Selling1!',
		'strSessionID' => '',
		'strTable' => $table,
		'arrInsertFieldsArray' => array('string'=>$fields),
		'arrInsertValueArray' => array('string'=>$values)
	 );
	
	$result =  $client->call('MethodAPIInsertV2', $method_array, array("soapaction"=>"https://www.methodintegration.com/MethodAPI/service.asmx/MethodAPIInsertV2"));
		//$result =  $client-> call('MethodAPIUpdateV2', $method_array, array("soapaction"=>"https://www.methodintegration.com/MethodAPI/service.asmx/MethodAPIUpdateV2")); 
	return new SimpleXMLElement($result["MethodAPIInsertV2Result"]); 
	//xml_attribute($api_results, "RecordID");
}
function updateMethod($table, $fields, $values, $id){
	require_once(dirname( __FILE__ ).DIRECTORY_SEPARATOR. 'nusoap.php'); 
	$client = new nusoap_client('http://www.methodintegration.com/MethodAPI/service.asmx?wsdl', true); 
	
	$method_array = array( 
		'strCompanyAccount' => 'sellingbusinessesaustraliacomau',
		'strLogin' => 'accounts@sellingbusinessesaustralia.com.au',
		'strPassword'  => 'Selling1!',
		'strSessionID' => '',
		'strTable' => $table,
		'arrUpdateFieldsArray' => array('string'=>$fields),
		'arrUpdateValueArray' => array('string'=>$values),
		'intRecordID' => $id
	 );
	
	$result =  $client->call('MethodAPIUpdateV2', $method_array, array("soapaction"=>"https://www.methodintegration.com/MethodAPI/service.asmx/MethodAPIUpdateV2")); 
	return new SimpleXMLElement($result["MethodAPIUpdateV2Result"]); 
}
function featuredtoRSS1($content) {
global $post;
if ( has_post_thumbnail( $post->ID ) ){
$content = '<div>' . get_the_post_thumbnail( $post->ID, 'medium', array( 'style' => 'margin-bottom: 15px;' ) ) . '</div>' . $content;
}
return $content;
} 
function featuredtoRSS($content) {
global $post;
if ( has_post_thumbnail( $post->ID ) ){
	//$img = get_the_post_thumbnail( $post->ID, 'medium', array( 'style' => 'margin-bottom: 15px;' ) );
	//$thumbnail = wp_get_attachment_url(get_post_thumbnail_id($post->ID));
	$img_url = get_the_post_thumbnail_url( $post->ID, 'medium' );
	$img = "&lt;img src='$img_url'&gt;";
	//$img = "[&lt;]b[&gt;]bold[&lt;]/b[&gt;]";
	//$img = str_replace(array("<",">"),array("&lt;","&gt;"),$img);
	$content = $img . ' ' . $content;
}
return $content;
} 
//add_filter('the_excerpt_rss', 'featuredtoRSS');
//add_filter('the_content_feed', 'featuredtoRSS');

// display featured post thumbnails in RSS feeds
function WPGood_rss_thumbs( $content ) {
    global $post;
    if( has_post_thumbnail( $post->ID ) ) {
        $content = '<media:content type="image/*">' . get_the_post_thumbnail( $post->ID, 'thumbnail' ) . '</media>' . $content;
    }
    return $content;
}
//add_filter( 'the_excerpt_rss', 'WPGood_rss_thumbs' );
//add_filter( 'the_content_feed', 'WPGood_rss_thumbs' );

function xmit_add_rss_node() {
	global $post;
	if(has_post_thumbnail($post->ID)):
		$url = wp_get_attachment_url(get_post_thumbnail_id($post->ID));
		$thumb = get_the_post_thumbnail( $post->ID, 'thumbnail' );
	echo('<media:content type="image/*" url="'.$url.'" src="'.$url.'"/>');
	endif;
}
add_action('rss2_item', 'xmit_add_rss_node');

function getAgencyIdByAuthor($id){
	global $wpdb;
		$results  = $wpdb->get_col( $wpdb->prepare( "
			SELECT $wpdb->posts.ID FROM $wpdb->posts  
			WHERE 1=1 
			AND $wpdb->posts.post_author IN (%d) 
			AND $wpdb->posts.post_type = 'agency'
			AND (( $wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'draft' OR $wpdb->posts.post_status = 'pending'))  
			ORDER BY $wpdb->posts.post_date ASC
			", $id ) );
	if(!empty($results)){
		return $results[0];
	}
}

function custom_share_func(){
	global $post;
	
	ob_start();
?>
<ul class="custom_widitem row no-gutters">
<?php
	$nonce = wp_create_nonce("realteo_bookmark_this_nonce");	
	$classObj = new Realteo_Bookmarks;
	if( $classObj->check_if_added($post->ID) ) {
?>
	<li class="col-xs-4 lstitm"><button onclick="window.location.href='<?php echo get_permalink(realteo_get_option( 'bookmarks_page' ))?>'"  class="widget-button save liked"><i class="fa fa-star"></i> Shortlist</button></li>
<?php } else { 
		if(is_user_logged_in()){
?>
	<li class="col-xs-4 lstitm"><button class="widget-button save realteo-bookmark-it with-tip" 
					data-tip-content="<?php esc_html_e('Save This Listing','realteo'); ?>"
					data-tip-content-bookmarking="<?php esc_html_e('Save Listing','realteo'); ?> <?php echo esc_attr('<i class="fa fa-circle-o-notch fa-spin fa-fw"></i>'); ?>"
					data-tip-content-bookmarked="<?php esc_html_e('Saved!','realteo'); ?>"   
					data-post_id="<?php echo esc_attr($post->ID); ?>" 
					data-nonce="<?php echo esc_attr($nonce); ?>" 
					><i class="fa fa-star-o"></i> Shortlist</button></li>
	<?php } else { ?>			
	<li class="col-xs-4 lstitm"><button href="#" class="widget-button with-tip" data-tip-content="<?php esc_html_e('Login to save this listing','realteo'); ?>"><i class="fa fa-star-o"></i> Shortlist</button></li>
<?php
		}
	}
?>
	<li class="col-xs-4 lstitm"><button class="widget-button print-simple" ><i class="sl sl-icon-printer"></i> Print</button></li>
    <li class="col-xs-4 lstitm"><div class="sfsi_widget" data-position="widget">   
		<div id='sfsi_wDiv'></div>
                    <?php 
						/* Link the main icons function */
               			echo sfsi_check_visiblity(0);
             		?>
	      		<div style="clear: both;"></div>
            </div><button href="javascript:void(0);" class="widget-button shrebtn"><i class="fa fa-share-alt"></i> Share</button></li>
</ul>
<script type="text/javascript">
jQuery(document).ready(function($){
	$('.shrebtn').setFocusEvent();
});
</script>
<?php
	$content = ob_get_clean();
	echo $content;
}
add_shortcode( 'custom_widget', 'custom_share_func' );

function custom_contact_func(){
	global $post;
	$agentID = get_the_author_meta( 'ID' );
	$agency = get_post(getAgencyIdByAuthor($agentID));
	if(empty($agency))
		return false;
	
	$agencyData = get_post_meta( $agency->ID );
	
	$img_url = (isset($agencyData['_logo']) && $agencyData['_logo'][0])?$agencyData['_logo'][0]:'/wp-content/plugins/realteo/templates/images/agency_placeholder.png';
	ob_start();
	
	echo do_shortcode('[custom_widget]');
?>
<div class="agent-widget">
	<div class="agent-title">
        <div class="agent-photo"><a href="<?=get_permalink($agency->ID)?>"><img src="<?=$img_url?>" style="border-radius:none" alt="<?php echo $agency->post_title ?>" /></a></div>
        <div class="agent-details">
                <h4><a href="<?php echo get_permalink($agency->ID) ?>"><?php echo $agency->post_title ?></a></h4>
                <?php 
				
				$sendTo = '';
                        if(isset($agencyData['realteo_phone']) && !empty($agencyData['realteo_phone'][0])) { 
                            ?><span><i class="sl sl-icon-call-in"></i><a href="tel:<?php echo $agencyData['realteo_phone'][0]; ?>"><?php echo $agencyData['realteo_phone'][0]; ?></a></span><?php 
                        }
                        if(isset($agencyData['realteo_email']) && !empty($agencyData['realteo_email'][0])) { 
						//$sendTo = 'businessemail="'.$agencyData['realteo_email'][0].'"';
                            ?><br><span><i class="fa fa-envelope-o "></i><a href="mailto:<?php echo $agencyData['realteo_email'][0];?>"><?php echo $agencyData['realteo_email'][0];?></a></span>
                        <?php } ?>
        </div>
        <div class="clearfix"></div>
    </div>    
<?php echo do_shortcode('[contact-form-7 id="5" title="Sidebar Contact"]'); ?>
</div>
<?php	
	$content = ob_get_clean();
	echo $content;
}
add_shortcode( 'custom_contact_widget', 'custom_contact_func' );


/**
 * Change comment form default field names.
 *
 * @link http://codex.wordpress.org/Plugin_API/Filter_Reference/gettext
 */
function theme_change_comment_field_names( $translated_text, $text, $domain ) {

    if ( is_singular() ) {

        switch ( $translated_text ) {
			case 'Property title' :
                $translated_text = __( 'Business listing title', 'realteo' );
				echo $domain;
				exit();
                break;
            case 'Email' :
                $translated_text = __( 'Email Address', 'theme_text_domain' );
                break;
        }

    }

    return $translated_text;
}
add_filter( 'gettext', 'theme_change_comment_field_names', 20, 3 );


add_action("wp_ajax_get_postage", "get_postage");
add_action( 'wp_ajax_nopriv_get_postage', 'get_postage' );
function get_postage() {
	global $wp_query;
   if ( !wp_verify_nonce( $_REQUEST['nonce'], "get_postage_nonce")) {
      exit();
   }
   $search = (isset($_POST['search']))?$_POST['search']:'';
   $url = 'https://digitalapi.auspost.com.au/postcode/search.json?q='.$search;
   $request = wp_remote_request($url, array('headers'=>array('Content-Type' => 'application/json', 'Auth-Key' => '4f881700-465c-4357-9389-23bb84dff16a')));
	if($request['response']['code']==200){
		$res = json_decode($request['body'], true);
		$result['result'] = $res['localities']['locality'];
		$result['type'] = "success";		
	}else{
		$result['type'] = "error";
	}
	
	
   if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
      $result = json_encode($result);
      echo $result;
   }
   else {
      header("Location: ".$_SERVER["HTTP_REFERER"]);
   }

   die();

}


add_action("wp_ajax_show_post", "show_post");
add_action( 'wp_ajax_nopriv_show_post', 'show_post' );
function show_post() {
	global $wp_query;
   if ( !wp_verify_nonce( $_REQUEST['nonce'], "show_post_nonce")) {
      exit();
   }
   
   $posts_per_page = 40;
   
   $paged = (isset($_POST['paged']))?$_POST['paged']:1;
   
	   $args = array('post_type' => 'property',
					 'posts_per_page' => $posts_per_page,
					 'orderby' => 'post_date',
					 'order' => 'DESC',
					 'post_status' => 'publish',
					 'paged' => $paged
					 );
				 
   $children = new WP_Query( $args );
	
	if($children->posts){
		$result['type'] = "success";
		$result['post_html'] =	setHmltProp($children->posts);
		
            $nextpage = $paged+1;
            $prevouspage = $paged-1;
            $total = $children->max_num_pages;
            $pagination_args = array(
            'base'               => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
            'format'             => '?paged=%#%',
            'total'              => $total,
            'current'            => $paged,
            'show_all'           => true,
            'end_size'           => 1,
            'mid_size'           => 3,
            'prev_next'          => true,
            'prev_text'       => __('<span class="prev-next" data-attr="'.$prevouspage.'">&laquo;</span>'),
            'next_text'       => __('<span class="prev-next" data-attr="'.$nextpage.'">&raquo;</span>'),
            'type'               => 'array',
			);		
			
        $paginate_links = paginate_links($pagination_args);		
		$result['paginator'] = '';
        if ($paginate_links && $total>1) {
			$result['paginator'] .= '<ul class="pagination justify-content-center">';
			foreach ( $paginate_links as $page ) {
				$result['paginator'] .= '<li class="page-item '.(strpos($page, 'current') !== false ? 'active' : '').'"> ' . str_replace( 'page-numbers', 'page-link', $page ) . '</li>';
			}
			$result['paginator'] .= '</ul>';
        }
		
		
	}else{
		$result['type'] = "error";
	}
	
	
   if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
      $result = json_encode($result);
      echo $result;
   }
   else {
      header("Location: ".$_SERVER["HTTP_REFERER"]);
   }

   die();
}
function setHmltProp($posts){
	if(!$posts)
		return false;
	$res = '<table>';
	$res .= '<tr><th>ID</th><th>Title</th><th>Address</th><th>Latitude</th><th>Longitude</th></tr>';
	foreach($posts as $child):
		$add = get_post_meta($child->ID, '_address', true);
		$geo['lat'] = get_post_meta( $child->ID, '_geolocation_lat', true);
		$geo['lng'] = get_post_meta( $child->ID, '_geolocation_long', true);
		if($add){
		/*	$geo = get_google_geographic_location($add);
			if($geo){
				if(!add_post_meta( $child->ID, '_geolocation_lat', $geo['lat'], true ) ){
					update_post_meta( $child->ID, '_geolocation_lat', $geo['lat'] );
				}
				if(!add_post_meta( $child->ID, '_geolocation_long', $geo['lng'], true ) ){
					update_post_meta( $child->ID, '_geolocation_long', $geo['lng'] );
				}
			}
			*/
		}
		wp_reset_query();
		
		$res .= '<tr>';
		$res .= '<td>'.$child->ID.'</td>';
		$res .= '<td>'.$child->post_title.'</td>';
		$res .= '<td>'.$add.'</td>';
		$res .= '<td>'.(($geo)?$geo['lat']:'').'</td>';
		$res .= '<td>'.(($geo)?$geo['lng']:'').'</td>';
		$res .= '</tr>';
	endforeach;
	$res .= '</table>';
	return $res;
}

function set_each_list(){
		ob_start();	
?>
	<div id="newslist" class="row mt-4 pl-1 pr-1">loading</div>
	<div class="pagination" id='pagination'></div>
<script type="text/javascript">
(function($) {
var ajaxUrl = '<?=admin_url('admin-ajax.php?action=show_post&nonce='.wp_create_nonce("show_post_nonce"))?>',
	loading = 'loading!!!';
	
	
    function post_filter(paged){
		$('#newslist').html(loading);
		
            $.ajax(
                {
                    url:ajaxUrl,
                    type:"POST",
                    data: {'paged': paged},
                    success: function(data) {
						data = $.parseJSON(data);
						if(data.type=='success'){
                    		$('#newslist').html(data.post_html);
							$('#pagination').html(data.paginator);
							if(data.paginator){
								setPaginator();
							}
						}else{
							$('#newslist').html('<h1 class="has-no-post-list d-flex justify-content-center w-100">Posts Not Found</h1>');
							$('#pagination').html('');
						}
                	}
            });
    }
	
	function setPaginator(){
		$('#pagination a').on('click',function(e){
			  e.preventDefault();     
				if ($(this).hasClass('prev')||$(this).hasClass('next')) {
					paginateNum = $(this).find('.prev-next').data('attr');
					post_filter(paginateNum);
				}
				else{
					paginateNum = $(this).text();
					post_filter(paginateNum);
				}
				return false;
		 });
	}
     post_filter(1);
	 
})(jQuery);
</script>
<?php
		return ob_get_clean();
}
add_shortcode( 'set_geographic', 'set_each_list' );

function get_google_geographic_location($address) { 
    $url = 'https://maps.googleapis.com/maps/api/geocode/json?sensor=false&address='.urlencode($address).'&key=AIzaSyDyFbdcknHUi8tu9IYGqSt-BBmiEUppOSs'; 
    $results = json_decode(file_get_contents($url),1); 
	$location = array();
    if (!empty($results['results'][0]['geometry'])) { 
      $location = $results['results'][0]['geometry']['location']; 
    };
	if(!empty($location))
		return $location; 
} 


function save_geolocation( $id, $post ) {
	if(isset($_POST['_address'])&&$_POST['_address']){		
		$geo = realteo_geocode($_POST['_address']);
		if($geo){
			if(!add_post_meta( $id, '_geolocation_lat', $geo[0], true ) ){
				update_post_meta( $id, '_geolocation_lat', $geo[0] );
			}
			if(!add_post_meta( $id, '_geolocation_long', $geo[1], true ) ){
				update_post_meta( $id, '_geolocation_long', $geo[1] );
			}
		}
	}
}
add_action( 'publish_property', 'save_geolocation', 10, 2 );

function initialize_custom_class($emails){	
	$emails['WC_Email_Business_Alert'] = include( 'woo-custom-email/class-custom-email.php' );	
	return $emails;
}
add_filter('woocommerce_email_classes', 'initialize_custom_class');

// this function will use for cronjob
function wp_custom_auto_check(){
	
	$buyers = get_users(
            array(
                'role' => 'buyer',
                'meta_query' => array(
                    array(
                        'key' => 'receive_email_alert',
                        'value' => true,
                        'compare' => '=='
                    )
                )
            )
        );
	if(!empty($buyers)){
		foreach($buyers as $buyer){
			//$buyers
			$tmp_fields = array(
					'name_this_notification',
					'keywords',
					'locations',
					'kms',
					'opportunities',
					'business_category',
					'investment_from',
					'investment_to',
				);
			$fields = array();
			foreach($tmp_fields as $field){
				$fields[$field] = get_user_meta( $buyer->ID, $field, true);
			}
			
			// get nearby post
			$latlng = realteo_geocode($fields['locations']);
			$nearbyposts = realteo_get_nearby_properties($latlng[0], $latlng[1], $fields['kms'], 'km' ); 
			if($nearbyposts){
				$post_ids = wp_list_pluck($nearbyposts, 'post_id');
				$search = (isset($fields['keywords'])&&$fields['keywords'])?$fields['keywords']:'';
				//$paged = 1;
				$search_args = array(
					'post_type' => 'property',
					'posts_per_page' => 3,
					'post__in' => $post_ids,
					'orderby' => 'post_date',
					'order' => 'DESC',
					's'		=> $search,
					'post_status' => 'publish',
            		//'paged' => $paged,
				);
				
				$meta_args = array('relation' => 'AND');
				$meta_args[] = array(
					'key' => '_price',
					'value' => array($fields['investment_from'], $fields['investment_to']),
					'compare' => 'BETWEEN',
					'type'    => 'NUMERIC'
				);
				if($fields['opportunities']){
					$meta_args[] = array(
						'key' => '_property_types',
						'value' => $fields['opportunities'],
						'compare' => 'IN'
					);
				}
				if($fields['business_category']){
					$meta_args[] = array(
						'key' => '_property_type',
						'value' => $fields['business_category'],
						'compare' => 'IN'
					);
				}				
				$search_args['meta_query'] = $meta_args;
				
				// convert to string to be compare to saved data
				$current_search = base64_encode ( serialize($search_args));
				//$toArray = unserialize(base64_decode($tostring));
				$temp_data = array(
					'main_query' 	=> $current_search,
					'current_page'	=> 1,
				);
				
				//if already recorded, get the last query
				$logged = get_user_meta( $buyer->ID, 'business_alert_logs', true );
				if($logged){
					$data_log = unserialize($logged);		
					
					// check latest post
					$lastest_args = $search_args;
					$lastest_args['date_query'] =  array(array('after' => '24 hours ago'));
					$latest = new WP_Query( $lastest_args );
					if($latest->found_posts){
						// reset if found new latest listing
						$search_args['paged'] = 1;
					}else{
						// compare to current search
						if($data_log['main_query'] == $current_search){
							// check if not end of page
							if($data_log['current_page'] < $data_log['total_pages']){
							// increment page
								$temp_data['current_page'] = $data_log['current_page']+1;
								$search_args['paged'] = $temp_data['current_page'];
							}else{
								// back to start page
								$search_args['paged'] = 1;
							}
						}
					}
					$temp_data['log_dates'] = $data_log['log_dates'];
				}else{
					$search_args['paged'] = 1;
				}
				
				
				$children = new WP_Query( $search_args );
				
				
				//$posts = get_posts($search_args);	
				if(!$children->found_posts)
					continue;
				
				
				$posts = array_map(
					function( $post ) {
						return (array) $post;
					},
					$children->posts
				);
				
				new WC_Emails();
				do_action( 'custom_business_alert_notification', $posts, $buyer->data->user_email, $fields['name_this_notification'] );
								
				$temp_data['total_post'] = $children->found_posts;
				$temp_data['total_pages'] = $children->max_num_pages;
				$temp_data['log_dates'][date("Y-m-d H:i:s")] = $children->request;
				$logs = serialize($temp_data);
				
				update_user_meta( $buyer->ID, 'business_alert_logs', $logs);
				//pr($temp_data);
				//exit();
				// search_logs
			}
			
		}
	}
	
}
add_action( 'system_check_customer', 'wp_custom_auto_check' );

function testhere() {
wp_custom_auto_check();
}
//add_action( 'init', 'testhere');

/**
* Filter the 'wpcf7_mail_components' to change componentes on composing email
*
* @param array $components Componentes from email
* @param WPCF7_ContactForm $contactform	Current ContactForm Obj
*/
function custom_mail_components( $components, $contactform ) {
	// CF7 Mail Compose - add woo email template
	
	//$properties = $contactform->get_properties();
	//pr($contactform);
	//pr($properties);
	//pr($contactform);
	//pr($components);
	//exit();

	// load the mailer class
		$mailer = WC()->mailer();

		$email_heading = $components['subject'];
		$message = cleanHtmlTags($components['body']);

			// create a new email
		$email = new WC_Email();

			// wrap the content with the email template and then add styles
		$message = apply_filters( 'woocommerce_mail_content', $email->style_inline( $mailer->wrap_message( $email_heading, $message ) ) );

		$components['body'] = $message;

	return $components;
}
add_filter( 'wpcf7_mail_components', 'custom_mail_components', 20, 2 );

/**
 * Strip only certain tags in the given HTML string
 * @return String
 */
function cleanHtmlTags($html){
	$tags = array('html','head','title','body');
	$tmp = $html;
	foreach($tags as $tag){
		$reg = '/<'.$tag.'[^>]*?>([\\s\\S]*?)<\/'.$tag.'>/';
		if($tag=='title')
			$tmp = preg_replace($reg,'', $tmp);
		else
			$tmp = preg_replace($reg,'\\1', $tmp);
	}
    return $tmp;
}
// https://contactform7.com/special-mail-tags/