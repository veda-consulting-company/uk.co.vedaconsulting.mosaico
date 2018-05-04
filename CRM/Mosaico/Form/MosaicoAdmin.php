<?php

use CRM_Mosaico_ExtensionUtil as E;

/**
 * Form controller class
 *
 * @see https://wiki.civicrm.org/confluence/display/CRMDOC/QuickForm+Reference
 */
class CRM_Mosaico_Form_MosaicoAdmin extends CRM_Admin_Form_Setting {

  protected $_settings = array(
    'mosaico_layout' => 'Mosaico Preferences',
    'mosaico_custom_templates_dir' => 'Mosaico Custom Templates Directory',
    'mosaico_custom_templates_url' => 'Mosaico Custom Templates URL',
    'mosaico_plugins' => 'Mosaico Plugin List',
    'mosaico_toolbar' => 'Mosaico Toolbar'
  );

  /**
   * Build the form object.
   */
  public function buildQuickForm() {
    parent::buildQuickForm();
  }

  /**
   * Function to process the form
   *
   * @access public
   * @return None
   */
  public function postProcess() {
    $params = $this->controller->exportValues($this->_name);
    // if plugin and toolbar field are empty then reset to default value
    if (empty($params['mosaico_plugins'])) {
      $params['mosaico_plugins'] = CIVICRM_MOSAICO_PLUGINS;
    }
    if (empty($params['mosaico_toolbar'])) {
      $params['mosaico_toolbar'] = CIVICRM_MOSAICO_TOOLBAR;
    }
    parent::commonProcess($params);
  }

}
