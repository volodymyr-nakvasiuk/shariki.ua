<?php
class ArOn_Crud_Grid_ExtJs_GalleryGrid extends ArOn_Crud_Grid_ExtJs {
	
	public $editController = 'grid';
	//public $editAction = false; <- must be set
	
	protected $actions = array (
		'create' => array(
			'active' => true,
			'text'   =>'Добавить',
    		'tooltip'=>'Создать новую запись',
		),
		'remove' => array(
			'active' => true,
			'text'   =>'Удалить',
    		'tooltip'=>'Удалить выделенные записи',
		),
	);
	
	public function init() {
		if(isset( $this->_params['id']))
			$this->setBaseParam('parent_id', $this->_params['id']);
		parent::init();
	}
	
	protected function renderAction_dblclick (){
		return "";
	}
}
