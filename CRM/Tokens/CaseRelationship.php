<?php

class CRM_Tokens_CaseRelationship {

  protected $relationship_type;

  protected $token_name;

  protected $token_label;

  protected $case_id;

  protected $contact_id = FALSE;

  protected $location_types = array();

  protected $phone_types = array();

  protected $salutations;

  protected $salutations_full;

  protected $salutations_greeting;

  protected $gender;

  public function __construct($relationship_type_name_a_b, $token_name, $token_label, $case_id = NULL) {
    $this->token_name = $token_name;
    $this->token_label = $token_label;
    $this->relationship_type = civicrm_api3('RelationshipType', 'getsingle', array('name_a_b' => $relationship_type_name_a_b));

    $config = CRM_Tokens_Config_Config::singleton();

    $this->location_types = $config->getLocationTypes();
    $this->phone_types = $config->getPhoneTypes();

    $this->gender = $config->getGender();
    $this->salutations = $config->getSalutations();
    $this->salutations_full = $config->getSalutionsFull();
    $this->salutations_greeting = $config->getSalutionsGreeting();

    if (empty($case_id)) {
      $this->case_id = CRM_Tokens_CaseId::getCaseId();
    }
    elseif ($case_id == 'parent') {
      $this->case_id = CRM_Tokens_CaseId::getParentCaseId();
    }
    else {
      $this->case_id = $case_id;
    }
  }

  protected function getContactId() {
    if ($this->contact_id) {
      return $this->contact_id;
    } elseif (!$this->case_id) {
      return false;
    }

    try {
      $this->contact_id = civicrm_api3('Relationship', 'getvalue', array(
        'return' => 'contact_id_b',
        'case_id' => $this->case_id,
        'relationship_type_id' => $this->relationship_type['id'],
      ));
      return $this->contact_id;
    } catch (Exception $e) {
      //do nothing
    }
    return false;
  }

