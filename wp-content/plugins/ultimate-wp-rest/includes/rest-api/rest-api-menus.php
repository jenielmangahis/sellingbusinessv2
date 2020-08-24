<?php
// Blocking access direct to the plugin
defined('UTM_WP_REST_BASE') or die('No script kiddies please!');

class RestApiMenus {
  ///////////// Call PWPCore /////////////
  public function __construct(&$PWP) {
    $this->PWP = &$PWP;

    define('UTM_WP_REST_API_MENUS', '/menus');
    define('UTM_WP_REST_API_MENU_LOCATIONS', '/menu-locations');
  }

  public function registerRestRoute() {

    // Get all menus
    register_rest_route(UTM_WP_REST_API_NAMESPACE, UTM_WP_REST_API_MENUS, [
      [
        'methods'  => WP_REST_Server::READABLE,
        'callback' => [$this, 'getMenus'],
      ],
    ]);

    // Get a menu by id
    register_rest_route(UTM_WP_REST_API_NAMESPACE, UTM_WP_REST_API_MENUS . '/(?P<id>\d+)', [
      [
        'methods'  => WP_REST_Server::READABLE,
        'callback' => [$this, 'getMenu'],
      ],
    ]);

    // Get all menu locations
    register_rest_route(UTM_WP_REST_API_NAMESPACE, UTM_WP_REST_API_MENU_LOCATIONS, [
      [
        'methods'  => WP_REST_Server::READABLE,
        'callback' => [$this, 'getMenuLocations'],
      ],
    ]);

    // Get a menu location by name
    register_rest_route(UTM_WP_REST_API_NAMESPACE, UTM_WP_REST_API_MENU_LOCATIONS . '/(?P<location>[a-zA-Z0-9_-]+)', [
      [
        'methods'  => WP_REST_Server::READABLE,
        'callback' => [$this, 'getMenuLocation'],
      ],
    ]);
  }

  ////////////////////////////////////////
  public function getMenus($oRequest) {
    $sCurrentLink = rest_url(UTM_WP_REST_API_NAMESPACE) . UTM_WP_REST_API_MENUS;

    $cached_wp_get_nav_menus = $this->PWP->cache->memcached ? $this->PWP->cache->wrap('wp_get_nav_menus') : 'wp_get_nav_menus';
    $oMenus                  = $cached_wp_get_nav_menus();

    if (empty($oMenus)) {
      $aJson = [
        'code'    => 'rest_menus_empty',
        'message' => __('Menus is empty', UTM_WP_REST_TEXTDOMAIN),
        'data'    => [],
        'meta'    => [
          'links' => [
            'current' => $sCurrentLink,
          ],
        ],
      ];

      return new WP_Error($aJson, 404);
    }

    $aMenu = $this->formatMenus($oMenus);

    $aJson = [
      'code'    => 'rest_menus_data',
      'message' => __('Menus data', UTM_WP_REST_TEXTDOMAIN),
      'data'    => $aMenu,
      'meta'    => [
        'links' => [
          'current' => $sCurrentLink,
        ],
      ],
    ];
    return new WP_REST_Response($aJson, 200);
  }

  public function getMenu($oRequest) {
    $iID          = (int) $oRequest['id'];
    $sCurrentLink = rest_url(UTM_WP_REST_API_NAMESPACE) . UTM_WP_REST_API_MENUS . '/' . $iID;

    $cached_wp_get_nav_menu_object = $this->PWP->cache->memcached ? $this->PWP->cache->wrap('wp_get_nav_menu_object') : 'wp_get_nav_menu_object';
    $cached_wp_get_nav_menu_items  = $this->PWP->cache->memcached ? $this->PWP->cache->wrap('wp_get_nav_menu_items') : 'wp_get_nav_menu_items';
    
    $oNavMenu      = $iID ? $cached_wp_get_nav_menu_object($iID) : [];
    $oNaveMenuItem = $iID ? $cached_wp_get_nav_menu_items($iID) : [];

    $aMenu = (array) $oNavMenu;

    if (!empty($aMenu)) {
      $aMenu['items'] = $oNaveMenuItem;

      $aMenu = $this->formatMenus($aMenu);

      $iStatus = 200;
      $aJson   = [
        'code'    => 'rest_menu_data',
        'message' => __('Menu data', UTM_WP_REST_TEXTDOMAIN),
        'data'    => $aMenu,
        'meta'    => [
          'links' => [
            'current' => $sCurrentLink,
          ],
        ],
      ];
    } else {
      $iStatus = 404;
      $aJson   = [
        'code'    => 'rest_menu_invalid_id',
        'message' => __('Invalid menu ID', UTM_WP_REST_TEXTDOMAIN),
        'data'    => $aMenu,
        'meta'    => [
          'links' => [
            'current' => $sCurrentLink,
          ],
        ],
      ];
    }
    
    return new WP_REST_Response($aJson, $iStatus);
  }

