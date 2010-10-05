<?php
interface ArOn_Crud_Grid_Filter_Decorator_Interface {

	public function decorateForm(ArOn_Crud_Grid_Filter $form);

	public function decorateGroup(ArOn_Crud_Grid_Filter $form, $groupFields, $groupName);

	public function decorateField(ArOn_Crud_Grid_Filter_Field $field);

}