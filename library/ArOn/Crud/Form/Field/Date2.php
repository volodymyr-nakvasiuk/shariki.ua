<?php
class ArOn_Crud_Form_Field_Date2 extends ArOn_Crud_Form_Field {

	protected $startDate;
	protected $endDate;

	function __construct($name, $title = null, $description = null, $startDate = null, $endDate = null, $required = null, $notEdit = false) {

		$this->startDate = $startDate;
		$this->endDate = $endDate;
		parent::__construct ( $name, $title, $description, $required, $notEdit );
	}

	public function updateField() {
		parent::updateField ();
		$this->element->setValidators ( array (array ('NotEmpty', true ), new Zend_Validate_Date ( 'MMM dd, yyyy', 'en_US' ) ) );
		if (null != $this->startDate) {
			$validator = new ArOn_Crud_Form_Field_Validate_DateGreaterThan ( $this->startDate );
			$this->element->addValidator ( $validator );
		}
		if (null != $this->endDate) {
			$validator = new ArOn_Crud_Form_Field_Validate_DateLessThan ( $this->endDate );
			$this->element->addValidator ( $validator );
		}
		$this->element->helper = 'GridFormCalendar';
	}

	public function getInsertData() {
		if (! $this->saveInDataBase)
		return false;
		$value = $this->element->getValue ();
		//$value = date('Y-m-d', strtotime($value));
		$date = new DateTime ( $value );
		$value = $date->format ( 'Y-m-d' );
		$data = array ();

		$data ['model'] = 'default';
		$data ['data'] = array ('key' => $this->element->getName (), 'value' => $value );

		return $data;
	}

	public function setValue($value) {
		$value = str_replace ( "-", "/", $value );
		$value = date ( 'M d, Y', strtotime ( $value ) );
		return parent::setValue ( $value );
	}
}
