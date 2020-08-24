<?php
// this file contains the contents of the popup window
require_once('get_wp.php');
$pt_icons = array(
            "twitter" => 'Twitter',
            "wordpress" => 'WordPress',
            "facebook" => 'Facebook',
            "linkedin" => 'LinkedIn',
            "steam" => 'Steam',
            "tumblr" => 'Tumblr',
            "github" => 'GitHub',
            "delicious" => 'Delicious',
            "instagram" => 'Instagram',
            "xing" => 'Xing',
            "amazon" => 'Amazon',
            "dropbox" => 'Dropbox',
            "paypal" => 'PayPal',
            "lastfm" => 'LastFM',
            "gplus" => 'Google+',
            "yahoo" => 'Yahoo',
            "pinterest" => 'Pinterest',
            "dribbble" => 'Dribbble',
            "flickr" => 'Flickr',
            "reddit" => 'Reddit',
            "vimeo" => 'Vimeo',
            "spotify" => 'Spotify',
            "rss" => 'RSS',
            'youtube' => 'YouTube',
            'blogger' => 'Blogger',
            'appstore' => 'AppStore',
            'digg' => 'Digg',
            'evernote' => 'Evernote',
            'fivehundredpx' => '500px',
            'forrst' => 'Forrst',
            'stumbleupon' => 'StumbleUpon',
        );
?>

<style type="text/css">
#TB_window a:active,
#TB_window a:visited,
#TB_window a:link {
    color: #Fff;
}
</style>

<div id="purethemes-popup">

	<form id="purethemes-popup-form" action="">
		<div id="form-container-ajax">
			<table class="form-table">
                <tr>
                    <td>
                        <div class="widget-content ptwsi">
                            <p id="selector">
                                <label><?php _e('Choose service:', 'purepress'); ?></label>
                                    <select class="widefat icontype notloaded" id="" name="">
                                        <option value="">-</option>
                                    <?php foreach ($pt_icons as $icon => $service) { ?>
                                        <option value="<?php echo $icon; ?>"><?php echo $service; ?></option>
                                    <?php } ?>
                                    </select>
                            </p>
                            <div id="socialicons">    </div>
                            <p>
                                <small><strong>Hint</strong> You can sort icons by drag&drop, and delete them by dragging element outside the widget!</small>
                            </p>
                            <p>
                                <label for="#">Select icon size</label>
                                <select name="target" id="target" >
                                     <option value="_self" >_self</option>
                                     <option value="_blank" selected="selected">_blank</option>
                                     <option value="_parent" >_parent</option>
                                     <option value="_top" >_top</option>
                                </select>
                            </p>
                            <p>
                                <label for="#">Select icon size</label>
                                <select name="iconsize" id="iconsize" >
                                    <option value="" selected="selected">Standard</option>
                                    <option value="small">Small</option>

                                </select>
                            </p>
                        </div>
                    </td>
                </tr>
			</table>
			<table class="form-table-footer">
				<tr class="form-row">
					<td class="field"><a href="#" style="margin-left:10px" class="button button-primary button-large ptsc-insert">Insert Shortcode</a></td>
				</tr>
			</table>

		</div>
	</form>
	<script>
	jQuery(document).ready(function ($) {
		var t=setTimeout(function(){
    		var tbAjax = $('#TB_ajaxContent'),
                tbWindow = $('#TB_window');
            tbWindow.css({
                height: 350,
                width: 500
            });
            tbAjax.css({
                paddingTop: 0,
                paddingLeft: 0,
                paddingRight: 0,
                height: (tbWindow.outerHeight() - 47),
                overflow: 'auto', // IMPORTANT
                width: tbWindow.outerWidth()-2
            });
        },300)
	});

	</script>
</div>