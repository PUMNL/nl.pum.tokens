<?php

class CRM_Tokens_ClientCase extends CRM_Tokens_CaseRelationship {
  
  protected $location_types = array();
  
  protected $phone_types = array();
  
  public function __construct($token_name, $token_label, $case_id = null) {
    $this->token_name = $token_name;
    $this->token_label = $token_label;

    if (empty($case_id)) {
      $this->case_id = CRM_Tokens_CaseId::getCaseId();
    } elseif($case_id=='parent') {
      $this->case_id = CRM_Tokens_CaseId::getParentCaseId();
    } else {
      $this->case_id = $case_id;
    }

    try {
      $this->contact_id = civicrm_api3('Case', 'getvalue', array(
        'return' => 'client_id',
        'id' => $this->case_id,
      ));
      if (is_array($this->contact_id)) {
        $this->contact_id = reset($this->contact_id);
      }
    } catch (Exception $e) {
      CRM_Core_Error::debug_log_message($e->getCode() & " - " & $e->getMessage(), FALSE);
    }
	
	$locTypes = civicrm_api3('LocationType', 'get', array());
    foreach($locTypes['values'] as $locType) {
      $this->location_types[$locType['name']] = $locType['id'];
    }
    
    $phoneTypes = civicrm_api3('OptionValue', 'get', array(
	  'version' => 3,
	  'sequential' => 1,
	  'option_group_name' => 'phone_type')
	);
    foreach($phoneTypes['values'] as $phoneType) {
  	  $this->phone_types[$phoneType['name']] = $phoneType['value'];
    }
    
	$this->get_gender();
	$this->get_salutations();
	$this->get_salutations_full();
	$this->get_salutations_greeting();
  }

}