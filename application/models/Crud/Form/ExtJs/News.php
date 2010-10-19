<?php
class Crud_Form_ExtJs_News extends ArOn_Crud_Form_ExtJs
{
	protected $modelName = 'Db_News';
	protected $_title = 'Описание';

	public function init ()
	{
		$this->action = '/' . self::$ajaxModuleName . '/news/save/';
		$this->actionName = 'news';

		$this->fields = array(
			'id' => new ArOn_Crud_Form_Field_Numeric('news_id', 'Id', null, true, true) ,
			'news_photo' => new ArOn_Crud_Form_Field_AdminImageUpload('news_photo',UPLOAD_IMAGES_PATH.'/news', '{id}/{sha}', true,'Изображение', null, '5242880', false, false, 150, true, array('big'=>'620x465','middle'=>'240x180','small'=>'140x105'), false),
			'title' => new ArOn_Crud_Form_Field_Text('news_title', 'Название', null, true) ,
			'short_description' => new ArOn_Crud_Form_Field_Text('news_description', 'Описание', null, false),
			'text' => new ArOn_Crud_Form_Field_TextArea('news_text', 'Новость', null, false),
		);
		if(empty($this->actionId)){
			unset($this->fields['id']);
		}
		else {
			$this->_alternative_data['is_deleted'] = 0;
			$this->fields['id']->setElementHelper('formNotEdit');
		}
		
		$this->groups = array('0' => array_keys($this->fields));
		
		parent::init();
	}

	public function saveValidData(){
		if (empty ( $this->actionId )) {
			$this->_alternative_data['news_created_date'] = date("Y-m-d H:i:s", time());
		}
		parent::saveValidData();
	}

}