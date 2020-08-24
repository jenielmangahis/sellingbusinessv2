<?php
// Blocking access direct to the plugin
defined('UTM_WP_REST_BASE') or die('No script kiddies please!');

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\ValidationData;
use Nette\Caching\Cache;

class UTM_WP_REST_RestAPI {
  private $jwtError;
  public $auth;

  ///////////// Call PWPCore /////////////
  public function __construct(&$PWP) {
    $this->PWP = &$PWP;
  }

  ///////////// Init /////////////
  public function init() {
    define('UTM_WP_REST_API_VERSION', 'v2');
    define('UTM_WP_REST_API_NAMESPACE', 'wp/' . UTM_WP_REST_API_VERSION);
    define('UTM_WP_REST_API_PATH', UTM_WP_REST_INCLUDES . 'rest-api' . DS);

    // Setup setting page
    $this->addTabSetting();
    $this->addApiMenuLocationInSetting();
    $this->addApiMenusInSetting();
    $this->addApiUsersInSetting();
    $this->addApiSettingsInSetting();
    $this->addApiCustomInSetting();

    $this->PWP->setting->setOption('UTM_WP_REST_AUTH_MODE', ['jwt']);

    define('UTM_WP_REST_API_AUTH_MODE', $this->PWP->setting->getOption('UTM_WP_REST_AUTH_MODE')[0]);    

    // Auth by Nonce
    if (UTM_WP_REST_API_AUTH_MODE === 'nonce') {
      if (!function_exists('wp_create_nonce')) {
        require_once ABSPATH . 'wp-includes/pluggable.php';
      }
      define('UTM_WP_REST_API_NONCE', wp_create_nonce('wp_rest'));
    }

    // Auth by JWT
    if (UTM_WP_REST_API_AUTH_MODE === 'jwt') {
      add_filter('rest_api_init', [$this, 'addCorsSupport']);
      add_filter('rest_pre_dispatch', [$this, 'checkJwtToken']);
    }

    // Caching
    add_filter('rest_pre_dispatch', [$this, 'responseByCache'], 10, 2);
    add_filter('rest_pre_echo_response', [$this, 'cacheResponse'], 10, 3);

    add_filter(UTM_WP_REST_TEXTDOMAIN . '_global_js_data', [$this, 'addAPIData'], 10, 1);
    add_filter(UTM_WP_REST_TEXTDOMAIN . '_admin_js_data', [$this, 'addAPIData'], 10, 1);

    // Setup RestApi
    $this->setupRestApiUsers();
    $this->setupRestApiMenu();
    $this->setupRestApiSettings();
  }

  public function addCorsSupport() {
    if (UTM_WP_REST_API_AUTH_MODE === 'jwt') {
      header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Authorization');
    }

    $sToken = $this->PWP->tool->getTokenFromAuthHeader();
    define('UTM_WP_REST_API_JWT_TOKEN', $sToken);
  }

  public function validateJwtToken($sToken = '', $bShowData = false) {
    // Get token form header
    if (empty($sToken)) {
      $sToken = $this->PWP->tool->getTokenFromAuthHeader();
    }

    if (empty($sToken)) {
      return false;
    }

    try {
      $oToken = (new Parser())->parse((string) $sToken);
    } catch (RuntimeException $e) {
      return false;
    }

    $oData  = new ValidationData();
    $bValid = $oToken->validate($oData);

    if ($bShowData) {
      if (!$bValid) {
        // ! If an invalid token is available, the current user will logout !?
        wp_clear_auth_cookie();

        return new WP_Error(
          'jwt_invalid_token',
          __('Invalid token.', UTM_WP_REST_TEXTDOMAIN),
          ['status' => 403]
        );
      }

      $userId = $oToken->getClaim('uid');
      $this->PWP->tool->setUserCookie($userId);

      return [
        'code' => 'jwt_valid_token',
        'data' => [
          'status'              => 200,
          'userId'              => $userId,
          'wp_get_current_user' => wp_get_current_user(),
        ],
      ];
    }

    return $bValid;
  }

