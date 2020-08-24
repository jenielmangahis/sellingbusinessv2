<?php
class Realteo_Walker_Checklist extends Walker_Category_Checklist {
	
	/**
     * Starts the list before the elements are added.
     *
     * @see Walker:start_lvl()
     *
     * @since 2.5.1
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param int    $depth  Depth of category. Used for tab indentation.
     * @param array  $args   An array of arguments. @see wp_terms_checklist()
     */
    public function start_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent\n";
    }
 
    /**
     * Ends the list of after the elements are added.
     *
     * @see Walker::end_lvl()
     *
     * @since 2.5.1
     *
     * @param string $output Passed by reference. Used to append additional content.
     * @param int    $depth  Depth of category. Used for tab indentation.
     * @param array  $args   An array of arguments. @see wp_terms_checklist()
     */
    public function end_lvl( &$output, $depth = 0, $args = array() ) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent\n";
    }

	public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
        if ( empty( $args['taxonomy'] ) ) {
            $taxonomy = 'category';
        } else {
            $taxonomy = $args['taxonomy'];
        }
 
        if ( $taxonomy == 'category' ) {
            $name = 'post_category';
        } else {
            $name = 'tax_input[' . $taxonomy . ']';
        }
 
        $args['popular_cats'] = empty( $args['popular_cats'] ) ? array() : $args['popular_cats'];
        $class = in_array( $category->term_id, $args['popular_cats'] ) ? ' class="popular-category"' : '';
 
        $args['selected_cats'] = empty( $args['selected_cats'] ) ? array() : $args['selected_cats'];

        $output .= "\n" .           
        	'<input value="' . $category->term_id . '" type="checkbox" name="'.$name.'[]" id="in-'.$taxonomy.'-' . $category->term_id . '"' .
            checked( in_array( $category->term_id, $args['selected_cats'] ), true, false ) .
            disabled( empty( $args['disabled'] ), false, false ) . ' /> ' .
            '<label for="in-'.$taxonomy.'-' . $category->term_id . '">'. esc_html( apply_filters( 'the_category', $category->name ) ) . '</label>';
       
    }

  }
