<?php
class ArOn_Crud_Form_Field_CreditCard extends ArOn_Crud_Form_Field {

	public function updateField() {
		parent::updateField ();
		$validator = new My_Validate_Ccnum ( );
		$this->element->setRequired ( $this->required )->setValidators ( array ($validator ) );
	}
}

class My_Validate_Ccnum extends Zend_Validate_Abstract {
	/**
	 * Validation failure message key for when the value is not of valid length
	 */
	const LENGTH = 'ccnumLength';

	/**
	 * Validation failure message key for when the value fails the mod-10 checksum
	 */
	const CHECKSUM = 'ccnumChecksum';

	/**
	 * Digits filter for input
	 *
	 * @var Zend_Filter_Digits
	 */
	protected static $_filter = null;

	/**
	 * Validation failure message template definitions
	 *
	 * @var array
	 */
	protected $_messageTemplates = array (self::LENGTH => 'Invalid credit card number', self::CHECKSUM => 'Invalid credit card number' );

	public function isValid($value, $context = null) {
		$this->_setValue ( $value );
		self::$_filter = new Zend_Filter_Digits ( );
		$valueFiltered = self::$_filter->filter ( $value );

		if (! empty ( $context ['type'] )) {
			$validCard = false;
			switch ($context ['type']) {
				case 'ax' :
					$validCard = ereg ( "^3[47][0-9]{13}$", $value );
					break;
				case 'dc' :
					$validCard = ereg ( "^3(0[0-5]|[68][0-9])[0-9]{11}$", $value );
					break;
				case 'ds' :
					$validCard = ereg ( "^6011[0-9]{12}$", $value );
					break;
				case 'mc' :
					$validCard = ereg ( "^5[1-5][0-9]{14}$", $value );
					break;
				case 'vs' :
					$validCard = ereg ( "^4[0-9]{12}([0-9]{3})?$", $value );
					break;
				default :
					$validCard = false;
			}
				
			if ($validCard) {
				$number = strrev ( $value );
				$sum = 0;

				for($i = 0; $i < strlen ( $number ); $i ++) {
					$currentNum = substr ( $number, $i, 1 );
					// Double every second digit
					if ($i % 2 == 1) {
						$currentNum *= 2;
					}
						
					// Add digits of 2-digit numbers together
					if ($currentNum > 9) {
						$firstNum = $currentNum % 10;
						$secondNum = ($currentNum - $firstNum) / 10;
						$currentNum = $firstNum + $secondNum;
					}
					$sum += $currentNum;
				}
				// If the total has no remainder it's OK
				$validCard = ($sum % 10 == 0);
			}
				
			if ($validCard) {
				return true;
			}
		}

		$this->_error ( self::LENGTH, $valueFiltered );
		return false;
	}
}