  public function generateJwtToken($aConfig) {
    $iNow = time();

    $oToken = (new Builder())->setIssuer($aConfig['issuer']) // Configures the issuer (iss claim)
      ->setAudience($aConfig['audience']) // Configures the audience (aud claim)
      ->setId($aConfig['id'], true) // Configures the id (jti claim), replicating as a header item
      ->setIssuedAt($iNow) // Configures the time that the token was issue (iat claim)
      ->setNotBefore($iNow + (int) $aConfig['nbf']) // Configures the time that the token can be used (nbf claim)
      ->setExpiration($iNow + (int) $aConfig['exp']) // Configures the expiration time of the token (exp claim)
      ->set('uid', $aConfig['uid']) // Configures a new claim, called "uid"
      ->getToken(); // Retrieves the generated token

    return $oToken;
  }

  public function checkJwtToken($oRequest) {
    $oToken = $this->validateJwtToken('', true);

    if (is_wp_error($oToken)) {
      if ($oToken->get_error_code() != 'jwt_invalid_token') {
        return $oToken;
      }
    }

    return $oRequest;
  }

  public function responseByCache($oRequest) {
    global $wp;
    //var_dump($oRequest);exit;

    // API response by cache
    $sKeyCache = md5($wp->request . serialize($_REQUEST));
    if (isset($_SERVER['HTTP_X_WP_CACHE']) && $wp->matched_rule === '^wp-json/(.*)?' && $this->PWP->cache->isLoaded) {
      $this->PWP->cache->setCacheKey($sKeyCache);

      $mResponse = $this->PWP->cache->load($sKeyCache);
      if (!empty($mResponse)) {
        $mResponse['cached'] = true;
        return wp_send_json($mResponse);
      }
    }

    return $oRequest;
  }

  public function cacheResponse($result, $that, $request) {
    $sKeyCache = $this->PWP->cache->getCacheKey();
    if (isset($_SERVER['HTTP_X_WP_CACHE']) && !empty($sKeyCache) && $this->PWP->cache->isLoaded) {
      $this->PWP->cache->save($sKeyCache, $result);
    }

    return $result;
  }

