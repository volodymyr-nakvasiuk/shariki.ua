<?php
class Init_Extjs{
	
	protected $_menu;
	protected $_view;
	protected $_session;
	
	public function __construct($view, $session, $menu = 'MENUCLIENT'){
		$this->_view = $view;
		$this->_session = $session;
		$this->_menu = $menu;
	}
	
	public function initClientMode(){
		$this->initMenu();
		$this->initClientJs();
		$this->initCss();
		
		$initGridWindows = new Init_Gridwin($this->_view);
		$initGridWindows->restoreWindows();	
	}
	
	protected function initClientJs(){
		$this->_view->headScript()
		->appendFile('/cms/js/extjs/adapter/ext/ext-base.js', 'text/javascript', array('non-cache'=>true))
		->appendFile('/cms/js/extjs/ext-all.js', 'text/javascript', array('non-cache'=>true))
		->appendFile('/cms/js/extjs/ux/utils/Ext.ux.utils.nv.js', 'text/javascript', array('non-cache'=>true))
		->appendFile('/cms/js/extjs/desktop/StartMenu.js', 'text/javascript', array('non-cache'=>true))
		->appendFile('/cms/js/extjs/desktop/TaskBarClient.js', 'text/javascript', array('non-cache'=>true))
		//->appendFile('/cms/js/extjs/desktop/TaskBar.js', 'text/javascript', array('non-cache'=>true))
		->appendFile('/cms/js/extjs/desktop/Desktop.js', 'text/javascript', array('non-cache'=>true))
		->appendFile('/cms/js/extjs/desktop/App.js', 'text/javascript', array('non-cache'=>true))
		->appendFile('/cms/js/extjs/desktop/Module.js', 'text/javascript', array('non-cache'=>true))
		//->appendFile('/cms/js/desktop/client-sample.js', 'text/javascript', array('non-cache'=>true))
		->appendFile('/cms/js/extjs/form/MultiSelect.js', 'text/javascript', array('non-cache'=>true))
		->appendFile('/cms/js/extjs/form/ColorField.js', 'text/javascript', array('non-cache'=>true))
		->appendFile('/cms/js/extjs/ux/FileUploadField.js', 'text/javascript', array('non-cache'=>true))
		->appendFile('/cms/js/extjs/ux/plugins/Ext.ux.plugins.HtmlEditorImageInsert.js', 'text/javascript', array('non-cache'=>true))
		->appendFile('/cms/js/extjs/ux/plugins/Ext.ux.plugins.HtmlEditorFullScreen.js', 'text/javascript', array('non-cache'=>true))
		->appendFile('/cms/js/extjs/ux/plugins/Ext.ux.HtmlEditor.Plugins-0.2-all.js', 'text/javascript', array('non-cache'=>true))
		->appendFile('/cms/js/extjs/ux/IFrame.js', 'text/javascript', array('non-cache'=>true))
		->appendFile('/cms/js/extjs/ux/Ext.ux.UploadDialog.js', 'text/javascript', array('non-cache'=>true))//http://max-bazhenov.com/dev/upload-dialog-2.0/index.php
		->appendFile('/cms/js/extjs/locale/ru.utf-8.js', 'text/javascript', array('non-cache'=>true))
		->appendFile('/cms/js/extjs/src/locale/ext-lang-ru.js', 'text/javascript', array('non-cache'=>true))
		;
	}
	
	protected function initCss(){
		$this->_view->headLink()->appendStylesheet(array('href'=>'/cms/js/extjs/resources/css/ext-all-car.css', 'non-cache'=>true, 'media'=>'screen', 'rel'=>'stylesheet', 'type'=>'text/css'))
		->appendStylesheet(array('href'=>'/cms/css/desktop.css', 'non-cache'=>true, 'media'=>'screen', 'rel'=>'stylesheet', 'type'=>'text/css'))
		->appendStylesheet(array('href'=>'/cms/css/icons.css', 'non-cache'=>true, 'media'=>'screen', 'rel'=>'stylesheet', 'type'=>'text/css'))
		->appendStylesheet(array('href'=>'/cms/css/file-upload.css', 'non-cache'=>true, 'media'=>'screen', 'rel'=>'stylesheet', 'type'=>'text/css'))
		->appendStylesheet(array('href'=>'/cms/css/Ext.ux.UploadDialog.css', 'non-cache'=>true, 'media'=>'screen', 'rel'=>'stylesheet', 'type'=>'text/css'))
		;
	}

	protected function initMenu(){
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
		//$this->initCmp();
	}
	
	protected function initCmp(){
		$has_new_cmp = false;
		
		$this->_view->cmpWin = $has_new_cmp;
		return false;
	}
}