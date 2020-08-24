<?php
/**
 * Template Functions
 *
 * Template functions for properties
 *
 * @author 		Lukasz Girek
 * @version     1.0
 */


/**
 * Add custom body classes
 */
function realteo_body_class( $classes ) {
	$classes   = (array) $classes;
	$classes[] = sanitize_title( wp_get_theme() );

	return array_unique( $classes );
}

add_filter( 'body_class', 'realteo_body_class' );


/**
 * Outputs the property offer type
 *
 * @return void
 */
function the_property_offer_type( $post = null ) {
	$type = get_the_property_offer_type( $post );
	$offers = realteo_get_offer_types_flat(true);
	
	if(is_array($type)) {
		foreach ($type as  $value) {
			if(array_key_exists($value, $offers)) {
				echo '<span class="property-badge property-badge-'.$value.'">'.$offers[$value].'</span>';	
			}
		}
	} else {
		
		if(array_key_exists($type, $offers)) {
			echo '<span class="property-badge property-badge-'.$type.'">'.$offers[$type].'</span>';	
		}
	}
	
}

/**
 * Gets the property offer type
 *
 * @return string
 */
function get_the_property_offer_type( $post = null ) {
	$post     = get_post( $post );
	if ( $post->post_type !== 'property' ) {
		return;
	}
	$offer_type = get_post_meta($post->ID,'_offer_type',false);
	if(isset($offer_type[0]) && is_array($offer_type[0])){ //check if value was previously saved as serialized array
		$offer_type = $offer_type[0];
	}
	return apply_filters( 'the_property_offer_type', $offer_type, $post );
}



function the_property_type( $post = null ) {
	$type = get_the_property_type( $post );
	$types = realteo_get_property_types(true);
	if(is_array($type)){
		foreach ($type as $value) {
			if(array_key_exists($value, $types)) {
				echo '<span class="property-type-badge property-type-badge-'.$value.'">'.$types[$value].'</span>';	
			}
		}
	} else {
		if(array_key_exists($type, $types)) {
			echo '<span class="property-type-badge property-type-badge-'.$type.'">'.$types[$type].'</span>';	
		}
	}
	
}
/**
 * Gets the property  type
 *
 * @return string
 */
function get_the_property_type( $post = null ) {
	$post     = get_post( $post );
	if ( $post->post_type !== 'property' ) {
		return;
	}
	$property_type = get_post_meta($post->ID,'_property_type',false);
	if(isset($property_type[0]) && is_array($property_type[0])){ //check if value was previously saved as serialized array
		$property_type = $property_type[0];
	}
	return apply_filters( 'the_property_type', $property_type, $post );
}

/**
 * Outputs the property location
 *
 * @return void
 */
function the_property_address( $post = null ) {
	echo get_the_property_address( $post );
}

/**
 * get_the_property_address function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return void
 */
if(!function_exists('get_the_property_address')) {
	function get_the_property_address( $post = null ) {
		$post = get_post( $post );
		if ( $post->post_type !== 'property' ) {
			return;
		}
		if(isset($post->_friendly_address) && !empty($post->_friendly_address)) {
			return apply_filters( 'the_property_friendly_address', $post->_friendly_address, $post );
		} else {
			return apply_filters( 'the_property_location', $post->_address, $post );
		}
	}
}


/**
 * Outputs the property price
 *
 * @return void
 */
function the_property_price( $post = null ) {
	echo get_the_property_price( $post );
}

/**
 * get_the_property_price function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return void
 */
function get_the_property_price( $post = null ) {
	return Realteo_Property::get_property_price( $post );
}


/**
 * Outputs the property price per scale
 *
 * @return void
 */
function the_property_price_per_scale( $post = null ) {
	echo get_the_property_price_per_scale( $post );
}

function get_the_property_price_per_scale( $post = null ) {
	return Realteo_Property::get_property_price_per_scale( $post );
}

if(!function_exists('the_property_location_link')) {
	function the_property_location_link($post = null, $map_link = true ) {

		$address =  get_post_meta( $post, '_address', true );
		$friendly_address =  get_post_meta( $post, '_friendly_address', true );
		if(empty($friendly_address)) { $friendly_address = $address; }
		if ( $address ) {
			if ( $map_link ) {
				// If linking to google maps, we don't want anything but text here
				echo apply_filters( 'the_property_map_link', '<a class="listing-address popup-gmaps" href="' . esc_url( 'https://maps.google.com/maps?q=' . urlencode( strip_tags( $address ) ) . '' ) . '"><i class="fa fa-map-marker"></i>' . esc_html( strip_tags( $friendly_address ) ) . '</a>', $address, $post );
			} else {
				echo wp_kses_post( $address );
			}
		} 

	}
}

