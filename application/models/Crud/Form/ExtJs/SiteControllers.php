<?php
class Crud_Form_ExtJs_SiteControllers extends ArOn_Crud_Form_ExtJs
{
	protected $modelName = 'Db_SiteController';
	protected $_title = 'Контроллер сайта';

	public function init ()
	{
		$this->action = '/' . self::$ajaxModuleName . '/site-controllers/save/';
		$this->actionName = 'site-controllers';

		$this->fields = array(
        	'id' => new ArOn_Crud_Form_Field_Numeric('controller_id', 'Id', null, true) ,
        	'sitemodule' => new ArOn_Crud_Form_Field_Many2One('controller_module_id','Модуль'),
        	'name' => new ArOn_Crud_Form_Field_Text('controller_name', 'Alt имя', null, true) ,        	
			'status' => new ArOn_Crud_Form_Field_Array2Select('controller_status', 'Статус', null, true)	
		);	
	
		$this->fields['status']->setOptions(array('active' => "Активный", 'not_active' => "Не активный"));		
		$this->fields['sitemodule']->model = 'Db_SiteModule';
		if(empty($this->actionId)) unset($this->fields['id']);
		else $this->fields['id']->setElementHelper('formNotEdit');
		
		$this->groups = array('0' => array_keys($this->fields));
		parent::init();


	}
}
