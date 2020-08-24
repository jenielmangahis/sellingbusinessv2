<label><input type="checkbox" name="<?php echo $option_name; ?>[convert]" value="1" <?php checked( $convert ); ?>/><?php
printf( __( 'Convert AdSense ads automatically into an AMP ad with a ratio of %s x %s', 'advanced-ads-responsive' ),
	'<input type="number" min="1" max="99999" name="' . $option_name . '[width]" value="' . $width . '"/>',
	'<input type="number" min="1" max="99999" name="' . $option_name . '[height]" value="' . $height . '"/>'
	); ?></label>
<a class="advads-manual-icon" href="<?php echo ADVADS_URL . 'manual/amp-adsense-wordpress/#utm_source=advanced-ads&utm_medium=link&utm_campaign=settings-adsense-amp'; ?>" target="_blank">
    <span class="dashicons dashicons-welcome-learn-more"></span>
</a>

<br />
<label><input type="checkbox" name="<?php echo $option_name; ?>[auto_ads_enabled]" value="1" <?php checked( $auto_ads_enabled ); ?>/><?php
	_e( 'Enable AMP Auto ads', 'advanced-ads-responsive' ); ?>
</label>
