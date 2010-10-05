<?php
class Client_GridController extends Client_AjaxController {

	protected $_grid;
	public $resultJSON = array();

	public function __call($function, $params){

		$count = preg_match('/(.*?)Action/',$function,$matches);

		if ($count) {
			$action = $this->_request->getParam('grid_action');
			$name = ucfirst($matches[1]);
			//$name = str_replace(array('View'),array('View_'),$name);
			$classname = 'Crud_Grid_Client_'.ucfirst($name);
			$params = $this->_request->getParams();
			unset($params['controller'],$params['action'],$params['grid_action'],$params['module']);
			
			$this->_grid = new $classname(null,$params);
			switch ($action){
				case 'updatevalue':
					$id    = $this->_request->getParam('id');
					$name  = $this->_request->getParam('name');
					if ($id && $name &&	($field = $this->_grid->getFieldByName($name))){
						$value = $this->_request->getParam('value');
						$result = $field->updateField($id, $value);
						if (isset($result['errors'])){
							$fieldName = $field->form->getFieldNameByColumnName ( $name );
							$err_msg = $field->form->fields[$fieldName]->getTitle().': ';
							foreach($result['errors'] as $error) $err_msg .= '<code>'.$error.'</code>; ';
							$this->resultJSON['success'] = false;
							$this->resultJSON['message'] = "<h3>Ошибки, при попытке сохранить данные:</h3>".$err_msg;
						}
						else {
							$this->resultJSON['success'] = true;
							$this->resultJSON['message'] = "Параметр успешно изменен!";
						}
					}
					else {
						$this->resultJSON['success'] = false;
						$this->resultJSON['message'] = "Ошибка! Неверный набор параметров в запросе!";
					}
					break;
				case 'index':
					$result = $this->_grid->render();
					if ($result){
						$this->resultJSON['comments'] = $result['data'];
						$this->resultJSON['paging'] = $result['array_pages'];
						//$this->resultJSON['message'] = "Ваши данные!";
					}
					else {
						$this->resultJSON['success'] = false;
						$this->resultJSON['message'] = "Ошибка при попытке получить данные!";
					}
					break;
				/*
				case 'list':
					echo $this->_grid->renderBody();
					break;
				*/
			}
			return true;
		}
		return false;
	}


}