  public static function tokens(&$tokens, $token_name, $token_label) {
    $t = array();
    $t[$token_name . '.address'] = ts('Address of ' . $token_label);
    $t[$token_name . '.address_street'] = ts('Address street of ' . $token_label);
    $t[$token_name . '.address_postalcode'] = ts('Address postalcode of ' . $token_label);
    $t[$token_name . '.address_city'] = ts('Address city of ' . $token_label);
    $t[$token_name . '.address_country'] = ts('Address country of ' . $token_label);
    $t[$token_name . '.work_address'] = ts('Work address of ' . $token_label);
    $t[$token_name . '.work_street'] = ts('Work address street of ' . $token_label);
    $t[$token_name . '.work_postalcode'] = ts('Work address postalcode of ' . $token_label);
    $t[$token_name . '.work_city'] = ts('Work address city of ' . $token_label);
    $t[$token_name . '.work_country'] = ts('Work address country of ' . $token_label);
    $t[$token_name . '.primary_phone'] = ts('Primary phone number of ' . $token_label);
    $t[$token_name . '.home_phone'] = ts('Home phone number of ' . $token_label);
    $t[$token_name . '.home_mobile'] = ts('Home mobile number of ' . $token_label);
    $t[$token_name . '.home_fax'] = ts('Home fax number of ' . $token_label);
    $t[$token_name . '.work_phone'] = ts('Work phone number of ' . $token_label);
    $t[$token_name . '.work_mobile'] = ts('Work mobile number of ' . $token_label);
    $t[$token_name . '.work_fax'] = ts('Work fax number of ' . $token_label);
    $t[$token_name . '.main_phone'] = ts('Main phone number of ' . $token_label);
    $t[$token_name . '.main_mobile'] = ts('Main mobile number of ' . $token_label);
    $t[$token_name . '.main_fax'] = ts('Main fax number of ' . $token_label);
    $t[$token_name . '.display_name'] = ts('Display name of ' . $token_label);
    $t[$token_name . '.email'] = ts('E-mail address of ' . $token_label);
    $t[$token_name . '.passport_first_name'] = ts('Passport firstname of ' . $token_label);
    $t[$token_name . '.passport_last_name'] = ts('Passport lastname of ' . $token_label);
    $t[$token_name . '.passport_number'] = ts('Passport number of ' . $token_label);
    $t[$token_name . '.passport_valid'] = ts('Passport valid date of ' . $token_label);
    $t[$token_name . '.passport_issue_date'] = ts('Passport issue date of ' . $token_label);
    $t[$token_name . '.passport_issue_place'] = ts('Passport issue place of ' . $token_label);
    $t[$token_name . '.passport_partner_name'] = ts('Passport partner name of ' . $token_label);
    $t[$token_name . '.nationlity'] = ts('Nationality of ' . $token_label);
    $t[$token_name . '.prefix'] = ts('Prefix of ' . $token_label);
    $t[$token_name . '.first_name'] = ts('First name of ' . $token_label);
    $t[$token_name . '.middle_name'] = ts('Middle name of ' . $token_label);
    $t[$token_name . '.last_name'] = ts('Last name of ' . $token_label);
    $t[$token_name . '.birth_date'] = ts('Birth date of ' . $token_label);
    $t[$token_name . '.age'] = ts('Age of ' . $token_label);

    $config = CRM_Tokens_Config_Config::singleton();
    if (!empty($config->getSalutations())) {
      foreach ($config->getSalutations() as $key => $value) {
        $t[$token_name . '.salutation_' . $key] = ts('Salutation (' . $key . ') for ' . $token_label);
      }
    }

    if (!empty($config->getSalutionsFull())) {
      foreach ($config->getSalutionsFull() as $key => $value) {
        $t[$token_name . '.salutationfull_' . $key] = ts('Salutation Full (' . $key . ') for ' . $token_label);
      }
    }

    if (!empty($config->getSalutionsGreeting())) {
      foreach ($config->getSalutionsGreeting() as $key => $value) {
        $t[$token_name . '.salutationgreeting_' . $key] = ts('Salutation Greeting (' . $key . ') for ' . $token_label);
      }
    }

    $tokens[$token_name] = $t;
  }

  public function tokenValues(&$values, $cids, $job = NULL, $tokens = array(), $context = NULL) {
    $this->addressToken($values, $cids, 'address');
    $this->addressStreetToken($values, $cids, 'address_street');
    $this->addressPostalCodeToken($values, $cids, 'address_postalcode');
    $this->addressCityToken($values, $cids, 'address_city');
    $this->addressCountryToken($values, $cids, 'address_country');
    $this->workAddressToken($values, $cids, 'work_address');
    $this->workStreetToken($values, $cids, 'work_street');
    $this->workPostalCodeToken($values, $cids, 'work_postalcode');
    $this->workCityToken($values, $cids, 'work_city');
    $this->workCountryToken($values, $cids, 'work_country');
    $this->displayNameToken($values, $cids, 'display_name');
    $this->emailToken($values, $cids, 'email');
    $this->workPhoneToken($values, $cids, 'work_phone');
    $this->passportFirstName($values, $cids, 'passport_first_name');
    $this->passportLastName($values, $cids, 'passport_last_name');
    $this->passportNumber($values, $cids, 'passport_number');
    $this->passportValid($values, $cids, 'passport_valid');
    $this->passportIssueDate($values, $cids, 'passport_issue_date');
    $this->passportIssuePlace($values, $cids, 'passport_issue_place');
    $this->passportPartnerName($values, $cids, 'passport_partner_name');
    $this->nationality($values, $cids, 'nationlity');
    $this->prefixToken($values, $cids, 'prefix');
    $this->firstNameToken($values, $cids, 'first_name');
    $this->middleNameToken($values, $cids, 'middle_name');
    $this->lastNameToken($values, $cids, 'last_name');
    $this->birthDate($values, $cids, 'birth_date');
    $this->age($values, $cids, 'age');
    $this->homePhoneToken($values, $cids, 'home_phone');
    $this->homeMobileToken($values, $cids, 'home_mobile');
    $this->homeFaxToken($values, $cids, 'home_fax');
    $this->workMobileToken($values, $cids, 'work_mobile');
    $this->workFaxToken($values, $cids, 'work_fax');
    $this->mainPhoneToken($values, $cids, 'main_phone');
    $this->mainMobileToken($values, $cids, 'main_mobile');
    $this->mainFaxToken($values, $cids, 'main_fax');
    $this->primaryPhoneToken($values, $cids, 'primary_phone');

    if (!empty($this->salutations)) {
      foreach ($this->salutations as $key => $value) {
        $this->salutationToken($values, $cids, 'salutation_' . $key, $key);
      }
    }

    if (!empty($this->salutations_full)) {
      foreach ($this->salutations_full as $key => $value) {
        $this->salutationFullToken($values, $cids, 'salutationfull_' . $key, $key);
      }
    }

    if (!empty($this->salutations_greeting)) {
      foreach ($this->salutations_greeting as $key => $value) {
        $this->salutationgreetingToken($values, $cids, 'salutationgreeting_' . $key, $key);
      }
    }
  }

