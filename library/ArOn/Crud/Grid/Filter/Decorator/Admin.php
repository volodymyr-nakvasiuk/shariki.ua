<?php
class ArOn_Crud_Grid_Filter_Decorator_Admin implements ArOn_Crud_Grid_Filter_Decorator_Interface {

	public function decorateForm(ArOn_Crud_Grid_Filter $form) {

		$form->setDisableLoadDefaultDecorators ( false );
		$form->addDecorator ( 'formElements' )->addDecorator ( 'htmlTag', array ('tag' => 'div' ) )->addDecorator ( 'Form', array ('class' => 'filter-form' ) );

	}

	public function decorateGroup(ArOn_Crud_Grid_Filter $form, $groupFields, $groupName) {

		$form->addDisplayGroup ( $groupFields, $groupName, array ('disableLoadDefaultDecorators' => true, 'decorators' => array ('FormElements', array ('HtmlTag', array ('tag' => 'p' ) ), 'Fieldset' ), 'legend' => $groupName ) );

	}

	public function decorateField(ArOn_Crud_Grid_Filter_Field $field) {

		$element = $field->getElement ();

		$element->addDecorator ( 'viewHelper' )->addDecorator ( 'htmlTag', array ('tag' => 'span', 'class' => 'field_input' ) )->addDecorator ( 'Description', array ('tag' => 'span' ) );

	}
}