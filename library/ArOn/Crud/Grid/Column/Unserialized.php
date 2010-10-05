<?php
class ArOn_Crud_Grid_Column_Unserialized extends ArOn_Crud_Grid_Column_Default {

	public $unserializedKey;

	function __construct($title, $name = null, $isSort = true, $unserializedKey) {
		parent::__construct ( $title, $name, $isSort );
		$this->unserializedKey = $unserializedKey;
	}

	public function render(array &$row) {
		$record = unserialize ( $row [$this->key] );
		$value = @$record [$this->unserializedKey];

		return $value;
	}

}