<?php

class CRM_Tokens_CustomerContribution {

  protected $token_name;
  protected $token_label;
  protected $case_id;
  protected $contribution_amount;

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
    $t[$this->token_name.'.amount'] = ts('Contribution amount of '.$this->token_label);
    $tokens[$this->token_name] = $t;
  }

  public function tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null) {
    if ($this->checkToken($tokens, 'amount')) {
      $this->contributionAmountToken($values, $cids, 'amount');
    }
  }

  private function contributionAmountToken(&$values, $cids, $token) {
    // retrieve custom group details
    $api_result = civicrm_api3('CustomGroup', 'get', array('name' => 'Contribution'));
    if ($api_result['count']==1) {
      $first = array_slice($api_result['values'], 0, 1);
      $customgroup_id = $first[0]['id'];
      $customgroup_table = $first[0]['table_name'];
    }

    // retrieve custom field details
    $customfields = array();
    if (!is_null($customgroup_id)) {
      $api_result = civicrm_api3('CustomField', 'get', array('custom_group_id' => $customgroup_id));
      if ($api_result['count']>0) {
        foreach($api_result['values'] as $fld) {
          $customfields[] = $fld['column_name'] . ' AS ' . $fld['name'];
        }
      }
    }

    if (!empty($this->case_id) && !empty($customfields) && !empty($customgroup_table)) {
      try{
        $sql = 'SELECT ' . implode(', ', $customfields) .
               ' FROM civicrm_activity act' .
               ' LEFT JOIN civicrm_case_activity ca ON ca.activity_id = act.id' .
               ' LEFT JOIN ' . $customgroup_table . ' cont ON cont.entity_id = act.id' .
               ' WHERE ca.case_id = '.$this->case_id.' AND act.activity_type_id = (SELECT ov.value FROM civicrm_option_value ov WHERE ov.option_group_id = (SELECT id FROM civicrm_option_group og WHERE og.name = \'activity_type\') AND ov.label = \'Condition: Customer Contribution\')' .
               ' AND act.is_current_revision = 1' .
               ' LIMIT 1';

        $dao = CRM_Core_DAO::executeQuery($sql);
        if($dao->N == 1) {
          $dao->fetch();
          $this->contribution_amount = $dao->Amount;
        }
      } catch (Exception $e) {
        //do nothing
      }
    }
    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $this->contribution_amount;
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