function realteo_check_if_bookmarked($id){
	if($id){
		$classObj = new Realteo_Bookmarks;
		return $classObj->check_if_added($id);
	} else {
		return false;
	}
}

function realteo_is_featured($id){
	$featured = get_post_meta($id,'_featured',true);
	if(!empty($featured)) {
		return true;
	} else {
		return false;
	}
}



/**
 * Gets the property title for the listing.
 *
 * @since 1.27.0
 * @param int|WP_Post $post (default: null)
 * @return string|bool|null
 */
function realteo_get_the_property_title( $post = null ) {
	$post = get_post( $post );
	if ( ! $post || 'property' !== $post->post_type ) {
		return;
	}

	$title = esc_html( get_the_title( $post ) );

	/**
	 * Filter for the property title.
	 *
	 * @since 1.27.0
	 * @param string      $title Title to be filtered.
	 * @param int|WP_Post $post
	 */
	return apply_filters( 'realteo_the_property_title', $title, $post );
}

function realteo_add_tooltip_to_label( $field_args, $field ) {
	// Get default label
	$label = $field->label();
	if ( $label && $field->options( 'tooltip' ) ) {
		$label = substr($label, 0, -9);
		
		// If label and tooltip exists, add it
		$label .= sprintf( ' <i class="tip" data-tip-content="%s"></i></label>',$field->options( 'tooltip' ) );
	}

	return $label;
}

/**
 * Overrides the default render field method
 * Allows you to add custom HTML before and after a rendered field
 *
 * @param  array             $field_args Array of field parameters
 * @param  CMB2_Field object $field      Field object
 */
function realteo_render_as_col_12( $field_args, $field ) {

	// If field is requesting to not be shown on the front-end
	if ( ! is_admin() && ! $field->args( 'on_front' ) ) {
		return;
	}

	// If field is requesting to be conditionally shown
	if ( ! $field->should_show() ) {
		return;
	}

	$field->peform_param_callback( 'before_row' );

	echo '<div class="col-md-12">';
	
	// Remove the cmb-row class
	printf( '<div class="custom-class %s">', $field->row_classes() );

	if ( ! $field->args( 'show_names' ) ) {
	
		// If the field is NOT going to show a label output this
		$field->peform_param_callback( 'label_cb' );
	
	} else {

		// Otherwise output something different
		if ( $field->get_param_callback_result( 'label_cb', false ) ) {
			echo $field->peform_param_callback( 'label_cb' );
		}
		
	}

	$field->peform_param_callback( 'before' );
	
	// The next two lines are key. This is what actually renders the input field
	$field_type = new CMB2_Types( $field );
	$field_type->render();

	$field->peform_param_callback( 'after' );

		echo '</div>'; //cmb-row

	echo '</div>';

	$field->peform_param_callback( 'after_row' );

    // For chaining
	return $field;
}
/**
 * Dispays bootstarp column start
 * @param  string $col integer column width
 */
function realteo_render_column($col='') {
	echo '<div class="col-md-'.$col.'">';
}


function realto_result_sorting($list_style, $layout_switch = null, $order_switch = null){
	
	if($order_switch == 'off') {
		return;
	}
	$template_loader = new Realteo_Template_Loader; 
	$template_loader->get_template_part( 'archive/sorting' ); 
}

function realto_result_layout_switch($list_style, $layout_switch = null, $order_switch = null){
	if(!isset($layout_switch)){
		$layout_switch = 'on';
	}
	if($list_style != 'compact' && $layout_switch == 'on') {
		$template_loader = new Realteo_Template_Loader; 
		$template_loader->get_template_part( 'archive/layout-switcher' ); 	
	}
	
}

/* Hooks */
add_action( 'realto_before_archive', 'realto_result_sorting', 10, 3 );
add_action( 'realto_before_archive', 'realto_result_layout_switch', 20, 3 );

/**
 * Return type of properties
 *
 */
function realteo_get_property_types(){
	 $options = array(
        	'apartments' => __( 'Apartments', 'realteo' ),
			'houses' 	 => __( 'Houses', 'realteo' ),
			'commercial' => __( 'Commercial', 'realteo' ),
			'garages' 	 => __( 'Garages', 'realteo' ),
			'lots' 		 => __( 'Lots', 'realteo' ),
    );
	return apply_filters('realteo_get_property_types',$options);
}
/*
function add_property_types_from_option($r){
	$properties =  realteo_get_option_with_name('realteo_general_options', 'realteo_default_property_types' );
	if(!empty($properties)) {
		$properties_array = explode(',',$properties);
		$r = array();
		foreach ($properties_array as $key ) {
			$id = sanitize_title($key);
			$r[$id] = $key;
		}
	}
	
	return $r;
}*/

/*add_filter('realteo_get_property_types','add_property_types_from_option');*/

