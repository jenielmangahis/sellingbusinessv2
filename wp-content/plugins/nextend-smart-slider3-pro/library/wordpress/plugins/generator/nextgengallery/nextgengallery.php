<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorNextGENGallery extends N2SliderGeneratorPluginAbstract {

    protected $name = 'nextgengallery';

    protected $url = 'https://wordpress.org/plugins/nextgen-gallery/';

    public function getLabel() {
        return 'NextGEN Gallery';
    }

    protected function loadSources() {

        new N2GeneratorNextGENGalleryGallery($this, 'gallery', n2_('Images'));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

    public function isInstalled() {
        return class_exists('nggGallery', false) || class_exists('C_Component_Registry', false);
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorNextgenGallery);
