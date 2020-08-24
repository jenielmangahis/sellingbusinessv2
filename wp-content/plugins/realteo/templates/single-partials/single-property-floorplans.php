<!-- Floorplans -->
<?php 
$floorplans = get_post_meta( $post->ID, '_floorplans', true ); 
if($floorplans) : ?>
<h3 class="desc-headline no-border"><?php esc_html_e('Floorplans','realteo'); ?></h3>
<!-- Accordion -->
<div class="style-1 fp-accordion">

	<div class="accordion">
		<?php foreach ( (array) $floorplans as $key => $plan ) { 
			if ( !isset( $plan['floorplan_title']) || empty($plan['floorplan_title']) )  {
				continue;
			}
			$scale = realteo_get_option( 'scale', 'sq ft' );
			if ( isset( $plan['floorplan_desc'] ) ) {
				$desc = wpautop( $plan['floorplan_desc'] );
			}
			if ( isset( $plan['floorplan_image_id'] ) ) {
				$img = wp_get_attachment_image( $plan['floorplan_image_id'], 'full', null, array(
					'class' => 'floorplan-image',
				) );
				$img_url = wp_get_attachment_image_src( $plan['floorplan_image_id'], 'full', null );
			}
		?>
		<h3><?php echo esc_html( $plan['floorplan_title'] ); ?><?php if ( isset( $plan['floorplan_area'] ) ) { ?>
				<span><?php echo esc_html( $plan['floorplan_area'] ); ?> <?php echo apply_filters('realteo_scale',$scale); ?></span> 
			<?php } ?><i class="fa fa-angle-down"></i>
		</h3>
		<div>
			<?php if ( isset( $plan['floorplan_image_id'] ) ) { ?>
			<a class="floor-pic mfp-image" href="<?php echo esc_url($img_url[0]); ?>">
				<?php echo $img; ?>
			</a>
			<?php } ?>
			<?php echo $desc; ?>
		</div>

		<?php } ?>

	</div>
</div>
<?php endif; ?>