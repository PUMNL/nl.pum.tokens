	<?php

class CRM_Tokens_InfoDsa {

  protected $token_name;
  protected $token_label;
  protected $case_id;
  protected $startdate;
  protected $enddate;

  public function __construct($token_name, $token_label, $case_id = null) {
    $this->token_name = $token_name;
    $this->token_label = $token_label;

    if (empty($case_id)) {
      $this->case_id = CRM_Tokens_CaseId::getCaseId();
    } else {
      $this->case_id = $case_id;
    }
    
    if(!empty($this->case_id)) {
    
      // retrieve custom group details
	  $api_result = civicrm_api3('CustomGroup', 'get', array('name' => 'Info_for_DSA'));
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
      
      // retrieve custom data
      if (!empty($customfields)) {
        try{
          $sql = 'SELECT ' . implode(', ', $customfields) . ' FROM ' . $customgroup_table . ' WHERE entity_id = ' . $this->case_id;
          $dao = CRM_Core_DAO::executeQuery($sql);
          if($dao->N == 1) {
            $dao->fetch();
            $this->startdate = $dao->start_date;
            $this->enddate = $dao->end_date;
          }
        } catch (Exception $e) {
          //do nothing
        }
      }
    }
  }
  
  
  public function tokens(&$tokens) {
    $t = array();
    $t[$this->token_name.'.startdate'] = t('Start date of '.$this->token_label);
    $t[$this->token_name.'.enddate'] = t('End date of '.$this->token_label);
    $tokens[$this->token_name] = $t;
  }
  
  
  public function tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null) {
    if ($this->checkToken($tokens, 'startdate')) {
      $this->startDateToken($values, $cids, 'startdate');
    }
    if ($this->checkToken($tokens, 'enddate')) {
      $this->endDateToken($values, $cids, 'enddate');
    }
  }
  
  private function startDateToken(&$values, $cids, $token) {
    $formatted_date = '';
    if ($this->case_id) {
      if ($this->startdate) {
        $date = new DateTime($this->startdate);
        $formatted_date = $date->format('Y-m-d');
      }
    }
    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $formatted_date;
    }
  }
  
  private function endDateToken(&$values, $cids, $token) {
    $formatted_date = '';
    if ($this->case_id) {
      if ($this->enddate) {
        $date = new DateTime($this->enddate);
        $formatted_date = $date->format('Y-m-d');
      }
    }
    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $formatted_date;
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