<?php
class Crud_Form_ExtJs_SiteModules extends ArOn_Crud_Form_ExtJs
{
	protected $modelName = 'Db_SiteModule';
	protected $_title = 'Модуль сайта';

	public function init ()
	{
		$this->action = '/' . self::$ajaxModuleName . '/site-modules/save/';
		$this->actionName = 'site-modules';

		$this->fields = array(
        	'id' => new ArOn_Crud_Form_Field_Numeric('module_id', 'Id', null, true, true) , 
        	'name' => new ArOn_Crud_Form_Field_Text('module_name', 'Alt имя', null, true) ,
        	'status' => new ArOn_Crud_Form_Field_Array2Select('module_status', 'Статус', null, true)
		);
		$this->fields['status']->setOptions(array('active' => "Активный", 'not_active' => "Не активный"));		
		if(empty($this->actionId)) unset($this->fields['id']);
		else $this->fields['id']->setElementHelper('formNotEdit');;

		$this->groups = array('0' => array_keys($this->fields));
		parent::init();


	}
}
