<?php
 
class ArOn_Acl_Controlv2 extends Zend_Acl {
 
	protected static $_instance = null;
 
	private function __construct()
	{}
 
	private function __clone()
	{}
	
	protected function _initialize()
	{
		$model = Db_AclRolePrivileges::getInstance();
		$select = $model->select();
		$select->setColumn('acl_role_id');
		$select->columnsJoinOne('Db_AclPrivileges',array('acl_privilege_name'));
		$select->columnsJoinOne(array('Db_AclPrivileges','Db_AclResources'),array('acl_resource_name'));
		$select->columnsJoinOne(array('Db_AclPrivileges','Db_AclResources','Db_AclModules'),array('acl_module_name'));
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
 		//$assert = new ArOn_Acl_Assert_Regular();
		foreach ($roles as $role) {
			$this->allow($role['acl_role_id'], $role['acl_module_name'].'_'.$role['acl_resource_name'], $role['acl_privilege_name']);
		}
	}
	
	
	protected function _getRuleType(Zend_Acl_Resource_Interface $resource = null, Zend_Acl_Role_Interface $role = null, $privilege = null) {
		// get the rules for the $resource and $role
		if (null === ($rules = $this->_getRules($resource, $role))) {
			return null;
		}

		// follow $privilege
		if (null === $privilege) {
			if (isset($rules['allPrivileges'])) {
				$rule = $rules['allPrivileges'];
			} else {
				return null;
			}
		} else if (!isset($rules['byPrivilegeId'][$privilege])) {
			if(null === ($rule = $this->_findAltType($rules['byPrivilegeId'],$privilege)))
				return null;
		} else {
			$rule = $rules['byPrivilegeId'][$privilege];
		}

		// check assertion first
		if ($rule['assert']) {
			$assertion = $rule['assert'];
			$assertionValue = $assertion->assert(
				$this,
				($this->_isAllowedRole instanceof Zend_Acl_Role_Interface) ? $this->_isAllowedRole : $role,
				($this->_isAllowedResource instanceof Zend_Acl_Resource_Interface) ? $this->_isAllowedResource : $resource,
				$privilege
				);
		} 
		
		if (null === $rule['assert'] || $assertionValue) {
			return $rule['type'];
		} else if (null !== $resource || null !== $role || null !== $privilege) {
			return null;
		} else if (self::TYPE_ALLOW === $rule['type']) {
			return self::TYPE_DENY;
		} else {
			return self::TYPE_ALLOW;
		}
	}
	
	public function has($resource)
	{
		if ($resource instanceof Zend_Acl_Resource_Interface) {
			$resourceId = $resource->getResourceId();
		} else {
			$resourceId = (string) $resource;
		}
		//var_dump($resource);
		if(!isset($this->_resources[$resourceId]) && $this->_findAltType($this->_resources,$resourceId) === null){
			return false;
		}
		return true;
	}
	
	public function get($resource)
	{
		if ($resource instanceof Zend_Acl_Resource_Interface) {
			$resourceId = $resource->getResourceId();
		} else {
			$resourceId = (string) $resource;
		}

		if (($resource = $this->_findAltType($this->_resources,$resourceId)) === null) {
			//require_once'Zend/Acl/Exception.php';
			throw new Zend_Acl_Exception("Resource '$resourceId' not found");
		}
		return $resource['instance'];
	}
	
	/*protected function _initialize()
	{
 
		$db = ArOn_Db_Table::getDefaultAdapter();
 
		$roles = $db->fetchAll("SELECT
				acl_role.acl_role_name,
				acl_role_privilege.acl_role_id, 
				acl_module.acl_module_name,
				acl_resource.acl_resource_name,
				acl_privilege.acl_privilege_name
				FROM acl_role
				INNER JOIN acl_role_privilege 
				ON acl_role_privilege.acl_role_id = acl_role.acl_role_id
				INNER JOIN acl_privilege 
				ON acl_role_privilege.acl_privilege_id = acl_privilege.acl_privilege_id
				INNER JOIN acl_resource
				ON acl_privilege.acl_resource_id = acl_resource.acl_resource_id
				INNER JOIN acl_module
				ON acl_resource.acl_module_id = acl_module.acl_module_id");
 
		foreach ($roles as $role) {
			if (!$this->has($role['acl_module_name'].'_'.$role['acl_resource_name'])) {
				$this->add(new Zend_Acl_Resource($role['acl_module_name'].'_'.$role['acl_resource_name']));
			}
			if (!$this->hasRole($role['acl_role_name'])) {
				$this->addRole(new Zend_Acl_Role($role['acl_role_name']));
			}
		}
 
		$this->deny();
		//$this->allow(null, $role['acl_module_name'] . '_login');
 
		foreach ($roles as $role) {
			$this->allow($role['acl_role_name'], $role['acl_module_name'].'_'.$role['acl_resource_name'], $role['acl_privilege_name']);
		}
 
	}*/
 	
	protected function _findAltType($types, $privilege){
		if(empty($types)) return null;
		$regexp = array();
		foreach($types as $type => $rule){
			if(strpos($type,"*") !== false){
				$regexp[$type] = $rule;
			}elseif ( $type == $privilege){
				return $rule;
			}
		}
		foreach ($regexp as $type => $rule){
			if($type == "*") return $rule;
			$type = str_replace(array(".*","*"),array("*",".*"),$type);
			if (preg_match("/^".$type."$/i", $privilege)){
				return $rule;
			}
		}
		return null;
	}
	
	public static function toStorage($identity) {
		$storage = array();

		$model = Db_AclUsers::getInstance();
		$select = $model->select();
		$select->columnsAll();
		$select->where('name = ?',$identity);

		$sql = "SELECT
					`users`.`id` AS `userid`, 
					`users`.`name` AS `username`,
					`users`.`role_id` AS `role`,
					`users`.`formal_name` AS `formalname`
				FROM
					`acl_users` `users`
				WHERE
					`users`.`name` = $uname";

		if (null === ($row = $model->fetchRow($select))) {
			throw new Exception('Wrong user! Mystical fail!');
		}
		$row = $row->toArray();
		$storage['user']['id'] = $row['id'];
		$storage['user']['name'] = $row['name'];
		$storage['user']['formal'] = $row['formal_name'];
		$storage['user']['role'] = $row['role_id'];

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