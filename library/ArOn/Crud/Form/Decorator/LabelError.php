<?php
class ArOn_Crud_Form_Decorator_LabelError extends Zend_Form_Decorator_Label {

	public function getLabel() {
		$element = $this->getElement ();
		$errors = $element->getMessages ();
		if (empty ( $errors )) {
			return parent::getLabel ();
		}

		$label = trim ( $element->getLabel () );
		$label .= ' <strong>' . implode ( '</strong><br /><strong>', $errors ) . '</strong>';

		$element->setLabel ( $label );

		return parent::getLabel ();
	}

}
?>