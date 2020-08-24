<?php
/*
Plugin Name: Social Media and Share Icons (Ultimate Social Media)
Plugin URI: http://ultimatelysocial.com
Description: Easy to use and 100% FREE social media plugin which adds social media icons to your website with tons of customization features!. 
Author: UltimatelySocial
Author URI: http://ultimatelysocial.com
Version: 2.2.0
License: GPLv2 or later
*/

sfsi_error_reporting();

global $wpdb;

/* define the Root for URL and Document */
define('SFSI_DOCROOT',    dirname(__FILE__));
define('SFSI_PLUGURL',    plugin_dir_url(__FILE__));
define('SFSI_WEBROOT',    str_replace(getcwd(), home_url(), dirname(__FILE__)));
define('SFSI_SUPPORT_FORM','https://goo.gl/wgrtUV');
define('SFSI_DOMAIN','ultimate-social-media-icons');

$wp_upload_dir = wp_upload_dir();
define('SFSI_UPLOAD_DIR_BASEURL', trailingslashit($wp_upload_dir['baseurl']));

define('SFSI_ALLICONS',serialize(array("rss","email","facebook","twitter","google","share","youtube","pinterest","instagram")));

function sfsi_get_current_page_url()
{
	global $post, $wp;

	if (!empty($wp)) {
		return home_url(add_query_arg(array(),$wp->request));
	}
	elseif(!empty($post))
	{
		return get_permalink($post->ID);
	}
	else
	{
		return site_url();
	}
}

/* load all files  */
include(SFSI_DOCROOT.'/libs/sfsi_install_uninstall.php');

include(SFSI_DOCROOT.'/helpers/common_helper.php');
include(SFSI_DOCROOT.'/libs/controllers/sfsi_socialhelper.php');
include(SFSI_DOCROOT.'/libs/controllers/sfsi_class_theme_check.php');
include(SFSI_DOCROOT.'/libs/controllers/sfsi_buttons_controller.php');
include(SFSI_DOCROOT.'/libs/controllers/sfsi_iconsUpload_contoller.php');
include(SFSI_DOCROOT.'/libs/controllers/sfsi_floater_icons.php');
include(SFSI_DOCROOT.'/libs/controllers/sfsi_frontpopUp.php');
include(SFSI_DOCROOT.'/libs/controllers/sfsiocns_OnPosts.php');

include(SFSI_DOCROOT.'/libs/sfsi_Init_JqueryCss.php');
include(SFSI_DOCROOT.'/libs/sfsi_widget.php');
include(SFSI_DOCROOT.'/libs/sfsi_subscribe_widget.php');
include(SFSI_DOCROOT.'/libs/sfsi_custom_social_sharing_data.php');
include(SFSI_DOCROOT.'/libs/sfsi_ajax_social_sharing_settings_updater.php');

/* plugin install and uninstall hooks */
register_activation_hook(__FILE__, 'sfsi_activate_plugin' );
register_deactivation_hook(__FILE__, 'sfsi_deactivate_plugin');
register_uninstall_hook(__FILE__, 'sfsi_Unistall_plugin');

if(!get_option('sfsi_pluginVersion') || get_option('sfsi_pluginVersion') < 2.20)
{
	add_action("init", "sfsi_update_plugin");
}

/* redirect setting page hook */
add_action('admin_init', 'sfsi_plugin_redirect');
function sfsi_plugin_redirect()
{
    if (get_option('sfsi_plugin_do_activation_redirect', false))
    {
        delete_option('sfsi_plugin_do_activation_redirect');
        wp_redirect(admin_url('admin.php?page=sfsi-options'));
    }
}

//************************************** Setting error reporting STARTS ****************************************//

function sfsi_error_reporting(){

	$option5 = unserialize(get_option('sfsi_section5_options',false));

	if(isset($option5['sfsi_icons_suppress_errors']) 

		&& !empty($option5['sfsi_icons_suppress_errors'])

		&& "yes" == $option5['sfsi_icons_suppress_errors']){
		
		error_reporting(0);			
	}	
}

//************************************** Setting error reporting CLOSES ****************************************//

//shortcode for the ultimate social icons {Monad}
add_shortcode("DISPLAY_ULTIMATE_SOCIAL_ICONS", "DISPLAY_ULTIMATE_SOCIAL_ICONS");
function DISPLAY_ULTIMATE_SOCIAL_ICONS($args = null, $content = null)
{
	$instance = array("showf" => 1, "title" => '');
	$return = '';
	if(!isset($before_widget)): $before_widget =''; endif;
	if(!isset($after_widget)): $after_widget =''; endif;
	
	/*Our variables from the widget settings. */
	$title 		= apply_filters('widget_title', $instance['title'] );
	$show_info  = isset( $instance['show_info'] ) ? $instance['show_info'] : false;
	
	global $is_floter;	      
	
	$return.= $before_widget;
		/* Display the widget title */
		if ( $title ) $return .= $before_title . $title . $after_title;
		$return .= '<div class="sfsi_widget">';
			$return .= '<div id="sfsi_wDiv"></div>';
			/* Link the main icons function */
			$return .= sfsi_check_visiblity(0);
	   		$return .= '<div style="clear: both;"></div>';
	    $return .= '</div>';
	$return .= $after_widget;
	return $return;
}

//adding some meta tags for facebook news feed {Monad}
function sfsi_checkmetas()
{
	if ( ! function_exists( 'get_plugins' ) )
	{
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}

	$adding_tags = "yes";

	$all_plugins = get_plugins();
	
	foreach($all_plugins as $key => $plugin):

		if(is_plugin_active($key))
		{
			if(preg_match("/(seo|search engine optimization|meta tag|open graph|opengraph|og tag|ogtag)/im", $plugin['Name']) || preg_match("/(seo|search engine optimization|meta tag|open graph|opengraph|og tag|ogtag)/im", $plugin['Description'])):

				$adding_tags= "no";

				break;

			endif;
		}

	endforeach;

	update_option("adding_tags", $adding_tags);

}
if ( is_admin() )
{
	sfsi_checkmetas();
}

