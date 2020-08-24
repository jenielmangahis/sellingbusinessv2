<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Realteo_Fields_Editor {

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
       
        add_filter('realteo_price_fields', array( $this,'add_realteo_price_fields_from_editor')); 
        add_filter('realteo_main_details_fields', array( $this,'add_realteo_main_details_fields_from_editor')); 
        add_filter('realteo_details_fields', array( $this,'add_realteo_details_fields_from_editor')); 
        add_filter('realteo_location_fields', array( $this,'add_realteo_location_fields_from_editor')); 
    }

    function add_realteo_price_fields_from_editor($fields) {
        $new_fields =  get_option('realteo_price_tab_fields');
        if(!empty($new_fields)) {
            $fields['fields'] = $new_fields;
        }
        return $fields;
    }    

    function add_realteo_main_details_fields_from_editor($fields) {
        $new_fields =  get_option('realteo_main_details_tab_fields');
        if(!empty($new_fields)) {
            $fields['fields'] = $new_fields;
        }
        return $fields;
    }    

    function add_realteo_details_fields_from_editor($fields) {
        $new_fields =  get_option('realteo_details_tab_fields');
        if(!empty($new_fields)) {
            $fields['fields'] = $new_fields;
        }
        return $fields;
    }

    function add_realteo_location_fields_from_editor($fields) {
        $new_fields =  get_option('realteo_locations_tab_fields');
        if(!empty($new_fields)) {
            $fields['fields'] = $new_fields;
        }
        return $fields;
    }

    /**
     * Add menu options page
     * @since 0.1.0
     */
    public function add_options_page() {        
         add_submenu_page( 'realteo-fields-and-form', 'Fields', 'Fields', 'manage_options', 'realteo-fields-builder', array( $this, 'output' )); 
    }
    public function output(){

        $tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'price_tab';

        $tabs = array(
            'price_tab'                 => __( 'Price tab fields', 'realteo-fafe' ),
            'main_details_tab'          => __( 'Main Details fields', 'realteo-fafe' ),
            'details_tab'               => __( 'Details tab', 'realteo-fafe' ),
            'locations_tab'             => __( 'Locations tab', 'realteo-fafe' ),
            //'custom_tab'             => __( 'Custom fields tab', 'realteo-fafe' ),
        );

        if ( ! empty( $_GET['reset-fields'] ) && ! empty( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'reset' ) ) {
            delete_option( "realteo_{$tab}_fields" );
            echo '<div class="updated"><p>' . __( 'The fields were successfully reset.', 'realteo' ) . '</p></div>';
        }
        
        if ( ! empty( $_POST )) { /* add nonce tu*/
          
            echo $this->form_editor_save($tab); 
        }


        $field_types = apply_filters( 'realteo_form_field_types', 
        array(
            'text'              => __( 'Text', 'realteo-editor' ),
            'textarea'          => __( 'Textarea', 'realteo-editor' ),
            'select'            => __( 'Select', 'realteo-editor' ),
            'select_multiple'   => __( 'Multi Select', 'realteo-editor' ),
            'checkbox'          => __( 'Checkbox', 'realteo-editor' ),
            'multicheck_split'  => __( 'Multi Checkbox', 'realteo-editor' ),
            'file'              => __( 'File upload', 'realteo-editor' ),
        ) );

        $predefined_options = apply_filters( 'realteo_predefined_options', array(
            'realteo_get_property_types'     => __( 'Property Types list', 'realteo-editor' ),
            'realteo_get_offer_types_flat'        => __( 'Offer Types list', 'realteo-editor' ),
            'realteo_get_rental_period'         => __( 'Rental Period list', 'realteo-editor' ),
        ) );
        switch ($tab) {
            case 'price_tab':
                $default_fields = Realteo_Meta_Boxes::meta_boxes_price();
                break;
            case 'main_details_tab':
                $default_fields = Realteo_Meta_Boxes::meta_boxes_main_details();
                break;            
            case 'details_tab':
                $default_fields = Realteo_Meta_Boxes::meta_boxes_details();
                break;            
            case 'locations_tab':
                $default_fields = Realteo_Meta_Boxes::meta_boxes_location();
                break;
            
            default:
                $default_fields = Realteo_Meta_Boxes::meta_boxes_price();
                break;
        }

        $options = get_option("realteo_{$tab}_fields");

        $fields = (!empty($options)) ? get_option("realteo_{$tab}_fields") : $default_fields; 
            if(isset($fields['fields'])) {
                $fields = $fields['fields'];
            }

        ?>
        <div class="wrap realteo-form-editor">
        <h2>Realteo Property Fields editor</h2>
        <h2 class="nav-tab-wrapper">
            <?php
                foreach( $tabs as $key => $value ) {
                    $active = ( $key == $tab ) ? 'nav-tab-active' : '';
                    echo '<a class="nav-tab ' . $active . '" href="' . admin_url( 'admin.php?page=realteo-fields-builder&tab=' . esc_attr( $key ) ) . '">' . esc_html( $value ) . '</a>';
                }
            ?>
        </h2>
        <form method="post" id="mainform" action="admin.php?page=realteo-fields-builder&amp;tab=<?php echo esc_attr( $tab ); ?>">
            <div class="realteo-forms-builder-top">
                <div class="form-editor-container" id="realteo-fafe-fields-editor" data-clone="<?php
                ob_start();
                $index = -2;
                $field_key = 'clone';
                $field = array(
                    'name' => 'clone',
                    'id' => '_clone',
                    'type' => 'text',
                    'invert' => '',
                    'desc' => '',
                    'options_source' => '',
                    'options_cb' => '',
                    'options' => array()
                ); ?>
                <div class="form_item" data-priority="<?php echo  $index; ?>">
                    <span class="handle dashicons dashicons-editor-justify"></span>
                    <div class="element_title"><?php echo esc_attr( $field['name'] );  ?> <span>(<?php echo $field['type']; ?>)</span> </div>
                    <?php include( plugin_dir_path( __DIR__  ) . 'views/form-field-edit.php' ); ?>
                    <div class="remove_item"> Remove </div>
                </div>
                <?php echo esc_attr( ob_get_clean() ); ?>">

                    <?php
                    $index = 0;

                    foreach ( $fields as $field_key => $field ) {
                        $index++;
                     
                        if(is_array($field)){ ?>
                            <div class="form_item">
                                <span class="handle dashicons dashicons-editor-justify"></span>
                                <div class="element_title"><?php echo esc_attr( $field['name'] );  ?> 
                                    <div class="element_title_edit"><span class="dashicons dashicons-edit"></span> Edit</div>
                                </div>
                                <?php include( plugin_dir_path( __DIR__  ) . 'views/form-field-edit.php' ); ?>
                                <div class="remove_item"> Remove </div>
                            </div>
                        <?php }
                    }  ?>
                    <div class="droppable-helper"></div>
                </div>
                <a class="add_new_item button-primary add-field" href="#"><?php _e( 'Add field', 'realteo' ); ?></a>
            </div>
                
            <?php wp_nonce_field( 'save-' . $tab ); ?>
            
            <div class="realteo-forms-builder-bottom">
                
                <input type="submit" class="save-fields button-primary" value="<?php _e( 'Save Changes', 'realteo' ); ?>" />
                <a href="<?php echo wp_nonce_url( add_query_arg( 'reset-fields', 1 ), 'reset' ); ?>" class="reset button-secondary"><?php _e( 'Reset to defaults', 'realteo' ); ?></a>
            </div>
            </form>
        </div>
       
        <?php wp_nonce_field( 'save-fields' ); ?>
        <?php
    }



    private function form_editor_save($tab) {
     
        $field_name             = ! empty( $_POST['name'] ) ? array_map( 'sanitize_textarea_field', $_POST['name'] )                     : array();
        $field_id               = ! empty( $_POST['id'] ) ? array_map( 'sanitize_text_field', $_POST['id'] )                         : array();
        $field_type             = ! empty( $_POST['type'] ) ? array_map( 'sanitize_text_field', $_POST['type'] )                     : array();
        $field_invert             = ! empty( $_POST['invert'] ) ? array_map( 'sanitize_text_field', $_POST['invert'] )                     : array();
        $field_desc             = ! empty( $_POST['desc'] ) ? array_map( 'sanitize_text_field', $_POST['desc'] )                    : array();
        $field_options_cb       = ! empty( $_POST['options_cb'] ) ? array_map( 'sanitize_text_field', $_POST['options_cb'] )        : array();
        $field_options_source   = ! empty( $_POST['options_source'] ) ? array_map( 'sanitize_text_field', $_POST['options_source'] ): array();
        $field_options          = ! empty( $_POST['options'] ) ? $this->sanitize_array( $_POST['options'] )                : array();
        $new_fields             = array();
        $index                  = 0;
    
       foreach ( $field_name as $key => $field ) {
           
            if ( empty( $field_name[ $key ] ) ) {
                continue;
            }
            $name            = sanitize_title( $field_id[ $key ] );
            $options        = array();
            if(! empty( $field_options[ $key ] )){
                foreach ($field_options[ $key ] as $op_key => $op_value) {
                    $options[$op_value['name']] = $op_value['value'];
                } 
            }

            $new_field                      = array();
            $new_field['name']              = stripslashes($field_name[ $key ]);
            $new_field['id']                = $field_id[ $key ];
            $new_field['type']              = $field_type[ $key ];
            $new_field['invert']            = isset($field_invert[ $key ]) ? $field_invert[ $key ] : false;
            $new_field['desc']              = $field_desc[ $key ];
            $new_field['options_source']    = $field_options_source[ $key ];
            $new_field['options_cb']        = $field_options_cb[ $key ];
            if(!empty($field_options_cb[ $key ])) {
                $new_field['options']           = array();
            } else {
                $new_field['options']           = $options;
            }

            $new_fields[ $name ]       = $new_field;
            
        }
      
        $result = update_option( "realteo_{$tab}_fields", $new_fields );

        if ( true === $result ) {
            echo '<div class="updated"><p>' . __( 'The fields were successfully saved.', 'realteo-editor' ) . '</p></div>';
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