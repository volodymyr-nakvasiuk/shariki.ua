<?php
class ArOn_Crud_Form_Field_Many2ManyGroups extends ArOn_Crud_Form_Field_Many2Many {

	public function updateField() {
		parent::updateField ();
		$this->element->helper = 'MyFormMultiCheckboxTree';

	}

}
