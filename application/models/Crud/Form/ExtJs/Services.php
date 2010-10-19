<?php
class Crud_Form_ExtJs_Services extends ArOn_Crud_Form_ExtJs
{
	protected $modelName = 'Db_Services';
	protected $_title = 'Описание';

	public function init ()
	{
		$this->action = '/' . self::$ajaxModuleName . '/services/save/';
		$this->actionName = 'services';

		$this->fields = array(
			'id' => new ArOn_Crud_Form_Field_Numeric('services_id', 'Id', null, true, true) ,
			'services_photo' => new ArOn_Crud_Form_Field_AdminImageUpload('services_photo',UPLOAD_IMAGES_PATH.'/services', '{id}/{sha}', true,'Изображение', null, '5242880', false, false, 150, true, array('big'=>'620x465','middle'=>'240x180','small'=>'140x105'), false),
			'title' => new ArOn_Crud_Form_Field_Text('services_title', 'Название', null, true) ,
			'text' => new ArOn_Crud_Form_Field_TextArea('services_text', 'Описание', null, false),
			'order' => new ArOn_Crud_Form_Field_Numeric('services_order', 'Порядок сортировки', null, true) ,
		);
		if(empty($this->actionId)){
			unset($this->fields['id']);
		}
		else {
			$this->_alternative_data['is_deleted'] = 0;
			$this->fields['id']->setElementHelper('formNotEdit');
		}
		
		$this->fields['order']->setValue(50);
		
		$this->groups = array('0' => array_keys($this->fields));
		
		parent::init();
	}
}