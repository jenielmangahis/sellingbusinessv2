<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
function moove_importer_get_acf_groups( ) {
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
    if ( function_exists( 'acf_get_field_groups' ) ) :
        $groups = acf_get_field_groups();
        $return_values = array();
        if ( $groups ) :
            $option = '<option value="0">'.__('Select a field','import-xml-feed').'</option>';
            foreach ( $groups as $group ) :
                $fields = acf_get_fields( $group['key'] );
                if ( $fields ) :
                    foreach ( $fields as $group_field ) :
                        if ( in_array( $group_field['type'], $supported_types ) ) :
                            $field_value = array(
                                'id'    =>  $group_field['key'],
                                'label' =>  $group['title'] . ' | ' . $group_field['label'],
                                'name'  =>  $group_field['name'],
                                'type'  =>  $group_field['type']
                            );
                            $option .= "<option value='".json_encode( $field_value )."'>". $group['title'] . ' | ' . $group_field['label']."</option>";
                        endif;
                    endforeach;
                endif;
            endforeach;
            if ( $option === '' ) :
                $option = "<option value='0'>".__('No fields were found!','import-xml-feed')."</option>";
                return $option;
            else :
                return $option;
            endif;
            return $return_values;
        else :
            $option = "<option value='0'>".__('No field groups defined!','import-xml-feed')."</option>";
            return $option;
        endif;
    else :
        $the_query = new WP_Query( array( 'post_type' => array('acf-field-group','acf' )) );
        $group_ids = array();
        // The Group Loop
        if ( $the_query->have_posts() ) :
            while ( $the_query->have_posts() ) : $the_query->the_post();
                $group_ids[] = array(
                    'id'        =>  get_the_ID(),
                    'title'     =>  get_the_title()
                );
            endwhile;
        endif;
        // Reset Post Data
        wp_reset_postdata();
        wp_reset_query();

        $return_values = array();
        $option = '';
        if ( $group_ids && ! empty( $group_ids ) ) :
            foreach ( $group_ids as $group_data ) :
                $fields = array();
                $group_id = $group_data['id'];
                $group_title = $group_data['title'];
                $fields = apply_filters('acf/field_group/get_fields', $fields, $group_id );
                if ( $fields ) :
                    $option = '<option value="0">'.__('Select a field','import-xml-feed').'</option>';
                    if ( $fields ) :
                        foreach ( $fields as $group_field ) :
                            if ( in_array( $group_field['type'], $supported_types ) ) :
                                $field_value = array(
                                    'id'    =>  $group_field['key'],
                                    'label' =>  $group_title . ' | ' . $group_field['label'],
                                    'name'  =>  $group_field['name'],
                                    'type'  =>  $group_field['type']
                                );
                                $option .= "<option value='".json_encode( $field_value )."'>". $group_title . ' | ' . $group_field['label']."</option>";
                            endif;
                        endforeach;
                    endif;
                endif;
            endforeach;
            if ( $option === '' ) :
                $option = "<option value='0'>".__('No fields were found!','import-xml-feed')."</option>";
                return $option;
            else :
                return $option;
            endif;
            return $groups;
        else :
            $option = "<option value='0'>".__('No field groups defined!','import-xml-feed')."</option>";
            return $option;
        endif;
    endif;

}

function moove_importer_generate_meta_keys($post_type){
    global $wpdb;
    $query = "
        SELECT DISTINCT($wpdb->postmeta.meta_key)
        FROM $wpdb->posts
        LEFT JOIN $wpdb->postmeta
        ON $wpdb->posts.ID = $wpdb->postmeta.post_id
        WHERE $wpdb->posts.post_type = '%s'
        AND $wpdb->postmeta.meta_key != ''
        AND $wpdb->postmeta.meta_key NOT RegExp '(^[_0-9].+$)'
        AND $wpdb->postmeta.meta_key NOT RegExp '(^[0-9]+$)'
    ";
    $meta_keys = $wpdb->get_col($wpdb->prepare($query, $post_type));
    return $meta_keys;
}