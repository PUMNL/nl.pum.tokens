<?php

class CRM_Tokens_CaseRelationship {

  protected $relationship_type;

  protected $token_name;

  protected $token_label;

  protected $case_id;

  protected $contact_id = false;

  protected $location_types = array();

  public function __construct($relationship_type_name_a_b, $token_name, $token_label, $case_id = null) {
    $this->token_name = $token_name;
    $this->token_label = $token_label;
    $this->relationship_type = civicrm_api3('RelationshipType', 'getsingle', array('name_a_b' => $relationship_type_name_a_b));

    if (empty($case_id)) {
      $this->case_id = CRM_Tokens_CaseId::getCaseId();
    } elseif($case_id=='parent') {
      $this->case_id = CRM_Tokens_CaseId::getParentCaseId();
    } else {
      $this->case_id = $case_id;
    }
	
    try {
      $this->contact_id = civicrm_api3('Relationship', 'getvalue', array(
        'return' => 'contact_id_b',
        'case_id' => $this->case_id,
        'relationship_type_id' => $this->relationship_type['id'],
      ));
    } catch (Exception $e) {
      //do nothing
    }

    $locTypes = civicrm_api3('LocationType', 'get', array());
    foreach($locTypes['values'] as $locType) {
      $this->location_types[$locType['name']] = $locType['id'];
    }
  }

  public function tokens(&$tokens) {
    $t = array();
    $t[$this->token_name.'.address'] = ts('Address of '.$this->token_label);
    $t[$this->token_name.'.work_address'] = ts('Work address of '.$this->token_label);
    $t[$this->token_name.'.display_name'] = ts('Display name of '.$this->token_label);
    $t[$this->token_name.'.email'] = ts('E-mail address of '.$this->token_label);
    $t[$this->token_name.'.work_phone'] = ts('Work phone number of '.$this->token_label);
    $t[$this->token_name.'.passport_first_name'] = ts('Passport firstname of '.$this->token_label);
    $t[$this->token_name.'.passport_last_name'] = ts('Passport lastname of '.$this->token_label);
    $t[$this->token_name.'.passport_number'] = ts('Passport number of '.$this->token_label);
    $t[$this->token_name.'.passport_valid'] = ts('Passport valid date of '.$this->token_label);
    $t[$this->token_name.'.passport_partner_name'] = ts('Passport partner name of '.$this->token_label);
    $t[$this->token_name.'.nationlity'] = ts('Nationality of '.$this->token_label);
    $t[$this->token_name.'.prefix'] = ts('Prefix of '.$this->token_label);
    $t[$this->token_name.'.first_name'] = ts('First name of '.$this->token_label);
    $t[$this->token_name.'.middle_name'] = ts('Middle name of '.$this->token_label);
    $t[$this->token_name.'.last_name'] = ts('Last name of '.$this->token_label);
    $t[$this->token_name.'.birth_date'] = ts('Birth date of '.$this->token_label);
    $t[$this->token_name.'.age'] = ts('Age of '.$this->token_label);
	$t[$this->token_name.'.home_phone'] = ts('Home phone number of '.$this->token_label);
    $tokens[$this->token_name] = $t;
  }

