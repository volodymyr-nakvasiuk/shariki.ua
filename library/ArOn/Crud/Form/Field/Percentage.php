<?php
class ArOn_Crud_Form_Field_Percentage extends ArOn_Crud_Form_Field_Numeric {

	/**
	 * @var ArOn_Db_Table
	 */
	public $model;
	public $parent_key;
	protected $_covertType = 'string';

	public function updateField() {
		parent::updateField ();
		//$this->element->addValidator('StringLength', false, array(1,3));
		$this->element->addValidator ( 'Between', false, array ('messages' => array ('notBetween' => "'%value%' is not between '%min%' and '%max%'" ), 'Min' => 0, 'Max' => 100, 'Inclusive' => true ) );
		if (empty ( $this->id )) {
			return $this->element->setValue ( '50' );
		}
		if (is_string ( $this->model ))
		$this->model = ArOn_Crud_Tools_Registry::singleton ( $this->model );
		$data = $this->form->getData ();

		/*$id = $this->formModel->getPrimary();
		  
		if($this->form->template and in_array($this->name,$this->form->template_fields)){
		$id = 'template_id';
		}else {
		return $this->element->setValue('50');
		}*/
		$select = $this->model->select ()->where ( "`{$this->model->getTableName()}`.`{$this->parent_key}` = ?", $this->id )->order ( 'start_date DESC' );
		$row = $this->model->fetchRow ( $select );
		$data = ($row) ? $row->toArray () : array ();
		if (empty ( $data ['percentage'] )) {
			return $this->element->setValue ( 50 );
		}
		$this->element->setValue ( $data ['percentage'] );
	}

	public function getInsertData() {
		return false;
	}

	public function postSaveAction($data = null) {

		if (isset ( $data [$this->name] )) {
			if (is_string ( $this->model ))
			$this->model = ArOn_Crud_Tools_Registry::singleton ( $this->model );
				
			$id = $this->formModel->getPrimary ();
			$select = $this->model->select ()->where ( "`{$this->model->getTableName()}`.`{$this->parent_key}` = ?", $data [$id] )->order ( 'start_date DESC' );
			$row = $this->model->fetchRow ( $select );
			$last_data = ($row) ? $row->toArray () : array ();
			$now_date = date ( "Y-m-d" );
			if (empty ( $last_data )) {
				$last_data = array ('percentage' => false, 'start_date' => false );
			}
			if ($last_data ['percentage'] != $data [$this->name]) {
				$new_data = array ('percentage' => $data [$this->name], $this->parent_key => $data [$id], 'start_date' => $now_date );
				/*if($last_data['start_date'] == $now_date){
					$this->model->update($new_data,"`".$this->model->getTableName()."`.`".$this->model->getPrimary()."` = '".$last_data[$this->model->getPrimary()]."'");
					}else {
					$this->model->insert($new_data);
					}*/
				$adapter = $this->model->getAdapter ();
				$table = $this->model->getTableName ();

				$cols = array ();
				$vals = array ();
				foreach ( $new_data as $col => $val ) {
					$cols [] = $adapter->quoteIdentifier ( $col, true );
					$vals [] = '?';
				}

				// build the statement
				$sql = "REPLACE INTO " . $adapter->quoteIdentifier ( $table, true ) . ' (' . implode ( ', ', $cols ) . ') ' . 'VALUES (' . implode ( ', ', $vals ) . ')';

				// execute the statement and return the number of affected rows
				$adapter = $this->model->getAdapter ();
				$adapter->query ( $sql, array_values ( $new_data ) );
					
			}
		}
	}
}