/**
 * Return type of properties
 *
 */
function realteo_get_rental_period(){
	 $options = array(
        	'daily' => __( 'Daily', 'realteo' ),
			'weekly' 	 => __( 'Weekly', 'realteo' ),
			'monthly' => __( 'Monthly', 'realteo' ),
			'yearly' 	 => __( 'Yearly', 'realteo' ),
    );
	return apply_filters('realteo_get_rental_period',$options);
}

/**
 * Return type of offers
 *
 */

function realteo_get_offer_types(){
	$options =  array(
        	'sale' => array( 
        		'name' => __( 'For Sale', 'realteo' ),
        		'front' => '1'
        		), 
			'rent' => array( 
        		'name' => __( 'For Rent', 'realteo' ),
        		'front' => '1',
        		'period' => '1'
        		), 
			'sold' => array( 
        		'name' => __( 'Sold', 'realteo' )
        		), 
			'rented' => array( 
        		'name' => __( 'Rented', 'realteo' )
        		), 
    );
	return apply_filters('realteo_get_offer_types',$options);
}

function realteo_get_offer_types_flat($with_all = false){
	$org_offer_types = realteo_get_offer_types();

	$options = array();
	foreach ($org_offer_types as $key => $value) {

		if($with_all == true ) {
			$options[$key] = $value['name']; 
		} else {
			if(isset($value['front']) && $value['front'] == 1) {
				$options[$key] = $value['name']; 
			} elseif(!isset($value['front']) && in_array($key, array('sale','rent'))) {
					$options[$key] = $value['name']; 
				
			}
		}
	}
	return $options;
}
function realteo_get_options_array($type,$data) {
	$options = array();
	if($type == 'taxonomy'){
		$categories =  get_terms( $data, array(
		    'hide_empty' => false,
		) );	
		$options = array();
		foreach ($categories as $cat) {
			$options[$cat->term_id] = array ( 
				'name'  => $cat->name,
				'slug'  => $cat->slug,
				'id'	=> $cat->term_id,
				);
		}
	}
	return $options;
}
function realteo_get_options_array_hierarchical($terms, $selected, $output = '', $parent_id = 0, $level = 0) {
    //Out Template
    $outputTemplate = '<option %SELECED% value="%ID%">%PADDING%%NAME%</option>';

    foreach ($terms as $term) {
        if ($parent_id == $term->parent) {
        	if(is_array($selected)) {
				$is_selected = in_array( $term->slug, $selected ) ? ' selected="selected" ' : '';
			} else {
				$is_selected = selected($selected, $term->slug);
			}
            //Replacing the template variables
            $itemOutput = str_replace('%SELECED%', $is_selected, $outputTemplate);
            $itemOutput = str_replace('%ID%', $term->slug, $itemOutput);
            $itemOutput = str_replace('%PADDING%', str_pad('', $level*12, '&nbsp;&nbsp;'), $itemOutput);
            $itemOutput = str_replace('%NAME%', $term->name, $itemOutput);

            $output .= $itemOutput;
            $output = realteo_get_options_array_hierarchical($terms, $selected, $output, $term->term_id, $level + 1);
        }
    }
    return $output;
}

/*$terms = get_terms('taxonomy', array('hide_empty' => false));
$output = get_terms_hierarchical($terms);

echo '<select>' . $output . '</select>';  
*/
/**
 * Returns html for select input with options based on type
 *
 *
 * @param  $type taxonomy
 * @param  $data term
 */	
function get_realteo_dropdown( $type, $data='', $name, $class='chosen-select-no-single', $placeholder='Any Type'){
	$output = '<select name="'.esc_attr($name).'" data-placeholder="'.esc_attr($placeholder).'" class="'.esc_attr($class).'">';
	if($type == 'taxonomy'){
		$categories =  get_terms( $data, array(
		    'hide_empty' => false,
		) );	
		
		$output .= '<option>'.esc_html__('Any Type','realteo').'</option>';
		foreach ($categories as $cat) { 
			$output .= '<option value='.$cat->term_id.'>'.$cat->name.'</option>';
		}
	}
	$output .= '</select>';
	return $output;
}

/**
 * Returns html for just options input based on data array
 *
 * @param  $data array
 */	
function get_realteo_options_dropdown(  $data,$selected ){
	$output = '';

	if(is_array($data)) :
		foreach ($data as $id => $value) {
			if(is_array($selected)) {

				$is_selected = in_array( $value['slug'], $selected ) ? ' selected="selected" ' : '';
				
			} else {
				$is_selected = selected($selected, $id);
			}
			$output .= '<option '.$is_selected.' value="'.esc_attr($value['slug']).'">'.esc_html($value['name']).'</option>';
		}
	endif;
	return $output;
}

