<?php
/**
 * CMB Tabbed Theme Options
 *
 * @author    Arushad Ahmed <@dash8x, contact@arushad.org>
 * @link      http://arushad.org/how-to-create-a-tabbed-options-page-for-your-wordpress-theme-using-cmb
 * @version   0.1.0
 */
class Realteo_Admin {

    /**
     * Default Option key
     * @var string
     */
    private $key = 'realteo_options';

    /**
     * Array of metaboxes/fields
     * @var array
     */
    protected $option_metabox = array();

    /**
     * Options Page title
     * @var string
     */
    protected $title = '';

    /**
     * Options Tab Pages
     * @var array
     */
    protected $options_pages = array();

    /**
     * Constructor
     * @since 0.1.0
     */
    public function __construct() {
        // Set our title
        $this->title = __( 'Realteo Options', 'realteo' );
    }

    /**
     * Initiate our hooks
     * @since 0.1.0
     */
    public function hooks() {
        add_action( 'admin_init', array( $this, 'init' ) );
        add_action( 'admin_menu', array( $this, 'add_options_page' ) ); //create tab pages
    }

    /**
     * Register our setting tabs to WP
     * @since  0.1.0
     */
    public function init() {
    	$option_tabs = self::option_fields();
        foreach ($option_tabs as $index => $option_tab) {
        	register_setting( $option_tab['id'], $option_tab['id'] );
        }
    }

    /**
     * Add menu options page
     * @since 0.1.0
     */
    public function add_options_page() {        
        $option_tabs = self::option_fields();
        foreach ($option_tabs as $index => $option_tab) {
        	if ( $index == 0) {
        		$this->options_pages[] = add_menu_page( $this->title, $this->title, 'manage_options', $option_tab['id'], array( $this, 'admin_page_display' ) ); //Link admin menu to first tab
        		add_submenu_page( $option_tabs[0]['id'], $this->title, $option_tab['title'], 'manage_options', $option_tab['id'], array( $this, 'admin_page_display' ) ); //Duplicate menu link for first submenu page
        	} else {
        		$this->options_pages[] = add_submenu_page( $option_tabs[0]['id'], $this->title, $option_tab['title'], 'manage_options', $option_tab['id'], array( $this, 'admin_page_display' ) );
        	}
        }
    }

