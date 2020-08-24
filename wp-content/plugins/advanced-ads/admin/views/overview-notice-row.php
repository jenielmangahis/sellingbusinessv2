<li data-notice="<?php echo esc_attr( $_notice_key ); ?>" <?php echo $is_hidden ? 'style="display: none;"' : ''; ?>>
    <span><?php echo $text; ?></span>
	<?php if ( $can_hide ) : ?>
        <button type="button" class="advads-ad-health-notice-hide<?php echo ! $hide ? ' remove' : ''; ?>"><span class="dashicons dashicons-hidden"></span></button>
	<?php endif; ?>
	<?php if ( $date ) : ?>
        <span class="date"><?php echo esc_attr( $date ); ?></span>
	<?php endif; ?>
</li>
