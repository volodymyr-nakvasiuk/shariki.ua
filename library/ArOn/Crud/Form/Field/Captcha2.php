<?php
class ArOn_Crud_Form_Field_Captcha2 extends ArOn_Crud_Form_Field {

	protected $_type = 'captcha';

	protected $saveInDataBase = false;

	public function createElement() {
		$this->loadHelper ();
		$this->element = new $this->elementClassName ( $this->name, array (

		'captcha' => array ('captcha' => 'Image', 'wordLen' => 6, 'timeout' => 300, 'font' => ROOT_PATH . '/res/fonts/arial.ttf', 'imgDir' => ROOT_PATH . 'images/captcha', 'imgUrl' => '/images/captcha' ) ) );

		$this->element->clearDecorators ();
		$this->required = true;
		//$this->element->addDecorator('htmlTag', array('tag' => 'div','class' => 'input'));
		//$this->element->addDecorator('image', array('tag' => 'div','class' => 'image'));


		//$this->element->addPrefixPath('Crud_Form_Decorator', 'Crud/Form/Decorator/', 'decorator');
		$this->updateField ();
		$this->element->setLabel ( $this->title )->setDescription ( $this->description );

		//if($this->notEdit) $this->element->disable = true;
		$validator = $this->element->getValidator ( 'image' );
		$validator->setMessage ( 'Security code is wrong', 'badCaptcha' );

		return $this->element;
	}

	public function updateField() {
		$this->form->decorateClass->decorateFieldCaptcha ( $this );

	}
}
