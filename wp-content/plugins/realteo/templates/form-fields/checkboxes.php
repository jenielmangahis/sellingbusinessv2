<?php 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$field = $data->field;
$key = $data->key;
?>
<div class="checkboxes in-row margin-bottom-20">

	<?php foreach ( $field['options'] as $slug => $name ) : ?>


		<input id="<?php echo esc_html($slug) ?>" type="checkbox" name="<?php echo $key.'['.esc_html($slug).']'; ?>">
		<label for="<?php echo esc_html($slug) ?>"><?php echo esc_html($name) ?></label>
	<?php endforeach; ?>

</div>