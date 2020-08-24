<?php
/*
 * Plugin Name: Realteo - Forms&Fields Editor
 * Version: 1.0.13
 * Plugin URI: http://www.purethemes.net/
 * Description: Editor for Realteo - Real Estate Plugin from Purethemes.net
 * Author: Purethemes.net
 * Author URI: http://www.purethemes.net/
 * Requires at least: 4.7
 * Tested up to: 4.8.2
 *
 * Text Domain: realteo-fafe
 * Domain Path: /languages/
 *
 * @package WordPress
 * @author Lukasz Girek
 * @since 1.0.0
 */


class Realteo_Forms_And_Fields_Editor {


    /**
     * The main plugin file.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $file;

    /**
     * The main plugin directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $dir;

    /**
     * The plugin assets directory.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_dir;

    /**
     * The plugin assets URL.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $assets_url;

    /**
     * The version number.
     * @var     string
     * @access  public
     * @since   1.0.0
     */
    public $_version;

	/**
     * Initiate our hooks
     * @since 0.1.0
     */
	public function __construct($file = '', $version = '1.0.0') {
        $this->_version = $version;
        add_action( 'admin_menu', array( $this, 'add_options_page' ) ); //create tab pages
        add_action('admin_enqueue_scripts', array( $this, 'enqueue_scripts_and_styles' ) ); 

        // Load plugin environment variables
        $this->file = __FILE__;
        $this->dir = dirname( $this->file );
        $this->assets_dir = trailingslashit( $this->dir ) . 'assets';
        $this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

        include( 'includes/class-realteo-forms-builder.php' );
        include( 'includes/class-realteo-fields-builder.php' );
        include( 'includes/class-realteo-user-fields-builder.php' );
        include( 'includes/class-realteo-submit-builder.php' );
        include( 'includes/class-realteo-import-export.php' );


        $this->forms  = Realteo_Forms_Editor::instance();
        $this->fields  = Realteo_Fields_Editor::instance();
        $this->submit  = Realteo_Submit_Editor::instance();
        $this->users  = Realteo_User_Fields_Editor::instance();
        $this->import_export  = Realteo_Forms_Import_Export::instance();

        add_filter('realteo_get_property_types', array( $this, 'add_property_types_from_option'));
        add_filter('realteo_get_rental_period', array( $this, 'add_rental_period_from_option'));
        add_filter('realteo_get_offer_types', array( $this, 'add_offer_types_from_option'));
        
    }


    public function enqueue_scripts_and_styles(){
        wp_enqueue_script('realteo-fafe-script', esc_url( $this->assets_url ) . 'js/admin.js', array('jquery','jquery-ui-droppable','jquery-ui-draggable', 'jquery-ui-sortable', 'jquery-ui-dialog'));
        
        wp_register_style( 'realteo-fafe-styles', esc_url( $this->assets_url ) . 'css/admin.css', array(), $this->_version );
        wp_enqueue_style( 'realteo-fafe-styles' );
        wp_enqueue_style (  'wp-jquery-ui-dialog');
    }

      /**
     * Add menu options page
     * @since 0.1.0
     */
    public function add_options_page() {        
        
            add_menu_page('Realteo Forms and Fields Editor', 'Realteo Editor', 'manage_options', 'realteo-fields-and-form',array( $this, 'output' ));
               
            //add_submenu_page( 'realteo-fields-and-form', 'Property Fields', 'Property Fields', 'manage_options', 'realte-fields-builder', array( $this, 'output' ));
    }

