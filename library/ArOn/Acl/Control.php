<?php
class ArOn_Acl_Control {
	protected static $instance;
	protected static $plugins = array();

	/**
	 * @var Zend_Loader_PluginLoader
	 */
	protected static $loader;

	protected $_storage;
	protected $_settings;

	protected $_plugins;

	/**
	 * @var Zend_Db_Adapter_Abstract
	 */
	protected $_db;

	/**
	 * @return Acl_Control
	 */
	public static function getInstance() {
		$auth = Zend_Auth::getInstance();
		if (!$auth->hasIdentity()) {
			return;
		}

		if (!isset(self::$instance) && is_array($auth->getIdentity())) {
			self::$instance = new self($auth->getIdentity());
		}
		return self::$instance;
	}

	public static function addPrefixPath($prefix, $path) {
		if (!isset(self::$loader)) {
			self::$loader = new Zend_Loader_PluginLoader();
			self::$loader->addPrefixPath('ArOn_Acl_Plugin','ArOn/Acl/Plugin');
		}
		self::$loader->addPrefixPath($prefix, $path);
	}

	public static function registerPlugin($pluginName, $params = array()) {
		if (!isset(self::$loader)) {
			self::$loader = new Zend_Loader_PluginLoader();
			self::$loader->addPrefixPath('ArOn_Acl_Plugin','ArOn/Acl/Plugin');
		}
		$className = self::$loader->load($pluginName);
		self::$plugins[$pluginName] = array('class'=>$className, 'params'=>$params);
	}

	public static function toStorage($identity) {
		$storage = array();

		$db = ArOn_Db_Table::getDefaultAdapter();
		$uname = $db->quote($identity);

		$sql = "SELECT
					`users`.`id` AS `userid`, 
					`users`.`name` AS `username`, 
					`groups`.`id` AS `groupid`,
					`groups`.`name` AS `groupname`,
					`users`.`formal_name` AS `formalname`
				FROM
					`acl_users` `users`
					INNER JOIN `acl_groups` `groups` ON `users`.`group_id` = `groups`.`id`
				WHERE
					`users`.`name` = $uname";

		if (false == ($row = $db->fetchRow($sql))) {
			throw new Exception('Wrong user! Mystical fail!');
		}

		$storage['user']['id'] = $row['userid'];
		$storage['user']['name'] = $row['username'];
		$storage['user']['formal'] = $row['formalname'];
		$storage['group']['id'] = $row['groupid'];
		$storage['group']['name'] = $row['groupname'];

		$uid = &$storage['user']['id'];
		$gid = &$storage['group']['id'];

		// rules
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

		$sql = "SELECT
					`rules`.`name` as `name`
				FROM
					`acl_rules` `rules`
					INNER JOIN `acl_users_rules` `users_rules` ON `rules`.`id` = `users_rules`.`rule_id`
				WHERE
					`users_rules`.`user_id` = $uid
					AND ISNULL(`rules`.`type`) 
					AND `rules`.`perm` = 'deny'";
		$storage['rules']['user']['deny'] = $db->fetchCol($sql);

		$sql = "SELECT
					`rules`.`name` as `name`
				FROM
					`acl_rules` `rules`
					INNER JOIN `acl_groups_rules` `groups_rules` ON `rules`.`id` = `groups_rules`.`rule_id`
				WHERE
					`groups_rules`.`group_id` = $gid
					AND ISNULL(`rules`.`type`) 
					AND `rules`.`perm` = 'allow'";
		$storage['rules']['group']['allow'] = $db->fetchCol($sql);

		$sql = "SELECT
					`rules`.`name` as `name`
				FROM
					`acl_rules` `rules`
					INNER JOIN `acl_groups_rules` `groups_rules` ON `rules`.`id` = `groups_rules`.`rule_id`
				WHERE
					`groups_rules`.`group_id` = $gid
					AND ISNULL(`rules`.`type`) 
					AND `rules`.`perm` = 'deny'";
		$storage['rules']['group']['deny'] = $db->fetchCol($sql);

		// settings
		$sql = "SELECT
					`settings`.`name`,
					`settings`.`value`
				FROM
					`acl_settings` `settings`
				WHERE
					`settings`.`user_id` = $uid";
		$storage['settings'] = $db->fetchPairs($sql);

		// plugins
		$storage['plugins'] = array();

		foreach (self::$plugins as $pluginName=>$plugin) {
			$storage['plugins'][$pluginName] = call_user_func_array(array($plugin['class'], 'toStorage'), array($uid, $gid));
		}


		return $storage;
	}