add_action('wp_head', 'ultimatefbmetatags');
function ultimatefbmetatags()
{
	$metarequest = get_option("adding_tags");
	$post_id = get_the_ID();
	
	$feed_id = sanitize_text_field(get_option('sfsi_feed_id'));
	$verification_code = get_option('sfsi_verificatiom_code');
	if(!empty($feed_id) && !empty($verification_code) && $verification_code != "no" )
	{
	    echo '<meta name="specificfeeds-verification-code-'.$feed_id.'" content="'.$verification_code.'"/>';
	}
	
	if($metarequest == 'yes' && !empty($post_id))
	{
		$post = get_post( $post_id );
		$attachment_id = get_post_thumbnail_id($post_id);
		$title = str_replace('"', "", strip_tags(get_the_title($post_id)));
		$url = get_permalink($post_id);
		$description = $post->post_content;
		$description = str_replace('"', "", strip_tags($description));
		
		echo '<meta name="viewport" content="width=device-width, initial-scale=1">';
		
		if($attachment_id)
		{
		   $feat_image = wp_get_attachment_url( $attachment_id );
		   if (preg_match('/https/',$feat_image))
		   {
				echo '<meta property="og:image:secure_url" content="'.$feat_image.'" data-id="sfsi">';
		   }
		   else
		   {
				echo '<meta property="og:image" content="'.$feat_image.'" data-id="sfsi">';
		   }
		   $metadata = wp_get_attachment_metadata( $attachment_id );
		   if(isset($metadata) && !empty($metadata))
		   {
			   if(isset($metadata['sizes']['post-thumbnail']))
			   {
					$image_type = $metadata['sizes']['post-thumbnail']['mime-type'];
			   }
			   else
			   {
					$image_type = '';  
			   }
			   if(isset($metadata['width']))
			   {
					$width = $metadata['width'];
			   }
			   else
			   {
					$width = '';  
			   }
			   if(isset($metadata['height']))
			   {
					$height = $metadata['height'];
			   }
			   else
			   {
					$height = '';  
			   }
		   }
		   else
		   {
				$image_type = '';
				$width = '';
				$height = '';  
		   }  
		   echo '<meta property="og:image:type" content="'.$image_type.'" data-id="sfsi" />';
		   echo '<meta property="og:image:width" content="'.$width.'" data-id="sfsi" />';
		   echo '<meta property="og:image:height" content="'.$height.'" data-id="sfsi" />';
		   echo '<meta property="og:url" content="'.$url.'" data-id="sfsi" />'; 
		   echo '<meta property="og:description" content="'.$description.'" data-id="sfsi" />';
		   echo '<meta property="og:title" content="'.$title.'" data-id="sfsi" />';
		}
	}
}

//Get verification code
if(is_admin())
{	
	$code = sanitize_text_field(get_option('sfsi_verificatiom_code'));
	$feed_id = sanitize_text_field(get_option('sfsi_feed_id'));
	if(empty($code) && !empty($feed_id))
	{
		add_action("init", "sfsi_getverification_code");
	}
}

function sfsi_getverification_code()
{
	$feed_id = sanitize_text_field(get_option('sfsi_feed_id'));
	$curl = curl_init();  
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'http://www.specificfeeds.com/wordpress/getVerifiedCode_plugin',
        CURLOPT_USERAGENT => 'sf get verification',
        CURLOPT_POST => 1,
        CURLOPT_POSTFIELDS => array(
            'feed_id' => $feed_id
        )
    ));
     // Send the request & save response to $resp
	$resp = curl_exec($curl);
	$resp = json_decode($resp);
	update_option('sfsi_verificatiom_code', $resp->code);
	curl_close($curl);
}

//checking for the youtube username and channel id option
add_action('admin_init', 'check_sfsfiupdatedoptions');
function check_sfsfiupdatedoptions()
{
	$option4=  unserialize(get_option('sfsi_section4_options',false));
	if(isset($option4['sfsi_youtubeusernameorid']) && !empty($option4['sfsi_youtubeusernameorid']))
	{
	}
	else
	{
		$option4['sfsi_youtubeusernameorid'] = 'name';
		update_option('sfsi_section4_options',serialize($option4));
	}
}

add_action('plugins_loaded', 'sfsi_load_domain');
function sfsi_load_domain() 
{
	$plugin_dir = basename(dirname(__FILE__)).'/languages';
	load_plugin_textdomain( SFSI_DOMAIN, false, $plugin_dir );
}

//sanitizing values
function string_sanitize($s) {
    $result = preg_replace("/[^a-zA-Z0-9]+/", " ", html_entity_decode($s, ENT_QUOTES));
    return $result;
}

