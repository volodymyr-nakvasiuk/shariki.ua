<?php
class Crud_Filter_Decorator_Advertiser implements Crud_Filter_Decorator_Abstract {

	public function decorateForm(Crud_Filter_Abstract $form) {

		$form->setDisableLoadDefaultDecorators ( false );
		$form->addDecorator ( 'formElements' )->addDecorator ( 'htmlTag', array ('tag' => 'div' ) )->addDecorator ( 'Form', array ('class' => 'filter-form' ) );

	}

	public function decorateGroup(Crud_Filter_Abstract $form, $groupFields, $groupName) {

		$form->addDisplayGroup ( $groupFields, $groupName, array ('disableLoadDefaultDecorators' => true, 'decorators' => array ('FormElements', array ('HtmlTag', array ('tag' => 'p' ) ), 'Fieldset' ), 'legend' => $groupName ) );

	}

	public function decorateField(Crud_Filter_Field_Abstract $field) {

		$element = $field->getElement ();
		$element->addDecorator ( 'viewHelper' )->addDecorator ( 'Label', array ('tag' => 'span' ) )->addDecorator ( 'htmlTag', array ('tag' => 'span', 'class' => 'field_input' ) )->addDecorator ( 'Description', array ('tag' => 'span' ) );

	}
}