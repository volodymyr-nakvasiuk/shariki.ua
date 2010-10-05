<?php
class ArOn_Crud_Form_Field_Many2OneTemplate extends ArOn_Crud_Form_Field_Many2One {

	public function updateField() {
		parent::updateField ();
		$action = '/' . ArOn_Crud_Form::$ajaxModuleName . '/ajax/' . $this->field_edit . '-template/' . $this->form->actionId;
		$this->element->setAttrib ( 'edit', $action );
		$this->element->helper = 'MyFormSelectTemplate';

	}

}
