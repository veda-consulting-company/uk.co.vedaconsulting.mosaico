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
      $records = array();
      $records[] = array(
        'name' => 'versafix-1',
        'title' => 'Versafix 1',
        'thumbnail' => CRM_Mosaico_Utils::getTemplatesUrl('absolute', 'versafix-1/edres/_full.png'),
        'path' => 'templates/versafix-1/template-versafix-1.html',
      );
      $records[] = array(
        'name' => 'tedc15',
        'title' => 'TEDC 15',
        'thumbnail' => CRM_Mosaico_Utils::getTemplatesUrl('absolute', 'tedc15/edres/_full.png'),
        'path' => 'templates/tedc15/template-tedc15.html',
      );
      Civi::$statics[__CLASS__]['bases'] = $records;
    }
    return Civi::$statics[__CLASS__]['bases'];
  }

}