//Add Subscriber form css
add_action("wp_footer", "addStyleFunction");
function addStyleFunction()
{
	$option8 = unserialize(get_option('sfsi_section8_options',false));
	$sfsi_feediid = sanitize_text_field(get_option('sfsi_feed_id'));
	$url = "https://www.specificfeeds.com/widgets/subscribeWidget/";
	echo $return = '';
	?>
    	<script>
			jQuery(document).ready(function(e) {
                jQuery("body").addClass("sfsi_<?php echo get_option("sfsi_pluginVersion");?>")
            });
			function sfsi_processfurther(ref) {
				var feed_id = '<?php echo $sfsi_feediid?>';
				var feedtype = 8;
				var email = jQuery(ref).find('input[name="data[Widget][email]"]').val();
				var filter = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
				if ((email != "Enter your email") && (filter.test(email))) {
					if (feedtype == "8") {
						var url ="<?php echo $url; ?>"+feed_id+"/"+feedtype;
						window.open('', "popupwindow", "scrollbars=yes,width=1080,height=760");
						ref.action=url;
						ref.target="popupwindow";
						return true;
					}else{
						return false
					}
				} else {
					alert("Please enter email address");
					jQuery(ref).find('input[name="data[Widget][email]"]').focus();
					return false;
				}
			}
		</script>
        <style type="text/css" aria-selected="true">
			.sfsi_subscribe_Popinner
			{
				<?php if(sanitize_text_field($option8['sfsi_form_adjustment']) == 'yes') : ?>
				width: 100% !important;
				height: auto !important;
				<?php else: ?>
				width: <?php echo intval($option8['sfsi_form_width']) ?>px !important;
				height: <?php echo intval($option8['sfsi_form_height']) ?>px !important;
				<?php endif;?>
				<?php if(sanitize_text_field($option8['sfsi_form_border']) == 'yes') : ?>
				border: <?php echo intval($option8['sfsi_form_border_thickness'])."px solid ".sfsi_sanitize_hex_color($option8['sfsi_form_border_color']);?> !important;
				<?php endif;?>
				padding: 18px 0px !important;
				background-color: <?php echo sfsi_sanitize_hex_color($option8['sfsi_form_background']) ?> !important;
			}
			.sfsi_subscribe_Popinner form
			{
				margin: 0 20px !important;
			}
			.sfsi_subscribe_Popinner h5
			{
				font-family: <?php echo sanitize_text_field($option8['sfsi_form_heading_font']) ?> !important;
				<?php if(sanitize_text_field($option8['sfsi_form_heading_fontstyle']) != 'bold') {?>
				font-style: <?php echo sanitize_text_field($option8['sfsi_form_heading_fontstyle']) ?> !important;
				<?php } else{ ?>
				font-weight: <?php echo sanitize_text_field($option8['sfsi_form_heading_fontstyle']) ?> !important;
				<?php }?>
				color: <?php echo sfsi_sanitize_hex_color($option8['sfsi_form_heading_fontcolor']) ?> !important;
				font-size: <?php echo intval($option8['sfsi_form_heading_fontsize'])."px" ?> !important;
				text-align: <?php echo sanitize_text_field($option8['sfsi_form_heading_fontalign']) ?> !important;
				margin: 0 0 10px !important;
    			padding: 0 !important;
			}
			.sfsi_subscription_form_field {
				margin: 5px 0 !important;
				width: 100% !important;
				display: inline-flex;
				display: -webkit-inline-flex;
			}
			.sfsi_subscription_form_field input {
				width: 100% !important;
				padding: 10px 0px !important;
			}
			.sfsi_subscribe_Popinner input[type=email]
			{
				font-family: <?php echo sanitize_text_field($option8['sfsi_form_field_font']); ?> !important;
				<?php if(sanitize_text_field($option8['sfsi_form_field_fontstyle']) != 'bold') {?>
				font-style: <?php echo sanitize_text_field($option8['sfsi_form_field_fontstyle']) ?> !important;
				<?php } else{ ?>
				font-weight: <?php echo sanitize_text_field($option8['sfsi_form_field_fontstyle']) ?> !important;
				<?php }?>
				color: <?php echo sfsi_sanitize_hex_color($option8['sfsi_form_field_fontcolor']); ?> !important;
				font-size: <?php echo intval($option8['sfsi_form_field_fontsize'])."px" ?> !important;
				text-align: <?php echo sanitize_text_field($option8['sfsi_form_field_fontalign']); ?> !important;
			}
			.sfsi_subscribe_Popinner input[type=email]::-webkit-input-placeholder {
			   	font-family: <?php echo sanitize_text_field($option8['sfsi_form_field_font']); ?> !important;
				<?php if(sanitize_text_field($option8['sfsi_form_field_fontstyle']) != 'bold') {?>
				font-style: <?php echo sanitize_text_field($option8['sfsi_form_field_fontstyle']) ?> !important;
				<?php } else{ ?>
				font-weight: <?php echo sanitize_text_field($option8['sfsi_form_field_fontstyle']) ?> !important;
				<?php }?>
				color: <?php echo sfsi_sanitize_hex_color($option8['sfsi_form_field_fontcolor']); ?> !important;
				font-size: <?php echo intval($option8['sfsi_form_field_fontsize'])."px" ?> !important;
				text-align: <?php echo sanitize_text_field($option8['sfsi_form_field_fontalign']); ?> !important;
			}
			.sfsi_subscribe_Popinner input[type=email]:-moz-placeholder { /* Firefox 18- */
			    font-family: <?php echo sanitize_text_field($option8['sfsi_form_field_font']); ?> !important;
				<?php if(sanitize_text_field($option8['sfsi_form_field_fontstyle']) != 'bold') {?>
				font-style: <?php echo sanitize_text_field($option8['sfsi_form_field_fontstyle']) ?> !important;
				<?php } else{ ?>
				font-weight: <?php echo sanitize_text_field($option8['sfsi_form_field_fontstyle']) ?> !important;
				<?php }?>
				color: <?php echo sfsi_sanitize_hex_color($option8['sfsi_form_field_fontcolor']); ?> !important;
				font-size: <?php echo intval($option8['sfsi_form_field_fontsize'])."px" ?> !important;
				text-align: <?php echo sanitize_text_field($option8['sfsi_form_field_fontalign']); ?> !important;
			}
			.sfsi_subscribe_Popinner input[type=email]::-moz-placeholder {  /* Firefox 19+ */
			    font-family: <?php echo sanitize_text_field($option8['sfsi_form_field_font']); ?> !important;
				<?php if(sanitize_text_field($option8['sfsi_form_field_fontstyle']) != 'bold') {?>
				font-style: <?php echo sanitize_text_field($option8['sfsi_form_field_fontstyle']) ?> !important;
				<?php } else{ ?>
				font-weight: <?php echo sanitize_text_field($option8['sfsi_form_field_fontstyle']) ?> !important;
				<?php }?>
				color: <?php echo sfsi_sanitize_hex_color($option8['sfsi_form_field_fontcolor']); ?> !important;
				font-size: <?php echo intval($option8['sfsi_form_field_fontsize'])."px" ?> !important;
				text-align: <?php echo sanitize_text_field($option8['sfsi_form_field_fontalign']); ?> !important;
			}
			.sfsi_subscribe_Popinner input[type=email]:-ms-input-placeholder {  
			  	font-family: <?php echo sanitize_text_field($option8['sfsi_form_field_font']); ?> !important;
				<?php if(sanitize_text_field($option8['sfsi_form_field_fontstyle']) != 'bold') {?>
				font-style: <?php echo sanitize_text_field($option8['sfsi_form_field_fontstyle']) ?> !important;
				<?php } else{ ?>
				font-weight: <?php echo sanitize_text_field($option8['sfsi_form_field_fontstyle']) ?> !important;
				<?php }?>
				color: <?php echo sfsi_sanitize_hex_color($option8['sfsi_form_field_fontcolor']); ?> !important;
				font-size: <?php echo intval($option8['sfsi_form_field_fontsize'])."px" ?> !important;
				text-align: <?php echo sanitize_text_field($option8['sfsi_form_field_fontalign']); ?> !important;
			}
			.sfsi_subscribe_Popinner input[type=submit]
			{
				font-family: <?php echo sanitize_text_field($option8['sfsi_form_button_font']); ?> !important;
				<?php if(sanitize_text_field($option8['sfsi_form_button_fontstyle']) != 'bold') {?>
				font-style: <?php echo sanitize_text_field($option8['sfsi_form_button_fontstyle']) ?> !important;
				<?php } else{ ?>
				font-weight: <?php echo sanitize_text_field($option8['sfsi_form_button_fontstyle']) ?> !important;
				<?php }?>
				color: <?php echo sfsi_sanitize_hex_color($option8['sfsi_form_button_fontcolor']); ?> !important;
				font-size: <?php echo intval($option8['sfsi_form_button_fontsize'])."px" ?> !important;
				text-align: <?php echo sanitize_text_field($option8['sfsi_form_button_fontalign']); ?> !important;
				background-color: <?php echo sfsi_sanitize_hex_color($option8['sfsi_form_button_background']); ?> !important;
			}
		</style>
	<?php
}
add_action('admin_notices', 'sfsi_admin_notice', 10);
function sfsi_admin_notice()
{
	$language = get_option("WPLANG");
	
	// if(isset($_GET['page']) && $_GET['page'] == "sfsi-options")
	// {
	// 	$style = "overflow: hidden; margin:12px 3px 0px;";
	// }
	// else
	// {
	// 	$style = "overflow: hidden;"; 
	// }
	
	// $style = "overflow: hidden;"; 

	// /**
	//  * if wordpress uses other language
	//  */
	// if(!empty($language) && isset($_GET['page']) && $_GET['page'] == "sfsi-options" && 
	// 	get_option("sfsi_languageNotice") == "yes")
	// {
	// 	?>
<!-- 	// 	<style type="text/css">
	// 		form.sfsi_languageNoticeDismiss{
	// 		    display: inline-block;
	// 		    margin: 5px 0 0;
	// 		    vertical-align: middle;
	// 		}
	// 		.sfsi_languageNoticeDismiss input[type='submit']{
	// 			background-color: transparent;
	// 		    border: medium none;
	// 		    margin: 0;
	// 		    padding: 0;
	// 		    cursor: pointer;
	// 		}
	// 	</style>
	// 	<div class="updated" style="<?php //echo $style; ?>">
	// 		<div class="alignleft" style="margin: 9px 0;">
	// 			We detected that you're using a language other than English in Wordpress. We created also the <a target="_blank" href="https://wordpress.org/plugins/ultimate-social-media-plus/">Ultimate Social Media PLUS</a> plugin (still FREE) which allows you to select buttons in non-English languages (under question 6).
	// 		</div>
	// 		<div class="alignright">
	// 			<form method="post" class="sfsi_languageNoticeDismiss">
	// 				<input type="hidden" name="sfsi-dismiss-languageNotice" value="true">
	// 				<input type="submit" name="dismiss" value="Dismiss" />
	// 			</form>
	// 		</div>
	// 	</div> -->
	 	<?php 
	// }

	/**
	 * Premium Notification
	 */
	$domain 	= sfsi_getdomain(site_url());
	$siteMatch 	= false;
	
	if(!empty($domain))
	{
		$regexp = "/^([a-d A-D])/im";
		if(preg_match($regexp, $domain)) {
			$siteMatch = true;
		}
		else {
			$siteMatch = false;
		}
	}

	if(get_option("show_premium_notification") == "yes")
	{
		?>
		<style type="text/css">
			
			div.sfsi_show_premium_notification{
				float: none;
				display:inline-block;
    			width: 98.2%;
    			margin-left: 37px;
    			margin-top: 15px;
    			padding: 8px;
				background-color: #38B54A;
				color: #fff;
				font-size: 18px;
			}    					
			.sfsi_show_premium_notification a{
			   	color: #fff;
			}
			form.sfsi_premiumNoticeDismiss {
			    display: inline-block;
			    margin: 5px 0 0;
			    vertical-align: middle;
			}
			.sfsi_premiumNoticeDismiss input[type='submit']{
				background-color: transparent;
			    border: medium none;
			    color: #fff;
			    margin: 0;
			    padding: 0;
			    cursor: pointer;
			}
		</style>
	    <div class="updated sfsi_show_premium_notification" style="<?php //echo $style; ?>">
			<div class="alignleft" style="margin: 9px 0;">
				BIG NEWS : There is now a <b><a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=notification_banner&utm_medium=banner" target="_blank">Premium Ultimate Social Media Plugin</a></b> available with many more cool features : <a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=notification_banner&utm_medium=banner" target="_blank">Check it out</a>
			</div>
			<div class="alignright">
				<form method="post" class="sfsi_premiumNoticeDismiss">
					<input type="hidden" name="sfsi-dismiss-premiumNotice" value="true">
					<input type="submit" name="dismiss" value="Dismiss" />
				</form>
			</div>
		</div>
		<?php
	} 
	

	if(is_ssl()){
		
		if(get_option("show_premium_cumulative_count_notification") == "yes")
		{
			?>
			<style type="text/css">
				div.sfsi_show_premium_cumulative_count_notification{
				   	color: #fff;
				   	float: left;
	    			width: 94.2%;
	    			margin-left: 37px;
	    			margin-top: 15px;
	    			padding: 8px;
					background-color: #38B54A;
					color: #fff;
					font-size: 18px;
				}
				.sfsi_show_premium_cumulative_count_notification a{
				   	color: #fff;

				}
				form.sfsi_premiumCumulativeCountNoticeDismiss {
				    display: inline-block;
				    margin: 5px 0 0;
				    vertical-align: middle;
				}
				.sfsi_premiumCumulativeCountNoticeDismiss input[type='submit']{
					background-color: transparent;
				    border: medium none;
				    color: #fff;
				    margin: 0;
				    padding: 0;
				    cursor: pointer;
				}
			</style>
		    <div class="updated sfsi_show_premium_cumulative_count_notification">
				<div class="alignleft" style="margin: 9px 0;">
					<b>Recently switched to https?</b> If you don’t want to lose the Facebook share & like counts <a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=https_share_counts&utm_medium=banner" target="_blank">have a look at our Premium Plugin</a>, we found a fix for that: <a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=https_share_counts&utm_medium=banner" target="_blank">Check it out</a>
				</div>
				<div class="alignright">
					<form method="post" class="sfsi_premiumCumulativeCountNoticeDismiss">
						<input type="hidden" name="sfsi-dismiss-premiumCumulativeCountNoticeDismiss" value="true">
						<input type="submit" name="dismiss" value="Dismiss" />
					</form>
				</div>
				<div style=”clear:both”></div>
			</div>
			<?php
		} 
	}


	/* show mobile notification */
	if(get_option("show_mobile_notification") == "yes"){
		$sfsi_install_date = strtotime(get_option( 'sfsi_installDate' ));
		$sfsi_future_date = strtotime( '14 days',$sfsi_install_date );
		$sfsi_past_date = strtotime("now");
		if($sfsi_past_date >= $sfsi_future_date) {
		?>
			<style type="text/css">
				.sfsi_show_mobile_notification a{
					color: #fff;
				}
				form.sfsi_mobileNoticeDismiss {
					display: inline-block;
					margin: 5px 0 0;
					vertical-align: middle;
				}
				.sfsi_mobileNoticeDismiss input[type='submit']{
					background-color: transparent;
					border: medium none;
					color: #fff;
					margin: 0;
					padding: 0;
					cursor: pointer;
				}
			</style>
		<!-- <div class="updated sfsi_show_mobile_notification" style="<?php //echo $style; ?>background-color: #38B54A; color: #fff; font-size: 18px;">
				<div class="alignleft" style="margin: 9px 0;line-height: 24px;width: 95%;">
					<b>Over 50% of visitors are mobile visitors.</b> Make sure your social media icons look good on mobile too, so that people like & share your site. With the premium plugin you can define the location of the icons separately on mobile:<a href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_settings_page&utm_campaign=check_mobile&utm_medium=banner" target="_blank">Check it out</a>
				</div>
				<div class="alignright">
					<form method="post" class="sfsi_mobileNoticeDismiss">
						<input type="hidden" name="sfsi-dismiss-mobileNotice" value="true">
						<input type="submit" name="dismiss" value="Dismiss" />
					</form>
				</div>
			</div> -->
		<?php
		}
	}
/* end show mobile notification */
/* start phpversion error notification*/
    $phpVersion = phpVersion();
	if($phpVersion <= '5.4')
	{
		if(get_option("sfsi_serverphpVersionnotification") == "yes")
		{

		?>
        
        <style type="text/css">
			.sfsi_show_phperror_notification {
			   	color: #fff;
			   	text-decoration: underline;
			}
			form.sfsi_phperrorNoticeDismiss {
			    display: inline-block;
			    margin: 5px 0 0;
			    vertical-align: middle;
			}
			.sfsi_phperrorNoticeDismiss input[type='submit']
			{
				background-color: transparent;
			    border: medium none;
			    color: #fff;
			    margin: 0;
			    padding: 0;
			    cursor: pointer;
			}
			.sfsi_show_phperror_notification p{line-height: 22px;}
			p.sfsi_show_notifictaionpragraph{padding: 0 !important;font-size: 18px;}
			
		</style>

	     <div class="updated sfsi_show_phperror_notification" style="<?php echo $style; ?>background-color: #D22B2F; color: #fff; font-size: 18px; border-left-color: #D22B2F;">
			<div class="alignleft" style="margin: 9px 0;">

				<p class="sfsi_show_notifictaionpragraph">
					We noticed you are running your site on a PHP version older than 5.4. Please upgrade to a more recent version. This is not only important for running the Ultimate Social Media Plugin, but also for security reasons in general.
					<br>
					If you do not know how to do the upgrade, please ask your server team or hosting company to do it for you.' 
                </p>
		
			</div>
			<div class="alignright">
				<form method="post" class="sfsi_phperrorNoticeDismiss">
					<input type="hidden" name="sfsi-dismiss-phperrorNotice" value="true">
					<input type="submit" name="dismiss" value="Dismiss" />
				</form>
			</div>
		</div>      
            
		<?php
		}
	}

    sfsi_get_language_detection_notice();

    sfsi_language_notice();
    
    sfsi_addThis_removal_notice();

	sfsi_error_reporting_notice();
}