function get_realteo_options_dropdown_by_type( $type, $data ){
	$output = '';
	if(is_array($data)) :
		foreach ($data as $id => $value) {
			$output .= '<option value="'.esc_attr($id).'">'.esc_html($value).'</option>';
		}
	endif;
	return $output;
}

function get_realteo_numbers_dropdown( $number=10 ){
	$output = '';
	$x = 1;
	while($x <= $number) {
		$output .= '<option value="'.esc_attr($x).'">'.esc_html($x).'</option>';
    	$x++;
	} 
	return $output;
}

function get_realteo_intervals_dropdown( $min, $max, $step = 100, $name = false ){
	$output = '';
	
	if($min == 'auto'){
		$min = Realteo_Search::get_min_meta_value($name);
	}
	if($max == 'auto'){
		$max = Realteo_Search::get_max_meta_value($name);
	}
	$range = range($min, $max, $step );
	if(sizeof($range) > 30 ) {
		$output = "<option>ADMIN NOTICE: increase your step value in Search Form Editor, having more than 30 steps is not recommended for performence options</option>";
	} else {
		foreach ($range as $number) {
		    $output .= '<option value="'.esc_attr($number).'">'.esc_html(number_format_i18n($number)).'</option>';
		}
	}
	return $output;
}


/**
 * Gets a number of posts and displays them as options
 * @param  array $query_args Optional. Overrides defaults.
 * @return array             An array of options that matches the CMB2 options array
 */
function realteo_cmb2_get_post_options( $query_args ) {

	$args = wp_parse_args( $query_args, array(
		'post_type'   => 'post',
		'numberposts' => -1,
	) );

	$posts = get_posts( $args );

	$post_options = array();
	$post_options[0] = esc_html__('--Choose page--','realteo');
	if ( $posts ) {
		foreach ( $posts as $post ) {
          $post_options[ $post->ID ] = $post->post_title;
		}
	}

	return $post_options;
}

/**
 * Gets 5 posts for your_post_type and displays them as options
 * @return array An array of options that matches the CMB2 options array
 */
function realteo_cmb2_get_pages_options() {
	return realteo_cmb2_get_post_options( array( 'post_type' => 'page', ) );
}




function realteo_agent_name(){
	$fname = get_the_author_meta('first_name');
	$lname = get_the_author_meta('last_name');
	$full_name = '';

	if( empty($fname)){
	    $full_name = $lname;
	} elseif( empty( $lname )){
	    $full_name = $fname;
	} else {
	    //both first name and last name are present
	    $full_name = "{$fname} {$lname}";
	}

	echo $full_name;
}


function realteo_pagination($pages = '', $range = 2 ) {
    global $paged;

    if(empty($paged))$paged = 1;

    $prev = $paged - 1;
    $next = $paged + 1;
    $showitems = ( $range * 2 )+1;
    $range = 2; // change it to show more links

    if( $pages == '' ){
        global $wp_query;

        $pages = $wp_query->max_num_pages;
        if( !$pages ){
            $pages = 1;
        }
    }

    if( 1 != $pages ){

        
            echo '<ul class="pagination">';
                echo ( $paged > 2 && $paged > $range+1 && $showitems < $pages ) ? '<li><a href="'.get_pagenum_link(1).'"><i class="fa fa-angle-double-left"></i></a></li>' : '';
                echo ( $paged > 1 ) ? '<li><a class="previouspostslink" href="'.get_pagenum_link($prev).'">'.__('Previous','realteo').'</a></li>' : '';
                for ( $i = 1; $i <= $pages; $i++ ) {
                    if ( 1 != $pages &&( !( $i >= $paged+$range+1 || $i <= $paged-$range-1 ) || $pages <= $showitems ) )
                    {
                        if ( $paged == $i ){
                            echo '<li class="current" data-paged="'.$i.'"><a href="'.get_pagenum_link($i).'">'.$i.' </a></li>';
                        } else {
                            echo '<li data-paged="'.$i.'"><a href="'.get_pagenum_link($i).'">'.$i.'</a></li>';
                        }
                    }
                }
                echo ( $paged < $pages ) ? '<li><a class="nextpostslink" href="'.get_pagenum_link($next).'">'.__('Next','realteo').'</a></li>' : '';
                echo ( $paged < $pages-1 &&  $paged+$range-1 < $pages && $showitems < $pages ) ? '<li><a  href="'.get_pagenum_link( $pages ).'"><i class="fa fa-angle-double-right"></i></a></li>' : '';
            echo '</ul>';
  

    }
}

