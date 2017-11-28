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
  );

  /**
   * Build the form object.
   */
  public function buildQuickForm() {
    parent::buildQuickForm();
  }

}
