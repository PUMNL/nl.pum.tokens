<?php

class CRM_Tokens_ActivitySourceContactTokens {

  protected $token_name = 'activity_source_contact';

  protected $token_label = 'Activity source contact';

  protected $source_contact_ids = array();

  public function tokens(&$tokens) {
    $t = array();
    $t[$this->token_name . '.display_name'] = ts('Display name of ' . $this->token_label);
    $t[$this->token_name . '.first_name'] = ts('First name of ' . $this->token_label);
    $t[$this->token_name . '.prefix'] = ts('Prefix of ' . $this->token_label);
    $t[$this->token_name . '.middle_name'] = ts('Middle name of ' . $this->token_label);
    $t[$this->token_name . '.last_name'] = ts('Last name of ' . $this->token_label);
    $tokens[$this->token_name] = $t;
  }

  public function tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null) {
    if ($this->checkToken($tokens, 'display_name')) {
      $this->displayNameToken($values, $cids, 'display_name');
    }
    if ($this->checkToken($tokens, 'prefix')) {
      $this->prefixToken($values, $cids, 'prefix');
    }
    if ($this->checkToken($tokens, 'first_name')) {
      $this->firstNameToken($values, $cids, 'first_name');
    }
    if ($this->checkToken($tokens, 'middle_name')) {
      $this->middleNameToken($values, $cids, 'middle_name');
    }
    if ($this->checkToken($tokens, 'last_name')) {
      $this->lastNameToken($values, $cids, 'last_name');
    }
  }

  private function prefixToken(&$values, $cids, $token) {
    $prefix = '';
    foreach($cids as $cid) {
      $contact_id = $this->getSourceContactId($values[$cid]);
      if ($contact_id) {
        $prefix = civicrm_api3('Contact', 'getvalue', array(
          'return' => 'individual_prefix',
          'id' => $contact_id
        ));
      }
      $values[$cid][$this->token_name.'.'.$token] = $prefix;
    }
  }

  private function firstNameToken(&$values, $cids, $token) {
    $name = '';
    foreach($cids as $cid) {
      $contact_id = $this->getSourceContactId($values[$cid]);
      if ($contact_id) {
        $name = civicrm_api3('Contact', 'getvalue', array(
          'return' => 'first_name',
          'id' => $contact_id
        ));
      }
      $values[$cid][$this->token_name.'.'.$token] = $name;
    }
  }

  private function middleNameToken(&$values, $cids, $token) {
    $name = '';
    foreach($cids as $cid) {
      $contact_id = $this->getSourceContactId($values[$cid]);
      if ($contact_id) {
        $name = civicrm_api3('Contact', 'getvalue', array(
          'return' => 'middle_name',
          'id' => $contact_id
        ));
      }
      $values[$cid][$this->token_name.'.'.$token] = $name;
    }
  }

  private function lastNameToken(&$values, $cids, $token) {
    $name = '';
    foreach($cids as $cid) {
      $contact_id = $this->getSourceContactId($values[$cid]);
      if ($contact_id) {
        $name = civicrm_api3('Contact', 'getvalue', array(
          'return' => 'last_name',
          'id' => $contact_id
        ));
      }
      $values[$cid][$this->token_name.'.'.$token] = $name;
    }
  }

  private function displayNameToken(&$values, $cids, $token) {
    $name = '';
    foreach($cids as $cid) {
      $contact_id = $this->getSourceContactId($values[$cid]);
      if ($contact_id) {
        $name = civicrm_api3('Contact', 'getvalue', array(
          'return' => 'display_name',
          'id' => $contact_id
        ));
      }
      $values[$cid][$this->token_name.'.'.$token] = $name;
    }
  }

  protected function getSourceContactId($value) {
    if (!isset($value['activity.activity_id'])) {
      return false;
    }
    $aid = $value['activity.activity_id'];
    if (!isset($this->source_contact_ids[$aid])) {
      $this->source_contact_ids[$aid] = civicrm_api3('Activity', 'getvalue', array('return' => 'source_contact_id', 'id' => $aid));
    }
    return $this->source_contact_ids[$aid];
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