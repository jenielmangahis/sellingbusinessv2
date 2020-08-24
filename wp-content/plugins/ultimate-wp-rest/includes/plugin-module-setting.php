<?php
// Blocking access direct to the plugin
defined('UTM_WP_REST_BASE') or die('No script kiddies please!');

use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Tracy\Debugger;

class UTM_WP_REST_Setting {
  // Option mode format
  private $READ  = 1;
  private $WRITE = 2;

  ///////////// Call PWPCore /////////////
  public function __construct(&$PWP) {
    $this->PWP = &$PWP;
  }

  ///////////// Register Debugger /////////////
  public function registerDebugger($bForce = false) {
    if (!function_exists('wp_get_current_user')) {
      require_once ABSPATH . "wp-includes/pluggable.php";
    }

    // Fix output has been sent
    $sOutput = ob_get_contents();
    if (!empty($sOutput)) {
      ob_end_clean();
    }

    Debugger::$showLocation = true;
    Debugger::$maxDepth     = 4; // default: 3
    Debugger::$maxLength    = 650; // default: 150
    Debugger::$strictMode   = false;

    if (current_user_can('administrator') || $bForce === true) {
      Debugger::enable(Debugger::DEVELOPMENT);
    } else {
      // PRODUCTION
      Debugger::enable(Debugger::PRODUCTION);
    }

    error_reporting(E_ALL & ~E_NOTICE);

    if (!empty($sOutput)) {
      bdump($sOutput);
    }
  }

