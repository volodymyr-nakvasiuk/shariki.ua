<?php
class ArOn_Crud_Form_Decorator_Front implements ArOn_Crud_Form_Decorator {

	public function decorateForm(ArOn_Crud_Form $form) {

		$form->setDisableLoadDefaultDecorators ( false );
		$form->addDecorator ( 'formElements' )->addDecorator ( 'htmlTag', array ('tag' => 'div' ) )->addDecorator ( 'form', array ('id' => 'form_' . $form->actionId ) );

	}

	public function decorateGroup(ArOn_Crud_Form $form, $groupFields, $groupName) {

		$form->addDisplayGroup ( $groupFields, $groupName, array (

		'decorators' => array ('FormElements', array ('HtmlTag', array ('tag' => 'div', 'class' => 'form_group' ) ), 'Fieldset' ), 'legend' => array ($groupName, 'htmlTag', array ('tag' => 'span', 'class' => 'field_input' ) ) ) );

	}

	public function decorateField(ArOn_Crud_Form_Field $field) {
		/**
		 * @var Zend_Form_Element
		 */
		$element = $field->getElement ();
		$element->addDecorator ( 'viewHelper' )->addDecorator ( 'Description', array ('tag' => 'span', 'escape' => false ) )->addDecorator ( 'Errors', array ('tag' => 'span' ) );
		//->addDecorator('htmlTag', array('tag' => 'span','class' => 'field_input'));
		/*
		$label = new Zend_Form_Decorator_Label();
		$label->setOptions( array(
		'tag'=>'span'
		)
		);
		if($field->isRequire()) $label->setOption('requiredSuffix' , ' (*)');
		$element->addDecorator($label);
		 
		//$element->addDecorator(array('trTag' => 'htmlTag'), array('tag' => 'p','id' => "tr_".$field->fieldName.""));
		*/
		//$element->removeDecorator('Description');


	}

	public function decorateFieldCaptcha(Crud_Form_Field_Captcha2 $field) {
		$element = $field->getElement ();
		$element->addDecorator ( 'Description', array ('tag' => 'span', 'escape' => false ) )->addDecorator ( 'Errors', array ('tag' => 'span' ) )->addDecorator ( 'htmlTag', array ('tag' => 'div', 'style' => 'display:block' ) );

		$element->addPrefixPath ( 'Crud_Form', 'Crud/Form/', 'decorator' );

	}

}