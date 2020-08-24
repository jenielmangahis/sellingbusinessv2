<?php
N2Loader::import('libraries.plugins.N2SliderGeneratorPluginAbstract', 'smartslider');

class N2SSPluginGeneratorAllinOneEventCalendar extends N2SliderGeneratorPluginAbstract {

    protected $name = 'allinoneeventcalendar';

    protected $url = 'https://wordpress.org/plugins/all-in-one-event-calendar/';

    public function getLabel() {
        return 'All-in-One Event Calendar';
    }

    protected function loadSources() {

        new N2GeneratorAllinOneEventCalendarEvents($this, 'events', n2_('Events'));
    }

    public function getPath() {
        return dirname(__FILE__) . DIRECTORY_SEPARATOR;
    }

    public function isInstalled() {
        return defined('AI1EC_PATH');
    }

}

N2SSGeneratorFactory::addGenerator(new N2SSPluginGeneratorAllinOneEventCalendar);

