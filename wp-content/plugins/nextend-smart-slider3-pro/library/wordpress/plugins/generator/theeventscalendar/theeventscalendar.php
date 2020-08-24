<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorTheEventsCalendar extends N2SliderGeneratorPluginAbstract {

    protected $name = 'theeventscalendar';

    protected $url = 'https://wordpress.org/plugins/the-events-calendar/';

    public function getLabel() {
        return 'The Events Calendar';
    }


    protected function loadSources() {

        new N2GeneratorTheEventsCalendarEvents($this, 'events', n2_('Events'));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

    public function isInstalled() {
        return class_exists('Tribe__Events__Main', false);
    }

}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorTheEventsCalendar);