function realteo_get_post_status($id){
	$status = get_post_status($id);
	switch ($status) {
		case 'publish':
			$friendly_status = esc_html__('Published', 'realteo');
			break;		
		case 'pending_payment':
			$friendly_status = esc_html__('Pending Payment', 'realteo');
			break;
		case 'expired':
			$friendly_status = esc_html__('Expired', 'realteo');
			break;
		case 'draft':
		case 'pending':
			$friendly_status = esc_html__('Pending Approval', 'realteo');
			break;
		
		default:
			$friendly_status = $status;
			break;
	}
	return $friendly_status;
	
}

/**
 * Calculates and returns the property expiry date.
 *
 * @since 1.22.0
 * @param  int $id
 * @return string
 */
function calculate_property_expiry( $id ) {
	// Get duration from the product if set...
	$duration = get_post_meta( $id, '_duration', true );
	
	// ...otherwise use the global option
	if ( ! $duration ) {
		$duration = absint( realteo_get_option( 'realteo_default_duration' ) );
	}

	if ( $duration ) {
		return strtotime( "+{$duration} days", current_time( 'timestamp' ) );
	}

	return '';
}

function realteo_get_expiration_date($id) {
	$expires = get_post_meta( $id, '_property_expires', true );

	if(!empty($expires)) {
		if(realteo_is_timestamp($expires)) {
			$saved_date = get_option( 'date_format' );
			$new_date = date($saved_date, $expires); 
		} else {
			return $expires;
		}
	}
	return (empty($expires)) ? __('Never/not set','realteo') : $new_date ;
}

function realteo_get_property_image($id){

	if(has_post_thumbnail($id)){ 
		return	wp_get_attachment_image_url( get_post_thumbnail_id( $id ),'findeo-property-grid' );
	} else {

		$gallery = (array) get_post_meta( $id, '_gallery', true );
		$ids = array_keys($gallery);
		if(!empty($ids[0]) && $ids[0] !== 0){ 
			return  wp_get_attachment_image_url($ids[0],'findeo-property-grid'); 
		} else {
			$placeholder = get_realteo_placeholder_image();
			return $placeholder;
		}
		
	} 
}

add_action('findeo_page_subtitle','realteo_my_account_hello');
function realteo_my_account_hello(){
	$my_account_page = realteo_get_option( 'my_account_page');
	if(is_user_logged_in() && !empty($my_account_page) && is_page($my_account_page)){
		$current_user = wp_get_current_user();
		if(!empty($current_user->user_firstname)){
			$name = $current_user->user_firstname.' '.$current_user->user_lastname;
		} else {
			$name = $current_user->display_name;
		}
		echo "<span>" . esc_html__('Howdy, ','realteo') . $name.'!</span>';
	}
}



function realteo_sort_by_priority( $array = array(), $order = SORT_NUMERIC ) {
	
		if ( ! is_array( $array ) )
			return;

		// Sort array by priority

		$priority = array();

		foreach ( $array as $key => $row ) {

			if ( isset( $row['position'] ) ) {
				$row['priority'] = $row['position'];
				unset( $row['position'] );
			}

			$priority[$key] = isset( $row['priority'] ) ? absint( $row['priority'] ) : false;
		}

		array_multisort( $priority, $order, $array );

		return apply_filters( 'realteo_sort_by_priority', $array, $order );
}


/**
 * CMB2 Select Multiple Custom Field Type
 * @package CMB2 Select Multiple Field Type
 */

/**
 * Adds a custom field type for select multiples.
 * @param  object $field             The CMB2_Field type object.
 * @param  string $value             The saved (and escaped) value.
 * @param  int    $object_id         The current post ID.
 * @param  string $object_type       The current object type.
 * @param  object $field_type_object The CMB2_Types object.
 * @return void
 */