function sfsi_get_language_detection_notice(){

    $currLang = get_locale();
    $text     = '';

    switch ($currLang) {

        // Arabic
        // case 'ar':
            
        //     $text = "";
        //     break;

        // Chinese - simplified
        case 'zh-Hans':
            
            $text = "似乎你的WordPress仪表盘使用的是法语。你知道 终极社交媒体插件 也支持法语吗？ <a target='_blank' href='https://wordpress.org/plugins/ultimate-social-media-plus/'><b>请点击此处</b></a>";
            break;

        // Chinese - traditional
        // case 'zh-Hant':
            
        //     $text = "";
        //     break;

        // Dutch, Dutch (Belgium)
        // case 'nl_NL': case 'nl_BE':                
        //     $text = "";
        //     break;

        // French (Belgium), French (France)
        case 'fr_BE': case 'fr_FR':
            
            $text = "Il semblerait que votre tableau de bord Wordpress soit en Français. Saviez-vous que l'extension Ultimate  Social Media est aussi disponible en Français? <a target='_blank' href='https://wordpress.org/plugins/ultimate-social-media-plus/'>Cliquez ici</a>";
            break;

        // German, German (Switzerland)
        case 'de': case 'de_CH':

            $text = "Dein Wordpress-Dashboard scheint auf deutsch zu sein. Wusstest Du dass das Ultimate Social Media Plugin auch auf deutsch verfügbar ist? <a target='_blank' href='https://wordpress.org/plugins/ultimate-social-media-plus/'>Klicke hier</a>"; 
            break;

        // Greek
        // case 'el':
            
        //     $text = "";
        //     break;

        // Hebrew
        case 'he_IL':

            $text = "נדמה שלוח הבקרה שלך הוא בעברית. האם ידעת שהתוסף זמין גם בשפה העברית? <a target='_blank' href='https://wordpress.org/plugins/ultimate-social-media-plus/'>לחץ כאן</a>";
            break;

        // Hindi
        // case 'hi_IN':
            
        //     $text = ""; 
        //     break;

        // Indonesian
        // case 'id':
            
        //     $text = "";

        //     break;

        // Italian
        case 'it_IT':
            
           $text = "Semberebbe che la tua bacheca di WordPress sia in Italiano.Lo sapevi che il plugin Ultimate Social Media è anche dispoinibile in Italiano? <a target='_blank' href='https://wordpress.org/plugins/ultimate-social-media-plus/'>Fai click qui</a>";
            
            break;                   

        // Japanese
        // case 'ja':
            
        //     $text = "";

        //     break;                       

        // Korean
        // case 'ko_KR ':

        //     $text = ""; 

        //     break;                       

        // Persian, Persian (Afghanistan)
        // case 'fa_IR':case 'fa_AF':
            
        //     $text = "";
            
        //     break;                       

        // Polish

        // case 'pl_PL':
        //     $text = "";
        //     break;

        //Portuguese (Brazil), Portuguese (Portugal)

        case 'pt_BR': case 'pt_PT':

            $text = "Parece que seu painel Wordpress está em português. Você sabia que o plugin Ultimate Social Media também está disponível em português? <a target='_blank' href='https://wordpress.org/plugins/ultimate-social-media-plus/'>Clique aqui</a>";

            break;                       

        // Russian, Russian (Ukraine)
        case 'ru_RU': case 'ru_UA': 

            $text = "Ты говоришь по-русски? Если у вас есть вопросы о плагине Ultimate Social Media, задайте свой вопрос в форуме поддержки, мы постараемся ответить на русский: <a target='_blank' href='https://wordpress.org/plugins/ultimate-social-media-plus/'>Нажмите здесь</a>";
            
            break;                       
        
        /* Spanish (Argentina), Spanish (Chile), Spanish (Colombia), Spanish (Mexico),
            Spanish (Peru), Spanish (Puerto Rico), Spanish (Spain), Spanish (Venezuela) */

        case 'es_AR': case 'es_CL': case 'es_CO': case 'es_MX':case 'es_PE':case 'es_PR':
        case 'es_ES': case 'es_VE':

            $text = "Al parecer, tu dashboard en Wordpress está en Francés/ ¿Sabías que el complemento Ultimate Social Media está también disponible en Francés? <a target='_blank' href='https://wordpress.org/plugins/ultimate-social-media-plus/'>Haz clic aquí</a>";
            break;                       

        //  Swedish

        // case 'sv_SE':
            
        //     $text = "<a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>Klicka här</a>";
        //     break;                       

        //  Turkish

        case 'tr_TR':
            $text = "Wordpress gösterge panelinizin dili Türkçe olarak görünüyor. Ultimate Social Media eklentisinin Türkçe için de mevcut olduğunu biliyor musunuz? <a target='_blank' href='https://wordpress.org/plugins/ultimate-social-media-plus/'>Buraya tıklayın</a>";
            break;                       

        //  Ukrainian

        // case 'uk':
        //     $text = "<a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>натисніть тут</a>";
        //     break;                       

        //  Vietnamese

        case 'vi':
            $text = 'Có vẻ như bảng điều khiển Wordpress của bạn đang hiển thị "tiếng Việt". Bạn có biết rằng Ultimate Social Media plugin cũng hỗ trợ tiếng Việt? <a target="_blank" href="https://wordpress.org/plugins/ultimate-social-media-plus/">Hãy nhấn vào đây</a>';
            break;    
    }

	$style = "overflow: hidden;padding:8px;margin:15px 15px 15px 0px !important";

	if(!empty($text) && isset($_GET['page']) 
		&& ("sfsi-options" == $_GET['page']) && ("yes" == get_option("sfsi_languageNotice") ) ) {
	 ?>

		<style type="text/css">
			form.sfsi_languageNoticeDismiss{display: inline-block;margin: 5px 0 0;vertical-align: middle;}
			.sfsi_languageNoticeDismiss input[type='submit']{background-color: transparent;border: medium none;margin: 0 5px 0 0px;padding: 0;cursor: pointer;font-size: 22px;}
		</style>
		<div class="notice notice-info" style="<?php echo $style; ?>">
			<div class="alignleft" style="margin: 9px 0;">
				<?php echo $text; ?>
			</div>
			<div class="alignright">
				<form method="post" class="sfsi_languageNoticeDismiss">
					<input type="hidden" name="sfsi-dismiss-languageNotice" value="true">
					<input type="submit" name="dismiss" value="&times;" />
				</form>
			</div>
		</div>
		
	<?php }
}


