<?php
/**
 * Customer minimum order notification email
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<?php echo wpautop( wptexturize( $body_content ) ) ?>
<?php

$aweek = strtotime("-1 week");
if($posts){
?>
	
    <table cellspacing="0" cellpadding="0" border="0" width="100%">
    	<tr>
        	<td>
            	<?php
				if( has_post_thumbnail( $posts[0]['ID'] ) ) {
					echo '<img width="100%" style="max-width:100%;height:auto" src="' . get_the_post_thumbnail_url( $posts[0]['ID'], 'full' ) . '" />';
				}
				$post_create_date = strtotime(get_the_date('Y-m-d H:i:s', $posts[0]['ID']));
				$badge = '';
				if($post_create_date>=$aweek){
					$badge = '<img width="35" src="'.get_stylesheet_directory_uri() . '/assets/img/sale-badge.png" style="vertical-align:middle" alt="" /> ';
				}
				?>
            	<h3><?=$badge?> <?=$posts[0]['post_title']?></h3>
                <p><?php echo trimCharacters(esc_attr( wp_strip_all_tags( $posts[0]['post_content'] ) ),150) ?></p>
                <p><a href="<?=get_permalink($posts[0]['ID'])?>">Read more</a></p>
			</td>
		</tr>
	</table>
<?php
	if(isset($posts[1])){
?>   
	
    <table cellspacing="0" cellpadding="0" border="0" width="100%">
    	<tr>
        	<td width="40%">
            	<?php
				if( has_post_thumbnail( $posts[1]['ID'] ) ) {
					echo '<img width="220" style="max-width:100%;height:auto" src="' . get_the_post_thumbnail_url( $posts[1]['ID'], 'large' ) . '" />';
				}
				$post_create_date = strtotime(get_the_date('Y-m-d H:i:s', $posts[1]['ID']));
				$badge = '';
				if($post_create_date>=$aweek){
					$badge = '<img width="35" src="'.get_stylesheet_directory_uri() . '/assets/img/sale-badge.png" style="vertical-align:middle" alt="" /> ';
				}
				?></td>
			<td width="2%">&nbsp;</td>
			<td width="58%"><h3><?=$badge?> <?=$posts[1]['post_title']?></h3>
                <p><?php echo trimCharacters(esc_attr( wp_strip_all_tags( $posts[1]['post_content'] ) ),150) ?></p>
                <p><a href="<?=get_permalink($posts[1]['ID'])?>">Read more</a></p>
			</td>
		</tr>
	</table>
	 
<?php
	}
	
	if(isset($posts[2])){
				$post_create_date = strtotime(get_the_date('Y-m-d H:i:s', $posts[2]['ID']));
				$badge = '';
				if($post_create_date>=$aweek){
					$badge = '<img width="35" src="'.get_stylesheet_directory_uri() . '/assets/img/sale-badge.png" style="vertical-align:middle" alt="" /> ';
				}
?>   
	
    <table cellspacing="0" cellpadding="0" border="0" width="100%">
    	<tr>
			<td width="58%"><h3><?=$badge?> <?=$posts[2]['post_title']?></h3>
                <p><?php echo trimCharacters(esc_attr( wp_strip_all_tags( $posts[2]['post_content'] ) ),150) ?></p>
                <p><a href="<?=get_permalink($posts[2]['ID'])?>">Read more</a></p>
			</td>
			<td width="2%">&nbsp;</td>
        	<td width="40%">
            	<?php
				if( has_post_thumbnail( $posts[2]['ID'] ) ) {
					echo '<img width="220" style="max-width:100%;height:auto" src="' . get_the_post_thumbnail_url( $posts[2]['ID'], 'large' ) . '" />';
				}
				?></td>
		</tr>
	</table>
	 
<?php
	}
}
/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );
