<?php
class Crud_Grid_ExtJs_View_ServicesGalleryForm extends ArOn_Crud_Grid_ExtJs_View_GalleryForm{
	
	protected $windowTitle = 'Добавление фото к услуге';
	protected $_parentModule = 'services';
	protected $ajaxActionName = 'services-gallery';
	
	protected $_uploadUrl='/cms/form/servicesgallery/save';
	protected $_uploadFileName='photos_name';
	protected $_maxFileSize=5242880;
	//protected $_fileTypes=array('Картинки (*.jpg, *.jpeg, *.gif, *.png)'=>'*.jpg;*.jpeg;*.gif;*.png');
	protected $_uploadParams = array("title"=>"","order"=>"50","main"=>"0");
	
	public function init() {
		if (isset($this->_params['parent_id']))
			$this->_uploadParams['parent_id'] = $this->_params['parent_id'];
		parent::init();
	}
}