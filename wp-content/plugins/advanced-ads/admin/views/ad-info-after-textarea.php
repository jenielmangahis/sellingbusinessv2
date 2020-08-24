<?php
/**
 * Render additional information below the text area on the ad edit page
 * currently "plain text" and "rich content" ad types
 *
 * @package   Advanced_Ads_Admin
 * @author    Thomas Maier <thomas.maier@webgilde.com>
 * @license   GPL-2.0+
 * @link      https://wpadvancedads.com
 * @copyright since 2013 Thomas Maier, webgilde GmbH
 *
 */

if ( defined( 'WP_DEBUG' ) && WP_DEBUG &&
	( $error = Advanced_Ads_Admin_Ad_Type::check_ad_dom_is_not_valid( $ad ) ) ) : ?>
	<p class="advads-error-message">
		<?php
		_e( 'The code of this ad might not work properly with the <em>Content</em> placement.', 'advanced-ads' );
		?>
		&nbsp;
		<?php
		/*
		 * translators: %s is a URL
		 */
		printf( __( 'Reach out to <a href="%s">support</a> to get help.', 'advanced-ads' ), admin_url( 'admin.php?page=advanced-ads-settings#top#support' ) );
		if ( true === WP_DEBUG ) :
			?>
			<span style="white-space:pre-wrap"><?php echo $error; ?></span>
			<?php
		endif;
		?>
	</p>
<?php endif;

do_action( 'advanced-ads-ad-params-below-textarea', $ad );
