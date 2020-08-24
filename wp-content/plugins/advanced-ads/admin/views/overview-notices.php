<h3<?php echo ! $has_problems ? ' style="display:none;"' : ''; ?>><?php
	esc_attr_e( 'Problems', 'advanceda-ads' ); ?></h3>
<?php
Advanced_Ads_Ad_Health_Notices::get_instance()->display_problems();

?><h3<?php echo ! $has_notices ? ' style="display:none;"' : ''; ?>><?php
	esc_attr_e( 'Notifications', 'advanceda-ads' );
	?>
</h3>
<?php
Advanced_Ads_Ad_Health_Notices::get_instance()->display_notices();

?>
<p class="adsvads-ad-health-notices-show-hidden" <?php echo ! $ignored_count ? 'style="display: none;"' : ''; ?>><?php
	printf(
// translators: %s includes a number and markup like <span class="count">6</span>.
		__( 'Show %s hidden', 'advanced-ads' ), '<span class="count">' . $ignored_count . '</span>' );
	?>&nbsp;
    <button type="button"><span class="dashicons dashicons-visibility"></span></button>
</p>
<?php

if ( Advanced_Ads_Ad_Health_Notices::has_visible_problems() ) {
	include ADVADS_BASE_PATH . 'admin/views/support-callout.php';
}