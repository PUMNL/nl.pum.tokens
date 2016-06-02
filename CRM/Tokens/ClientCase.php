<?php

class CRM_Tokens_ClientCase extends CRM_Tokens_CaseRelationship {
  
  protected $location_types = array();
  
  protected $phone_types = array();
  
  public function __construct($token_name, $token_label, $values, $case_id = null) {
    $this->token_name = $token_name;
    $this->token_label = $token_label;

    $config = CRM_Tokens_Config_Config::singleton();

    $this->location_types = $config->getLocationTypes();
    $this->phone_types = $config->getPhoneTypes();

    $this->gender = $config->getGender();
    $this->salutations = $config->getSalutations();
    $this->salutations_full = $config->getSalutionsFull();
    $this->salutations_greeting = $config->getSalutionsGreeting();

    if (empty($case_id)) {
      $this->case_id = CRM_Tokens_CaseId::getCaseId();
    } elseif($case_id=='parent') {
      $this->case_id = CRM_Tokens_CaseId::getParentCaseId();
    } else {
      $this->case_id = $case_id;
    }

    if (is_array($values) && isset($values['activity.activity_id'])) {
      $activity_id = $values['activity.activity_id'];
      if (!isset(self::$case_activity_ids[$activity_id])) {
        self::$case_activity_ids[$activity_id] = false;
        self::$case_activity_ids[$activity_id] = CRM_Core_DAO::singleValueQuery("SELECT case_id from civicrm_case_activity where activity_id = %1", array(1=>array($activity_id, 'Integer')));
      }
      if (!empty(self::$case_activity_ids[$activity_id])) {
        $this->case_id = self::$case_activity_ids[$activity_id];
      }
      if ($case_id == 'parent') {
        $this->case_id = CRM_Tokens_CaseId::fetchParentCaseId($this->case_id);
      }
    }
    if (is_array($values) && isset($values['activity.activity_id']) && !empty($this->case_id)) {
      $this->scheduled_reminder_token = true;
    }
  }

  protected function getContactId() {
    if ($this->contact_id) {
      return $this->contact_id;
    } elseif (!$this->case_id) {
      return false;
    }

    try {
      $this->contact_id = civicrm_api3('Case', 'getvalue', array(
        'return' => 'client_id',
        'id' => $this->case_id,
      ));
      if (is_array($this->contact_id)) {
        $this->contact_id = reset($this->contact_id);
      }
      return $this->contact_id;
    } catch (Exception $e) {
      //do nothing
    }
    return false;
  }

}