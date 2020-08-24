<?php
function sendpulse_config(){
  $currenturl = $_SERVER["REQUEST_URI"];
  ?>

  <link rel="stylesheet" type="text/css" href="<?php echo SENDPULSE_WEBPUSH_PUBLIC_PATH;?>/custom.css" media="all"/>

  <div class="wrap">
  <h2><?php _e('Insert integration code', 'sendpulse-webpush'); ?></h2>
  <h3><?php _e('The code you put in here will be inserted into the &lt;head&gt; tag on every page.', 'sendpulse-webpush'); ?></h3>
 
  <?php
  $html = get_option( 'sendpulse_code', '' );

  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	  if (isset($_POST['sendpulse_active'])) {
		  update_option( 'sendpulse_active', 'Y' );
	  } else {
		  delete_option( 'sendpulse_active' );
	  }
	  if (isset($_POST['sendpulse_addinfo'])) {
		  update_option( 'sendpulse_addinfo', 'Y' );
	  } else {
		  delete_option( 'sendpulse_addinfo' );
	  }
	  
	  if(isset($_POST['html'])/* && current_user_can('add-to-head')*/){
		  $newhtml = stripslashes_deep($_POST['html']);
		  if($newhtml == $html){
			  echo "<p class=\"not-edited\">".__('The code is not updated', 'sendpulse-webpush')."</p>";
		  }else{
			  update_option( 'sendpulse_code', $newhtml );
			  $html = $newhtml;
			  printf("<p class=\"succes-edited\">".__("Succesfully edited %s!", 'sendpulse-webpush')."</p>", '');
		  }
	  }     
  }
  
  $sendpulse_active = get_option( 'sendpulse_active', 'N' );
  $sendpulse_addinfo = get_option( 'sendpulse_addinfo', 'N' );
  ?><form method="post" action="<?php echo $currenturl; ?>"><?php
  //if(current_user_can('add-to-head')|| current_user_can('manage_options')):
  if(isset($html)){
  	?><textarea style="white-space:pre; width:80%; min-width:600px; height:300px;" name="html"><?php echo $html; ?></textarea><?php
  }
  ?><br />
  <h3><?php _e('You need to <a target="_blank" href="https://sendpulse.com/webpush?utm_source=wordpress">create a free account</a> to get the web push integration code and send web push notifications.', 'sendpulse-webpush');?></h3>
  <table>
  	<?php
    $post_types = get_post_types('', 'names');
    ?>
    <tr><td><input type="checkbox" name="sendpulse_addinfo" value="Y" <?php if($sendpulse_addinfo == 'Y'){ echo ' checked="checked"';} ?> /></td><td><?php _e('Pass emails and usernames of Wordpress users for personalization.', 'sendpulse-webpush');?></td></tr>
  </table>
  <p><?php _e('Note: this event is triggered only when a new user signs up' , 'sendpulse-webpush'); ?></p>
  <?php
  submit_button();
  //endif;
  echo "</form></div>";
}
?>