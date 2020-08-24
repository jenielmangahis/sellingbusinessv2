<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Realteo_Submit_Editor {
/**
     * Stores static instance of class.
     *
     * @access protected
     * @var Realteo_Submit The single instance of the class
     */
    protected static $_instance = null;

    /**
     * Returns static instance of class.
     *
     * @return self
     */
    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public function __construct($version = '1.0.0') {
  
       	add_action( 'admin_menu', array( $this, 'add_options_page' ) ); //create tab pages
      	add_filter('submit_property_form_fields', array( $this,'add_realteo_submit_property_form_fields_form_editor')); 
       
    }

    function add_realteo_submit_property_form_fields_form_editor($r){
        $fields =  get_option('realteo_submit_form_fields');
        if(!empty($fields)) { $r['property'] = $fields; }
        return $r;
      }

     /**
     * Add menu options page
     * @since 0.1.0
     */
    public function add_options_page() {        
         add_submenu_page( 'realteo-fields-and-form', 'Submit Form', 'Submit Form', 'manage_options', 'realteo-submit-builder', array( $this, 'output' )); 
    }


    public function output(){

    	 $predefined_options = apply_filters( 'realteo_predefined_options', array(
                'realteo_get_property_types'     => __( 'Property Types list', 'wp-job-manager-applications' ),
                'realteo_get_offer_types_flat'        => __( 'Offer Types list', 'wp-job-manager-applications' ),
                'realteo_get_rental_period'         => __( 'Rental Period list', 'wp-job-manager-applications' ),
            ) );

    	$scale = realteo_get_option( 'scale', 'sq ft' );
    	$defaults = array(
				'header1' => array(
					'label'       => __( 'Basic Information', 'realteo' ),
					'type'        => 'header',
					'name'		  => 'header1',
					'priority'    => 1,
				),
				'property_title' => array(
					'label'       => __( 'Property Title', 'realteo' ),
					'type'        => 'text',
					'name'       => 'property_title',
					'tooltip'	  => __( 'Type title that will also contains an unique feature of your property (e.g. renovated, air contidioned)', 'realteo' ),
					'required'    => true,
					'placeholder' => '',
					'class'		  => '',
					'priority'    => 2
				),
				'_offer_type' => array(
					'label'       	 => __( 'Offer', 'realteo' ),
					'name'       	 => '_offer_type',
					'type'       	 => 'select',
					'options_source' => 'predefined',
					'options_cb'     =>  'realteo_get_offer_types_flat', //realteo_get_offer_types_flat(false),
					'class'		  	 => 'chosen-select-no-single',
					'default'    	 => 'sale',
					'before_row' 	 => '<div class="row with-forms">',
					'render_row_col' => '4',
					'priority'    	 => 3,
					'required'    	 => false,
				),						
				'_rental_period' => array(
					'label'       	 => __( 'Rental Period', 'realteo' ),
					'type'       	 => 'select',
					'name'       	 => '_rental_period',
					'options_source' => 'predefined',
					'options_cb'     => 'realteo_get_rental_period', //realteo_get_rental_period(),
					'class'		  	 => 'chosen-select-no-single',
					'default'    	 => 'monthly',
					'render_row_col' => '4',
					'priority'    	 => 4,
					'required'    	 => false,
				),				
				'_property_type' => array(
					'label'       	 => __( 'Property Type', 'realteo' ),
					'type'       	 => 'select',
					'name'       	 => '_property_type',
					'options_source' => 'predefined',
					'options_cb'   	 => 'realteo_get_property_types', //realteo_get_property_types(),
					'class'		  	 => 'chosen-select-no-single',
					'default'    	 => 'house',
					'after_row' 	 => '</div>',
					'render_row_col' => '4',
					'priority'    	 => 5,
					'required'    => false,
				),
				'_price' => array(
					'label'       	=> __( 'Price', 'realteo' ),
					'type'        	=> 'text',
					'name'       	 => '_price',
					'tooltip'	  	=> __( 'Type overall or monthly price if property is for rent', 'realteo' ),
					'required'   	=> true,
					'placeholder'	=> '',
					'class'		  	=> '',
					'unit'		  	=> realteo_get_option( 'currency' ),
					'priority'    	=> 6,
					'before_row' 	 => '<div class="row with-forms">',
					'render_row_col' => '4'
				),				
				'_area' => array(
					'label'       => __( 'Area', 'realteo' ),
					'type'        => 'text',
					'name'        => '_area',
					'required'    => false,
					'placeholder' => '',
					'class'		  => '',
					'unit'		  => apply_filters('realteo_scale',$scale),
					'priority'    => 7,
					'render_row_col' => '4'
				),
				'_rooms' => array(
					'label'       	=> __( 'Rooms', 'realteo' ),
					'type'       	=> 'select',
					'name'       	 => '_rooms',
					'options_source' => 'custom',
					'options'   => array(
						'1' 	 => __( '1', 'realteo' ),
						'2' 	 => __( '2', 'realteo' ),
						'3' 	 => __( '3', 'realteo' ),
						'4' 	 => __( '4', 'realteo' ),
						'5+' 	 => __( '5+', 'realteo' ),
					),
					'class'		  	 => 'chosen-select-no-single',
					'default'    	 => '1',
					'after_row' 	 => '</div>',
					'render_row_col' => '4',
					'priority'    	 => 8,
					'required'    => false,
				),
				'_video' => array(
					'label'       => __( 'Video', 'realteo' ),
					'type'        => 'text',
					'name'        => '_video',
					'required'    => false,
					'placeholder' => 'URL to oEmbed supported service',
					'class'		  => '',
					'priority'    => 5,
				),
				'_header_gallery' => array(
					'label'       => __( 'Gallery', 'realteo' ),
					'name'       => '_header_gallery',
					'type'        => 'header',
				),
				'_gallery' => array(
					'label'       => __( 'Gallery', 'realteo' ),
					'name'       => '_gallery',
					'type'        => 'files',
					'description' => __( 'By selecting (clicking on a photo) one of the uploaded photos you will set it as Featured Image for this property (marked by icon with star). Drag and drop thumbnails to re-order images in gallery.', 'realteo' ),
					'placeholder' => 'Upload images',
					'class'		  => '',
					'priority'    => 9,
					'required'    => false,
				),				
				'_thumbnail_id' => array(
					'label'       => __( 'Thumbnail ID', 'realteo' ),
					'type'        => 'hidden',
					'name'        => '_thumbnail_id',
					'class'		  => '',
					'priority'    => 10,
					'required'    => false,
				),
				'header_location' => array(
					'label'       => __( 'Location', 'realteo' ),
					'type'        => 'map',
					'name'		  => 'header_location'
				),
				'address' => array(
					'label'       => __( 'Google Maps Address', 'realteo' ),
					'type'        => 'text',
					'required'    => true,
					'name'        => '_address',
					'placeholder' => '',
					'class'		  => '',
					'before_row' 	 => '<div class="row with-forms">',
					'priority'    => 11,
					'render_row_col' => '6'
				),				
				'friendly_address' => array(
					'label'       => __( 'Friendly Address', 'realteo' ),
					'type'        => 'text',
					'required'    => false,
					'name'        => '_friendly_address',
					'placeholder' => '',
					'tooltip'	  => __('Human readable address, if not set, the Google address will be used', 'realteo'),
					'class'		  => '',
					'after_row' 	 => '</div>',
					'priority'    => 12,
					'render_row_col' => '6'
				),				
				'geolocation_long' => array(
					'label'       => __( 'Longitude', 'realteo' ),
					'type'        => 'text',
					'required'    => true,
					'placeholder' => '',
					'name'        => '_geolocation_long',
					'class'		  => '',
					'before_row' 	 => '<div class="row with-forms">',
					'priority'    => 13,
					'render_row_col' => '3'
				),				
				'geolocation_lat' => array(
					'label'       => __( 'Latitude', 'realteo' ),
					'type'        => 'text',
					'required'    => true,
					'placeholder' => '',
					'name'        => '_geolocation_lat',
					'class'		  => '',
					'priority'    => 14,
					'render_row_col' => '3'
				),
				'region' => array(
					'label'       => __( 'Region', 'realteo' ),
					'type'        => 'term-select',
					'placeholder' => '',
					'name'        => 'region',
					'taxonomy'	  => 'region',
					'after_row'   => '</div>',
					'priority'    => 15,
					'default'	  => '',
					'render_row_col' => '6',
					'required'    => false,
				),

				/**/
				'header3' => array(
					'label'       => __( 'Detailed Information', 'realteo' ),
					'type'        => 'header',
					'name'        => 'header3',
					'priority'    	 => 16,
				),
				'property_description' => array(
					'label'       => __( 'Description', 'realteo' ),
					'type'        => 'wp-editor',
					'name'        => 'property_description',
					'required'    => true,
					'placeholder' => '',
					'priority'    => 17
				),
				'_building_age' => array(
					'label'       	=> __( 'Building age', 'realteo' ),
					'type'       	=> 'select',
					'name'        => '_building_age',
					'options_source' => 'custom',
					'options'    	=> array(
						'0 - 1 Years' => __( '0 - 1 Years', 'realteo' ),
						'2 - 5 Years' => __( '2 - 5 Years', 'realteo' ),
						'6 - 10 Years' => __( '6 - 10 Years', 'realteo' ),
						'11 - 20 Years' => __( '11 - 20 Years', 'realteo' ),
						'21 - 50 Years' => __( '21 - 50 Years', 'realteo' ),
					),
					'class'		  	 => 'chosen-select-no-single',
					'default'    	 => '',
					'before_row' 	 => '<div class="row with-forms">',
					'render_row_col' => '4',
					'priority'    	 => 18,
					'required'    => false,
				),
				'_bedrooms' => array(
					'label'       	=> __( 'Bedrooms', 'realteo' ),
					'type'       	=> 'select',
					'name'        => '_bedrooms',
					'options_source' => 'custom',
					'options'    	=> array(
						'1'  => __( '1', 'realteo' ),
						'2'  => __( '2', 'realteo' ),
						'3'  => __( '3', 'realteo' ),
						'4'  => __( '4', 'realteo' ),
						'5+' => __( '5+', 'realteo' ),
					),
					'class'		  	 => 'chosen-select-no-single',
					'default'    	 => 'sale',
					'render_row_col' => '4',
					'priority'    	 => 19,
					'required'    => false,
				),				
				'_bathrooms' => array(
					'label'       	=> __( 'Bathrooms', 'realteo' ),
					'type'       	=> 'select',
					'name'			=> '_bathrooms',
					'options_source' => 'custom',
					'options'    	=> array(
						'1'  => __( '1', 'realteo' ),
						'2'  => __( '2', 'realteo' ),
						'3'  => __( '3', 'realteo' ),
						'4'  => __( '4', 'realteo' ),
						'5+' => __( '5', 'realteo' ),
					),
					'class'		  	 => 'chosen-select-no-single',
					'default'    	 => 'sale',
					'after_row' 	 => '</div>',
					'render_row_col' => '4',
					'priority'    	 => 20,
					'required'    => false,
				),

				'_parking' => array(
					'label'       => __( 'Parking', 'realteo' ),
					'type'        => 'text',
					'required'    => false,
					'name'			=> '_parking',
					'placeholder' => '',
					'class'		  => '',
					'before_row' 	 => '<div class="row with-forms">',
					'priority'    => 21,
					'render_row_col' => '4'
				),				
				'_cooling' => array(
					'label'       => __( 'Cooling', 'realteo' ),
					'type'        => 'text',
					'name'			=> '_cooling',
					'required'    => false,
					'placeholder' => '',
					'class'		  => '',
					'priority'    => 22,
					'render_row_col' => '4'
				),
				'_heating' => array(
					'label'       => __( 'Heating', 'realteo' ),
					'type'        => 'text',
					'name'			=> '_heating',
					'required'    => false,
					'placeholder' => '',
					'class'		  => '',
					'after_row' 	 => '</div>',
					'priority'    => 23,
					'render_row_col' => '4'
				),

				'_sewer' => array(
					'label'       => __( 'Sewer', 'realteo' ),
					'type'        => 'text',
					'required'    => false,
					'placeholder' => '',
					'name'			=> '_sewer',
					'class'		  => '',
					'before_row' 	 => '<div class="row with-forms">',
					'priority'    => 24,
					'render_row_col' => '3'
				),				
				'_water' => array(
					'label'       => __( 'Water', 'realteo' ),
					'type'        => 'text',
					'required'    => false,
					'name'			=> '_water',
					'placeholder' => '',
					'class'		  => '',
					'priority'    => 25,
					'render_row_col' => '3'
				),
				'_exercise_room' => array(
					'label'       => __( 'Exercise Room', 'realteo' ),
					'type'        => 'text',
					'required'    => false,
					'name'			=> '_exercise_room',
					'placeholder' => '',
					'class'		  => '',
					'priority'    => 26,
					'render_row_col' => '3'
				),				
				'_storage_room' => array(
					'label'       => __( 'Storage Room', 'realteo' ),
					'type'        => 'text',
					'required'    => false,
					'name'			=> '_storage_room',
					'placeholder' => '',
					'class'		  => '',
					'after_row' 	 => '</div>',
					'priority'    => 27,
					'render_row_col' => '3'
				),

				'property_feature' => array(
					'label'       	=> __( 'Other Features', 'realteo' ),
					'type'        	=> 'term-checkboxes',
					'taxonomy'		=> 'property_feature',
					'name'			=> 'property_feature',
					'class'		  	 => 'chosen-select-no-single',
					'default'    	 => '',
					'priority'    	 => 28,
					'required'    => false,
				),
				'header4' => array(
					'label'       => __( 'Floorplans', 'realteo' ),
					'type'        => 'header',
					'required'    => false,
				),
				'_floorplans' => array(
					'label'       => __( 'Floorplans', 'realteo' ),
					'name'       => '_floorplans',
					'type'        => 'floorplans',
					'placeholder' => 'Upload floorplans',
					'class'		  => '',
					'priority'    => 1,
					'required'    => false,
				),	
			);

	 		if ( ! empty( $_GET['reset-fields'] ) && ! empty( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'reset' ) ) {
                delete_option("realteo_submit_form_fields");
                echo '<div class="updated"><p>' . __( 'The fields were successfully reset.', 'realteo' ) . '</p></div>';
            }
      


            if ( ! empty( $_POST )) { /* add nonce tu*/
              
                echo $this->form_editor_save(); 
            }
            
	    	$options = get_option("realteo_submit_form_fields");
	        $search_fields = (!empty($options)) ? get_option("realteo_submit_form_fields") : $defaults;

       
         ?>
			<h2>Realteo Submit Form editor</h2>
			<div class="wrap realteo-forms-builder clearfix">
                               
                <form method="post" id="mainform" action="admin.php?page=realteo-submit-builder">
               
                <div class="realteo-forms-builder-left">
                    <div class="form-editor-container" id="realteo-fafe-forms-editor"> 
                        <?php
                        $index = 0;
                        foreach ( $search_fields as $field_key => $field ) {

                            $index++;
                            if(is_array($field)){ ?>
                                <div class="form_item form_item_<?php echo $field_key; ?> form_item_<?php echo $field['type']; ?>" data-priority="<?php echo  $index; ?>">
                                    <span class="handle dashicons dashicons-editor-justify"></span>
                                    <div class="element_title"><?php echo  esc_attr( $field['label'] );  ?>  
                                        <div class="element_title_edit"><span class="dashicons dashicons-edit"></span> Edit</div>
                                    </div>
                                    <?php include( plugin_dir_path( __DIR__  ) .  'views/form-edit-submit.php' ); ?>

                                    <?php if( 
                                    	isset($field['name']) && 
                                    	!in_array( $field['name'], array('property_title','thumbnail','property_description') ) ) { ?>
                                        <div class="remove_item"> Remove </div>
                                    <?php } ?>

                                </div>
                            <?php }
                        }  ?>
                        <div class="droppable-helper"></div>
                    </div>
                  
                    <input type="submit" class="save-fields button-primary" value="<?php _e( 'Save Changes', 'realteo' ); ?>" />
           
                    <a href="<?php echo wp_nonce_url( add_query_arg( 'reset-fields', 1 ), 'reset' ); ?>" class="reset button-secondary"><?php _e( 'Reset to defaults', 'realteo' ); ?></a>
                </div>
                <?php wp_nonce_field( 'save-fields' ); ?>
                <?php wp_nonce_field( 'save'); ?>
        </form>
<div class="realteo-forms-builder-right">
                    <h3>Available Submit Form Elements</h3>
                    
                    <div class="form-editor-available-elements-container">
                        <h4>Visual fields:</h4>
                        <?php 
                        $visual_fields = array(
								'header' => array(
									'name' => __( 'Header', 'realteo' ),
									'id'   => '_header',
									'type' => 'header',
									'desc' => __( '', 'realteo' ),
								),
								'map' => array(
									'name' => __( 'Map', 'realteo' ),
									'id'   => '_map',
									'type' => 'map',
									'desc' => __( '', 'realteo' ),
								),
								'_floorplans' => array(
									'label'       => __( 'Floorplans', 'realteo' ),
									'name'       => __( 'Floorplans', 'realteo' ),
									'id'       => '_floorplans',
									'type'        => 'floorplans',
									'placeholder' => 'Upload floorplans',
								),
								'_gallery' => array(
									'label'       	=> __( 'Gallery', 'realteo' ),
									'name'       	=> __( 'Gallery', 'realteo' ),
									'id'       		=> '_gallery',
									'type'        	=> 'files',
									'description' 	=> __( 'By selecting (clicking on a photo) one of the uploaded photos you will set it as Featured Image for this property (marked by icon with star). Drag and drop thumbnails to re-order images in gallery.', 'realteo' ),
									'placeholder' => 'Upload images',
								),	
						);
                        foreach ($visual_fields as $key => $field) { 
                             $index++;
                        ?>
                        <div class="form_item form_item_<?php echo $key; ?>" data-priority="0">
                            <span class="handle dashicons dashicons-editor-justify"></span>
                             <div class="element_title"><?php echo  esc_attr( $field['name'] );  ?> <div class="element_title_edit"><span class="dashicons dashicons-edit"></span> Edit</div></div>
                            <?php include( plugin_dir_path( __DIR__  ) .  'views/form-edit-submit-ready-regular.php' ); ?>
                            <div class="remove_item"> Remove </div>
                        </div>
                        <?php } 
                        ?>

                        <h4>Taxonomies:</h4>
                        <?php 
                        $taxonomy_objects = get_object_taxonomies( 'property', 'objects' );
                        foreach ($taxonomy_objects as $tax) {
                             $index++;
                        ?>
                        <div class="form_item" data-priority="0">
                            <span class="handle dashicons dashicons-editor-justify"></span>
                            <div class="element_title"><?php echo  esc_attr( $tax->label );  ?> <div class="element_title_edit"><span class="dashicons dashicons-edit"></span> Edit</div></div>
                            <?php include( plugin_dir_path( __DIR__  ) .  'views/form-edit-submit-ready-tax.php' ); ?>
                            <div class="remove_item"> Remove </div>
                        </div>
                        <?php } 
                        ?>
                    
                   
                    <h4>Custom Field</h4>
                    <?php  
                    $price_fields = Realteo_Meta_Boxes::meta_boxes_price();
                    foreach ($price_fields['fields'] as $key => $field) {  $index++;?>
                        <div class="form_item" data-priority="0">
                            <span class="handle dashicons dashicons-editor-justify"></span>
                            <div class="element_title"><?php echo  esc_attr( $field['name'] );  ?> <div class="element_title_edit"><span class="dashicons dashicons-edit"></span> Edit</div></div>
                            <?php  include( plugin_dir_path( __DIR__  ) .  'views/form-edit-submit-ready-field.php' ); ?>
                            <div class="remove_item"> Remove </div>
                        </div>
                        <?php } 
                    ?>               
                    <?php  
                    $main_details = Realteo_Meta_Boxes::meta_boxes_main_details();
                    foreach ($main_details['fields'] as $key => $field) {  $index++;?>
                        <div class="form_item" data-priority="0">
                            <span class="handle dashicons dashicons-editor-justify"></span>
                            <div class="element_title"><?php echo  esc_attr( $field['name'] );  ?> <div class="element_title_edit"><span class="dashicons dashicons-edit"></span> Edit</div></div>
                            <?php  include( plugin_dir_path( __DIR__  ) .  'views/form-edit-submit-ready-field.php' ); ?>
                            <div class="remove_item"> Remove </div>
                        </div>
                        <?php } 
                    ?>                    
                    <?php  
                    $details = Realteo_Meta_Boxes::meta_boxes_details();
                    foreach ($details['fields'] as $key => $field) {  $index++;?>
                        <div class="form_item" data-priority="0">
                            <span class="handle dashicons dashicons-editor-justify"></span>
                            <div class="element_title"><?php echo  esc_attr( $field['name'] );  ?> <div class="element_title_edit"><span class="dashicons dashicons-edit"></span> Edit</div></div>
                            <?php include( plugin_dir_path( __DIR__  ) .  'views/form-edit-submit-ready-field.php' ); ?>
                            <div class="remove_item"> Remove </div>
                        </div>
                        <?php } 
                    ?>                    
                    <?php  
                    $location = Realteo_Meta_Boxes::meta_boxes_location();
                    foreach ($location['fields'] as $key => $field) {  $index++;?>
                        <div class="form_item" data-priority="0">
                            <span class="handle dashicons dashicons-editor-justify"></span>
                            <div class="element_title"><?php echo  esc_attr( $field['name'] );  ?> <div class="element_title_edit"><span class="dashicons dashicons-edit"></span> Edit</div></div>
                            <?php include( plugin_dir_path( __DIR__  ) .  'views/form-edit-submit-ready-field.php' ); ?>
                            <div class="remove_item"> Remove </div>
                        </div>
                        <?php } 
                    ?>
                    </div>

                  
                </div>
                </div>
        <?php
    }

     /**
     * Save the form fields
     */
    private function form_editor_save() {

        $field_type             = ! empty( $_POST['type'] ) ? array_map( 'sanitize_text_field', $_POST['type'] )                    : array();
        $field_name             = ! empty( $_POST['name'] ) ? array_map( 'sanitize_text_field', $_POST['name'] )                    : array();
        $field_label            = ! empty( $_POST['label'] ) ? array_map( 'sanitize_text_field', $_POST['label'] )                  : array();
        $field_multi            = ! empty( $_POST['multi'] ) ? array_map( 'sanitize_text_field', $_POST['multi'] )                  : array();
        $field_placeholder      = ! empty( $_POST['placeholder'] ) ? array_map( 'sanitize_text_field', $_POST['placeholder'] )      : array();
        $field_tooltip      	= ! empty( $_POST['tooltip'] ) ? array_map( 'sanitize_text_field', $_POST['tooltip'] )      : array();
        $field_description      = ! empty( $_POST['description'] ) ? array_map( 'sanitize_text_field', $_POST['description'] )      : array();

        $field_required         = ! empty( $_POST['required'] ) ? array_map( 'sanitize_text_field', $_POST['required'] ) : array();

        $field_render_row_col  = ! empty( $_POST['render_row_col'] ) ? array_map( 'sanitize_text_field', $_POST['render_row_col'] )                  : array();

        $field_class            = ! empty( $_POST['class'] ) ? array_map( 'sanitize_text_field', $_POST['class'] )                  : array();
    
        $field_before_row         = ! empty( $_POST['before_row'] ) ? array_map( 'sanitize_text_field', $_POST['before_row'] )            : array();
        $field_after_row        = ! empty( $_POST['after_row'] ) ? array_map( 'sanitize_text_field', $_POST['after_row'] )          : array();
        
        $field_unit            = ! empty( $_POST['unit'] ) ? array_map( 'sanitize_text_field', $_POST['unit'] )                  : array();

        $field_priority         = ! empty( $_POST['priority'] ) ? array_map( 'sanitize_text_field', $_POST['priority'] )            : array();
       
        $field_taxonomy         = ! empty( $_POST['field_taxonomy'] ) ? array_map( 'sanitize_text_field', $_POST['field_taxonomy'] ): array();
        $field_default         = ! empty( $_POST['default'] ) ? array_map( 'sanitize_text_field', $_POST['default'] ): array();
        
        $field_options_cb       = ! empty( $_POST['options_cb'] ) ? array_map( 'sanitize_text_field', $_POST['options_cb'] )        : array();
        $field_options_source   = ! empty( $_POST['options_source'] ) ? array_map( 'sanitize_text_field', $_POST['options_source'] ): array();
        $field_options          = ! empty( $_POST['options'] ) ? $this->sanitize_array( $_POST['options'] )                         : array();
        $new_fields             = array();
        $index                  = 0;

       foreach ( $field_label as $key => $field ) {
          
      
            $name                = sanitize_title( $field_name[ $key ] );
            $options             = array();
            if(! empty( $field_options[ $key ] )){
                foreach ($field_options[ $key ] as $op_key => $op_value) {
                    $options[$op_value['name']] = $op_value['value'];
                } 
            }
            $new_field                       = array();
            $new_field['type']               = $field_type[ $key ];
            $new_field['name']               = $field_name[ $key ];
            $new_field['multi']              = isset($field_multi[ $key ]) ? $field_multi[ $key ] : false;
            $new_field['label']              = $field_label[ $key ];
            $new_field['placeholder']        = $field_placeholder[ $key ];
            $new_field['tooltip']            = $field_tooltip[ $key ];
            $new_field['description']        = $field_description[ $key ];
            $new_field['class']              = $field_class[ $key ];
            $new_field['render_row_col']     = $field_render_row_col[ $key ];
            $new_field['required']           = isset($field_required[ $key ]) ? $field_required[ $key ] : false;

            $new_field['before_row']         = isset($field_before_row[ $key ]) ? '<div class="row with-forms">' : false;
            $new_field['after_row']          = isset($field_after_row[ $key ]) ? '</div>' : false;
            $new_field['unit']               = $field_unit[ $key ];
            $new_field['taxonomy']           = $field_taxonomy[ $key ];
            $new_field['default']            = $field_default[ $key ];
            $new_field['options_source']     = $field_options_source[ $key ];
            $new_field['options_cb']         = $field_options_cb[ $key ];
            if(!empty($field_options_cb[ $key ])) {
                $new_field['options']           = array();
            } else {
                $new_field['options']           = $options;
            }
            $new_field['priority']           = $index ++;
            
            $new_fields[ $name ]            = $new_field;
            
        }

        $result = update_option( "realteo_submit_form_fields", $new_fields);
        

        if ( true === $result ) {
            echo '<div class="updated"><p>' . __( 'The fields were successfully saved.', 'wp-job-manager-applications' ) . '</p></div>';
        }
      
    }

    /**
     * Sanitize a 2d array
     * @param  array $array
     * @return array
     */
    private function sanitize_array( $input ) {
        if ( is_array( $input ) ) {
            foreach ( $input as $k => $v ) {
                $input[ $k ] = $this->sanitize_array( $v );
            }
            return $input;
        } else {
            return sanitize_text_field( $input );
        }
    }
}