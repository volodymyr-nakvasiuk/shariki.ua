<?php
abstract class ArOn_Acl_Auth_Abstract extends Zend_Auth {
	protected static $_instance = null;
	public $loginCookie = 'client_auth';
	protected $userLoginField = 'login';
	protected $userPasswordField = 'password';
	protected $userCredentialTreatment = false;
	protected $userDB; // Need to be declarated in __construct
	protected $userAuth; // Need to be declarated in __construct
	
	abstract protected function initUserAuthAndDb();
	
 	protected function __construct(){
 		$this->initUserAuthAndDb();
 	}
 	
	protected function storageWriteControl($identity){
 		$this->getStorage()->write(ArOn_Acl_Control::toStorage($identity));
 	}

	public static function getInstance() {
		if (is_null ( self::$_instance )) {
			self::$_instance = parent::getInstance ();
			self::$_instance->setStorage ( new Zend_Auth_Storage_Session ( get_called_class() ) );
		}
		return self::$_instance;
	}
	
	public function getIdentity(){
		if($this->hasIdentity()){
			return parent::getIdentity();
		}
		return null;
	}
	
	public function hasIdentity(){
		$hasIdentity = parent::hasIdentity();
		return $hasIdentity?$hasIdentity:$this->cookieAuth();
	}

	protected function cookieAuth(){
		if(empty($_COOKIE[$this->loginCookie]))
			return false;

		$salt = (defined('PASSWORD_SALT')) ? PASSWORD_SALT: 'login';
		$crypt = new ArOn_Crud_Tools_Crypt($salt);
		if(!$crypt->check($_COOKIE[$this->loginCookie])){
			setcookie($this->loginCookie,"-",time()-3600,'/');
			return false;
		}
		$id = $crypt->getUserId();
		$date = $crypt->getUserDate();
		if(!$date){					
			setcookie($this->loginCookie,"-",time()-3600,'/');
			return false;
		}
		
		$data = $this->userDB->getRowById($id);
		$this->userAuth->setIdentity($data[$this->userLoginField])->setCredential($data[$this->userPasswordField]);
		if ($this->userCredentialTreatment) $this->userAuth->setCredentialTreatment($this->userCredentialTreatment);
		$result = $this->authenticate($this->userAuth);
		$this->storageWriteControl($result->getIdentity());
		return true;
	}
}