  public function getMenuLocations($oRequest) {
    $sCurrentLink = rest_url(UTM_WP_REST_API_NAMESPACE) . UTM_WP_REST_API_MENU_LOCATIONS;

    $cached_get_nav_menu_locations   = $this->PWP->cache->memcached ? $this->PWP->cache->wrap('get_nav_menu_locations') : 'get_nav_menu_locations';
    $cached_get_registered_nav_menus = $this->PWP->cache->memcached ? $this->PWP->cache->wrap('get_registered_nav_menus') : 'get_registered_nav_menus';

    $oLocations       = $cached_get_nav_menu_locations();
    $oRegisteredMenus = $cached_get_registered_nav_menus();
    $aMenusLocation   = [];

    if (!empty($oLocations) && !empty($oRegisteredMenus)) {
      foreach ($oRegisteredMenus as $sSlug => $sLabel) {
        if (!isset($oLocations[$sSlug])) {
          continue;
        }

        $aMenusLocation[$sSlug]          = (array) $oLocations;
        $aMenusLocation[$sSlug]['label'] = $sLabel;
      }
    }

    if (empty($aMenusLocation)) {
      $aJson = [
        'code'    => 'rest_menu_locations_empty',
        'message' => __('Menu locations is empty', UTM_WP_REST_TEXTDOMAIN),
        'data'    => [],
        'meta'    => [
          'links' => [
            'current' => $sCurrentLink,
          ],
        ],
      ];

      return new WP_Error($aJson, 404);
    }

    $aJson = [
      'code'    => 'rest_menu_locations_data',
      'message' => __('Menu locations data', UTM_WP_REST_TEXTDOMAIN),
      'data'    => $aMenusLocation,
      'meta'    => [
        'links' => [
          'current' => $sCurrentLink,
        ],
      ],
    ];
    return new WP_REST_Response($aJson, 200);

  }

  public function getMenuLocation($oRequest) {
    $sLocation    = $oRequest['location'];
    $sCurrentLink = rest_url(UTM_WP_REST_API_NAMESPACE) . UTM_WP_REST_API_MENU_LOCATIONS . '/' . $sLocation;

    $cached_get_nav_menu_locations = $this->PWP->cache->memcached ? $this->PWP->cache->wrap('get_nav_menu_locations') : 'get_nav_menu_locations';

    $oLocations    = $cached_get_nav_menu_locations();
    $aMenuLocation = [];

    if (!isset($oLocations[$sLocation])) {
      $aJson = [
        'code'    => 'rest_menu_location_invalid_name',
        'message' => __('Invalid menu location ID.', UTM_WP_REST_TEXTDOMAIN),
        'data'    => [],
        'meta'    => [
          'links' => [
            'current' => $sCurrentLink,
          ],
          //'debug' => [$oRequest, $oLocations],
        ],
      ];
      return new WP_Error($aJson, 404);
    }

    $cached_wp_get_nav_menu_object = $this->PWP->cache->memcached ? $this->PWP->cache->wrap('wp_get_nav_menu_object') : 'wp_get_nav_menu_object';
    $cached_wp_get_nav_menu_items  = $this->PWP->cache->memcached ? $this->PWP->cache->wrap('wp_get_nav_menu_items') : 'wp_get_nav_menu_items';

    $oNavMenu      = $cached_wp_get_nav_menu_object($oLocations[$sLocation]);
    $oNaveMenuItem = $cached_wp_get_nav_menu_items($oNavMenu->term_id);
    $aMenuLocation = array_reverse($oNaveMenuItem);
    $aMenu         = $this->formatMenus($aMenuLocation);

    $iStatus = 200;
    $aJson   = [
      'code'    => 'rest_menu_location_data',
      'message' => __('Menu location data', UTM_WP_REST_TEXTDOMAIN),
      'data'    => $aMenu,
      'meta'    => [
        'links' => [
          'current' => $sCurrentLink,
        ],
        //'debug' => [$oRequest, $oLocations],
      ],
    ];
    return new WP_REST_Response($aJson, 200);
  }

  private function formatMenus($aMenu) {
    $aMenu = $this->PWP->tool->objectToArray($aMenu);

    $formatData = function ($menus) {
      $data = [];
      foreach ($menus as $item) {
        if (!isset($item['ID'])) {
          continue;
        }

        $object_slug = explode('/', $item['url']);
        $object_slug = $object_slug[count($object_slug) - 2];

        $data[$item['ID']] = [
          'id'          => $item['ID'],
          'order'       => $item['menu_order'],
          'parent'      => $item['menu_item_parent'],
          'title'       => $item['title'],
          'url'         => $item['url'],
          'attr'        => $item['attr_title'],
          'target'      => $item['target'],
          'classes'     => $item['classes'],
          'xfn'         => $item['xfn'],
          'description' => $item['description'],
          'object_id'   => $item['object_id'],
          'object'      => $item['object'],
          'object_slug' => $object_slug,
          'type'        => $item['type'],
          'type_label'  => $item['type_label'],
        ];
      }
      return $data;
    };

    $buildTree = function ($menus, $parentId = 0) use (&$buildTree) {
      $branch = [];

      foreach ($menus as $item) {
        if ($item['parent'] == $parentId) {
          $children = $buildTree($menus, $item['id']);
          if ($children) {
            $item['children'] = $children;
          }
          $branch[] = $item;
        }
      }

      return $branch;
    };

    $aMenuItem = [];
    if (!empty($aMenu['items'])) {
      $aMenuItem = $formatData($aMenu['items']);
    } elseif (isset($aMenu[0]['ID'])) {
      $aMenuItem = $formatData($aMenu);
    }

    $aMenuItem = $buildTree($aMenuItem);

    $aNewMenu = [];

    $formatMenu = function ($menus) use ($aMenuItem) {
      $newMenus = [];
      foreach ($menus as $key => $menu) {
        if (empty($menu['term_id'])) {
          continue;
        }
        $newMenus[$key] = [
          'id'          => $menu['term_id'],
          'name'        => $menu['name'],
          'slug'        => $menu['slug'],
          'description' => $menu['description'],
          'count'       => $menu['count'],
        ];
        if (!empty($aMenuItem)) {
          $newMenus[$key]['items'] = $aMenuItem;
        }
      }
      return $newMenus;
    };

    if (isset($aMenu['term_id'])) {
      $aNewMenu = $formatMenu([$aMenu])[0];
    } elseif (isset($aMenu[0]['ID'])) {
      $aNewMenu = $aMenuItem;
    } else {
      $aNewMenu = $formatMenu($aMenu);
    }

    return $aNewMenu;
  }
}