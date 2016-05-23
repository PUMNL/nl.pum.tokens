<?php

class CRM_Tokens_SectorCoordinator extends CRM_Tokens_CaseRelationship {

  private $sc_role;

  public function __construct($relationship_type_name_a_b, $token_name, $token_label, $case_id = NULL) {
    parent::__construct($relationship_type_name_a_b, $token_name, $token_label);

    $segment_roles = civicrm_api3('OptionGroup', 'getvalue', array('name' => 'civicoop_segment_role', 'return' => 'id'));
    $this->sc_role = civicrm_api3('OptionValue', 'getvalue', array('name' => 'sector_coordinator', 'option_group_id' => $segment_roles, 'return' => 'value'));
  }

  public static function tokens(&$tokens, $token_name, $token_label) {
    CRM_Tokens_CaseRelationship::tokens($tokens, $token_name, $token_label);
    $tokens[$token_name][$token_name.'.sector'] = ts('Sector of '.$token_label);
  }

  public function tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null) {
    parent::tokenValues($values, $cids, $job, $tokens, $context);
    if ($this->isTokenInTokens($tokens, 'sector')) {
      $this->sectorToken($values, $cids, 'sector');
    }
  }

  private function sectorToken(&$values, $cids, $token) {
    $sector = '';
    if ($this->contact_id) {
      $contact_segments = civicrm_api3('ContactSegment', 'get', array('role_value' => $this->sc_role, 'is_active' => true));
      foreach($contact_segments['values'] as $contact_segment) {
        $segment = civicrm_api3('Segment', 'getsingle', array('id' => $contact_segment['segment_id']));
        if (strlen($sector)) {
          $sector .= ', ';
        }
        $sector .= $segment['label'];
      }
    }

    $this->setTokenValue($values, $cids, $token, $sector);
  }

}