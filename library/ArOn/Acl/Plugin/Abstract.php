<?php
abstract class ArOn_Acl_Plugin_Abstract {
	/**
	 * @var Acl_Control
	 */
	protected $_acl;
	protected $_storage;
	protected $_params;

	public function __construct(ArOn_Acl_Control $acl, &$storage=array(), $params=array()) {
		$this->_acl = $acl;
		$this->_storage = $storage;
		$this->_params = $params;
	}

	public abstract static function toStorage($uid, $gid);

	public abstract function check($value);
}
?>