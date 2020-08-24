<?php

N2Loader::import('libraries.form.elements.list');

class N2ElementTheEventsCalendarVenues extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $venues = get_posts(array(
            'posts_per_page'   => 5,
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
            'post_type'        => 'tribe_venue'
        ));

        $this->options['0'] = n2_('All');
        if (count($venues)) {
            foreach ($venues AS $option) {
                $this->options[$option->ID] = ' - ' . $option->post_title;
            }
        }

    }
}