  ///////////// Register Setting /////////////
  public function registerGlobalSetting() {
    // Setting id menu
    $this->menu = [
      'pwp-setting' => true,
    ];

    // Setting page
    // Icon by MaterializeCSS
    $this->page = [
      'pwp-main-options'    => [
        'title' => __('General', UTM_WP_REST_TEXTDOMAIN),
        'icon'  => 'settings',
        'hash'  => 'general',
        'menu'  => 'pwp-setting',
      ],
      'pwp-caching-options' => [
        'title' => __('Caching', UTM_WP_REST_TEXTDOMAIN),
        'icon'  => 'settings',
        'hash'  => 'caching',
        'menu'  => 'pwp-setting',
      ],
    ];

    // Setting section
    $this->section = [
      // Maintenance
      'utm_wp_restmaintenance' => [
        'title' => __('Maintenance', UTM_WP_REST_TEXTDOMAIN),
        'page'  => 'pwp-main-options',
      ],
      // Caching
      'utm_wp_restcaching'     => [
        'title' => __('Caching', UTM_WP_REST_TEXTDOMAIN),
        'page'  => 'pwp-caching-options',
      ],
    ];

    // Setting option
    // Support Type: string, integer, number, array, boolean
    $this->option = [
      // Enable Debugger
      'UTM_WP_REST_ENABLE_DEBUGGER'             => [
        'title'    => __('Enable Debugger', UTM_WP_REST_TEXTDOMAIN),
        'section'  => 'utm_wp_restmaintenance',
        'type'     => 'boolean',
        'value'    => true,
        'elements' => [
          ['htmltag' => 'template',
            'file'     => 'element-tooltip',
            'attr'     => [
              'data-tooltip' => __('Quick turn on/off Debugger.', UTM_WP_REST_TEXTDOMAIN),
            ],
          ],
          ['htmltag' => 'template',
            'file'     => 'element-switch',
          ],
        ],
      ],
      // Cache mode
      'UTM_WP_REST_CACHE_MODE'                  => [
        'title'    => __('Caching Mode', UTM_WP_REST_TEXTDOMAIN),
        'section'  => 'utm_wp_restcaching',
        'type'     => 'array',
        'value'    => ['file'],
        'elements' => [
          ['htmltag' => 'template',
            'file'     => 'element-tooltip',
            'attr'     => [
              'data-tooltip' => __('Select cache mode.', UTM_WP_REST_TEXTDOMAIN),
            ],
          ],
          ['htmltag' => 'template',
            'file'     => 'element-select',
            'attr'     => [
              'multiple'      => true,
              'data-onchange' => json_encode(['showElementFormSelect', ['memcached', '.UTM_WP_REST_CACHE_CONFIG_MEMCACHED']]),
              'data-onload'   => json_encode(['showElementFormSelect', ['memcached', '.UTM_WP_REST_CACHE_CONFIG_MEMCACHED']]),
            ],
            'option'   => [
              //'memcached' => __('Memcached Storage', UTM_WP_REST_TEXTDOMAIN),
              'memory' => __('Memory Storage', UTM_WP_REST_TEXTDOMAIN),
              'file'   => __('File Storage', UTM_WP_REST_TEXTDOMAIN),
            ],
          ],
        ],
      ],
      'UTM_WP_REST_CACHE_TIME'                  => [
        'title'    => __('Cache Time', UTM_WP_REST_TEXTDOMAIN),
        'section'  => 'utm_wp_restcaching',
        'type'     => 'integer',
        'value'    => 60,
        'elements' => [
          ['htmltag' => 'template',
            'file'     => 'element-tooltip',
            'attr'     => [
              'data-tooltip' => __('Cache time by seconds.', UTM_WP_REST_TEXTDOMAIN),
            ],
          ],
          ['htmltag' => 'template',
            'file'     => 'element-range',
            'attr'     => [
              'istime' => true,
              'min'    => 5,
              'max'    => 3600,
            ],
          ],
        ],
      ],
      'UTM_WP_REST_CACHE_CONFIG_MEMCACHED'      => [
        'title'    => __('Memcached Config', UTM_WP_REST_TEXTDOMAIN),
        'section'  => 'utm_wp_restcaching',
        'elements' => [
          'class' => 'UTM_WP_REST_CACHE_CONFIG_MEMCACHED',
          ['htmltag' => 'template',
            'file'     => 'element-tooltip',
            'attr'     => [
              'data-tooltip' => __('Configures your Memcached.', UTM_WP_REST_TEXTDOMAIN),
            ],
          ],
          ['htmltag' => 'input',
            'attr'     => [
              'name'        => 'UTM_WP_REST_CACHE_CONFIG_MEMCACHED_HOST',
              'placeholder' => 'Enter Memcached host',
            ],
          ],
          ['htmltag' => 'input',
            'attr'     => [
              'name'        => 'UTM_WP_REST_CACHE_CONFIG_MEMCACHED_PORT',
              'placeholder' => 'Enter Memcached port',
            ],
          ],
        ],
      ],
      // Hidden option
      'UTM_WP_REST_CACHE_CONFIG_MEMCACHED_HOST' => [
        'title'   => '',
        'section' => 'utm_wp_restcaching',
        'type'    => 'string',
        'value'   => 'localhost',
      ],
      'UTM_WP_REST_CACHE_CONFIG_MEMCACHED_PORT' => [
        'title'   => '',
        'section' => 'utm_wp_restcaching',
        'type'    => 'integer',
        'value'   => 11211,
      ],
      'UTM_WP_REST_AUTH_CONFIG_JWT_NBF'         => [
        'title'   => '',
        'section' => 'utm_wp_restauth',
        'type'    => 'integer',
        'value'   => 5,
      ],
      'UTM_WP_REST_AUTH_CONFIG_JWT_EXP'         => [
        'title'   => '',
        'section' => 'utm_wp_restauth',
        'type'    => 'integer',
        'value'   => 3600,
      ],
    ];

    $this->addSettings();

    // Register js script data
    add_filter(UTM_WP_REST_TEXTDOMAIN . '_admin_js_data', [$this, 'addAdminJsData'], 10, 1);
    add_filter(UTM_WP_REST_TEXTDOMAIN . '_global_js_data', [$this, 'addGlobalJsData'], 10, 1);
  }

