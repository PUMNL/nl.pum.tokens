<?php

class Tokens_OptionGroup {
	
	/*
	 * returns the definitions for dsa option groups
	 */
	static function required() {
		return array(
			array(
				'group_name'	=>	'tokens_salutation',
				'group_label'	=>	'Tokens Salutation',
				'enable_level'	=>	'group',
				'values'		=>	array(
					array(
						'label'			=> 'mr_en',
						'name'			=> 'mr_en',
						'value'			=> 'Mr.',
						'weight'		=> 10,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'mrs_en',
						'name'			=> 'mrs_en',
						'value'			=> 'Mrs.',
						'weight'		=> 20,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'mr_nl',
						'name'			=> 'mr_nl',
						'value'			=> 'Dhr.',
						'weight'		=> 30,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'mrs_nl',
						'name'			=> 'mrs_nl',
						'value'			=> 'Mw.',
						'weight'		=> 40,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'mr_fr',
						'name'			=> 'mr_fr',
						'value'			=> 'M.',
						'weight'		=> 50,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'mrs_fr',
						'name'			=> 'mrs_fr',
						'value'			=> 'Mme.',
						'weight'		=> 60,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'mr_es',
						'name'			=> 'mr_es',
						'value'			=> 'Sr.',
						'weight'		=> 70,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'mrs_es',
						'name'			=> 'mrs_es',
						'value'			=> 'Sra.',
						'weight'		=> 80,
						'description'	=> '',
						'default'		=> FALSE,
					),
				),
			),
			array(
				'group_name'	=>	'tokens_salutation_full',
				'group_label'	=>	'Tokens Salutation Full',
				'enable_level'	=>	'group',
				'values'		=>	array(
					array(
						'label'			=> 'mr_en',
						'name'			=> 'mr_en',
						'value'			=> 'sir',
						'weight'		=> 10,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'mrs_en',
						'name'			=> 'mrs_en',
						'value'			=> 'madame',
						'weight'		=> 20,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'mr_nl',
						'name'			=> 'mr_nl',
						'value'			=> 'heer',
						'weight'		=> 30,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'mrs_nl',
						'name'			=> 'mrs_nl',
						'value'			=> 'mevrouw',
						'weight'		=> 40,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'mr_fr',
						'name'			=> 'mr_fr',
						'value'			=> 'monsieur',
						'weight'		=> 50,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'mrs_fr',
						'name'			=> 'mrs_fr',
						'value'			=> 'madame',
						'weight'		=> 60,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'mr_es',
						'name'			=> 'mr_es',
						'value'			=> 'se'.html_entity_decode("&ntilde;",ENT_COMPAT,"UTF-8").'or',
						'weight'		=> 70,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'mrs_es',
						'name'			=> 'mrs_es',
						'value'			=> 'se'.html_entity_decode("&ntilde;",ENT_COMPAT,"UTF-8").'ora',
						'weight'		=> 80,
						'description'	=> '',
						'default'		=> FALSE,
					),
				),
			),
			array(
				'group_name'	=>	'tokens_salutation_greeting',
				'group_label'	=>	'Tokens Salutation Greeting',
				'enable_level'	=>	'group',
				'values'		=>	array(
					array(
						'label'			=> 'mr_en',
						'name'			=> 'mr_en',
						'value'			=> 'Dear',
						'weight'		=> 10,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'mrs_en',
						'name'			=> 'mrs_en',
						'value'			=> 'Dear',
						'weight'		=> 20,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'mr_nl',
						'name'			=> 'mr_nl',
						'value'			=> 'Geachte',
						'weight'		=> 30,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'mrs_nl',
						'name'			=> 'mrs_nl',
						'value'			=> 'Geachte',
						'weight'		=> 40,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'mr_fr',
						'name'			=> 'mr_fr',
						'value'			=> 'Cher',
						'weight'		=> 50,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'mrs_fr',
						'name'			=> 'mrs_fr',
						'value'			=> 'Ch'.html_entity_decode("&eacute;",ENT_COMPAT,"UTF-8").'re',
						'weight'		=> 60,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'mr_es',
						'name'			=> 'mr_es',
						'value'			=> 'Estimado',
						'weight'		=> 70,
						'description'	=> '',
						'default'		=> FALSE,
					),
					array(
						'label'			=> 'mrs_es',
						'name'			=> 'mrs_es',
						'value'			=> 'Estimada',
						'weight'		=> 80,
						'description'	=> '',
						'default'		=> FALSE,
					),
				),
			),
		);
	}
	
