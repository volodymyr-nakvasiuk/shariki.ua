<?php
class Crud_Form_ExtJs_Marketdgallery extends ArOn_Crud_Form_ExtJs
{
	protected $modelName = 'Db_View_MarketdPhotos';
	protected $_title = 'Фото';

	public function init ()
	{
		$this->_main_grid = 'marketd';
		$this->action = '/' . self::$ajaxModuleName . '/Marketdgallery/save/';
		$this->actionName = 'marketdgallery';
		$this->_parent_grid_id = 'tabs-win-marketd';

		$this->fields = array(
			//'id' => new ArOn_Crud_Form_Field_Numeric('photos_id', 'Id', null, true) ,
			'parent_id' => new ArOn_Crud_Form_Field_Text('photos_parent_id','-'),
			'title' => new ArOn_Crud_Form_Field_Text('photos_title','-'),
			'photos_name' => new ArOn_Crud_Form_Field_AdminImageUpload('photos_name',UPLOAD_IMAGES_PATH.'/market_tov', '{parent_id}/{sha}', true, 'Изображение', null, '5242880', false, false, 150, true, array('big'=>'620x465','middle'=>'240x180','small'=>'140x105'), false),
			'order' => new ArOn_Crud_Form_Field_Text('photos_order', '-'),
			'main' => new ArOn_Crud_Form_Field_Array2Radio('photos_main','-'),
		);
		//if(empty($this->actionId)){
		//	unset($this->fields['id']);
		//}
		if (!empty($_POST['parent_id'])){
			$this->fields['parent_id']->setValue($_POST['parent_id']);
		}
		$this->fields['order']->setValue(50);
		$this->fields['main']->setOptions(array(1 => ''));
		//$this->fields['main']->setValue(false);

		$this->groups = array('0' => array_keys($this->fields));
		parent::init();
	}

	public function saveValidData(){
		$this->_alternative_data['photos_type'] = 'market';
		$result = parent::saveValidData();
		return $result;
	}
}
