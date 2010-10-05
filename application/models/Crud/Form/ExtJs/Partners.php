<?php
class Crud_Form_ExtJs_Partners extends ArOn_Crud_Form_ExtJs
{
	protected $modelName = 'Db_Partners';
	protected $_title = 'Партнер';

	public function init ()
	{
		$this->action = '/' . self::$ajaxModuleName . '/partners/save/';
		$this->actionName = 'partners';

		$this->fields = array(
			'id' => new ArOn_Crud_Form_Field_Numeric('partners_id', 'Id', null, true, true) ,
			'partners_logo' => new ArOn_Crud_Form_Field_AdminImageUpload('partners_logo',UPLOAD_IMAGES_PATH.'/partners', '{id}/{sha}', true,'Логотип', null, '5242880', false, false, 150, true, array('big'=>'620x465','middle'=>'240x180','small'=>'140x105'), false),
			'title' => new ArOn_Crud_Form_Field_Text('partners_title', 'Название', null, true) ,
			'url' => new ArOn_Crud_Form_Field_Text('partners_url', 'Ссылка', null, false),
			'order' => new ArOn_Crud_Form_Field_Numeric('partners_order', 'Порядок сортировки', null, true) ,
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

	public function saveValidData(){
		if (empty ( $this->actionId )) {
			$this->_alternative_data['news_created_date'] = date("Y-m-d 12:00:00", time());
		}
		parent::saveValidData();
	}

}