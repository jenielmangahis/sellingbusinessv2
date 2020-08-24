<?php
/**
 * CM Ad Changer
 *
 * @author CreativeMinds (http://ad-changer.cminds.com)
 * @copyright Copyright (c) 2013, CreativeMinds
 */
?>

<script type="text/javascript">
    plugin_url = '<?php echo plugins_url( '', __FILE__ ) ?>';
</script>

<div class="wrap ad_changer ac_settings">
    <h2><?php echo $plugin_data[ 'Name' ]; ?> : Settings</h2>
	<?php
	ac_top_menu();
	if ( isset( $errors ) && !empty( $errors ) ) {
		?>
		<ul class="ac_error cmac-clear">
			<?php
			foreach ( $errors as $error )
				echo '<li>' . $error . '</li>';
			?>
		</ul>
		<?php
	}
	if ( isset( $success ) && !empty( $success ) ) {
		echo '<div class="ac_success cmac-clear">' . $success . '</div>';
	}
	?>

    <div class="cmac-clear"></div>

	<?php
	echo do_shortcode( '[cminds_free_ads]' );
	?>

    <div class="acs-shortcode-reference cmac-clear cminds_settings_description">
        <p>To insert the ads container into a page or post use following shortcode: [cm_ad_changer]. <br/>
            To use it outsite of the content use the following code: <code>&lt;?php echo do_shortcode('[cm_ad_changer]'); ?&gt;</code></p>
        <p>Here is the list of parameters: <a href="javascript:void(0)" onclick="jQuery( this ).parent().next().slideToggle()">Show/Hide</a></p>
        <ul style="list-style-type: disc; margin-left: 20px;">
            <li>
                <strong>campaign_id</strong> - ID of a campaign (required*)
            </li>
            <li>
                <strong>group_id</strong> - ID of a campaign group (required*)
            </li>
            <li>
                <strong>linked_banner</strong> - Banner is a linked image or just image. Can be 1 or 0 (default: 1)
            </li>
            <li>
                <strong>debug</strong> - Show the debug info. Can be 1 or 0 (default: 0)
            </li>
            <li>
                <strong>wrapper</strong> - Wrapper On or Off. Wraps banner with  a div tag. Can be 1 or 0 (default: 0)
            </li>
            <li>
                <strong>class</strong> - Banner (div) class name
            </li>
            <li>
                <strong>no_responsive</strong> - Banner not responsive. Can be 1 or 0 (default: 0)
            </li>
            <li>
                <strong>custom_css</strong> - The CSS code which would only be outputted if the banner is shown. (default: empty)
            </li>
            <li>
                <strong>allow_inject_js</strong> - Whether to allow server to inject JS or not. Can be 1 or 0 (default: 0)
            </li>
            <li>
                <strong>allow_inject_html</strong> - Whether to allow server to send the HTML Ads or not. Can be 1 or 0 (default: 0)
            </li>
            <li>
                <strong>width</strong> - Width of the banner image (default: auto)
            </li>
            <li>
                <strong>height</strong> - Height of the banner image (default: auto)
            </li>
            <li style="list-style: none">
                <div class="hint">
                    <i>* - You have to provide either a Group ID or a Campaign ID</i>
                </div>
            </li>
        </ul>
    </div>

    <br/>
    <div class="cmac-clear"></div>

    <div class="ac-edit-form cmac-clear">
        <form id="acs_settings_form" method="post">
            <input type="hidden" name="action" value="acs_settings" />
            <div id="settings_fields" class="cmac-clear">
                <ul>
                    <li><a href="#general_settings_fields">General Settings</a></li>
                    <li><a href="#geolocation_fields">Geolocation</a></li>
                    <li><a href="#rotated_settings">Rotated Banners</a></li>
                    <li><a href="#cutom_css_settings">Custom CSS</a></li>
                    <li><a href="#responsive_settings">Responsive Settings</a></li>
                    <li><a href="#trash_settings">Trash</a></li>
                </ul>
                <table cellspacing=3 cellpadding=0 border=0 id="general_settings_fields">
                    <tr>
                        <td style="width:40%">
                            <label class="ac-form-label" for="acs_active" >Server Active </label><div class="field_help" title="<?php echo $label_descriptions[ 'acs_active' ] ?>"></div>
                        </td>
                        <td>
                            <input type="checkbox" name="acs_active" id="acs_active" value="1" <?php echo ($fields_data[ 'acs_active' ] == '1' ? 'checked=checked' : '') ?> />
                        </td>
                    </tr>
                    <tr>
                        <td style="width:40%">
                            <label class="ac-form-label" for="acs_disable_history_table" >Disable history functionality </label><div class="field_help" title="<?php echo $label_descriptions[ 'acs_disable_history_table' ] ?>"></div>
                        </td>
                        <td>
                            <input type="checkbox" name="acs_disable_history_table" id="acs_disable_history_table" value="1" <?php echo ($fields_data[ 'acs_disable_history_table' ] == '1' ? 'checked=checked' : '') ?> />
                        </td>
                    </tr>
                    <tr>
                        <td valign=top>
                            <label class="ac-form-label" for="acs_notification_email_tpl" >Notification Email Template </label><div class="field_help" title="<?php echo $label_descriptions[ 'acs_notification_email_tpl' ] ?>"></div>
                        </td>
                        <td>
                            <textarea name="acs_notification_email_tpl" id="acs_notification_email_tpl" rows="10" value="<?php echo $fields_data[ 'acs_notification_email_tpl' ] ?>"><?php echo $fields_data[ 'acs_notification_email_tpl' ] ?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label class="ac-form-label" for="acs_inject_scripts" >Inject JS libraries on ALL pages</label><div class="field_help" title="<?php echo $label_descriptions[ 'acs_inject_scripts' ] ?>"></div>
                        </td>
                        <td>
                            <input type="checkbox" name="acs_inject_scripts" id="acs_inject_scripts"  value="1" <?php echo ($fields_data[ 'acs_inject_scripts' ] == '1' ? 'checked=checked' : '') ?> />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label class="ac-form-label" for="acs_script_in_footer" >Inject JS files in footer</label>
                            <div class="field_help" title="<?php echo $label_descriptions[ 'acs_script_in_footer' ] ?>"></div>
                        </td>
                        <td>
                            <input type="checkbox" name="acs_script_in_footer" id="acs_script_in_footer"  value="1" <?php checked( '1', $fields_data[ 'acs_script_in_footer' ] ); ?> />
                        </td>
                    </tr>
					<tr>
                        <td style="width:40%">
                            <label class="ac-form-label" for="acs_auto_deactivate_campaigns" >Auto-deactivate old campaigns </label><div class="field_help" title="<?php echo $label_descriptions[ 'acs_auto_deactivate_campaigns' ] ?>"></div>
                        </td>
                        <td>
                            <input type="checkbox" name="acs_auto_deactivate_campaigns" id="acs_auto_deactivate_campaigns" value="1" <?php echo ($fields_data[ 'acs_auto_deactivate_campaigns' ] == '1' ? 'checked=checked' : '') ?> />
                        </td>
                    </tr>
                </table>

                <table cellspacing=3 cellpadding=0 border=0 id="geolocation_fields">
                    <tr>
                        <td>
                            <label class="ac-form-label" for="acs_geolocation_api_key" >Geolocation API Key </label><div class="field_help" title="<?php echo $label_descriptions[ 'acs_geolocation_api_key' ] ?>"></div>
                        </td>
                        <td>
                            <input type="text" name="acs_geolocation_api_key" id="acs_geolocation_api_key" value="<?php echo $fields_data[ 'acs_geolocation_api_key' ] ?>" style="width:300px" /><br>
                            <p>To receive API Key register at <a href="http://ipinfodb.com/register.php" target="new">IPinfodb</a></p>
                        </td>
                    </tr>
                </table>

                <table id="rotated_settings">
                    <tr>
                        <td style="width:40%">
                            <label class="ac-form-label" for="acs_slideshow_effect" >Rotated Banner switch effect </label>
                            <div class="field_help" title="<?php echo $label_descriptions[ 'acs_slideshow_effect' ] ?>"></div>
                        </td>
                        <td>
                            <select id="acs_slideshow_effect" name="acs_slideshow_effect">
                                <option value="fade" <?php echo!isset( $fields_data[ 'acs_slideshow_effect' ] ) || $fields_data[ 'acs_slideshow_effect' ] == 'fade' ? 'selected=selected' : '' ?>>Fade</option>
                                <option value="slide" <?php echo isset( $fields_data[ 'acs_slideshow_effect' ] ) && $fields_data[ 'acs_slideshow_effect' ] == 'slide' ? 'selected=selected' : '' ?>>Slide</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label class="ac-form-label" for="acs_slideshow_interval" >Rotated Banner switch interval </label>
                            <div class="field_help" title="<?php echo $label_descriptions[ 'acs_slideshow_interval' ] ?>"></div>
                        </td>
                        <td>
                            <input type="text" name="acs_slideshow_interval" id="acs_slideshow_interval" value="<?php echo $fields_data[ 'acs_slideshow_interval' ] ?>" style="width:50px" />
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <label class="ac-form-label" for="acs_slideshow_transition_time" >Rotated Banner Transition time </label>
                            <div class="field_help" title="<?php echo $label_descriptions[ 'acs_slideshow_transition_time' ] ?>"></div>
                        </td>
                        <td>
                            <input type="text" name="acs_slideshow_transition_time" id="acs_slideshow_transition_time" value="<?php echo $fields_data[ 'acs_slideshow_transition_time' ] ?>" style="width:50px" />
                        </td>
                    </tr>

                </table>
                <table id="cutom_css_settings">
                    <tr>
                        <td valign=top>
                            <label class="ac-form-label" for="acs_custom_css" >Custom CSS </label><div class="field_help" title="<?php echo $label_descriptions[ 'acs_custom_css' ] ?>"></div>
                        </td>
                        <td>
                            <textarea id="acs_custom_css" name="acs_custom_css" rows=7 cols=40><?php echo esc_html( stripslashes( $fields_data[ 'acs_custom_css' ] ) ) ?></textarea>
                        </td>
                    </tr>
                </table>
                <table id="responsive_settings">
                    <tr><td colspan=2><p>Using banner variations means it will take more time to show ad on client side since it is called only after calculating possible size</p><br></td></tr>

                    <tr>
                        <td valign="top">
                            <label class="ac-form-label" for="acs_use_banner_variations" >Use Banner Variations </label><div class="field_help" title="<?php echo $label_descriptions[ 'acs_use_banner_variations' ] ?>"></div>
                        </td>
                        <td>
                            <input type="radio" value="1" name="acs_use_banner_variations" id="acs_use_banner_variations" <?php echo!isset( $fields_data[ 'acs_use_banner_variations' ] ) || $fields_data[ 'acs_use_banner_variations' ] == '1' ? 'checked=checked' : '' ?> />&nbsp;<label for="acs_use_banner_variations" >Yes</label><br/>
                            <input type="radio" value="0" name="acs_use_banner_variations" id="acs_not_use_banner_variations" <?php echo isset( $fields_data[ 'acs_use_banner_variations' ] ) && $fields_data[ 'acs_use_banner_variations' ] == '0' ? 'checked=checked' : '' ?> />&nbsp;<label for="acs_not_use_banner_variations" >No</label>
                            <div id="variations_settings">
                                <label class="ac-form-label" >Choose variation based on width of </label><div class="field_help" title="<?php echo $label_descriptions[ 'acs_banner_area' ] ?>"></div><br/>
                                <input type="radio" value="screen" name="acs_banner_area" id="screen" <?php echo!isset( $fields_data[ 'acs_banner_area' ] ) || $fields_data[ 'acs_banner_area' ] == 'screen' ? 'checked=checked' : '' ?> />&nbsp;<label for="screen" >Screen Size Only</label><br/>
                                <input type="radio" value="container" name="acs_banner_area" id="container" <?php echo isset( $fields_data[ 'acs_banner_area' ] ) && $fields_data[ 'acs_banner_area' ] == 'container' ? 'checked=checked' : '' ?> />&nbsp;<label for="container" >Container Size and Screen Size (the smallest)</label><br/><br/>
                                <label class="ac-form-label" >If no variations are available resize banner </label><div class="field_help" title="<?php echo $label_descriptions[ 'acs_resize_banner' ] ?>"></div>
                                <input type="checkbox" value="1" name="acs_resize_banner" id="screen" <?php echo isset( $fields_data[ 'acs_resize_banner' ] ) && $fields_data[ 'acs_resize_banner' ] == '1' ? 'checked=checked' : '' ?> />

                            </div>
                        </td>
                    </tr>
                </table>
				<table id="trash_settings">
					<tr>
						<td colspan=2><p>Here you able to remove statistics using following options</p><br></td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><p class="stat_delete_msg"></p></td>
					</tr>
					<tr>
						<td style="width:40%">
                            <label class="ac-form-label" for="acs_slideshow_effect" >History Remove From </label>
                        </td>
                        <td>
                            <input name="stat_start_date" id="stat_start_date" value="<?php echo date('Y-m-d'); ?>" readonly />
							To
							<input name="stat_end_date" id="stat_end_date" value="<?php echo date('Y-m-d'); ?>" readonly />
                        </td>
                    </tr>
					<tr>
						<td style="width:40%">
                            &nbsp;
                        </td>
                        <td>
                            <input type="button" value="Delete" class="stat_delete" ajaxurl="<?php echo get_bloginfo('wpurl'); ?>/wp-admin/admin-ajax.php?action=acs_empty_history_by_date" />
                        </td>
                    </tr>
					<tr>
						<td colspan=2>
							<br>
						</td>
					</tr>
				</table>
            </div>
            <input type="submit" value="Store Settings" id="submit_button">
			<?php wp_nonce_field( 'CM_ADCHANGER_GENERAL_SETTINGS', 'general_settings_noncename' ); ?>
        </form>
    </div>
    <script type="text/javascript">
        jQuery( document ).ready( function () {
			jQuery("#stat_start_date").datepicker({
				dateFormat: "yy-mm-dd",
				onSelect: function () {
					var dt2 = jQuery('#stat_end_date');
					var startDate = jQuery(this).datepicker('getDate');
					startDate.setDate(startDate.getDate() + 30);
					var minDate = jQuery(this).datepicker('getDate');
					var dt2Date = dt2.datepicker('getDate');
					var dateDiff = (dt2Date - minDate)/(86400 * 1000);
					if (dt2Date == null || dateDiff < 0) {
						dt2.datepicker('setDate', minDate);
					}
					else if (dateDiff > 30){
						dt2.datepicker('setDate', startDate);
					}
					dt2.datepicker('option', 'maxDate', startDate);
					dt2.datepicker('option', 'minDate', minDate);
				}
			});
			jQuery('#stat_end_date').datepicker({
				dateFormat: "yy-mm-dd"
			});
			jQuery( '.stat_delete' ).click( function () {
				jQuery( '.stat_delete_msg' ).html('');
				var deleteconfirm = confirm("Are You Sure?");
				if (deleteconfirm == true)
				{
					jQuery.ajax({
						'url': jQuery(this).attr('ajaxurl'),
						'type': 'post',
						'data': { start_date : jQuery('#stat_start_date').val(), end_date : jQuery('#stat_end_date').val() },
					}).done(function(response) {
						jQuery( '.stat_delete_msg' ).html(response);
					});
				}
			});
            jQuery( '#acs_max_campaigns_no' ).spinner({
                max: 50,
                min: 0
            });
            jQuery( '#acs_div_wrapper' ).click( function () {

                if ( jQuery( this ).attr( 'checked' ) == 'checked' ) {
                    jQuery( '.custom_style' ).css( 'display', 'inline' );
                } else
                    jQuery( '.custom_style' ).hide();
            } )

            if ( jQuery( '#acs_div_wrapper' ).attr( 'checked' ) == 'checked' )
                jQuery( '.custom_style' ).css( 'display', 'inline' );

            jQuery( '.field_help' ).tooltip( {
                show: {
                    effect: "slideDown",
                    delay: 100
                },
                position: {
                    my: "left top",
                    at: "right top"
                }
            } )

            jQuery( '#settings_fields' ).tabs();


            jQuery( 'input[name="acs_use_banner_variations"]' ).click( function () {
                if ( jQuery( '#acs_use_banner_variations' ).attr( 'checked' ) == 'checked' )
                    jQuery( '#variations_settings' ).show();
                else
                    jQuery( '#variations_settings' ).hide();
            } )

            if ( jQuery( 'input[name="acs_use_banner_variations"][checked="checked"]' ).val() == '1' )
                jQuery( '#variations_settings' ).show();
        } )
    </script>
</div>
<style>
.stat_delete { cursor:pointer; }
</style>