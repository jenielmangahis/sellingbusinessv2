<?php
// Blocking access direct to the plugin
defined('UTM_WP_REST_BASE') or die('No script kiddies please!');

class UTM_WP_REST_Admin {

  ///////////// Call PWPCore /////////////
  public function __construct(&$PWP) {
    $this->PWP = &$PWP;
  }

  public function addAdminMenu() {
    if (is_admin()) {
      add_action('admin_menu', [$this, 'createMenu']);

      // Ajax Admin
      add_action('wp_ajax_admin_ajax', [$this, 'ajaxAdmin']);

      add_filter(UTM_WP_REST_TEXTDOMAIN . '_admin_ajax', [$this, 'addSettingAjax'], 10, 1);
    }
  }

  ///////////// Menu Admin /////////////
  public function createMenu() {
    // Create top-level token menu
    add_menu_page(__('UWR', UTM_WP_REST_TEXTDOMAIN), __('UWR', UTM_WP_REST_TEXTDOMAIN), 'manage_options', 'pwp-menus');

    // Create submenu
    add_submenu_page('pwp-menus', __('Settings', UTM_WP_REST_TEXTDOMAIN), __('Settings', UTM_WP_REST_TEXTDOMAIN), 'manage_options', 'pwp-menus', [$this, 'parseAdminSetting']);

    // Notification
    add_action('admin_notices', [$this->PWP->render, 'showAdminNotification']);

    // Call register settings function
    add_action('admin_init', [$this->PWP->setting, 'initSettings']);
  }

  public function parseAdminSetting() {
    // Enqueue scripts/styles
    $this->PWP->setting->enqueueAdminScript();
    $this->PWP->setting->enqueueAdminStyle();

    // Order tab setting
    $aMenu = ['pwp-setting', 'pwp-api'];
    $this->PWP->render->showSettingsPage($aMenu);
  }

  ///////////// Ajax /////////////

  // Hook ajaxAdmin
  public function ajaxAdmin() {
    $aCallback = apply_filters(UTM_WP_REST_TEXTDOMAIN . '_admin_ajax', []);

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      $sReceiveAction = filter_input(INPUT_GET, 'go', FILTER_SANITIZE_ENCODED);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $sReceiveAction = filter_input(INPUT_POST, 'go', FILTER_SANITIZE_ENCODED);
    }

    $aResult['success'] = false;

    if (empty($sReceiveAction)) {
      http_response_code(403);
      $this->PWP->render->json($aResult);
    }

    if (!empty($aCallback[$sReceiveAction])) {
      call_user_func_array($aCallback[$sReceiveAction], [ & $aResult]);
    }

    $this->PWP->render->json($aResult);
  }

  /////////// Set function to call by Go //////////////
  public function addSettingAjax($aCallback) {
    $aCallback['saveOptions'] = [$this, 'saveOptions'];
    return $aCallback;
  }

  /////////// Callback Ajax /////////////
  public function saveOptions(&$aResult) {
    $aFilter = [
      'options' => [
        'flags' => FILTER_FORCE_ARRAY,
        'name'  => FILTER_SANITIZE_ENCODED,
        'value' => FILTER_SANITIZE_ENCODED,
      ],
    ];

    $aValidate = filter_input_array(INPUT_POST, $aFilter);

    if (empty($aValidate)) {
      http_response_code(403);
      $aResult['message'] = __('Save options failed!', UTM_WP_REST_TEXTDOMAIN);
      return;
    }

    $aOption = $aValidate['options'];

    foreach ($aOption as $sOption => $aValue) {
      $aValue['value']   = is_string($aValue['value']) ? stripslashes($aValue['value']) : $aValue['value'];
      $aOption[$sOption] = $this->PWP->setting->setOption($aValue['name'], $aValue['value']);
    }

    $aResult['success'] = true;
    $aResult['message'] = __('Save options success!', UTM_WP_REST_TEXTDOMAIN);
    //$aResult['debug']   = $aOption;
  }
}