  private function passportFirstName(&$values, $cids, $token) {
    $passport = CRM_Tokens_Config_PassportInfo::singleton();
    $tokenValue = '';
    if ($this->getContactId()) {
      $tokenValue = civicrm_api3('Contact', 'getvalue', array(
        'return' => 'custom_' . $passport->name['id'],
        'id' => $this->getContactId()
      ));
    }
    $this->setTokenValue($values, $cids, $token, $tokenValue);
  }

  private function passportLastName(&$values, $cids, $token) {
    $passport = CRM_Tokens_Config_PassportInfo::singleton();
    $tokenValue = '';
    if ($this->getContactId()) {
      $tokenValue = civicrm_api3('Contact', 'getvalue', array(
        'return' => 'custom_' . $passport->last_name['id'],
        'id' => $this->getContactId()
      ));
    }
    $this->setTokenValue($values, $cids, $token, $tokenValue);
  }

  private function passportPartnerName(&$values, $cids, $token) {
    $passport = CRM_Tokens_Config_PassportInfo::singleton();
    $tokenValue = '';
    if ($this->getContactId()) {
      $tokenValue = civicrm_api3('Contact', 'getvalue', array(
        'return' => 'custom_' . $passport->partner_name['id'],
        'id' => $this->getContactId()
      ));
    }
    $this->setTokenValue($values, $cids, $token, $tokenValue);
  }

  private function passportNumber(&$values, $cids, $token) {
    $passport = CRM_Tokens_Config_PassportInfo::singleton();
    $tokenValue = '';
    if ($this->getContactId()) {
      $tokenValue = civicrm_api3('Contact', 'getvalue', array(
        'return' => 'custom_' . $passport->passport_number['id'],
        'id' => $this->getContactId()
      ));
    }
    $this->setTokenValue($values, $cids, $token, $tokenValue);
  }

  private function passportValid(&$values, $cids, $token) {
    $passport = CRM_Tokens_Config_PassportInfo::singleton();
    $tokenValue = '';
    if ($this->getContactId()) {
      $passport_valid = civicrm_api3('Contact', 'getvalue', array(
        'return' => 'custom_' . $passport->passport_valid['id'],
        'id' => $this->getContactId()
      ));
      if ($passport_valid) {
        $validDate = new DateTime($passport_valid);
        $tokenValue = $validDate->format('Y-m-d');
      }
    }
    $this->setTokenValue($values, $cids, $token, $tokenValue);
  }

