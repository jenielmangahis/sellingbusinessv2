<?php
// Blocking access direct to the plugin
defined('UTM_WP_REST_BASE') or die('No script kiddies please!');

class RestApiUsers extends WP_REST_Controller {
  protected $meta;
  private $oUsersController;

  ///////////// Call PWPCore /////////////
  public function __construct(&$PWP) {
    $this->PWP = &$PWP;

    define('UTM_WP_REST_API_USERS', '/users');

    $this->meta             = new WP_REST_User_Meta_Fields();
    $this->oUsersController = new WP_REST_Users_Controller();
  }

  public function registerRestField() {
    register_rest_field('user', 'info', [
      'get_callback' => [$this, 'addFieldInfo'],
      'schema'       => null,
    ]);
    add_filter('rest_prepare_user', [$this, 'mergeUserInfo'], 10, 3);
  }

  public function registerRestRoute() {
    // User Register
    register_rest_route(UTM_WP_REST_API_NAMESPACE, UTM_WP_REST_API_USERS . '/register', [
      [
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => [$this->oUsersController, 'create_item'],
        'permission_callback' => [$this, 'registerUserPermissionsCheck'],
        'args'                => $this->get_endpoint_args_for_item_schema(WP_REST_Server::CREATABLE),
      ],
    ]);

    // User Login
    register_rest_route(UTM_WP_REST_API_NAMESPACE, UTM_WP_REST_API_USERS . '/me/login', [
      [
        'methods'             => WP_REST_Server::CREATABLE,
        'callback'            => [$this, 'loginUser'],
        'permission_callback' => [$this, 'loginUserPermissionsCheck'],
        'args'                => [
          'username' => [
            'type' => 'string',
          ],
          'password' => [
            'type' => 'string',
          ],
          'remember' => [
            'default' => true,
          ],
        ],
      ],
    ]);

    // User logout
    register_rest_route(UTM_WP_REST_API_NAMESPACE, UTM_WP_REST_API_USERS . '/me/logout', [
      [
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => [$this, 'logoutUser'],
        'permission_callback' => [$this, 'logoutUserPermissionsCheck'],
      ],
    ]);

    // Get current nonce
    register_rest_route(UTM_WP_REST_API_NAMESPACE, UTM_WP_REST_API_USERS . '/me/nonce', [
      [
        'methods'             => WP_REST_Server::READABLE,
        'callback'            => [$this, 'getCurrentNonce'],
        'permission_callback' => [$this, 'getNoncePermissionsCheck'],
      ],
    ]);

    // Get current user
    register_rest_route(UTM_WP_REST_API_NAMESPACE, UTM_WP_REST_API_USERS . '/me', [
      [
        'methods'  => WP_REST_Server::READABLE,
        'callback' => [$this, 'getCurrentUser'],
        'args'     => [
          'context' => $this->oUsersController->get_context_param(['default' => 'view']),
        ],
      ],
    ]);

    // Token auth
    register_rest_route(UTM_WP_REST_API_NAMESPACE, UTM_WP_REST_API_USERS . '/me/token', [
      [
        'methods'  => WP_REST_Server::CREATABLE,
        'callback' => [$this, 'checkToken'],
      ],
    ]);

    // Get current nonce
    register_rest_route(UTM_WP_REST_API_NAMESPACE, UTM_WP_REST_API_USERS . '/me/forgotpass', [
      [
        'methods'  => WP_REST_Server::CREATABLE,
        'callback' => [$this, 'forgotPassword'],
        'args'     => [
          'user' => [
            'type' => 'string',
          ],
        ],
      ],
    ]);
  }

  ////////////////////////////////////////
  public function addFieldInfo($oData, $sFieldName, $oRequest) {
    $aUserInfo = [
      'username'   => $oData['username'],
      'first_name' => $oData['first_name'],
      'last_name'  => $oData['last_name'],
      'email'      => $oData['user_email'],
      'nickname'   => $oData['nickname'],
      //'roles'           => $oData['roles'],
      //'registered_date' => $oData['registered_date'],
      //'debug'      => [$oData, $sFieldName, $oRequest],
    ];
    return $aUserInfo;
  }

  public function mergeUserInfo($oResponse, $oUser, $oRequest) {
    return new WP_Error('rest_user_not_logged_in', __('You are not currently logged in.', UTM_WP_REST_TEXTDOMAIN), ['status' => 403]);
    $aData     = $this->PWP->tool->objectToArray($oResponse);
    $aUserInfo = $aData['data']['info'];
    unset($aData['data']['info']);
    return array_merge($aData['data'], $aUserInfo);
  }

