<?php

require_once 'tokens.civix.php';

function tokens_civicrm_buildForm($formName, &$form) {
    //mae sure we have a case ID available in the token.
    //the case ID is set in the form and the class CRM_Tokens_CaseId will
    //store this for further use
    CRM_Tokens_CaseId::buildForm($formName, $form);
}

function tokens_civicrm_tokens(&$tokens) {
  
  // current case tokens
  CRM_Tokens_ClientCase::tokens($tokens, 'client', 'Client');
  CRM_Tokens_CaseRelationship::tokens($tokens, 'rct', 'RCT');
  CRM_Tokens_CaseRelationship::tokens($tokens, 'representative', 'Representative');
  CRM_Tokens_CaseRelationship::tokens($tokens, 'expert', 'Expert');
  CRM_Tokens_CaseRelationship::tokens($tokens, 'authorised_contact', 'Authorised contact');
  CRM_Tokens_CaseRelationship::tokens($tokens, 'business_participant', 'Business participant');
  CRM_Tokens_CaseRelationship::tokens($tokens, 'grant_coordinator', 'Grant Coordinator');
  CRM_Tokens_CaseRelationship::tokens($tokens, 'business_coordinator', 'Business Coordinator');

  CRM_Tokens_CountryCoordinator::tokens($tokens, 'cc', 'Country Coordinator');
  CRM_Tokens_CountryCoordinator::tokens($tokens, 'proj_off', 'Project officer');
  CRM_Tokens_SectorCoordinator::tokens($tokens, 'sc', 'Sector Coordinator');
  
  $main_info = new CRM_Tokens_MainActivityInfo('mainactivity_info', 'Main Activity information');
  $main_info->tokens($tokens);
  
  $info_dsa = new CRM_Tokens_InfoDsa('info_dsa', 'Info for DSA');
  $info_dsa->tokens($tokens);
  
  $case_num = new CRM_Tokens_CaseNumber('case_number', 'PUM Case number');
  $case_num->tokens($tokens);
  
  //Case donation info
  $case_donation = new CRM_Tokens_CaseDonation('case_donation', 'Case donation for FA');
  $case_donation->tokens($tokens);
  
  // parent case tokens
  CRM_Tokens_ClientCase::tokens($tokens, 'parent_client', 'Parent client');
  CRM_Tokens_CaseRelationship::tokens($tokens, 'parent_representative', 'Parent Representative');
  CRM_Tokens_CaseRelationship::tokens($tokens, 'parent_expert', 'Parent Expert');
  CRM_Tokens_CaseRelationship::tokens($tokens, 'parent_authorised_contact', 'Parent Authorised contact');
  CRM_Tokens_CaseRelationship::tokens($tokens, 'parent_business_participant', 'Parent Business participant');
  CRM_Tokens_CaseRelationship::tokens($tokens, 'parent_business_coordinator', 'Parent Business Coordinator');
  CRM_Tokens_CountryCoordinator::tokens($tokens, 'parent_cc', 'Parent Country Coordinator');
  CRM_Tokens_CountryCoordinator::tokens($tokens, 'parent_proj_off', 'Parent Project officer');
  CRM_Tokens_SectorCoordinator::tokens($tokens, 'parent_sc', 'Parent Sector Coordinator');
  
  $parent_main_info = new CRM_Tokens_MainActivityInfo('parent_mainactivity_info', 'Parent Main Activity information', 'parent');
  $parent_main_info->tokens($tokens);
    
  $parent_info_dsa = new CRM_Tokens_InfoDsa('parent_info_dsa', 'Parent Info for DSA', 'parent');
  $parent_info_dsa->tokens($tokens);

  $parent_case_num = new CRM_Tokens_CaseNumber('parent_case_number', 'Parent PUM Case number', 'parent');
  $parent_case_num->tokens($tokens);
  
  // misc
  $info_tokens = new CRM_Tokens_SysInfo('server', 'Server');
  $info_tokens->tokens($tokens);

  //activity source tokens
  $activity_source_tokens = new CRM_Tokens_ActivitySourceContactTokens();
  $activity_source_tokens->tokens($tokens);
}