add_action('admin_init', 'sfsi_dismiss_admin_notice');
function sfsi_dismiss_admin_notice()
{
	if ( isset($_REQUEST['sfsi-dismiss-notice']) && $_REQUEST['sfsi-dismiss-notice'] == 'true' )
	{
		update_option( 'show_notification_plugin', "no" );
		//header("Location: ".site_url()."/wp-admin/admin.php?page=sfsi-options");die;
	}
	
	if ( isset($_REQUEST['sfsi-dismiss-languageNotice']) && $_REQUEST['sfsi-dismiss-languageNotice'] == 'true' )
	{
		update_option( 'sfsi_languageNotice', "no" );
		//header("Location: ".site_url()."/wp-admin/admin.php?page=sfsi-options"); die;
	}

	if ( isset($_REQUEST['sfsi-dismiss-premiumNotice']) && $_REQUEST['sfsi-dismiss-premiumNotice'] == 'true' )
	{
		update_option( 'show_premium_notification', "no" );
		//header("Location: ".site_url()."/wp-admin/admin.php?page=sfsi-options");die;
	}
	
	if ( isset($_REQUEST['sfsi-dismiss-mobileNotice']) && $_REQUEST['sfsi-dismiss-mobileNotice'] == 'true' )
	{
		update_option( 'show_mobile_notification', "no" );
		//header("Location: ".site_url()."/wp-admin/admin.php?page=sfsi-options");die;
	}
	if ( isset($_REQUEST['sfsi-dismiss-phperrorNotice']) && $_REQUEST['sfsi-dismiss-phperrorNotice'] == 'true' )
	{
		update_option( 'sfsi_serverphpVersionnotification', "no" );
	}
	if ( isset($_REQUEST['sfsi-dismiss-premiumCumulativeCountNoticeDismiss']) && $_REQUEST['sfsi-dismiss-premiumCumulativeCountNoticeDismiss'] == 'true' )
	{
		update_option( 'show_premium_cumulative_count_notification', "no" );
	}

}