  public function registerUserPermissionsCheck() {
    return get_option('users_can_register');
  }

  public function loginUserPermissionsCheck() {
    $sToken = $this->PWP->tool->getTokenFromAuthHeader();

    if (UTM_WP_REST_API_AUTH_MODE === 'jwt' && !empty($sToken)) {
      return new WP_Error('rest_user_bad_auth_header', __('No authorization header required.', UTM_WP_REST_TEXTDOMAIN), ['status' => 403]);
    }

    if (!isset($_SERVER['HTTP_X_WP_NONCE']) && UTM_WP_REST_API_AUTH_MODE === 'nonce') {
      return new WP_Error('rest_cookie_invalid_nonce', __('Cookie nonce is invalid', UTM_WP_REST_TEXTDOMAIN), ['status' => 403]);
    }

    if (is_user_logged_in()) {
      return new WP_Error('rest_user_logged_in', __('You are currently logged in.', UTM_WP_REST_TEXTDOMAIN), ['status' => 403]);
    }

    return true;
  }

  public function logoutUserPermissionsCheck() {
    $sToken = $this->PWP->tool->getTokenFromAuthHeader();

    if (UTM_WP_REST_API_AUTH_MODE === 'jwt' && empty($sToken)) {
      return new WP_Error('rest_user_bad_auth_header', __('Authorization header malformed.', UTM_WP_REST_TEXTDOMAIN), ['status' => 403]);
    }

    if (!isset($_SERVER['HTTP_X_WP_NONCE']) && UTM_WP_REST_API_AUTH_MODE === 'nonce') {
      return new WP_Error('rest_cookie_invalid_nonce', __('Cookie nonce is invalid', UTM_WP_REST_TEXTDOMAIN), ['status' => 403]);
    }

    if (!is_user_logged_in()) {
      return new WP_Error('rest_user_not_logged_in', __('You are not currently logged in.', UTM_WP_REST_TEXTDOMAIN), ['status' => 403]);
    }

    return true;
  }

  public function getNoncePermissionsCheck() {
    if (UTM_WP_REST_API_AUTH_MODE !== 'nonce') {
      return new WP_Error('rest_user_nonce_disabled', __('Nonce code is not available.', UTM_WP_REST_TEXTDOMAIN), ['status' => 403]);
    }
    return true;
  }

  ////////////////////////////////////////
  public function loginUser($oRequest) {
    $oUser = wp_authenticate($oRequest['username'], $oRequest['password']);

    if (is_wp_error($oUser)) {
      return $oUser;
    }

    if (UTM_WP_REST_API_AUTH_MODE === 'nonce') {
      $this->PWP->tool->setUserCookie($oUser->ID);
    }

    $aJson = [
      'code'    => 'rest_user_login_success',
      'message' => __('Logged in successfully.', UTM_WP_REST_TEXTDOMAIN),
      'data'    => [
        'user_id' => $oUser->ID,
      ],
    ];

    $sToken = null;
    if (UTM_WP_REST_API_AUTH_MODE === 'jwt') {
      $aConfig = [
        'issuer'   => $this->PWP->tool->getMainDomain(UTM_WP_REST_SERVER),
        'audience' => $this->PWP->tool->getMainDomain($_SERVER['REQUEST_URI']),
        'id'       => wp_create_nonce('wp_rest_jwt'),
        'uid'      => $oUser->ID,
        'nbf'      => $this->PWP->setting->getOption('UTM_WP_REST_AUTH_CONFIG_JWT_NBF'),
        'exp'      => $this->PWP->setting->getOption('UTM_WP_REST_AUTH_CONFIG_JWT_EXP'),
      ];
      $sToken                 = (string) $this->PWP->api->generateJwtToken($aConfig);
      $aJson['data']['token'] = $sToken;
    }

    return new WP_REST_Response($aJson, 200);
  }

  public function logoutUser($oRequest) {
    wp_clear_auth_cookie();
    wp_logout();

    $aJson = [
      'code'    => 'rest_user_logout_success',
      'message' => __('Log out successfully.', UTM_WP_REST_TEXTDOMAIN),
    ];
    return new WP_REST_Response($aJson, 200);
  }

  public function getCurrentNonce($oRequest) {
    $aJson = [
      'code'    => 'rest_user_get_nonce',
      'message' => __('Get current nonce.', UTM_WP_REST_TEXTDOMAIN),
      'data'    => defined('UTM_WP_REST_API_NONCE') ? UTM_WP_REST_API_NONCE : false,
    ];
    return new WP_REST_Response($aJson, 200);
  }

