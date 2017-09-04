<?php

class CRM_Mosaico_BAO_MosaicoTemplate extends CRM_Mosaico_DAO_MosaicoTemplate {

  /**
   * Create a new MosaicoTemplate based on array-data
   *
   * @param array $params key-value pairs
   * @return CRM_Mosaico_DAO_MosaicoTemplate|NULL
   *
  public static function create($params) {
    $className = 'CRM_Mosaico_DAO_MosaicoTemplate';
    $entityName = 'MosaicoTemplate';
    $hook = empty($params['id']) ? 'create' : 'edit';

    CRM_Utils_Hook::pre($hook, $entityName, CRM_Utils_Array::value('id', $params), $params);
    $instance = new $className();
    $instance->copyValues($params);
    $instance->save();
    CRM_Utils_Hook::post($hook, $entityName, $instance->id, $instance);

    return $instance;
  } */

  /**
   * @return mixed
   */
  public static function findBaseTemplates() {
    if (!isset(Civi::$statics[__CLASS__]['bases'])) {
      $templatesDir = CRM_Core_Resources::singleton()->getPath('uk.co.vedaconsulting.mosaico');
      if (!$templatesDir) {
        return FALSE;
      }
      $templatesDir .= '/packages/mosaico/templates';
      if (!is_dir($templatesDir)) {
        return FALSE;
      }

      $templatesUrl = CRM_Mosaico_Utils::getTemplatesUrl('absolute');

      $records = array();

      foreach (glob("{$templatesDir}/*", GLOB_ONLYDIR) as $dir) {
        $template = basename($dir);
        $templateHTML = "{$templatesUrl}/{$template}/template-{$template}.html";
        $templateThumbnail = "{$templatesUrl}/{$template}/edres/_full.png";

        $records[] = array(
          'name' => $template,
          'title' => $template,
          'thumbnail' => $templateThumbnail,
          'path' => $templateHTML,
        );
      }
      Civi::$statics[__CLASS__]['bases'] = $records;
    }

    return Civi::$statics[__CLASS__]['bases'];
  }

}
