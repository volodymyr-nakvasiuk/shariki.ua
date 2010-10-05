<?php
class Db_AclPrivileges extends ArOn_Db_Table {
	
	protected $_primary = 'acl_privilege_id';
	protected $_name = 'acl_privilege';
	protected $_name_expr = 'acl_privilege_name';

	protected $_dependentTables = array(
		'Db_AclRolePrivileges'
	);
	
	protected $_referenceMap    = array(
        'AclResources' => array(
            'columns'           => 'acl_resource_id',
            'refTableClass'     => 'Db_AclResources',
            'refColumns'        => 'acl_resource_id'
            )
   );
}