    public function output(){ 
        $tab = isset( $_GET['tab'] ) ? sanitize_text_field( $_GET['tab'] ) : 'property_types';

        $tabs = array(
            'property_types'          => __( 'Property Types', 'realteo-fafe' ),
            'offer_types'          => __( 'Offer Types', 'realteo-fafe' ),
            'rental_periods'          => __( 'Rental Periods', 'realteo-fafe' ),
        );
        if ( ! empty( $_POST )) { /* add nonce tu*/
            echo $this->form_editor_save($tab); 
        }
        if ( ! empty( $_GET['reset-fields'] ) && ! empty( $_GET['_wpnonce'] ) && wp_verify_nonce( $_GET['_wpnonce'], 'reset' ) ) {
            delete_option( "realteo_{$tab}_fields" );
            echo '<div class="updated"><p>' . __( 'The fields were successfully reset.', 'realteo' ) . '</p></div>';
        }
        switch ($tab) {
            case 'property_types':
                $default_fields = realteo_get_property_types();
                break;
            case 'offer_types':
                $default_fields = realteo_get_offer_types();
                break; 
            case 'rental_periods':
                $default_fields = realteo_get_rental_period();
                break;            
           
            default:
                $default_fields = realteo_get_property_types();
                break;
        }
        
        ?>
        <div class="wrap realteo-form-editor">
        <h2>Realteo Editor</h2>
        <h2 class="nav-tab-wrapper">
            <?php
                foreach( $tabs as $key => $value ) {
                    $active = ( $key == $tab ) ? 'nav-tab-active' : '';
                    echo '<a class="nav-tab ' . $active . '" href="' . admin_url( 'admin.php?page=realteo-fields-and-form&tab=' . esc_attr( $key ) ) . '">' . esc_html( $value ) . '</a>';
                }

            ?>
        </h2>
        
        <form method="post" id="mainform" action="admin.php?page=realteo-fields-and-form&amp;tab=<?php echo esc_attr( $tab ); ?>">
            <div class="realteo-form-editor main-options">
            <table class="widefat fixed">
                    <thead>
                        <tr>
                            <td><h3>Name</h3></td>
                            <?php  if($tab =="offer_types") { ?>
                                <td>
                                    <h3>Value</h3>
                                    <small>Sale and rent values can't be change to not break theme functionality</small>
                                </td>  
                                <td><h3>Show in front-end?</h3>
                                    <small>Check which offer types will be visible in search and submit form</small></td>
                                <td><h3>Available for rental period?</h3>
                                    <small>Check which offer types will enable the rental period field</small></td>
                            <?php } ?>
                            <td></td>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <td colspan="<?php echo ($tab =="offer_types") ? '5' : '2' ;  ?>">
                                <a class=" button-primary add-new-main-option" href="#">Add New</a>
                            </td>
                        </tr>
                    </tfoot>

                    <tbody data-field="<?php
                     if($tab =="offer_types") {
                        echo esc_attr('<tr>
                            <td><input type="text" class="input-text options" name="value[-1]" /></td>
                            <td><input type="text" class="input-text options" name="key[-1]" /></td>
                            <td><input type="checkbox" value="on" name="front[-1]" /></td>
                            <td><input type="checkbox" value="on" name="period[-1]" /></td>
                            <td><a class="remove-row button" href="#">Remove</a></td></tr>');
                     } else {
                        echo esc_attr('<tr><td><input type="text" class="input-text options" name="value[-1]" /></td><td><a class="remove-row button" href="#">Remove</a></td></tr>');
                     } ?>">
                        <?php
                        
                             $i = 0;
                            foreach ($default_fields as $key => $value) {

                            ?>
                            <tr>
                            <?php  if($tab =="offer_types") { ?>
                                
                                <td>
                                    <input type="text" value="<?php echo stripslashes(esc_attr($value['name']));?>" class="input-text options" name="value[<?php echo esc_attr( $i); ?>]" />
                                </td>
                          
                                <td>
                                    <input type="text" value="<?php echo esc_attr($key);?>" class="input-text options" name="key[<?php echo esc_attr( $i); ?>]"
                                    <?php if(in_array($key, array('sale','rent'))) { echo 'readonly'; } ?> />
                                </td>
                                <td>

                                    <input type="checkbox" value="1" name="front[<?php echo esc_attr( $i); ?>]"
                                    <?php 
                                    
                                    if(isset($value['front'])) {
                                        checked($value['front'],1);
                                    } else {
                                        if(in_array($key, array('sale','rent'))) { echo 'checked'; } 
                                    }
                                    ?> 

                                    />
                                </td> 
                                <td>

                                    <input type="checkbox" value="1" name="period[<?php echo esc_attr( $i); ?>]"
                                    <?php 
                                    
                                    if(isset($value['period'])) {
                                        checked($value['period'],1);
                                    } else {
                                        if(in_array($key, array('rent'))) { echo 'checked'; } 
                                    }
                                    ?> 

                                    />
                                </td>
                            <?php } else { ?>
                                <td>
                                    <input type="text" value="<?php echo stripslashes(esc_attr($value));?>" class="input-text options" name="value[<?php echo esc_attr( $i); ?>]" />
                                </td>
                            <?php } ?>
                            <td><a class="remove-row button" href="#">Remove</a></td>
                        </tr>
                            <?php 
                            $i++;
                            }
                         ?>
                    </tbody>
                </table>
                </div>

                <div class="realteo-forms-editor-bottom">
                    <input type="submit" class="save-fields button-primary" value="<?php _e( 'Save Changes', 'realteo' ); ?>" />
                    <a href="<?php echo wp_nonce_url( add_query_arg( 'reset-fields', 1 ), 'reset' ); ?>" class="reset button-secondary"><?php _e( 'Reset to defaults', 'realteo' ); ?></a>
                </div>
        </form>
        <?php        
    }

   
    private function form_editor_save($tab) {
        if($tab == "offer_types") {
            
            $field_name    = ! empty( $_POST['value'] ) ? array_map( 'sanitize_text_field', $_POST['value'] )  : array();
            $field_value   = ! empty( $_POST['key'] ) ? array_map( 'sanitize_text_field', $_POST['key'] )      : array();
            $field_front    = ! empty( $_POST['front'] ) ? array_map( 'sanitize_text_field', $_POST['front'] ) : array();
            $field_period    = ! empty( $_POST['period'] ) ? array_map( 'sanitize_text_field', $_POST['period'] ) : array();
            $new_fields             = array();
            $index                  = 0;

               foreach ( $field_name as $key => $field ) {
       
                if ( empty( $field_name[ $key ] ) ) {
                    continue;
                }
                $name            = sanitize_title( $field_value[ $key ] );
                $new_field                      = array();
                $new_field['name']              = $field_name[ $key ];
                $new_field['value']             = $field_value[ $key ];
                $new_field['front']             = (isset($field_front[ $key ])) ? isset($field_front[ $key ]) : false ;
                $new_field['period']             = (isset($field_period[ $key ])) ? isset($field_period[ $key ]) : false ;
                $new_fields[ $name ]       = $new_field;
            
            }
        } else {
            $values             = ! empty( $_POST['value'] ) ? array_map( 'sanitize_text_field', $_POST['value'] )                     : array();
            $new_fields             = array();
            $index                  = 0;

            foreach ( $values as $key => $field ) {
                if ( empty( $values[ $key ] ) ) {
                    continue;
                }
                $new_fields[]       = $values[ $key ];
            }
        }

        $result = update_option( "realteo_{$tab}_fields", $new_fields );

        if ( true === $result ) {
            echo '<div class="updated"><p>' . __( 'The fields were successfully saved.', 'wp-job-manager-applications' ) . '</p></div>';
        }
    }


function add_property_types_from_option($r){
    $properties =  get_option('realteo_property_types_fields');
    if(!empty($properties)) {
        
        $r = array();
        foreach ($properties as $key ) {
            $id = sanitize_title($key);
            $r[$id] = $key;
        }
    }
    
    return $r;
}

function add_rental_period_from_option($r){
    $properties =  get_option('realteo_rental_periods_fields');
    
    if(!empty($properties)) {
        
        $r = array();
        foreach ($properties as $key ) {

            $id = sanitize_title($key);
            $r[$id] = $key;
        }

    }
    
    return $r;
}

function add_offer_types_from_option($r){
    $offer_types =  get_option('realteo_offer_types_fields');
    if(!empty($offer_types)) {
       
       $r = array();
        foreach ($offer_types as $key => $value ) {
               $r[$value['value']] = array(
                    'name' => $value['name'],
                    'front' => $value['front'],
                    'period' => isset($value['period']) ? $value['period'] : '',
                );
        }
    }
    
    return $r;
}

  


 
}

$Realteo_Form_Editor = new Realteo_Forms_And_Fields_Editor();

