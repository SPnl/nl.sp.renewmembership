<?php

require_once 'renewmembership.civix.php';

/**
 * Implementation of hook_civicrm_navigationMenu
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_navigationMenu
 */
function renewmembership_civicrm_navigationMenu( &$params ) {
  foreach ($params as &$menu) {
    if (array_key_exists('attributes', $menu) && $menu['attributes']['name'] == 'Memberships') {
      $menu['child'][$maxKey+1] = array (
        'attributes' => array (
          "label"=> ts('Verniew lidmaatschappen'),
          "name"=> ts('Vernieuw lidmaatschappen'),
          "url"=> "civicrm/member/renew",
          "permission" => "edit memberships",
          "parentID" => $menu['attributes']['navID'],
          "active" => 1,
        ),
        'child' => array(),
      );
    }
  }
}

/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function renewmembership_civicrm_config(&$config) {
  _renewmembership_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function renewmembership_civicrm_xmlMenu(&$files) {
  _renewmembership_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function renewmembership_civicrm_install() {
  return _renewmembership_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function renewmembership_civicrm_uninstall() {
  return _renewmembership_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function renewmembership_civicrm_enable() {
  return _renewmembership_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function renewmembership_civicrm_disable() {
  return _renewmembership_civix_civicrm_disable();
}

/**
 * Implementation of hook_civicrm_upgrade
 *
 * @param $op string, the type of operation being performed; 'check' or 'enqueue'
 * @param $queue CRM_Queue_Queue, (for 'enqueue') the modifiable list of pending up upgrade tasks
 *
 * @return mixed  based on op. for 'check', returns array(boolean) (TRUE if upgrades are pending)
 *                for 'enqueue', returns void
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function renewmembership_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _renewmembership_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function renewmembership_civicrm_managed(&$entities) {
  return _renewmembership_civix_civicrm_managed($entities);
}

/**
 * Implementation of hook_civicrm_caseTypes
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function renewmembership_civicrm_caseTypes(&$caseTypes) {
  _renewmembership_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function renewmembership_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _renewmembership_civix_civicrm_alterSettingsFolders($metaDataFolders);
}
