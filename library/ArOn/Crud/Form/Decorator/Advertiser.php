<?php
class ArOn_Crud_Form_Decorator_Advertiser implements ArOn_Crud_Form_Decorator {

	public function decorateForm(ArOn_Crud_Form $form) {

		$form->setDisableLoadDefaultDecorators ( false );
		$form->addDecorator ( 'formElements' )->addDecorator ( 'htmlTag', array ('tag' => 'div' ) )->addDecorator ( 'form', array ('id' => 'form_' . $form->actionId ) );

	}

	public function decorateGroup(ArOn_Crud_Form $form, $groupFields, $groupName) {

		$form->addDisplayGroup ( $groupFields, $groupName, array (

		'decorators' => array ('FormElements', array ('HtmlTag', array ('tag' => 'table', 'class' => 'form_group', 'cellspacing' => "12", 'cellpadding' => "0", 'border' => "0" ) ), 'Fieldset' ), 'legend' => $groupName ) );

	}

	public function decorateField(ArOn_Crud_Form_Field $field) {

		$element = $field->getElement ();
		$element->addDecorator ( 'viewHelper' )->addDecorator ( 'Description', array ('tag' => 'td', 'escape' => false ) )->addDecorator ( 'Errors', array ('tag' => 'td' ) )->addDecorator ( 'htmlTag', array ('tag' => 'td', 'class' => 'field_input' ) );

		$label = new Zend_Form_Decorator_Label ( );
		$label->setOptions ( array ('tag' => 'th' ) );
		if ($field->isRequire ())
		$label->setOption ( 'requiredSuffix', ' (*)' );
		$element->addDecorator ( $label );

		$element->addDecorator ( array ('trTag' => 'htmlTag' ), array ('tag' => 'tr', 'id' => "tr_" . $field->fieldName . "", 'class' => $field->rowClass ) );

	}

	public function decorateFieldCaptcha(Crud_Form_Field_Captcha2 $field) {
		$element = $field->getElement ();
		$element->addDecorator ( 'Description', array ('tag' => 'td', 'escape' => false ) )->addDecorator ( 'Errors', array ('tag' => 'td' ) )->addDecorator ( 'htmlTag', array ('tag' => 'td', 'class' => 'field_input' ) );

		$label = new Zend_Form_Decorator_Label ( );
		$label->setOptions ( array ('tag' => 'th' ) );
		if ($field->isRequire ())
		$label->setOption ( 'requiredSuffix', ' (*)' );
		$element->addDecorator ( $label );
	}
}