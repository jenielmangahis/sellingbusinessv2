<?php
/**
 * Customer minimum order notification email
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

echo "= " . $email_heading . " =\n\n";

echo "----------\n\n";

echo wptexturize( $body_content ) . "\n\n";


if($posts){
echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

	foreach($posts as $post){
		echo "------------\n\n";
		echo $post['post_title'] ."\n\n";
		echo trimCharacters(esc_attr( wp_strip_all_tags( $postpost_content ) ),150) ."\n\n";
		echo get_permalink($post['ID']);
	}
}

echo "----------\n\n";

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo apply_filters( 'woocommerce_email_footer_text', get_option( 'woocommerce_email_footer_text' ) );
