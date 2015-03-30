<?php

class CRM_Tokens_ClientCase extends CRM_Tokens_CaseRelationship {

  public function __construct($token_name, $token_label, $case_id = null) {
    $this->token_name = $token_name;
    $this->token_label = $token_label;

    if (empty($case_id)) {
      $this->case_id = CRM_Tokens_CaseId::getCaseId();
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
      //do nothing
    }
  }

}