  private function passportIssueDate(&$values, $cids, $token) {
    $passport = CRM_Tokens_Config_PassportInfo::singleton();
    $tokenValue = '';
    if ($this->getContactId()) {
      $passport_issued = civicrm_api3('Contact', 'getvalue', array(
        'return' => 'custom_' . $passport->passport_issue_date['id'],
        'id' => $this->getContactId()
      ));
      if ($passport_issued) {
        $validDate = new DateTime($passport_issued);
        $tokenValue = $validDate->format('Y-m-d');
      }
    }
    $this->setTokenValue($values, $cids, $token, $tokenValue);
  }

  private function passportIssuePlace(&$values, $cids, $token) {
    $passport = CRM_Tokens_Config_PassportInfo::singleton();
    $tokenValue = '';
    if ($this->getContactId()) {
      $tokenValue = civicrm_api3('Contact', 'getvalue', array(
        'return' => 'custom_' . $passport->passport_issue_place['id'],
        'id' => $this->getContactId()
      ));
    }
    $this->setTokenValue($values, $cids, $token, $tokenValue);
  }

  private function birthDate(&$values, $cids, $token) {
    $tokenValue = '';
    if ($this->getContactId()) {
      $birth_date = civicrm_api3('Contact', 'getvalue', array(
        'return' => 'birth_date',
        'id' => $this->getContactId()
      ));
      if ($birth_date) {
        $birth_date = new DateTime($birth_date);
        $tokenValue = $birth_date->format('Y-m-d');
      }
    }
    $this->setTokenValue($values, $cids, $token, $tokenValue);
  }

  private function age(&$values, $cids, $token) {
    $tokenValue = '';
    if ($this->getContactId()) {
      $birth_date = civicrm_api3('Contact', 'getvalue', array(
        'return' => 'birth_date',
        'id' => $this->getContactId()
      ));
      if ($birth_date) {
        $birth_date = new DateTime($birth_date);
        $tokenValue = $birth_date->diff(new DateTime())->y;
      }
    }
    $this->setTokenValue($values, $cids, $token, $tokenValue);
  }

  private function nationality(&$values, $cids, $token) {
    $passport = CRM_Tokens_Config_PassportInfo::singleton();
    $tokenValue = '';
    if ($this->getContactId()) {
      $tokenValue = civicrm_api3('Contact', 'getvalue', array(
        'return' => 'custom_' . $passport->nationality['id'],
        'id' => $this->getContactId()
      ));
    }
    $this->setTokenValue($values, $cids, $token, $tokenValue);
  }

  private function prefixToken(&$values, $cids, $token) {
    $tokenValue = '';
    if ($this->getContactId()) {
      $tokenValue = civicrm_api3('Contact', 'getvalue', array(
        'return' => 'individual_prefix',
        'id' => $this->getContactId()
      ));
    }
    $this->setTokenValue($values, $cids, $token, $tokenValue);
  }

  private function firstNameToken(&$values, $cids, $token) {
    $tokenValue = '';
    if ($this->getContactId()) {
      $tokenValue = civicrm_api3('Contact', 'getvalue', array(
        'return' => 'first_name',
        'id' => $this->getContactId()
      ));
    }
    $this->setTokenValue($values, $cids, $token, $tokenValue);
  }

  private function middleNameToken(&$values, $cids, $token) {
    $tokenValue = '';
    if ($this->getContactId()) {
      $tokenValue = civicrm_api3('Contact', 'getvalue', array(
        'return' => 'middle_name',
        'id' => $this->getContactId()
      ));
    }
    $this->setTokenValue($values, $cids, $token, $tokenValue);
  }

  private function lastNameToken(&$values, $cids, $token) {
    $tokenValue = '';
    if ($this->getContactId()) {
      $tokenValue = civicrm_api3('Contact', 'getvalue', array(
        'return' => 'last_name',
        'id' => $this->getContactId()
      ));
    }
    $this->setTokenValue($values, $cids, $token, $tokenValue);
  }

