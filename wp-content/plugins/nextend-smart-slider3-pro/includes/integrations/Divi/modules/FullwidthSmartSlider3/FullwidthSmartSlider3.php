<?php

class Nextend_FullwidthSmartSlider3 extends Nextend_SmartSlider3 {

    public function init() {
        $this->name       = 'Smart Slider 3';
        $this->slug       = 'et_pb_nextend_smart_slider_3_fullwidth';
        $this->vb_support = 'on';
        $this->fullwidth  = true;
    }
}

new Nextend_FullwidthSmartSlider3();