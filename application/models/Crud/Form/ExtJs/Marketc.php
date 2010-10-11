<?php
class Crud_Form_ExtJs_Marketc extends ArOn_Crud_Form_ExtJs
{
	protected $modelName = 'Db_Marketc';
	protected $_title = 'Розничная торговля - Категория';

	public function init ()
	{
		$this->action = '/' . self::$ajaxModuleName . '/marketc/save/';
		$this->actionName = 'marketc';

		$this->fields = array(
			'id' => new ArOn_Crud_Form_Field_Numeric('marketc_id', 'Id', null, true, true) ,
			'marketc_img' => new ArOn_Crud_Form_Field_AdminImageUpload('marketc_img',UPLOAD_IMAGES_PATH.'/market_cat', '{id}/{sha}', true,'Изображение', null, '5242880', false, false, 150, true, array('big'=>'620x465','middle'=>'240x180','small'=>'140x105'), false),
			'title' => new ArOn_Crud_Form_Field_Text('marketc_title', 'Название', null, true) ,
			'text' => new ArOn_Crud_Form_Field_TextArea('marketc_text', 'Описание', null, false),
			'order' => new ArOn_Crud_Form_Field_Numeric('marketc_order', 'Порядок сортировки', null, true) ,
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