<?php
/**
 * Render additional content below the ad edit page.
 *
 * @package   Advanced_Ads_Admin
 * @author    Thomas Maier <thomas.maier@webgilde.com>
 * @license   GPL-2.0+
 * @link      https://wpadvancedads.com
 * @copyright since 2013 Thomas Maier, webgilde GmbH
 *
 */

?><div id="advads-wizard-controls" class="hidden">
    <button type="button" id="advads-wizard-controls-prev" class="button button-secondary button-large"><span class="dashicons dashicons-controls-back"></span>&nbsp;<?php echo esc_attr_x( 'previous', 'wizard navigation', 'advanced-ads' ); ?></button>
	<input type="submit" id="advads-wizard-controls-save" class="button button-primary button-large" value="<?php
	// display different message depending on a new ad (filter = raw) or existing one.
	echo ( isset( $post->filter ) && 'raw' === $post->filter ) ? esc_attr_x( 'next', 'wizard navigation', 'advanced-ads' ) : esc_attr_x( 'save', 'wizard navigation', 'advanced-ads' );
	?>"/>
	<button type="button" id="advads-wizard-controls-next" class="button button-primary button-large"><?php echo esc_attr_x( 'next', 'wizard navigation', 'advanced-ads' ); ?>&nbsp;<span class="dashicons dashicons-controls-forward"></span></button>
	<p><a href="javascript:void(0)" class="advads-stop-wizard"><?php esc_attr_e( 'Stop Wizard and show all options', 'advanced-ads' ); ?></a></p>
</div>
<?php
// start the wizard if it was never closed.
if ( $this->start_wizard_automatically() ) :
	?>
<script>jQuery( document ).ready(function ($) { advads_wizard.start() });</script>
	<?php
endif;

/**
 * Support and review box
 */
if( ! Advanced_Ads_Plugin::any_activated_add_on() ) :
    include ADVADS_BASE_PATH . 'admin/views/support-callout.php';
    ?><script>jQuery( document ).ready(function () { jQuery( '#advads-support-callout').insertAfter( '#ad-main-box' ); });</script>
<?php endif;