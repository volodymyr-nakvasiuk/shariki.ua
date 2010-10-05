<?php
class Crud_Form_ExtJs_Feedback extends ArOn_Crud_Form_ExtJs
{
	protected $modelName = 'Db_Feedback';
	protected $_title = 'Отзыв';

	public function init ()
	{
		$this->action = '/' . self::$ajaxModuleName . '/feedback/save/';
		$this->actionName = 'feedback';

		$this->fields = array(
			'id' => new ArOn_Crud_Form_Field_Numeric('feedback_id', 'Id', null, true, true) ,
			'title' => new ArOn_Crud_Form_Field_Text('feedback_title', 'Заголовок', null, true) ,
			'text' => new ArOn_Crud_Form_Field_TextArea('feedback_text', 'Отзыв', null, false),
			'order' => new ArOn_Crud_Form_Field_Numeric('feedback_order', 'Порядок сортировки', null, true) ,
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