  private function displayNameToken(&$values, $cids, $token) {
    $name = '';
    if ($this->getContactId()) {
      $name = civicrm_api3('Contact', 'getvalue', array(
        'return' => 'display_name',
        'id' => $this->getContactId()
      ));
    }
    $this->setTokenValue($values, $cids, $token, $name);
  }

  private function homePhoneToken(&$values, $cids, $token) {
    $tokenValue = '';
    if ($this->getContactId() && isset($this->location_types['Home']) && isset($this->phone_types['Phone'])) {
      $phoneNumber = civicrm_api('Phone', 'get', array(
        'contact_id' => $this->getContactId(),
        'location_type_id' => $this->location_types['Home'],
        'phone_type_id' => $this->phone_types['Phone'],
        'version' => 3,
      ));

      if (!empty($phoneNumber['values'])) {
        $phoneFilter = $this->phoneFilter($phoneNumber['values']);
        if (!empty($phoneFilter['phone'])) {
          $tokenValue = $phoneFilter['phone'];
        }
      }

      $this->setTokenValue($values, $cids, $token, $tokenValue);
    }
  }

  private function homeMobileToken(&$values, $cids, $token) {
    $phone = '';
    if ($this->getContactId() && isset($this->location_types['Home']) && isset($this->phone_types['Mobile'])) {
      $phoneNumber = civicrm_api('Phone', 'get', array(
        'contact_id' => $this->getContactId(),
        'location_type_id' => $this->location_types['Home'],
        'phone_type_id' => $this->phone_types['Mobile'],
        'version' => 3,
      ));

      if (!empty($phoneNumber['values'])) {
        $phoneFilter = $this->phoneFilter($phoneNumber['values']);
        if (!empty($phoneFilter['phone'])) {
          $tokenValue = $phoneFilter['phone'];
        }
      }

      $this->setTokenValue($values, $cids, $token, $phone);
    }
  }

  private function homeFaxToken(&$values, $cids, $token) {
    $phone = '';
    if ($this->getContactId() && isset($this->location_types['Home']) && isset($this->phone_types['Fax'])) {
      $phoneNumber = civicrm_api('Phone', 'get', array(
        'contact_id' => $this->getContactId(),
        'location_type_id' => $this->location_types['Home'],
        'phone_type_id' => $this->phone_types['Fax'],
        'version' => 3,
      ));

      if (!empty($phoneNumber['values'])) {
        $phoneFilter = $this->phoneFilter($phoneNumber['values']);
        if (!empty($phoneFilter['phone'])) {
          $phone = $phoneFilter['phone'];
        }
      }

      $this->setTokenValue($values, $cids, $token, $phone);
    }
  }

  private function workPhoneToken(&$values, $cids, $token) {
    $tokenValue = '';
    if ($this->getContactId() && isset($this->location_types['Work']) && isset($this->phone_types['Phone'])) {
      $phoneNumber = civicrm_api('Phone', 'get', array(
        'contact_id' => $this->getContactId(),
        'location_type_id' => $this->location_types['Work'],
        'phone_type_id' => $this->phone_types['Phone'],
        'version' => 3,
      ));

      if (!empty($phoneNumber['values'])) {
        $phoneFilter = $this->phoneFilter($phoneNumber['values']);
        if (!empty($phoneFilter['phone'])) {
          $tokenValue = $phoneFilter['phone'];
        }
      }

      $this->setTokenValue($values, $cids, $token, $tokenValue);
    }
  }

  private function workMobileToken(&$values, $cids, $token) {
    $phone = '';
    if ($this->getContactId() && isset($this->location_types['Work']) && isset($this->phone_types['Mobile'])) {
      $phoneNumber = civicrm_api('Phone', 'get', array(
        'contact_id' => $this->getContactId(),
        'location_type_id' => $this->location_types['Work'],
        'phone_type_id' => $this->phone_types['Mobile'],
        'version' => 3,
      ));
      if (!empty($phoneNumber['values'])) {
        $phoneFilter = $this->phoneFilter($phoneNumber['values']);
        if (!empty($phoneFilter['phone'])) {
          $phone = $phoneFilter['phone'];
        }
      }

      $this->setTokenValue($values, $cids, $token, $phone);
    }
  }