	//  ==============================================================================================================

	public function __construct($storage) {
		$this->_storage = $storage;
		$this->_settings = &$this->_storage['settings'];

		foreach (self::$plugins as $pluginName=>$plugin) {
			$class = $plugin['class'];
			$this->_plugins[$pluginName] = new $class($this, $storage['plugins'][$pluginName], $plugin['params']);
		}

		$this->_db = ArOn_Db_Table::getDefaultAdapter();
	}

	//  ==============================================================================================================

	/**
	 * @return ArOn_Acl_Plugin_Abstract
	 */
	public function plugin($name) {
		if (!isset($this->_plugins[$name])) {
			trigger_error("Unknown plugin '$name'", E_USER_ERROR);
		}
		return $this->_plugins[$name];
	}

	/**
	 * @return string
	 */
	public function settings($name) {
		$uid = $this->getUserId();
		$_name = $this->_db->quote($name);
		$sql = "SELECT `value` FROM `acl_settings` WHERE `user_id` = $uid AND `name`=$_name";
		return $this->_db->fetchOne($sql);
	}

	public function setSettings($name, $value=NULL) {
		$uid = $this->getUserId();
		if (isset($value)) {
			$_name = $this->_db->quote($name);
			$_value = $this->_db->quote($value);
			$sql = "REPLACE INTO `acl_settings`(`user_id`, `name`, `value`) VALUES ($uid, $_name, $_value)";
			$this->_settings[$name] = $value;
		}
		else {
			$_name = $this->_db->quote($name);
			$sql = "DELETE FROM `acl_settings` WHERE `user_id` = $uid AND `name`=$_name";
			unset($this->_settings[$name]);
		}
		$this->_db->query($sql);
	}

	//  ==============================================================================================================

	public function getUserId() {
		return $this->_storage['user']['id'];
	}

	public function getUserName() {
		return $this->_storage['user']['name'];
	}

	public function getFormalName() {
		return $this->_storage['user']['formal'];
	}

	public function getGroupId() {
		return $this->_storage['group']['id'];
	}

	public function getGroupName() {
		return $this->_storage['group']['name'];
	}

	public function checkUser($user) {
		if (is_string($user)) {
			return ($this->getUserName() === $user);
		}
		return $this->userid == $user;
	}

	public function checkGroup($group) {
		if (is_string($group)) {
			return ($this->getGroupName() === $group);
		}
		return ($this->groupid == $group);
	}

	public function check($value) {
		$ga = &$this->_storage['rules']['group']['allow'];
		$gd = &$this->_storage['rules']['group']['deny'];
		$ua = &$this->_storage['rules']['user']['allow'];
		$ud = &$this->_storage['rules']['user']['deny'];


		$ok = false;
		foreach ($ga as $rule) {
			if ($rule == $value) {
				$ok = true;
				break;
			}
		}
		if ($ok) {
			foreach ($gd as $rule) {
				if ($rule == $value) {
					$ok = false;
					break;
				}
			}
		}
		if (!$ok) {
			foreach ($ua as $rule) {
				if ($rule == $value) {
					$ok = true;
					break;
				}
			}
		}
		if ($ok) {
			foreach ($ud as $rule) {
				if ($rule == $value) {
					$ok = false;
					break;
				}
			}
		}
		return $ok;
	}

	public function __call($name, $arguments) {
		if (substr($name, 0, 5) === 'check') {
			$plugin = $this->plugin(strtolower(substr($name, 5)));
			return $plugin->check($arguments[0]);
		}
		trigger_error("Wrong method '$name'", E_USER_ERROR);
	}

}
?>