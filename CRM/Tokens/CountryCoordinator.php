<?php

class CRM_Tokens_CountryCoordinator extends CRM_Tokens_CaseRelationship {

  public static function tokens(&$tokens, $token_name, $token_label) {
    CRM_Tokens_CaseRelationship::tokens($tokens, $token_name, $token_label);
    $tokens[$token_name][$token_name.'.country'] = ts('Country of '.$token_label);
  }

  public function tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null) {
    parent::tokenValues($values, $cids, $job, $tokens, $context);
    $this->countryToken($values, $cids, 'country');
  }

  private function countryToken(&$values, $cids, $token) {
    $country = '';
    if ($this->contact_id) {
      $dao = CRM_Core_DAO::executeQuery("SELECT c.*
                                        FROM civicrm_relationship r
                                        INNER JOIN civicrm_contact c on r.contact_id_a = c.id
                                        where r.relationship_type_id = %2
                                        AND r.contact_id_b = %1
                                        AND r.case_id is NULL
                                        AND
                                        (`start_date` IS NULL OR DATE(`start_date`) <= DATE(NOW()))
                                        AND
                                        (`end_date` IS NULL OR DATE(`end_date`) >= DATE(NOW()))
                                        ", array(
                                          1=>array($this->contact_id, 'Integer'),
                                          2=>array($this->relationship_type['id'], 'Integer'),
                                        ));
      while($dao->fetch()) {
        if (strlen($country)) {
          $country .= ', ';
        }
        $country .= $dao->display_name;
      }
    }

    $this->setTokenValue($values, $cids, $token, $country);
  }

}