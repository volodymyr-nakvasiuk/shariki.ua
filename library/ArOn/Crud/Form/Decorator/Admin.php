<?php

class ArOn_Crud_Form_Decorator_Admin implements ArOn_Crud_Form_Decorator {

	public function decorateForm(ArOn_Crud_Form $form) {

		$form->setDisableLoadDefaultDecorators ( false );
		$form->addDecorator ( 'formElements' )->addDecorator ( 'htmlTag', array ('tag' => 'div' ) )->addDecorator ( 'form', array ('id' => 'form_' . $form->actionId ) );

	}

	public function decorateGroup(ArOn_Crud_Form $form, $groupFields, $groupName) {

		$form->addDisplayGroup ( $groupFields, $groupName, array (

		'decorators' => array ('FormElements', array ('HtmlTag', array ('tag' => 'table', 'class' => 'form_group' ) ), 'Fieldset' ), 'legend' => $groupName ) );

	}

	public function decorateField(ArOn_Crud_Form_Field $field) {

		$element = $field->getElement ();
		$element->addDecorator ( 'viewHelper' )->addDecorator ( 'Description', array ('tag' => 'td', 'escape' => false ) )->addDecorator ( 'Errors', array ('tag' => 'td' ) )->addDecorator ( 'htmlTag', array ('tag' => 'td', 'class' => 'field_input' ) );

		/*$decorator = ArOn_Crud_Tools_Registry::singleton ('Zend_Form_Decorator_ViewHelper');
		 $element->addDecorator ($decorator);
		 $decorator = ArOn_Crud_Tools_Registry::singleton ('Zend_Form_Decorator_Description');
		 $decorator->setOptions(array ('tag' => 'td', 'escape' => false ));
		 $element->addDecorator ($decorator);
		 $decorator = ArOn_Crud_Tools_Registry::singleton ('Zend_Form_Decorator_Errors');
		 $decorator->setOptions(array ('tag' => 'td' ));
		 $element->addDecorator ($decorator);
		 $decorator = ArOn_Crud_Tools_Registry::singleton ('Zend_Form_Decorator_htmlTag');
		 $decorator->setOptions(array ('tag' => 'td', 'class' => 'field_input' ));
		 $element->addDecorator ($decorator);
		 */
		$decorator = ArOn_Crud_Tools_Registry::singleton ('Zend_Form_Decorator_Label');
		$decorator->setOptions ( array ('tag' => 'th' ) );
		if ($field->isRequire ())
		$decorator->setOption ( 'requiredSuffix', ' (*)' );
		$element->addDecorator ( $decorator );

		$element->addDecorator ( array ('trTag' => 'htmlTag' ), array ('tag' => 'tr', 'id' => "tr_" . $field->getName() . "", 'class' => $field->rowClass ) );

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

	public function decorateFieldMany2ManyTable(Crud_Form_Field_Many2Many $field) {
		$element = $field->getElement ();

		$element->addPrefixPath ( 'Crud_Form_Decorator_', 'Crud/Form/Decorator/', Zend_Form_Element::DECORATOR );
		$element->setDecorators ( array (array ('decorator' => array ('labelGroup' => 'GroupDecorator' ), 'options' => array ('items' => array (array ('decorator' => 'TooltipLabel', 'options' => array ('placement' => Zend_Form_Decorator_Abstract::PREPEND, 'element_name' => $element->getName (),

		'table_name' => $field->getTableName (), 'place' => 'Form', 'tag' => 'span' ) ), array ('decorator' => array ('div' => 'HtmlTag' ), 'options' => array ('tag' => 'div', 'class' => 'many2many' ) ), 'ViewHelper', array ('decorator' => array ('labelCell' => 'HtmlTag' ), 'options' => array ('tag' => 'td', 'class' => 'field_input', 'colspan' => 2 ) ) ) ) ), array ('decorator' => array ('mainRowClose' => 'HtmlTag' ), 'options' => array ('tag' => 'tr', 'id' => "tr_" . $field->fieldName . "", 'class' => $field->rowClass ) ) ) );

	}
}