  public function addSettings($aSetting = []) {
    // Add setting form template/add-on/core to global setting
    if (!empty($aSetting['page']) || !empty($aSetting['section']) || !empty($aSetting['option'])) {
      foreach ($aSetting as $sSetting => $aData) {
        $this->$sSetting = array_merge($aData, $this->$sSetting);
      }
    }
    //bdump($this->$sSetting, 'All Settings');
  }

  public function addAdminJsData($aData) {

    $aData['ajax'] = [
      'url' => admin_url('admin-ajax.php'),
    ];

    $aData['translate'] = [
      'ajaxError'   => __('An error has occurred while sending the request', UTM_WP_REST_TEXTDOMAIN),
      'chooseImage' => __('Choose a image', UTM_WP_REST_TEXTDOMAIN),
      'saveSuccess' => __('Save options success!', UTM_WP_REST_TEXTDOMAIN),
      'saveFailed'  => __('Save options failed!', UTM_WP_REST_TEXTDOMAIN),
    ];

    return $aData;
  }

  public function addGlobalJsData($aData) {
    $aData['debugger'] = [
      'enable' => $this->getOption('UTM_WP_REST_ENABLE_DEBUGGER'),
    ];

    $aData['ajax'] = [
      'url' => esc_url_raw(admin_url('admin-ajax.php')),
    ];

    return $aData;
  }
  ///////////// Action /////////////

  public function getOption($sKey = '', $bRaw = false) {
    $mValue = empty($sKey) ? false : get_option($sKey, false);

    if ($mValue === false && isset($this->option[$sKey]['value'])) {
      $mValue = $this->option[$sKey]['value'];
    }

    if ($bRaw === true || !isset($this->option[$sKey]['type'])) {
      return $mValue;
    }

    $mValue = $this->formatOptionValue($mValue, $this->option[$sKey]['type'], $this->READ);

    return $mValue;
  }

  public function setOption($sKey, $mValue) {
    $mValue = $this->formatOptionValue($mValue, $this->option[$sKey]['type'], $this->WRITE);

    // Don't save invalid option
    if ($mValue === false) {
      //return false;
    }

    return update_option($sKey, $mValue);
  }

  private function formatOptionValue($mValue, $sType, $iMode) {
    switch ($sType) {
    case 'string':
      $mValue = strval($mValue);
      break;
    case 'integer':
      $mValue = intval($mValue);
      break;
    case 'number':
      $mValue = floatval($mValue);
      break;
    case 'boolean':
      $mValue = boolval($mValue) ? 1 : 0;
      break;
    case 'array':
      if ($iMode === $this->READ) {
        if (is_string($mValue)) {
          try {
            $mValue = Json::decode($mValue);
          } catch (JsonException $e) {
            $mValue = !empty($mValue) ? [$mValue] : [];
          }
        }
      } elseif ($iMode === $this->WRITE) {
        $mValue = is_array($mValue) ? $mValue : (!empty($mValue) ? [$mValue] : []);
        $mValue = Json::encode($mValue, Json::FORCE_ARRAY);
      }
      break;
    }

    return $mValue;
  }

  public function initSettings() {

    // Register section
    $aSectionSetting = $this->section;
    foreach ($aSectionSetting as $sKey => $aValue) {
      add_settings_section($sKey, $aValue['title'], null, $aValue['page']);
    }

    // Register setting
    $aSetting = $this->option;

    foreach ($aSetting as $sKey => $aValue) {
      register_setting(UTM_WP_REST_TEXTDOMAIN . '-settings-group', $sKey, [
        'type'    => isset($aValue['type']) ? $aValue['type'] : null,
        'default' => isset($aValue['value']) ? $aValue['value'] : null,
      ]);

      // Check elements, if ignored it will use set get set
      if (empty($aValue['elements'])) {
        continue;
      }

      // Show setting to HTML
      $sTitle   = !empty($aValue['title']) ? $aValue['title'] : '';
      $sSection = $aValue['section'];

      $aElements = $aValue['elements'];

      $aElements['name']  = $sKey;
      $aElements['value'] = $this->getOption($sKey);

      $sPage = $aSectionSetting[$sSection]['page'];

      add_settings_field($sKey, $sTitle, [$this->PWP->render, 'generateElementHTML'], $sPage, $sSection, $aElements);
    }
  }

