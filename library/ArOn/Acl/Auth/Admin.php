<?php
class ArOn_Acl_Auth_Admin extends ArOn_Acl_Auth {
	static function getDbAuthAdapter() {
		$db = ArOn_Db_Table::getDefaultAdapter(false);
		$tableName = 'acl_users';
		$identityColumn = 'name';
		$credentialColumn = 'password';
		$credentialTreatment = '? AND enabled = true';
		return new ArOn_Zend_Auth_Adapter_DbTable($db, $tableName, $identityColumn, $credentialColumn, $credentialTreatment);
	}
}
?>