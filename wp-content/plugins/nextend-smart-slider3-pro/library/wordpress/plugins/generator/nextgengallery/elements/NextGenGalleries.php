<?php

N2Loader::import('libraries.form.elements.list');

class N2ElementNextGenGalleries extends N2ElementList {

    protected function fetchElement() {
        global $wpdb;

        $galleries = $wpdb->get_results("SELECT * FROM " . $wpdb->base_prefix . "ngg_gallery ORDER BY name");

        if (count($galleries)) {
            foreach ($galleries AS $gallery) {
                $this->options[$gallery->gid] = $gallery->title;
            }

            if ($this->getValue() == '') {
                $this->setValue($galleries[0]->gid);
            }
        }

        return parent::fetchElement();
    }
}
