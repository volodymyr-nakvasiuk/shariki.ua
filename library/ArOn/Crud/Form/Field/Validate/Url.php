<?php

/**
 * @see Zend_Validate_Abstract
 */
//require_once 'Zend/Validate/Abstract.php';

class ArOn_Crud_Form_Field_Validate_Url extends Zend_Validate_Abstract {
	const NOT_MATCH = 'regexNotMatch';

	/**
	 * @var array
	 */
	protected $_messageTemplates = array (self::NOT_MATCH => "'%value%' Invalid Url" );

	public function isValid($value) {
		$valueString = ( string ) $value;

		$this->_setValue ( $valueString );
		if (strpos ( $valueString, 'http' ) === false) {
			$valueString = 'http://' . $valueString;
		}

		$status = @preg_match ( "/^((http(s?))\:\/\/)?[a-zA-Z0-9][a-zA-Z0-9\-\.]*\.(com|edu|gov|mil|net|org|biz|info|name|museum|us|ca|uk|[a-z]{2})(\:[0-9]+)*(\/($|[a-zA-Z0-9\.\,\;\?\'\\\+&%\$#\=~_\-]*))*$/i", $valueString );
		if (false === $status) {
			/**
			 * @see Zend_Validate_Exception
			 */
			//require_once 'Zend/Validate/Exception.php';
			throw new Zend_Validate_Exception ( "Internal error matching pattern '$this->_pattern' against value '$valueString'" );
		}
		if (! $status) {
			$this->_error ();
			return false;
		}
		return true;
	}
}
