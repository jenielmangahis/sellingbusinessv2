<?php

N2Loader::import('libraries.form.elements.list');

class N2ElementAllInOneEventCalendarTags extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $categories = get_categories(array(
            'type'         => 'ai1ec_event',
            'child_of'     => 0,
            'parent'       => '',
            'orderby'      => 'name',
            'order'        => 'ASC',
            'hide_empty'   => 0,
            'hierarchical' => 1,
            'exclude'      => '',
            'include'      => '',
            'number'       => '',
            'taxonomy'     => 'events_tags',
            'pad_counts'   => false
        ));

        $new        = array();
        foreach ($categories as $a) {
            $new[$a->category_parent][] = $a;
        }
        $options = array();
        $this->createTree($options, $new, 0);

        $this->options['0'] = n2_('All');
        if (count($options)) {
            foreach ($options AS $option) {
                $this->options[$option->cat_ID] = ' - ' . $option->treename;
            }
        }
    }
}
