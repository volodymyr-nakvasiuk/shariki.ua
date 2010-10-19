<?php
class Db_AclModules extends ArOn_Db_Table {
	
	protected $_primary = 'acl_module_id';
	protected $_name = 'acl_module';
	protected $_name_expr = 'acl_module_name';

	protected $_dependentTables = array(
		'Db_AclResources'
	);

}