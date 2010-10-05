<?php

/**
 * CarController
 *
 * @author
 * @version
 */

class Cms_GridController extends Abstract_Controller_CmsController {

	public function indexAction(){
		$grid = new Crud_Grid_Catalog();
		$data = $grid->getData();
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender();
	}

	public function addAction() {
		//Core_Debug::getGenerateTime('dbcar');
		Zend_Debug::dump($_POST);
		$db_model = Db_Catalog::getInstance();
		Core_Debug::getGenerateTime('form');
		$form = new Crud_Form_Catalog();
		Core_Debug::getGenerateTime('start create form');
		$form->createForm();
		Core_Debug::getGenerateTime('end create form');
		//echo $form->render();
		//Core_Debug::getGenerateTime('end render form');
		//$db_model->insert($_POST);
		if($this->_request->isPost()){
			$photos = array();
			$image_folder = false;
			if(!empty($_POST['tmp_dir'])){
				$imagekeys = array('price_photo_01', 'price_photo_02', 'price_photo_03', 'price_photo_04', 'price_photo_05');
				$image_folder = str_replace('//','/',$_SERVER['DOCUMENT_ROOT'] . '/uploads/tmp/' . $_POST['tmp_dir'] . '/');
				if($images = $this->getFiles($image_folder)){
					if(($k = count($imagekeys)) > ($v = count($images))){
						$imagekeys = array_slice($imagekeys,0,$v);
					}else{
						$images = array_slice($images,0,$k);
					}
					$photos = array_combine($imagekeys, $images);
				}
			}
			if($form->isValid($_POST)){
				if(!empty($photos)) {
					$photos['price_photos'] = count($images);
					$_POST = array_merge($_POST,$photos);
				}
				$id = $db_model->insert($_POST);
				if(!empty($images)){
					$this->copyFolder($image_folder,$_SERVER['DOCUMENT_ROOT'] . '/uploads/images/' . $id . '/');
					$this->resizeImages($images,$_SERVER['DOCUMENT_ROOT'] . '/uploads/images/' . $id . '/');
				}
			}else {
				echo "no valid<br>";
				Zend_Debug::dump($form->getErrors());
			}
			if(!empty($_POST['tmp_dir'])){
				//$this->deleteFolder($image_folder);
			}
			//$this->_redirect('http://car-my.ua/?page=marks&id='.$id);
		}

		$this->getHelper ( 'layout' )->disableLayout ();
		$this->getHelper ( 'viewRenderer' )->setNoRender ();
	}



}
