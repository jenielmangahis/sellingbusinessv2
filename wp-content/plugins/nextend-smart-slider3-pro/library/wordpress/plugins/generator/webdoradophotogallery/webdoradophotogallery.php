<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorWebDoradoPhotoGallery extends N2SliderGeneratorPluginAbstract {

    protected $name = 'webdorado';

    protected $url = 'https://wordpress.org/plugins/photo-gallery/';

    public function getLabel() {
        return 'Photo Gallery by WD';
    }

    protected function loadSources() {

        new N2GeneratorWebDoradoImages($this, 'images', n2_('Images'));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

    public function isInstalled() {
        return defined('WD_BWG_DIR');
    }

}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorWebDoradoPhotoGallery);
