<?php
class Crud_Form_ExtJs_Shortcuts extends ArOn_Crud_Form_ExtJs
{
	protected $modelName = 'Db_Shortcuts';
	protected $_title = 'Ярлык на рабочий стол';

	public function init ()
	{
		$this->action = '/' . self::$ajaxModuleName . '/shortcuts/save/';
		$this->actionName = 'shortcuts';

		$this->fields = array(
        	'id' => new ArOn_Crud_Form_Field_Numeric('shortcut_id', 'Id', null, true, true) , 
        	'shortcut_module' => new ArOn_Crud_Form_Field_Text('shortcut_module', 'Модуль', null, true) ,
        	'shortcut_text' => new ArOn_Crud_Form_Field_Text('shortcut_text', 'Подпись', null, true) ,
			'shortcut_icon' => new ArOn_Crud_Form_Field_FileUpload('shortcut_icon', UPLOAD_CMS_IMAGES_PATH, 'icons/desktop/{sha}', false, 'Иконка (48x48)', null, '5242880',true,false,150,'jpg,png,gif,jpeg,bmp'),
		);
		
		$this->setData($this->getAttribs());
		
		if(empty($this->actionId)) unset($this->fields['id']);
		else $this->fields['id']->setElementHelper('formNotEdit');
		//if(!empty($this->actionId)) $this->fields['link']->notEdit = true;

		$this->groups = array('0' => array_keys($this->fields));
		parent::init();


	}

}
