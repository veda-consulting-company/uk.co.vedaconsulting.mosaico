<?php


function civicrm_api3_mosaico_gettemplateid($params) {
  
  $tableName = CRM_Mosaico_DAO_MessageTemplate::getTableName();
  $dao = CRM_Core_DAO::executeQuery("SELECT msg_tpl_id FROM {$tableName}");
  $results = array();
  while ($dao->fetch()) {
    $results[] = $dao->msg_tpl_id;
  }

  return civicrm_api3_create_success($results, $params);
}
