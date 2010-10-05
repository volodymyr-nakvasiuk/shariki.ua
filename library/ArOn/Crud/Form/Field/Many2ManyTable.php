<?php
class ArOn_Crud_Form_Field_Many2ManyTable extends ArOn_Crud_Form_Field_Many2Many {

	public $list_title = '';
	public $assign_title = '';

	public function updateField() {
		parent::updateField ();

		$this->element->setAttrib ( 'list_title', $this->list_title );
		$this->element->setAttrib ( 'assign_title', $this->assign_title );
		$this->element->setAttrib ( 'class', 'multiCheckboxContent-table' );
		$this->element->setAttrib ( 'fields', $this->fields );
		$this->element->helper = 'MyFormMultiCheckboxTableOld';
	}

}
