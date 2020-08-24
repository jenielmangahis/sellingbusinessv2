<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorMSPosts extends N2SliderGeneratorPluginAbstract {

    protected $name = 'msposts';

    public function getLabel() {
        return 'WordPress MultiSite';
    }

    protected function loadSources() {

        foreach (get_sites( array('number' => null) ) AS $site) {
            if ($site->blog_id == get_current_blog_id()) {
                continue;
            }

            $current_blog_details = get_blog_details(array('blog_id' => $site->blog_id));

            new N2GeneratorMSPostsPosts($this, 'posts' . $site->blog_id, $site->blog_id, 'Multisite - ' . n2_('Posts') . ' - ' . $current_blog_details->blogname);
        }
    }

    function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }
}

if (is_multisite()) {
    N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorMSPosts);
}
