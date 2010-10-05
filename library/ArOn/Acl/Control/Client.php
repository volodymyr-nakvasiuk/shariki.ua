<?php
class ArOn_Acl_Control_Client extends ArOn_Acl_Controlv2 {
 
	protected static $_instance = null;
 
	private function __construct()
	{}
 
	private function __clone()
	{}
 	
	protected function _initialize()
	{		
		$model = Db_AclRolePrivileges::getInstance();
		$select = $model->select();
		$select->columnsJoinOne('Db_AclPrivileges',array('acl_privilege_name'));
		$select->columnsJoinOne(array('Db_AclPrivileges','Db_AclResources'),array('acl_resource_name'));
		$select->columnsJoinOne(array('Db_AclPrivileges','Db_AclResources','Db_AclModules'),array('acl_module_name'));
		/*$roles = $db->fetchAll("SELECT
				acl_role_privilege.acl_role_id, 
				acl_module.acl_module_name,
				acl_resource.acl_resource_name,
				acl_privilege.acl_privilege_name
				FROM acl_role_privilege
				INNER JOIN acl_privilege 
				ON acl_role_privilege.acl_privilege_id = acl_privilege.acl_privilege_id
				INNER JOIN acl_resource
				ON acl_privilege.acl_resource_id = acl_resource.acl_resource_id
				INNER JOIN acl_module
				ON acl_resource.acl_module_id = acl_module.acl_module_id");*/

		$result = $model->fetchAll($select);
		if($result === null)
			return false;
		else
			$roles = $result->toArray();
			
		foreach ($roles as $role) {
			if (!$this->has($role['acl_module_name'].'_'.$role['acl_resource_name'])) {
				$this->add(new Zend_Acl_Resource($role['acl_module_name'].'_'.$role['acl_resource_name']));
			}
			if (!$this->hasRole($role['acl_role_id'])) {
				$this->addRole(new Zend_Acl_Role($role['acl_role_id']));
			}
		}
 
		$this->deny();
		//$this->allow(null, $role['acl_module_name'] . '_login');
 
		foreach ($roles as $role) {
			$this->allow($role['acl_role_id'], $role['acl_module_name'].'_'.$role['acl_resource_name'], $role['acl_privilege_name']);
		}
 
	}
	 	
	public static function toStorage($identity) {
		$storage = array();
		$model = Db_Client::getInstance();
		$select = $model->select();
		$select->columnsAll();
		$select->where('client_email = ?',$identity);
		
		$sql = "SELECT
					`users`.*,
					`users`.`client_id` AS `userid`, 
					`users`.`client_name` AS `username`,
					`users`.`acl_role_id` AS `role`
				FROM
					`client` `users`
				WHERE
					`users`.`client_email` = $uname";

		if (null === ($row = $model->fetchRow($select))) {
			throw new Exception('Wrong user! Mystical fail!');
		}
		$row = $row->toArray();
		$storage['user']['id'] = $row['client_id'];
		$storage['user']['name'] = $row['client_name'];
		$storage['user']['password'] = $row['client_password'];
		//$storage['user']['referal'] = $row['client_referal'];
		$storage['user']['tel'] = $row['client_tel'];
		$storage['user']['email'] = $row['client_email'];
		$storage['user']['url'] = $row['client_url'];
		$storage['user']['region_id'] = $row['client_region_id'];
		$storage['user']['place_id'] = $row['client_place_id'];
		$storage['user']['info'] = $row['client_info'];
		$storage['user']['addr'] = $row['client_addr'];
		$storage['user']['photos'] = $row['client_photos'];
		$storage['user']['priority'] = $row['client_priority'];
		$storage['user']['role'] =  $row['acl_role_id'];
		
		$uid = &$storage['user']['id'];
		$gid = &$storage['group']['id'];

		/*// rules
		$sql = "SELECT
					`rules`.`name` as `name`
				FROM
					`acl_rules` `rules`
					INNER JOIN `acl_users_rules` `users_rules` ON `rules`.`id` = `users_rules`.`rule_id`
				WHERE
					`users_rules`.`user_id` = $uid
					AND ISNULL(`rules`.`type`) 
					AND `rules`.`perm` = 'allow'";
		$storage['rules']['user']['allow'] = $db->fetchCol($sql);

		*/


		return $storage;
	}
	
	public static function getInstance()
    {
	   if (null === self::$_instance) {
		self::$_instance = new self();
		self::$_instance->_initialize();
	   }
 
	   return self::$_instance;
    }
 
}