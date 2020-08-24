<?php

N2Loader::import('libraries.form.elements.list');

class N2ElementEventsManagerLocations extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $venues = get_posts(array(
            'posts_per_page'   => -1,
            'offset'           => 0,
            'category'         => '',
            'category_name'    => '',
            'orderby'          => 'date',
            'order'            => 'DESC',
            'include'          => '',
            'exclude'          => '',
            'meta_key'         => '',
            'meta_value'       => '',
            'post_mime_type'   => '',
            'post_parent'      => '',
            'post_status'      => 'publish',
            'suppress_filters' => true,
            'post_type'        => 'location'
        ));

        $this->options['0'] = n2_('All');

        if (count($venues)) {
            foreach ($venues AS $option) {
                $title = ' - ' . $option->post_title;
                $town  = get_post_meta($option->ID, '_location_town', true);
                if (!empty($town)) {
                    $title .= ', ' . $town;
                }
                $address = get_post_meta($option->ID, '_location_address', true);
                if (!empty($address)) {
                    $title .= ', ' . $address;
                }

                $this->options[$option->ID] = $title;
            }
        }
    }
}
