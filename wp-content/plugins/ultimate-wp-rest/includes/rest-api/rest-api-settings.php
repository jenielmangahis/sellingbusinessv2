<?php
// Blocking access direct to the plugin
defined('UTM_WP_REST_BASE') or die('No script kiddies please!');

class RestApiSettings {
  ///////////// Call PWPCore /////////////
  public function __construct(&$PWP) {
    $this->PWP = &$PWP;

    define('UTM_WP_REST_API_SETTINGS', '/settings');
  }

  public function registerRestRoute() {
    // Get option by name
    register_rest_route(UTM_WP_REST_API_NAMESPACE, UTM_WP_REST_API_SETTINGS . '/option', [
      [
        'methods'  => WP_REST_Server::READABLE,
        'callback' => [$this, 'getOptions'],
      ],
    ]);

    // Get current version
    register_rest_route(UTM_WP_REST_API_NAMESPACE, UTM_WP_REST_API_SETTINGS . '/version', [
      [
        'methods'  => WP_REST_Server::READABLE,
        'callback' => [$this, 'getCurrentVersion'],
      ],
    ]);
  }

  public function getOptions($oRequest) {
    $sOptionName = isset($_GET['name']) ? trim(wp_unslash($_GET['name'])) : false;
    if ($sOptionName === false) {
      $aJson = [
        'code'    => 'rest_option_name_empty',
        'message' => __('Option name is empty!', UTM_WP_REST_TEXTDOMAIN),
      ];
      return new WP_Error($aJson, 404);
    }

    $aJson = [
      'name'  => $sOptionName,
      'value' => $this->PWP->setting->getOption($sOptionName),
    ];

    return new WP_REST_Response($aJson, 200);
  }

  public function getCurrentVersion($oRequest) {
    if (!function_exists('get_plugin_data')) {
      require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    $aPluginData = get_plugin_data(UTM_WP_REST_PATH);

    $aJson = [
      'name'    => $aPluginData['Name'],
      'version' => $aPluginData['Version'],
    ];

    return new WP_REST_Response($aJson, 200);
  }
}