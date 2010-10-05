<?php
interface ArOn_Crud_Form_Decorator {

	public function decorateForm(ArOn_Crud_Form $form);
		
	public function decorateGroup(ArOn_Crud_Form $form,$groupFields,$groupName);

	public function decorateField(ArOn_Crud_Form_Field $field);

	 
}