  ///////////// Internationalizing /////////////
  public function initLanguage($sTextDomain) {
    defined('UTM_WP_REST_TEXTDOMAIN') ? null : define('UTM_WP_REST_TEXTDOMAIN', $sTextDomain);
    load_plugin_textdomain(UTM_WP_REST_TEXTDOMAIN, false, basename(UTM_WP_REST_BASE) . DS . 'languages' . DS);
  }

  ///////////// Register All Scripts/Styles /////////////
  public function registerAllScripts() {
    wp_register_script(UTM_WP_REST_TEXTDOMAIN . '-materialize', UTM_WP_REST_URL . 'admin/assets/js/materialize.min.js', ['jquery'], false, true);
    wp_register_script(UTM_WP_REST_TEXTDOMAIN . '-jscookie', UTM_WP_REST_URL . 'admin/assets/js/js.cookie.min.js', ['jquery'], false, true);

    // Global Script
    wp_register_script(UTM_WP_REST_TEXTDOMAIN . '-script-global', UTM_WP_REST_URL . 'public/assets/js/uwr-global.js', ['jquery'], false, true);

    // Admin Script
    wp_register_script(UTM_WP_REST_TEXTDOMAIN . '-script-pwp-admin', UTM_WP_REST_URL . 'admin/assets/js/pwp-admin.js', ['jquery'], false, true);
    wp_register_script(UTM_WP_REST_TEXTDOMAIN . '-script-uwr-admin', UTM_WP_REST_URL . 'admin/assets/js/uwr-admin.js', ['jquery'], false, true);
  }

  public function registerAllStyles() {
    wp_register_style(UTM_WP_REST_TEXTDOMAIN . '-materialize', UTM_WP_REST_URL . 'admin/assets/css/materialize.min.css', [], false);
    wp_register_style(UTM_WP_REST_TEXTDOMAIN . '-materialize-custom', UTM_WP_REST_URL . 'admin/assets/css/materialize.custom.css', [], true);
    wp_register_style(UTM_WP_REST_TEXTDOMAIN . '-materialize-icon', esc_url_raw('https://fonts.googleapis.com/icon?family=Material+Icons'), [], false);
  }

  ///////////// Enqueue Global Scripts/Styles /////////////
  public function enqueueGlobalScript() {
    $globalData = apply_filters(UTM_WP_REST_TEXTDOMAIN . '_global_js_data', []);
    wp_localize_script(UTM_WP_REST_TEXTDOMAIN . '-script-global', UTM_WP_REST_TEXTDOMAIN . 'Data', $globalData);
    wp_enqueue_script(UTM_WP_REST_TEXTDOMAIN . '-script-global');
  }

  public function enqueueGlobalStyle() {

  }

  ///////////// Enqueue Admin Scripts/Styles /////////////
  public function enqueueAdminScript() {
    wp_enqueue_script(UTM_WP_REST_TEXTDOMAIN . '-materialize');
    wp_enqueue_script(UTM_WP_REST_TEXTDOMAIN . '-jscookie');

    $adminData = apply_filters(UTM_WP_REST_TEXTDOMAIN . '_admin_js_data', []);
    wp_localize_script(UTM_WP_REST_TEXTDOMAIN . '-script-pwp-admin', 'pwpData', $adminData);
    wp_enqueue_script(UTM_WP_REST_TEXTDOMAIN . '-script-pwp-admin');

    wp_enqueue_script(UTM_WP_REST_TEXTDOMAIN . '-script-uwr-admin');
  }

  public function enqueueAdminStyle() {
    wp_enqueue_style(UTM_WP_REST_TEXTDOMAIN . '-materialize');
    wp_enqueue_style(UTM_WP_REST_TEXTDOMAIN . '-materialize-custom');
    wp_enqueue_style(UTM_WP_REST_TEXTDOMAIN . '-materialize-icon');
  }
}