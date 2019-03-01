<?php

class CRM_Tokens_ActivitySourceContactTokens {

  protected $token_name = 'activity_source_contact';

  protected $token_label = 'Activity source contact';

  protected $source_contact_ids = array();

  protected $salutations;

  protected $salutations_full;

  protected $salutations_greeting;

  protected $gender;

  public function __construct() {
    $config = CRM_Tokens_Config_Config::singleton();

    $this->gender = $config->getGender();
    $this->salutations = $config->getSalutations();
    $this->salutations_full = $config->getSalutionsFull();
    $this->salutations_greeting = $config->getSalutionsGreeting();
  }
  public function tokens(&$tokens) {
    $t = array();
    $t[$this->token_name . '.display_name'] = ts('Display name of ' . $this->token_label);
    $t[$this->token_name . '.first_name'] = ts('First name of ' . $this->token_label);
    $t[$this->token_name . '.prefix'] = ts('Prefix of ' . $this->token_label);
    $t[$this->token_name . '.middle_name'] = ts('Middle name of ' . $this->token_label);
    $t[$this->token_name . '.last_name'] = ts('Last name of ' . $this->token_label);

    $config = CRM_Tokens_Config_Config::singleton();
    if (!empty($this->salutations)) {
      foreach ($this->salutations as $key => $value) {
        $t[$this->token_name . '.salutation_' . $key] = ts('Salutation (' . $key . ') for ' . $this->token_label);
      }
    }

    if (!empty($this->salutations_full)) {
      foreach ($this->salutations_full as $key => $value) {
        $t[$this->token_name . '.salutationfull_' . $key] = ts('Salutation Full (' . $key . ') for ' . $this->token_label);
      }
    }

    if (!empty($this->salutations_greeting)) {
      foreach ($this->salutations_greeting as $key => $value) {
        $t[$this->token_name . '.salutationgreeting_' . $key] = ts('Salutation Greeting (' . $key . ') for ' . $this->token_label);
      }
    }

    $tokens[$this->token_name] = $t;
  }

