<?php

class CRM_Tokens_Config_PassportInfo {

  /**
   * @var CRM_Tokens_Config_PassportInfo
   */
  private static $singleton;

  public $name;

  public $last_name;

  public $passport_number;

  public $passport_valid;

  public $partner_name;

  public $nationality;


  private function __construct() {
    $passport_info = civicrm_api3('CustomGroup', 'getsingle', array('name' => 'Passport_Information'));
    $nationality_group = civicrm_api3('CustomGroup', 'getsingle', array('name' => 'Nationality'));

    $this->name = civicrm_api3('CustomField', 'getsingle', array('name' => 'Passport_Name', 'custom_group_id' => $passport_info['id']));
    $this->last_name = civicrm_api3('CustomField', 'getsingle', array('name' => 'Passport_Name_Last_Name', 'custom_group_id' => $passport_info['id']));
    $this->passport_number = civicrm_api3('CustomField', 'getsingle', array('name' => 'Passport_Number', 'custom_group_id' => $passport_info['id']));
    $this->passport_valid = civicrm_api3('CustomField', 'getsingle', array('name' => 'Passport_Valid_until', 'custom_group_id' => $passport_info['id']));
    $this->partner_name = civicrm_api3('CustomField', 'getsingle', array('name' => 'Passport_Name_Partner_Last_Name', 'custom_group_id' => $passport_info['id']));
    $this->nationality = civicrm_api3('CustomField', 'getsingle', array('name' => 'Nationality', 'custom_group_id' => $nationality_group['id']));
  }


  /**
   * @return CRM_Tokens_Config_PassportInfo
   */
  public static function singleton() {
    if (!self::$singleton) {
      self::$singleton = new CRM_Tokens_Config_PassportInfo();
    }
    return self::$singleton;
  }

}