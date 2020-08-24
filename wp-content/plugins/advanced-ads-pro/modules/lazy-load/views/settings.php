<?php
$options = Advanced_Ads_Pro::get_instance()->get_options();
$module_enabled = isset( $options['lazy-load']['enabled'] ) && $options['lazy-load']['enabled'];
$offset = ! empty( $options['lazy-load']['offset'] ) ? absint( $options['lazy-load']['offset'] ) : 0;
?>
<input name="<?php echo Advanced_Ads_Pro::OPTION_KEY; ?>[lazy-load][enabled]" id="advanced-ads-pro-lazy-load-enabled" type="checkbox" value="1" <?php checked( $module_enabled ); ?> />
<label for="advanced-ads-pro-lazy-load-enabled" class="description"><?php _e( 'Activate <em>lazy load</em> module.', 'advanced-ads-pro' ); ?></label>

<div style="display: <?php echo $module_enabled ? 'block' : 'none'; ?>;">
<br />
<label>
<?php $field = '<input name="' . Advanced_Ads_Pro::OPTION_KEY .'[lazy-load][offset]" type="number" min="0" max="99999" value="' . $offset . '" />';
printf(__( 'Start loading the ads %s pixels before they are visible on the screen.', 'advanced-ads-pro' ), $field ); ?>
 </label>
</div>
