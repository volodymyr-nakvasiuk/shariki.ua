<?php
class ArOn_Crud_Form_Field_Many2Many extends ArOn_Crud_Form_Field {

	protected $_type = 'multiCheckbox';
	protected $elementHelper = 'MyFormMultiCheckbox';
	protected $model;

	public $key;

	public $parent_key;
	public $rowClass = 'field_many2many';
	public $id;

	/**
	 * @var ArOn_Db_Table
	 */
	public $workingModel;
	public $workingModelOrder;

	public $optionName;
	public $category;
	public $categoryName;
	public $emptyCategory;
	public $emptyItem;
	public $where;
	public $fields = null;
	
	public $onchange;
	
	public $links = false;
	public $category_order = null;

	public $noTableReference = false;
	
	public function updateField() {
		parent::updateField ();
		if (is_string ( $this->model ))
		$this->model = ArOn_Crud_Tools_Registry::singleton ( $this->model );
		if (is_string ( $this->workingModel ))
		$this->workingModel = ArOn_Crud_Tools_Registry::singleton ( $this->workingModel );
		
		if (isset ( $this->key )) {
			throw new Exception ( "Outdated Many2Many settings for " . $this->form->getClass ()  . " - " . $this->model->getClass() );
		}
		
		if(!$this->noTableReference){
			$refModel = $this->workingModel->getReferenceEx ( $this->model->getClass() );
			$refFormModel = $this->workingModel->getReferenceEx ( $this->formModel->getClass() );

			$this->key = $refModel ['columns'];
			$this->parent_key = $refFormModel ['columns'];
		}
		
		if (! empty ( $this->id ) && !$this->noTableReference) {
			$select = $this->workingModel->select ()->columnsAll ()->where ( "$this->parent_key=?", $this->id );
			if ($this->workingModelOrder) {
				$select->order ( $this->workingModelOrder );
			}
			$selected_data = (($result = $this->workingModel->fetchAll ( $select )) === null) ? array() : $result->toArray();
			$selected_options = $this->selectedArray ( $selected_data );
		}

		$options_function = (null !== $this->fields) ? 'prepareOptionsAll' : 'prepareOptions';
		$select = $this->model->select ();
		$options = ArOn_Crud_Tools_Multiselect::$options_function ( $select, $this->optionName, $this->category, $this->categoryName, $this->where, $this->emptyCategory, $this->emptyItem, true, $this->fields, $this->category_order );
		if ($this->links) {
			$this->element->setAttrib ( 'action_links', $this->links );
		}
		$this->element->setAttrib ( 'class', 'multiCheckboxContent' );		
		$this->element->addMultiOptions ( $options );
		$this->element->setRegisterInArrayValidator ( false );
		if (! empty ( $this->id ) && !$this->noTableReference ) {
			$this->_value = $this->element->getValue ();
			$this->element->setValue ( $selected_options );
		}
		if (! empty ( $this->onchange ))
			$this->element->setAttrib ( 'onchange', $this->onchange );
	}

	protected function selectedArray($selected_data) {
		return ArOn_Crud_Tools_Array::assocToLinearArray ( $selected_data, $this->key );
	}

	public function getInsertData() {

		if (! $this->saveInDataBase)
		return false;

		$data = array ();
		if (empty ( $this->id ))
		return false;
		$post_values = $this->element->getValue ();
		if($this->explode) $post_values = explode($this->explode,$post_values);
		$select = $this->workingModel->select ();
		$select->where ( $this->workingModel->q ( "$this->parent_key=?", $this->id ) );
		$saved_data = (($result = $this->workingModel->fetchAll ( $select )) === null) ? array() : $result->toArray();
		$saved_values = array ();
		$working_id = $this->workingModel->info ( 'primary' );
		if (count ( $working_id ) == 1)
		$working_id = $working_id [1];

		foreach ( $saved_data as $value ) {
			if (is_array ( $working_id )) {
				$primary = array ();
				foreach ( $working_id as $key ) {
					$primary [] = $value [$key];
				}
				$primary = implode ( "\0", $primary );
			} else {
				$primary = $value [$working_id];
			}
			$saved_values [$primary] = $value [$this->key];
		}
		if (! is_array ( $saved_values ))
		$saved_values = array ();
		if (! is_array ( $post_values ))
		$post_values = array ();
		$delete = array_diff ( $saved_values, $post_values );
		$insert = array_diff ( $post_values, $saved_values );
		$data ['insert'] = array ();
		$data ['delete'] = array ();
		if (is_array ( $insert )) {
			foreach ( $insert as $v ) {
				$data ['insert'] [] = array ($this->parent_key => $this->id, $this->key => $v );
			}
		} elseif (! empty ( $insert )) {
			$data ['insert'] [] = array ($this->parent_key => $this->id, $this->key => $insert );
		}
		if (is_array ( $delete )) {
			foreach ( $delete as $v ) {
				$data ['delete'] [] = $this->workingModel->q ( "$this->parent_key=?", $this->id ) . " AND " . $this->workingModel->q ( "$this->key=?", $v );
			}
		} elseif (! empty ( $delete )) {
			$data ['delete'] [] = $this->workingModel->q ( "$this->parent_key=?", $this->id ) . " AND " . $this->workingModel->q ( "$this->key=?", $delete );
		}
		$db = $this->model->getAdapter ();
		$db->beginTransaction ();
		try {
			foreach ( $data ['delete'] as $delete ) {
				$this->workingModel->delete ( $delete );
			}
			foreach ( $data ['insert'] as $insert ) {
				$this->workingModel->insert ( $insert );
			}
			$db->commit ();

		} catch ( Exception $e ) {
			$db->rollBack ();
			echo $e->getMessage ();
			die ();
		}

		return true;
	}

	protected function updateMany2ManySelect(ArOn_Db_TableSelect $select) {
		return $select;
	}

	/*public function setValue(){
	  
	$selected_data = $this->workingModel->getList($this->workingModel->getAdapter()->quoteInto("$this->parent_key=?",$this->id));
	$selected_options = Crud_Tools_Array::assocToLinearArray($selected_data,$this->key);
	$this->element->setValue($selected_options);
		
	}*/
}
