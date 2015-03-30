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
        }
    }

    public static function getCaseId() {
        return self::$caseId;
    }


}