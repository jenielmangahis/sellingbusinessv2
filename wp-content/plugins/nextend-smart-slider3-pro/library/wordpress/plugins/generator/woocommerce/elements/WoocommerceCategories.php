<?php

N2Loader::import('libraries.form.elements.list');

class N2ElementWoocommerceCategories extends N2ElementList {

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        $args       = array(
            'child_of'     => 0,
            'parent'       => '',
            'orderby'      => 'name',
            'order'        => 'ASC',
            'hide_empty'   => 0,
            'hierarchical' => 1,
            'exclude'      => '',
            'include'      => '',
            'number'       => '',
            'taxonomy'     => 'product_cat',
            'pad_counts'   => false
        );
        $categories = get_categories($args);
        $new        = array();
        foreach ($categories as $a) {
            $new[$a->category_parent][] = $a;
        }

        $options = array();
        $this->createTree($options, $new, 0);

        $this->options['0'] = n2_('Root');
        if (count($options)) {
            foreach ($options AS $option) {
                $this->options[$option->cat_ID] = ' - ' . $option->treename;
            }
        }
    }
}
