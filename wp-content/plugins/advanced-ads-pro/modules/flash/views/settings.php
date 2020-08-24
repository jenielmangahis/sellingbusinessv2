<?php
$options = Advanced_Ads_Pro::get_instance()->get_options();
$check = isset($options['flash']['enabled']) && $options['flash']['enabled'];
?>
<input name="<?php echo Advanced_Ads_Pro::OPTION_KEY; ?>[flash][enabled]" id="advanced-ads-pro-flash-enabled" type="checkbox" value="1" <?php checked( $check ); ?> />
<label for="advanced-ads-pro-flash-enabled" class="description"><?php _e('Activate <em>flash</em> module.', 'advanced-ads-pro'); ?></label>
