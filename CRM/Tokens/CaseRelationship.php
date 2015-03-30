<?php

class CRM_Tokens_CaseRelationship {

  private $relationship_type;

  private $token_name;

  private function __construct($relationship_type_name_a_b, $token_name) {
    $this->token_name = $token_name;
    $this->relationship_type = civicrm_api3('RelationshipType', 'getsingle', array('name_a_b' => $relationship_type_name_a_b));
  }

  public function tokens(&$tokens) {
    $t = array();
    $t[$this->token_name.'address'] = t('Address of '.$this->token_name);
    $tokens[$this->token_name] = $t;
  }

  public function tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null) {
  
  }



}