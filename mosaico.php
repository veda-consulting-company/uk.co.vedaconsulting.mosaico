<?php

require_once 'mosaico.civix.php';
define('MOSAICO_TABLE_NAME', 'civicrm_mosaico_msg_template');
/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function mosaico_civicrm_config(&$config) {
  _mosaico_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function mosaico_civicrm_xmlMenu(&$files) {
  _mosaico_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function mosaico_civicrm_install() {
  _mosaico_civix_civicrm_install();
  
  $schema = new CRM_Logging_Schema();
  $schema->fixSchemaDifferences();

  $civiConfig = CRM_Core_Config::singleton();
  if ($civiConfig->imageUploadDir) {
    $prefix = rtrim($civiConfig->imageUploadDir, DIRECTORY_SEPARATOR);
    foreach (array("$prefix/static", "$prefix/uploads", "$prefix/uploads/thumbnails") as $staticDir) {
      if(!file_exists($staticDir)) {
        mkdir($staticDir, 0755);
      }
    }
  }
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function mosaico_civicrm_uninstall() {
  _mosaico_civix_civicrm_uninstall();

  $schema = new CRM_Logging_Schema();
  $schema->fixSchemaDifferences();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function mosaico_civicrm_enable() {
  _mosaico_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function mosaico_civicrm_disable() {
  _mosaico_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed
 *   Based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function mosaico_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _mosaico_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function mosaico_civicrm_managed(&$entities) {
  _mosaico_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function mosaico_civicrm_caseTypes(&$caseTypes) {
  _mosaico_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function mosaico_civicrm_angularModules(&$angularModules) {
_mosaico_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function mosaico_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _mosaico_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

/**
 * Functions below this ship commented out. Uncomment as required.
 *

/**
 * Implements hook_civicrm_preProcess().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_preProcess
 *
function mosaico_civicrm_preProcess($formName, &$form) {

}

*/

function mosaico_civicrm_navigationMenu(&$params){
  $parentId = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Navigation', 'Mailings', 'id', 'name');
  //$msgTpls  = CRM_Core_DAO::getFieldValue('CRM_Core_DAO_Navigation', 'Message Templates', 'id', 'name');

  $maxId       = max(array_keys($params));
  $msgTplMaxId = empty($msgTpls) ? $maxId+1 : $msgTpls;
  $params[$parentId]['child'][$msgTplMaxId] = array(
    'attributes' => array(
      'label'     => ts('Message Template Builder'),
      'name'      => 'Message_Template_Builder',
      'url'       => CRM_Utils_System::url('civicrm/mosaico/index', 'reset=1', TRUE),
      'active'    => 1,
      'parentID'  => $parentId,
      'operator'  => NULL,
      'navID'     => $msgTplMaxId,
      'permission'=> 'administer CiviCRM',
    ),
  );
}


function mosaico_civicrm_pageRun(&$page){
  $pageName = $page->getVar('_name');
  if ($pageName == 'Civi\Angular\Page\Main') {
    CRM_Core_Resources::singleton()->addScriptFile('uk.co.vedaconsulting.mosaico', 'js/crmMailingCustom.js', 800);
  }
  if ($pageName == 'CRM_Admin_Page_MessageTemplates') {
    $activeTab  = CRM_Utils_Request::retrieve('activeTab', 'String', $form, false, null, 'REQUEST');
    $resultArray= $exsitingTemplates = array();
    $smarty     = CRM_Core_Smarty::singleton();
    $tableName  = MOSAICO_TABLE_NAME;
    $dao = CRM_Core_DAO::executeQuery("SELECT mosaico.*, cmt.msg_title, cmt.msg_subject, cmt.is_active 
      FROM {$tableName} mosaico 
      JOIN civicrm_msg_template cmt ON (cmt.id = mosaico.msg_tpl_id)
    ");
    while ($dao->fetch()) {
      $resultArray[$dao->id] = $dao->toArray();
      
      $editURL= CRM_Utils_System::url('civicrm/mosaico/editor', 'snippet=2', FALSE, $dao->hash_key);
      $delURL = CRM_Utils_System::url('civicrm/admin/messageTemplates', 'action=delete&id='.$dao->msg_tpl_id);
      $enableDisableText = $dao->is_active ? 'Disable' : 'Enable';
      $action = sprintf('<span>
      <a href="%s" class="action-item crm-hover-button" title="Edit this message template" >Edit</a>
      <a href="#" class="action-item crm-hover-button crm-enable-disable" title="Disable this message template" >%s</a>
      <a href="%s" class="action-item crm-hover-button small-popup" title="Delete this message template" >Delete</a>
      <a href="#" class="action-item crm-hover-button copy-template" value = "%s" title="Copy this message template">Copy</a>
      </span>', $editURL, $enableDisableText, $delURL, $dao->msg_tpl_id);
      
      $resultArray[$dao->id]['action'] = $action;
	  
      //all tempalte values which are exist in Mosaico tempalte and related hash key
      $exsitingTemplates[$dao->msg_tpl_id] = $dao->hash_key; 
    }
    //To inject new action link with default actions.
    //row already assinged in smarty, so get template variable , and append action and assigned new row values to tpl.
    $rows = $smarty->get_template_vars('rows');
    foreach ($rows['userTemplates'] as $key => $value) {
      $editURL = '#';
      $editableClassName = "edit_msg_tpl_to_mosaico";
      $editableLinkName = "Import in Mosaico";
	  
      //url for existing mosaico templates. otherwise we create dummy with new hashkey and link to open up in mosaico editor.
      //and using classname to allow edit only if not exist in mosaico template.
      if (array_key_exists($key, $exsitingTemplates)) {
        $editURL    = CRM_Utils_System::url('civicrm/mosaico/editor', 'snippet=2', FALSE, $exsitingTemplates[$key]);
        $editableClassName = NULL;
        $editableLinkName = "Edit In Mosaico";
      }

      $action = sprintf('<span>
        <a href="%s" class="action-item crm-hover-button %s"  value="xx" title="Open in Mosaico" >%s</a>
      </span>', $editURL, $editableClassName, $editableLinkName);

      if (defined('CIVICRM_MOSAICO_IMPORT') && CIVICRM_MOSAICO_IMPORT == 1) {
        //MV: allow open msg template in mosaico editor. 
        $rows['userTemplates'][$key]['action'] .= $action;
      }
    }

    $smarty->assign('rows', $rows);    
    $smarty->assign('mosaicoTemplates', $resultArray);
    $smarty->assign('selectedChild', $activeTab);
    // From civi 4.7, no more tinymce, so if only civi version is less than 4.7 show tinymce.
    //https://civicrm.org/blog/colemanw/big-changes-to-wysiwyg-editing-in-47
    $showTinymceTpl = FALSE;
    $currentVer = CRM_Core_BAO_Domain::version(TRUE);
    if (version_compare($currentVer, '4.7') < 0) {
      $showTinymceTpl = TRUE;
    }
    $smarty->assign('showTinymceTpl', $showTinymceTpl);
  }
}

function mosaico_civicrm_check(&$messages) {
  //Make sure the ImageMagick library is loaded.
  if( !(extension_loaded('imagick') || class_exists("Imagick"))){
    $messages[] = new CRM_Utils_Check_Message(
      'mosaico_imagick',
      ts('the ImageMagick library is not installed.  The Email Template Builder extension will not work without it.'),
      ts('ImageMagick not installed'),
      \Psr\Log\LogLevel::CRITICAL
    );
  }
}
