<?php

class CRM_Tokens_CaseNumber {

  protected $token_name;
  protected $token_label;
  protected $case_id;
  protected $case_number_sequence;
  protected $case_number_type;
  protected $case_number_country;
  
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
      $case_number = civicrm_api3('PumCaseNumber', 'get', array('case_id' => $this->case_id)); // nl.pum.generic
      $this->case_number_sequence = $case_number['values'][0]['sequence'];
      $this->case_number_type     = $case_number['values'][0]['type'];
      $this->case_number_country  = $case_number['values'][0]['country'];
    } catch (Exception $e) {
      //do nothing
    }
  }

  public function tokens(&$tokens) {
    $t = array();
    $t[$this->token_name.'.sequence'] = ts('Sequential number in '.$this->token_label);
    $t[$this->token_name.'.type'] = ts('Type code in '.$this->token_label);
    $t[$this->token_name.'.country'] = ts('County code in  '.$this->token_label);
    $t[$this->token_name.'.full'] = ts('Full notation of '.$this->token_label);
    $tokens[$this->token_name] = $t;
  }
  
  public function tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null) {
    if ($this->checkToken($tokens, 'sequence')) {
      $this->sequenceToken($values, $cids, 'sequence');
    }
    if ($this->checkToken($tokens, 'type')) {
      $this->typeToken($values, $cids, 'type');
    }
    if ($this->checkToken($tokens, 'country')) {
      $this->countryToken($values, $cids, 'country');
    }
    if ($this->checkToken($tokens, 'full')) {
      $this->fullToken($values, $cids, 'full');
    }
  }
  
  private function sequenceToken(&$values, $cids, $token) {
    $value = $this->case_number_sequence;
    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $value;
    }
  }
  
  private function typeToken(&$values, $cids, $token) {
    $value = $this->case_number_type;
    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $value;
    }
  }
  
  private function countryToken(&$values, $cids, $token) {
    $value = $this->case_number_country;
    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $value;
    }
  }
  
  private function fullToken(&$values, $cids, $token) {
    $value = preg_replace('/\s+/', ' ',trim(implode(' ', array($this->case_number_sequence, $this->case_number_type, $this->case_number_country))));
    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $value;
    }
  }
  
  /**
   * Returns true when token in set in the tokens array
   *
   * @param $tokens
   * @param $token
   * @return bool
   */
  protected function checkToken($tokens, $token) {
    if (!empty($tokens[$this->token_name])) {
      if (in_array($token, $tokens[$this->token_name])) {
        return true;
      } elseif (array_key_exists($token, $tokens[$this->token_name])) {
        return true;
      }
    }
    return false;
  }
}