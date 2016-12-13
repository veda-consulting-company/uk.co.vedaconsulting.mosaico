<?php

/**
 * MosaicoTemplate.create API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRMDOC/API+Architecture+Standards
 */
function _civicrm_api3_mosaico_template_create_spec(&$spec) {
  // $spec['some_parameter']['api.required'] = 1;
}

/**
 * MosaicoTemplate.create API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_mosaico_template_create($params) {
  return _civicrm_api3_basic_create('CRM_Mosaico_BAO_MosaicoTemplate', $params);
}

/**
 * MosaicoTemplate.delete API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_mosaico_template_delete($params) {
  return _civicrm_api3_basic_delete('CRM_Mosaico_BAO_MosaicoTemplate', $params);
}

/**
 * MosaicoTemplate.get API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_mosaico_template_get($params) {
  return _civicrm_api3_basic_get('CRM_Mosaico_BAO_MosaicoTemplate', $params);
}
