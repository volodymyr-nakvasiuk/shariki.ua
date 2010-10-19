<?php
class Cms_FormController extends Abstract_Controller_CmsController {

	/**
	 * @var ArOn_Crud_Form_ExtJs
	 */
	protected $_form;

	public function init(){
		parent::init();
		$this->_helper->viewRenderer->setNoRender();
	}
	
	
	public function __call($function,$params){

		$count = preg_match('/(.*?)Action/',$function,$matches);

		if ($count) {
			$action = $this->_request->getParam('grid_action');
			$name = ucfirst($matches[1]);
			$name = str_replace('Acl','Acl_',$name);
			$classname = 'Crud_Form_ExtJs_'.ucfirst($name);
			
			/**
			 * @var ArOn_Crud_Form
			 */
			$this->_form = new $classname($this->_request->getParam('id'), $this->_request->getParams());
			$this->_form->createForm();
			switch ($action){
				case 'index':
					echo $this->_form->render();
					break;
				case 'create':
					echo $this->_form->render();
					break;
				case 'edit':
					echo $this->_form->render();
					break;
				case 'remove':
					$ids = Zend_Json::decode($this->_request->getParam('ids'));
					$model = $this->_form->getModel();
					//$model = new $Dbclass();
					$key = $model->getPrimary();
					foreach($ids as $id){
						$model->delete($model->getAdapter()->quoteInto($key." = ?",$id));
					}
					echo '{success: true, message: "Успешно удалено!"}';
					break;
				case 'duplicate':
					$ids = Zend_Json::decode($this->_request->getParam('ids'));
					$model = $this->_form->getModel();
					$model->duplicate($ids);
					echo '{success: true, message: "Успешно клонировано!"}';
					break;
				case 'save':
					//move_uploaded_file ($_FILES['logo']['tmp_name'], './uploads/images/'.basename($_FILES['logo']['name'])); exit();
					$params = $this->_request->getParams();
				
					if($this->_form->isValid($params)){
						$id = $this->_form->saveValidData();
						if(is_array($id) && array_key_exists('error',$id)){
							echo '{success: false, message: "'.$id['error'].'"}';
							$id = false;
						}else{
							echo "{success: true}";
						}
					}else{
						$err = $this->_form->getErrors();
						$err_msg = '';
						foreach($err as $field_name => $field){
							if (!empty($field)){
								$err_msg .= '<b>'.$this->_form->fields[$field_name]->getTitle().'</b>:<br/>';
								foreach($field as $error) $err_msg .= ' - '.$error.'<br/>';
								$err_msg .= '<br/>';
							}
						}
						echo '{success: false, message: "'.$err_msg.'"}';
					}
					break;
			}
			return true;
		}
		return false;
	}
	
	public function fgalleryAction(){
		$action = $this->_request->getParam('grid_action');
		switch ($action){
			case 'create':
				$generation_id = $this->_request->getParam('parent_id');
				if ($generation_id){
					$grid = new Crud_Grid_Generation(null, array('id' => $generation_id));
					$data = $grid->getData();
					if (isset($data['data'][0])){
						$this->view->data = $data['data'][0];
						
						$grid = new Crud_Grid_Article(null, array('parent' => $generation_id));
						$data = $grid->getData();
						$this->view->articles = $data['data'];
						
						echo $this->view->render('fgallery/form.phtml');
					}
				}
				break;
			case 'save':
				break;
			case 'edit':
				$tree_val = $this->_request->getParam('tree_val');
				$node = $this->_request->getParam('node');
				$retrn = array();
				if ($tree_val){
					$pre_str  = '{succes: true,rows: ';
					$post_str = '}';
					$dir = UPLOAD_CATALOG_FLASH_PATH.'/'.$tree_val;
					if (is_dir($dir)){
						if ($handle = opendir($dir)) {
						    while (false !== ($file = readdir($handle))) { 
						        if ($file != "." && $file != ".." && is_file($dir.'/'.$file)) { 
						            $retrn[] = '{optionValue: "'.$tree_val.'/'.$file.'", displayText:"'.$file.'"}';
						        } 
						    }
						    closedir($handle); 
						}
					}
				}
				elseif ($node){
					$pre_str  = '';
					$post_str = '';
					$dir = UPLOAD_CATALOG_FLASH_PATH.'/'.$node;
					if (is_dir($dir)){
						if ($handle = opendir($dir)) {
						    while (false !== ($file = readdir($handle))) { 
						        if ($file != "." && $file != ".." && is_dir($dir.'/'.$file)) { 
						            $retrn[] = '{"text":"'.$file.'", "id":"'.$node.'/'.$file.'", "leaf":false}';
						        } 
						    }
						    closedir($handle); 
						}
					}
				}
				echo $pre_str.'['.implode(', ', $retrn).']'.$post_str;
				break;
			case 'remove':
				$ids = Zend_Json::decode($this->_request->getParam('ids'));
				$this->_form = new Crud_Form_ExtJs_Eflash();
				$this->_form->createForm();
				$model = $this->_form->getModel();
				//$model = new $Dbclass();
				$key = $model->getPrimary();
				foreach($ids as $id){
					$model->delete($model->getAdapter()->quoteInto($key." = ?",$id));
				}
				echo '{success: true, message: "Успешно удалено!"}';
				break;
		}
	}
	
