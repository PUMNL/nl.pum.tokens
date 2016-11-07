<?php

class CRM_Tokens_CaseDonation {

  protected $token_name;
  protected $token_label;
  protected $case_id;
  
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
  }

  public function tokens(&$tokens) {
    $t = array();
    $t[$this->token_name.'.donation_for_fa'] = ts($this->token_label);
    $tokens[$this->token_name] = $t;
  }
  
  public function tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null) {
    if ($this->checkToken($tokens, 'donation_for_fa')) {
      $this->donationToken($values, $cids, 'donation_for_fa');
    }
  }
  
  private function donationToken(&$values, $cids, $token) {
    if (!empty($this->case_id)) {
      $dao = CRM_Core_DAO::executeQuery("SELECT dnr.display_name FROM civicrm_case c
                                          LEFT JOIN civicrm_donor_link dlk ON dlk.entity_id = c.id AND dlk.entity = 'Case' AND dlk.is_fa_donor = 1
                                          LEFT JOIN civicrm_contribution ctr ON ctr.id = dlk.donation_entity_id
                                          LEFT JOIN civicrm_contact dnr ON dnr.id = ctr.contact_id
                                          WHERE c.id = %1 LIMIT 1", 
                                          array(1=>array($this->case_id, 'Integer')));
      
      while($dao->fetch()) {
        $value = $dao->display_name;
        
        foreach($cids as $cid) {
          $values[$cid][$this->token_name.'.'.$token] = $value;
        }
      }
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