  private function addTabSetting() {
    // Setting menu
    $aSetting['menu']['pwp-api'] = true;

    // Setting page
    $aSetting['page']['pwp-api-options'] = [
      'title' => __('RestAPI', UTM_WP_REST_TEXTDOMAIN),
      'icon'  => 'code',
      'hash'  => 'api',
      'menu'  => 'pwp-api',
    ];

    // Setting section
    $aSetting['section']['utm_wp_restauth_api'] = [
      'title' => __('API Auth', UTM_WP_REST_TEXTDOMAIN),
      'page'  => 'pwp-api-options',
    ];
    $aSetting['section']['utm_wp_resttest_api'] = [
      'title' => __('API Demo', UTM_WP_REST_TEXTDOMAIN),
      'page'  => 'pwp-api-options',
    ];

    // Auth mode
    $aSetting['option']['UTM_WP_REST_AUTH_MODE'] = [
      'title'    => __('API Auth Mode', UTM_WP_REST_TEXTDOMAIN),
      'section'  => 'utm_wp_restauth_api',
      'type'     => 'array',
      'value'    => ['jwt'],
      'elements' => [
        ['htmltag' => 'template',
          'file'     => 'element-tooltip',
          'attr'     => [
            'data-tooltip' => __('Select auth mode.', UTM_WP_REST_TEXTDOMAIN),
          ],
        ],
        ['htmltag' => 'template',
          'file'     => 'element-select',
          'attr'     => [
            'multiple'      => false,
            'data-onchange' => json_encode(['showElementFormSelect', ['jwt', '.UTM_WP_REST_AUTH_CONFIG_JWT']]),
            'data-onload'   => json_encode(['showElementFormSelect', ['jwt', '.UTM_WP_REST_AUTH_CONFIG_JWT']]),
          ],
          'option'   => [
            //'nonce' => __('Auth by Account', UTM_WP_REST_TEXTDOMAIN),
            'jwt'   => __('Auth by JWT', UTM_WP_REST_TEXTDOMAIN),
          ],
        ],
      ],
    ];
    $aSetting['option']['UTM_WP_REST_AUTH_CONFIG_JWT'] = [
      'title'    => __('Auth JWT Config', UTM_WP_REST_TEXTDOMAIN),
      'section'  => 'utm_wp_restauth_api',
      'elements' => [
        'class' => 'UTM_WP_REST_AUTH_CONFIG_JWT',
        ['htmltag' => 'template',
          'file'     => 'element-tooltip',
          'attr'     => [
            'data-tooltip' => __('Configures the time that the token can be used (nbf claim).', UTM_WP_REST_TEXTDOMAIN),
          ],
        ],
        ['htmltag' => 'template',
          'file'     => 'element-range',
          'attr'     => [
            'name'   => 'UTM_WP_REST_AUTH_CONFIG_JWT_NBF',
            'istime' => true,
            'min'    => 0,
            'max'    => 60, // 1 minute
          ],
        ],
        ['htmltag' => 'template',
          'file'     => 'element-tooltip',
          'attr'     => [
            'data-tooltip' => __('Configures the expiration time of the token (exp claim)', UTM_WP_REST_TEXTDOMAIN),
          ],
        ],
        ['htmltag' => 'template',
          'file'     => 'element-range',
          'attr'     => [
            'name'   => 'UTM_WP_REST_AUTH_CONFIG_JWT_EXP',
            'istime' => true,
            'min'    => 5,
            'max'    => 86400, // 1 day
          ],
        ],
      ],
    ];

    $this->PWP->setting->addSettings($aSetting);
  }

  private function addApiUsersInSetting() {
    $aSetting['option']['UTM_WP_REST_API_USERS'] = [
      'title'                      => 'Users',
      'section'                    => 'utm_wp_resttest_api',
      'type'                       => 'array',
      'value'                      => ['GET'],
      'elements'                   => [
        ['htmltag' => 'template',
          'file'     => 'element-tooltip',
          'attr'     => [
            'data-tooltip' => '*',
          ],
        ],
        ['htmltag' => 'template',
          'file'     => 'element-select',
          'attr'     => [
            'id'            => 'users_method',
            'data-onchange' => json_encode(['setInputValueFormSelect', '#users_name']),
            'data-onload'   => json_encode(['setInputValueFormSelect', '#users_name']),
          ],
          'option'   => [
            'users'               => [
              'GET'  => 'GET - Get all user',
              'POST' => 'POST - Create a user',
            ],
            'users/register'      => [
              'POST' => 'POST - Register a User',
            ],
            'users/1'             => [
              'GET'  => 'GET - Get a user',
              'POST' => 'POST - Update a User',
            ],
            'users/me'            => [
              'GET' => 'GET - Get current  user',
            ],
            'users/me/login'      => [
              'POST' => 'POST - Login  user',
            ],
            'users/me/logout'     => [
              'GET' => 'GET - Logout user',
            ],
            'users/me/forgotpass' => [
              'POST' => 'POST - Forgot Password',
            ],
            'users/me/nonce'      => [
              'GET' => 'GET - Get current nonce',
            ],
            'users/me/token'      => [
              'POST' => 'POST - Check Token',
            ],
          ],
        ],
        ['htmltag' => 'input',
          'attr'     => [
            'id'       => 'users_name',
            'value'    => '',
            'type'     => 'text',
            'disabled' => true,
          ],
        ],
        ['htmltag' => 'textarea',
          'attr'     => [
            'name'   => 'UTM_WP_REST_API_USERS_DATA',
            'id'     => 'users_data',
            'format' => 'jsonp',
            'style'  => 'height:60px',
          ],
          'text'     => 'UTM_WP_REST_API_USERS_DATA',
        ],
        ['htmltag' => 'a',
          'text'     => 'Send',
          'attr'     => [
            'name'         => '',
            'style'        => 'width:10rem',
            'class'        => 'btn-small red_wp waves-effect waves-light',
            'data-onclick' => json_encode(['sendRequestAPI', [
              'method' => '#users_method',
              'api'    => '#users_name',
              'data'   => '#users_data',
            ]]),
          ],
        ],
      ],
      // Hidden option
      'UTM_WP_REST_API_USERS_DATA' => [
        'title'   => '',
        'section' => 'utm_wp_resttest_api',
        'type'    => 'array',
        'value'   => [],
      ],
    ];
    $this->PWP->setting->addSettings($aSetting);
  }

