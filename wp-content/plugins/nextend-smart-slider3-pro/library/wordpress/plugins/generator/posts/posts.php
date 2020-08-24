<?php

if (N2Platform::$isWordpress) {
    N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

    class N2SSPluginGeneratorPosts extends N2SliderGeneratorPluginAbstract {

        protected $name = 'posts';

        public function getLabel() {
            return 'Posts';
        }

        protected function loadSources() {

            new N2GeneratorPostsPosts($this, 'posts', n2_('Posts by filter'));

            new N2GeneratorPostsPostsByIDs($this, 'postsbyids', n2_('Posts by IDs'));
            $customPosts = get_post_types();

            unset($customPosts['post'], $customPosts['nav_menu_item'], $customPosts['revision'], $customPosts['attachment']);

            foreach ($customPosts AS $post_type) {
                $post_type_object = get_post_type_object($post_type);
                if ($post_type_object->public) {

                    new N2GeneratorPostsCustomPosts($this, 'customposts__' . $post_type, $post_type, n2_('Custom') . ' - ' . @get_post_type_object($post_type)->labels->name . ' (' . $post_type . ')');
                }
            }
            new N2GeneratorPostsAllCustomPosts($this, 'allcustomposts', n2_('All custom posts'));
        
        }

        public function getPath() {
            return dirname(__FILE__) . DIRECTORY_SEPARATOR;
        }
    }

    N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorPosts);
}