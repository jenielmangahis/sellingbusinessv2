<?php
/**
 * Plugin Name: Ultimate WP REST
 * Description: This is a plug-in for the Menus, Thumb APIs, JWT authentication, caching, and many advanced features that help you develop web and mobile applications.
 * Version: 1.2.7
 * Text Domain: wpr
 * Author: EGANY
 * Author URI: https://egany.com
 * Domain Path: /languages/
 * License: GPLv2 or later
 */

// Blocking access direct to the plugin
defined('ABSPATH') or die('No script kiddies please!');

// Blocking called direct to the plugin
defined('WPINC') or die('No script kiddies please!');

// Initialization path plugin
defined('DS') ? null : define('DS', DIRECTORY_SEPARATOR);
define('UTM_WP_REST_PATH', __FILE__);
define('UTM_WP_REST_BASE', realpath(plugin_dir_path(__FILE__)) . DS);
define('UTM_WP_REST_URL', plugin_dir_url(__FILE__));

if (version_compare(phpversion(), '5.6', '<')) {
  add_action(
    'admin_notices',
    function () {
      echo '<div class="notice notice-error is-dismissible"><p><strong>Ultimate WP REST:</strong> Your php version (' . phpversion() . ') is not eligible! Please use version 7.0 or higher</p></div>';
    }
  );
  return;
}
error_reporting(E_ALL & ~E_NOTICE);

// Initialization MVP Core
require_once UTM_WP_REST_BASE . 'vendor' . DS . 'autoload.php';
require_once UTM_WP_REST_BASE . 'includes' . DS . 'plugin-core.php';

// Initialization Lang
$PWP->setting->initLanguage('uwr');

// Initialization Template Engine
$PWP->render->initTemplateEngine();

// Register Setting
$PWP->setting->registerGlobalSetting();

// Initialization Debugger
if ($PWP->setting->getOption('UTM_WP_REST_ENABLE_DEBUGGER') == ENABLE) {
  $PWP->setting->registerDebugger();
}

// Event listener
register_activation_hook(__FILE__, [$PWP->control, 'eventActivatePlugin']);
register_deactivation_hook(__FILE__, [$PWP->control, 'eventDeactivatePlugin']);

// Register Scripts/Styles
add_action('wp_loaded', [$PWP->setting, 'registerAllScripts']);
add_action('wp_loaded', [$PWP->setting, 'registerAllStyles']);

// Enqueue Scripts/Styles
add_action('wp_enqueue_scripts', [$PWP->setting, 'enqueueGlobalScript']);
add_action('wp_enqueue_scripts', [$PWP->setting, 'enqueueGlobalStyle']);

// Initialization Admin Menu
$PWP->admin->addAdminMenu();

// Initialization Cache
$PWP->cache->init();

// Initialization REST API
$PWP->api->init();
