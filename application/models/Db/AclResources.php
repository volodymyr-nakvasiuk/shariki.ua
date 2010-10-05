<?php
class Db_AclResources extends ArOn_Db_Table {
	
	protected $_primary = 'acl_resource_id';
	protected $_name = 'acl_resource';
	protected $_name_expr = 'acl_resource_name';

	protected $_dependentTables = array(
		'Db_AclPrivileges'
	);
	
	protected $_referenceMap    = array(
        'AclModules' => array(
            'columns'           => 'acl_module_id',
            'refTableClass'     => 'Db_AclModules',
            'refColumns'        => 'acl_module_id'
            )
   );

}