if(!function_exists('cmb2_render_select_multiple_field_type')) {
	function cmb2_render_select_multiple_field_type( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
		$saved_values = get_post_meta($object_id,$field->args['_name']);

		$select_multiple = '<select class="widefat" multiple name="' . $field->args['_name'] . '[]" id="' . $field->args['_id'] . '"';
		foreach ( $field->args['attributes'] as $attribute => $value ) {
			$select_multiple .= " $attribute=\"$value\"";
		}
		$select_multiple .= ' />';
		
		if(is_string($escaped_value)) {
			$escaped_value = explode(',',$escaped_value);
		} 
		foreach ( $field->options() as $value => $name ) {
			$selected = '';
			if(is_array($saved_values)){

				if(in_array($value,$saved_values)) {
					$selected = 'selected="selected"';
				}
			} else {
				$selected = ( $escaped_value && in_array( $value, $escaped_value ) ) ? 'selected="selected"' : '';	
			}
			
			
			$select_multiple .= '<option class="cmb2-option" value="' . esc_attr( $value ) . '" ' . $selected . '>' . esc_html( $name ) . '</option>';
		}

		$select_multiple .= '</select>';
		$select_multiple .= $field_type_object->_desc( true );

		echo $select_multiple; // WPCS: XSS ok.
	}
	add_action( 'cmb2_render_select_multiple', 'cmb2_render_select_multiple_field_type', 10, 5 );


	/**
	 * Sanitize the selected value.
	 */
	
	function cmb2_sanitize_select_multiple_callback( $override_value, $value ) {
		if ( is_array( $value ) ) {
			foreach ( $value as $key => $saved_value ) {
				$value[$key] = sanitize_text_field( $saved_value );
			}
			return $value;
		}
		return;
	}
	add_filter( 'cmb2_sanitize_select_multiple', 'cmb2_sanitize_select_multiple_callback', 10, 4 );

	

	function cmb2_save_select_multiple_callback( $override, array $args, array  $field_args ) {
		if($field_args['type'] == 'select_multiple' || $field_args['type'] === 'multicheck_split') {
			if ( is_array( $args['value'] ) ) {
			
				delete_post_meta($args['id'], $args['field_id']);
				foreach ( $args['value'] as $key => $saved_value ) {
					$sanitized_value = sanitize_text_field( $saved_value );
					add_post_meta( $args['id'], $args['field_id'], $sanitized_value );
				}

				
			}
			return true;
		}
		return $override;
		
	}
	add_filter( 'cmb2_override_meta_save', 'cmb2_save_select_multiple_callback', 10, 4 );


}
function cmb2_render_multicheck_split_field_type( $field, $escaped_value, $object_id, $object_type, $field_type_object ) {
	$saved_values = get_post_meta($object_id,$field->args['_name']);

	$select_multiple = '
	<ul class="cmb2-checkbox-list cmb2-list">	';
	
	
	if(is_string($escaped_value)) {
		$escaped_value = explode(',',$escaped_value);
	} 
	$i = 0;
	foreach ( $field->options() as $value => $name ) {
		$selected = '';
		$i++;
		if(is_array($saved_values)){
			if(in_array($value,$saved_values)) {
				$selected = 'checked="checked"';
			}
		} else {
			$selected = ( $escaped_value && in_array( $value, $escaped_value ) ) ? 'checked="checked"' : '';	
		}	
		
		$select_multiple .= '<li><input type="checkbox" class="cmb2-option" name="' . $field->args['_name'] . '[]" id="' . $field->args['_id'] . $i .'" value="' . esc_attr( $value ) . '" ' . $selected . '><label for="' . $field->args['_id'] . $i .'">' . esc_html( $name ) . '</label></li>';
	}
	$select_multiple .= "</ul>";
	
	$select_multiple .= $field_type_object->_desc( true );

	echo $select_multiple; // WPCS: XSS ok.
}
add_action( 'cmb2_render_multicheck_split', 'cmb2_render_multicheck_split_field_type', 5, 5 );


function realteo_array_sort_by_column(&$arr, $col, $dir = SORT_ASC) {
    $sort_col = array();
    foreach ($arr as $key=> $row) {
        $sort_col[$key] = $row[$col];
    }

    array_multisort($sort_col, $dir, $arr);
}


function realteo_get_nearby_properties($lat, $lng, $distance, $radius_type){
    global $wpdb;
    if($radius_type=='km') {
    	$ratio = 6371;
    } else {
    	$ratio = 3959;
    }

  	$post_ids = 
			$wpdb->get_results(
				$wpdb->prepare( "
			SELECT DISTINCT
			 		geolocation_lat.post_id,
			 		geolocation_lat.meta_key,
			 		geolocation_lat.meta_value as propertyLat,
			        geolocation_long.meta_value as propertyLong,
			        ( %d * acos( cos( radians( %f ) ) * cos( radians( geolocation_lat.meta_value ) ) * cos( radians( geolocation_long.meta_value ) - radians( %f ) ) + sin( radians( %f ) ) * sin( radians( geolocation_lat.meta_value ) ) ) ) AS distance 
		       
			 	FROM 
			 		$wpdb->postmeta AS geolocation_lat
			 		LEFT JOIN $wpdb->postmeta as geolocation_long ON geolocation_lat.post_id = geolocation_long.post_id
					WHERE geolocation_lat.meta_key = '_geolocation_lat' AND geolocation_long.meta_key = '_geolocation_long'
			 		HAVING distance < %d

		 	", 
		 	$ratio, 
		 	$lat, 
		 	$lng, 
		 	$lat, 
		 	$distance)
		,ARRAY_A);

    return $post_ids;
 
}


