<?php

N2Loader::import('libraries.form.elements.list');

class N2ElementBestWebSoftGalleries extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $galleries = get_posts(array(
            'post_type'    => 'bws-gallery',
            'child_of'     => 0,
            'parent'       => '',
            'orderby'      => 'name',
            'order'        => 'ASC',
            'hide_empty'   => 0,
            'hierarchical' => 1,
            'exclude'      => '',
            'include'      => '',
            'number'       => '',
            'pad_counts'   => false
        ));

        if (count($galleries)) {
            foreach ($galleries AS $gallery) {
                $this->options[$gallery->ID] = $gallery->post_title;
            }
            if ($this->getValue() == '') {
                $this->set($this->name, $galleries[0]->ID);
            }
        }

    }
}
