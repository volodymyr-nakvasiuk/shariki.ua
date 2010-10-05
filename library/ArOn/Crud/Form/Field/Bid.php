<?php
class ArOn_Crud_Form_Field_Bid extends ArOn_Crud_Form_Field {

	public function updateField() {
		parent::updateField ();

		//$validator = new Zend_Validate_Between(MIN_BID_PRICE, MAX_BID_PRICE, true);
		$this->element->addValidator ( 'Between', false, array ('messages' => array ('notBetween' => "'%value%' is not between '%min%' and '%max%'" ), 'Min' => MIN_BID_PRICE, 'Max' => MAX_BID_PRICE, 'Inclusive' => true ) );
	}

	public function getInsertData() {

		if (! $this->saveInDataBase)
		return false;
		$value = $this->element->getValue ();
		if (empty ( $value ))
		$value = null;

		$data = array ();
		$data ['model'] = 'default';
		$data ['data'] = array ('key' => $this->element->getName (), 'value' => $value );

		return $data;
	}
}