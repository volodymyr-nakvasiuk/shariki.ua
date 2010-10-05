<?php
class ArOn_Crud_Grid_Filter_Field_Select extends ArOn_Crud_Grid_Filter_Field {

	protected $_type = 'select';

	public $model;

	public $key;

	public $id;

	public $optionName = 'name';

	public $optionKey = 'id';

	public $notAll = false;

	public function updateField() {
		parent::updateField ();
		if (! is_object ( $this->model ))
		return;
		$table = $this->model->getTableName ();
		$select = $this->model->getCurrentSelect ();
		$select->reset ( 'from' );
		$select->reset ( 'columns' );
		$select->reset ( 'order' );
		$select->from ( $table, array ('name' => $this->optionName, 'id' => $this->optionKey ) );
		$select->order ( 'name' );
		$this->model->setCurrentSelect ( $select );
		$data = $this->model->getList ();
		$options = Crud_Tools_Array::arrayToAssoc ( $data, 'name', 'id' );
		if (! $this->notAll)
		$this->element->addMultiOption ( 0, 'All' );
		$this->element->addMultiOptions ( $options );

	}

	public function getFieldWhere() {
		$value = $this->getFieldValue ();
		if (! empty ( $value )) {
			$table = $this->model->getTableName ();
			$where = "$this->table.$this->name = " . ArOn_Crud_Tools_String::quote ( $value );
			return $where;
		}
	}
}