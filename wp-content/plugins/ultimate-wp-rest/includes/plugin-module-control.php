<?php
// Blocking access direct to the plugin
defined('UTM_WP_REST_BASE') or die('No script kiddies please!');

class UTM_WP_REST_Control {
  public $queryRequest;

  ///////////// Call PWPCore /////////////
  public function __construct(&$PWP) {
    $this->PWP = &$PWP;
  }

  ///////////// Event listener /////////////
  public function eventActivatePlugin() {

  }

  public function eventDeactivatePlugin() {

  }
}