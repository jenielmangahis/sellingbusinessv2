<?php
N2Loader::import('libraries.form.elements.list');

class N2ElementWoocommerceTags extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $args = array(
            'child_of'     => 0,
            'parent'       => '',
            'orderby'      => 'name',
            'order'        => 'ASC',
            'hide_empty'   => 0,
            'hierarchical' => 1,
            'exclude'      => '',
            'include'      => '',
            'number'       => '',
            'taxonomy'     => 'product_tag',
            'pad_counts'   => false
        );
        $tags = get_categories($args);


        $this->options['0'] = n2_('All');
        if (count($tags)) {
            foreach ($tags AS $tag) {
                $this->options[$tag->cat_ID] = $tag->name;
            }
        }

    }
}
