<?php
class ArOn_Crud_Form_Decorator_Test implements ArOn_Crud_Form_Decorator {

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

		$element->addDecorator ( 'viewHelper' )->addDecorator ( 'Callback', array ('callback' => array ($this, 'fieldDecorator' ), 'placement' => false ) );

	}

	public function fieldDecorator($content, Zend_Form_Element $element, array $options) {

		$messages = $element->getMessages ();
		$error_message = "";

		if (! empty ( $messages )) {
				
			$error_message = '<ul tag="td" class="errors">';
				
			foreach ( $messages as $message ) {
				$error_message .= "<li>";
				$error_message .= $message;
				$error_message .= "</li>";
			}
				
			$error_message .= "</ul>";

		}

		$content = <<<EOT
  		
			<tr>
				<th><label class="required" for="{$element->getName ()}">{$element->getLabel ()}: (*)</label></th>
				<td>
				$content
				$error_message
				</td>
				<td class="hint">
				{$element->getDescription()}
     			</td>
			</tr>
			
EOT;
				$content = preg_replace ( "/([\s\x{0}\x{0B}]+)/i", " ", trim ( $content ) );

				return $content;

	}

	/**
	 * old admin fields decorators
	 */

	/* public function decorateField(ArOn_Crud_Form_Field $field){

	$element = $field->getElement();
	$element->removeDecorator('Label');
	$element
	->addDecorator('viewHelper')
	->addDecorator('Description',array('tag'=>'td','escape'=>false))
	->addDecorator('Errors',array('tag'=>'td'))
	->addDecorator('htmlTag', array('tag' => 'td','class' => 'field_input'));

	 
	$label = new ArOn_Crud_Form_Decorator_TooltipLabel();
	$label->setOptions( array(
	'element_name' => $element->getName(),
	'placement' => 'prepend',
	'table_name' => $field->getTableName(),
	'place' => 'Form',
	'tag'=>'th'
	)
	);
	if($field->isRequire()) $label->setOption('requiredSuffix' , ' (*)');
	$element->addDecorator($label);
	 
	$element->addDecorator(array('trTag' => 'htmlTag'), array('tag' => 'tr','id' => "tr_".$field->fieldName.""));
	 

	}*/
}