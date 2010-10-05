<?php
class ArOn_Crud_Form_Field_Many2ManyOrder extends ArOn_Crud_Form_Field_Many2Many {

	public function updateField() {
		if (is_string ( $this->workingModel ))
		$this->workingModel = ArOn_Crud_Tools_Registry::singleton ( $this->workingModel );
		$this->workingModelOrder = $this->workingModel->getPrimary ();
		parent::updateField ();
		$this->element->helper = 'MyFormMultiCheckboxOrder';
	}
}
