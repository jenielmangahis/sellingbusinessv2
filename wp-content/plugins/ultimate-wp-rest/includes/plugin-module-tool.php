<?php
// Blocking access direct to the plugin
defined('UTM_WP_REST_BASE') or die('No script kiddies please!');

class UTM_WP_REST_Tool {

  ///////////// Call PWPCore /////////////
  public function __construct(&$PWP) {
    $this->PWP = &$PWP;
  }

  public function setUserCookie($userId) {
    wp_clear_auth_cookie();
    wp_set_current_user($userId);

    // ? For WP Nonce
    wp_set_auth_cookie($userId, false, is_ssl());
  }

  public function getTokenFromAuthHeader() {
    $sAuth = defined('UTM_WP_REST_API_JWT_TOKEN') ? UTM_WP_REST_API_JWT_TOKEN : '';
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
      $sAuth = $_SERVER['HTTP_AUTHORIZATION'];
    } elseif (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
      $sAuth = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
    }
    list($sToken) = sscanf($sAuth, 'Bearer %s');
    return $sToken;
  }
  public function getLinkBySlug($slug, $type = 'post') {
    $post = get_page_by_path($slug, OBJECT, $type);
    return get_permalink($post->ID);
  }

  public function getCurrentPageType($wp_query = []) {
    if (empty($wp_query)) {
      global $wp_query;
    }

    if (empty($wp_query)) {
      return $sType;
    }

    $sType = 'unknown';

    $aDataType = [
      'is_single'            => 'single',
      'is_page'              => 'page',
      'is_category'          => 'category',
      'is_preview'           => 'preview',
      'is_date'              => 'date',
      'is_year'              => 'year',
      'is_month'             => 'month',
      'is_day'               => 'day',
      'is_time'              => 'time',
      'is_author'            => 'author',
      'is_tag'               => 'tag',
      'is_tax'               => 'tax',
      'is_search'            => 'search',
      'is_feed'              => 'feed',
      'is_comment_feed'      => 'comment_feed',
      'is_trackback'         => 'trackback',
      'is_archive'           => 'archive',
      'is_404'               => '404',
      'is_embed'             => 'embed',
      'is_paged'             => 'paged',
      'is_admin'             => 'admin',
      'is_attachment'        => 'attachment',
      'is_singular'          => 'singular',
      'is_robots'            => 'robots',
      'is_posts_page'        => 'posts_page',
      'is_post_type_archive' => 'post_type_archive',
      'is_home'              => 'home',
    ];

    foreach ($aDataType as $sTest => $sValue) {
      // No break, true is more than 1
      if ($wp_query->$sTest === true) {
        $sType = $sValue;
        break;
      }
    }

    if ($sType === 'unknown') {
      $oQueried = get_queried_object();
      if (!empty($oQueried->post_type)) {
        $sType = $oQueried->post_type;
      } elseif (!empty($oQueried->taxonomy)) {
        $sType = $oQueried->taxonomy;
      }
    }

    return $sType;
  }

  public function objectToArray($input) {
    return json_decode(json_encode($input), true);
  }

  public function arrayToObject($array) {
    if (!is_array($array)) {
      return $array;
    }

    $object = new stdClass();
    if (count($array) > 0) {
      foreach ($array as $name => $value) {
        if (!empty($name) || $name >= 0) {
          $object->$name = $this->arrayToObject($value);
        }
      }
      return $object;
    } else {
      return false;
    }
  }

  public function getMainDomain($sText) {
    $sURL = $sText;

    // Get domain name form string
    if (preg_match_all('#[-a-zA-Z0-9@:%_\+.~\#?&//=]{2,256}\.[a-z]{2,4}\b(\/[-a-zA-Z0-9@:%_\+.~\#?&//=]*)?#si', $sText, $aResult, PREG_PATTERN_ORDER)) {
      $sURL = $aResult[0][0];
    }

    $aURL    = parse_url($sURL);
    $sDomain = !empty($aURL['host']) ? $aURL['host'] : false;

    if ($sDomain === false && $this->validDomainName($sText) === true) {
      $sDomain = $sText;
    }

    return $sDomain;
  }

  public function validDomainName($sDomain) {
    if (!preg_match("/^[a-z0-9][a-z0-9-._]{1,61}[a-z0-9]\.[a-z]{2,}$/i", $sDomain)) {
      return false;
    }
    return true;
  }

  public function getURLinContent($sContent) {
    preg_match_all('/(href|src)=("|\')(.*?)("|\')/smi', $sContent, $aURL);
    $aURL = array_unique($aURL[3]);

    // Had found urls
    $aFoundURL = null;
    if (boolval($aURL)) {
      foreach ($aURL as $iKey => $sURL) {
        if (!empty($sURL[1])) {
          $sCURL = $sURL[1] === '/' ? SCHEME . ':' . $sURL : $sURL;
        }
        // Filter validate url
        if (filter_var($sCURL, FILTER_VALIDATE_URL) !== false) {
          // Get file type (ext)
          preg_match('/\/[^\/\?]+\.([^\?\/]+)(\?[^\?\/]+)?$/', $sURL, $aExt);
          if (!empty($aExt[1])) {
            $aFoundURL[$iKey]['extension'] = $aExt[1];
          } else {
            $aFoundURL[$iKey]['extension'] = 'noext';
          }
          $aFoundURL[$iKey]['URL'] = $sURL;
        }
      }
    }
    return $aFoundURL;
  }

  public function arraySearchRecursive($mFind, $aData, $bOnlyParent = false, $sKeyParent = 0) {
    foreach ($aData as $sKey => $nValue) {
      if (is_array($nValue)) {
        $sPass = $sKeyParent;
        if (is_string($sKey)) {
          $sPass = $sKey;
        }
        $currentKey = $this->arraySearchRecursive($mFind, $nValue, $bOnlyParent, $sPass);
        if ($currentKey !== false) {
          return $currentKey;
        }
      } else if ($nValue === $mFind) {
        if ($bOnlyParent === true) {
          return $sKeyParent;
        }
        return $sKey;
      }
    }

    return false;
  }

  public function arrayFilterRecursive($aData, $fCallback = null, $bRemoveEmpty = false) {
    if (empty($aData)) {
      return $aData;
    }

    foreach ($aData as $sKey => &$nValue) {
      // mind the reference
      if (is_array($nValue)) {
        $nValue = $this->arrayFilterRecursive($nValue, $fCallback, $bRemoveEmpty);
        if ($bRemoveEmpty && !(bool) $nValue) {
          unset($aData[$sKey]);
        }
      } else {
        if (!is_null($fCallback) && !$fCallback($nValue, $sKey)) {
          unset($aData[$sKey]);
        } elseif (!(bool) $nValue) {
          unset($aData[$sKey]);
        }
      }
    }
    unset($nValue);
    return $aData;
  }

  public function arrayReplaceRecursive($aData, $mFind, $mReplace, $sType = 'value') {
    if (is_array($aData)) {
      foreach ($aData as $Key => $Val) {
        if (is_array($aData[$Key])) {
          $aData[$Key] = $this->arrayReplaceRecursive($aData[$Key], $mFind, $mReplace);
        } else {
          if ($sType === 'key' && $Key == $mFind) {
            $aData[$Key] = $mReplace;
          } elseif ($sType === 'value' && $Val == $mFind) {
            $aData[$Key] = $mReplace;
          }
        }
      }
    }
    return $aData;
  }

  public function getCurrentURL() {
    $sPageURL = 'http';
    if ($_SERVER["HTTPS"] == "on") {$sPageURL .= "s";}
    $sPageURL .= "://";
    $sPageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    return $sPageURL;
  }

  public function mimeContentType($sFileName) {

    $aMimeTypes = array(

      'txt'  => 'text/plain',
      'htm'  => 'text/html',
      'html' => 'text/html',
      'php'  => 'text/html',
      'css'  => 'text/css',
      'js'   => 'application/javascript',
      'json' => 'application/json',
      'xml'  => 'application/xml',
      'swf'  => 'application/x-shockwave-flash',
      'flv'  => 'video/x-flv',

      // images
      'png'  => 'image/png',
      'jpe'  => 'image/jpeg',
      'jpeg' => 'image/jpeg',
      'jpg'  => 'image/jpeg',
      'gif'  => 'image/gif',
      'bmp'  => 'image/bmp',
      'ico'  => 'image/vnd.microsoft.icon',
      'tiff' => 'image/tiff',
      'tif'  => 'image/tiff',
      'svg'  => 'image/svg+xml',
      'svgz' => 'image/svg+xml',

      // archives
      'zip'  => 'application/zip',
      'rar'  => 'application/x-rar-compressed',
      'exe'  => 'application/x-msdownload',
      'msi'  => 'application/x-msdownload',
      'cab'  => 'application/vnd.ms-cab-compressed',

      // audio/video
      'mp3'  => 'audio/mpeg',
      'qt'   => 'video/quicktime',
      'mov'  => 'video/quicktime',

      // adobe
      'pdf'  => 'application/pdf',
      'psd'  => 'image/vnd.adobe.photoshop',
      'ai'   => 'application/postscript',
      'eps'  => 'application/postscript',
      'ps'   => 'application/postscript',

      // ms office
      'doc'  => 'application/msword',
      'rtf'  => 'application/rtf',
      'xls'  => 'application/vnd.ms-excel',
      'ppt'  => 'application/vnd.ms-powerpoint',

      // open office
      'odt'  => 'application/vnd.oasis.opendocument.text',
      'ods'  => 'application/vnd.oasis.opendocument.spreadsheet',
    );

    $sExt = strtolower(array_pop(explode('.', $sFileName)));
    if (array_key_exists($sExt, $aMimeTypes)) {
      return $aMimeTypes[$sExt];
    } elseif (function_exists('finfo_open')) {
      $finfo    = finfo_open(FILEINFO_MIME);
      $mimetype = finfo_file($finfo, $sFileName);
      finfo_close($finfo);
      return $mimetype;
    } else {
      return 'application/octet-stream';
    }
  }
}