function tokens_civicrm_tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null) {
  /* Set case ID. This done by retrieving the activity ID from the $values array.
   * The activity_id is present ins the $values array when the tokens are rendered from
   * a scheduled reminder.
   */
  CRM_Tokens_CaseId::getCaseIdFromTokenValues($values, $cids);


  // current case tokens
  
  $client_tokens = new CRM_Tokens_ClientCase('client', 'Client', $values);
  $client_tokens->tokenValues($values, $cids, $job, $tokens, $context);

  $rct_tokens = new CRM_Tokens_CaseRelationship('Recruitment Team Member', 'rct', 'RCT', $values);
  $rct_tokens->tokenValues($values, $cids, $job, $tokens, $context);

  $rep_tokens = new CRM_Tokens_CaseRelationship('Representative is', 'representative', 'Representative', $values);
  $rep_tokens->tokenValues($values, $cids, $job, $tokens, $context);

  $expert_tokens = new CRM_Tokens_CaseRelationship('Expert', 'expert', 'Expert', $values);
  $expert_tokens->tokenValues($values, $cids, $job, $tokens, $context);

  $authorised_contact_tokens = new CRM_Tokens_CaseRelationship('Has authorised', 'authorised_contact', 'Authorised contact',  $values);
  $authorised_contact_tokens->tokenValues($values, $cids, $job, $tokens, $context);

  $cc = new CRM_Tokens_CountryCoordinator('Country Coordinator is', 'cc', 'Country Coordinator', $values);
  $cc->tokenValues($values, $cids, $job, $tokens, $context);

  $proj_off = new CRM_Tokens_CountryCoordinator('Project Officer for', 'proj_off', 'Project officer', $values);
  $proj_off->tokenValues($values, $cids, $job, $tokens, $context);

  $sc = new CRM_Tokens_SectorCoordinator('Sector Coordinator', 'sc', 'Sector Coordinator', $values);
  $sc->tokenValues($values, $cids, $job, $tokens, $context);

  $main_info = new CRM_Tokens_MainActivityInfo('mainactivity_info', 'Main Activity information');
  $main_info->tokenValues($values, $cids, $job, $tokens, $context);
  
  $info_dsa = new CRM_Tokens_InfoDsa('info_dsa', 'Info for DSA');
  $info_dsa->tokenValues($values, $cids, $job, $tokens, $context);
  
  $case_num = new CRM_Tokens_CaseNumber('case_number', 'PUM Case number');
  $case_num->tokenValues($values, $cids, $job, $tokens, $context);
  
  //Case donation info
  $case_donation = new CRM_Tokens_CaseDonation('case_donation', 'Case donation for FA');
  $case_donation->tokenValues($values, $cids, $job, $tokens, $context);
  
  $bus_part_tokens = new CRM_Tokens_CaseRelationship('Business participant is', 'business_participant', 'Business participant', $values);
  $bus_part_tokens->tokenValues($values, $cids, $job, $tokens, $context);
  
  // parent case tokens
  
  $parent_client_tokens = new CRM_Tokens_ClientCase('parent_client', 'Parent Client', $values, 'parent');
  $parent_client_tokens->tokenValues($values, $cids, $job, $tokens, $context);
  
  $parent_rep_tokens = new CRM_Tokens_CaseRelationship('Representative is', 'parent_representative', 'Parent Representative', $values, 'parent');
  $parent_rep_tokens->tokenValues($values, $cids, $job, $tokens, $context);
  
  $parent_expert_tokens = new CRM_Tokens_CaseRelationship('Expert', 'parent_expert', 'Parent Expert', $values, 'parent');
  $parent_expert_tokens->tokenValues($values, $cids, $job, $tokens, $context);

  $parent_authorised_contact_tokens = new CRM_Tokens_CaseRelationship('Has authorised', 'parent_authorised_contact', 'Parent Authorised contact', $values, 'parent');
  $parent_authorised_contact_tokens->tokenValues($values, $cids, $job, $tokens, $context);
  
  $parent_cc = new CRM_Tokens_CountryCoordinator('Country Coordinator is', 'parent_cc', 'Parent Country Coordinator', $values, 'parent');
  $parent_cc->tokenValues($values, $cids, $job, $tokens, $context);

  $parent_proj_off = new CRM_Tokens_CountryCoordinator('Project Officer for', 'parent_proj_off', 'Parent Project officer', $values, 'parent');
  $parent_proj_off->tokenValues($values, $cids, $job, $tokens, $context);

  $parent_sc = new CRM_Tokens_SectorCoordinator('Sector Coordinator', 'parent_sc', 'Parent Sector Coordinator', $values, 'parent');
  $parent_sc->tokenValues($values, $cids, $job, $tokens, $context);
  
  $parent_main_info = new CRM_Tokens_MainActivityInfo('parent_mainactivity_info', 'Parent Main Activity information', 'parent');
  $parent_main_info->tokenValues($values, $cids, $job, $tokens, $context);
  
  $parent_info_dsa = new CRM_Tokens_InfoDsa('parent_info_dsa', 'Parent info for DSA', 'parent');
  $parent_info_dsa->tokenValues($values, $cids, $job, $tokens, $context);
  
  $parent_case_num = new CRM_Tokens_CaseNumber('parent_case_number', 'Parent PUM Case number', 'parent');
  $parent_case_num->tokenValues($values, $cids, $job, $tokens, $context);
  
  $parent_bus_part_tokens = new CRM_Tokens_CaseRelationship('Business participant is', 'parent_business_participant', 'Parent Business participant', $values, 'parent');
  $parent_bus_part_tokens->tokenValues($values, $cids, $job, $tokens, $context);

  $grant_coordinator_tokens = new CRM_Tokens_CaseRelationship('Grant Coordinator', 'grant_coordinator', 'Grant Coordinator', $values);
  $grant_coordinator_tokens->tokenValues($values, $cids, $job, $tokens, $context);
  
  $business_coordinator_tokens = new CRM_Tokens_CaseRelationship('Business Coordinator', 'business_coordinator', 'Business Coordinator', $values);
  $business_coordinator_tokens->tokenValues($values, $cids, $job, $tokens, $context);
  
  $parent_business_coordinator_tokens = new CRM_Tokens_CaseRelationship('Business Coordinator', 'parent_business_coordinator', 'Parent Business Coordinator', $values, 'parent');
  $parent_business_coordinator_tokens->tokenValues($values, $cids, $job, $tokens, $context);
    
  // misc
  $info_tokens = new CRM_Tokens_SysInfo('server', 'Server');
  $info_tokens->tokenValues($values, $cids, $job, $tokens, $context);

  //activity source tokens
  $activity_source_tokens = new CRM_Tokens_ActivitySourceContactTokens();
  $activity_source_tokens->tokenValues($values, $cids, $job, $tokens, $context);
}


