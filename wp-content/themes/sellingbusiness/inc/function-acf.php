<?php 
if ( !defined('ABSPATH')) exit;


function custom_acf_init(){
	if( !function_exists('acf_add_local_field_group') ){
		return;
	}
	// TABS FOR OPTION THEME
	if( function_exists('acf_add_options_page') ) {
		acf_add_options_page(array(
			'page_title' 	=> 'Property Types Banner',
			'menu_title'	=> 'Property Types Banner',
			'menu_slug' 	=> 'property-type-settings',
			'capability'	=> 'edit_posts',
			'redirect'		=> false
		));
	}
	
	// get property category from wp option
	$properties =  get_option('realteo_property_types_fields');
    if(!empty($properties)) {
        
        $fields = array();
		
		// Default Tab
			$fields[] = array(
				'key' => 'field_tab_default_999',
				'label' => 'Default',
				'name' => '',
				'type' => 'tab',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'placement' => 'top',
				'endpoint' => 0,
			);
			// default image
			$fields[] = array(
				'key' => 'field_image_888_default',
				'label' => 'Default Image',
				'name' => 'default_property_banner_image',
				'type' => 'image',
				'instructions' => 'This will be the default banner image for all property types',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'return_format' => 'array',
				'preview_size' => 'large',
				'library' => 'all',
				'min_width' => '',
				'min_height' => '',
				'min_size' => '',
				'max_width' => '',
				'max_height' => '',
				'max_size' => '',
				'mime_types' => '',
			);
        foreach ($properties as $indx=>$key ) {
            $id = sanitize_title($key);	
			// Tab
			$fields[] = array(
				'key' => 'field_tab_'.$indx.'_'.$id,
				'label' => $key,
				'name' => '',
				'type' => 'tab',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'placement' => 'top',
				'endpoint' => 0,
			);
			// image
			$fields[] = array(
				'key' => 'field_image_'.$indx.'_'.$id,
				'label' => $key.' Image',
				'name' => $id.'_image',
				'type' => 'image',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'return_format' => 'array',
				'preview_size' => 'large',
				'library' => 'all',
				'min_width' => '',
				'min_height' => '',
				'min_size' => '',
				'max_width' => '',
				'max_height' => '',
				'max_size' => '',
				'mime_types' => '',
			);
			
        }
		
	
		$final_fields = array(
			'key' => 'group_5c3706f22f460',
			'title' => 'Property Categories Banner',
			'fields' => $fields,
			'location' => array(
				array(
					array(
						'param' => 'options_page',
						'operator' => '==',
						'value' => 'property-type-settings',
					),
				),
			),
			'menu_order' => 0,
			'position' => 'normal',
			'style' => 'default',
			'label_placement' => 'top',
			'instruction_placement' => 'label',
			'hide_on_screen' => array(
				0 => 'permalink',
				1 => 'the_content',
				2 => 'excerpt',
				3 => 'discussion',
				4 => 'comments',
				5 => 'revisions',
				6 => 'slug',
				7 => 'author',
				8 => 'format',
				9 => 'page_attributes',
				10 => 'featured_image',
				11 => 'categories',
				12 => 'tags',
				13 => 'send-trackbacks',
			),
			'active' => 1,
			'description' => 'This will appear in search result page',
		);
		
		
		if( function_exists('acf_add_local_field_group') ){
			acf_add_local_field_group($final_fields);
		}
    }
	
	$types =  get_option('realteo_offer_types_fields');
	$cats = array();
	$oprtn = array();
	foreach ($properties as $key ) {
		$cats[sanitize_title($key)] = $key;
	}
	foreach ($types as $key=>$type ) {
		//if($type['front'])
			$oprtn[$key] = $type['name'];
	}
	
	acf_add_local_field_group(array(
		'key' => 'group_5c8c941a66101',
		'title' => 'Saved search',
		'fields' => array(
			array(
				'key' => 'field_5c8c949eb5101',
				'label' => 'Receive Email Alert?',
				'name' => 'receive_email_alert',
				'type' => 'true_false',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'message' => '',
				'default_value' => 0,
				'ui' => 1,
				'ui_on_text' => '',
				'ui_off_text' => '',
			),
			array(
				'key' => 'field_5c8c971347101',
				'label' => 'Name this notification',
				'name' => 'name_this_notification',
				'type' => 'text',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_5c8c949eb5101',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'field_5c8c971f47101',
				'label' => 'Keyword',
				'name' => 'keywords',
				'type' => 'text',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_5c8c949eb5101',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => 'col-lg-6',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => 'e.g. "cafe" or "coffee shop"',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'field_5c8c98bb47101',
				'label' => 'Location',
				'name' => 'locations',
				'type' => 'text',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_5c8c949eb5101',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => 'col-sm-6 col-lg-4',
					'id' => '',
				),
				'default_value' => '',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'field_5c8ca255c9101',
				'label' => ' &nbsp; ',
				'name' => 'kms',
				'type' => 'select',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_5c8c949eb5101',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => 'col-sm-6 col-lg-2',
					'id' => '',
				),
				'choices' => array(
					5 => '5km',
					10 => '10km',
					20 => '20km',
					50 => '50km',
				),
				'default_value' => array(
				),
				'allow_null' => 0,
				'multiple' => 0,
				'ui' => 0,
				'return_format' => 'value',
				'ajax' => 0,
				'placeholder' => '',
			),
			array(
				'key' => 'field_5c8ca60dc0101',
				'label' => 'Opportunities',
				'name' => 'opportunities',
				'type' => 'select',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_5c8c949eb5101',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => 'col-lg-6',
					'id' => '',
				),
				'choices' => $oprtn,
				'default_value' => array(
				),
				'allow_null' => 1,
				'multiple' => 1,
				'ui' => 1,
				'return_format' => 'value',
				'ajax' => 0,
				'placeholder' => 'All franchises & businesses',
			),
			array(
				'key' => 'field_5c8ca7a37d101',
				'label' => 'Industry',
				'name' => 'business_category',
				'type' => 'select',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_5c8c949eb5101',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => 'col-lg-6',
					'id' => '',
				),
				'choices' => $cats,
				'default_value' => array(
				),
				'allow_null' => 1,
				'multiple' => 1,
				'ui' => 1,
				'return_format' => 'value',
				'ajax' => 0,
				'placeholder' => 'Any',
			),
			array(
				'key' => 'field_5c8cabcb7d101',
				'label' => 'Investment From',
				'name' => 'investment_from',
				'type' => 'select',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_5c8c949eb5101',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => 'col-sm-6',
					'id' => '',
				),
				'choices' => array(
					0 => '$0',
					25000 => '$25,000',
					50000 => '$50,000',
					100000 => '$100,000',
					200000 => '$200,000',
					300000 => '$300,000',
					400000 => '$400,000',
					500000 => '$500,000',
					600000 => '$600,000',
					700000 => '$700,000',
					800000 => '$800,000',
					900000 => '$900,000',
					1000000 => '$1,000,000',
					2000000 => '$2,000,000',
				),
				'default_value' => array(
					0 => 0,
				),
				'allow_null' => 0,
				'multiple' => 0,
				'ui' => 0,
				'return_format' => 'value',
				'ajax' => 0,
				'placeholder' => '',
			),
			array(
				'key' => 'field_5c8cac1e7d101',
				'label' => 'Investment To',
				'name' => 'investment_to',
				'type' => 'select',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_5c8c949eb5101',
							'operator' => '==',
							'value' => '1',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => 'col-sm-6',
					'id' => '',
				),
				'choices' => array(
					25000 => '$25,000',
					50000 => '$50,000',
					100000 => '$100,000',
					200000 => '$200,000',
					300000 => '$300,000',
					400000 => '$400,000',
					500000 => '$500,000',
					600000 => '$600,000',
					700000 => '$700,000',
					800000 => '$800,000',
					900000 => '$900,000',
					1000000 => '$1,000,000',
					2000000 => '$2,000,000',
					999999999 => 'Over $2,000,000',
				),
				'default_value' => array(
					0 => 999999999,
				),
				'allow_null' => 0,
				'multiple' => 0,
				'ui' => 0,
				'return_format' => 'value',
				'ajax' => 0,
				'placeholder' => '',
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'user_form',
					'operator' => '==',
					'value' => 'all',
				),
				array(
					'param' => 'user_role',
					'operator' => '==',
					'value' => 'buyer',
				),
			),
		),
		'menu_order' => 0,
		'position' => 'normal',
		'style' => 'default',
		'label_placement' => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen' => '',
		'active' => 1,
		'description' => '',
	));
	
	/*
	if(is_user_logged_in()){
		$current_user = wp_get_current_user();
		acf_register_form(array(
			'id'		=> 'user_'.$current_user->ID,
			//'post_id'	=> 'new_post',
						'field_groups' => array('group_5c8c941a66101'),
			'post_title'=> true,
			'post_content'=> true,
		));
	}
		*/
}
add_action( 'init', 'custom_acf_init' );


