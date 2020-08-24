<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Realteo_Forms_Editor {

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
       add_filter('realteo_search_fields', array( $this,'add_realteo_search_fields_form_editor')); 
       add_filter('realteo_search_fields_fw', array( $this,'add_realteo_search_fields_fw_form_editor')); 
       add_filter('realteo_search_fields_half', array( $this,'add_realteo_search_fields_half_form_editor')); 
       add_filter('realteo_search_fields_home', array( $this,'add_realteo_search_fields_home_form_editor')); 
       add_filter('realteo_search_fields_home_alt', array( $this,'add_realteo_search_fields_home_alt_form_editor')); 

    }

    function add_realteo_search_fields_form_editor($r){
        $fields =  get_option('realteo_sidebar_search_form_fields');
        if(!empty($fields)) { $r = $fields; }
        return $r;
    }

    function add_realteo_search_fields_fw_form_editor($r){
        $fields =  get_option('realteo_full_width_search_form_fields');
        if(!empty($fields)) { $r = $fields; }
        return $r;
    }    

    function add_realteo_search_fields_half_form_editor($r){
        $fields = get_option('realteo_search_on_half_map_form_fields');
        if(!empty($fields)) { $r = $fields; }
        return $r;
    }    

    function add_realteo_search_fields_home_form_editor($r){
        $fields = get_option('realteo_search_on_home_page_form_fields');
        if(!empty($fields)) { $r = $fields; }
        return $r;
    }    

    function add_realteo_search_fields_home_alt_form_editor($r){
        $fields = get_option('realteo_search_on_home_page_alt_form_fields');

        if(!empty($fields)) { $r = $fields; }
        return $r;
    }

    /**
     * Add menu options page
     * @since 0.1.0
     */
    public function add_options_page() {        
         add_submenu_page( 'realteo-fields-and-form', 'Search Forms', 'Search Forms', 'manage_options', 'realteo-forms-builder', array( $this, 'output' )); 
    }


    public function output(){
                
            $tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'sidebar_search';

            $tabs = array(
                'sidebar_search'            => __( 'Sidebar Search', 'realteo-fafe' ),
                'search_on_home_page'       => __( 'Search on Home Page', 'realteo-fafe' ),
                'full_width_search'         => __( 'Full Width Search', 'realteo-fafe' ),
                'search_on_half_map'        => __( 'Search on Half Map', 'realteo-fafe' ),
                'search_on_home_page_alt'   => __( 'Search on Home Page Alt', 'realteo-fafe' ),
        
            );
            $predefined_options = apply_filters( 'realteo_predefined_options', array(
                'realteo_get_property_types'     => __( 'Property Types list', 'wp-job-manager-applications' ),
                'realteo_get_offer_types'        => __( 'Offer Types list', 'wp-job-manager-applications' ),
                'realteo_get_rental_period'         => __( 'Rental Period list', 'wp-job-manager-applications' ),
            ) );

            if ( ! empty( $_GET['reset-fields'] ) && ! empty( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'reset' ) ) {
                delete_option("realteo_{$tab}_form_fields");
                echo '<div class="updated"><p>' . __( 'The fields were successfully reset.', 'realteo' ) . '</p></div>';
            }
      


            if ( ! empty( $_POST )) { /* add nonce tu*/
                echo $this->form_editor_save($tab); 
            }
            

            switch ( $tab ) {
                case 'sidebar_search' :
                    $default_fields = Realteo_Search::get_search_fields();
                break;
                case 'full_width_search' :
                    $default_fields = Realteo_Search::get_search_fields_fw();
                break;
                case 'search_on_half_map' :
                    $default_fields =  Realteo_Search::get_search_fields_half();
                break;
                case 'search_on_home_page' :
                    $default_fields = Realteo_Search::get_search_fields_home();
                break;
                case 'search_on_home_page_alt' :
                    $default_fields = Realteo_Search::get_search_fields_home_alt();
                break;
                default :
                    $default_fields = Realteo_Search::get_search_fields();
                break;
            }
            $options = get_option("realteo_{$tab}_form_fields");
            $search_fields = (!empty($options)) ? get_option("realteo_{$tab}_form_fields") : $default_fields; 

        ?>
        <h2>Realteo Property Forms editor</h2>
        <h2 class="nav-tab-wrapper">
            <?php
                foreach( $tabs as $key => $value ) {
                    $active = ( $key == $tab ) ? 'nav-tab-active' : '';
                    echo '<a class="nav-tab ' . $active . '" href="' . admin_url( 'admin.php?page=realteo-forms-builder&tab=' . esc_attr( $key ) ) . '">' . esc_html( $value ) . '</a>';
                }
            ?>
           
        </h2>
        <div class="wrap realteo-forms-builder clearfix">
                               
                <form method="post" id="mainform" action="admin.php?page=realteo-forms-builder&amp;tab=<?php echo esc_attr( $tab );?>">

                <div class="realteo-forms-builder-left">
                    <div class="form-editor-container" id="realteo-fafe-forms-editor">
                    <?php if('search_on_home_page' == $tab) { ?>
                    <div id="realteo_home_search_options">
                        <h4>Form Options:</h4>
                        <div class="form_item">
                            
                            <input type="checkbox" id="offer_types_tabs" <?php $checked = get_option('realteo_home_offer_types_tabs');checked( $checked, 1,true ); ?> value="on" name="offer_types_tabs">
                            <label for="offer_types_tabs">Remove offer types tabs</label>
                            <p class="desc">Activate the checkbox to remove offer tabs over the form</p>
                        </div>
                        <div class="form_item">
                            
                            <input type="checkbox" id="home_search_first_row" <?php  $checked = get_option('realteo_home_search_first_row');checked( $checked, 1,true ); ?> value="on" name="home_search_first_row">
                            <label for="home_search_first_row">Remove first row of search form</label>
                            <p class="desc">Activate the checkbox to remove default keywoard and submit buttons, you can add your own to the draggable list below.</p>
                        </div>
                        <div class="form_item">
                            
                            <input type="checkbox" id="home_search_any_status" <?php  $checked = get_option('realteo_home_search_any_status');checked( $checked, 1,true ); ?> value="on" name="home_search_any_status">
                            <label for="home_search_any_status">Remove "Any Status" from offer types</label>
                            <p class="desc">Activate the checkbox to remove "Any Status" option from the search form.</p>
                        </div>
                    </div>
                    <h4>Form Fields:</h4>
                    <?php } ?>

        <?php
            $index = 0;
            foreach ( $search_fields as $field_key => $field ) {
                $index++;
                if(is_array($field)){ ?>
                    <div class="form_item form_item_<?php echo $field_key; ?>" data-priority="<?php echo  $index; ?>">
                        <span class="handle dashicons dashicons-editor-justify"></span>
                        <div class="element_title"><?php echo  esc_attr( $field['placeholder'] );  ?>  
                            <div class="element_title_edit"><span class="dashicons dashicons-edit"></span> Edit</div>
                        </div>
                        <?php include( plugin_dir_path( __DIR__  ) .  'views/form-edit.php' ); ?>

                        <?php if(isset($field['name']) && $field['name'] != 'realteo_order') { ?>
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
 <button id="realteo-show-names" class="button">Show fields names</button> (adv users only)
                <div class="realteo-forms-builder-right">

                    <h3>Available searchable elements</h3>
                   
                    <div class="form-editor-available-elements-container">
                        <h4>Standard elements:</h4>
                        <?php 
                        $visual_fields = array(
                            'header' => array(
                                'class'         => '',
                                'open_row'      => false,
                                'close_row'     => false,
                                'name'          => 'Header',
                                'id'            => 'header',
                                'place'         => 'main',
                                'type'          => 'header',
                                'placeholder'       => __( 'Header/separator', 'realteo' ),
                            ),  
                            'submit' => array(
                                'class'         => '',
                                'open_row'      => false,
                                'close_row'     => false,
                                'name'          => 'submit',
                                'id'          => 'submit',
                                'place'         => 'main',
                                'type'          => 'submit',
                                'placeholder'       => __( 'Search button', 'realteo' ),
                            ),  
                            'keyword_search' => array(
                                'placeholder'   => __( 'Enter address', 'realteo' ),
                                'key'           => '_keyword_search',
                                'class'         => '',
                                'open_row'      => false,
                                'close_row'     => false,
                                'id'          => 'keyword_search',
                                'name'          => 'keyword_search',
                                'priority'      => 1,
                                'place'         => 'main',
                                'type'          => 'location',
                            ),   
                            'search_radius' => array(
                                'placeholder'   => __( 'Radius search', 'realteo' ),
                                'id'           => 'search_radius',
                                'name'           => __( 'Radius search', 'realteo' ),
                                'class'         => 'col-md-4',
                                'open_row'      => true,
                                'close_row'     => false,
                                'priority'      => 1,
                                'place'         => 'main',
                                'type'          => 'input-select',
                                'min'           => 10,
                                'max'           => 150,
                                'step'          => 10,
                                'unit'          => realteo_get_option( 'radius_unit','km' ),

                            ),
                            
   
                        );
                        foreach ($visual_fields as $key => $field) { 
                             $index++;
                        ?>
                        <div class="form_item" data-priority="0">
                            <span class="handle dashicons dashicons-editor-justify"></span>
                            <div class="element_title"><?php echo  $field['placeholder'];  ?> <div class="element_title_edit"><span class="dashicons dashicons-edit"></span> Edit</div></div>
                            <?php include( plugin_dir_path( __DIR__  ) .  'views/form-edit-ready-field.php' ); ?>
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
                            <?php include( plugin_dir_path( __DIR__  ) .  'views/form-edit-ready-tax.php' ); ?>
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
                            <?php  include( plugin_dir_path( __DIR__  ) .  'views/form-edit-ready-field.php' ); ?>
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
                            <?php  include( plugin_dir_path( __DIR__  ) .  'views/form-edit-ready-field.php' ); ?>
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
                            <?php include( plugin_dir_path( __DIR__  ) .  'views/form-edit-ready-field.php' ); ?>
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
                            <?php include( plugin_dir_path( __DIR__  ) .  'views/form-edit-ready-field.php' ); ?>
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
    private function form_editor_save($tab) {

        if(isset($_POST['offer_types_tabs'])){
            update_option( "realteo_home_offer_types_tabs", true);
        } else {
            delete_option("realteo_home_offer_types_tabs");
        }
        if(isset($_POST['home_search_first_row'])){
             update_option( "realteo_home_search_first_row", true);
        } else {
            delete_option("realteo_home_search_first_row");
        }

        if(isset($_POST['home_search_any_status'])){
             update_option( "realteo_home_search_any_status", true);
        } else {
            delete_option("realteo_home_search_any_status");
        }

        $field_type             = ! empty( $_POST['type'] ) ? array_map( 'sanitize_text_field', $_POST['type'] )                    : array();
        $field_name             = ! empty( $_POST['name'] ) ? array_map( 'sanitize_text_field', $_POST['name'] )                    : array();
        //$field_label            = ! empty( $_POST['label'] ) ? array_map( 'sanitize_text_field', $_POST['label'] )                  : array();
        $field_placeholder      = ! empty( $_POST['placeholder'] ) ? array_map( 'wp_kses_post', $_POST['placeholder'] )             : array();
        $field_class            = ! empty( $_POST['class'] ) ? array_map( 'sanitize_text_field', $_POST['class'] )                  : array();
        $field_css_class        = ! empty( $_POST['css_class'] ) ? array_map( 'sanitize_text_field', $_POST['css_class'] )          : array();
        $field_multi            = ! empty( $_POST['multi'] ) ? array_map( 'sanitize_text_field', $_POST['multi'] )                  : array();
        $field_open_row         = ! empty( $_POST['open_row'] ) ? array_map( 'sanitize_text_field', $_POST['open_row'] )            : array();
        $field_close_row        = ! empty( $_POST['close_row'] ) ? array_map( 'sanitize_text_field', $_POST['close_row'] )          : array();
        $field_priority         = ! empty( $_POST['priority'] ) ? array_map( 'sanitize_text_field', $_POST['priority'] )            : array();
        $field_place            = ! empty( $_POST['place'] ) ? array_map( 'sanitize_text_field', $_POST['place'] )                  : array();
        $field_taxonomy         = ! empty( $_POST['field_taxonomy'] ) ? array_map( 'sanitize_text_field', $_POST['field_taxonomy'] ): array();
        $field_max              = ! empty( $_POST['max'] ) ? array_map( 'sanitize_text_field', $_POST['max'] )                      : array();
        $field_min              = ! empty( $_POST['min'] ) ? array_map( 'sanitize_text_field', $_POST['min'] )                      : array();
        $field_step             = ! empty( $_POST['step'] ) ? array_map( 'sanitize_text_field', $_POST['step'] )                    : array();
        $field_unit             = ! empty( $_POST['unit'] ) ? array_map( 'sanitize_text_field', $_POST['unit'] )                    : array();
        $field_options_cb       = ! empty( $_POST['options_cb'] ) ? array_map( 'sanitize_text_field', $_POST['options_cb'] )        : array();
        $field_options_source   = ! empty( $_POST['options_source'] ) ? array_map( 'sanitize_text_field', $_POST['options_source'] ): array();
        $field_options          = ! empty( $_POST['options'] ) ? $this->sanitize_array( $_POST['options'] )                         : array();
        $new_fields             = array();
        $index                  = 0;

       foreach ( $field_name as $key => $field ) {
          
      
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
            //$new_field['label']              = $field_label[ $key ];
            $new_field['placeholder']        = $field_placeholder[ $key ];
            $new_field['class']              = $field_class[ $key ];
            $new_field['css_class']          = $field_css_class[ $key ];
            $new_field['multi']              = isset($field_multi[ $key ]) ? $field_multi[ $key ] : false;
            $new_field['open_row']           = isset($field_open_row[ $key ]) ? $field_open_row[ $key ] : false;
            $new_field['close_row']          = isset($field_close_row[ $key ]) ? $field_close_row[ $key ] : false;
            $new_field['priority']           = $field_priority[ $key ];
            $new_field['place']              = $field_place[ $key ];
            $new_field['taxonomy']           = $field_taxonomy[ $key ];
            $new_field['max']                = $field_max[ $key ];
            $new_field['min']                = $field_min[ $key ];
            $new_field['step']               = $field_step[ $key ];
            $new_field['unit']               = $field_unit[ $key ];
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

        $result = update_option( "realteo_{$tab}_form_fields", $new_fields);
        

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

