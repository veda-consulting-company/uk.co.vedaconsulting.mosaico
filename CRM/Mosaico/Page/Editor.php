<?php

class CRM_Mosaico_Page_Editor extends CRM_Core_Page {
  const DEFAULT_MODULE_WEIGHT = 200;

  function run() {
    $this->registerResources(CRM_Core_Resources::singleton());
    return parent::run();
  }

  /**
   * @param CRM_Core_Resources $res
   */
  public function registerResources(CRM_Core_Resources $res) {
    $weight = self::DEFAULT_MODULE_WEIGHT;
    $distUrl = CRM_Mosaico_Utils::getMosaicoDistUrl('relative');

    $res->addScriptUrl("$distUrl/vendor/knockout.js", $weight++, 'html-header', FALSE);
    $res->addScriptUrl("$distUrl/vendor/jquery.min.js", $weight++, 'html-header', FALSE);
    $res->addScriptUrl("$distUrl/vendor/jquery-ui.min.js", $weight++, 'html-header', FALSE);
    $res->addScriptUrl("$distUrl/vendor/jquery.ui.touch-punch.min.js", $weight++, 'html-header', FALSE);
    $res->addScriptUrl("$distUrl/vendor/load-image.all.min.js", $weight++, 'html-header', FALSE);
    $res->addScriptUrl("$distUrl/vendor/canvas-to-blob.min.js", $weight++, 'html-header', FALSE);
    $res->addScriptUrl("$distUrl/vendor/jquery.iframe-transport.js", $weight++, 'html-header', FALSE);
    $res->addScriptUrl("$distUrl/vendor/jquery.fileupload.js", $weight++, 'html-header', FALSE);
    $res->addScriptUrl("$distUrl/vendor/jquery.fileupload-process.js", $weight++, 'html-header', FALSE);
    $res->addScriptUrl("$distUrl/vendor/jquery.fileupload-image.js", $weight++, 'html-header', FALSE);
    $res->addScriptUrl("$distUrl/vendor/jquery.fileupload-validate.js", $weight++, 'html-header', FALSE);
    $res->addScriptUrl("$distUrl/vendor/knockout-jqueryui.min.js", $weight++, 'html-header', FALSE);
    $res->addScriptUrl("$distUrl/vendor/tinymce.min.js", $weight++, 'html-header', FALSE);
    $res->addScriptUrl("$distUrl/mosaico.min.js?v=0.15", $weight++, 'html-header', FALSE);

    $res->addScriptFile("uk.co.vedaconsulting.mosaico", "js/editor.js", $weight++, 'html-header', FALSE);
    
    $res->addStyleUrl("$distUrl/mosaico-material.min.css?v=0.10", $weight++, 'html-header', TRUE);
    $res->addStyleUrl("$distUrl/vendor/notoregular/stylesheet.css", $weight++, 'html-header', TRUE);
  }
}
