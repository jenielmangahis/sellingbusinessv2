<?php 
$video = get_post_meta( $post->ID, '_video', true ); 
if($video) : ?> 
<!-- Video -->

<h3 class="desc-headline no-border"><?php esc_html_e('Video','realteo'); ?></h3>
<div class="responsive-iframe">
	<?php echo wp_oembed_get( $video ); ?>
</div>
<?php endif ?>