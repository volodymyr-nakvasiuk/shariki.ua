<?php


class ArOn_Acl_Auth_Admin_DbTable extends Zend_Auth_Adapter_DbTable
{
	protected function _authenticateCreateSelect()
	{
		$dbSelect = parent::_authenticateCreateSelect();

		$dbSelect->where('is_deleted = 0');

		return $dbSelect;
	}
}

?>