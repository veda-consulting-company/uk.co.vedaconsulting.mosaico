<?php

class CRM_Mosaico_Page_Editor extends CRM_Core_Page {
  const DEFAULT_MODULE_WEIGHT = 200;

  function run() {
    $smarty = CRM_Core_Smarty::singleton();
    $smarty->assign('scriptUrls', $this->getScriptUrls());
    $smarty->assign('styleUrls', $this->getStyleUrls());
    $smarty->assign('mosaicoConfig', json_encode(
      $this->createMosaicoConfig(),
      defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 0
    ));
    echo $smarty->fetch('CRM/Mosaico/Page/Editor.tpl');
    CRM_Utils_System::civiExit();
  }

  protected function getScriptUrls() {
    $cacheCode = CRM_Core_Resources::singleton()->getCacheCode();
    $mosaicoDistUrl = CRM_Mosaico_Utils::getMosaicoDistUrl('relative');
    $mosaicoExtUrl = CRM_Core_Resources::singleton()->getUrl('uk.co.vedaconsulting.mosaico');
    return array(
      "{$mosaicoDistUrl}/vendor/knockout.js?r={$cacheCode}",
      "{$mosaicoDistUrl}/vendor/jquery.min.js?r={$cacheCode}",
      "{$mosaicoDistUrl}/vendor/jquery-ui.min.js?r={$cacheCode}",
      "{$mosaicoDistUrl}/vendor/jquery.ui.touch-punch.min.js?r={$cacheCode}",
      "{$mosaicoDistUrl}/vendor/load-image.all.min.js?r={$cacheCode}",
      "{$mosaicoDistUrl}/vendor/canvas-to-blob.min.js?r={$cacheCode}",
      "{$mosaicoDistUrl}/vendor/jquery.iframe-transport.js?r={$cacheCode}",
      "{$mosaicoDistUrl}/vendor/jquery.fileupload.js?r={$cacheCode}",
      "{$mosaicoDistUrl}/vendor/jquery.fileupload-process.js?r={$cacheCode}",
      "{$mosaicoDistUrl}/vendor/jquery.fileupload-image.js?r={$cacheCode}",
      "{$mosaicoDistUrl}/vendor/jquery.fileupload-validate.js?r={$cacheCode}",
      "{$mosaicoDistUrl}/vendor/knockout-jqueryui.min.js?r={$cacheCode}",
      "{$mosaicoDistUrl}/vendor/tinymce.min.js?r={$cacheCode}",
      "{$mosaicoDistUrl}/mosaico.min.js?v=0.15?&={$cacheCode}",
    );
  }

  protected function getStyleUrls() {
    $cacheCode = CRM_Core_Resources::singleton()->getCacheCode();
    $mosaicoDistUrl = CRM_Mosaico_Utils::getMosaicoDistUrl('relative');
    // $mosaicoExtUrl = CRM_Core_Resources::singleton()->getUrl('uk.co.vedaconsulting.mosaico');
    return array(
      "{$mosaicoDistUrl}/mosaico-material.min.css?v=0.10&r={$cacheCode}",
      "{$mosaicoDistUrl}/vendor/notoregular/stylesheet.css?r={$cacheCode}",
    );
  }


  /**
   * Generate the configuration options for `Mosaico.init()`.
   *
   * @return array
   */
  protected function createMosaicoConfig() {
    return array(
      'imgProcessorBackend' => $this->getUrl('civicrm/mosaico/img', NULL, TRUE),
      'emailProcessorBackend' => $this->getUrl('civicrm/mosaico/dl', NULL, FALSE),
      'titleToken' => 'MOSAICO Responsive Email Designer',
      'fileuploadConfig' => array(
        'url' => $this->getUrl('civicrm/mosaico/upload', NULL, FALSE),
        // messages??
      ),
    );
  }

  /**
   * Get the URL for a Civi route.
   *
   * @param string $path
   *   Ex: 'civicrm/admin/foo'.
   * @param string $query
   *   Ex: 'reset=1&id=123'.
   * @param bool $frontend
   * @return string
   */
  protected function getUrl($path, $query, $frontend) {
    // This function shouldn't really exist, but it's tiring to set `$htmlize`
    // to false every.single.time we need a URL.
    return CRM_Utils_System::url($path, $query, FALSE, NULL, FALSE, $frontend);
  }

}
