<?php
$options = Advanced_Ads_Pro::get_instance()->get_options();
$module_enabled = isset( $options['cache-busting']['enabled'] ) && $options['cache-busting']['enabled'];
$method = ( $module_enabled &&
	( ! isset( $options['cache-busting']['default_auto_method'] ) || $options['cache-busting']['default_auto_method'] === 'ajax' ) 
) ? 'ajax' : 'passive';
$fallback_method = ! empty( $options['cache-busting']['default_fallback_method'] ) ? $options['cache-busting']['default_fallback_method'] : 'ajax';
$passive_all = ! empty( $options['cache-busting']['passive_all'] );
?>
<input name="<?php echo Advanced_Ads_Pro::OPTION_KEY; ?>[cache-busting][enabled]" id="advanced-ads-pro-cache-busting-enabled" type="checkbox" value="1" <?php checked( $module_enabled ); ?> />
<label for="advanced-ads-pro-cache-busting-enabled" class="description"><?php _e( 'Activate <em>cache busting</em> module.', 'advanced-ads-pro' ); ?></label>

<div style="display: <?php echo $module_enabled ? 'block' : 'none'; ?>;">

	<p class="description"><?php _e( 'Choose which method to use when cache-busting is set to “auto”.', 'advanced-ads-pro' ); ?></p>
	<label>
		<input name="<?php echo Advanced_Ads_Pro::OPTION_KEY; ?>[cache-busting][default_auto_method]" type="radio" value="passive" <?php
		checked( $method, 'passive' ); ?>/><?php _e( 'passive', 'advanced-ads-pro' ); ?>
	</label><br/>
	<label>
		<input name="<?php echo Advanced_Ads_Pro::OPTION_KEY; ?>[cache-busting][default_auto_method]" type="radio" value="ajax" <?php
		checked( $method, 'ajax' ); ?>/><?php _e( 'AJAX', 'advanced-ads-pro' ); ?>
	</label>

	<p class="description"><?php _e( 'Choose the fallback if “passive“ cache-busting is not possible.', 'advanced-ads-pro' ); ?></p>
	<label>
		<input name="<?php echo Advanced_Ads_Pro::OPTION_KEY; ?>[cache-busting][default_fallback_method]" type="radio" value="ajax" <?php
		checked( $fallback_method, 'ajax' ); ?>/><?php _e( 'Use AJAX', 'advanced-ads-pro' ); ?>
	</label><br/>
	<label>
		<input name="<?php echo Advanced_Ads_Pro::OPTION_KEY; ?>[cache-busting][default_fallback_method]" type="radio" value="disable" <?php
		checked( $fallback_method, 'disable' ); ?>/><?php _e( 'No cache-busting', 'advanced-ads-pro' ); ?>
	</label><br /><br />
	<label>
		<input name="<?php echo Advanced_Ads_Pro::OPTION_KEY; ?>[cache-busting][passive_all]" type="checkbox" value="1" <?php 
		checked( $passive_all, 1 ); ?> />
		<?php _e( 'Enable passive cache-busting for all ads and groups which are not delivered through a placement, if possible.', 'advanced-ads-pro' ); ?>
	</label>
</div>
<!--
<br/><p><?php printf(__( 'Please note that cache-busting only works through <a href="%s">placements</a>.', 'advanced-ads-pro' ), admin_url('admin.php?page=advanced-ads-placements') ); ?></p>-->