/**
 * Implementation of hook_civicrm_config
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function tokens_civicrm_config(&$config) {
  _tokens_civix_civicrm_config($config);
}

/**
 * Implementation of hook_civicrm_xmlMenu
 *
 * @param $files array(string)
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function tokens_civicrm_xmlMenu(&$files) {
  _tokens_civix_civicrm_xmlMenu($files);
}

/**
 * Implementation of hook_civicrm_install
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function tokens_civicrm_install() {
  _tokens_civix_civicrm_install();
}

/**
 * Implementation of hook_civicrm_uninstall
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function tokens_civicrm_uninstall() {
  _tokens_civix_civicrm_uninstall();
}

/**
 * Implementation of hook_civicrm_enable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function tokens_civicrm_enable() {
  _tokens_civix_civicrm_enable();
}

/**
 * Implementation of hook_civicrm_disable
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function tokens_civicrm_disable() {
  _tokens_civix_civicrm_disable();
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
function tokens_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _tokens_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implementation of hook_civicrm_managed
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function tokens_civicrm_managed(&$entities) {
  _tokens_civix_civicrm_managed($entities);
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
function tokens_civicrm_caseTypes(&$caseTypes) {
  _tokens_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implementation of hook_civicrm_alterSettingsFolders
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function tokens_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _tokens_civix_civicrm_alterSettingsFolders($metaDataFolders);
}
