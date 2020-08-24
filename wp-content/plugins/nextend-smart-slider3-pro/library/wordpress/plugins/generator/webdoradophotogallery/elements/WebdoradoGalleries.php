<?php

N2Loader::import('libraries.form.elements.list');

class N2ElementWebDoradoGalleries extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        global $wpdb;
        parent::__construct($parent, $name, $label, $default, $parameters);

        $this->options['0'] = n2_('All');

        $galleries = $wpdb->get_results("SELECT id, name FROM " . $wpdb->base_prefix . "bwg_gallery WHERE published = 1");
        foreach ($galleries AS $gallery) {
            $this->options[$gallery->id] = $gallery->name;
        }
    }
}
