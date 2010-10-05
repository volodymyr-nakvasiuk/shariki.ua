<?php
class ArOn_Acl_Auth_Client extends ArOn_Acl_Auth {
	static function getDbAuthAdapter() {
		$db = ArOn_Db_Table::getDefaultAdapter(false);
		$tableName = 'client';
		$identityColumn = 'client_email';
		$credentialColumn = 'client_password';
		$credentialTreatment = '? AND client_enabled = true';
		return new ArOn_Zend_Auth_Adapter_DbTable($db, $tableName, $identityColumn, $credentialColumn, $credentialTreatment);
	}
}
?>