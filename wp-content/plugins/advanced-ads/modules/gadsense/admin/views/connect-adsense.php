<?php
/**
 * HTML markup for AdSense connection modal frame.
 *
 * @package   Advanced_Ads_Admin
 */

$data_obj = Advanced_Ads_AdSense_Data::get_instance();
$options  = $data_obj->get_options();

$nonce = wp_create_nonce( 'advads-mapi' );

$CID = Advanced_Ads_AdSense_MAPI::CID;

$use_user_app = Advanced_Ads_AdSense_MAPI::use_user_app();
if ( $use_user_app ) {
	$CID = ADVANCED_ADS_MAPI_CID;
}

$connection_error_messages = Advanced_Ads_AdSense_MAPI::get_connect_error_messages();

$auth_url = 'https://accounts.google.com/o/oauth2/v2/auth?scope=' .
			urlencode( 'https://www.googleapis.com/auth/adsense.readonly' ) .
			'&client_id=' . $CID .
			'&redirect_uri=' . urlencode( 'urn:ietf:wg:oauth:2.0:oob' ) .
			'&access_type=offline&include_granted_scopes=true&prompt=select_account&response_type=code';

?>
<div id="gadsense-modal">
	<div id="gadsense-modal-outer">
		<div id="gadsense-modal-inner">
			<div id="gadsense-modal-content">
				<div class="gadsense-modal-content-inner" data-content="confirm-code">
					<i class="dashicons dashicons-dismiss"></i>
					<label style="font-size:1.1em;font-weight:600;margin-bottom:.3em;display:block;"><?php _e( 'Please enter the confirmation code.', 'advanced-ads' ); ?></label>
					<input type="text" class="widefat" id="mapi-code" value="" />
					<p><label><input type="checkbox" value="1" id="mapi-autoads"<?php echo ( $options['page-level-enabled'] ) ? ' checked="checked"' : ''; ?> />&nbsp;<?php _e( 'Insert the AdSense header code used for verification and the Auto Ads feature.', 'advanced-ads' ); ?></label></p>
					<p class="submit">
						<button id="mapi-confirm-code" class="button-primary preventDefault"><?php _e( 'Submit code', 'advanced-ads' ); ?></button>
					</p>
					<div class="gadsense-overlay">
						<img alt="..." src="<?php echo ADVADS_BASE_URL . 'admin/assets/img/loader.gif'; ?>" style="margin-top:3em" />
					</div>
				</div>
				<div class="gadsense-modal-content-inner" data-content="error" style="display:none;">
					<i class="dashicons dashicons-dismiss"></i>
					<h3><?php esc_html_e( 'Cannot access your account information.', 'advanced-ads' ); ?></h3>
					<p class="error-message" style="font-size:1.15em;background-color:#e4e4e4;padding:.3em .8em;"></p>
					<p class="error-description" style="font-size:1.1em;"></p>
				</div>
				<div class="gadsense-modal-content-inner" data-content="account-selector" style="display:none;">
					<i class="dashicons dashicons-dismiss"></i>
					<h3><?php esc_html_e( 'Please select an account', 'advanced-ads' ); ?></h3>
					<p>
						<select id="mapi-select-account">
						</select>
					</p>
					<p><button class="button-primary"><?php esc_html_e( 'Use this account', 'advanced-ads' ); ?></button></p>
					<input type="hidden" class="token-data" value="" />
					<input type="hidden" class="accounts-details" value="" />
					<div class="gadsense-overlay">
						<img alt="..." src="<?php echo ADVADS_BASE_URL . 'admin/assets/img/loader.gif'; ?>" style="margin-top:3em" />
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	if ( 'undefined' == typeof window.AdsenseMAPI ) {
		AdsenseMAPI = {};
	}
	AdsenseMAPI.nonce = '<?php echo wp_strip_all_tags( $nonce ); ?>';
	AdsenseMAPI.oAuth2 = '<?php echo wp_strip_all_tags( $auth_url ); ?>';
    AdsenseMAPI.connectErrorMsg = <?php echo wp_json_encode( $connection_error_messages ); ?>;
</script>
<style type="text/css">
.gadsense-overlay {
	display:none;
	background-color:rgba(255,255,255,.5);
	position:absolute;
	width: 100%;
	height: 100%;
	top: 0;
	left: 0;
	text-align:center;
}
#gadsense-modal {
	display: none;
	background-color: rgba(0,0,0,.5);
	position:fixed;
	top:0;
	left:0;
	right:0;
	bottom:0;
}
#gadsense-modal-outer {
	position: relative;
	width: 60%;
	height: 100%;
    <?php if ( is_rtl() ) : ?>
	margin-right: 20%;
    <?php else : ?>
	margin-left: 20%;
    <?php endif; ?>
}
#gadsense-modal-inner {
	display: table;
	width: 100%;
	height: 100%;
}
#gadsense-modal-content {
	display: table-cell;
	vertical-align: middle;
}
.gadsense-modal-content-inner {
	padding: 1em;
	background-color: #f0f0f0;
	position: relative;
	border: 3px solid #808b94;
}
.gadsense-modal-content-inner .dashicons-dismiss {
	background-color: #fff;
	border-radius: 100%;
	cursor: pointer;
	top: -.5em;
    <?php if ( is_rtl() ) : ?>
    left: -.5em;
    <?php else : ?>
	right: -.5em;
    <?php endif; ?>
	position: absolute;
	z-index: 2;
}
</style>
