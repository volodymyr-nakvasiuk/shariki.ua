<?php

class ArOn_Crud_Form_Decorator_Captcha_Word extends Zend_Form_Decorator_Abstract {

	public function render($content) {
		$element = $this->getElement ();
		$view = $element->getView ();
		if (null === $view) {
			return $content;
		}

		$name = $element->getFullyQualifiedName ();

		$hiddenName = $name . '[id]';
		$textName = $name . '[input]';

		$placement = "PREPEND";
		$separator = "<br/>";

		$hidden = $view->formHidden ( $hiddenName, $element->getValue (), $element->getAttribs () );
		$text = $view->formText ( $textName, '', $element->getAttribs () );
		switch ($placement) {
			case 'PREPEND' :
				$content = $hidden . $text . $separator . $content;
				break;
			case 'APPEND' :
			default :
				$content = $content . $separator . $hidden . $separator . $text;
		}
		return $content;
	}
}
