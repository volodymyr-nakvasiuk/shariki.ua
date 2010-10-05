<?php

/**
 * CarController
 *
 * @author
 * @version
 */

class AjaxController extends Abstract_Controller_FrontendController {
	
	public function preDispatch() {}
	
	public function init(){
		if(!$this->_request->isXmlHttpRequest()){
			$this->_redirect('/error/error');
		}
		parent::init();
		$this->_helper->layout->disableLayout();
	}
	
	public function postDispatch(){}
	
	public function searchAction(){
		$this->_helper->viewRenderer->setNoRender();
		if($this->_request->isPost()){
			$params = $this->_request->getPost();
			$search = new Init_Search($params);
			$data = $search->getData();
			echo Zend_Json::encode($data);
			unset($search);
		}
	}
	
	public function getimagesAction(){
		$id = $this->_request->getParam('model_id');
		$this->view->data = array();
		if(empty($id)) return false;
		$model = Db_Model::getInstance();
		$data = $model->getRowById($id);
		if(empty($data['model_catalog_id'])) return false;
		
		$model = Db_Generation::getInstance();
		$select = $model->select();
		$select->where("parent_id = ".$data['model_catalog_id']);
		$data = $model->fetchAll($select);
		$data = $data->toArray();
		$data_gallery = array();
		$ids = array();
		foreach ($data as $generation){
			$ids[] = $generation['generation_id'];
		}
		if(empty($ids)) return false;
		$model = Db_Ephotos::getInstance();
		$select = $model->select();
		$select->where("photos_visible = 1 AND parent_id IN (" . implode(",",$ids) .")");
		$data_gallery = $model->fetchAll($select)->toArray();
		foreach ($data_gallery as $key => $image){
			$data_gallery[$key]['image'] = Tools_Bmsimages::getCatalogImage($image['parent_id'],$image['photos_name'],'middle');
			if($data_gallery[$key]['image'] === false){
				unset($data_gallery[$key]);
			}
		}	
		$this->view->data = $data_gallery;
	}
		
	protected function getTestGalleryFiles($id){
		$images = array();
		$dir = PARSED_CATALOG_TESTS_IMAGES_PATH."/".$id;
		//if(!file_exists($dir)) return false;
		$dir = dir($dir);		
		while (($file = $dir->read()) !== false)
			if($file != "." && $file != ".." && (strpos($file,'_sm') !== false) ) {
				$images[] = array('small' => $file,'big' => str_replace('_sm','',$file));
			}
		
		$dir->close();
		return $images;
	}
	
	public function getfmodelAction(){
		$this->view->filterParams = $this->_request->getParams();
	}
	
	public function getcarsAction(){
		$this->view->filterParams = $this->_request->getParams();
	}
	
	protected function setup(){
		$this->initCache();
	}
	
	public function emptyAction(){
	}
	
	public function vipAction(){
		$id = $this->_request->getParam('car_id');
		$this->view->id = $id;
		$form = new Crud_Form_Car($id);
		$form->createForm();
		$this->view->carData = $form->getRenderData();
		$car = new Init_Car($id, $this->view->carData['type']);
		$this->view->photos = $car->getPhotos();
	}
}
