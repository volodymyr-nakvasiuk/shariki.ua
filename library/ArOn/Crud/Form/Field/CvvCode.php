<?php
class ArOn_Crud_Form_Field_CvvCode extends ArOn_Crud_Form_Field {

	public function updateField() {
		parent::updateField ();
		$validator = new My_Validate_CVV ( );
		$this->element->setRequired ( $this->required )->setValidators ( array ($validator ) );
	}
}

class My_Validate_CVV extends Zend_Validate_Abstract {
	/**
	 * Digits filter for input
	 *
	 * @var Zend_Filter_Digits
	 */
	protected static $_filter = null;

	/**
	 * Validation failure message key for when the value is not of valid length
	 */
	const FORMAT = 'cvvcodeFormat';

	/**
	 * Validation failure message template definitions
	 *
	 * @var array
	 */
	protected $_messageTemplates = array (self::FORMAT => 'Invalid CVV code' );

	public function isValid($value, $context = null) {
		$this->_setValue ( $value );
		self::$_filter = new Zend_Filter_Digits ( );
		$valueFiltered = self::$_filter->filter ( $value );

		if (! empty ( $context ['type'] )) {
			if (! empty ( $valueFiltered ) && (($context ['type'] == 'ax' && strlen ( $valueFiltered ) == 4) || ($context ['type'] != 'ax' && strlen ( $valueFiltered ) == 3))) {
				return true;
			}
		}

		$this->_error ( self::FORMAT, $valueFiltered );
		return false;
	}
}