  private function addApiMenusInSetting() {
    $aSetting['option']['UTM_WP_REST_API_MENUS'] = [
      'title'                      => 'Menus',
      'section'                    => 'utm_wp_resttest_api',
      'type'                       => 'array',
      'value'                      => ['GET'],
      'elements'                   => [
        ['htmltag' => 'template',
          'file'     => 'element-tooltip',
          'attr'     => [
            'data-tooltip' => '*',
          ],
        ],
        ['htmltag' => 'template',
          'file'     => 'element-select',
          'attr'     => [
            'id'            => 'menus_method',
            'data-onchange' => json_encode(['setInputValueFormSelect', '#menus_name']),
            'data-onload'   => json_encode(['setInputValueFormSelect', '#menus_name']),
          ],
          'option'   => [
            'menus' => [
              'GET' => 'GET - Get menus',
            ],
          ],
        ],
        ['htmltag' => 'input',
          'attr'     => [
            'id'       => 'menus_name',
            'value'    => '',
            'type'     => 'text',
            'disabled' => true,
          ],
        ],
        ['htmltag' => 'textarea',
          'attr'     => [
            'name'   => 'UTM_WP_REST_API_MENUS_DATA',
            'id'     => 'menus_data',
            'format' => 'jsonp',
            'style'  => 'height:60px',
          ],
          'text'     => 'UTM_WP_REST_API_MENUS_DATA',
        ],
        ['htmltag' => 'a',
          'text'     => 'Send',
          'attr'     => [
            'name'         => '',
            'style'        => 'width:10rem',
            'class'        => 'btn-small red_wp waves-effect waves-light',
            'data-onclick' => json_encode(['sendRequestAPI', [
              'method' => '#menus_method',
              'api'    => '#menus_name',
              'data'   => '#menus_data',
            ]]),
          ],
        ],
      ],
      // Hidden option
      'UTM_WP_REST_API_MENUS_DATA' => [
        'title'   => '',
        'section' => 'utm_wp_resttest_api',
        'type'    => 'array',
        'value'   => [],
      ],
    ];

    $this->PWP->setting->addSettings($aSetting);
  }

