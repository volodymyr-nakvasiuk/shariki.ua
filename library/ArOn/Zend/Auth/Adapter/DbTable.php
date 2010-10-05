<?php
class ArOn_Zend_Auth_Adapter_DbTable extends Zend_Auth_Adapter_DbTable{
	
	public function __construct($zendDb, $tableName = null, $identityColumn = null,
                                $credentialColumn = null, $credentialTreatment = null)
    {
        $this->_zendDb = $zendDb;

        if (null !== $tableName) {
            $this->setTableName($tableName);
        }

        if (null !== $identityColumn) {
            $this->setIdentityColumn($identityColumn);
        }

        if (null !== $credentialColumn) {
            $this->setCredentialColumn($credentialColumn);
        }

        if (null !== $credentialTreatment) {
            $this->setCredentialTreatment($credentialTreatment);
        }
    }
}