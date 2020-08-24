<?php 
$ids = '';
if(isset($data)) :
	$ids	 	= (isset($data->ids)) ? $data->ids : '' ;
endif; 
$no_bookmarks = false;

if($ids) {
	foreach ($ids as $id) {

		if($id && get_post_status($id) == 'publish'){
			$no_bookmarks = false;
		} else {
			$no_bookmarks = true;
		}
	}
} else {
	$no_bookmarks = true;
}

?> 
<div class="col-md-8">
<?php if(!empty($ids) && $no_bookmarks == false) : ?>
	<table class="manage-table bookmarks-table responsive-table">
		<?php if($no_bookmarks == false) { ?>
		<tr>
			<th><i class="fa fa-file-text"></i>  <?php esc_html_e('Businesses','realteo');?></th>
			<th></th>
		</tr>
		<?php } ?>
		<?php
		$nonce = wp_create_nonce("realteo_remove_fav_nonce");
		foreach ($ids as $bookmark) {
			if($bookmark) :
				if ( get_post_status( $bookmark ) !== 'publish' ) {
					$no_bookmarks = true;
					continue;
					
				}
				$property = get_post($bookmark);

				$no_bookmarks = false; ?>
				<tr>
					<td class="title-container">
						<?php 
						if(has_post_thumbnail()){ 
							echo get_the_post_thumbnail($property);
						} else {
							$gallery = (array) get_post_meta( $bookmark, '_gallery', true );
							$ids = array_keys($gallery);
							if(!empty($ids[0]) && $ids[0] !== 0){ 
								echo  wp_get_attachment_image($ids[0]); 
							}	
						}
						

						?>
						<div class="title">
							<h4><a href="<?php echo get_permalink( $bookmark ) ?>"><?php echo get_the_title( $bookmark ); ?></a></h4>
							<span><?php the_property_address($property); ?></span>
							<span class="table-property-price"><?php the_property_price($property); ?></span>
						</div>
					</td>
					<td class="action">
						<a href="#" class="realteo-unbookmark-it delete" data-post_id="<?php echo esc_attr($bookmark); ?>" data-nonce="<?php echo esc_attr($nonce); ?>"><i class="fa fa-remove"></i> <?php esc_html_e('Remove','realteo');?></a>
					</td>
				</tr>
		<?php endif;
		} ?>
		
		<!-- Item #1 -->
		
	</table>
<?php else: ?>
			<div class="notification notice ">
				<p><span><?php esc_html_e('No bookmarks!','realteo'); ?></span> <?php esc_html_e('You haven\'t saved anything yet!','realteo'); ?></p>
				
			</div>
<?php endif;
?>

</div>