  private function workFaxToken(&$values, $cids, $token) {
    $phone = '';
    if ($this->getContactId() && isset($this->location_types['Work']) && isset($this->phone_types['Fax'])) {
      $phoneNumber = civicrm_api('Phone', 'get', array(
        'contact_id' => $this->getContactId(),
        'location_type_id' => $this->location_types['Work'],
        'phone_type_id' => $this->phone_types['Fax'],
        'version' => 3,
      ));

      if (!empty($phoneNumber['values'])) {
        $phoneFilter = $this->phoneFilter($phoneNumber['values']);
        if (!empty($phoneFilter['phone'])) {
          $phone = $phoneFilter['phone'];
        }
      }

      $this->setTokenValue($values, $cids, $token, $phone);
    }
  }

  private function mainPhoneToken(&$values, $cids, $token) {
    $phone = '';
    if ($this->getContactId() && isset($this->location_types['Main']) && isset($this->phone_types['Phone'])) {
      $phoneNumber = civicrm_api('Phone', 'get', array(
        'contact_id' => $this->getContactId(),
        'location_type_id' => $this->location_types['Main'],
        'phone_type_id' => $this->phone_types['Phone'],
        'version' => 3,
      ));

      if (!empty($phoneNumber['values'])) {
        $phoneFilter = $this->phoneFilter($phoneNumber['values']);
        if (!empty($phoneFilter['phone'])) {
          $phone = $phoneFilter['phone'];
        }
      }

      $this->setTokenValue($values, $cids, $token, $phone);
    }
  }

  private function mainMobileToken(&$values, $cids, $token) {
    $phone = '';
    if ($this->getContactId() && isset($this->location_types['Main']) && isset($this->phone_types['Mobile'])) {
      $phoneNumber = civicrm_api('Phone', 'get', array(
        'contact_id' => $this->getContactId(),
        'location_type_id' => $this->location_types['Main'],
        'phone_type_id' => $this->phone_types['Mobile'],
        'version' => 3,
      ));

      if (!empty($phoneNumber['values'])) {
        $phoneFilter = $this->phoneFilter($phoneNumber['values']);
        if (!empty($phoneFilter['phone'])) {
          $phone = $phoneFilter['phone'];
        }
      }

      $this->setTokenValue($values, $cids, $token, $phone);
    }
  }

  private function mainFaxToken(&$values, $cids, $token) {
    $phone = '';
    if ($this->getContactId() && isset($this->location_types['Main']) && isset($this->phone_types['Fax'])) {
      $phoneNumber = civicrm_api('Phone', 'get', array(
        'contact_id' => $this->getContactId(),
        'location_type_id' => $this->location_types['Main'],
        'phone_type_id' => $this->phone_types['Fax'],
        'version' => 3,
      ));

      if (!empty($phoneNumber['values'])) {
        $phoneFilter = $this->phoneFilter($phoneNumber['values']);
        if (!empty($phoneFilter['phone'])) {
          $phone = $phoneFilter['phone'];
        }
      }

      $this->setTokenValue($values, $cids, $token, $phone);
    }
  }

  private function primaryPhoneToken(&$values, $cids, $token) {
    $phone = '';
    if ($this->getContactId()) {
      $phoneNumber = civicrm_api('Phone', 'get', array(
        'contact_id' => $this->getContactId(),
        'is_primary' => 1,
        'version' => 3,
      ));

      if (!empty($phoneNumber['values'])) {
        $phoneFilter = $this->phoneFilter($phoneNumber['values']);
        if (!empty($phoneFilter['phone'])) {
          $phone = $phoneFilter['phone'];
        }
      }

      $this->setTokenValue($values, $cids, $token, $phone);
    }
  }


