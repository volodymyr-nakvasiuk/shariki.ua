<?php
class Init_Client{
	
	protected $_view;
	protected $_session;
	protected $_menu;
	
	public function __construct($view, $session, $menu = 'MENUCLIENT'){
		$this->_view = $view;
		$this->_session = $session;
		$this->_menu = $menu;
	}
	
	public function initClientMode(){
		$this->initMenu();
		$this->initClientJs();
		$this->initClientCss();
	}
	
	protected function initClientJs(){
		//$this->_view->headScript()
		//->appendFile('/cms/js/extjs/adapter/ext/ext-base.js', 'text/javascript', array('non-cache'=>true))
		//;
	}
	
	protected function initClientCss(){
		//$this->_view->headLink()
		//->appendStylesheet(array('href'=>'/cms/js/extjs/resources/css/ext-all-car.css', 'non-cache'=>true, 'media'=>'screen', 'rel'=>'stylesheet', 'type'=>'text/css'))
		//;
	}

	protected function initMenu(){
		/*
		$menu = new ArOn_Menu_Bilder();
		$data = $menu->getMenuBilderArray();
		$this->_view->data = $data;
		$container = new Zend_Navigation($data);
		Zend_Registry::set('Zend_Navigation', $container);
		//$partial = array('menu.phtml', 'client');
		//$this->view->navigation()->menu()->setPartial($partial);
		$menu = $this->_view->navigation();
		$menu->setDefaultProxy('ExtJsMenu');
		$this->_view->menu = $this->_view->navigation()->render($menu->findOneByLabel($this->_menu));
		*/
	}

}