  public function tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null) {
    $this->displayNameToken($values, $cids, 'display_name');
    $this->prefixToken($values, $cids, 'prefix');
    $this->firstNameToken($values, $cids, 'first_name');
    $this->middleNameToken($values, $cids, 'middle_name');
    $this->lastNameToken($values, $cids, 'last_name');

    if (!empty($this->salutations)) {
      foreach ($this->salutations as $key => $value) {
        if ($this->isTokenInTokens($tokens, 'salutation_' . $key)) {
          $this->salutationToken($values, $cids, 'salutation_' . $key, $key);
        }
      }
    }

    if (!empty($this->salutations_full)) {
      foreach ($this->salutations_full as $key => $value) {
        if ($this->isTokenInTokens($tokens, 'salutationfull_' . $key)) {
          $this->salutationFullToken($values, $cids, 'salutationfull_' . $key, $key);
        }
      }
    }

    if (!empty($this->salutations_greeting)) {
      foreach ($this->salutations_greeting as $key => $value) {
        if ($this->isTokenInTokens($tokens, 'salutationgreeting_' . $key)) {
          $this->salutationgreetingToken($values, $cids, 'salutationgreeting_' . $key, $key);
        }
      }
    }
  }

  private function prefixToken(&$values, $cids, $token) {
    $prefix = '';
    $contacts_ids = $cids;
    if (!is_array($cids)) {
      $contacts_ids = array($cids);
    }
    foreach($contacts_ids as $cid) {
      if (!is_array($cids)) {
        $value = $values;
      } else {
        $value = $values[$cid];
      }
      $contact_id = $this->getSourceContactId($value);
      if ($contact_id) {
        $prefix = civicrm_api3('Contact', 'getvalue', array(
          'return' => 'individual_prefix',
          'id' => $contact_id
        ));
      }
      if (!is_array($cids)) {
        $values[$this->token_name . '.' . $token] = $prefix;
      } else {
        $values[$cid][$this->token_name . '.' . $token] = $prefix;
      }
    }
  }

  private function firstNameToken(&$values, $cids, $token) {
    $name = '';
    $contacts_ids = $cids;
    if (!is_array($cids)) {
      $contacts_ids = array($cids);
    }
    foreach($contacts_ids as $cid) {
      if (!is_array($cids)) {
        $value = $values;
      } else {
        $value = $values[$cid];
      }
      $contact_id = $this->getSourceContactId($value);
      if ($contact_id) {
        $name = civicrm_api3('Contact', 'getvalue', array(
          'return' => 'first_name',
          'id' => $contact_id
        ));
      }
      if (!is_array($cids)) {
        $values[$this->token_name . '.' . $token] = $name;
      } else {
        $values[$cid][$this->token_name . '.' . $token] = $name;
      }
    }
  }

  private function middleNameToken(&$values, $cids, $token) {
    $name = '';
    $contacts_ids = $cids;
    if (!is_array($cids)) {
      $contacts_ids = array($cids);
    }
    foreach($contacts_ids as $cid) {
      if (!is_array($cids)) {
        $value = $values;
      } else {
        $value = $values[$cid];
      }
      $contact_id = $this->getSourceContactId($value);
      if ($contact_id) {
        $name = civicrm_api3('Contact', 'getvalue', array(
          'return' => 'middle_name',
          'id' => $contact_id
        ));
      }
      if (!is_array($cids)) {
        $values[$this->token_name . '.' . $token] = $name;
      } else {
        $values[$cid][$this->token_name . '.' . $token] = $name;
      }
    }
  }

  private function lastNameToken(&$values, $cids, $token) {
    $name = '';
    $contacts_ids = $cids;
    if (!is_array($cids)) {
      $contacts_ids = array($cids);
    }
    foreach($contacts_ids as $cid) {
      if (!is_array($cids)) {
        $value = $values;
      } else {
        $value = $values[$cid];
      }
      $contact_id = $this->getSourceContactId($value);
      if ($contact_id) {
        $name = civicrm_api3('Contact', 'getvalue', array(
          'return' => 'last_name',
          'id' => $contact_id
        ));
      }
      if (!is_array($cids)) {
        $values[$this->token_name . '.' . $token] = $name;
      } else {
        $values[$cid][$this->token_name . '.' . $token] = $name;
      }
    }
  }

  private function displayNameToken(&$values, $cids, $token) {
    $name = '';
    $contacts_ids = $cids;
    if (!is_array($cids)) {
      $contacts_ids = array($cids);
    }
    foreach($contacts_ids as $cid) {
      if (!is_array($cids)) {
        $value = $values;
      } else {
        $value = $values[$cid];
      }
      $contact_id = $this->getSourceContactId($value);
      if ($contact_id) {
        $name = civicrm_api3('Contact', 'getvalue', array(
          'return' => 'display_name',
          'id' => $contact_id
        ));
      }
      if (!is_array($cids)) {
        $values[$this->token_name . '.' . $token] = $name;
      } else {
        $values[$cid][$this->token_name . '.' . $token] = $name;
      }
    }
  }

  protected function getSourceContactId($value) {
    if (empty($value['activity.activity_id']) && empty($value['contact_id'])) {
      return false;
    }
    $aid = '';
    if(!empty($value['activity.activity_id'])) {
      $aid = $value['activity.activity_id'];
      if (!isset($this->source_contact_ids[$aid])) {
        $this->source_contact_ids[$aid] = civicrm_api3('Activity', 'getvalue', array('return' => 'source_contact_id', 'id' => $aid));
      }
    }

    if (!empty($this->source_contact_ids[$aid])) {
      return $this->source_contact_ids[$aid];
    } else if (!empty($value['contact_id'])) {
      return $value['contact_id'];
    } else {
      return FALSE;
    }
  }

  private function salutationToken(&$values, $cids, $token, $lang) {
    /**
     *  $lang = 'en' | 'fr' | 'es' | 'nl' | ... (option group 'tokens_salutation')
     *  $this->salutations = array(
     *    'en' = array(
     *      'mr' => 'Mr.,
     *      'mrs' = 'Mrs.,
     *    ),
     *    'fr' = array(
     *      'mr' => 'M.',
     *      'mrs' => 'Mme.',
     *    ),
     *    ...
     *  )
     *  $this->gender = array(
     *    1 => 'mrs',
     *    2 => 'mr',
     *  ) // (option group 'gender')
     */
    $contacts_ids = $cids;
    if (!is_array($cids)) {
      $contacts_ids = array($cids);
    }
    foreach($contacts_ids as $cid) {
      if (!is_array($cids)) {
        $value = $values;
      } else {
        $value = $values[$cid];
      }
      $contact_id = $this->getSourceContactId($value);
      if ($contact_id) {
        $name = civicrm_api3('Contact', 'getvalue', array(
          'return' => 'display_name',
          'id' => $contact_id
        ));

        $params = array(
          'version' => 3,
          'sequential' => 1,
          'contact_id' => $contact_id,
        );
        $result = civicrm_api('Contact', 'get', $params);

        $prefix = 'mr';
        if (!empty($result['values'][0]['gender_id'])) {
          $gender_id = $result['values'][0]['gender_id'];

          if (array_key_exists($gender_id, $this->gender)) {
            $prefix = $this->gender[$gender_id];
          }
        }

        $tokenValue = $this->salutations[$lang][$prefix];
        $this->setTokenValue($values, $cids, $token, $tokenValue);
      }
    }
  }

  private function salutationFullToken(&$values, $cids, $token, $lang) {
    /**
     *  $lang = 'en' | 'fr' | 'es' | 'nl' | ... (option group 'tokens_salutation_full')
     *  $this->salutations = array(
     *    'en' = array(
     *      'mr' => 'sir',
     *      'mrs' = 'madame',
     *    ),
     *    'fr' = array(
     *      'mr' => 'monsieur',
     *      'mrs' => 'madame',
     *    ),
     *    ...
     *  )
     *  $this->gender = array(
     *    1 => 'mrs',
     *    2 => 'mr',
     *  ) // (option group 'gender')
     */

    $contacts_ids = $cids;
    if (!is_array($cids)) {
      $contacts_ids = array($cids);
    }
    foreach($contacts_ids as $cid) {
      if (!is_array($cids)) {
        $value = $values;
      } else {
        $value = $values[$cid];
      }
      $contact_id = $this->getSourceContactId($value);
      if ($contact_id) {
        $name = civicrm_api3('Contact', 'getvalue', array(
          'return' => 'display_name',
          'id' => $contact_id
        ));

        $params = array(
          'version' => 3,
          'sequential' => 1,
          'contact_id' => $contact_id,
        );
        $result = civicrm_api('Contact', 'get', $params);

        $prefix = 'mr';
        if (!empty($result['values'][0]['gender_id'])) {
          $gender_id = $result['values'][0]['gender_id'];

          if (array_key_exists($gender_id, $this->gender)) {
            $prefix = $this->gender[$gender_id];
          }
        }

        $tokenValue = $this->salutations_full[$lang][$prefix];
        $this->setTokenValue($values, $cids, $token, $tokenValue);
      }
    }
  }

  private function salutationgreetingToken(&$values, $cids, $token, $lang) {
    /**
     *  $lang = 'en' | 'fr' | 'es' | 'nl' | ... (option group 'tokens_salutations_greeting')
     *  $this->salutations = array(
     *    'en' = array(
     *      'mr' => 'Dear,
     *      'mrs' = 'Dear,
     *    ),
     *    'es' = array(
     *      'mr' => 'Estimado',
     *      'mrs' => 'Estimada',
     *    ),
     *    ...
     *  )
     *  $this->gender = array(
     *    1 => 'mrs',
     *    2 => 'mr',
     *  ) // (option group 'gender')
     */

    $contacts_ids = $cids;
    if (!is_array($cids)) {
      $contacts_ids = array($cids);
    }
    foreach($contacts_ids as $cid) {
      if (!is_array($cids)) {
        $value = $values;
      } else {
        $value = $values[$cid];
      }
      $contact_id = $this->getSourceContactId($value);
      if ($contact_id) {
        $name = civicrm_api3('Contact', 'getvalue', array(
          'return' => 'display_name',
          'id' => $contact_id
        ));

        $params = array(
          'version' => 3,
          'sequential' => 1,
          'contact_id' => $contact_id,
        );
        $result = civicrm_api('Contact', 'get', $params);

        $prefix = 'mr';
        if (!empty($result['values'][0]['gender_id'])) {
          $gender_id = $result['values'][0]['gender_id'];

          if (array_key_exists($gender_id, $this->gender)) {
            $prefix = $this->gender[$gender_id];
          }
        }

        $tokenValue = $this->salutations_greeting[$lang][$prefix];
        $this->setTokenValue($values, $cids, $token, $tokenValue);
      }
    }
  }

  protected function isTokenInTokens($tokens, $token) {
    if (in_array($token, $tokens)) {
      return true;
    } elseif (isset($tokens[$token])) {
      return true;
    } elseif (isset($tokens[$this->token_name]) && in_array($token, $tokens[$this->token_name])) {
      return true;
    } elseif (isset($tokens[$this->token_name][$token])) {
      return true;
    }
    return FALSE;
  }

  /**
   * Set the value for a token and checks whether cids is an array or not.
   *
   * @param $values
   * @param $cids
   * @param $token
   * @param $tokenValue
   */
  protected function setTokenValue(&$values, $cids, $token, $tokenValue) {
    if (is_array($cids)) {
      foreach ($cids as $cid) {
        $values[$cid][$this->token_name . '.' . $token] = $tokenValue;
      }
    }
    else {
      $values[$this->token_name . '.' . $token] = $tokenValue;
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