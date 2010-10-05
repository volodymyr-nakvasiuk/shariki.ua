<?php
class Cms_LoginController extends Abstract_Controller_CmsController {

	public function indexAction(){
		//if($this->cookieAuth())
		//	$this->_redirect('/cms');
			
		$form = new Zend_Form();

		$form->addElement('text', 'login', array('id'=>'login', 'label'=>'Login'));
		$form->addElement('password', 'password', array('id'=>'password', 'label'=>'Password'));
		$form->addElement('submit', 'submit', array('class'=>'submit', 'value'=>'Login'));
		$params = $this->_request->getParams();
		if ($this->getRequest()->isPost() && isset($params['login'])) {
			$this->_helper->viewRenderer->setNoRender();
			if ($form->isValid($params)) {
				$auth = ArOn_Acl_Auth_Admin_Abstract::getInstance();
				$adapter = ArOn_Acl_Auth_Admin::getDbAuthAdapter();
				$adapter->setIdentity($form->getValue('login'))->setCredential($form->getValue('password'));
				$result = $auth->authenticate($adapter);
				if ($result->isValid()) {
					$auth->getStorage()->write(ArOn_Acl_Controlv2::toStorage($result->getIdentity()));
					//$this->_forward($this->_getParam('oldAction'));
					echo "{success: true}";
					return;
				}
				else {
					//$this->view->error = 'Wrong auth!';
					echo "{success: false}";
					return;
				}
			}
		}

		$form->getElement('submit')->setValue('Login');
		$this->view->form = $form;
		$this->render('login/login', null, true);
	}
}
