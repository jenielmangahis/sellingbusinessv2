<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorWooCommerce extends N2SliderGeneratorPluginAbstract {

    protected $name = 'woocommerce';

    protected $url = 'http://www.woothemes.com/woocommerce/';

    public function getLabel() {
        return 'WooCommerce';
    }

    protected function loadSources() {

        new N2GeneratorWooCommerceProductsByFilter($this, 'productsbyfilter', n2_('Products by filter'));

        new N2GeneratorWooCommerceProductsByIds($this, 'productsbyids', n2_('Products by IDs'));

        new N2GeneratorWooCommerceCategories($this, 'categories', n2_('Categories'));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

    public function isInstalled() {
        return class_exists('WooCommerce', false);
    }
}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorWooCommerce);