	public function egalleryallAction(){
		$this->view->extModuleName = 'grid-win-egalleryall';
		$this->view->extParentWin = 'grid-win-egalleryall';
		return $this->egalleryAction();
	}
	
	public function egalleryAction(){
		$action = $this->_request->getParam('grid_action');
		switch ($action){
			case 'upload':
					$generation_id = $this->_request->getParam('parent_id');
					if ($generation_id){
						$params = $_POST;
						unset($params['controller'],$params['action'],$params['grid_action'],$params['module']);
						$form = new Crud_Grid_ExtJs_View_EGalleryForm(null, $params);
						echo $form->render();
					}
					else {
						echo '{success: false, message: "Выберите поколение!"}';
					}
				break;
			case 'create':
				$generation_id = $this->_request->getParam('parent_id');
				if ($generation_id){
					$grid = new Crud_Grid_Generation(null, array('id' => $generation_id));
					$data = $grid->getData();
					if (isset($data['data'][0])){
						$this->view->data = $data['data'][0];
						
						$grid = new Crud_Grid_Article(null, array('parent' => $generation_id));
						$data = $grid->getData();
						$this->view->articles = $data['data'];
						
						echo $this->view->render('egallery/form.phtml');
					}
				}
				else {
					echo '{success: false, message: "Выберите поколение!"}';
				}
				break;
			case 'save':
				$params = $this->_request->getParams();
				$this->_form = new Crud_Form_ExtJs_Egallery();
				$this->_form->createForm();
				if($this->_form->isValid($params)){
					$car_id = $this->_form->saveValidData();
					if(is_array($car_id) && isset($cars_id['error'])){
						$car_id = false;
						echo '{success: false, message: "'.$cars_id['error'].'"}';
					}else{
						echo "{success: true}";
					}
				}else{
					$err = $this->_form->getErrors();
					$err_msg = '';
					foreach($err as $field_name => $field){
						if (!empty($field)){
							$err_msg .= '<b>'.$this->_form->fields[$field_name]->getTitle().'</b>:<br/>';
							foreach($field as $error) $err_msg .= ' - '.$error.'<br/>';
							$err_msg .= '<br/>';
						}
					}
					echo '{success: false, message: "'.$err_msg.'"}';
				}
				break;
			case 'edit':
				$tree_val = $this->_request->getParam('tree_val');
				$node = $this->_request->getParam('node');
				$retrn = array();
				if ($tree_val){
					$pre_str  = '{succes: true,rows: ';
					$post_str = '}';
					$dir = UPLOAD_CATALOG_IMAGES_PATH.'/generations/'.$tree_val;
					if (is_dir($dir)){
						if ($handle = opendir($dir)) {
						    while (false !== ($file = readdir($handle))) { 
						        if (strpos($file, "_orig.")===false && $file != "." && $file != ".." && is_file($dir.'/'.$file)) {
						        	$db = Db_Ephotos::getInstance();
						        	$data = $db->fetchRow("photos_name='".$tree_val."/".$file."'");
						        	if (!empty($data)) continue;
						            $retrn[] = '{optionValue: "'.$tree_val.'/'.$file.'", displayText:"'.$file.'"}';
						        } 
						    }
						    closedir($handle); 
						}
					}
				}
				elseif ($node){
					$pre_str  = '';
					$post_str = '';
					$dir = UPLOAD_CATALOG_IMAGES_PATH.'/generations/'.$node;
					if (is_dir($dir)){
						if ($handle = opendir($dir)) {
						    while (false !== ($file = readdir($handle))) { 
						        if ($file != "." && $file != ".." && is_dir($dir.'/'.$file)) {
						            $retrn[] = '{"text":"'.$file.'", "id":"'.$node.'/'.$file.'", "leaf":false}';
						        } 
						    }
						    closedir($handle); 
						}
					}
				}
				echo $pre_str.'['.implode(', ', $retrn).']'.$post_str;
				break;
			case 'remove':
				$ids = Zend_Json::decode($this->_request->getParam('ids'));
				$this->_form = new Crud_Form_ExtJs_Egallery();
				$this->_form->createForm();
				$model = $this->_form->getModel();
				//$model = new $Dbclass();
				$key = $model->getPrimary();
				foreach($ids as $id){
					$model->delete($model->getAdapter()->quoteInto($key." = ?",$id));
				}
				echo '{success: true, message: "Успешно удалено!"}';
				break;
		}
	}

}
