<!-- Features -->
<?php   
$term_list = get_the_term_list( $post->ID, 'property_feature' );
if(!empty($term_list)): ?>
<h3 class="desc-headline"><?php esc_html_e('Features','realteo'); ?></h3>
<?php $terms = get_the_terms($post->ID, 'property_feature');
   echo '<ul class="property-features checkboxes margin-top-0">';
   foreach($terms as $term){
   	$term_link = get_term_link( $term );
     echo '<li id="feature-'.$term->slug.'"><a href="' . esc_url( $term_link ) . '">'.$term->name.'</a></li>';
   };
  echo "</ul>";
?>
<?php  endif; ?>