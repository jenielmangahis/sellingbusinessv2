<!-- Location -->
<?php 
$fake_location = realteo_get_option('realteo_single_property_fake_location');
$latitude = get_post_meta( $post->ID, '_geolocation_lat', true ); 
$longitude = get_post_meta( $post->ID, '_geolocation_long', true ); 
if($fake_location) {
	$dither= '0.001';
	$latitude = $latitude + (rand(5,15)-0.5)*$dither;
}

if(!empty($latitude)) : 
?>
<h3 class="desc-headline no-border print-no" id="location"><?php esc_html_e('Location','realteo'); ?></h3>
<div id="propertyMap-container" class="print-no <?php if($fake_location) { echo 'circle-point'; } ?>">
	<div id="propertyMap" data-latitude="<?php echo esc_attr($latitude); ?>" data-longitude="<?php echo esc_attr($longitude); ?>"></div>
	<a href="#" id="streetView"><?php esc_html_e('Street View','realteo'); ?></a>
</div>

<?php endif; ?>