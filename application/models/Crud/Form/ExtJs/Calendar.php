<?php
class Crud_Form_ExtJs_Calendar extends ArOn_Crud_Form_ExtJs
{
	protected $modelName = 'Db_Calendar';
	protected $_title = 'Событие';

	public function init ()
	{
		$this->action = '/' . self::$ajaxModuleName . '/calendar/save/';
		$this->actionName = 'calendar';

		$this->fields = array(
			'id' => new ArOn_Crud_Form_Field_Numeric('calendar_id', 'Id', null, true, true) ,
			'calendar_photo' => new ArOn_Crud_Form_Field_AdminImageUpload('calendar_photo',UPLOAD_IMAGES_PATH.'/calendar', '{id}/{sha}', true,'Изображение', null, '5242880', false, false, 150, true, array('big'=>'620x465','middle'=>'240x180','small'=>'140x105'), false),
			'title' => new ArOn_Crud_Form_Field_Text('calendar_title', 'Название', null, true) ,
			'short_description' => new ArOn_Crud_Form_Field_Text('calendar_description', 'Описание', null, false),
			'text' => new ArOn_Crud_Form_Field_TextArea('calendar_text', 'Полный текст', null, false),
			'date' => new ArOn_Crud_Form_Field_Date('calendar_date', 'Дата', null, null, null, true),
		);
		if(empty($this->actionId)){
			unset($this->fields['id']);
		}
		else {
			$this->fields['id']->setElementHelper('formNotEdit');
		}
		
		$this->groups = array('0' => array_keys($this->fields));
		
		parent::init();
	}
}