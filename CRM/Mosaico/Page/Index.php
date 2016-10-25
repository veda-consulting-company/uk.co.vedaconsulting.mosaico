<?php

class CRM_Mosaico_Page_Index extends CRM_Core_Page {
  const DEFAULT_MODULE_WEIGHT = 200;

  function run() {

    $this->registerResources(CRM_Core_Resources::singleton());
    $mConfig = CRM_Mosaico_Utils::getConfig();
    $messages = array();

    // check if imagemagick php extension is installed correctly.
    if (!(extension_loaded('imagick') || class_exists("Imagick"))) {
      $messages[] = new CRM_Utils_Check_Message('mosaico_imagick', ts('Email Template Builder extension will not work. Extension requires ImageMagick to be installed as php module. Please double check.'), ts('ImageMagick not installed'));
    }

    // check if fileinfo php extension loaded
    if (!extension_loaded('fileinfo')) {
      $messages[] = new CRM_Utils_Check_Message('mosaico_fileinfo', ts('May experience mosaico template or thumbnail loading issues (404 errors).'), ts('PHP extension Fileinfo not loaded or enabled'));
    }

    // check if base url for image uploads in CiviCRM is set and works
    if (!empty($mConfig['BASE_URL'])) {
      // detect incorrect image upload url. (Note: Since v4.4.4, CRM_Utils_Check_Security has installed index.html placeholder.)
      $handle = curl_init($mConfig['BASE_URL'] . '/index.html');
      curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
      $response = curl_exec($handle);
      $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
      if ($httpCode == 404) {
        $messages[] = new CRM_Utils_Check_Message('mosaico_base_url', ts('BASE_URL seems incorrect - %1. Images when uploaded, may not appear correctly as thumbnails. Make sure "Image Upload URL" is configured correctly with Administer » System Settings » Resouce URLs.', array(1 => $mConfig['BASE_URL'])), ts('Incorrect image upload url'));
      }
    }

    // Check if UPLOADS directory exists and create it if it doesn't
    if (!is_dir($mConfig['BASE_DIR'] . $mConfig['UPLOADS_DIR'])) {
      if (!mkdir($mConfig['BASE_DIR'] . $mConfig['UPLOADS_DIR'], 0775, TRUE)) {
        $messages[] = new CRM_Utils_Check_Message('mosaico_uploads_dir', ts('%1 not writable or configured.', array(1 => $mConfig['BASE_DIR'] . $mConfig['UPLOADS_DIR'])), ts('UPLOADS_DIR not writable or configured'));
      }
    } elseif (!is_writable($mConfig['BASE_DIR'] . $mConfig['UPLOADS_DIR'])) {
      $messages[] = new CRM_Utils_Check_Message('mosaico_uploads_dir', ts('%1 not writable or configured.', array(1 => $mConfig['BASE_DIR'] . $mConfig['UPLOADS_DIR'])), ts('UPLOADS_DIR not writable or configured'));
    }

    // Check if uploads/STATIC directory exists and create it if it doesn't
    if (!is_dir($mConfig['BASE_DIR'] . $mConfig['STATIC_DIR'])) {
      if (!mkdir($mConfig['BASE_DIR'] . $mConfig['STATIC_DIR'], 0775, TRUE)) {
        $messages[] = new CRM_Utils_Check_Message('mosaico_static_dir', ts('%1 not writable or configured.', array(1 => $mConfig['BASE_DIR'] . $mConfig['STATIC_DIR'])), ts('STATIC_DIR not writable or configured'));
      }
    } elseif (!is_writable($mConfig['BASE_DIR'] . $mConfig['STATIC_DIR'])) {
      $messages[] = new CRM_Utils_Check_Message('mosaico_static_dir', ts('%1 not writable or configured.', array(1 => $mConfig['BASE_DIR'] . $mConfig['STATIC_DIR'])), ts('STATIC_DIR not writable or configured'));
    }

    // Check if uploads/THUMBNAILS directory exists and create it if it doesn't
    if (!is_dir($mConfig['BASE_DIR'] . $mConfig['THUMBNAILS_DIR'])) {
      if (!mkdir($mConfig['BASE_DIR'] . $mConfig['THUMBNAILS_DIR'], 0775, TRUE)) {
        $messages[] = new CRM_Utils_Check_Message('mosaico_thumbnails_dir', ts('%1 not writable or configured.', array(1 => $mConfig['BASE_DIR'] . $mConfig['THUMBNAILS_DIR'])), ts('THUMBNAILS_DIR not writable or configured'));
      }
    } elseif (!is_writable($mConfig['BASE_DIR'] . $mConfig['THUMBNAILS_DIR'])) {
      $messages[] = new CRM_Utils_Check_Message('mosaico_thumbnails_dir', ts('%1 not writable or configured.', array(1 => $mConfig['BASE_DIR'] . $mConfig['THUMBNAILS_DIR'])), ts('THUMBNAILS_DIR not writable or configured'));
    }

    // check if Mosaico extension is in the correctly named extension directory
    $extDirName = basename(dirname(dirname(dirname(dirname(__FILE__)))));
    if ($extDirName != 'uk.co.vedaconsulting.mosaico') {
      $messages[] = new CRM_Utils_Check_Message('mosaico_extdirname', ts("We expect extension directory name to be '%1' instead of '%2'. Images and icons may not load correctly.", array(
        1 => 'uk.co.vedaconsulting.mosaico',
        2 => $extDirName
      )), ts('Installed extension directory name not suitable'));
    }

    foreach ($messages as $message) {
      CRM_Core_Session::setStatus($message->getMessage(), $message->getTitle(), 'error');
    }

    $this->assign('extResUrl', CRM_Core_Resources::singleton()
      ->getUrl('uk.co.vedaconsulting.mosaico'));

    return parent::run();
  }

  /**
   * @param CRM_Core_Resources $res
   */
  public function registerResources(CRM_Core_Resources $res) {
    $weight = self::DEFAULT_MODULE_WEIGHT;

    $res->addSettingsFactory(function () {
      // in order to use ext resource url in JS - e.g CRM.resourceUrls
      $jsvar = array(
        'resourceUrls' => CRM_Extension_System::singleton()
          ->getMapper()
          ->getActiveModuleUrls(),
      );
      return $jsvar;
    });

    $res->addStyleFile('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist/mosaico-material.min.css', $weight++, 'html-header');
    $res->addStyleFile('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist/vendor/notoregular/stylesheet.css', $weight++, 'html-header');

    $res->addScriptFile('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist/vendor/knockout.js', $weight++, 'html-header', TRUE);
    $res->addScriptFile('uk.co.vedaconsulting.mosaico', 'js/index.js', $weight++, 'html-header', FALSE);

    $res->addStyleFile('uk.co.vedaconsulting.mosaico', 'css/index.css', $weight++, 'html-header');
    $res->addScriptFile('uk.co.vedaconsulting.mosaico', 'js/index2.js', $weight++, 'html-header');
  }
}
