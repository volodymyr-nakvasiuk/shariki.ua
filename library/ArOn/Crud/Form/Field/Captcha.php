<?php
class ArOn_Crud_Form_Field_Captcha extends ArOn_Crud_Form_Field {

	protected $_type = 'captcha';

	protected $saveInDataBase = false;

	public function createElement() {
		$this->loadHelper ();
		$this->element = new $this->elementClassName ( $this->name, array (

		'captcha' => array ('captcha' => 'Image', 'wordLen' => 6, 'timeout' => 300, 'font' => DOCUMENT_ROOT.'/css/fonts/arial.ttf', 'imgDir' => ROOT_PATH . '/www/images/captcha', 'imgUrl' => '/images/captcha' ) ) );
		$this->element->removeDecorator ( 'Label' );
		$this->element->setDisableLoadDefaultDecorators ( true );
		$this->element->addPrefixPath ( 'ArOn_Crud_Form_Decorator', 'ArOn/Crud/Form/Decorator/', 'decorator' );
		$this->element->setLabel ( $this->title )->setDescription ( $this->description );
		$this->updateField ();
		//if($this->notEdit) $this->element->disable = true;
		$validator = $this->element->getValidator ( 'image' );
		$validator->setMessage ( 'Security code is wrong', 'badCaptcha' );
		return $this->element;
	}

	public function updateField() {

		$this->element->//    	->addDecorator('viewHelper')
		addDecorator ( 'errors' )->addDecorator ( 'Description', array ('tag' => 'td' ) )->addDecorator ( 'Errors', array ('tag' => 'td' ) )->addDecorator ( 'htmlTag', array ('tag' => 'td', 'class' => 'field_input' ) )->addDecorator ( 'label', array ('tag' => 'th', 'requiredSuffix' => ' (*)' ) )->setRequired ( TRUE );

	}
}
