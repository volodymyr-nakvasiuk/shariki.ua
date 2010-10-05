<?php
class Crud_Form_ExtJs_Indexmenu extends ArOn_Crud_Form_ExtJs
{
	protected $modelName = 'Db_Indexmenu';
	protected $_title = 'Раздел меню на главной';

	public function init ()
	{
		$this->action = '/' . self::$ajaxModuleName . '/indexmenu/save/';
		$this->actionName = 'indexmenu';

		$this->fields = array(
			'id' => new ArOn_Crud_Form_Field_Numeric('indexmenu_id', 'Id', null, true, true) , 
			'url' => new ArOn_Crud_Form_Field_Text('indexmenu_url', 'Относительная ссылка', null, true) ,
			'title' => new ArOn_Crud_Form_Field_Text('indexmenu_title', 'Подпись', null, true) ,
			'order' => new ArOn_Crud_Form_Field_Numeric('indexmenu_order', 'Порядок сортировки', null, true) ,
		);
		
		if(empty($this->actionId)) unset($this->fields['id']);
		else $this->fields['id']->setElementHelper('formNotEdit');
		
		$this->fields['order']->setValue(50);

		$this->groups = array('0' => array_keys($this->fields));
		parent::init();


	}

}
