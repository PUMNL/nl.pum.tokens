<?php

/**
 * Class CRM_Tokens_CaseId
 *
 * This class is used for storing the CaseID from a buildForm hook
 * so that this case ID could be used in further processing
 * e.g. in the hook for tokens
 *
 * The problem is that in the hook_tokenValues we don't know
 * from which case an send e-mail or create PDF letter is executed
 * we only know that when we know which form is used
 * and that we know in the buildForm hook.
 *
 * So the solution is to retrieve the caseID from the form in a buildForm hook
 * and eventually use this class in tokenValues to determine which case.
 */
class CRM_Tokens_CaseId {

    private static $caseId = false;
    private static $parentCaseId = false;


    /**
     * Check if the form has a case id and if so store the case id localy
     * so we can use the case when we parse tokens to values
     *
     * @param $formName
     * @param $form
     */
    public static function buildForm($formName, &$form) {
        if (!empty($form->_caseId)) {
            self::$caseId = $form->_caseId;
            // now retrieve the parentCaseId as well
            self::$parentCaseId = self::fetchParentCaseId(self::$caseId);
        }
    }

    public static function getCaseId() {
        return self::$caseId;
    }
	
	public static function getParentCaseId() {
        return self::$parentCaseId;
    }

	public static function fetchParentCaseId($childCaseId) {
        if(!$childCaseId) {
            return false;
        }
		// retrieve custom table and - columns
        $customGroup = civicrm_api3('CustomGroup', 'getsingle', array('name' => 'travel_parent'));
        $customFields = civicrm_api3('CustomField', 'get', array('custom_group_id' => $customGroup['id']));
        $cols = array();
        foreach($customFields['values'] as $fld) {
            $cols[] = $fld['column_name'] . ' AS ' . $fld['name'];
        }
        // build and execute query to retrieve parentCaseId from custom table
        $sql = 'SELECT ' . implode(', ', $cols) . ' FROM ' . $customGroup['table_name'] . ' WHERE entity_id=' . $childCaseId;
        $dao = CRM_Core_DAO::executeQuery($sql);
        if ($dao->fetch()) {
            // data retrieved: return parentCaseId
            return $dao->case_id;
        } else {
            return false;
        }
    }
}