function sfsi_get_bloginfo($url)
{
	$web_url = get_bloginfo($url);
	
	//Block to use feedburner url
	if (preg_match("/(feedburner)/im", $web_url, $match))
	{
		$web_url = site_url()."/feed";
	}
	return $web_url;
}

function sfsi_getdomain($url)
{
	$pieces = parse_url($url);
	$domain = isset($pieces['host']) ? $pieces['host'] : '';
	if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
		return $regs['domain'];
	}
	return false;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), "sfsi_actionLinks", -10 );
function sfsi_actionLinks($links)
{
	unset($links['edit']);    
	$links['a'] = '<a target="_blank" href="https://goo.gl/auxJ9C#no-topic-0" id="sfsi_deactivateButton" style="color:#FF0000;"><b>Need help?</b></a>';	
	//$links[] = '<a target="_blank" href="https://www.ultimatelysocial.com/usm-premium/?utm_source=usmi_manage_plugin_page&utm_campaign=check_out_pro_version&utm_medium=banner" id="sfsi_deactivateButton" style="color:#38B54A;"><b>Check out pro version</b></a>';
	
	/*if(isset($links["edit"]) && !empty($links["edit"])){
		$links[] = @$links["edit"];		
	}*/

	//$slug = plugin_basename(dirname(__FILE__));
	//$links[$slug] = @$links["deactivate"].'<i class="sfsi-deactivate-slug"></i>';

	$links['e'] = '<a href="'.admin_url("/admin.php?page=sfsi-options").'">Settings</a>';

    	ksort($links);

	//unset($links["deactivate"]);
	return $links;
}

global $pagenow;

if( 'plugins.php' === $pagenow ){

	add_action( 'admin_footer', '_sfsi_add_deactivation_feedback_dialog_box');

	function _sfsi_add_deactivation_feedback_dialog_box(){ 
		
		include_once(SFSI_DOCROOT.'/views/deactivation/sfsi_deactivation_popup.php'); ?>

		<script type="text/javascript">
		    
		    jQuery(document).ready(function($){

		    	var _deactivationLink = $('.sfsi-deactivate-slug').prev();

		    	$('.sfsi-deactivation-reason-link').find('a').attr('href',_deactivationLink.attr('href'));

		        _deactivationLink.on('click',function(e){
		            e.preventDefault();
		            $('[data-popup="popup-1"]').fadeIn(350);
		        });

		        //----- CLOSE
		        $('[data-popup-close]').on('click', function(e) {
		            e.preventDefault();
		            var targeted_popup_class = jQuery(this).attr('data-popup-close');
		            $('[data-popup="' + targeted_popup_class + '"]').fadeOut(350);
		        });

		        //----- OPEN
		        $('[data-popup-open]').on('click', function(e) {
		            e.preventDefault();
		            var targeted_popup_class = jQuery(this).attr('data-popup-open');
		            $('[data-popup="' + targeted_popup_class + '"]').fadeIn(350);
		        });

		        $('.sfsi-deactivate-radio').on('click', function(e) {

		            $('.sfsi-deactivate-radio').attr('checked',false);
		            $(this).attr('checked',true);

		            var val = $(this).val();

		            $('.sfsi-reason-section').removeClass('show').addClass('hide');
		            $(this).parent().find('.sfsi-reason-section').addClass('show').removeClass('hide');
		        });

		        $('.sfsi-deactivate-radio-text').on('click',function(e){
		            $(this).prev().trigger('click');
		        });

		    });

		</script>
		<?php
	}
}

/* redirect setting page hook */

/*add_action('admin_init', 'sfsi_plugin_redirect');
function sfsi_plugin_redirect()
{
    if (get_option('sfsi_plugin_do_activation_redirect', false))
    {
        delete_option('sfsi_plugin_do_activation_redirect');
        wp_redirect(admin_url('admin.php?page=sfsi-options'));
    }
}
*/
function sfsi_curl_error_notification()
{
	if(get_option("sfsi_curlErrorNotices") == "yes")
	{   
		?>
	        <script type="text/javascript">
	        jQuery(document).ready(function(e) {
	            jQuery(".sfsi_curlerror_cross").click(function(){
	                SFSI.ajax({
	                    url:sfsi_icon_ajax_object.ajax_url,
	                    type:"post",
	                    data: {action: "sfsi_curlerrornotification"},
	                    success:function(msg)
	                    {   
	                        jQuery(".sfsi_curlerror").hide("fast");
	                        
	                    }
	                });
	            });
	        });
	        </script>

	        <div class="sfsi_curlerror">
	            We noticed that your site returns a cURL error («Error:  
	            <?php  echo ucfirst(get_option("sfsi_curlErrorMessage")); ?>
	            »). This means that it cannot send a notification to SpecificFeeds.com when a new post is published. Therefore this email-feature doesn’t work. However there are several solutions for this, please visit our FAQ to see the solutions («Perceived bugs» => «cURL error messages»): 
	            <a href="https://www.ultimatelysocial.com/faq/" target="_new">
	                www.ultimatelysocial.com/faq
	            </a>
	           <div class="sfsi_curlerror_cross">Dismiss</div>
	        </div>
        <?php  
    } 
}

