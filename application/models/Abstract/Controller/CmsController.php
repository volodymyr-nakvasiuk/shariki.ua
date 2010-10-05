<?php

/**
 * CarController
 *
 * @author
 * @version
 */

class Abstract_Controller_CmsController extends Abstract_Controller_InitController {
	
	public function init(){
		$this->_helper->layout->disableLayout();
		/*$this->_helper->viewRenderer->setNoRender();*/

		parent::init();

		if (!Zend_Session::isStarted()) {
			Zend_Session::start();
		}
		self::$currentAuth = ArOn_Acl_Auth_Admin_Abstract::getInstance();
	}

	public function preDispatch() {
		$actionName = $this->getRequest()->getActionName();

		if (in_array($actionName, array('login', 'logout', 'deny'))) {
			return;
		}
		 
		
		if (!self::$currentAuth->hasIdentity() && $this->getRequest()->getActionName() !== 'login') {
			self::$currentAuth->clearIdentity();
			self::$currentAuth->getStorage()->clear();
			$this->_forward('login', null, null, array('oldAction' => $this->getRequest()->getActionName()));
			return;
		}
	}

	public function postDispatch() {
		if (in_array($this->getRequest()->getActionName(), array('login', 'logout', 'deny','csv'))) {
			return;
		}

		if (self::$currentAuth->hasIdentity()) {
			$this->view->acl = ArOn_Acl_Controlv2::getInstance();
		}
	}

	public function loginAction(){
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
				$adapter = ArOn_Acl_Auth_Admin::getDbAuthAdapter();
				$adapter->setIdentity($form->getValue('login'))->setCredential($form->getValue('password'));
				$result = self::$currentAuth->authenticate($adapter);
				if ($result->isValid()) {
					self::$currentAuth->getStorage()->write(ArOn_Acl_Controlv2::toStorage($result->getIdentity()));
					//$this->_forward($this->_getParam('oldAction'));
					
				 	//if(!empty($params['save_login']) && $params['save_login'] == 'yes'){
			                $salt = (defined('PASSWORD_SALT')) ? PASSWORD_SALT: 'login';
			                $life_time = (defined('COOKIE_LIFE_TIME')) ? time() + COOKIE_LIFE_TIME: TIME() + 60*60*24*7;
				            $crypt = new ArOn_Crud_Tools_Crypt($salt);	           
			                $this->userData = self::$currentAuth->getStorage()->read();
			                $code = $crypt->encUserId($this->userData['user']['id']);                
			                $myDomain = preg_replace('/^[^\.]*\.([^\.]*)\.(.*)$/i', '$1.$2',$_SERVER['HTTP_HOST']);
			                setcookie(self::$currentAuth->loginCookie,$code,$life_time,'/');//,$myDomain,false);
				    //}
					
					echo "{success: true}";
					return;
				}
				else {
					//$this->view->error = 'Wrong auth!';
					echo "{success: false, message: 'Неверный логин либо пароль!<br/>Проверьте данные и попробуйте еще раз.'}";
					return;
				}
			}
		}

		$form->getElement('submit')->setValue('Login');
		$this->view->form = $form;
		$this->render('login/login', null, true);
	}

	public function logoutAction() {
		self::$currentAuth->clearIdentity();
		Zend_Session::forgetMe();
		setcookie(self::$currentAuth->loginCookie,"-",time(),'/');
		$this->_redirect('/cms');
	}

	public function denyAction() {
		$this->render('deny', null, true);
	}
}