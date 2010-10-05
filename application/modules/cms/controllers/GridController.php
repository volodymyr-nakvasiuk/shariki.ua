<?php
class Cms_GridController extends Abstract_Controller_CmsController {

	/**
	 * @var ArOn_Crud_Grid_ExtJs
	 */
	protected $_grid;

	public function init(){
		parent::init();
		$this->_helper->viewRenderer->setNoRender();
	}

	public function __call($function,$params){

		$count = preg_match('/(.*?)Action/',$function,$matches);

		if ($count) {
			$action = $this->_request->getParam('grid_action');
			$name = ucfirst($matches[1]);
			$name = str_replace(array('Acl','View'),array('Acl_','View_'),$name);
			$classname = 'Crud_Grid_ExtJs_'.ucfirst($name);
			//$params = $this->_request->getParams();
			$params = $_POST;
			unset($params['controller'],$params['action'],$params['grid_action'],$params['module']);
			if(!class_exists($classname)) return false;
			$this->_grid = new $classname(null,$params);
			if ($this->_request->getParam('winstatus'))
				$this->_grid->setGridWindowStatus($this->_request->getParam('winstatus'));
			if ($this->_request->getParam('winstartblinking'))
				$this->_grid->setWindowBlinking(true);
			switch ($action){
				case 'updatevalue':
					if (($id = $this->_request->getParam('id'))     &&
					$this->_request->getParam('name')           &&
					($field = $this->_grid->getFieldByName($this->_request->getParam('name')))
					){
						$value = $this->_request->getParam('value');
						$result = $field->updateField($id,$value);
						if (isset($result['errors'])){
							echo "{success: false}";
						}
						else {
							echo "{success: true}";
						}
					}
					else {
						echo "{success: false}";
					}
					break;
				case 'index':
					echo $this->_grid->render();
					break;
				case 'list':
					echo $this->_grid->renderBody();
					break;
				case 'edit':
					echo $this->_grid->render();
					break;
				case 'create':
					echo $this->_grid->render();
					break;
			}
			return true;
		}
		return false;
	}


}
