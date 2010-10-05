<?php
class Db_AclUsers extends ArOn_Db_Table {
	protected $_name = 'acl_users';
	protected $_name_expr = 'name';

	protected $_dependentTables = array(
		'Db_Client',
		'Db_AclUserRules',
		'Db_FinanceAccounts',
	);

	protected $_referenceMap    = array(
        'AclUsers_AclGroups' => array(
            'columns'           => 'group_id',
            'refTableClass'     => 'Db_AclGroups',
            'refColumns'        => 'id'
            ),
        'AclRoles' => array(
            'columns'           => 'role_id',
            'refTableClass'     => 'Db_AclRoles',
            'refColumns'        => 'acl_role_id'
            )
   );


}