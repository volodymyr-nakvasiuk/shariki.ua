<?php
class Db_AclRolePrivileges extends ArOn_Db_Table {
	
	protected $_primary = array('acl_privilege_id','acl_role_id');	
	protected $_name = 'acl_role_privilege';

	protected $_referenceMap    = array(
        'AclPrivileges' => array(
            'columns'           => 'acl_privilege_id',
            'refTableClass'     => 'Db_AclPrivileges',
            'refColumns'        => 'acl_privilege_id'
            ),
        'AclRoles' => array(
            'columns'           => 'acl_role_id',
            'refTableClass'     => 'Db_AclRoles',
            'refColumns'        => 'acl_role_id'
            )
   );
}