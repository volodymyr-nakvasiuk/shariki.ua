<?php
class Client_FormController extends Client_AjaxController {

	protected $_form;
	public $resultJSON = array();

	public function __call($function, $params){
		$count = preg_match('/(.*?)Action/',$function,$matches);

		if ($count) {
			$action = $this->_request->getParam('grid_action');
			$name = ucfirst($matches[1]);
			//$name = str_replace(array('View'),array('View_'),$name);
			$classname = 'Crud_Form_Client_'.$name;
			$params = $this->_request->getParams();
			unset($params['controller'],$params['action'],$params['grid_action'],$params['module']);
			
			$this->_form = new $classname($this->_request->getParam('id'), $params);
			$this->_form->createForm();
			switch ($action){
				case 'index':
					$this->resultJSON['success'] = true;
					$this->resultJSON['form'] = array(
						'data'=>$this->_form->getData(),
						'renderdata'=>$this->_form->getRenderData(),
					);
					//$this->resultJSON['message'] = "Данные получены!";
					break;
				case 'restore':
					$model = $this->_form->getModel();
					$result = $model->restore($model->getAdapter()->quoteInto($model->getPrimary()." = ?",$this->_request->getParam('id')));
					if ($result){
						$this->resultJSON['success'] = true;
						$this->resultJSON['message'] = "Успешно восстановлено!";
					}
					else {
						$this->resultJSON['success'] = false;
						$this->resultJSON['message'] = "Ошибка, при попытке восстановления!";
					}
					break;
				case 'remove':
					$model = $this->_form->getModel();
					$result = $model->delete($model->getAdapter()->quoteInto($model->getPrimary()." = ?",$this->_request->getParam('id')));
					if ($result){
						$this->resultJSON['success'] = true;
						$this->resultJSON['message'] = "Успешно удалено!";
					}
					else {
						$this->resultJSON['success'] = false;
						$this->resultJSON['message'] = "Ошибка, при попытке удаления!";
					}
					break;
				case 'save':
					if($this->_form->isValid($params)){
						$id = $this->_form->saveValidData($this);
						if(is_array($id) && array_key_exists('error', $id)){
							$this->resultJSON['success'] = false;
							$this->resultJSON['message'] = "<h3>Ошибка, при попытке сохранить данные:</h3>".$id['error'];
							return false;
						}
						else{
							$this->resultJSON['success'] = true;
							$this->resultJSON['message'] = "Изменения успешно сохранены!";
						}
					}else{
						$err = $this->_form->getMessages();
						$err_msg = '';
						foreach($err as $field_name => $field){
							if (!empty($field)){
								$err_msg .= $this->_form->fields[$field_name]->getTitle().': ';
								foreach($field as $error) $err_msg .= '<code>'.$error.'</code>; ';
								$err_msg .= '<br/>';
							}
						}
						$this->resultJSON['success'] = false;
						$this->resultJSON['message'] = "<h3>Ошибки в данных:</h3>".$err_msg;
						return false;
					}
					break;
			}
			return true;
		}
		else {
			$this->resultJSON['success'] = false;
			$this->resultJSON['message'] = "Ошибка! Неверный запрос!";
			return false;
		}
	}
}
