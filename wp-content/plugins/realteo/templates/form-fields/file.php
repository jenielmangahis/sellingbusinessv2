<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$field = $data->field;
$key = $data->key;
$value = (isset($field['value'])) ? $field['value'] : '' ;
$allowed_mime_types = array_keys( ! empty( $field['allowed_mime_types'] ) ? $field['allowed_mime_types'] : get_allowed_mime_types() );

if ( ! empty( $field['value'] ) ) : ?>
<div class="realteo-uploaded-file">

		<?php
		if ( is_numeric( $value ) ) {
			$image_src = wp_get_attachment_image_src( absint( $value ) );
			$image_src = $image_src ? $image_src[0] : '';
		} else {
			$image_src = $value;
		}
		$extension = ! empty( $extension ) ? $extension : substr( strrchr( $image_src, '.' ), 1 );
		if ( 'image' === wp_ext2type( $extension ) ) : ?>
			<span class="realteo-uploaded-file-preview"><img src="<?php echo esc_url( $image_src ); ?>" /> 
			<a class="remove-uploaded-file" href="#"><?php _e( 'Remove file', 'realteo' ); ?></a></span>
		<?php else : ?>
			<span class="realteo-uploaded-file-name"><?php echo esc_html( basename( $image_src ) ); ?> 
			<a class="remove-uploaded-file" href="#"><?php _e( 'Remove file', 'realteo' ); ?></a></span>
		<?php endif; ?>

		<input type="hidden" class="input-text" name="current_<?php echo esc_attr( $field['name'] ); ?>" value="<?php echo esc_attr( $value ); ?>" />
	</div>

<?php endif; ?>

<input type="file"  name="<?php echo esc_attr( isset( $field['name'] ) ? $field['name'] : $key ); ?>" id="<?php echo esc_attr( $key ); ?>" <?php if ( ! empty( $field['required'] ) ) echo 'required'; ?> />
