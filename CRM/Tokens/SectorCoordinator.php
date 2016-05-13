<?php

class CRM_Tokens_SectorCoordinator extends CRM_Tokens_CaseRelationship {

  public static function tokens(&$tokens, $token_name, $token_label) {
    CRM_Tokens_CaseRelationship::tokens($tokens, $token_name, $token_label);
    $tokens[$token_name][$token_name.'.sector'] = ts('Sector of '.$token_label);
  }

  public function tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null) {
    parent::tokenValues($values, $cids, $job, $tokens, $context);
    $this->sectorToken($values, $cids, 'sector');
  }

  private function sectorToken(&$values, $cids, $token) {
    $sector = '';
    if ($this->contact_id) {
      $dao = CRM_Core_DAO::executeQuery("SELECT t.*
                                        FROM `civicrm_tag_enhanced` `e`
                                        INNER JOIN `civicrm_tag` `t` ON `e`.`tag_id` = `t`.`id`
                                        WHERE `e`.`coordinator_id` = %1
                                        AND
                                        (`start_date` IS NULL OR DATE(`start_date`) <= DATE(NOW()))
                                        AND
                                        (`end_date` IS NULL OR DATE(`end_date`) >= DATE(NOW()))
                                        ", array(1 => array($this->contact_id, 'Integer')));
      while($dao->fetch()) {
        if (strlen($sector)) {
          $sector .= ', ';
        }
        $sector .= $dao->name;
      }
    }

    $this->setTokenValue($values, $cids, $token, $sector);
  }

}