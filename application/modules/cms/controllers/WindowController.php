<?php
class Cms_WindowController extends Abstract_Controller_CmsController {

	/**
	 * @var ArOn_Crud_Window_ExtJs
	 */
	protected $_win;

	public function init(){
		parent::init();
		$this->_helper->viewRenderer->setNoRender();
	}

	public function __call($function,$params){

		$count = preg_match('/(.*?)Action/',$function,$matches);

		if ($count) {
			$action = $this->_request->getParam('grid_action');
			$name = ucfirst($matches[1]);
			$classname = 'Crud_Window_ExtJs_'.ucfirst($name);
			//$params = $this->_request->getParams();
			$params = $_POST;
			unset($params['controller'],$params['action'],$params['grid_action'],$params['module']);
			if(!class_exists($classname)) return false;
			$this->_win = new $classname($params);
			switch ($action){
				case 'index':
					echo $this->_win->render();
					break;
			}
			return true;
		}
		return false;
	}


}
