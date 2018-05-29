<?php

class CRM_Tokens_SysInfo {

  protected $token_name;
  protected $token_label;
  protected $datetime;

  public function __construct($token_name, $token_label) {
    $this->token_name = $token_name;
    $this->token_label = $token_label;
	$config = CRM_Core_Config::singleton();
  }

  public function tokens(&$tokens) {
    $t = array();
    $t[$this->token_name.'.datetime'] = ts('Date/time from ' . $this->token_label);
    $t[$this->token_name.'.fulldate'] = ts('Full date from ' . $this->token_label);
    $t[$this->token_name.'.partialdate'] = ts('Partial date from ' . $this->token_label);
    $t[$this->token_name.'.year'] = ts('Year from ' . $this->token_label);
    $t[$this->token_name.'.time'] = ts('Time from ' . $this->token_label);
    $tokens[$this->token_name] = $t;
  }

  public function tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null) {
    $config = CRM_Core_Config::singleton();
    $this->datetime = time();
    if ($this->checkToken($tokens, 'datetime')) {
      $this->dateTimeToken($values, $cids, 'datetime', 'd-m-Y H:i:s');
    }
    if ($this->checkToken($tokens, 'fulldate')) {
      $this->dateTimeToken($values, $cids, 'fulldate', 'd-m-Y');
    }
    if ($this->checkToken($tokens, 'partialdate')) {
      $this->dateTimeToken($values, $cids, 'partialdate', 'd-m');
    }
    if ($this->checkToken($tokens, 'year')) {
      $this->dateTimeToken($values, $cids, 'year', 'Y');
    }
    if ($this->checkToken($tokens, 'time')) {
      $this->dateTimeToken($values, $cids, 'time', 'H:i:s');
    }
  }

  private function dateTimeToken(&$values, $cids, $token, $format=null) {
    $value = $this->datetime;
    if(is_array($cids)) {
      foreach($cids as $cid) {
        $values[$cid][$this->token_name.'.'.$token] = date($format, $value); // $format . ' ' . date($format, $value);
      }
    } else {
      $values[$this->token_name.'.'.$token] = date($format, $value); // $format . ' ' . date($format, $value);
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