  private function phoneFilter($phoneNumbers) {
    $phoneTypeGroupId = civicrm_api('OptionGroup', 'getsingle', array(
      'version' => 3,
      'name' => 'phone_type',
      'return' => 'id',
    ));
    $phoneTypeValues = civicrm_api('OptionValue', 'get', array(
      'version' => 3,
      'option_group_name' => 'phone_type',
    ));
    // build array phonetype value -> phonetype name
    $phoneTypes = array();
    if (!empty($phoneTypeValues['values'])) {
      foreach ($phoneTypeValues['values'] as $key => $value) {
        $phoneTypes[$value['value']] = $value['name'];
      }
    }
    // prioritise and single out the most important phonenumber: 1st primary > 2nd phone (not mobile) > 3rd others
    $phoneNumber = array('priority' => 4);
    foreach ($phoneNumbers as $no) {
      if ($no['is_primary'] == '1') {
        $no['priority'] = 1;
      }
      else {
        $type_id = $no['phone_type_id'];
        if (!array_key_exists($type_id, $phoneTypes)) {
          $no['priority'] = 3;
        }
        elseif ($phoneTypes[$type_id] == 'Phone') {
          $no['priority'] = 2;
        }
        else {
          $no['priority'] = 3;
        }
      }
      if ($no['priority'] < $phoneNumber['priority']) {
        $phoneNumber = $no;
      }
    }
    return ($phoneNumber);
  }

