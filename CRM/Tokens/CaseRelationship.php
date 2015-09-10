<?php

class CRM_Tokens_CaseRelationship {

  protected $relationship_type;

  protected $token_name;

  protected $token_label;

  protected $case_id;

  protected $contact_id = false;

  protected $location_types = array();
  
  protected $salutations;
  
  protected $gender;

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
    
    $this->get_salutations();
    $this->get_gender();
  }
  
  private function get_salutations() {
	$salutations = array();
	
	try {
		$params = array(
		  'version' => 3,
		  'sequential' => 1,
		  'option_group_name' => 'tokens_salutation',
		);
		
		$result = civicrm_api('OptionValue', 'get', $params);
		
		foreach ($result['values'] as $key => $value) {
			$v = explode('_',$value['name']);	//$v[0] = "mrs" $v[1]=en
			 
			if (!array_key_exists($v[1],$salutations)) {
				$salutations[$v[1]] = array();	
			}
			
			$salutations[$v[1]][$v[0]] = $value['value'];	
		}		
	} catch (Exception $e) {
	
	}
	
	$this->salutations = $salutations;
  }
  
  private function get_gender() {
	$gender = array();
	
	try {
		$params = array(
		  'version' => 3,
		  'sequential' => 1,
		  'option_group_name' => 'gender',
		);
		
		$result = civicrm_api('OptionValue', 'get', $params);
		
		foreach ($result['values'] as $key => $value) {
			if ($value['name'] == 'Male') {
				$gender[$value['value']] = "mr";
			} elseif ($value['name'] == 'Female') {
				$gender[$value['value']] = "mrs";
			}	
		}		
	} catch (Exception $e) {
	
	}
	
	$this->gender = $gender;
  }
  
  public function tokens(&$tokens) {
    $t = array();
    $t[$this->token_name.'.address'] = ts('Address of '.$this->token_label);
	$t[$this->token_name.'.address_street'] = ts('Address street of '.$this->token_label);
	$t[$this->token_name.'.address_postalcode'] = ts('Address postalcode of '.$this->token_label);
	$t[$this->token_name.'.address_city'] = ts('Address city of '.$this->token_label);
	$t[$this->token_name.'.address_country'] = ts('Address country of '.$this->token_label);
    $t[$this->token_name.'.work_address'] = ts('Work address of '.$this->token_label);
	$t[$this->token_name.'.work_street'] = ts('Work address street of '.$this->token_label);
	$t[$this->token_name.'.work_postalcode'] = ts('Work address postalcode of '.$this->token_label);
	$t[$this->token_name.'.work_city'] = ts('Work address city of '.$this->token_label);
	$t[$this->token_name.'.work_country'] = ts('Work address country of '.$this->token_label);
    $t[$this->token_name.'.display_name'] = ts('Display name of '.$this->token_label);
    $t[$this->token_name.'.email'] = ts('E-mail address of '.$this->token_label);
    $t[$this->token_name.'.work_phone'] = ts('Work phone number of '.$this->token_label);
    $t[$this->token_name.'.passport_first_name'] = ts('Passport firstname of '.$this->token_label);
    $t[$this->token_name.'.passport_last_name'] = ts('Passport lastname of '.$this->token_label);
    $t[$this->token_name.'.passport_number'] = ts('Passport number of '.$this->token_label);
    $t[$this->token_name.'.passport_valid'] = ts('Passport valid date of '.$this->token_label);
	$t[$this->token_name.'.passport_issue_date'] = ts('Passport issue date of '.$this->token_label);
	$t[$this->token_name.'.passport_issue_place'] = ts('Passport issue place of '.$this->token_label);
    $t[$this->token_name.'.passport_partner_name'] = ts('Passport partner name of '.$this->token_label);
    $t[$this->token_name.'.nationlity'] = ts('Nationality of '.$this->token_label);
    $t[$this->token_name.'.prefix'] = ts('Prefix of '.$this->token_label);
    $t[$this->token_name.'.first_name'] = ts('First name of '.$this->token_label);
    $t[$this->token_name.'.middle_name'] = ts('Middle name of '.$this->token_label);
    $t[$this->token_name.'.last_name'] = ts('Last name of '.$this->token_label);
    $t[$this->token_name.'.birth_date'] = ts('Birth date of '.$this->token_label);
    $t[$this->token_name.'.age'] = ts('Age of '.$this->token_label);
	$t[$this->token_name.'.home_phone'] = ts('Home phone number of '.$this->token_label);
	if (!empty($this->salutations)) {
		foreach ($this->salutations as $key => $value) {
			$t[$this->token_name.'.salutation_'.$key] = ts('Salutation ('.$key.') for '.$this->token_label);		
		}
	}
    $tokens[$this->token_name] = $t;
    
  }

  public function tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null) {
    if ($this->checkToken($tokens, 'address')) {
      $this->addressToken($values, $cids, 'address');
    }
	if ($this->checkToken($tokens, 'address_street')) {
      $this->addressStreetToken($values, $cids, 'address_street');
    }
	if ($this->checkToken($tokens, 'address_postalcode')) {
      $this->addressPostalCodeToken($values, $cids, 'address_postalcode');
    }
	if ($this->checkToken($tokens, 'address_city')) {
      $this->addressCityToken($values, $cids, 'address_city');
    }
	if ($this->checkToken($tokens, 'address_country')) {
      $this->addressCountryToken($values, $cids, 'address_country');
    }
    if ($this->checkToken($tokens, 'work_address')) {
      $this->workAddressToken($values, $cids, 'work_address');
    }
	if ($this->checkToken($tokens, 'work_street')) {
      $this->workStreetToken($values, $cids, 'work_street');
    }
	if ($this->checkToken($tokens, 'work_postalcode')) {
      $this->workPostalCodeToken($values, $cids, 'work_postalcode');
    }
	if ($this->checkToken($tokens, 'work_city')) {
      $this->workCityToken($values, $cids, 'work_city');
    }
	if ($this->checkToken($tokens, 'work_country')) {
      $this->workCountryToken($values, $cids, 'work_country');
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
	if ($this->checkToken($tokens, 'passport_issue_date')) {
      $this->passportIssueDate($values, $cids, 'passport_issue_date');
    }
	if ($this->checkToken($tokens, 'passport_issue_place')) {
      $this->passportIssuePlace($values, $cids, 'passport_issue_place');
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
    
    if (!empty($this->salutations)) {
		foreach ($this->salutations as $key => $value) {
			if ($this->checkToken($tokens, 'salutation_'.$key)) {
				$this->saluationToken($values, $cids, 'salutation_'.$key, $key);
			}		
		}
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
  
  private function passportIssueDate(&$values, $cids, $token) {
    $passport = CRM_Tokens_Config_PassportInfo::singleton();
    $issued = '';
    if ($this->contact_id) {
      $passport_issued = civicrm_api3('Contact', 'getvalue', array('return' => 'custom_'.$passport->passport_issue_date['id'], 'id' => $this->contact_id));
      if ($passport_issued) {
        $validDate = new DateTime($passport_issued);
        $issued = $validDate->format('Y-m-d');
      }
    }
    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $issued;
    }
  }
  
  private function passportIssuePlace(&$values, $cids, $token) {
    $passport = CRM_Tokens_Config_PassportInfo::singleton();
    $issued = '';
    if ($this->contact_id) {
      $issued = civicrm_api3('Contact', 'getvalue', array('return' => 'custom_'.$passport->passport_issue_place['id'], 'id' => $this->contact_id));
    }
    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $issued;
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
	    $phoneFilter = $this->phoneFilter($phoneNumber['values']);
	    if (!empty($phoneFilter['phone'])) {
		  $phone = $phoneFilter['phone'];
		}
      }
    }

    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $phone;
    }
  }
  
  private function workPhoneToken(&$values, $cids, $token) {
    $phone = '';
    if ($this->contact_id) {
      $phoneNumber = civicrm_api('Phone', 'get', array(
        'contact_id' => $this->contact_id,
        'location_type_id' => $this->location_types['Work'],
        'version' => 3,
      ));

      if (!empty($phoneNumber['values'])) {
	    $phoneFilter = $this->phoneFilter($phoneNumber['values']);
	    if (!empty($phoneFilter['phone'])) {
		  $phone = $phoneFilter['phone'];
		}
      }
    }

    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $phone;
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
      'option_group_id' => 35,
	));
	// build array phonetype value -> phonetype name
	$phoneTypes = array();
	if(!empty($phoneTypeValues['values'])) {
	  foreach($phoneTypeValues['values'] as $key=>$value) {
  		$phoneTypes[$value['value']] = $value['name'];
	  }
	}
	// prioritise and single out the most important phonenumber: 1st primary > 2nd phone (not mobile) > 3rd others
	$phoneNumber = array('priority'=>4);
	foreach($phoneNumbers as $no) {
      if($no['is_primary']=='1') {
        $no['priority'] = 1;
      } else {
	    $type_id = $no['phone_type_id'];
		if (!array_key_exists($type_id, $phoneTypes)) {
			$no['priority'] = 3;
		} elseif ($phoneTypes[$type_id]=='Phone') {
			$no['priority'] = 2;
		} else {
			$no['priority'] = 3;
		}
	  }
	  if ($no['priority']<$phoneNumber['priority']) {
		$phoneNumber = $no;
	  }
	}
	return($phoneNumber);
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
  
  private function workStreetToken(&$values, $cids, $token) {
    $formatedAddress = '';
    if ($this->contact_id) {
      $address = civicrm_api('Address', 'getsingle', array(
        'contact_id' => $this->contact_id,
        'location_type_id' => $this->location_types['Work'],
        'version' => 3,
      ));

      if (!empty($address) && !empty($address['street_address'])) {
        $formatedAddress = $address['street_address'];
      }
    }

    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $formatedAddress;
    }
  }
  
  private function workPostalCodeToken(&$values, $cids, $token) {
    $formatedAddress = '';
    if ($this->contact_id) {
      $address = civicrm_api('Address', 'getsingle', array(
        'contact_id' => $this->contact_id,
        'location_type_id' => $this->location_types['Work'],
        'version' => 3,
      ));

      if (!empty($address) && !empty($address['postal_code'])) {
        $formatedAddress = $address['postal_code'];
      }
    }

    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $formatedAddress;
    }
  }
  
  private function workCityToken(&$values, $cids, $token) {
    $formatedAddress = '';
    if ($this->contact_id) {
      $address = civicrm_api('Address', 'getsingle', array(
        'contact_id' => $this->contact_id,
        'location_type_id' => $this->location_types['Work'],
        'version' => 3,
      ));

      if (!empty($address) && !empty($address['city'])) {
        $formatedAddress = $address['city'];
      }
    }

    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $formatedAddress;
    }
  }
  
  private function workCountryToken(&$values, $cids, $token) {
    $formatedAddress = '';
    if ($this->contact_id) {
      $address = civicrm_api('Address', 'getsingle', array(
        'contact_id' => $this->contact_id,
        'location_type_id' => $this->location_types['Work'],
        'version' => 3,
      ));

      if (!empty($address) && !empty($address['id'])) {
        $country_id = $address['country_id'];
        $formatedAddress = CRM_Core_PseudoConstant::country($country_id);
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
  
  private function addressStreetToken(&$values, $cids, $token) {
    $formatedAddress = '';
    if ($this->contact_id) {
      $address = civicrm_api('Address', 'getsingle', array(
        'contact_id' => $this->contact_id,
        'is_primary' => 1,
        'version' => 3,
      ));

      if (!empty($address) && !empty($address['street_address'])) {
        $formatedAddress = $address['street_address'];
      }
    }

    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $formatedAddress;
    }
  }
  
  private function addressPostalCodeToken(&$values, $cids, $token) {
    $formatedAddress = '';
    if ($this->contact_id) {
      $address = civicrm_api('Address', 'getsingle', array(
        'contact_id' => $this->contact_id,
        'is_primary' => 1,
        'version' => 3,
      ));

      if (!empty($address) && !empty($address['postal_code'])) {
        $formatedAddress = $address['postal_code'];
      }
    }

    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $formatedAddress;
    }
  }
  
  private function addressCityToken(&$values, $cids, $token) {
    $formatedAddress = '';
    if ($this->contact_id) {
      $address = civicrm_api('Address', 'getsingle', array(
        'contact_id' => $this->contact_id,
        'is_primary' => 1,
        'version' => 3,
      ));

      if (!empty($address) && !empty($address['city'])) {
        $formatedAddress = $address['city'];
      }
    }

    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $formatedAddress;
    }
  }
  
  private function addressCountryToken(&$values, $cids, $token) {
    $formatedAddress = '';
    if ($this->contact_id) {
      $address = civicrm_api('Address', 'getsingle', array(
        'contact_id' => $this->contact_id,
        'is_primary' => 1,
        'version' => 3,
      ));

      if (!empty($address) && !empty($address['id'])) {
        $country_id = $address['country_id'];
        $formatedAddress = CRM_Core_PseudoConstant::country($country_id);
      }
    }

    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $formatedAddress;
    }
  }

  private function saluationToken(&$values, $cids, $token, $lang) {
    /**
	 *	$lang = 'en' | 'fr' | 'es' | 'nl' | ... (option group 'tokens_salutation')
	 *	$this->salutations = array(
	 *		'en' = array(
	 *			'mr' => 'Mr.,
	 *			'mrs' = 'Mrs.,
	 *		),
	 *		'fr' = array(
	 *			'mr' => 'M.',
	 *			'mrs' => 'Mme.',
	 *		),
	 *		...
	 *	)
	 *	$this->gender = array(
	 *		1 => 'mrs',
	 *		2 => 'mr',
	 *	) // (option group 'gender')
	 */
	
  	if ($this->contact_id) {
	  	$params = array(
		  'version' => 3,
		  'sequential' => 1,
		  'contact_id' => $this->contact_id,
		);
		$result = civicrm_api('Contact', 'get', $params);

		$prefix = 'mr';
		if (!empty($result['values'][0]['gender_id'])) {
			$gender_id = $result['values'][0]['gender_id'];

			if (array_key_exists($gender_id, $this->gender)) {
				$prefix = $this->gender[$gender_id]; 
			}
		}

		foreach($cids as $cid) {
      		$values[$cid][$this->token_name.'.'.$token] = $this->salutations[$lang][$prefix];
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