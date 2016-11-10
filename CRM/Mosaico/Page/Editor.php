<?php

class CRM_Mosaico_Page_Editor extends CRM_Core_Page {
  const DEFAULT_MODULE_WEIGHT = 200;

  function run() {
    $smarty = CRM_Core_Smarty::singleton();
    $smarty->assign('scriptUrls', $this->getScriptUrls());
    $smarty->assign('styleUrls', $this->getStyleUrls());
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
      "{$mosaicoExtUrl}/js/editor.js?r={$cacheCode}"
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

}
