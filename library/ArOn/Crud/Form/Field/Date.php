<?php
class ArOn_Crud_Form_Field_Date extends ArOn_Crud_Form_Field {

	protected $startDate;
	protected $endDate;
	protected $format;

	function __construct($name, $title = null, $description = null, $startDate = null, $endDate = null, $required = null, $notEdit = false, $format = 'MM-DD-YYYY') {

		$this->startDate = $startDate;
		$this->endDate = $endDate;
		$this->format = $format;
		parent::__construct ( $name, $title, $description, $required, $notEdit );
	}

	public function updateField() {
		parent::updateField ();
		$this->element->setValidators ( array (array ('NotEmpty', true ), new Zend_Validate_Date ( $this->format, 'en_US' ) ) );
		if (null != $this->startDate) {
			$validator = new Zend_Validate_GreaterThan ( $this->startDate );
			$this->element->addValidator ( $validator );
		}
		if (null != $this->endDate) {
			$validator = new Zend_Validate_LessThan ( $this->endDate );
			$this->element->addValidator ( $validator );
		}
		$this->element->helper = 'GridFormCalendar';
	}

	public function getInsertData() {
		if (! $this->saveInDataBase)
		return false;
		$value = $this->element->getValue ();
		$value = str_replace ( "-", "/", $value );
		$value = date ( 'Y-m-d', strtotime ( $value ) );

		$data = array ();
		$data ['model'] = 'default';
		$data ['data'] = array ('key' => $this->element->getName (), 'value' => $value );

		return $data;
	}

	public function setValue($value) {
		$value = str_replace ( "-", "/", $value );
		$value = date ( 'm-d-Y', strtotime ( $value ) );
		return parent::setValue ( $value );
	}
}
