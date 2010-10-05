<?php
class ArOn_Crud_Form_Field_GalleryTextArea extends ArOn_Crud_Form_Field {

	protected $_type = 'textarea';
	
	function __construct($name = false, $title = null, $description = null, $required = null, $notEdit = false, $width = 600){
		parent::__construct($name,$title,$description,$required,$notEdit,$width);
	}
	
	public function updateField() {
		parent::updateField ();
		$this->element->helper = 'formGalleryTextArea';
		//$this->element->addFilter ( 'StringTrim' )->//->addValidator('Regex',false,array('/^[a-z][a-z0-9., \'-]{2,}$/i'))
		//setAttribs ( array ('rows' => 4, 'cols' => 30 ) );
	}
}