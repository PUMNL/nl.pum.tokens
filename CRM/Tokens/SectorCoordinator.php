<?php

class CRM_Tokens_SectorCoordinator extends CRM_Tokens_CaseRelationship {

  public function tokens(&$tokens) {
    parent::tokens($tokens);
    $tokens[$this->token_name][$this->token_name.'.sector'] = t('Sector of '.$this->token_label);
  }

  public function tokenValues(&$values, $cids, $job = null, $tokens = array(), $context = null) {
    parent::tokenValues($values, $cids, $job, $tokens, $context);
    if ($this->checkToken($tokens, 'sector')) {
      $this->sectorToken($values, $cids, 'sector');
    }
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

    foreach($cids as $cid) {
      $values[$cid][$this->token_name.'.'.$token] = $sector;
    }
  }

}