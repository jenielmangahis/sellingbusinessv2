<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorBestWebSoft extends N2SliderGeneratorPluginAbstract {

    protected $name = 'bestwebsoft';

    protected $url = 'https://wordpress.org/plugins/gallery-plugin/';

    public function getLabel() {
        return 'BestWebSoft Gallery';
    }

    protected function loadSources() {

        new N2GeneratorBestWebSoftGallery($this, 'gallery', n2_('Images'));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

    public function isInstalled() {
        return function_exists('gllr_init');
    }

}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorBestWebSoft);

