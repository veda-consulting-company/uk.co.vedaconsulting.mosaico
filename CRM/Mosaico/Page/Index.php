<?php

class CRM_Mosaico_Page_Index extends CRM_Core_Page {
  const DEFAULT_MODULE_WEIGHT = 200;

  function run() {
    $this->registerResources(CRM_Core_Resources::singleton());
    return parent::run();
  }

  /**
   * @param CRM_Core_Resources $res
   */
  public function registerResources(CRM_Core_Resources $res) {
    //CRM_Core_Error::debug('$res', $res);
    $weight = self::DEFAULT_MODULE_WEIGHT;
    
    $res->addStyleFile('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist/mosaico-material.min.css', $weight++, 'html-header', TRUE);
    $res->addStyleFile('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist/vendor/notoregular/stylesheet.css', $weight++, 'html-header', TRUE);

    $res->addScriptFile('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist/vendor/knockout.js', $weight++, 'html-header', TRUE);

    // civi already has jquery.min
    //$res->addScriptFile('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist/vendor/jquery.min.js', $weight++, 'html-header', TRUE);
    $res->addScriptFile('uk.co.vedaconsulting.mosaico', 'js/index.js', $weight++, 'html-header', FALSE);


    $res->addStyleFile('uk.co.vedaconsulting.mosaico', 'css/index.css', $weight++, 'html-header', TRUE);
    $res->addScriptFile('uk.co.vedaconsulting.mosaico', 'js/index2.js', $weight++, 'html-header', TRUE);
  }
}
