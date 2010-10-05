<?php
class Acl_Plugin_Client extends ArOn_Acl_Plugin_Abstract {

	protected $_name = 'client';
	protected $_aclRule = 'restrict-affiliates';

	public static function toStorage($uid, $gid) {
		$sql = "SELECT `client_id` FROM `acl_users_clients` WHERE user_id = '$uid'";
		$db = ArOn_Db_Table::getDefaultAdapter(false);
		return $db->fetchCol($sql);
	}

	public function check($value) {
		if (!$this->_acl->check($this->_aclRule)) {
			return true;
		}
		return in_array($value, $this->_storage);
	}

	function setAffiliatesFilter(&$target) {
		if (!$this->_acl->check($this->_aclRule)) {
			return false;
		}
	}

	function addFilter(&$filters) {
		if (!$this->_acl->check($this->_aclRule)) {
			return false;
		}
		return false;
	}

	function getList() {
		if (!$this->_acl->check($this->_aclRule)) {
			return false;
		}
		return $this->_storage;
	}

	function getName() {
		return $this->_name;
	}

	function aclRule(){
		return $this->_aclRule;
	}
}
?>