function my_acf_google_map_api( $api ){	
	$api['key'] = 'AIzaSyDyFbdcknHUi8tu9IYGqSt-BBmiEUppOSs';	
	return $api;	
}
add_filter('acf/fields/google_map/api', 'my_acf_google_map_api');

function my_acf_init() {	
	acf_update_setting('google_api_key', 'AIzaSyDyFbdcknHUi8tu9IYGqSt-BBmiEUppOSs');
}
add_action('acf/init', 'my_acf_init');

function my_theme_add_scripts() {
    if (is_page('evet')) {
        wp_enqueue_script( 'google-map', 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDyFbdcknHUi8tu9IYGqSt-BBmiEUppOSs', array(), '3', true );
    }
}
//add_action( 'wp_enqueue_scripts', 'my_theme_add_scripts' );
//pr(get_option('realteo_pages'));
//exit();
function setCustomPage($name, $postID) {
	$custom_page = get_option('realteo_pages');
	if($name && $postID){
		unset($custom_page['business-alerts']);
		$custom_page[$name] = $postID;
		update_option( 'realteo_pages', $custom_page );
	}
}
//setCustomPage('business_alerts_page', 4895);

function business_alert_page(){
	$template_loader = new Realteo_Template_Loader;
	ob_start();
	acf_form_head();
	$template_loader->set_template_data( array( 'current' => 'business-alerts' ) )->get_template_part( 'account/navigation' );
	$template_loader->get_template_part( 'account/business_alerts' ); 
	return ob_get_clean();
}
add_shortcode( 'realteo_busines_alert', 'business_alert_page' );