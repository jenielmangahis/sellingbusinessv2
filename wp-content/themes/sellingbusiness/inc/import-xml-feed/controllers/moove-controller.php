<?php
/**
 * Moove_Controller File Doc Comment
 *
 * @category  Moove_Controller
 * @package   moove-feed-importer
 * @author    Gaspar Nemes
 */

/**
 * Moove_Controller Class Doc Comment
 *
 * @category Class
 * @package  Moove_Controller
 * @author   Gaspar Nemes
 */
class Moove_Importer_Controller {
	/**
     * xml content variable
     */
    private $xmlreturn = null;
    /**
     * Construct function
     */
	function __construct() {
        $this->xmlreturn = array();
        $this->xmlnodes = array();
        add_action( 'moove_importer_sanitize_xml', array( &$this, 'moove_importer_sanitize_xml' ), 5, 1 );
        add_action( 'moove_importer_check_other_taxonomies', array( &$this, 'moove_importer_check_taxonomies' ), 5, 3 );
	}

    function moove_importer_check_taxonomies( $taxonomies, $post_types, $acf_groups ) {
        echo $taxonomies;
    }
    /**
     * Recursive function to read XML nodes
     * @param  object $xml     XML object.
     * @param  string $parent  Parent string.
     * @return int $child_count
     */
    private function moove_recurse_xml( $xml , $parent = "" ) {
        $child_count = 0;
        foreach( $xml as $key => $value ) :
            $child_count++;
            if ( count($value) ) :
                $name = $value->getName();
                $count = $this->xmlnodes[$name]['count'] + 1;
                $this->xmlnodes[$name] = array(
                    'count'         =>  $count,
                    'name'          =>  $name,
                    'attributes'    =>  $value->attributes(),
                    'key'           =>  $parent . "/" . (string)$key
                );
            endif;
            // No childern, aka "leaf node".
            if( Moove_Importer_Controller::moove_recurse_xml( $value , $parent . "/" . $key ) == 0 ) {
                $this->xmlreturn[] = array(
                    'key'           =>  $parent . "/" . (string)$key,
                    'attributes'    =>  $value->attributes(),
                    'value'         =>  maybe_unserialize( htmlspecialchars( $value ) )
                );
            }
        endforeach;
       return $child_count;
    }

    public function moove_importer_sanitize_xml( $xml ) {
        return $xml;
    }
    /**
     * Work with the xml file
     * @param  array $args Fields from AJAX Post.
     * @return mixt
     */
    public function moove_importer_get_content( $url ) {
        /* gets the data from a URL */

        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);

