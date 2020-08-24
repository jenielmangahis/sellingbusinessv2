<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorEventsManager extends N2SliderGeneratorPluginAbstract {

    protected $name = 'eventsmanager';

    protected $url = 'https://wordpress.org/plugins/events-manager/';

    public function getLabel() {
        return 'Events Manager';
    }

    protected function loadSources() {

        new N2GeneratorEventsManagerEvents($this, 'events', n2_('Events'));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

    public function isInstalled() {
        return class_exists('EM_Events', false);
    }

}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorEventsManager);

