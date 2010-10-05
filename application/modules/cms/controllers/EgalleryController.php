<?php
class Cms_EgalleryController extends Abstract_Controller_CmsController {

	public function init(){
		parent::init();
		//$this->_helper->viewRenderer->setNoRender();
	}

	public function indexAction(){

	}

	public function treeAction(){
		$this->_helper->viewRenderer->setNoRender();

		$table_name = 'Db_Emark';
		$where = '';

		if (isset($_REQUEST['node']) && $_REQUEST['node'] != 'src'){
			$node = explode('_',$_REQUEST['node']);
			$node_name = $node[0];
			$node_id   = $node[1];
			$where = '`parent_id` = '.$node_id;
		}

		switch ($node_name){
			case 'mark':
				$table_name = 'Db_Emodel';
				$child_name = 'model';
				$leaf       = 'false';
				break;
			case 'model':
				$table_name = 'Db_Generation';
				$child_name = 'generation';
				$leaf       = 'true';
				break;
			default:
				$table_name = 'Db_Emark';
				$child_name = 'mark';
				$leaf       = 'false';
		}
		$db = new $table_name();
		$select = $db->select();
		if (!empty($where))
		$select->where($where);
		$select->order($child_name.'_name');
		$rows = $db->fetchAll($select);
		$result = array();
		foreach($rows as $row){
			$result_param = array();
			$result_param[] = '"text":"'.$row[$child_name.'_name'].'"';
			$result_param[] = '"id":"'.$child_name.'_'.$row[$child_name.'_id'].'"';
			$result_param[] = '"leaf":'.$leaf;

			$result[] = '{'.implode(', ',$result_param).'}';
		}
		echo '['.implode(', ',$result).']';
		unset($result_param);
		unset($result);
	}

	public function getphotosAction(){
		$this->_helper->viewRenderer->setNoRender();
		if (isset($_REQUEST['node']) && $_REQUEST['node'] != 'src'){
			$node = explode('_',$_REQUEST['node']);
			$node_name = $node[0];
			$node_id   = $node[1];
			$grid = new Crud_Grid_ExtJs_Egalleryold(null,array('parent'=>$node_id));
			$grid->where = 'photos_type = 1';
			$data = $grid->getData();
			$result = array();
			$result['success'] = 'true';
			$result['results'] = $data['all_count'];
			$rows = array();
			foreach($data['data'] as $row_data){
				$row = array();
				$row['photos_id'   ] = $row_data['photos_id'   ];
				//$row['photos_path' ] = $row_data['mark_link'   ].'/'.$row_data['model_link'].'/'.$row_data['generation_link'].'/small/'.$row_data['photos_name'];
				$row['photos_path' ] = '../../../img/nophoto_s.gif';
				if (is_file(DOCUMENT_ROOT.'/catalog/images/generations/'.$node_id.'/small/'.$row_data['photos_name'])) {
					$row['photos_path' ] = $node_id.'/small/'.$row_data['photos_name'];
				}
				elseif (is_file(DOCUMENT_ROOT.'/catalog/images/generations/parsed/'.$row_data['photos_name'])) {
					$row['photos_path' ] = 'parsed/'.$row_data['photos_name'];
				}
				$row['photos_title'] = $row_data['photos_title'];
				$row['photos_main' ] = $row_data['photos_main' ];
				$row['photos_order'] = $row_data['photos_order'];
				$row['photos_visible'] = $row_data['photos_visible'];
				$rows[] = $row;
			}
			$result['rows'] = $rows;
			echo Zend_Json::encode($result);
		}
		else {
			echo '{success:false}';
		}
	}

	public function removeAction(){
		$this->_helper->viewRenderer->setNoRender();
		$ids = Zend_Json::decode($this->_request->getParam('ids'));
		foreach($ids as $id){
			$grid = new Crud_Grid_ExtJs_Egalleryold(null,array('id'=>$id));
			$data = $grid->getData();
			$row_data = $data['data'][0];
			$path = DOCUMENT_ROOT.'/catalog/images/generations/'.$row_data['parent_id'];
			$model = Db_Ephotos::getInstance();
			$key = $model->getPrimary();
			$model->delete($model->getAdapter()->quoteInto($key." = ?",$id));
			if(is_file($path.'/'.$row_data['photos_name']))  unlink($path.'/'.$row_data['photos_name']);
			if(is_file($path.'/small/'.$row_data['photos_name']))  unlink($path.'/small/'.$row_data['photos_name']);
			if(is_file($path.'/middle/'.$row_data['photos_name'])) unlink($path.'/middle/'.$row_data['photos_name']);
			if(is_file($path.'/big/'.$row_data['photos_name']))    unlink($path.'/big/'.$row_data['photos_name']);
		}
		echo '{success: true, message: "Успешно удалено!"}';
	}

	public function createAction(){
		$this->_helper->viewRenderer->setNoRender();
		$node = explode('_',$_REQUEST['node']);
		$node_name = $node[0];
		$node_id   = $node[1];
		$grid = new Crud_Grid_ExtJs_Generation(null,array('id'=>$node_id));
		$data = $grid->getData();
		$row_data = $data['data'][0];
		$form = new Crud_Form_ExtJs_Egallery();
		$form->default['generation-text'] = $row_data['mark_link'   ].' -> '.$row_data['model_link'].' -> '.$row_data['generation_name'];
		$form->default['generation'] = $node_id;
		$form->createForm();
		echo $form->render();
	}

	public function createmultiAction(){
		//$this->_helper->viewRenderer->setNoRender();
		$node = explode('_',$_REQUEST['node']);
		$node_name = $node[0];
		$node_id   = $node[1];
		$grid = new Crud_Grid_ExtJs_Generation(null,array('id'=>$node_id));
		$data = $grid->getData();
		//$row_data = $data['data'][0];
		$this->view->data = $data;
		//$form = new Crud_Form_ExtJs_Egallery();
		//$form->default['generation-text'] = $row_data['mark_link'   ].' -> '.$row_data['model_link'].' -> '.$row_data['generation_name'];
		//$form->default['generation'] = $node_id;
		//$form->createForm();
		//echo $form->render();
	}

	public function setvalueAction(){
		$this->_helper->viewRenderer->setNoRender();
		if (isset($_REQUEST['id']) && isset($_REQUEST['name']) && isset($_REQUEST['value'])){
			$id     = $_REQUEST['id'];
			$column = $_REQUEST['name'];
			$value  = $_REQUEST['value'];
			$model = Db_Ephotos::getInstance();
			$key = $model->getPrimary();
			//if ($column == 'photos_main') $model->update(array('photos_main' => 0), '');
			$model->update(array($column => $value), $key.' = '.$id);
			echo '{success: true, message: "Значение установлено!"}';
		}
		else{
			echo '{success: false, message: "Ошибка в данных"}';
		}

	}

}