	/*
	 * handler for hook_civicrm_install
	 * option groups are built in 2 cycles: (1) the groups themselves and (2) their values
	 * attempts to build the values in the same cycle as the groups often failed, possibly due to transactional processing
	 */
	static function install() {
		$created = array();
		$message = '';
		$required = self::required();
		
		// cycle 1:  create option groups
		foreach ($required as $optionGroup) {
			$optionGroupId = NULL;
			
			// verify if group exists
			$params = array(
				'sequential'	=> 1,
				'name'			=> $optionGroup['group_name'],
			);
			try {
				$result = civicrm_api3('OptionGroup', 'getsingle', $params);
				$optionGroupId = $result['id'];
			} catch (Exception $e) {
				// optiongroup not found: $optionGroupId remains NULL
			}
		
			// if group was not found: create it
			if (is_null($optionGroupId)) {
				$params = array(
					'sequential'	=> 1,
					'name'			=> $optionGroup['group_name'],
					'title'			=> $optionGroup['group_label'],
					'is_active'		=> 1,
					'description'	=> 'nl.pum.tokens',
				);
				try {
					$result = civicrm_api3('OptionGroup', 'create', $params);
					// group created: retrieve $customGroupId (perform an intentional new db request)
					$params = array(
						'sequential'	=> 1,
						'name'			=> $optionGroup['group_name'],
					);
					try {
						$result = civicrm_api3('OptionGroup', 'getsingle', $params);
						$optionGroupId = $result['id'];
						$created[] = $optionGroup['group_label'];
					} catch (Exception $e) {
						// optiongroup not found: $optionGroupId remains NULL
					}
				} catch (Exception $e) {
					// group not created: $optionGroupId remains NULL
				}
			}
		} // next $optionGroup
		
		if (count($created) > 0) {
			$message = "Option group ".implode(", ", $created)." succesfully created";
			CRM_Utils_System::setUFMessage($message);
		}
		
		usleep(1000);
		
		// cycle 2:  create option values
		foreach ($required as $optionGroup) {
			$created = array();
			$optionGroupId = NULL;
			
			// verify if group exists
			$params = array(
				'sequential'	=> 1,
				'name'			=> $optionGroup['group_name'],
			);
			try {
				$result = civicrm_api3('OptionGroup', 'getsingle', $params);
				$optionGroupId = $result['id'];
			} catch (Exception $e) {
				// optiongroup not found: $optionGroupId remains NULL
			}
			
			// create optionvalues (if option group exists)
			if (!is_null($optionGroupId)) {
				foreach ($optionGroup['values'] as $optionValue) {
					// verify if option value exists
					$params = array(
						'sequential'		=> 1,
						'option_group_id'	=> $optionGroupId,
						'name'				=> $optionValue['name'],
					);
					try {
						$result = civicrm_api3('OptionValue', 'getsingle', $params);
						// option value found
					} catch (Exception $e) {
						// option value NOT found
						$params = array(
							'sequential'		=> 1,
							'option_group_id'	=> $optionGroupId,
							'label'				=> $optionValue['label'],
							'name'				=> $optionValue['name'],
							'value'				=> $optionValue['value'],
							'weight'			=> $optionValue['weight'],
							'description'		=> $optionValue['description'],
							'is_reserved'		=> TRUE,
							'is_active'			=> TRUE,
							'is_default'		=> $optionValue['default'],
						);
						try {
							$result_val = civicrm_api3('OptionValue', 'create', $params);
							$created[] = $optionValue['label'];
						} catch (Exception $e) {
						}
					}
					
					//CRM_Utils_System::setUFMessage($optionValue['name']);
				} // next option value
			}
			
			if (count($created) > 0) {
				$message = 'Option group ' . $optionGroup['group_label'] . ': value(s) ' . implode(', ', $created) . ' succesfully created';
				CRM_Utils_System::setUFMessage($message);
			}
			
		} // next $optionGroup
	}
	
	
	/*
	 * handler for hook_civicrm_enable
	 */
	static function enable() {
		$required = self::required();
		// set all option groups to enabled
		foreach ($required as $optionGroup) {
			$params = array(
				'sequential' => 1,
				'name' => $optionGroup['group_name'],
			);
			$result = civicrm_api3('OptionGroup', 'getsingle', $params);
			if (!array_key_exists('id', $result)) {
				// optiongroup not found: cannot enable
				$group_id = NULL;
			} else {
				// optiongroup found: proceed
				$group_id = $result['id'];
				if ($optionGroup['enable_level']=='group') {
					
					$qryEnable = "UPDATE civicrm_option_group SET is_active=1 WHERE name='" . $optionGroup['group_name'] . "'";
					CRM_Core_DAO::executeQuery($qryEnable);
				} elseif ($optionGroup['enable_level']=='value') {
					// enable the values within the group
					foreach ($optionGroup['values'] as $optionValue) {
						$qryEnable = "UPDATE civicrm_option_value SET is_active=1 WHERE option_group_id='" . $group_id . "' AND name='" . $optionValue['name'] . "'";
						CRM_Core_DAO::executeQuery($qryEnable);
					}
				} else {
					// cannot decide what to enable
				}
			}
		}
	}
	
	/*
	 * handler for hook_civicrm_disable
	 */
	static function disable() {
		$required = self::required();
		// set all option groups to enabled
		foreach ($required as $optionGroup) {
			$params = array(
				'sequential' => 1,
				'name' => $optionGroup['group_name'],
			);
			$result = civicrm_api3('OptionGroup', 'getsingle', $params);
			if (!array_key_exists('id', $result)) {
				// optiongroup not found: cannot enable
				$group_id = NULL;
			} else {
			// optiongroup found: proceed
				$group_id = $result['id'];
				if ($optionGroup['enable_level']=='group') {
					$qryEnable = "UPDATE civicrm_option_group SET is_active=0 WHERE name='" . $optionGroup['group_name'] . "'";
					CRM_Core_DAO::executeQuery($qryEnable);
				} elseif ($optionGroup['enable_level']=='value') {
					// disable the values within the group
					foreach ($optionGroup['values'] as $optionValue) {
						$qryDisable = "UPDATE civicrm_option_value SET is_active=0 WHERE option_group_id='" . $group_id . "' AND name='" . $optionValue['name'] . "'";
						CRM_Core_DAO::executeQuery($qryDisable);
					}
				} else {
					// cannot decide what to enable
				}
			}
		}
	}
	
	/*
	 * handler for hook_civicrm_uninstall
	 */
	static function uninstall() {
	}
	
}