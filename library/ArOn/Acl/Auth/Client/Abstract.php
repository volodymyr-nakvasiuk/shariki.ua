<?php
class ArOn_Acl_Auth_Client_Abstract extends ArOn_Acl_Auth_Abstract {
	public $loginCookie = 'info_client';
	protected $userLoginField = 'client_email';
	protected $userPasswordField = 'client_password';
	protected $userCredentialTreatment = '(?) AND client_status = \'approved\' ';
	
	protected function initUserAuthAndDb(){
		$this->userDB = Db_Client::getInstance();
 		$this->userAuth = ArOn_Acl_Auth_Client::getDbAuthAdapter();
	}
 	
	protected function storageWriteControl($identity){
 		$this->getStorage()->write(ArOn_Acl_Control_Client::toStorage($identity));
 	}
}