// function to geocode address, it will return false if unable to geocode address
function realteo_geocode($address){
 
    // url encode the address
    $address = urlencode($address);
	$api_key = realteo_get_option( 'realteo_maps_api_server' );
    // google map geocode api url
    $url = "https://maps.google.com/maps/api/geocode/json?address={$address}&key={$api_key}";
 
    // get the json response
    // get the json response
    $resp_json = wp_remote_get($url);
    $file = 'wp-content/geocode.txt';
    //file_put_contents($file, $resp_json);
    // decode the json
    
 
	$resp_json = wp_remote_get($url);
 	$resp = json_decode( wp_remote_retrieve_body( $resp_json ), true );
 

    // response status will be 'OK', if able to geocode given address 
    if($resp['status']=='OK'){
 
        // get the important data
        $lati = $resp['results'][0]['geometry']['location']['lat'];
        $longi = $resp['results'][0]['geometry']['location']['lng'];
        $formatted_address = $resp['results'][0]['formatted_address'];
         
        // verify if data is complete
        if($lati && $longi && $formatted_address){
         
            // put the data in the array
            $data_arr = array();            
             
            array_push(
                $data_arr, 
                    $lati, 
                    $longi, 
                    $formatted_address
                );
             
            return $data_arr;
             
        }else{
            return false;
        }
         
    }else{
        return false;
    }
}


/**
 * Checks if the user can edit a property.
 */
function realteo_if_can_edit_property( $property_id ) {
	$can_edit = true;

	if ( ! is_user_logged_in() || ! $property_id ) {
		$can_edit = false;
	} else {
		$property      = get_post( $property_id );

		if ( ! $property || ( absint( $property->post_author ) !== get_current_user_id()  ) ) {
			$can_edit = false;
		}
		
	}

	return apply_filters( 'realteo_if_can_edit_property', $can_edit, $property_id );
}



//&& ! current_user_can( 'edit_post', $property_id )


add_filter('submit_property_form_submit_button_text','realteo_rename_button_no_preview');

function realteo_rename_button_no_preview(){
	if(realteo_get_option_with_name('realteo_property_submit_option', 'realteo_new_property_preview' )) {
			return  __( 'Submit', 'realteo' );
		} else {
			return  __( 'Preview', 'realteo' );
		}
}

function get_realteo_placeholder_image(){
	$image_id = realteo_get_option_with_name('realteo_general_options', 'realteo_placeholder_id' );
	if($image_id) {
		$placeholder = wp_get_attachment_image_src($image_id,'findeo-property-grid');
		return $placeholder[0];
	} else {
		return  plugin_dir_url( __FILE__ )."templates/images/placeimg.jpg";
	}
	
}


/**
 * Prepares files for upload by standardizing them into an array. This adds support for multiple file upload fields.
 *
 * @since 1.21.0
 * @param  array $file_data
 * @return array
 */
function realteo_prepare_uploaded_files( $file_data ) {
	$files_to_upload = array();

	if ( is_array( $file_data['name'] ) ) {
		foreach( $file_data['name'] as $file_data_key => $file_data_value ) {
			if ( $file_data['name'][ $file_data_key ] ) {
				$type              = wp_check_filetype( $file_data['name'][ $file_data_key ] ); // Map mime type to one WordPress recognises
				$files_to_upload[] = array(
					'name'     => $file_data['name'][ $file_data_key ],
					'type'     => $type['type'],
					'tmp_name' => $file_data['tmp_name'][ $file_data_key ],
					'error'    => $file_data['error'][ $file_data_key ],
					'size'     => $file_data['size'][ $file_data_key ]
				);
			}
		}
	} else {
		$type              = wp_check_filetype( $file_data['name'] ); // Map mime type to one WordPress recognises
		$file_data['type'] = $type['type'];
		$files_to_upload[] = $file_data;
	}

	return apply_filters( 'realteo_prepare_uploaded_files', $files_to_upload );
}



/**
 * Uploads a file using WordPress file API.
 *
 * @since 1.21.0
 * @param  array|WP_Error      $file Array of $_FILE data to upload.
 * @param  string|array|object $args Optional arguments
 * @return stdClass|WP_Error Object containing file information, or error
 */