function _is_curl_installed(){

	if(in_array('curl', get_loaded_extensions())) {
	    return true;
	}
	else{
	    return false;
	}
}

// ********************************* Link to support forum for different languages STARTS *******************************//

function sfsi_get_language_notice_text(){

    $currLang = get_locale();
    $text     = '';

    switch ($currLang) {

        // Arabic
        case 'ar':
            
            $text = "hal tatakalam alearabia? 'iidha kanat ladayk 'asyilat hawl almukawan al'iidafii l Ultimate Social Media , aitruh sualik fi muntadaa aldaem , sanuhawil alrada biallughat alearabiat: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'><b>'unqur huna</b></a>";
            break;

        // Chinese - simplified
        case 'zh-Hans':
            
            $text = "你会说中文吗？如果您有关于Ultimate Social Media插件的问题，请在支持论坛中提出您的问题，我们将尝试用中文回复：<a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'><b>点击此处</b></a>";
            break;

        // Chinese - traditional
        case 'zh-Hant':
            
            $text = "你會說中文嗎？如果您有關於Ultimate Social Media插件的問題，請在支持論壇中提出您的問題，我們將嘗試用中文回复：<a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'><b>點擊此處</b></a>";
            break;

        // Dutch, Dutch (Belgium)
        case 'nl_NL': case 'nl_BE':                
            $text = "Jij spreekt Nederlands? Als je vragen hebt over de Ultimate Social Media-plug-in, stel je vraag in het ondersteuningsforum, we zullen proberen in het Nederlands te antwoorden: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>klik hier</a>";
            break;

        // French (Belgium), French (France)
        case 'fr_BE': case 'fr_FR':
            
            $text = "Vous parlez français? Si vous avez des questions sur le plugin Ultimate Social Media, posez votre question sur le forum de support, nous essaierons de répondre en français: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>Cliquez ici</a>";
            break;

        // German, German (Switzerland)
        case 'de': case 'de_CH':

            $text = "Du sprichst Deutsch? Wenn Du Fragen zum Ultimate Social Media-Plugins hast, einfach im Support Forum fragen. Wir antworten auch auf Deutsch! <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>Klicke hier</a>"; 
            break;

        // Greek
        case 'el':
            
            $text = "Μιλάτε Ελληνικά? Αν έχετε ερωτήσεις σχετικά με το plugin Ultimate Social Media, ρωτήστε την ερώτησή σας στο φόρουμ υποστήριξης, θα προσπαθήσουμε να απαντήσουμε στα ελληνικά: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>Κάντε κλικ εδώ</a>";
            break;

        // Hebrew
        case 'he_IL':
            
            $text = "אתה מדבר עברית? אם יש לך שאלות על תוסף המדיה החברתית האולטימטיבית, שאל את השאלה שלך בפורום התמיכה, ננסה לענות בעברית: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>לחץ כאן</a>";
            break;

        // Hindi
        case 'hi_IN':
            
            $text = "आप हिंदी बोलते हो? यदि आपके पास अल्टीमेट सोशल मीडिया प्लगइन के बारे में कोई प्रश्न है, तो समर्थन फोरम में अपना प्रश्न पूछें, हम हिंदी में जवाब देने का प्रयास करेंगे: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>यहां क्लिक करें</a>"; 
            break;

        // Indonesian
        case 'id':
            
            $text = "Anda berbicara bahasa Indonesia? Jika Anda memiliki pertanyaan tentang plugin Ultimate Social Media, ajukan pertanyaan Anda di Forum Dukungan, kami akan mencoba menjawab dalam Bahasa Indonesia: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>Klik di sini</a>";

            break;

        // Italian
        case 'it_IT':
            
            $text = "Tu parli italiano? Se hai domande sul plugin Ultimate Social Media, fai la tua domanda nel Forum di supporto, cercheremo di rispondere in italiano: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>clicca qui</a>";
            
            break;                   

        // Japanese
        case 'ja':
            
            $text = "あなたは日本語を話しますか？アルティメットソーシャルメディアのプラグインに関する質問がある場合は、サポートフォーラムで質問してください。日本語で対応しようと思っています：<a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>ここをクリック</a>";

            break;                       

        // Korean
        case 'ko_KR ':

            $text = "한국어를 할 줄 아세요? 궁극적 인 소셜 미디어 플러그인에 대해 궁금한 점이 있으면 지원 포럼에서 질문하십시오. 한국어로 답변하려고합니다 : <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>여기를 클릭하십시오.</a>"; 

            break;                       

        // Persian, Persian (Afghanistan)
        case 'fa_IR':case 'fa_AF':
            
            $text = "شما فارسی صحبت می کنید؟ اگر سوالی در مورد پلاگین رسانه Ultimate Social دارید، سوال خود را در انجمن پشتیبانی بپرسید، سعی خواهیم کرد به فارسی پاسخ دهید: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>اینجا را کلیک کنید</a>";
            
            break;                       

        // Polish

        case 'pl_PL':
            $text = "Mówisz po polsku? Jeśli masz pytania dotyczące wtyczki Ultimate Social Media, zadaj pytanie na Forum pomocy technicznej, postaramy się odpowiedzieć po polsku: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>Kliknij tutaj</a>";
            break;

        //Portuguese (Brazil), Portuguese (Portugal)

        case 'pt_BR': case 'pt_PT':

            $text = "Você fala português? Se você tiver dúvidas sobre o plug-in Ultimate Social Media, faça sua pergunta no Fórum de suporte, tentaremos responder em português: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>Clique aqui</a>";

            break;                       

        // Russian, Russian (Ukraine)
        case 'ru_RU': case 'ru_UA': 

            $text = "Ты говоришь по-русски? Если у вас есть вопросы о плагине Ultimate Social Media, задайте свой вопрос в форуме поддержки, мы постараемся ответить на русский: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>Нажмите здесь</a>";
            
            break;                       
        
        /* Spanish (Argentina), Spanish (Chile), Spanish (Colombia), Spanish (Mexico),
            Spanish (Peru), Spanish (Puerto Rico), Spanish (Spain), Spanish (Venezuela) */

        case 'es_AR': case 'es_CL': case 'es_CO': case 'es_MX':case 'es_PE':case 'es_PR':
        case 'es_ES': case 'es_VE':

            $text = "¿Tu hablas español? Si tiene alguna pregunta sobre el complemento Ultimate Social Media, formule su pregunta en el foro de soporte, intentaremos responder en español: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>haga clic aquí</a>";
            break;                       

        //  Swedish

        case 'sv_SE':
            
            $text = "Pratar du svenska? Om du har frågor om programmet Ultimate Social Media, fråga din fråga i supportforumet, vi försöker svara på svenska: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>Klicka här</a>";
            break;                       

        //  Turkish

        case 'tr_TR':
            $text = "Sen Türkçe konuş? Nihai Sosyal Medya eklentisi hakkında sorularınız varsa, sorunuza Destek Forumu'nda sorun, Türkçe olarak cevap vermeye çalışacağız: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>Tıklayın</a>";
            break;                       

        //  Ukrainian

        case 'uk':
            $text = "Ви говорите по-українськи? Якщо у вас є запитання про плагін Ultimate Social Media, задайте своє питання на Форумі підтримки, ми спробуємо відповісти українською: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>натисніть тут</a>";
            break;                       

        //  Vietnamese

        case 'vi':
            $text = "Bạn nói tiếng việt không Nếu bạn có câu hỏi về plugin Ultimate Social Media, hãy đặt câu hỏi của bạn trong Diễn đàn hỗ trợ, chúng tôi sẽ cố gắng trả lời bằng tiếng Việt: <a target='_blank' href='https://goo.gl/ZiFsAF#no-topic-0'>Nhấp vào đây</a>";
            break;    
    }

    return $text;
}