  public function tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null) {
    if ($this->checkToken($tokens, 'address')) {
      $this->addressToken($values, $cids, 'address');
    }
    if ($this->checkToken($tokens, 'work_address')) {
      $this->workAddressToken($values, $cids, 'work_address');
    }
    if ($this->checkToken($tokens, 'display_name')) {
      $this->displayNameToken($values, $cids, 'display_name');
    }
    if ($this->checkToken($tokens, 'email')) {
      $this->emailToken($values, $cids, 'email');
    }
    if ($this->checkToken($tokens, 'work_phone')) {
      $this->workPhoneToken($values, $cids, 'work_phone');
    }
    if ($this->checkToken($tokens, 'passport_first_name')) {
      $this->passportFirstName($values, $cids, 'passport_first_name');
    }
    if ($this->checkToken($tokens, 'passport_last_name')) {
      $this->passportLastName($values, $cids, 'passport_last_name');
    }
    if ($this->checkToken($tokens, 'passport_number')) {
      $this->passportNumber($values, $cids, 'passport_number');
    }
    if ($this->checkToken($tokens, 'passport_valid')) {
      $this->passportValid($values, $cids, 'passport_valid');
    }
    if ($this->checkToken($tokens, 'passport_partner_name')) {
      $this->passportPartnerName($values, $cids, 'passport_partner_name');
    }
    if ($this->checkToken($tokens, 'nationlity')) {
      $this->nationality($values, $cids, 'nationlity');
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
    if ($this->checkToken($tokens, 'birth_date')) {
      $this->birthDate($values, $cids, 'birth_date');
    }
    if ($this->checkToken($tokens, 'age')) {
      $this->age($values, $cids, 'age');
    }
	if ($this->checkToken($tokens, 'home_phone')) {
      $this->homePhoneToken($values, $cids, 'home_phone');
    }
  }

  private function passportFirstName(&$values, $cids, $token) {
    $passport = CRM_Tokens_Config_PassportInfo::singleton();
    $name = '';
    if ($this->contact_id) {
      $name = civicrm_api3('Contact', 'getvalue', array('return' => 'custom_'.$passport->name['id'], 'id' => $this->contact_id));
    }
    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $name;
    }
  }

  private function passportLastName(&$values, $cids, $token) {
    $passport = CRM_Tokens_Config_PassportInfo::singleton();
    $name = '';
    if ($this->contact_id) {
      $name = civicrm_api3('Contact', 'getvalue', array('return' => 'custom_'.$passport->last_name['id'], 'id' => $this->contact_id));
    }
    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $name;
    }
  }

  private function passportPartnerName(&$values, $cids, $token) {
    $passport = CRM_Tokens_Config_PassportInfo::singleton();
    $name = '';
    if ($this->contact_id) {
      $name = civicrm_api3('Contact', 'getvalue', array('return' => 'custom_'.$passport->partner_name['id'], 'id' => $this->contact_id));
    }
    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $name;
    }
  }

  private function passportNumber(&$values, $cids, $token) {
    $passport = CRM_Tokens_Config_PassportInfo::singleton();
    $number = '';
    if ($this->contact_id) {
      $number = civicrm_api3('Contact', 'getvalue', array('return' => 'custom_'.$passport->passport_number['id'], 'id' => $this->contact_id));
    }
    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $number;
    }
  }

  private function passportValid(&$values, $cids, $token) {
    $passport = CRM_Tokens_Config_PassportInfo::singleton();
    $valid = '';
    if ($this->contact_id) {
      $passport_valid = civicrm_api3('Contact', 'getvalue', array('return' => 'custom_'.$passport->passport_valid['id'], 'id' => $this->contact_id));
      if ($passport_valid) {
        $validDate = new DateTime($passport_valid);
        $valid = $validDate->format('Y-m-d');
      }
    }
    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $valid;
    }
  }

  private function birthDate(&$values, $cids, $token) {
    $formated_birth_date = '';
    if ($this->contact_id) {
      $birth_date = civicrm_api3('Contact', 'getvalue', array('return' => 'birth_date', 'id' => $this->contact_id));
      if ($birth_date) {
        $birth_date = new DateTime($birth_date);
        $formated_birth_date = $birth_date->format('Y-m-d');
      }
    }
    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $formated_birth_date;
    }
  }

  private function age(&$values, $cids, $token) {
    $formated_birth_date = '';
    if ($this->contact_id) {
      $birth_date = civicrm_api3('Contact', 'getvalue', array('return' => 'birth_date', 'id' => $this->contact_id));
      if ($birth_date) {
        $birth_date = new DateTime($birth_date);
        $formated_birth_date = $birth_date->diff(new DateTime())->y;
      }
    }
    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $formated_birth_date;
    }
  }

  private function nationality(&$values, $cids, $token) {
    $passport = CRM_Tokens_Config_PassportInfo::singleton();
    $nationality = '';
    if ($this->contact_id) {
      $nationality = civicrm_api3('Contact', 'getvalue', array('return' => 'custom_'.$passport->nationality['id'], 'id' => $this->contact_id));
    }
    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $nationality;
    }
  }

  private function prefixToken(&$values, $cids, $token) {
    $prefix = '';
    if ($this->contact_id) {
      $prefix = civicrm_api3('Contact', 'getvalue', array('return' => 'individual_prefix', 'id' => $this->contact_id));
    }
    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $prefix;
    }
  }

  private function firstNameToken(&$values, $cids, $token) {
    $name = '';
    if ($this->contact_id) {
      $name = civicrm_api3('Contact', 'getvalue', array('return' => 'first_name', 'id' => $this->contact_id));
    }
    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $name;
    }
  }

  private function middleNameToken(&$values, $cids, $token) {
    $name = '';
    if ($this->contact_id) {
      $name = civicrm_api3('Contact', 'getvalue', array('return' => 'middle_name', 'id' => $this->contact_id));
    }
    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $name;
    }
  }

  private function lastNameToken(&$values, $cids, $token) {
    $name = '';
    if ($this->contact_id) {
      $name = civicrm_api3('Contact', 'getvalue', array('return' => 'last_name', 'id' => $this->contact_id));
    }
    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $name;
    }
  }

  private function displayNameToken(&$values, $cids, $token) {
    $name = '';
    if ($this->contact_id) {
      $name = civicrm_api3('Contact', 'getvalue', array('return' => 'display_name', 'id' => $this->contact_id));
    }
    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $name;
    }
  }

  private function homePhoneToken(&$values, $cids, $token) {
    $phone = '';
    $phoneAr = array();
    if ($this->contact_id) {
      $phoneNumber = civicrm_api('Phone', 'get', array(
        'contact_id' => $this->contact_id,
        'location_type_id' => $this->location_types['Home'],
        'version' => 3,
      ));

      if (!empty($phoneNumber['values'])) {
	    foreach($phoneNumber['values'] as $value) {
          if(!empty($value['phone'])) {
            $phoneAr[] = $value['phone'];
		  }
        }
      }
	  
	  $phone = implode(', ', $phoneAr);
    }

    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $phone;
    }
  }
  
  private function workPhoneToken(&$values, $cids, $token) {
    $phone = '';
    if ($this->contact_id) {
      $phoneNumber = civicrm_api('Phone', 'getsingle', array(
        'contact_id' => $this->contact_id,
        'location_type_id' => $this->location_types['Work'],
        'version' => 3,
      ));

      if (!empty($phoneNumber) && !empty($phoneNumber['phone'])) {
        $phone = $phoneNumber['phone'];
      }
    }

    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $phone;
    }
  }

  private function workAddressToken(&$values, $cids, $token) {
    $formatedAddress = '';
    if ($this->contact_id) {
      $address = civicrm_api('Address', 'getsingle', array(
        'contact_id' => $this->contact_id,
        'location_type_id' => $this->location_types['Work'],
        'version' => 3,
      ));

      if (!empty($address) && !empty($address['id'])) {
        $country_id = $address['country_id'];
        $country = CRM_Core_PseudoConstant::country($country_id);

        $formatedAddress .= $address['street_address'] . "<br>\r\n";
        $formatedAddress .= $address['postal_code'] . ' ' . $address['city'] . "<br>\r\n";
        $formatedAddress .= $country;
      }
    }

    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $formatedAddress;
    }
  }

  private function emailToken(&$values, $cids, $token) {
    $formattedEmail = '';
    if ($this->contact_id) {
      $email = civicrm_api('Email', 'getsingle', array(
        'contact_id' => $this->contact_id,
        'is_primary' => 1,
        'version' => 3,
      ));

      if (!empty($email) && !empty($email['email'])) {
        $formattedEmail = '<a href="mailto:'.$email['email'].'">'.$email['email'].'</a>';
      }
    }

    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $formattedEmail;
    }
  }

  private function addressToken(&$values, $cids, $token) {
    $formatedAddress = '';
    if ($this->contact_id) {
      $address = civicrm_api('Address', 'getsingle', array(
        'contact_id' => $this->contact_id,
        'is_primary' => 1,
        'version' => 3,
      ));

      if (!empty($address) && !empty($address['id'])) {
        $country_id = $address['country_id'];
        $country = CRM_Core_PseudoConstant::country($country_id);

        $formatedAddress .= $address['street_address'] . "<br>\r\n";
        $formatedAddress .= $address['postal_code'] . ' ' . $address['city'] . "<br>\r\n";
        $formatedAddress .= $country;
      }
    }

    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $formatedAddress;
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