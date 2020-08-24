<?php
/**
Plugin Name: SendPulse Free Web Push
Plugin URI: https://sendpulse.com/webpush?utm_source=wordpress
Description: SendPulse Free Web Push plugin adds your web push integration code into the &lt;head&gt; section of your website. The plugin will enable web push subscription requests to your website visitors and optionally pass  emails and names of logged in users for segmentation and personalization. To get started: 1)Click the "Activate" link to the left of this description, 2) Sign up for a free <a href="https://sendpulse.com/webpush/register?utm_source=wordpress&utm_medium=referral&utm_campaign=wordpresspush">Sendpulse account</a>, and 3) Add your website to SendPulse, copy and paste the integation code into the plugin settings section
Version: 1.1.0
Author: SendPulse
Author URI: https://sendpulse.com/webpush?utm_source=wordpress
License: GPLv2
Text Domain: sendpulse-webpush
*/

// Directories
define('SENDPULSE_WEBPUSH_ABS_PATH', get_public_dir(basename(dirname(__FILE__))));
define('SENDPULSE_WEBPUSH_PUBLIC_PATH', str_replace(DIRECTORY_SEPARATOR, '/', str_replace(str_replace('/', DIRECTORY_SEPARATOR, $_SERVER['DOCUMENT_ROOT']), '', dirname(__FILE__))));

load_plugin_textdomain('sendpulse-webpush', false, basename( dirname( __FILE__ ) ) . '/languages' );

/*
add_action('init', 'start_session', 1);
add_action('wp_logout', 'end_session');
add_action('wp_login', 'end_session');

function start_session() {
    if(!session_id()) {
        session_start();
    }
}

function end_session() {
    session_destroy ();
}
*/

function get_domain() {
    return ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;
}

// Check for forwardslash/backslash in folder path to structure paths
function get_public_dir($url = '') {
    $url = strval($url);
    if (!empty($url) && !preg_match('#(\\\\|/)$#', $url)) {
        return $url . '/';
    } else if (empty($url)) {
        return '/';
    } else {
        return $url;
    }
}

add_action('admin_notices', 'send_pulse_admin_notices');
function send_pulse_admin_notices() {
    if ($notices= get_option('send_pulse_deferred_admin_notices')) {
        foreach ($notices as $notice) {
            echo "<div class='updated'><p>$notice</p></div>";
        }
        delete_option('send_pulse_deferred_admin_notices');
    }
}

add_action( 'wp_head', 'sendpulse_display', 1000);
add_action( 'login_enqueue_scripts', 'sendpulse_display' ); // Write our JS below here
function sendpulse_display(){
	$pageid = get_queried_object_id();	
	$html = get_option( 'sendpulse_code', '' );
	echo $html;
}

add_action( 'wp_footer', 'sendpulse_user_reg_action' ); // Write our JS below here
add_action( 'login_enqueue_scripts', 'sendpulse_user_reg_action' ); // Write our JS below here
function sendpulse_user_reg_action() {
    $sendpulse_addinfo = get_option( 'sendpulse_addinfo', 'N' );
    if ($sendpulse_addinfo != 'Y')
        return;
    
	if (isset($_COOKIE['sendpulse_webpush_addinfo'])) {
		list($login, $email, $user_id) = explode('|', $_COOKIE['sendpulse_webpush_addinfo']);
        $domain = get_domain();
		?>
		<script src="<?php echo SENDPULSE_WEBPUSH_PUBLIC_PATH;?>/js/utils.js" type="text/javascript" ></script>
		<script type="text/javascript" >
        domReady(function() {
            var domain = '<?php echo $domain; ?>';
            window.addEventListener("load", function() {
                oSpP.push("Name","<?php echo $login; ?>");
                oSpP.push("Email","<?php echo $email; ?>");
                <?php /*deleteCookie('sendpulse_webpush_addinfo', '/',  domain);*/ ?>
            });
        })
		</script><?php
        $domain = get_domain();
        $secure = empty($_SERVER["HTTPS"]) ? 0 : 1;
        setcookie("sendpulse_webpush_addinfo", NULL, ( strtotime('-1 Year', time()) ), '/', $domain, $secure);
	}
}

add_action( 'user_register', 'sendpulseplugin_registration_save', 10, 1 );
function sendpulseplugin_registration_save( $user_id ) {
    $sendpulse_addinfo = get_option( 'sendpulse_addinfo', 'N' );
    if ($sendpulse_addinfo != 'Y')
        return;
    
	$login = ! empty($_REQUEST["user_login"]) ? $_REQUEST["user_login"] : '';
	$email = ! empty($_REQUEST["user_email"]) ? $_REQUEST["user_email"] : '';
    $expire = time()+3600*24*7;
    $domain = get_domain();
    $data = array(trim($login), $email, $user_id);
    $secure = empty($_SERVER["HTTPS"]) ? 0 : 1;
    setcookie("sendpulse_webpush_addinfo", implode('|', $data), $expire, "/", $domain, $secure);
}

//Create a menu
//Load in the option page
function SendpulseCreateATHMenu() {
	$menuname = __('Sendpulse webpush', 'sendpulse-webpush');
	add_options_page( $menuname, $menuname, 'manage_options', 'sendpulse-webpush', 'SendPulseSettings' );
}

function SendPulseSettings(){
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'settings.php');
	sendpulse_config();
}

//Create the option menu, and load admin CSS to it
add_action( 'admin_menu', 'SendpulseCreateATHMenu' );

//Installation
register_activation_hook( __FILE__, 'SendPulseInstallStep1');
function SendPulseInstallStep1(){
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'installdeinstall.php');
	SendPulseInstallStep2();
}

//Deactivation
register_deactivation_hook(__FILE__, 'SendPulseDeactivationStep1');
function SendPulseDeactivationStep1() {
    delete_option('send_pulse_deferred_admin_notices'); 
}

//Deinstallation
register_uninstall_hook( __FILE__, 'SendPulseDeinstallStep1');
function SendPulseDeinstallStep1(){
	include_once(dirname(__FILE__).DIRECTORY_SEPARATOR.'installdeinstall.php');
	SendPulseDeinstallStep2();	
}
?>