  private function workAddressToken(&$values, $cids, $token) {
    $formatedAddress = '';
    if ($this->getContactId() && isset($this->location_types['Work'])) {
      $address = civicrm_api('Address', 'getsingle', array(
        'contact_id' => $this->getContactId(),
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
    $tokenValue = $formatedAddress;

    $this->setTokenValue($values, $cids, $token, $tokenValue);
  }

  private function workStreetToken(&$values, $cids, $token) {
    $formatedAddress = '';
    if ($this->getContactId() && isset($this->location_types['Work'])) {
      $address = civicrm_api('Address', 'getsingle', array(
        'contact_id' => $this->getContactId(),
        'location_type_id' => $this->location_types['Work'],
        'version' => 3,
      ));

      if (!empty($address) && !empty($address['street_address'])) {
        $formatedAddress = $address['street_address'];
      }
    }
    $tokenValue = $formatedAddress;

    $this->setTokenValue($values, $cids, $token, $tokenValue);
  }

  private function workPostalCodeToken(&$values, $cids, $token) {
    $formatedAddress = '';
    if ($this->getContactId() && isset($this->location_types['Work'])) {
      $address = civicrm_api('Address', 'getsingle', array(
        'contact_id' => $this->getContactId(),
        'location_type_id' => $this->location_types['Work'],
        'version' => 3,
      ));

      if (!empty($address) && !empty($address['postal_code'])) {
        $formatedAddress = $address['postal_code'];
      }
    }
    $tokenValue = $formatedAddress;

    $this->setTokenValue($values, $cids, $token, $tokenValue);
  }

  private function workCityToken(&$values, $cids, $token) {
    $formatedAddress = '';
    if ($this->getContactId() && isset($this->location_types['Work'])) {
      $address = civicrm_api('Address', 'getsingle', array(
        'contact_id' => $this->getContactId(),
        'location_type_id' => $this->location_types['Work'],
        'version' => 3,
      ));

      if (!empty($address) && !empty($address['city'])) {
        $formatedAddress = $address['city'];
      }
    }
    $tokenValue = $formatedAddress;

    $this->setTokenValue($values, $cids, $token, $tokenValue);
  }

  private function workCountryToken(&$values, $cids, $token) {
    $formatedAddress = '';
    if ($this->getContactId() && isset($this->location_types['Work'])) {
      $address = civicrm_api('Address', 'getsingle', array(
        'contact_id' => $this->getContactId(),
        'location_type_id' => $this->location_types['Work'],
        'version' => 3,
      ));

      if (!empty($address) && !empty($address['id'])) {
        $country_id = $address['country_id'];
        $formatedAddress = CRM_Core_PseudoConstant::country($country_id);
      }
    }
    $tokenValue = $formatedAddress;

    $this->setTokenValue($values, $cids, $token, $tokenValue);
  }

  private function emailToken(&$values, $cids, $token) {
    $formattedEmail = '';
    if ($this->getContactId()) {
      $email = civicrm_api('Email', 'getsingle', array(
        'contact_id' => $this->getContactId(),
        'is_primary' => 1,
        'version' => 3,
      ));

      if (!empty($email) && !empty($email['email'])) {
        $formattedEmail = '<a href="mailto:' . $email['email'] . '">' . $email['email'] . '</a>';
      }
    }
    $tokenValue = $formattedEmail;

    $this->setTokenValue($values, $cids, $token, $tokenValue);
  }

  private function addressToken(&$values, $cids, $token) {
    $formatedAddress = '';
    if ($this->getContactId()) {
      $address = civicrm_api('Address', 'getsingle', array(
        'contact_id' => $this->getContactId(),
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
    $tokenValue = $formatedAddress;

    $this->setTokenValue($values, $cids, $token, $tokenValue);
  }

  private function addressStreetToken(&$values, $cids, $token) {
    $formatedAddress = '';
    if ($this->getContactId()) {
      $address = civicrm_api('Address', 'getsingle', array(
        'contact_id' => $this->getContactId(),
        'is_primary' => 1,
        'version' => 3,
      ));

      if (!empty($address) && !empty($address['street_address'])) {
        $formatedAddress = $address['street_address'];
      }
    }
    $tokenValue = $formatedAddress;

    $this->setTokenValue($values, $cids, $token, $tokenValue);
  }

  private function addressPostalCodeToken(&$values, $cids, $token) {
    $formatedAddress = '';
    if ($this->getContactId()) {
      $address = civicrm_api('Address', 'getsingle', array(
        'contact_id' => $this->getContactId(),
        'is_primary' => 1,
        'version' => 3,
      ));

      if (!empty($address) && !empty($address['postal_code'])) {
        $formatedAddress = $address['postal_code'];
      }
    }
    $tokenValue = $formatedAddress;

    $this->setTokenValue($values, $cids, $token, $tokenValue);
  }

  private function addressCityToken(&$values, $cids, $token) {
    $formatedAddress = '';
    if ($this->getContactId()) {
      $address = civicrm_api('Address', 'getsingle', array(
        'contact_id' => $this->getContactId(),
        'is_primary' => 1,
        'version' => 3,
      ));

      if (!empty($address) && !empty($address['city'])) {
        $formatedAddress = $address['city'];
      }
    }
    $tokenValue = $formatedAddress;

    $this->setTokenValue($values, $cids, $token, $tokenValue);
  }

  private function addressCountryToken(&$values, $cids, $token) {
    $formatedAddress = '';
    if ($this->getContactId()) {
      $address = civicrm_api('Address', 'getsingle', array(
        'contact_id' => $this->getContactId(),
        'is_primary' => 1,
        'version' => 3,
      ));

      if (!empty($address) && !empty($address['id'])) {
        $country_id = $address['country_id'];
        $formatedAddress = CRM_Core_PseudoConstant::country($country_id);
      }
    }
    $tokenValue = $formatedAddress;

    $this->setTokenValue($values, $cids, $token, $tokenValue);
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

    if ($this->getContactId()) {
      $params = array(
        'version' => 3,
        'sequential' => 1,
        'contact_id' => $this->getContactId(),
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

    if ($this->getContactId()) {
      $params = array(
        'version' => 3,
        'sequential' => 1,
        'contact_id' => $this->getContactId(),
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

    if ($this->getContactId()) {
      $params = array(
        'version' => 3,
        'sequential' => 1,
        'contact_id' => $this->getContactId(),
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


}