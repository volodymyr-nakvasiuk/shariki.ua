<?php
class Crud_Form_ExtJs_Photos extends ArOn_Crud_Form_ExtJs
{
	protected $modelName = 'Db_Photos';
	protected $_title = 'Фото';

	public function init ()
	{
		$this->action = '/' . self::$ajaxModuleName . '/photos/save/';
		$this->actionName = 'photos';

		$this->fields = array(
			'id' => new ArOn_Crud_Form_Field_Numeric('photos_id', 'Id', null, true, true) ,
			'photos_core' => new ArOn_Crud_Form_Field_AdminImageUpload('photos_core',UPLOAD_IMAGES_PATH.'/gallery', '{id}/{sha}', true,'Изображение', null, '5242880', false, false, 150, true, array('big'=>'700x525','middle'=>'340x240','small'=>'140x105'), false),
			'title' => new ArOn_Crud_Form_Field_Text('photos_title', 'Подпись', null, true) ,
			'order' => new ArOn_Crud_Form_Field_Numeric('photos_order', 'Порядок сортировки', null, true) ,
		);
		if(empty($this->actionId)){
			unset($this->fields['id']);
		}
		else {
			$this->fields['id']->setElementHelper('formNotEdit');
		}
		
		$this->fields['order']->setValue(50);
		
		$this->groups = array('0' => array_keys($this->fields));
		
		parent::init();
	}
}