  private function addApiMenuLocationInSetting() {
    $aSetting['option']['UTM_WP_REST_API_MENU_LOCATIONS'] = [
      'title'                               => 'Menu Locations',
      'section'                             => 'utm_wp_resttest_api',
      'type'                                => 'array',
      'value'                               => ['GET'],
      'elements'                            => [
        ['htmltag' => 'template',
          'file'     => 'element-tooltip',
          'attr'     => [
            'data-tooltip' => '*',
          ],
        ],
        ['htmltag' => 'template',
          'file'     => 'element-select',
          'attr'     => [
            'id'            => 'menu_locations_method',
            'data-onchange' => json_encode(['setInputValueFormSelect', '#menu_locations_name']),
            'data-onload'   => json_encode(['setInputValueFormSelect', '#menu_locations_name']),
          ],
          'option'   => [
            'menu-locations' => [
              'GET' => 'GET - Get menu locations',
            ],
          ],
        ],
        ['htmltag' => 'input',
          'attr'     => [
            'id'       => 'menu_locations_name',
            'value'    => '',
            'type'     => 'text',
            'disabled' => true,
          ],
        ],
        ['htmltag' => 'textarea',
          'attr'     => [
            'name'   => 'UTM_WP_REST_API_MENU_LOCATIONS_DATA',
            'id'     => 'menu_locations_data',
            'format' => 'jsonp',
            'style'  => 'height:60px',
          ],
          'text'     => 'UTM_WP_REST_API_MENU_LOCATIONS_DATA',
        ],
        ['htmltag' => 'a',
          'text'     => 'Send',
          'attr'     => [
            'name'         => 'button-ajax',
            'style'        => 'width:10rem',
            'class'        => 'btn-small red_wp waves-effect waves-light',
            'data-onclick' => json_encode(['sendRequestAPI', [
              'method' => '#menu_locations_method',
              'api'    => '#menu_locations_name',
              'data'   => '#menu_locations_data',
            ]]),
          ],
        ],
      ],
      // Hidden option
      'UTM_WP_REST_API_MENU_LOCATIONS_DATA' => [
        'title'   => '',
        'section' => 'utm_wp_resttest_api',
        'type'    => 'array',
        'value'   => [],
      ],
    ];

    $this->PWP->setting->addSettings($aSetting);
  }

  private function addApiSettingsInSetting() {
    $aSetting['option']['UTM_WP_REST_API_SETTINGS'] = [
      'title'                         => 'Settings',
      'section'                       => 'utm_wp_resttest_api',
      'type'                          => 'array',
      'value'                         => ['GET'],
      'elements'                      => [
        ['htmltag' => 'template',
          'file'     => 'element-tooltip',
          'attr'     => [
            'data-tooltip' => '*',
          ],
        ],
        ['htmltag' => 'template',
          'file'     => 'element-select',
          'attr'     => [
            'id'            => 'settings_method',
            'data-onchange' => json_encode(['setInputValueFormSelect', '#settings_name']),
            'data-onload'   => json_encode(['setInputValueFormSelect', '#settings_name']),
          ],
          'option'   => [
            'settings'         => [
              'GET' => 'GET - Get settings',
            ],
            'settings/version' => [
              'GET' => 'GET - Get current version',
            ],
          ],
        ],
        ['htmltag' => 'input',
          'attr'     => [
            'id'       => 'settings_name',
            'value'    => '',
            'type'     => 'text',
            'disabled' => true,
          ],
        ],
        ['htmltag' => 'textarea',
          'attr'     => [
            'name'   => 'UTM_WP_REST_API_SETTINGS_DATA',
            'id'     => 'settings_data',
            'format' => 'jsonp',
            'style'  => 'height:60px',
          ],
          'text'     => 'UTM_WP_REST_API_SETTINGS_DATA',
        ],
        ['htmltag' => 'a',
          'text'     => 'Send',
          'attr'     => [
            'name'         => 'button-ajax',
            'style'        => 'width:10rem',
            'class'        => 'btn-small red_wp waves-effect waves-light',
            'data-onclick' => json_encode(['sendRequestAPI', [
              'method' => '#settings_method',
              'api'    => '#settings_name',
              'data'   => '#settings_data',
            ]]),
          ],
        ],
      ],
      // Hidden option
      'UTM_WP_REST_API_SETTINGS_DATA' => [
        'title'   => '',
        'section' => 'utm_wp_resttest_api',
        'type'    => 'array',
        'value'   => [],
      ],
    ];

    $this->PWP->setting->addSettings($aSetting);
  }

