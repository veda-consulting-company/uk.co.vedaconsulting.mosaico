<?php

class CRM_Mosaico_Utils {

  static function getTemplate() {
    $args = explode('/', $_GET['q']);
    if ($args[0] == 'civicrm' && $args[1] == 'mosaico' && $args[2] == 'templates') {
      array_shift($args);
      array_shift($args);

      $config = CRM_Core_Config::singleton();
      $file   = $config->extensionsURL . 'uk.co.vedaconsulting.mosaico/packages/mosaico/' . implode('/', $args);
      $content = file_get_contents($file);
      echo $content;
    }
    CRM_Utils_System::civiExit();
  }
}
