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
    $res->addScriptFile('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist/vendor/knockout.js', $weight++, 'html-header', FALSE);
    $res->addScriptFile('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist/vendor/jquery.min.js', $weight++, 'html-header', FALSE);
    $res->addScriptFile('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist/vendor/jquery-ui.min.js', $weight++, 'html-header', FALSE);
    $res->addScriptFile('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist/vendor/jquery.ui.touch-punch.min.js', $weight++, 'html-header', FALSE);
    $res->addScriptFile('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist/vendor/load-image.all.min.js', $weight++, 'html-header', FALSE);
    $res->addScriptFile('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist/vendor/canvas-to-blob.min.js', $weight++, 'html-header', FALSE);
    $res->addScriptFile('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist/vendor/jquery.iframe-transport.js', $weight++, 'html-header', FALSE);
    $res->addScriptFile('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist/vendor/jquery.fileupload.js', $weight++, 'html-header', FALSE);
    $res->addScriptFile('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist/vendor/jquery.fileupload-process.js', $weight++, 'html-header', FALSE);
    $res->addScriptFile('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist/vendor/jquery.fileupload-image.js', $weight++, 'html-header', FALSE);
    $res->addScriptFile('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist/vendor/jquery.fileupload-validate.js', $weight++, 'html-header', FALSE);
    $res->addScriptFile('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist/vendor/knockout-jqueryui.min.js', $weight++, 'html-header', FALSE);
    $res->addScriptFile('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist/vendor/tinymce.min.js', $weight++, 'html-header', FALSE);
    $res->addScriptFile('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist/mosaico.min.js?v=0.15', $weight++, 'html-header', FALSE);

    $res->addScriptFile('uk.co.vedaconsulting.mosaico', 'js/editor.js', $weight++, 'html-header', FALSE);
    
    $res->addStyleFile('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist/mosaico-material.min.css?v=0.10', $weight++, 'html-header', TRUE);
    $res->addStyleFile('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist/vendor/notoregular/stylesheet.css', $weight++, 'html-header', TRUE);
  }
}