    /**
     * Admin page markup. Mostly handled by CMB
     * @since  0.1.0
     */
    public function admin_page_display() {
    	$option_tabs = self::option_fields(); //get all option tabs
    	$tab_forms = array();     	   	
        ?>
        <div class="wrap cmb_options_page <?php echo $this->key; ?>">        	
            <h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
            
            <!-- Options Page Nav Tabs -->           
            <h2 class="nav-tab-wrapper">
            	<?php foreach ($option_tabs as $option_tab) :
            		$tab_slug = $option_tab['id'];
            		$nav_class = 'nav-tab';
            		if ( $tab_slug == $_GET['page'] ) {
            			$nav_class .= ' nav-tab-active'; //add active class to current tab
            			$tab_forms[] = $option_tab; //add current tab to forms to be rendered
            		}
            	?>            	
            	<a class="<?php echo $nav_class; ?>" href="<?php menu_page_url( $tab_slug ); ?>"><?php esc_attr_e($option_tab['title']); ?></a>
            	<?php endforeach; ?>
            </h2>
            <!-- End of Nav Tabs -->

            <?php foreach ($tab_forms as $tab_form) : //render all tab forms (normaly just 1 form) ?>
            <div id="<?php esc_attr_e($tab_form['id']); ?>" class="group">
            	<?php cmb2_metabox_form( $tab_form, $tab_form['id'] ); ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

    /**
     * Defines the theme option metabox and field configuration
     * @since  0.1.0
     * @return array
     */
    public function option_fields() {

        // Only need to initiate the array once per page-load
        if ( ! empty( $this->option_metabox ) ) {
            return $this->option_metabox;
        }
                
        $property_types = realteo_get_property_types();
        $property_default_types = implode(',', $property_types);

        $this->option_metabox[] = array(
            'id'         => 'realteo_general_options', //id used as tab page slug, must be unique
            'title'      => 'General Options',
            'show_on'    => array( 'key' => 'options-page', 'value' => array( 'general_options' ), ), //value must be same as id
            'show_names' => true,
            'fields'     => array(
				array(
					'name' 		=> __('Currency', 'realteo'),
					'desc' 		=> __('Choose a currency used.', 'realteo'),
					'id' 		=> 'currency', //each field id must be unique
					'type' 		=> 'select',
					'options'	=> array(
							'none' => esc_html__( 'Disable Currency Symbol', 'realteo' ),
							'USD' => esc_html__( 'US Dollars', 'realteo' ),
							'AED' => esc_html__( 'United Arab Emirates Dirham', 'realteo' ),
							'ARS' => esc_html__( 'Argentine Peso', 'realteo' ),
							'AUD' => esc_html__( 'Australian Dollars', 'realteo' ),
							'BDT' => esc_html__( 'Bangladeshi Taka', 'realteo' ),
							'BHD' => esc_html__( 'Bahraini Dinar', 'realteo' ),
							'BRL' => esc_html__( 'Brazilian Real', 'realteo' ),
							'BGN' => esc_html__( 'Bulgarian Lev', 'realteo' ),
							'CAD' => esc_html__( 'Canadian Dollars', 'realteo' ),
							'CLP' => esc_html__( 'Chilean Peso', 'realteo' ),
							'CNY' => esc_html__( 'Chinese Yuan', 'realteo' ),
							'COP' => esc_html__( 'Colombian Peso', 'realteo' ),
							'CZK' => esc_html__( 'Czech Koruna', 'realteo' ),
							'DKK' => esc_html__( 'Danish Krone', 'realteo' ),
                            'DOP' => esc_html__( 'Dominican Peso', 'realteo' ),
							'MAD' => esc_html__( 'Moroccan Dirham', 'realteo' ),
                            'EUR' => esc_html__( 'Euros', 'realteo' ),
							'GHS' => esc_html__( 'Ghanaian Cedi', 'realteo' ),
							'HKD' => esc_html__( 'Hong Kong Dollar', 'realteo' ),
							'HRK' => esc_html__( 'Croatia kuna', 'realteo' ),
							'HUF' => esc_html__( 'Hungarian Forint', 'realteo' ),
							'ISK' => esc_html__( 'Icelandic krona', 'realteo' ),
							'IDR' => esc_html__( 'Indonesia Rupiah', 'realteo' ),
							'INR' => esc_html__( 'Indian Rupee', 'realteo' ),
							'NPR' => esc_html__( 'Nepali Rupee', 'realteo' ),
							'ILS' => esc_html__( 'Israeli Shekel', 'realteo' ),
                            'JPY' => esc_html__( 'Japanese Yen', 'realteo' ),
                            'JOD' => esc_html__( 'Jordanian Dinar', 'realteo' ),
							'KZT' => esc_html__( 'Kazakhstani tenge', 'realteo' ),
							'KIP' => esc_html__( 'Lao Kip', 'realteo' ),
							'KRW' => esc_html__( 'South Korean Won', 'realteo' ),
							'LKR' => esc_html__( 'Sri Lankan Rupee', 'realteo' ),
							'MYR' => esc_html__( 'Malaysian Ringgits', 'realteo' ),
							'MXN' => esc_html__( 'Mexican Peso', 'realteo' ),
							'NGN' => esc_html__( 'Nigerian Naira', 'realteo' ),
							'NOK' => esc_html__( 'Norwegian Krone', 'realteo' ),
							'NZD' => esc_html__( 'New Zealand Dollar', 'realteo' ),
							'PYG' => esc_html__( 'Paraguayan Guaraní', 'realteo' ),
							'PHP' => esc_html__( 'Philippine Pesos', 'realteo' ),
							'PLN' => esc_html__( 'Polish Zloty', 'realteo' ),
							'GBP' => esc_html__( 'Pounds Sterling', 'realteo' ),
							'RON' => esc_html__( 'Romanian Leu', 'realteo' ),
							'RUB' => esc_html__( 'Russian Ruble', 'realteo' ),
							'SGD' => esc_html__( 'Singapore Dollar', 'realteo' ),
							'ZAR' => esc_html__( 'South African rand', 'realteo' ),
							'SEK' => esc_html__( 'Swedish Krona', 'realteo' ),
							'CHF' => esc_html__( 'Swiss Franc', 'realteo' ),
							'TWD' => esc_html__( 'Taiwan New Dollars', 'realteo' ),
							'THB' => esc_html__( 'Thai Baht', 'realteo' ),
							'TRY' => esc_html__( 'Turkish Lira', 'realteo' ),
							'UAH' => esc_html__( 'Ukrainian Hryvnia', 'realteo' ),
							'USD' => esc_html__( 'US Dollars', 'realteo' ),
							'VND' => esc_html__( 'Vietnamese Dong', 'realteo' ),
							'EGP' => esc_html__( 'Egyptian Pound', 'realteo' ),
							'ZMK' => esc_html__( 'Zambian Kwacha', 'realteo' )
						),
					'default'		=> 'USD'
				),		
                array(
                    'name'      => __('Currency position', 'realteo'),
                    'desc'      => __('Set currency symbol before or after', 'realteo'),
                    'id'        => 'currency_postion',
                    'type'      => 'radio',
                    'options'   => array( 
                            'after' => 'After', 
                            'before' => 'Before' 
                        ),
                    'default'   => 'after'
                ),				
                array(
					'name' 		=> __('Scale', 'realteo'),
					'desc' 		=> __('Choose a scale', 'realteo'),
					'id' 		=> 'scale',
					'type'		=> 'select',
					'options'	=> array( 
                            'sq m' => esc_html__( 'Square meter', 'realteo' ),
                            'm²' => esc_html__( 'Square meter(m²)', 'realteo' ),
                            'sq ft' => esc_html__( 'Square feet', 'realteo' ),
                        ),
					'default'	=> 'sq m'
				),
                array(
                    'name'      => __('Agents contact form', 'realteo'),
                    'desc'      => __('Choose which form will be displayed over the gallery if this style is used', 'realteo'),
                    'id'        => 'agent_form',
                    'type'      => 'select',
                    'options'   => $this->get_cf_forms(),
                ),
                  array(
                    'name'      => __('Agency contact form', 'realteo'),
                    'desc'      => __('Choose which form will be displayed in single Agency view', 'realteo'),
                    'id'        => 'agency_form',
                    'type'      => 'select',
                    'options'   => $this->get_cf_forms(),
                ),
                array(
                    'name'      => __('Property name autocomplete', 'realteo'),
                    'desc'      => __('Enable it to use property name autocomplete instead of Google Maps locations autocomplete on keyword search', 'realteo'),
                    'id'        => 'realteo_search_name_autocomplete',
                    'type'      => 'checkbox',
                ),  
                 array(
                    'name'      => __('Combine text search results with geolocalization search results', 'realteo'),
                    'desc'      => __('By enabling this option, results page will have all properties searched by a radius and all properties with matching title/content/custom field', 'realteo'),
                    'id'        => 'include_text_search',
                    'std'        => 'on',
                    'type'      => 'checkbox',
                ), 
				array(
                    'name' => __( 'Gooogle Maps API key', 'realteo' ),
                    'desc' => __( 'Generate API key for google maps functionality (can be domain restricted).', 'realteo' ),
                    'id'   => 'realteo_maps_api', //field id must be unique
                    'type' => 'text',
                ),

                array(
                    'name' => __( 'Google Maps API key for server side geocoding', 'realteo' ),
                    'desc' => __( 'Generate API key for google maps functionality without any domain/key restriction.', 'realteo' ),
                    'id'   => 'realteo_maps_api_server', //field id must be unique
                    'type' => 'text',
                ),
                 array(
                    'name'      => __('Radius search unit', 'realteo'),
                    'desc'      => __('Choose a unit', 'realteo'),
                    'id'        => 'radius_unit',
                    'type'      => 'select',
                    'options'   => array( 
                            'km' => esc_html__( 'km', 'realteo' ),
                            'miles' => esc_html__( 'miles', 'realteo' ),
                        ),
                    'default'   => 'km'
                ), 
                 array(
                    'name' => __( 'Default radius search value', 'realteo' ),
                    'desc' => __( 'Set default radius for search, leave empty to disable default radius search.', 'realteo' ),
                    'id'   => 'realteo_maps_default_radius', //field id must be unique
                    'type' => 'text',
                    'default'   => 50
                ),
                 array(
                    'name' => __( 'Restrict search results to one country', 'realteo' ),
                    'desc' => __( 'Put symbol of country you want to restrict your results to (eg. uk for United Kingdon). Leave empty to search whole world.', 'realteo' ),
                    'id'   => 'realteo_maps_limit_country', //field id must be unique
                    'type' => 'text',
                ),
                array(
                    'name' => __( 'Properties map center point', 'realteo' ),
                    'desc' => __( 'Write latitude and longitude separated by come, for example -34.397,150.644', 'realteo' ),
                    'id'   => 'realteo_map_center_point', //field id must be unique
                    'type' => 'text',
                    'default' => "52.2296756,21.012228700000037",    
                ),
                array(
                    'name'      => __('By default sort properties by:', 'realteo'),
                    'desc'      => __('sort by', 'realteo'),
                    'id'        => 'realteo_sort_by',
                    'type'      => 'select',
                    'options'   => array( 
                            'date-asc' => esc_html__( 'Oldest Properties', 'realteo' ),
                            'date-desc' => esc_html__( 'Newest Properties', 'realteo' ),
                            'featured' => esc_html__( 'Featured', 'realteo' ),
                            'price-asc' => esc_html__( 'Price Low to High', 'realteo' ),
                            'price-desc' => esc_html__( 'Price High to Low', 'realteo' ),
                            'rand' => esc_html__( 'Random', 'realteo' ),
                        ),
                    'default'   => 'date-desc'
                ),
                array(
                    'name'      => __('Enable front-end login and registration', 'realteo'),
                    'desc'      => __('Check to enable redirection to front-end forms', 'realteo'),
                    'id'        => 'realteo_front_end_login',
                    'type'      => 'checkbox',
                ),  

                array(
                    'name'      => __('Hide price per scale under Property Price', 'realteo'),
                    'desc'      => __("Enabling this field will hide the price per scale, but won't hide the rental period  ", 'realteo'),
                    'id'        => 'realteo_hide_price_per_scale',
                    'type'      => 'checkbox',
                ),  
                array(
                    'name'      => __('Hide Listing Footer on properties list', 'realteo'),
                    'desc'      => __("Enabling this field will hide the property section that shows property author and date", 'realteo'),
                    'id'        => 'realteo_hide_listing_footer',
                    'type'      => 'checkbox',
                ), 

                array(
                    'name'      => esc_html__( 'Placeholder Image', 'cmb2' ),
                    'desc'      => esc_html__( 'This image will be used in various places (map markers, compare table etc) if the property does not have any Featured Image or Gallery added.', 'cmb2' ),
                    'id'        => 'realteo_placeholder',
                    'type'      => 'file',
                ),
                   array(
                    'name'      => __('Region in property permalinks', 'realteo'),
                    'desc'      => __('By enabling this option the links to properties will <br> be prepended  with regions (e.g /property/las-vegas/arlo-apartment/).<br> After enabling this go to Settings-> Permalinks and click \' Save Changes \' ', 'realteo'),
                    'id'        => 'region_in_links',
                    'type'      => 'checkbox',
                ), 
                
			)
       );		
        $this->option_metabox[] = array(
            'id'         => 'realteo_pages',
            'title'      => 'Pages',
            'show_on'    => array( 'key' => 'options-page', 'value' => array( 'pages' ) ),
            'show_names' => true,
            'fields'     => array(
                array(
                    'name'          => __('My Account Page', 'realteo'),
                    'desc'          => __('Select page that holds [realteo_my_account] shortcode', 'realteo'),
                    'id'            =>  'my_account_page',
                    'type'          => 'select',
                    'options_cb'    => 'realteo_cmb2_get_pages_options',
                ),               
                array(
                    'name'          => __('Bookmarks Page', 'realteo'),
                    'desc'          => __('Select page that holds [realteo_bookmarks] shortcode', 'realteo'),
                    'id'            =>  'bookmarks_page',
                    'type'          => 'select',
                    'options_cb'    => 'realteo_cmb2_get_pages_options',
                ),                
                array(
                    'name'          => __('My Properties Page', 'realteo'),
                    'desc'          => __('Select page that holds [realteo_my_properties] shortcode', 'realteo'),
                    'id'            =>  'my_properties_page',
                    'type'          => 'select',
                    'options_cb'    => 'realteo_cmb2_get_pages_options',
                ),                  
                array(
                    'name'          => __('Compare Properties Page', 'realteo'),
                    'desc'          => __('Select page that holds [realteo_compare] shortcode', 'realteo'),
                    'id'            =>  'compare_page',
                    'type'          => 'select',
                    'options_cb'    => 'realteo_cmb2_get_pages_options',
                ),                
                array(
                    'name'          => __('Submit Property Page', 'realteo'),
                    'desc'          => __('Select page that holds [realteo_submit_property] shortcode', 'realteo'),
                    'id'            =>  'submit_property_page',
                    'type'          => 'select',
                    'options_cb'    => 'realteo_cmb2_get_pages_options',
                ),                 
                array(
                    'name'          => __('Property Packages Page', 'realteo'),
                    'desc'          => __('Select page that holds [realteo_my_packages] shortcode', 'realteo'),
                    'id'            =>  'property_packages_page',
                    'type'          => 'select',
                    'options_cb'    => 'realteo_cmb2_get_pages_options',
                ),                
                array(
                    'name'          => __('Change Password Page', 'realteo'),
                    'desc'          => __('Select page that holds [realteo_change_password] shortcode', 'realteo'),
                    'id'            =>  'change_password_page',
                    'type'          => 'select',
                    'options_cb'    => 'realteo_cmb2_get_pages_options',
                ),     
                array(
                    'name'          => __('Lost Password Page', 'realteo'),
                    'desc'          => __('Select page that holds [realteo_lost_password] shortcode', 'realteo'),
                    'id'            =>  'lost_password_page',
                    'type'          => 'select',
                    'options_cb'    => 'realteo_cmb2_get_pages_options',
                ),                
                array(
                    'name'          => __('Reset Password Page', 'realteo'),
                    'desc'          => __('Select page that holds [realteo_reset_password] shortcode', 'realteo'),
                    'id'            =>  'reset_password_page',
                    'type'          => 'select',
                    'options_cb'    => 'realteo_cmb2_get_pages_options',
                ),
                array(
                    'name'          => __('Agency Management Page', 'realteo'),
                    'desc'          => __('Select page that holds [realteo_agency_managment] shortcode', 'realteo'),
                    'id'            =>  'agency_page',
                    'type'          => 'select',
                    'options_cb'    => 'realteo_cmb2_get_pages_options',
                ),
                array(
                    'name'          => __('Agency Submit/Edit Page', 'realteo'),
                    'desc'          => __('Select page that holds [realteo_agency_submit] shortcode', 'realteo'),
                    'id'            =>  'agency_submit_page',
                    'type'          => 'select',
                    'options_cb'    => 'realteo_cmb2_get_pages_options',
                ),
                array(
                    'name'          => __('My Orders Page', 'realteo'),
                    'desc'          => __('Select page that holds [realteo_my_orders] shortcode', 'realteo'),
                    'id'            =>  'my_orders_page',
                    'type'          => 'select',
                    'options_cb'    => 'realteo_cmb2_get_pages_options',
                ),  
             /*   array(
                    'name' => __('Custom CSS', 'realteo'),
                    'desc' => __('Enter any custom CSS you want here.', 'realteo'),
                    'id' => 'new_custom_css',
                    'default' => '',                
                    'type' => 'textarea',
                ),
                */
            )
        );
        $this->option_metabox[] = array(
            'id'         => 'realteo_property_submit_option',
            'title'      => 'Property Submit',
            'show_on'    => array( 'key' => 'options-page', 'value' => array( 'property_submit_option' ), ),
            'show_names' => true,
            'fields'     => array(
                array(
                    'name'      => __('Admin approval required', 'realteo'),
                    'desc'      => __('Require admin approval for any new properties added', 'realteo'),
                    'id'        => 'realteo_new_property_requires_approval',
                    'type'      => 'checkbox',
                ),                
                array(
                    'name'      => __('Paid properties', 'realteo'),
                    'desc'      => __('Adding properties by users will require purchasing a Listing Package', 'realteo'),
                    'id'        => 'realteo_new_property_requires_purchase',
                    'type'      => 'checkbox',
                ),                
                array(
                    'name'      => __('Remove Preview step from Submit Property', 'realteo'),
                    'desc'      => __('Enable this option to remove Preview step', 'realteo'),
                    'id'        => 'realteo_new_property_preview',
                    'type'      => 'checkbox',
                ),
                array(
                    'name' => __( 'Property duration', 'realteo' ),
                    'desc' => __( 'Set default property duration (if not set via property package). Set to 0 if you don\'t want properties to have an expiration date.', 'realteo' ),
                    'id'   => 'realteo_default_duration', //field id must be unique
                    'type' => 'text',
                    'default' => '30',    
                ),       
                array(
                    'name' => __( 'Property images upload limit', 'realteo' ),
                    'desc' => __( 'Number of images that can be uploaded to one property', 'realteo' ),
                    'id'   => 'realteo_max_files', //field id must be unique
                    'type' => 'text',
                    'default' => '10',    
                ),  
                array(
                    'name'      => __('Create and assign Region based on Google geocoding', 'realteo'),
                    'desc'      => __("Enabling this field will use 'state_long' value from geolocalization request to add new term for Region taxonomy and assign property to this term.", 'realteo'),
                    'id'        => 'realteo_auto_region',
                    'type'      => 'checkbox',
                ),   
                array(
                    'name' => __( 'Property image maximum size (in MB)', 'realteo' ),
                    'desc' => __( 'Maximum file size to upload ', 'realteo' ),
                    'id'   => 'realteo_max_filesize', //field id must be unique
                    'type' => 'text',
                    'default' => '2',    
                ),               
                array(
                    'name' => __( 'Submit Property map center point', 'realteo' ),
                    'desc' => __( 'Write latitude and longitude separated by come, for example -34.397,150.644', 'realteo' ),
                    'id'   => 'realteo_submit_center_point', //field id must be unique
                    'type' => 'text',
                    'default' => "52.2296756,21.012228700000037",    
                ),
                array(
                    'name'      => __('Default gallery style', 'realteo'),
                    'desc'      => __('Choose a default gallery style for all newly created properties', 'realteo'),
                    'id'        => 'default_gallery',
                    'type'      => 'select',
                    'options'   => array( 
                            'style-1' => __( 'Style 1', 'realteo' ),
                            'style-2' => __( 'Style 2 (with contact form)', 'realteo' ),
                            'style-3' => __( 'Style 3', 'realteo' ),
                        ),
                    'default'   => 'style-1'
                ),
            )
        );
        $this->option_metabox[] = array(
            'id'         => 'realteo_single_property_option',
            'title'      => 'Single Property',
            'show_on'    => array( 'key' => 'options-page', 'value' => array( 'single_property_option' ), ),
            'show_names' => true,
            'fields'     => array(
                array(
                    'name'      => __('Enable comments on single properties', 'realteo'),
                    'desc'      => __('You might need to enable them also on single properties https://codex.wordpress.org/Comments_in_WordPress#Enabling_Comments_on_Your_Site', 'realteo'),
                    'id'        => 'realteo_single_property_comments',
                    'type'      => 'checkbox',
                ),       
                array(
                    'name'      => __('Enable "show more" on single properties', 'realteo'),
                    'desc'      => __('Check this checkbox to limit the property content text.', 'realteo'),
                    'id'        => 'realteo_single_property_show_more',
                    'type'      => 'checkbox',
                ), 
                array(
                    'name'      => __('Disable "similar properties" on single properties', 'realteo'),
                    'desc'      => __('Check this checkbox to hide the default "Similar Properties" section.', 'realteo'),
                    'id'        => 'realteo_single_property_similar',
                    'type'      => 'checkbox',
                ),  
                array(
                    'name'      => __('"Similar properties" layout', 'realteo'),
                    'desc'      => __('Choose layout for the similar properties section" section.', 'realteo'),
                    'id'        => 'realteo_single_property_similar_layout',
                    'type'      => 'select',
                    'options'   => array( 
                            'list-layout' => __( 'List', 'realteo' ),
                            'grid-layout' => __( 'Grid', 'realteo' ),
                            
                        ),
                    'default'   => 'list-layout'
                ),   
                array(
                    'name'      => __('Show approximate location on single property view', 'realteo'),
                    'desc'      => __('Check this checkbox to hide the exact position of the locations on the map.', 'realteo'),
                    'id'        => 'realteo_single_property_fake_location',
                    'type'      => 'checkbox',
                ),
                array(
                    'name'      => __('Walk Score ID', 'realteo'),
                    'desc'      => __('Add here your Walk Score ID to show Walks Score widget in your single property. Apply on https://www.walkscore.com/professional/sign-up.php', 'realteo'),
                    'id'        => 'realteo_single_property_walkscore_id',
                    'type'      => 'text',
                ),               
                
            )
        );
        $this->option_metabox[] = array(
            'id'         => 'recaptha_settings',
            'title'      => 'reCAPTCHA Settings',
            'show_on'    => array( 'key' => 'options-page', 'value' => array( 'recaptha_settings' ), ),
            'show_names' => true,
            'fields'     => array(
                    array(
                        'name'      => __('Enable reCAPTCHA on registration form', 'realteo'),
                        'desc'      => __('Check this checkbox to add reCAPTCHA to form. You need to provide API keys for that', 'realteo'),
                        'id'        => 'realteo_recaptcha',
                        'type'      => 'checkbox',
                    ), 
                    array(
                        'name'      => __('reCAPTCHA Site Key', 'realteo'),
                        'desc'      => sprintf(__('Get the sitekey from %s.', 'realteo'),'<a href="https://www.google.com/recaptcha/intro/index.html">google.com/recaptcha</a>'),
                        'id'        => 'realteo_recaptcha_sitekey',
                        'type'      => 'text',
                    ), 
                    array(
                        'name'      => __('reCAPTCHA Secret Key', 'realteo'),
                        'desc'      => sprintf(__('Get the sitekey from %s.', 'realteo'),'<a href="https://www.google.com/recaptcha/intro/index.html">google.com/recaptcha</a>'),
                        'id'        => 'realteo_recaptcha_secretkey',
                        'type'      => 'text',
                    ), 
                )
            );

        $this->option_metabox[] = array(
            'id'         => 'emails',
            'title'      => 'Emails',
            'show_on'    => array( 'key' => 'options-page', 'value' => array( 'emails' ), ),
            'show_names' => true,
            'fields'     => array(

                array(
                    'name'  => __('"From name" in email', 'realteo'),
                    'desc'  => __('The name from who the email is received, by default it is your site name.', 'realteo'),
                    'id'    => 'emails_name',
                    'default' =>  get_bloginfo( 'name' ),                
                    'type'  => 'text',
                ),

                array(
                    'name'  => __('"From" email ', 'realteo'),
                    'desc'  => __('This will act as the "from" and "reply-to" address.', 'realteo'),
                    'id'    => 'emails_from_email',
                    'default' =>  get_bloginfo( 'admin_email' ),               
                    'type'  => 'text',
                ),
                /*----------------*/
                array(
                    'name' =>  __('Published property notification email', 'realteo'),
                    'desc' =>  __('Settings for email sent when property is published', 'realteo'),
                    'type' => 'title',
                    'id'   => 'header_published'
                ), 
                array(
                    'name'      => __('Enable property published notification email', 'realteo'),
                    'desc'      => __('Check this checkbox to enable sending emails to property authors', 'realteo'),
                    'id'        => 'property_published_email',
                    'type'      => 'checkbox',
                ), 
                array(
                    'name'      => __('Published notification Email Subject', 'realteo'),
                    'default'      => __('Your property was published - {property_name}', 'realteo'),
                    'id'        => 'property_published_email_subject',
                    'type'      => 'text',
                ),
                 array(
                    'name'      => __('Published notification Email Content', 'realteo'),
                    'default'      => trim(preg_replace('/\t+/', '', "Hi {user_name},<br>
                    We are pleased to inform you that your submission '{property_name}' was just published on our website.<br>
                    <br>
                    Thank you.
                    <br>")),
                    'id'        => 'property_published_email_content',
                    'type'      => 'wysiwyg',
                ),   
                /*----------------*/
                array(
                    'name'      =>  __('New property notification email', 'realteo'),
                    'desc'      =>  __('Settings for email sent when property is submitted by user', 'realteo'),
                    'type'      => 'title',
                    'id'        => 'header_new'
                ), 
                array(
                    'name'      => __('Enable new property notification email', 'realteo'),
                    'desc'      => __('Check this checkbox to enable sending emails to property authors', 'realteo'),
                    'id'        => 'property_new_email',
                    'type'      => 'checkbox',
                ), 
                array(
                    'name'      => __('New Property notification email subject', 'realteo'),
                    'default'      => __('Thank you for adding a property', 'realteo'),
                    'id'        => 'property_new_email_subject',
                    'type'      => 'text',
                ),
                 array(
                    'name'      => __('New Property notification email content', 'realteo'),
                    'default'      => trim(preg_replace('/\t+/', '', "Hi {user_name},<br>
                    Thank you for submitting your property '{property_name}'.<br>
                    <br>")),
                    'id'        => 'property_new_email_content',
                    'type'      => 'wysiwyg',
                ),  

                /*----------------*/
                array(
                    'name' =>  __('Expired property notification email', 'realteo'),
                    'desc' =>  __('Settings for email sent when property has expired', 'realteo'),
                    'type' => 'title',
                    'id'   => 'header_expired'
                ), 
                array(
                    'name'      => __('Enable expired property notification email', 'realteo'),
                    'desc'      => __('Check this checkbox to enable sending emails to property authors', 'realteo'),
                    'id'        => 'property_expired_email',
                    'type'      => 'checkbox',
                ), 
                array(
                    'name'      => __('Expired Property notification email subject', 'realteo'),
                    'default'      => __('Your property has expired - {property_name}', 'realteo'),
                    'id'        => 'property_expired_email_subject',
                    'type'      => 'text',
                ),
                 array(
                    'name'      => __('Expired Property notification email content', 'realteo'),
                    'default'      => trim(preg_replace('/\t+/', '', "Hi {user_name},<br>
                    We'd like you to inform you that your property '{property_name}' has expired and is no longer visible on our website. You can renew it in your account.<br>
                    <br>
                    Thank you
                    <br>")),
                    'id'        => 'property_expired_email_content',
                    'type'      => 'wysiwyg',
                ),

                /*----------------*/
                array(
                    'name' =>  __('Expiring soon property notification email', 'realteo'),
                    'desc' =>  __('Settings for email sent when property is expiring in next 5 days', 'realteo'),
                    'type' => 'title',
                    'id'   => 'header_expiring_soon'
                ), 
                array(
                    'name'      => __('Enable Expiring soon property notification email', 'realteo'),
                    'desc'      => __('Check this checkbox to enable sending emails to property authors', 'realteo'),
                    'id'        => 'property_expiring_soon_email',
                    'type'      => 'checkbox',
                ), 
                array(
                    'name'      => __('Expiring soon property notification email subject', 'realteo'),
                    'default'      => __('Your property is expiring in 5 days - {property_name}', 'realteo'),
                    'id'        => 'property_expiring_soon_email_subject',
                    'type'      => 'text',
                ),
                 array(
                    'name'      => __('Expiring soon property notification email content', 'realteo'),
                    'default'      => trim(preg_replace('/\t+/', '', "Hi {user_name},<br>
                    We'd like you to inform you that your property '{property_name}' is expiring in 5 days.<br>
                    <br>
                    Thank you
                    <br>")),
                    'id'        => 'property_expiring_soon_email_content',
                    'type'      => 'wysiwyg',
                ),  

                /*----------------*/
                array(
                    'name' =>  __('Agent invitation notification email', 'realteo'),
                    'desc' =>  __('Settings for email sent when agency manager adds ', 'realteo'),
                    'type' => 'title',
                    'id'   => 'header_agent_invitation'
                ), 
                array(
                    'name'      => __('Enable agent invitation notification email', 'realteo'),
                    'desc'      => __('Check this checkbox to enable sending emails to agents ', 'realteo'),
                    'id'        => 'agent_invitation_email',
                    'type'      => 'checkbox',
                ), 
                array(
                    'name'      => __('Agent invitation notification email subject', 'realteo'),
                    'default'      => __('You have been invited to agency - {agency_name}. Please confirm', 'realteo'),
                    'id'        => 'agent_invitation_email_subject',
                    'type'      => 'text',
                ),
                 array(
                    'name'      => __('Agent invitation notification email content', 'realteo'),
                    'default'      => trim(preg_replace('/\t+/', '', "Hi {user_name},<br>
                    You've been invited to join the Agency {agency_name}. Please confirm it in your dashboard.<br>
                    <br>
                    Thank you
                    <br>")),
                    'id'        => 'pagent_invitation_email_content',
                    'type'      => 'wysiwyg',
                ), 
              


            ),
        );

        $this->option_metabox[] = array(
            'id'         => 'agents_settings',
            'title'      => 'Agents/Roles Settings',
            'show_on'    => array( 'key' => 'options-page', 'value' => array( 'agents_settings' ), ),
            'show_names' => true,
            'fields'     => array(
                    array(
                        'name'      => __('Agent contact details visibility', 'realteo'),
                        'desc'      => __('Check this checkbox to show agent phone/email only to logged in users', 'realteo'),
                        'id'        => 'realteo_agent_contact_details',
                        'type'      => 'select',
                        'options'   => array( 
                            'always'    => esc_html__( 'Always show agent phone/email', 'realteo' ),
                            'loggedin'  => esc_html__( 'Show agent phone/email only to logged in users', 'realteo' ),
                            'never'     => esc_html__( 'Never show agent phone/email', 'realteo' ),
                        ),
                    ), 
                    array(
                        'name'      => __('Hide agents email address', 'realteo'),
                        'desc'      => __('Check this checkbox to hide email address from agent profiles', 'realteo'),
                        'id'        => 'realteo_agent_contact_email',
                        'type'      => 'checkbox',
                    ), 
                    array(
                        'name'      => __('Disable role dropdown on registration', 'realteo'),
                        'desc'      => __('Check this checkbox to hide role dropdown', 'realteo'),
                        'id'        => 'realteo_registration_role',
                        'type'      => 'checkbox',
                    ),  
                    array(
                        'name'      => __('Add Password pickup field to registration form', 'realteo'),
                        'desc'      => __('If disabled password will be randomly generated and sent via email', 'realteo'),
                        'id'        => 'realteo_generate_password',
                        'type'      => 'checkbox',
                    ), 
                    array(
                        'name'      => __('Require "Privacy Policy"  checkbox on registration', 'realteo'),
                        'desc'      => __('Set your Privacy Policy page in Settings -> Privacy', 'realteo'),
                        'id'        => 'realteo_privacy_policy',
                        'type'      => 'checkbox',
                    ), 
                    array(
                        'name'      => __('Fixing options"  checkbox on registration', 'realteo'),
                        'desc'      => __('something to fix', 'realteo'),
                        'id'        => 'realteo_fix_option',
                        'default'   => 'sth to save',
                        'type'      => 'hidden',
                    ), 

                    
                )
            );

       
        //insert extra tabs here

        return $this->option_metabox;
    }

    /**
     * Returns the option key for a given field id
     * @since  0.1.0
     * @return array
     */
    public function get_option_key($field_id) {
    	$option_tabs = $this->option_fields();
    	foreach ($option_tabs as $option_tab) { //search all tabs
    		foreach ($option_tab['fields'] as $field) { //search all fields
    			if ($field['id'] == $field_id) {
    				return $option_tab['id'];
    			}
    		}
    	}
    	return $this->key; //return default key if field id not found
    }

    /**
     * Public getter method for retrieving protected/private variables
     * @since  0.1.0
     * @param  string  $field Field to retrieve
     * @return mixed          Field value or exception is thrown
     */
    public function __get( $field ) {

        // Allowed fields to retrieve
        if ( in_array( $field, array( 'key', 'fields', 'title', 'options_pages' ), true ) ) {
            return $this->{$field};
        }
        if ( 'option_metabox' === $field ) {
            return $this->option_fields();
        }

        throw new Exception( 'Invalid property: ' . $field );
    }

    public function get_cf_forms() {
        $forms  = array( 0 => __( 'Please select a form', 'realteo' ) );

        $_forms = get_posts(
            array(
                'numberposts' => -1,
                'post_type'   => 'wpcf7_contact_form',
            )
        );

        if ( ! empty( $_forms ) ) {

            foreach ( $_forms as $_form ) {
                $forms[ $_form->ID ] = $_form->post_title;
            }
        }

        return $forms;
    }


}

// Get it started
$Realteo_Admin = new Realteo_Admin();
$Realteo_Admin->hooks();


/**
 * Wrapper function around cmb_get_option
 * @since  0.1.0
 * @param  string  $key Options array key
 * @return mixed        Option value
 */
function realteo_get_option( $key = '', $default = '' ) {
    global $Realteo_Admin;

    $array = get_option( $Realteo_Admin->get_option_key($key), $key );
    if(is_array($array) && array_key_exists($key, $array) ){
        $value = $array[$key];
    }
    if(empty($value)) {
        return $default;
    } else {
        return $value;
    }
}


/**
 * Wrapper function around cmb_get_option
 * @since  0.1.0
 * @param  string  $key Options array ke y
 * @return mixed        Option value
 */
function realteo_get_option_with_name($name= '', $key = '', $default = '' ) {
    $array = get_option( $name, $key );
    if(is_array($array) && array_key_exists($key, $array) ){
        $value = $array[$key];
    }
    if(empty($value)) {
        return $default;
    } else {
        return $value;
    }
}

?>