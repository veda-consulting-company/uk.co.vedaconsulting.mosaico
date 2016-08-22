<?php

class CRM_Mosaico_Page_Index extends CRM_Core_Page {
  const DEFAULT_MODULE_WEIGHT = 200;

  function run() {
    $config = CRM_Core_Config::singleton();
    $this->registerResources(CRM_Core_Resources::singleton());

    $messages = array();
    $syscheck = CRM_Utils_Request::retrieve('runcheck', 'Boolean', CRM_Core_DAO::$_nullObject);
    $tplCount = CRM_Core_DAO::singleValueQuery("SELECT count(id) FROM civicrm_mosaico_msg_template");
    if ($syscheck || empty($tplCount)) {
      if (!(extension_loaded('imagick') || class_exists("Imagick"))) {
        $messages[] = new CRM_Utils_Check_Message(
          'mosaico_imagick',
          ts('Email Template Builder extension will not work. Extension requires ImageMagick to be installed as php module. Please double check.'),
          ts('ImageMagick not installed')
        );
      }
      if (!extension_loaded('fileinfo')) {
        $messages[] = new CRM_Utils_Check_Message(
          'mosaico_fileinfo',
          ts('May experience mosaico template or thumbnail loading issues (404 errors).'),
          ts('PHP extension Fileinfo not loaded or enabled')
        );
      }
      if (!is_writable($config->imageUploadDir)) {
        $messages[] = new CRM_Utils_Check_Message(
          'mosaico_uploaddir',
          ts('%1 dir not writable or configured.', array(1 => $config->imageUploadDir)),
          ts('Upload dir not writable or configured')
        );
      }
      $staticDir = rtrim($config->imageUploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . 'static'; 
      if (!is_writable($staticDir)) {
        $messages[] = new CRM_Utils_Check_Message(
          'mosaico_staticdir',
          ts('%1 dir not writable or configured.', array(1 => $staticDir)),
          ts('Static dir not writable or configured')
        );
      }
      if ($config->imageUploadURL) {
        // detect incorrect image upload url. (Note: Since v4.4.4, CRM_Utils_Check_Security has installed index.html placeholder.)
        $handle = curl_init($config->imageUploadURL . '/index.html');
        curl_setopt($handle,  CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($handle);
        $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
        if($httpCode == 404) {
          $messages[] = new CRM_Utils_Check_Message(
            'mosaico_uploadurl',
            ts('Image upload url seems incorrect - %1. Images when uploaded, may not appear correctly as thumbnails. Make sure "Image Upload URL" is configured correctly with Administer » System Settings » Resouce URLs.', array(1 => $config->imageUploadURL)),
            ts('Incorrect image upload url')
          );
        }
      }
      $extDirName = basename(dirname(dirname(dirname(dirname(__FILE__)))));
      if ($extDirName != 'uk.co.vedaconsulting.mosaico') {
        $messages[] = new CRM_Utils_Check_Message(
          'mosaico_extdirname',
          ts("We expect extension dir name to be '%1' instead of '%2'. Images and icons may not load correctly.", array(1 => 'uk.co.vedaconsulting.mosaico', 2 => $extDirName)),
          ts('Installed extension dir name not suitable')
        );
      }
    }
    foreach ($messages as $message) {
      CRM_Core_Session::setStatus($message->getMessage(), $message->getTitle(), 'error');
    }

    $this->assign('extResUrl', CRM_Core_Resources::singleton()->getUrl('uk.co.vedaconsulting.mosaico'));
    return parent::run();
  }

  /**
   * @param CRM_Core_Resources $res
   */
  public function registerResources(CRM_Core_Resources $res) {
    $weight = self::DEFAULT_MODULE_WEIGHT;

    $res->addSettingsFactory(function () {
      // inorder to use ext resource url in JS - e.g CRM.resourceUrls
      $jsvar = array(
        'resourceUrls' => CRM_Extension_System::singleton()->getMapper()->getActiveModuleUrls(),
      );
      return $jsvar;
    });
    
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
