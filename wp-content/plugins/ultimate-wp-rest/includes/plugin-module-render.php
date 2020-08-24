<?php
// Blocking access direct to the plugin
defined('UTM_WP_REST_BASE') or die('No script kiddies please!');

use Latte\Engine;
use Nette\Utils\Html;

class UTM_WP_REST_Render {

  ///////////// Call PWPCore /////////////
  public function __construct(&$PWP) {
    $this->PWP = &$PWP;
  }

  ///////////// Show Notification ////////////
  public function showAdminNotification() {
    $oScreen = get_current_screen();
  }

  ///////////// Init Template /////////////
  public function initTemplateEngine() {
    $this->engine = new Latte\Engine;
    $this->engine->setTempDirectory(UTM_WP_REST_PATH_CACHE . 'template' . DS);
    $this->engine->setLoader(new Latte\Loaders\FileLoader);

    // Add filter call php function
    $this->engine->addFilter('php', function ($sFunction, ...$arg) {
      return $sFunction(...$arg);
    });
  }

  ///////////// HTML Elements ////////////
  public function runCallback(&$mValue, $sKey) {
    if (is_array($mValue)) {
      // Get callback function and parameter
      $aCallback  = array_slice($mValue, 0, 2);
      $aParameter = array_slice($mValue, 2);
      if (is_callable($aCallback)) {
        $mValue = call_user_func_array($aCallback, $aParameter);
      } else {
        array_walk($mValue, [$this, 'runCallback']);
      }
    }
  }

  public function generateElementHTML($aElements, $bPrint = true) {
    // Add more element
    if (empty($aElements)) {
      return false;
    }

    //bdump($aElements, 'Elements');

    $oElement = Html::el();

    foreach ($aElements as $aElement) {
      if (!isset($aElement['htmltag'])) {
        continue;
      }

      $sTag = $aElement['htmltag'];
      unset($aElement['htmltag']);

      // Get data for item has a callback
      array_walk($aElement, [$this, 'runCallback']);

      // Try get option value if have
      $elementValue = false;
      if (isset($aElement['attr']['name'])) {
        $elementValue = $this->PWP->setting->getOption($aElement['attr']['name']);
      }

      // If have attr name it'll have a value
      if ($elementValue && !isset($aElement['attr']['value'])) {
        $aElement['attr']['value'] = $elementValue;
      }

      // If don't have a attr name, it have value of main option name
      if (!isset($aElement['attr']['name']) && isset($aElements['value']) && !isset($aElement['attr']['value'])) {
        $aElement['attr']['name']  = $aElements['name'];
        $aElement['attr']['value'] = $aElements['value'];
      }

      // Render element by template part
      if ($sTag === 'template') {
        $sPathElement = realpath(UTM_WP_REST_PATH_ADMIN . 'template' . DS . $aElement['file'] . '.latte');
        unset($aElement['file']);
        if ($sPathElement) {
          $oHTML = $this->engine->renderToString($sPathElement, $aElement);
          $oElement->addHtml($oHTML);
        }
        continue;
      }

      $oHTML = $this->renderHtml($sTag, $aElement);

      $oElement->addHtml($oHTML);
    }

    //bdump((string) $oElement);
    if ($bPrint === false) {
      return (string) $oElement;
    }

    echo $oElement;
  }

  public function renderHtml($sTag, $aElement) {
    // Render element by array
    $oHTML = Html::el($sTag);

    // Set attr for current element
    if (!empty($aElement['attr'])) {
      $oHTML->addAttributes($aElement['attr']);
    }

    // Set dataset for current element
    if (!empty($aElement['dataset'])) {
      foreach ($aElement['dataset'] as $sName => $mDataSet) {
        $oHTML->data($sName, $mDataSet);
      }
    }

    // Set text for current element
    if (!empty($aElement['text'])) {
      if ($aElement['text'] === $aElement['attr']['name']) {
        $oHTML->setText(isset($aElement['attr']['value']) ? $aElement['attr']['value'] : '');
      } else {
        $oHTML->setText($aElement['text']);
      }
    }

    // Set html for current element
    if (!empty($aElement['html'])) {
      if ($aElement['html'] === $aElement['attr']['name']) {
        $oHTML->setHtml(isset($aElement['attr']['value']) ? $aElement['attr']['value'] : '');
      } else {
        $oHTML->setHtml($aElement['html']);
      }
    }

    // Get child elements
    $aChildElement = [];
    if (!empty($aElement['elements'])) {
      $aChildElement = $aElement['elements'];
      unset($aElement['elements']);
    }

    // Add child element
    if (!empty($aChildElement) && is_array($aChildElement)) {
      $sChildElement = $this->generateElementHTML($aChildElement, false);
      $oHTML->addHtml($sChildElement);
    }

    return $oHTML;
  }

  ///////////// Translate Template /////////////
  public function translateTemplate($sMessage) {
    return __($sMessage, UTM_WP_REST_TEXTDOMAIN);
  }

  ///////////// Render Template /////////////
  public function showSettingsPage($aMenu) {
    if (empty($aMenu)) {
      echo '<h2 class="center-align">' . __('Misconfiguration', UTM_WP_REST_TEXTDOMAIN) . '</h2>';
      return false;
    }

    // Get setting menu
    $aMenuSetting = $this->PWP->setting->menu;

    // Get setting page
    $aPage = $this->PWP->setting->page;

    // Remove menu if disable
    foreach ($aPage as $sPage => $aOption) {
      if (array_search($aOption['menu'], $aMenu) === false || $aMenuSetting[$aOption['menu']] === false) {
        unset($aPage[$sPage]);
      }
    }

    // Sort by $aMenu order
    uasort($aPage, function ($a, $b) use ($aMenu) {
      sort($aMenu);
      foreach ($aMenu as $sMenu) {
        if ($a['menu'] == $sMenu) {
          return 1;
        }

        if ($b['menu'] == $sMenu) {
          return 0;
        }
      }
    });

    $aElement = [
      'data'   => $aPage,
      'active' => isset($_COOKIE['tab_setting']) ? $_COOKIE['tab_setting'] : '',
      'attr'   => [],
    ];

    $sPathPage = realpath(UTM_WP_REST_PATH_ADMIN . 'template' . DS . 'page-setting.latte');
    $oHTML     = $this->engine->renderToString($sPathPage, $aElement);
    echo $oHTML;
  }

  // Convert to JSON
  public function json($aData) {
    $sContent = json_encode($aData, true);

    header_remove();
    header('Cache-Control: max-age=0, must-revalidate', true);
    header('Content-Type: application/json;charset=UTF-8', true);

    echo $sContent;
    exit;
  }

  // Convert to Text
  public function text($aData) {
    $sContent = implode(' ', $aData);

    header_remove();
    header('Cache-Control: max-age=0, must-revalidate', true);
    header('Content-Type: text; charset=UTF-8', true);

    echo $sContent;

    exit;
  }
}