<?php
if ( ! defined( 'WPINC' ) || ! ( $order_id ) ) {
    die;
}
// $ad = new Advanced_Ads_Ad( $ad_id );
// $ad_options = $ad->options();
?><!DOCTYPE html>
<html <?php language_attributes(); ?>><head>
    <meta charset="<?php echo get_option( 'blog_charset' ); ?>">
    <title><?php echo bloginfo( 'name' ); ?> | <?php _e( 'Ad Setup', 'advanced-ads-selling' ); ?></title>
    <meta name="robots" content="noindex, nofollow" />
    <script type="text/javascript" src="<?php echo includes_url( '/js/jquery/jquery.js' ); ?>"></script>
    <script type="text/javascript" src="<?php echo AASA_BASE_URL . 'public/assets/js/ad-setup.js'; ?>"></script>
    <link rel="stylesheet" href="<?php echo AASA_BASE_URL . 'public/assets/css/ad-setup.css'; ?>" />
    <script>
	var advads_selling_ajax_url = "<?php echo admin_url('admin-ajax.php'); ?>";
    </script>
</head>
    <body class="advanced-ads-selling-setup-page">
	<h1><?php echo get_bloginfo( 'name' ); ?></h1>
	<?php include( 'ad-setup-form.php' ); ?>
    </body>
</html>