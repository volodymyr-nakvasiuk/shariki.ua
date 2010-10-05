<?php
class Client_LoginController extends Client_AjaxController  {
	
	public $resultJSON = array();

	public function indexAction(){
		$params = $this->_request->getParams();
		if ($this->getRequest()->isPost() && !empty($params['login'])) {
			$form = new Zend_Form();
			$form->addElement('text', 'login', array('id'=>'login', 'label'=>'Login'));
			$form->addElement('password', 'password', array('id'=>'password', 'label'=>'Password'));
			$form->addElement('submit', 'submit', array('class'=>'submit', 'value'=>'Login'));
			if ($form->isValid($params)) {
				$adapter = ArOn_Acl_Auth_Client::getDbAuthAdapter();
				$adapter->setIdentity($form->getValue('login'))->setCredential($form->getValue('password'));
				//$prev_date = "2010:01:09:16:53:01";
				$prev_date = time() - 86400;
				$now_date = time();
				$adapter->setCredentialTreatment('(?) AND (client_status = \'approved\' OR UNIX_TIMESTAMP(client_created_date) > '.$prev_date.')');
				$result = self::$currentAuth->authenticate($adapter);
				if ($result->isValid()) {
					$this->setupUser($result->getIdentity(), !empty($params['save_login']) && $params['save_login'] == 'yes');
					$this->initUser();
					$this->resultJSON['success'] = true;
					$this->resultJSON['loginRedirect'] = HOST_NAME.'/client/';
					return;
				}
				else {
					$cl_db = Db_Client::getInstance();
					$authent = $cl_db->fetchAll("client_email= '".$form->getValue('login')."' AND client_password='".$form->getValue('password')."'");
					if($authent){
						$authent = $authent->toArray();
						if(is_array($authent)){
							foreach($authent AS $value){
								$this->resultJSON['success'] = false;
								$this->resultJSON['message'] = 1; //"Не введен код подтверждения!";
								return;
							}
						};
						$this->resultJSON['success'] = false;
						$this->resultJSON['message'] = 2; //"Неверный логин либо пароль!\nПроверьте данные и попробуйте еще раз.";
						return;
					}
				}
			}
			else {
				$this->resultJSON['success'] = false;
				$this->resultJSON['message'] = 3; //"Неверно заполненная форма!\nПроверьте данные и попробуйте еще раз.";
				return;
			}
		}
		elseif($this->_request->isXmlHttpRequest()){
			$this->resultJSON['success'] = false;
			$this->resultJSON['message'] = 4; //"Некорректный запрос.";
			return;
		}
		$this->_helper->viewRenderer->setNoRender(false);
		$this->_forward('error', 'error', 'default');
	}

	public function logoutAction() {
		$this->forgetUser();
		$this->_redirect('/');
	}
	
	public function loginAction() {
		setcookie('login_client','1',time()+120,'/',str_replace('http://', '.', HOST_NAME));
		$this->_redirect('/');
	}
}
