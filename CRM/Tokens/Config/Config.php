<?php
/**
 * @author Jaap Jansma (CiviCooP) <jaap.jansma@civicoop.org>
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 */

class CRM_Tokens_Config_Config {

  private static $singleton;

  protected $locationTypes;

  protected $phoneTypes;

  protected $salutations;

  protected $salutationsFull;

  protected $salutationsGreeting;

  protected $gender;

  private function __construct() {
    $locTypes = civicrm_api3('LocationType', 'get', array());
    foreach($locTypes['values'] as $locType) {
      $this->locationTypes[$locType['name']] = $locType['id'];
    }

    $phoneTypes = civicrm_api3('OptionValue', 'get', array(
        'version' => 3,
        'sequential' => 1,
        'option_group_name' => 'phone_type')
    );
    foreach($phoneTypes['values'] as $phoneType) {
      $this->phoneTypes[$phoneType['name']] = $phoneType['value'];
    }

    $this->loadGender();
    $this->loadSalutations();
    $this->loadSalutationsFull();
    $this->loadSalutationsGreeting();
  }

  /**
   * @return CRM_Tokens_Config_Config
   */
  public static function singleton() {
    if (!self::$singleton) {
      self::$singleton = new CRM_Tokens_Config_Config();
    }
    return self::$singleton;
  }

  public function getLocationTypes() {
    return $this->locationTypes;
  }

  public function getPhoneTypes() {
    return $this->phoneTypes;
  }

  public function getGender() {
    return $this->gender;
  }

  public function getSalutations() {
    return $this->salutations;
  }

  public function getSalutionsFull() {
    return $this->salutationsFull;
  }

  public function getSalutionsGreeting() {
    return $this->salutationsGreeting;
  }


  protected function loadSalutations() {
    $salutations = array();

    try {
      $params = array(
        'version' => 3,
        'sequential' => 1,
        'option_group_name' => 'tokens_salutation',
      );

      $result = civicrm_api('OptionValue', 'get', $params);

      foreach ($result['values'] as $key => $value) {
        $v = explode('_', $value['name']);  //$v[0] = "mrs" $v[1]=en

        if (!array_key_exists($v[1], $salutations)) {
          $salutations[$v[1]] = array();
        }

        $salutations[$v[1]][$v[0]] = $value['value'];
      }
    } catch (Exception $e) {

    }

    $this->salutations = $salutations;
  }

  protected function loadSalutationsFull() {
    $salutations_full = array();

    try {
      $params = array(
        'version' => 3,
        'sequential' => 1,
        'option_group_name' => 'tokens_salutation_full',
      );

      $result = civicrm_api('OptionValue', 'get', $params);

      foreach ($result['values'] as $key => $value) {
        $v = explode('_', $value['name']);  //$v[0] = "mrs" $v[1]=en

        if (!array_key_exists($v[1], $salutations_full)) {
          $salutations_full[$v[1]] = array();
        }

        $salutations_full[$v[1]][$v[0]] = $value['value'];
      }
    } catch (Exception $e) {

    }

    $this->salutationsFull = $salutations_full;
  }

  protected function loadSalutationsGreeting() {
    $salutations_greeting = array();

    try {
      $params = array(
        'version' => 3,
        'sequential' => 1,
        'option_group_name' => 'tokens_salutation_greeting',
      );

      $result = civicrm_api('OptionValue', 'get', $params);

      foreach ($result['values'] as $key => $value) {
        $v = explode('_', $value['name']);  //$v[0] = "mrs" $v[1]=en

        if (!array_key_exists($v[1], $salutations_greeting)) {
          $salutations_greeting[$v[1]] = array();
        }

        $salutations_greeting[$v[1]][$v[0]] = $value['value'];
      }
    } catch (Exception $e) {

    }

    $this->salutationsGreeting = $salutations_greeting;
  }

  protected function loadGender() {
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
        }
        elseif ($value['name'] == 'Female') {
          $gender[$value['value']] = "mrs";
        }
      }
    } catch (Exception $e) {

    }

    $this->gender = $gender;
  }

}