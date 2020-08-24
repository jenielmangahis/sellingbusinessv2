<?php
// Blocking access direct to the plugin
defined('UTM_WP_REST_BASE') or die('No script kiddies please!');

use Nette\Caching\Cache;
use Nette\Caching\Storages\FileStorage;
use Nette\Caching\Storages\MemoryStorage;
use Nette\Caching\Storages\NewMemcachedStorage;
use Nette\InvalidStateException;
use Nette\NotSupportedException;
use Nette\Utils\FileSystem;

class UTM_WP_REST_Cache {
  public $memory = false;
  public $file   = false;

  public $memcached = false;

  public $isLoaded = false;

  private $sKeyCache;
  private $iTimeCache;

  ///////////// Call PWPCore /////////////
  public function __construct(&$PWP) {
    $this->PWP = &$PWP;
  }

  public function init() {
    $aCacheMode = array_flip($this->PWP->setting->getOption('UTM_WP_REST_CACHE_MODE'));

    if (empty($aCacheMode)) {
      return false;
    }

    if (isset($aCacheMode['memcached'])) {
      $sMemcachedHost = $this->PWP->setting->getOption('UTM_WP_REST_CACHE_CONFIG_MEMCACHED_HOST');
      $sMemcachedPort = $this->PWP->setting->getOption('UTM_WP_REST_CACHE_CONFIG_MEMCACHED_PORT');

      try {
        $oStorage        = new NewMemcachedStorage($sMemcachedHost, $sMemcachedPort);
        $this->memcached = new Cache($oStorage);
      } catch (NotSupportedException $e) {
        bdump($e->getMessage(), 'Module Cache');
      } catch (InvalidStateException $e) {
        bdump($e->getMessage(), 'Module Cache');
      }
    }

    if (isset($aCacheMode['memory'])) {
      $oStorage     = new MemoryStorage();
      $this->memory = new Cache($oStorage);
    }

    if (isset($aCacheMode['file'])) {
      if (!file_exists(UTM_WP_REST_PATH_CACHE)) {
        FileSystem::createDir(UTM_WP_REST_PATH_CACHE, 744);
      }
      $oStorage   = new FileStorage(UTM_WP_REST_PATH_CACHE);
      $this->file = new Cache($oStorage, 'Global');
    }

    $this->isLoaded   = true;
    $this->iTimeCache = $this->PWP->setting->getOption('UTM_WP_REST_CACHE_TIME') . ' seconds';

    // Test cache
    // bdump(UTM_WP_REST_PATH_CACHE, 'Cache file path');
    // bdump($this->iTimeCache, 'Cache time');
    // $data = $this->load('test');
    // if (!$data) {
    //   $this->save('test', 'data cached');
    // }
  }

  public function setCacheKey($name) {
    $this->sKeyCache = $name;
  }

  public function getCacheKey() {
    return $this->sKeyCache;
  }

  public function __call($function, $arguments) {
      $result = null;

      // Set time cache form setting
      if ($function === 'save' && !isset($arguments[2]['expire'])) {
        $arguments[2]['expire']  = $this->iTimeCache;
        $arguments[2]['sliding'] = false;
      }

      //bdump($arguments, $function);

      if ($this->memory !== false) {
        $result = $this->memory->$function(...$arguments);
        //bdump($result, 'memory');
      }

      if ($this->memcached !== false && ($result === null || $function === 'save')) {
        $result = $this->memcached->$function(...$arguments);
        //bdump($result, 'memcached');
      }

      if ($this->file !== false && ($result === null || $function === 'save')) {
        $result = $this->file->$function(...$arguments);
        //bdump($result, 'file');
      }

      return $result;
  }
}