function sfsi_language_notice(){

    if (isset($_GET['page']) && "sfsi-options" == $_GET['page']) : 

        $langText    = sfsi_get_language_notice_text();
        $isDismissed = get_option('sfsi_lang_notice_dismissed');

        if(!empty($langText) && false == $isDismissed) { ?>
                    
            <div id="sfsi_plus_langnotice" class="notice notice-info">

                <p><?php echo $langText; ?></p>

                <button type="button" class="sfsi-notice-dismiss notice-dismiss"></button>

            </div>

        <?php } ?>

    <?php endif;
}


function sfsi_dismiss_lang_notice(){
	if ( !wp_verify_nonce( $_POST['nonce'], "sfsi_dismiss_lang_notice'")) {
		echo  json_encode(array('res'=>"error")); exit;
	}
    if(!current_user_can('manage_options')){ echo json_encode(array('res'=>'not allowed'));die(); }

	
	echo update_option('sfsi_lang_notice_dismissed',true) ? "true" : "false";
	die;
}

add_action( 'wp_ajax_sfsi_dismiss_lang_notice', 'sfsi_dismiss_lang_notice' );

// ********************************* Link to support forum for different languages CLOSES *******************************//



// ********************************* Notice for removal of AddThis option STARTS *******************************//
function sfsi_addThis_removal_notice(){

    if (isset($_GET['page']) && "sfsi-options" == $_GET['page']) : 
        
        $sfsi_addThis_removalText    = "We removed Addthis from the plugin due to issues with GDPR, the new EU data protection regulation.";

        $isDismissed   =  get_option('sfsi_addThis_icon_removal_notice_dismissed',false);

        if( false == $isDismissed) { ?>
                    
            <div id="sfsi_plus_addThis_removal_notice" class="notice notice-info">

                <p><?php echo $sfsi_addThis_removalText; ?></p>

                <button type="button" class="sfsi-AddThis-notice-dismiss notice-dismiss"></button>

            </div>

        <?php } ?>

    <?php endif;
}

function sfsi_dismiss_addthhis_removal_notice(){
	if ( !wp_verify_nonce( $_POST['nonce'], "sfsi_dismiss_addThis_icon_notice")) {
		echo  json_encode(array('res'=>"error")); exit;
	}
    if(!current_user_can('manage_options')){ echo json_encode(array('res'=>'not allowed'));die(); }



	echo (string) update_option('sfsi_addThis_icon_removal_notice_dismissed',true);
	die;
}

add_action( 'wp_ajax_sfsi_dismiss_addThis_icon_notice', 'sfsi_dismiss_addthhis_removal_notice' );

// ********************************* Notice for removal of AddThis option CLOSES *******************************//


// ********************************* Link to support forum left of every Save button STARTS *******************************//

function sfsi_ask_for_help($viewNumber){ ?>

    <div class="sfsi_askforhelp askhelpInview<?php echo $viewNumber; ?>">
	
		<img src="<?php echo SFSI_PLUGURL."images/questionmark.png";?>"/>
		
		<span>Questions? <a target="_blank" href="#" onclick="event.preventDefault();sfsi_open_chat(event)"><b>Ask us</b></a></span>

	</div>

<?php }

// ********************************* Link to support forum left of every Save button CLOSES *******************************//


// ********************************* Notice for error reporting STARTS *******************************//

function sfsi_error_reporting_notice(){

    if (is_admin()) : 
        
        $sfsi_error_reporting_notice_txt    = 'We noticed that you have set error reporting to "yes" in wp-config. Our plugin (Ultimate Social Media Icons) switches this to "off" so that no errors are displayed (which may also impact error messages from your theme or other plugins). If you don\'t want that, please select the respective option under question 6 (at the bottom).';

        $isDismissed   =  get_option('sfsi_error_reporting_notice_dismissed',false);

        $option5 = unserialize(get_option('sfsi_section5_options',false));

		$sfsi_icons_suppress_errors = isset($option5['sfsi_icons_suppress_errors']) && !empty($option5['sfsi_icons_suppress_errors']) ? $option5['sfsi_icons_suppress_errors']:  false;

        if(isset($isDismissed) && false == $isDismissed && defined('WP_DEBUG') && false != WP_DEBUG && "yes"== $sfsi_icons_suppress_errors) { ?>
                    
            <div style="padding: 10px;margin-left: 0px;position: relative;" id="sfsi_error_reporting_notice" class="error notice">

                <p><?php echo $sfsi_error_reporting_notice_txt; ?></p>

                <button type="button" class="sfsi_error_reporting_notice-dismiss notice-dismiss"></button>

            </div>

            <script type="text/javascript">

				if(typeof jQuery != 'undefined'){

				    (function sfsi_dismiss_notice(btnClass,ajaxAction,nonce){
				        
				        var btnClass = "."+btnClass;

						var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";

				        jQuery(document).on("click", btnClass, function(){
				            
				            jQuery.ajax({
				                url:ajaxurl,
				                type:"post",
				                data:{action: ajaxAction},
				                success:function(e) {
				                    if(false != e){
				                        jQuery(btnClass).parent().remove();
				                    }
				                }
				            });

				        });

				    }("sfsi_error_reporting_notice-dismiss","sfsi_dismiss_error_reporting_notice","<?php echo wp_create_nonce('sfsi_dismiss_error_reporting_notice'); ?>"));
				}            	
            </script>

        <?php } ?>

    <?php endif;	
}

function sfsi_dismiss_error_reporting_notice(){
	if ( !wp_verify_nonce( $_POST['nonce'], "sfsi_dismiss_error_reporting_notice")) {
		echo  json_encode(array('res'=>"error")); exit;
	}
    if(!current_user_can('manage_options')){ echo json_encode(array('res'=>'not allowed'));die(); }



	echo (string) update_option('sfsi_error_reporting_notice_dismissed',true);
	die;
}
add_action( 'wp_ajax_sfsi_dismiss_error_reporting_notice', 'sfsi_dismiss_error_reporting_notice' );

// ********************************* Notice for error reporting CLOSE *******************************//