  private function addApiCustomInSetting() {
    $aSetting['option']['UTM_WP_REST_API_CUSTOM'] = [
      'title'                       => 'Custom: ' . UTM_WP_REST_API_NAMESPACE,
      'section'                     => 'utm_wp_resttest_api',
      'type'                        => 'array',
      'value'                       => ['GET'],
      'elements'                    => [
        ['htmltag' => 'template',
          'file'     => 'element-tooltip',
          'attr'     => [
            'data-tooltip' => '*',
          ],
        ],
        ['htmltag' => 'template',
          'file'     => 'element-select',
          'attr'     => [
            'id' => 'custom_method',
          ],
          'option'   => [
            'GET'    => 'GET',
            'POST'   => 'POST',
            'PUT'    => 'PUT',
            'PATCH'  => 'PATCH',
            'DELETE' => 'DELETE',
          ],
        ],
        ['htmltag' => 'input',
          'attr'     => [
            'name' => 'UTM_WP_REST_API_CUSTOM_NAME',
            'id'   => 'custom_name',
            'type' => 'text',
          ],
        ],
        ['htmltag' => 'textarea',
          'attr'     => [
            'name'   => 'UTM_WP_REST_API_CUSTOM_DATA',
            'id'     => 'custom_data',
            'format' => 'jsonp',
            'style'  => 'height:60px',
          ],
          'text'     => 'UTM_WP_REST_API_CUSTOM_DATA',
        ],
        ['htmltag' => 'a',
          'text'     => 'Send',
          'attr'     => [
            'name'         => '',
            'style'        => 'width:10rem',
            'class'        => 'btn-small red_wp waves-effect waves-light',
            'data-onclick' => json_encode(['sendRequestAPI', [
              'method' => '#custom_method',
              'api'    => '#custom_name',
              'data'   => '#custom_data',
            ]]),
          ],
        ],
      ],
      // Hidden option
      'UTM_WP_REST_API_CUSTOM_NAME' => [
        'title'   => '',
        'section' => 'utm_wp_resttest_api',
        'type'    => 'string',
        'value'   => '',
      ],
      'UTM_WP_REST_API_CUSTOM_DATA' => [
        'title'   => '',
        'section' => 'utm_wp_resttest_api',
        'type'    => 'array',
        'value'   => [],
      ],
    ];

    $this->PWP->setting->addSettings($aSetting);
  }

  // Add nonce for js
  public function addAPIData($aData) {
    $aData['api']['url']   = esc_url_raw(rest_url(UTM_WP_REST_API_NAMESPACE));
    $aData['api']['nonce'] = defined('UTM_WP_REST_API_NONCE') ? UTM_WP_REST_API_NONCE : null;

    return $aData;
  }

  ///////////// API Menus /////////////

  // Format:
  // - Menus: /wp-json/wp/v2/menus
  // - Locations: /wp-json/wp/v2/menu-locations
  private function setupRestApiMenu() {
    require_once UTM_WP_REST_API_PATH . 'rest-api-menus.php';
    $oApiMenus = new RestApiMenus($this->PWP);
    add_action('rest_api_init', [$oApiMenus, 'registerRestRoute'], 10);
  }

  ///////////// API User /////////////

  // Format:
  // - User: /wp-json/wp/v2/user
  private function setupRestApiUsers() {
    require_once UTM_WP_REST_API_PATH . 'rest-api-users.php';
    $oApiUsers = new RestApiUsers($this->PWP);
    //add_action('rest_api_init', [$oApiUsers, 'registerRestField'], 10);
    add_action('rest_api_init', [$oApiUsers, 'registerRestRoute'], 10);
  }

  //////////// API Settings /////////////

  // Format:
  // - Settings: /wp-json/wp/v2/settings
  private function setupRestApiSettings() {
    require_once UTM_WP_REST_API_PATH . 'rest-api-settings.php';
    $oApiSettings = new RestApiSettings($this->PWP);
    add_action('rest_api_init', [$oApiSettings, 'registerRestRoute'], 10);
  }
}