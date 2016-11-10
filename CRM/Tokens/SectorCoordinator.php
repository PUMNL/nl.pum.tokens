<?php

class CRM_Tokens_SectorCoordinator extends CRM_Tokens_CaseRelationship {

  public function __construct($relationship_type_name_a_b, $token_name, $token_label, $values, $case_id = NULL) {
    parent::__construct($relationship_type_name_a_b, $token_name, $token_label, $values, $case_id);

    $segment_roles = civicrm_api3('OptionGroup', 'getvalue', array('name' => 'civicoop_segment_role', 'return' => 'id'));

    if (empty($case_id)) {
      $this->case_id = CRM_Tokens_CaseId::getCaseId();
    } elseif($case_id=='parent') {
      $this->case_id = CRM_Tokens_CaseId::getParentCaseId();
    } else {
      $this->case_id = $case_id;
    }
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

    $sc_role_id = civicrm_api('RelationshipType', 'getvalue', array('version' => 3, 'sequential' => 1, 'name_a_b' => 'Sector Coordinator', 'return' => 'id'));

    if (!empty($this->case_id) && !empty($sc_role_id)) {
      $qry = "SELECT sg.label AS 'label'
              FROM civicrm_contact_segment cs
                LEFT JOIN civicrm_contact ct ON ct.id = cs.contact_id
                LEFT JOIN civicrm_segment sg ON sg.id = cs.segment_id
              WHERE cs.contact_id = (SELECT contact_id_b FROM civicrm_relationship WHERE case_id = %1 AND relationship_type_id = %2 AND is_active = '1' LIMIT 1) AND cs.is_main = '1' LIMIT 1";

      $dao2 = CRM_Core_DAO::executeQuery($qry, array(1=>array($this->case_id, 'Integer'),
                                                     2=>array($sc_role_id, 'Integer')));

      while($dao2->fetch()) {
        if (strlen($dao2->label)) {
          $this->setTokenValue($values, $cids, $token, $dao2->label);
        }
      }
    }
  }
}