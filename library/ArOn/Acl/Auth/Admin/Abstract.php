<?php
class ArOn_Acl_Auth_Admin_Abstract extends ArOn_Acl_Auth_Abstract {
	public $loginCookie = 'info_admin';
	protected $userLoginField = 'name';
	protected $userPasswordField = 'password';
	
	protected function initUserAuthAndDb(){
		$this->userDB = Db_AclUsers::getInstance();
 		$this->userAuth = ArOn_Acl_Auth_Admin::getDbAuthAdapter();
	}
	
	protected function storageWriteControl($identity){
 		$this->getStorage()->write(ArOn_Acl_Controlv2::toStorage($identity));
 	}
}
