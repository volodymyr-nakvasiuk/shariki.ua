<?php
class Crud_Form_ExtJs_Marketd extends ArOn_Crud_Form_ExtJs
{
	protected $modelName = 'Db_Marketd';
	protected $_title = 'Розничная торговля - Товар';

	public function init ()
	{
		$this->action = '/' . self::$ajaxModuleName . '/marketd/save/';
		$this->actionName = 'marketd';

		$this->fields = array(
			'id' => new ArOn_Crud_Form_Field_Numeric('marketd_id', 'Id', null, true, true) ,
			'marketc' => new ArOn_Crud_Form_Field_Many2One('marketc_id','Категория'),
			'marketd_img' => new ArOn_Crud_Form_Field_AdminImageUpload('marketd_img',UPLOAD_IMAGES_PATH.'/market_tov', '{id}/{sha}', true,'Изображение', null, '5242880', false, false, 150, true, array('big'=>'620x465','middle'=>'240x180','small'=>'140x105'), false),
			'text' => new ArOn_Crud_Form_Field_TextArea('marketd_text', 'Описание', null, false),
			'order' => new ArOn_Crud_Form_Field_Numeric('marketd_order', 'Порядок сортировки', null, true) ,
		);
		$this->fields['marketc']->model = 'Db_Marketc';
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