function realteo_upload_file( $file, $args = array() ) {
	global $realteo_upload, $realteo_uploading_file;

	include_once( ABSPATH . 'wp-admin/includes/file.php' );
	include_once( ABSPATH . 'wp-admin/includes/media.php' );

	$args = wp_parse_args( $args, array(
		'file_key'           => '',
		'file_label'         => '',
		'allowed_mime_types' => '',
	) );

	$realteo_upload         = true;
	$realteo_uploading_file = $args['file_key'];
	$uploaded_file              = new stdClass();
	
	$allowed_mime_types = $args['allowed_mime_types'];
	

	/**
	 * Filter file configuration before upload
	 *
	 * This filter can be used to modify the file arguments before being uploaded, or return a WP_Error
	 * object to prevent the file from being uploaded, and return the error.
	 *
	 * @since 1.25.2
	 *
	 * @param array $file               Array of $_FILE data to upload.
	 * @param array $args               Optional file arguments
	 * @param array $allowed_mime_types Array of allowed mime types from field config or defaults
	 */
	$file = apply_filters( 'realteo_upload_file_pre_upload', $file, $args, $allowed_mime_types );

	if ( is_wp_error( $file ) ) {
		return $file;
	}

	if ( ! in_array( $file['type'], $allowed_mime_types ) ) {
		if ( $args['file_label'] ) {
			return new WP_Error( 'upload', sprintf( __( '"%s" (filetype %s) needs to be one of the following file types: %s', 'wp-job-manager' ), $args['file_label'], $file['type'], implode( ', ', array_keys( $allowed_mime_types ) ) ) );
		} else {
			return new WP_Error( 'upload', sprintf( __( 'Uploaded files need to be one of the following file types: %s', 'wp-job-manager' ), implode( ', ', array_keys( $allowed_mime_types ) ) ) );
		}
	} else {
		$upload = wp_handle_upload( $file, apply_filters( 'submit_property_wp_handle_upload_overrides', array( 'test_form' => false ) ) );
		if ( ! empty( $upload['error'] ) ) {
			return new WP_Error( 'upload', $upload['error'] );
		} else {
			$uploaded_file->url       = $upload['url'];
			$uploaded_file->file      = $upload['file'];
			$uploaded_file->name      = basename( $upload['file'] );
			$uploaded_file->type      = $upload['type'];
			$uploaded_file->size      = $file['size'];
			$uploaded_file->extension = substr( strrchr( $uploaded_file->name, '.' ), 1 );
		}
	}

	$realteo_upload         = false;
	$realteo_uploading_file = '';

	return $uploaded_file;
}



/**
 * Returns mime types specifically for WPJM.
 *
 * @since 1.25.1
 * @param   string $field Field used.
 * @return  array  Array of allowed mime types
 */
function realteo_get_allowed_mime_types( $field = '' ){
	
		$allowed_mime_types = array(
			'jpg|jpeg|jpe' => 'image/jpeg',
			'gif'          => 'image/gif',
			'png'          => 'image/png',
			'pdf'          => 'application/pdf',
			'doc'          => 'application/msword',
			'docx'         => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		);
	
	/**
	 * Mime types to accept in uploaded files.
	 *
	 * Default is image, pdf, and doc(x) files.
	 *
	 * @since 1.25.1
	 *
	 * @param array  {
	 *     Array of allowed file extensions and mime types.
	 *     Key is pipe-separated file extensions. Value is mime type.
	 * }
	 * @param string $field The field key for the upload.
	 */
	return apply_filters( 'realteo_mime_types', $allowed_mime_types, $field );
}

/**
 * Outputs the agency location
 *
 * @return void
 */
function the_agency_address( $post = null ) {
	echo get_the_agency_address( $post );
}

/**
 * get_the_agency_address function.
 *
 * @access public
 * @param mixed $post (default: null)
 * @return void
 */
function get_the_agency_address( $post = null ) {
	$post = get_post( $post );
	if ( $post->post_type !== 'agency' ) {
		return;
	}
	
	if(isset($post->_friendly_address) && !empty($post->_friendly_address)) {
		return apply_filters( 'the_agency_friendly_address', $post->_friendly_address, $post );
	} else {
		return apply_filters( 'the_agency_location', $post->_address, $post );
	}
}

function realteo_post_exists( $id ) {
  return is_string( get_post_status( $id ) );
}

function get_available_for_rental_period() {
	$offer_types = realteo_get_offer_types();
	$types = array();
	foreach ($offer_types as $key => $value) {
		
		if(isset($value['period']) && $value['period']) {
			$types[] = $key;
		}
	}
	return $types;
}

if ( ! function_exists('rlt_write_log')) {
   function rlt_write_log ( $log )  {
      if ( is_array( $log ) || is_object( $log ) ) {
         error_log( print_r( $log, true ) );
      } else {
         error_log( $log );
      }
   }
}


function realteo_cmb2_get_user_options( $query_args ) {

    $args = wp_parse_args( $query_args, array(

        'fields' => array( 'user_login' ),

    ) );

    $users = get_users(  );

    $user_options = array();
    if ( $users ) {
        foreach ( $users as $user ) {
          $user_options[ $user->ID ] = $user->user_login;
        }
    }

    return $user_options;
}

function realteo_is_timestamp($timestamp) {

		$check = (is_int($timestamp) OR is_float($timestamp))
			? $timestamp
			: (string) (int) $timestamp;
		return  ($check === $timestamp)
	        	AND ( (int) $timestamp <=  PHP_INT_MAX)
	        	AND ( (int) $timestamp >= ~PHP_INT_MAX);
	}