  public function getCurrentUser($oRequest) {
    $iUserID = get_current_user_id();

    if (empty($iUserID)) {
      return new WP_Error('rest_not_logged_in', __('You are not currently logged in.'), ['status' => 401]);
    }

    $oUser     = wp_get_current_user();
    //return new WP_REST_Response($oUser, 200);

    $oResponse = $this->oUsersController->prepare_item_for_response($oUser, $oRequest);

    $aUserInfo = [
      'nickname'   => $oUser->nickname,
      'first_name' => $oUser->first_name,
      'last_name'  => $oUser->last_name,
      'email'      => $oUser->user_email,
    ];

    $aData = $this->PWP->tool->objectToArray($oResponse);
    $aJson = array_merge($aUserInfo, $aData['data']);

    return new WP_REST_Response($aJson, 200);
  }

  public function checkToken($oRequest) {
    $isValid = $this->PWP->api->validateJwtToken($oRequest['token']);

    if (!$isValid) {
      $aJson = [
        'code'    => 'rest_user_invalid_token',
        'message' => __('Invalid token.', UTM_WP_REST_TEXTDOMAIN),
      ];
      return new WP_REST_Response($aJson, 403);
    }

    $aJson = [
      'code'    => 'rest_user_valid_token',
      'message' => __('Valid token.', UTM_WP_REST_TEXTDOMAIN),
    ];
    return new WP_REST_Response($aJson, 200);
  }

  public function forgotPassword($oRequest) {
    $sUser = isset($oRequest['user']) ? trim(wp_unslash($oRequest['user'])) : false;
    if ($sUser === false) {
      $aJson = [
        'code'    => 'rest_user_name_empty',
        'message' => __('Enter a username or email address', UTM_WP_REST_TEXTDOMAIN),
      ];
      return new WP_Error($aJson, 404);
    }

    $cached_get_user_by = $this->PWP->cache->memcached ? $this->PWP->cache->wrap('get_user_by') : 'get_user_by';
    $oUser              = $cached_get_user_by('email', $sUser);

    // Try again with username
    if ($oUser === false) {
      $oUser = $cached_get_user_by('login', $oRequest['user']);
    }

    if ($oUser === false) {
      $aJson = [
        'code'    => 'rest_user_not_found',
        'message' => __('User not found', UTM_WP_REST_TEXTDOMAIN),
      ];
      return new WP_Error($aJson, 404);
    }

    // Redefining user_login ensures we return the right case in the email.
    $sUserLogin = $oUser->user_login;
    $sUserEmail = $oUser->user_email;
    $sKey       = get_password_reset_key($oUser);

    if (is_wp_error($sKey)) {
      $aJson = [
        'code'    => 'rest_user_forgotpass_error',
        'message' => __('There was an error', UTM_WP_REST_TEXTDOMAIN),
        'error'   => $sKey,
      ];
      return new WP_Error($aJson, 500);
    }

    if (is_multisite()) {
      $sSiteName = get_network()->site_name;
    } else {
      $sSiteName = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
    }

    $sMessage = __('Someone has requested a password reset for the following account:') . "\r\n\r\n";
    /* translators: %s: site name */
    $sMessage .= sprintf(__('Site Name: %s'), $sSiteName) . "\r\n\r\n";
    /* translators: %s: user login */
    $sMessage .= sprintf(__('Username: %s'), $sUserLogin) . "\r\n\r\n";
    $sMessage .= __('If this was a mistake, just ignore this email and nothing will happen.') . "\r\n\r\n";
    $sMessage .= __('To reset your password, visit the following address:') . "\r\n\r\n";
    $sMessage .= '<' . network_site_url("wp-login.php?action=rp&key=$sKey&login=" . rawurlencode($sUserLogin), 'login') . ">\r\n";

    /* translators: Password reset email subject. %s: Site name */
    $sTitle = sprintf(__('[%s] Password Reset'), $sSiteName);

    $sTitle = apply_filters('retrieve_password_title', $sTitle, $sUserLogin, $oUser);

    $sMessage = apply_filters('retrieve_password_message', $sMessage, $sKey, $sUserLogin, $oUser);

    if ($sMessage && !wp_mail($sUserEmail, wp_specialchars_decode($sTitle), $sMessage)) {
      $aJson = [
        'code'    => 'rest_user_forgotpass_error',
        'message' => __('The email could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function.'),
      ];
      return new WP_Error($aJson, 500);
    }

    $aJson = [
      'code'    => 'rest_user_send_request_forgotpass',
      'message' => __('An email has been sent', UTM_WP_REST_TEXTDOMAIN),
    ];
    return new WP_REST_Response($aJson, 200);
  }
}