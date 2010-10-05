<?php
class ArOn_Crud_Form_Field_Many2ManyTable2 extends ArOn_Crud_Form_Field_Many2ManyTable {

	public function updateField() {
		parent::updateField ();
		$this->element->setAttrib ( 'checkbox_name', $this->name . "[{value}][id]" );
	}

	protected function decorator() {
		$this->form->decorateClass->decorateFieldMany2ManyTable ( $this );
	}

	public function updateMany2ManySelect(ArOn_Db_TableSelect $select) {
		$name = $this->workingModel->getTableName ();
		$name2 = $this->model->getTableName ();
		$id2 = $this->model->getPrimary ();
		$select->joinLeft ( $name, "$name.$this->key = $name2.$id2", array ("$name.requests_limit", "$name.clicks_limit" ) );
		return $select;
	}

	protected function selectedArray($selected_data) {
		$data = array ();
		foreach ( $selected_data as $value ) {
			$data [$value [$this->key]] = $value;
			$data [$value [$this->key]] ['id'] = $value [$this->key];
		}

		return $data;
	}

	public function getInsertData() {

		if (! $this->saveInDataBase)
		return false;

		$data = array ();
		//$data['model'] = $this->model;


		if (empty ( $this->id ))
		return false;
		$post_values = $this->element->getValue ();

		$select = $this->workingModel->select ();
		$select->where ( $this->workingModel->getAdapter ()->quoteInto ( "$this->parent_key=?", $this->id ) );
		$saved_data = (($result = $this->workingModel->fetchAll ( $select )) === null) ? array() : $result->toArray();
		$saved_values = array ();
		foreach ( $saved_data as $value ) {
			$saved_values [$value [$this->key]] = $value;
		}
		if (! is_array ( $saved_values ))
		$saved_values = array ();
		if (! is_array ( $post_values ))
		$post_values = array ();
		foreach ( $post_values as $key => $v ) {
			if (! is_array ( $v ) || ! isset ( $v ['id'] )) {
				unset ( $post_values [$key] );
			}
		}
		$delete = array_diff_key ( $saved_values, $post_values );
		$insert = array_diff_key ( $post_values, $saved_values );
		$update = array_intersect_key ( $post_values, $saved_values );
		$data ['insert'] = array ();
		$data ['delete'] = array ();
		$data ['update'] = array ();
		$working_id = $this->workingModel->getPrimary ();
		if (is_array ( $insert )) {
			foreach ( $insert as $v ) {
				$edata = array ($this->parent_key => $this->id, $this->key => $v ['id'] );
				unset ( $v ['id'] );
				$validator = new Zend_Validate_Int ( );
				foreach ( $v as $a_name => $a_value ) {
					if (! $validator->isValid ( $a_value ) or $a_value < 0)
					$v [$a_name] = 0;
				}
				$edata = array_merge ( $edata, $v );
				$data ['insert'] [] = $edata;
					
			}
		}
		if (is_array ( $update )) {
			foreach ( $update as $key => $v ) {
				$edata = array ($this->parent_key => $this->id, $this->key => $v ['id'] );
				$edata = $saved_values [$v ['id']];
				$edata [$this->parent_key] = $this->id;
				$edata [$this->key] = $v ['id'];
				unset ( $v ['id'] );
				$validator = new Zend_Validate_Int ( );
				foreach ( $v as $a_name => $a_value ) {
					if (! $validator->isValid ( $a_value ) or $a_value < 0)
					$v [$a_name] = 0;
				}
				$edata = array_merge ( $edata, $v );
				$data ['update'] [$saved_values [$key] [$working_id]] = $edata;
					
			}
		}
		if (is_array ( $delete )) {
			foreach ( $delete as $v ) {
				if (is_array ( $v ) && isset ( $v [$this->key] ) && ! empty ( $v [$this->key] )) {
					$data ['delete'] [] = $this->workingModel->getAdapter ()->quoteInto ( "$this->parent_key=?", $this->id ) . " AND " . $this->workingModel->getAdapter ()->quoteInto ( "$this->key=?", $v [$this->key] );
				}
			}
		}
		$db = $this->model->getAdapter ();
		$db->beginTransaction ();
		try {
			foreach ( $data ['delete'] as $delete ) {
				$this->workingModel->delete ( $delete );
			}
				
			$working_id = $this->workingModel->info ( 'primary' );
			if (count ( $working_id ) == 1)
			$working_id = $working_id [1];
				
			foreach ( $data ['update'] as $id => $update ) {

				foreach ( $saved_data as $value ) {
					if (is_array ( $working_id )) {
						$where = array ();
						foreach ( $working_id as $key ) {
							$where [] = $this->workingModel->getAdapter ()->quoteInto ( "$key=?", $update [$key] );
						}
					} else {
						$where = $this->workingModel->getAdapter ()->quoteInto ( "$working_id=?", $update [$working_id] );
					}
				}
				$this->workingModel->update ( $update, $where );
			}
				
			foreach ( $data ['insert'] as $insert ) {
				$this->workingModel->insert ( $insert );
			}
			$db->commit ();
		} catch ( Exception $e ) {
			$db->rollBack ();
			//echo $e->getMessage();
		}

		return true;
	}
}
