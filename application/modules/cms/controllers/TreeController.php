<?php
class Cms_TreeController extends Abstract_Controller_CmsController {

	protected $_tree;

	public function init(){
		parent::init();
		$this->_helper->viewRenderer->setNoRender();
	}

	public function __call($function,$params){

		$count = preg_match('/(.*?)Action/',$function,$matches);

		if ($count) {
			$action = $this->_request->getParam('grid_action');
			$name = ucfirst($matches[1]);
			$name = str_replace(array('View'),array('View_'),$name);
			$classname = 'Crud_Tree_ExtJs_'.ucfirst($name);
			//$params = $this->_request->getParams();
			$params = $_POST;
			unset($params['controller'],$params['action'],$params['grid_action'],$params['module']);
			if(!class_exists($classname)) return false;
			$this->_tree = new $classname($params);
			switch ($action){
				case 'index':
					echo $this->_tree->render();
					break;
				case 'node':
					echo $this->_tree->renderNode();
					break;
				case 'data':
					echo $this->_tree->renderData();
					break;
			}
			return true;
		}
		return false;
	}


}
