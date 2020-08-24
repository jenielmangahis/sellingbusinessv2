<?php
// Blocking access direct to the plugin
defined('UTM_WP_REST_BASE') or die('No script kiddies please!');

// Set path modules
define('UTM_WP_REST_INCLUDES', UTM_WP_REST_BASE . 'includes' . DS);
define('UTM_WP_REST_PATH_CACHE', UTM_WP_REST_BASE . 'cache' . DS);
define('UTM_WP_REST_PATH_ADMIN', UTM_WP_REST_BASE . 'admin' . DS);
define('UTM_WP_REST_PATH_PUBLIC', UTM_WP_REST_BASE . 'public' . DS);
define('UTM_WP_REST_WP_HOME', get_home_url());
define('UTM_WP_REST_SERVER', UTM_WP_REST_WP_HOME);
define('UTM_WP_REST_VERSION', '1.0');

//
define('ENABLE', 1);
define('DISABLE', 0);

// Require MODULE
require_once UTM_WP_REST_INCLUDES . 'plugin-module-admin.php';
require_once UTM_WP_REST_INCLUDES . 'plugin-module-cache.php';
require_once UTM_WP_REST_INCLUDES . 'plugin-module-control.php';
require_once UTM_WP_REST_INCLUDES . 'plugin-module-render.php';
require_once UTM_WP_REST_INCLUDES . 'plugin-module-setting.php';
require_once UTM_WP_REST_INCLUDES . 'plugin-module-tool.php';
require_once UTM_WP_REST_INCLUDES . 'plugin-module-rest-api.php';

$PWP = new PWPCore;

class PWPCore {
  public function __construct() {
    $this->tool    = new UTM_WP_REST_Tool($this);
    $this->setting = new UTM_WP_REST_Setting($this);
    $this->admin   = new UTM_WP_REST_Admin($this);
    $this->cache   = new UTM_WP_REST_Cache($this);
    $this->control = new UTM_WP_REST_Control($this);
    $this->render  = new UTM_WP_REST_Render($this);
    $this->api     = new UTM_WP_REST_RestAPI($this);
  }
}