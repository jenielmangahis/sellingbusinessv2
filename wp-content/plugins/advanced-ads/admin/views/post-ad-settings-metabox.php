<p><a href="<?php echo ADVADS_URL ?>how-to-block-ads-on-a-specific-page/#utm_source=advanced-ads&utm_medium=link&utm_campaign=disable-ads-on-specific-pages" target="_blank"><?php _e( 'How to disable ads on specific pages', 'advanced-ads'); ?></a></p>
<label><input type="checkbox" name="advanced_ads[disable_ads]" value="1" <?php
if ( isset( $values['disable_ads'] ) ) {
	checked( $values['disable_ads'], true ); }
?>/><?php _e( 'Disable ads on this page', 'advanced-ads' ); ?></label>
