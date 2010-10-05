<?php
class ArOn_Crud_Grid_Filter_Decorator_Front implements ArOn_Crud_Grid_Filter_Decorator_Interface {

	public function decorateForm(ArOn_Crud_Grid_Filter $form) {

		$form->setDisableLoadDefaultDecorators ( false );
		$form->addDecorator ( 'formElements' )->addDecorator ( 'htmlTag', array ('tag' => 'div' ) )->addDecorator ( 'form', array ('id' => 'form_' . $form->actionId ) );

	}

	public function decorateGroup(ArOn_Crud_Grid_Filter $form, $groupFields, $groupName) {

		$form->addDisplayGroup ( $groupFields, $groupName, array (

		'decorators' => array ('FormElements', array ('HtmlTag', array ('tag' => 'div', 'class' => 'form_group' ) ), 'Fieldset' ), 'legend' => array ($groupName, 'htmlTag', array ('tag' => 'span', 'class' => 'field_input' ) ) ) );

	}

	public function decorateField(ArOn_Crud_Grid_Filter_Field $field) {
		/**
		 * @var Zend_Form_Element
		 */
		$element = $field->getElement ();
		$element->addDecorator ( 'viewHelper' )->addDecorator ( 'Description', array ('tag' => 'span', 'escape' => false ) )->addDecorator ( 'Errors', array ('tag' => 'span' ) );
		//->addDecorator('htmlTag', array('tag' => 'span','class' => 'field_input'));

		$label = new Zend_Form_Decorator_Label();
		$label->setOptions( array(
					    		'tag'=>'span'
					    		)
					    		);
					    		$element->addDecorator($label);
					    		/*if($field->isRequire()) $label->setOption('requiredSuffix' , ' (*)');
					    		 $element->addDecorator($label);
					    		 */
					    		//$element->addDecorator(array('trTag' => 'htmlTag'), array('tag' => 'p','id' => "tr_".$field->fieldName.""));
					    		 
					    		//$element->removeDecorator('Description');


	}

}