        $errors = curl_error($ch);
        $response = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        return $data;
    }
    public function moove_read_xml( $args ) {
        $return_array = array();

        if ( $args['type'] === 'url' ) :
            $xml_string = Moove_Importer_Controller::moove_importer_get_content( $args['data'] );
            $xml = simplexml_load_string( $xml_string );
        else :
            $xml = simplexml_load_string( wp_unslash( $args['data'] ) );
        endif;

        $xml = apply_filters( 'moove_importer_sanitize_xml', $xml );


        if ( $args['xmlaction'] === 'check' ) :
            if ( $xml ) :
                $parent = $parent . "/" . $xml->getName();

                Moove_Importer_Controller::moove_recurse_xml( $xml, $parent );
                // $this->xmlnodes = array_unique( $this->xmlnodes );
                ob_start(); ?>
                <h4><?php _e( 'Select your repeated XML element you want to import', 'import-xml-feed' ); ?></h4>
                <select name="moove-xml-nodes" id="moove-xml-nodes" class="moove-xml-nodes">
                    <?php
                    $first_node_select = "";
                    foreach ( $this->xmlnodes as $nodekey => $nodecount ) : ?>
                        <?php if ( $first_node_select == '' ) : $first_node_select = $nodecount['key']; endif; ?>
                        <option value="<?php echo $nodecount['key']; ?>">
                            <?php echo $nodekey.' ('.$nodecount['count'].') '.$nodecount['key'].''; ?>
                        </option>
                    <?php
                    endforeach;
                    ?>
                </select>
                <br / >
                <br / >
                <?php
                return json_encode(
                    array(
                        'select_nodes'      =>  ob_get_clean(),
                        'selected_element'  =>  $first_node_select,
                        'response'          =>  'true'
                    )
                );
            else :
                return json_encode( array( 'response' => 'false' ) );
            endif;

        elseif ( $args['xmlaction'] === 'import' ) :
            $return_array['node_count'] = count( $xml );
            if ( count( $xml ) ) :
                foreach ( $xml as $key => $value ) :
                    Moove_Importer_Controller::moove_recurse_xml( $value );
                    $return_array['data'][]= $this->xmlreturn;
                    $this->xmlreturn = array();
                endforeach;
            endif;
            return true;
        elseif ( $args['xmlaction'] === 'preview' ) :
            $selected_node = $args['node'];
            $xxml = $xml;
            if ( $xml->getNamespaces(true) ) :
                $xml->registerXpathNamespace( 'atom' , 'http://www.w3.org/2005/Atom' );
                $selected_node = str_replace( "/" , "/atom:" , $selected_node );
            endif;
            $xml = $xml->xpath( "$selected_node" );

            if ( count( $xml ) ) :
                ob_start();
                echo "<hr><h4>Node count: ". count( $xml )." <span class='pagination-info'> 1 / " . count( $xml ) . " </span></h4>";
                if ( count( $xml ) > 1 ) :
                    echo "<span data-current='1'>";
                    echo "<a href='#' class='moove-xml-preview-pagination button-previous button-disabled'>Previous</a>";
                    echo "<a href='#' class='moove-xml-preview-pagination button-next'>Next</a>";
                    echo "</span>";
                endif;
                echo "<hr>";
                $i == 0;
                $return_keys = array();
                $readed_data = array();
                foreach ( $xml as $key => $value ) :
                    $i++;
                    Moove_Importer_Controller::moove_recurse_xml( $value );
                    if ( $i > 1 ) : $hidden_class = 'moove-hidden'; else : $hidden_class = 'moove-active'; endif;
                    echo "<div class='moove-importer-readed-feed $hidden_class' data-total='".count( $xml )."' data-no='$i'>";
                    foreach ( $this->xmlreturn as $xmlvalue ) :
                        $return_keys[] = $xmlvalue['key'];
                        if ( isset( $xmlvalue['attributes'] ) && ! empty( $xmlvalue['attributes'] ) ) :
                            foreach ( $xmlvalue['attributes'] as $attr_key => $attr_val ) :
                                $return_keys[] = $xmlvalue['key'] . '/@' . $attr_key;
                            endforeach;
                        endif;
                        $readed_data[ $i ]['values'][] = array(
                            'key'           =>  $xmlvalue['key'],
                            'attributes'    =>  $xmlvalue['attributes'],
                            'value'         =>  $xmlvalue['value']
                        );?>
                        <p>
                            <strong>
                                <?php echo $xmlvalue['key']; ?>:
                            </strong>
                            <?php echo $xmlvalue['value']; ?>
                        </p>

                        <?php do_action( 'moove_importer_get_attribues', $xmlvalue ); ?>
                    <?php
                    endforeach;
                    $this->xmlreturn = null;
                    echo "</div>";
                endforeach;
                $return_keys = array_unique( $return_keys );
                if ( count( $return_keys ) ) :
                    $select_options = "<option value='0'>Select a field</option>";
                    $_xml = $xml;
                    foreach ( $return_keys as $select_value ) :
                        $select_options .= "<option value='" . $select_value . "'>" . $select_value . "</option>";
                    endforeach;
                endif;
                return json_encode(
                    array(
                        'content'           =>  ob_get_clean(),
                        'select_option'     =>  $select_options,
                        'xml_json_data'     =>  json_encode( $readed_data )
                    )
                );
            else :
                $selected_node = $args['node'];
                $xml = $xxml->xpath( "$selected_node" );
                ob_start();

                echo "<hr><h4>Node count: ". count( $xml )." <span class='pagination-info'> 1 / " . count( $xml ) . " </span></h4>";
                if ( count( $xml ) > 1 ) :
                    echo "<span data-current='1'>";
                    echo "<a href='#' class='moove-xml-preview-pagination button-previous button-disabled'>Previous</a>";
                    echo "<a href='#' class='moove-xml-preview-pagination button-next'>Next</a>";
                    echo "</span>";
                endif;
                echo "<hr>";
                $i == 0;
                $return_keys = array();
                $readed_data = array();
                foreach ( $xml as $key => $value ) :
                    $i++;
                    Moove_Importer_Controller::moove_recurse_xml( $value );
                    if ( $i > 1 ) : $hidden_class = 'moove-hidden'; else : $hidden_class = 'moove-active'; endif;
                    echo "<div class='moove-importer-readed-feed $hidden_class' data-total='".count( $xml )."' data-no='$i'>";
                    foreach ( $this->xmlreturn as $xmlvalue ) :
                        $return_keys[] = $xmlvalue['key'];
                        if ( isset( $xmlvalue['attributes'] ) && ! empty( $xmlvalue['attributes'] ) ) :
                            foreach ( $xmlvalue['attributes'] as $attr_key => $attr_val ) :
                                $return_keys[] = $xmlvalue['key'] . '/@' . $attr_key;
                            endforeach;
                        endif;
                        $readed_data[ $i ]['values'][] = array(
                            'key'           =>  $xmlvalue['key'],
                            'attributes'    =>  $xmlvalue['attributes'],
                            'value'         =>  $xmlvalue['value']
                        );?>
                        <p>
                            <strong>
                                <?php echo $xmlvalue['key']; ?>:
                            </strong>
                            <?php echo $xmlvalue['value']; ?>
                        </p>

                        <?php if ( $xmlvalue['attributes'] ) : ?>
                        <?php do_action( 'moove_importer_get_attribues', $xmlvalue ); ?>
                        <?php endif; ?>
                    <?php
                    endforeach;
                    $this->xmlreturn = null;
                    echo "</div>";
                endforeach;
                $return_keys = array_unique( $return_keys );
                if ( count( $return_keys ) ) :
                    $select_options = "<option value='0'>Select a field</option>";
                    $_xml = $xml;
                    foreach ( $return_keys as $select_value ) :
                        $select_options .= "<option value='" . $select_value . "'>" . $select_value . "</option>";
                    endforeach;
                endif;
                return json_encode(
                    array(
                        'content'           =>  ob_get_clean(),
                        'select_option'     =>  $select_options,
                        'xml_json_data'     =>  json_encode( $readed_data )
                    )
                );
            endif;
        endif;
    }
    /**
     * Searches for $needle in the multidimensional array $haystack.
     *
     * @param mixed $needle The item to search for.
     * @param array $haystack The array to search.
     * @return array|bool The indices of $needle in $haystack across the
     *  various dimensions. FALSE if $needle was not found.
     */
    private function moove_recursive_array_search($needle,$haystack) {
        foreach( $haystack as $key => $value ) :
            if( $needle === $value ) :
                return array( $key );
            else :
                if ( is_array( $value ) && $subkey = Moove_Importer_Controller::moove_recursive_array_search( $needle , $value ) ) :
                    array_unshift( $subkey, $key );
                    return $subkey;
                endif;
            endif;
        endforeach;
    }
    /**
     * Return an ID of an attachment by searching the database with the file URL.
     *
     * First checks to see if the $url is pointing to a file that exists in
     * the wp-content directory. If so, then we search the database for a
     * partial match consisting of the remaining path AFTER the wp-content
     * directory. Finally, if a match is found the attachment ID will be
     * returned.
     *
     * @param string $url The URL of the image (ex: http://mysite.com/wp-content/uploads/2013/05/test-image.jpg).
     *
     * @return int|null $attachment Returns an attachment ID, or null if no attachment is found
     */
    private function moove_get_attachment_id_from_src( $url ) {
        $attachment_id = 0;
        $dir = wp_upload_dir();
        $file = basename( $url );
        $query_args = array(
            'post_type'   => 'attachment',
            'post_status' => 'inherit',
            'fields'      => 'ids',
            'meta_query'  => array(
                array(
                    'value'   => $file,
                    'compare' => 'LIKE',
                    'key'     => '_wp_attachment_metadata',
                ),
            )
        );
        $query = new WP_Query( $query_args );
        if ( $query->have_posts() ) {
            foreach ( $query->posts as $post_id ) :
                $meta = wp_get_attachment_metadata( $post_id );
                $original_file       = basename( $meta['file'] );
                $cropped_image_files = wp_list_pluck( $meta['sizes'], 'file' );
                if ( $original_file === $file || in_array( $file, $cropped_image_files ) ) :
                    $attachment_id = $post_id;
                    break;
                endif;
            endforeach;
            wp_reset_query();
            wp_reset_postdata();
        }
        return $attachment_id;
    }

    /**
     * Upload image, and set as featured image
     * @param  int $post_id   Assign as featured image for this post.
     * @param  string $image_url Image URL from the feed
     * @return void
     */
    private function moove_set_featured_image( $post_id, $image_url, $set_as_thumbnail ) {
        // Add Featured Image to Post.
        $upload_dir = wp_upload_dir(); // Set upload folder.
        $image_data = file_get_contents($image_url); // Get image data.
        $filename   = basename($image_url); // Create image file name.
        // Check folder permission and define file location.
        if( wp_mkdir_p( $upload_dir['path'] ) ) :
            $file = $upload_dir['path'] . '/' . $filename;
        else :
            $file = $upload_dir['basedir'] . '/' . $filename;
        endif;
        if ( $wp_filetype = wp_check_filetype( $filename, null ) ) {
            if( !file_exists( $file ) ) :
                // Create the image  file on the server.
                file_put_contents( $file, $image_data );
                // Check image file type.
                $wp_filetype = wp_check_filetype( $filename, null );
                // Set attachment data.
                $attachment = array(
                    'post_mime_type'    =>  $wp_filetype['type'],
                    'post_title'        =>  sanitize_file_name( $filename ),
                    'post_content'      =>  '',
                    'post_status'       =>  'inherit'
                );
                // Create the attachment.
                $attach_id = wp_insert_attachment( $attachment, $file, $post_id );
                // Include image.php
                require_once(ABSPATH . 'wp-admin/includes/image.php');
                // Define attachment metadata.
                $attach_data = wp_generate_attachment_metadata( $attach_id, $file );
                // Assign metadata to attachment.
                wp_update_attachment_metadata( $attach_id, $attach_data );
            else :
                // Searching for attachement ID.
                $attach_id = Moove_Importer_Controller::moove_get_attachment_id_from_src( $file );
            endif;
            // And finally assign featured image to post.
            if ( $set_as_thumbnail ) :
                set_post_thumbnail( $post_id, $attach_id );
            endif;
        }
        return $attach_id;
    }

    /**
     * Place ACF custom fields to post
     * @param  array $args Custom data
     * @return boolean True if the post was created successfully, and False if not.
     */
    public function moove_instert_acf_fields( $args, $post_id ) {
        if ( $args && ! empty( $args ) ) :
            $supported_types = array(
                'text',
                'number',
                'textarea',
                'email',
                'password',
                'wysiwyg',
                'image',
                'date_picker',
                'color_picker'
            );
            foreach ( $args['acf'] as $form_key => $acf_value ) :
                if ( in_array( $acf_value['type'], $supported_types ) ) :
                    $key    = $acf_value['key'];
                    $value  = $acf_value['value'];
                    if ( function_exists( 'update_field' ) ) :
                        switch ($acf_value['type']) {
                            case 'number':
                                $value = intval( $value );
                                break;
                            case 'email':
                                $value = sanitize_email( $value );
                                break;
                            case 'image':
                                $attachment_id = Moove_Importer_Controller::moove_set_featured_image( $post_id, $value, false );
                                $value = intval( $attachment_id );
                                break;
                            case 'date_picker' :
                                try {
                                    $value = DateTime::createFromFormat( "D, d M Y H:i:s O", $value )->format('Ymd');
                                } catch (Exception $e) {
                                    $value = '';
                                }
                            default:
                                $value = sanitize_text_field( $value );
                        }
                        if ( $value ) :
                            update_field( $key, $value, $post_id );
                        endif;
                    endif;
                endif;
            endforeach;
            return true;
        endif;
        return false;
    }

    /**
     * Place custom fields to post
     * @param  array $args Custom data
     * @return boolean True if the post was created successfully, and False if not.
     */
    public function moove_instert_custom_fields( $args, $post_id ) {
        if ( $args && ! empty( $args ) ) :
            foreach ( $args['customfields'] as $form_key => $customfield_value ) :
                add_post_meta( $post_id, sanitize_text_field($customfield_value['field']), $customfield_value['value'] );
            endforeach;
            return true;
        endif;
        return false;
    }


    /**
     * Create post from $args data
     * @param  array $args Custom data
     * @return boolean True if the post was created successfully, and False if not.
     */
    public function moove_create_post( $args ) {
        $form_data = $args['form_data'];
        $key = json_decode( wp_unslash( $args['key'] ) );
        $xml_data_values = $args['value'];
        $new_form_data = array();
        $customfields_data = array();
		$special_field = array('region', '_listing_status','_property_type', '_absolute_water');
        foreach ( $form_data as $form_key => $form_value ) :

            if ( $form_value !== '0') :
			
                if ( $form_key === 'customfields' ) :
                    $j = 0;
                    foreach ( $form_value as $cf_key => $customfields ) :
                        if ( $customfields['value'] !== '0' && $customfields['field'] !== '0' ) :
                            $j++;

                            $title_explode = explode( '/@', $customfields['value'] );
                            if ( count ( $title_explode ) > 1 ) :

                                $_key =  Moove_Importer_Controller::moove_recursive_array_search( $title_explode[0], $xml_data_values['values']) ;
                                $_field = json_decode( wp_unslash( $customfields['field'] ), true );
                                if ( is_array( $_key ) ) :
                                    $customfields_title = $xml_data_values['values'][$_key[0]]['attributes']['@attributes'][$title_explode[1]];
									if(in_array($customfields['field'], $special_field)){
										$customfields_title = $customfields['value'];
									}
                                    $customfields_data[ $form_key ][] = array(
                                        'field'         =>  wp_unslash( $customfields['field'] ),
                                        'value'         =>  $customfields_title,
                                    );
                                endif;

                            else :
                                $_key =  Moove_Importer_Controller::moove_recursive_array_search( $customfields['value'], $xml_data_values['values']) ;
                                $_field = json_decode( wp_unslash( $customfields['field'] ), true );
                                if ( is_array( $_key ) ) :
                                    $customfields_title = $xml_data_values['values'][$_key[0]]['value'];
									if(in_array($customfields['field'], $special_field)){
										$customfields_title = $customfields['value'];
									}
                                    $customfields_data[ $form_key ][] = array(
                                        'field'         =>  wp_unslash( $customfields['field'] ),
                                        'value'         =>  $customfields_title,
                                    );
                                endif;
                            endif;
							
							if(in_array($customfields['field'], $special_field)){
                                    $customfields_data[ $form_key ][] = array(
                                        'field'         =>  $customfields['field'],
                                        'value'         =>  $customfields['value']
                                    );
							}

                        endif;
                    endforeach;

                endif;
				
            else :
                if ( $form_key === 'post_featured_image' ) :

                    $title_explode = explode( '/@', $form_value );
                    if ( count ( $title_explode ) > 1 ) :
                        $_key =  Moove_Importer_Controller::moove_recursive_array_search( $title_explode[0] , $xml_data_values['values'] );
                        if ( is_array( $_key ) ) :
                            $img_url = $xml_data_values['values'][$_key[0]]['attributes']['@attributes'][$title_explode[1]];
                            $new_form_data[ $form_key ] = preg_replace( '/\\?.*/', '', $img_url );
                        endif;
                    else :
                        $_key =  Moove_Importer_Controller::moove_recursive_array_search( $form_value , $xml_data_values['values'] );
                        if ( is_array( $_key ) ) :
                            $img_url = $xml_data_values['values'][$_key[0]]['value'];
                            $new_form_data[ $form_key ] = preg_replace( '/\\?.*/', '', $img_url );
                        endif;

                    endif;

                endif;
            endif;
        endforeach;
		
		if(empty($customfields_data))
			return false;
			
        // Create post object.
		
		$property_title = '';
		$property_description = '';
		$region_id = '';
        foreach ( $customfields_data['customfields'] as $k=>$field ) {
			if($field['field']=='property_title'){
				$property_title = strip_tags( htmlspecialchars_decode($field['value']));
				unset($customfields_data['customfields'][$k]);
			}
			if($field['field']=='property_description'){
				$property_description = sanitize_text_field($field['value']);
				unset($customfields_data['customfields'][$k]);
			}
			if($field['field']=='region'){
				$region_id = $field['value'];
				unset($customfields_data['customfields'][$k]);
			}
		}
		
		$property_data = array(
			'post_title'     => $property_title,
			'post_content'   => $property_description,
			'post_type'      => 'property',
			'post_status'	 => 'publish',
			'comment_status' => 'closed'
		);
		
        // Insert the post into the database.
        $post_id = wp_insert_post( $property_data );
		
		if($region_id)
			wp_set_object_terms( $post_id, $region_id, 'region', false );
		
        Moove_Importer_Controller::moove_set_featured_image( $post_id, $new_form_data[ 'post_featured_image' ], true );
        Moove_Importer_Controller::moove_instert_custom_fields( $customfields_data, $post_id );

        return ( $post_id ) ? true : false;
    }
	
}
$moove_importer_controller = new Moove_Importer_Controller();
