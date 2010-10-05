<?php
class Crud_Form_ExtJs_Team extends ArOn_Crud_Form_ExtJs
{
	protected $modelName = 'Db_Team';
	protected $_title = 'Член команды';

	public function init ()
	{
		$this->action = '/' . self::$ajaxModuleName . '/team/save/';
		$this->actionName = 'team';

		$this->fields = array(
			'id' => new ArOn_Crud_Form_Field_Numeric('team_id', 'Id', null, true, true) ,
			'team_photo' => new ArOn_Crud_Form_Field_AdminImageUpload('team_photo',UPLOAD_IMAGES_PATH.'/team', '{id}/{sha}', true,'Фото', null, '5242880', false, false, 150, true, array('big'=>'620x465','middle'=>'240x180','small'=>'140x105'), false),
			'name' => new ArOn_Crud_Form_Field_Text('team_name', 'Имя', null, true) ,
			'title' => new ArOn_Crud_Form_Field_Text('team_title', 'Должность', null, true) ,
			'order' => new ArOn_Crud_Form_Field_Numeric('team_order', 'Порядок сортировки', null, true) ,
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