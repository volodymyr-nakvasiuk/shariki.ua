<?php
class Crud_Grid_ExtJs_NewsTab extends ArOn_Crud_Grid_ExtJs_TabGrid  
{
	protected $modelName = 'Db_News';
	
	public function init(){
		$this->ajaxActionName = 'news';
		$this->gridTitle = 'Новость';
		$clear_form = false;
		if(empty($this->_params['id'])){
			$clear_form = true;
			$table = new $this->modelName;
			$insert = array('is_deleted' => 1);
			$id = $table->insert($insert);

			$this->actionId = $id;
			$this->grid_id = $id;
			
			$this->_params['id'] = $id;
		}
		$form = new Crud_Form_ExtJs_News($this->_params['id']);
		if($clear_form) $form->clearData()->setData(array('id' => $this->_params['id']));
		$this->_tabs = array(
			$form ,
			new Crud_Grid_ExtJs_Newsgallery(null,$this->_params),
		);
	}
}
