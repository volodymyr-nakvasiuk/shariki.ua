<?php
class Db_AclRoles extends ArOn_Db_Table {
	
	protected $_primary = 'acl_role_id';	
	protected $_name = 'acl_role';
	protected $_name_expr = 'acl_role_name';

	protected $_dependentTables = array(
		'Db_AclRolePrivileges',
		'Db_AclUsers'
	);

}