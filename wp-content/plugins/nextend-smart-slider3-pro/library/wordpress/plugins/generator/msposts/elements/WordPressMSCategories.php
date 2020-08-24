<?php

N2Loader::import('libraries.form.elements.list');

class N2ElementWordPressMSCategories extends N2ElementList {

    protected $blog_id;

    public function __construct($parent, $name = '', $label = '', $default = '', $parameters = array()) {
        parent::__construct($parent, $name, $label, $default, $parameters);

        switch_to_blog($this->blog_id);
        $categories = get_categories(array(
            'type'         => 'post',
            'child_of'     => 0,
            'parent'       => '',
            'orderby'      => 'name',
            'order'        => 'ASC',
            'hide_empty'   => 0,
            'hierarchical' => 1,
            'exclude'      => '',
            'include'      => '',
            'number'       => '',
            'taxonomy'     => 'category',
            'pad_counts'   => false

        ));

        $new = array();
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

        restore_current_blog();
    }

    /**
     * @param mixed $blog_id
     */
    public function setBlogId($blog_id) {
        $this->blog_id = $blog_id;
    }


}
