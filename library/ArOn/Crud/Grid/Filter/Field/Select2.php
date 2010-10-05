<?php
class ArOn_Crud_Grid_Filter_Field_Select2 extends ArOn_Crud_Grid_Filter_Field {

	protected $_type = 'select';

	public $notAll = true;

	protected $_indent = ' ';

	protected $_empty_category;
	protected $_empty_item;

	protected $_filterPath;
	protected $_table;
	protected $_categoryPath;
	protected $_allOptions;

	protected $_glue = 'OR';

	function __construct($name, $title, $table, $filterPath = null, $categoryPath = null, $showAll = true, $criteria = ArOn_Db_Filter_Field::EQ) {
		parent::__construct ( $name, $title, $criteria );
		$this->_table = is_string ( $table ) ? ArOn_Crud_Tools_Registry::singleton ( $table ) : $table;
		$this->_filterPath = $filterPath === null ?  $this->_table->getClass() : $filterPath;
		$this->_categoryPath = $categoryPath;
		$this->notAll = ! $showAll;
	}

	public function updateField() {
		parent::updateField ();
		$select = $this->_table->select ();
		$options = ArOn_Crud_Tools_Multiselect::prepareOptions ( $select, null, $this->_categoryPath, null, null, $this->_empty_category, $this->_empty_item );

		if (! $this->notAll) {
			$this->element->addMultiOption ( '', '-' );
		}
		$this->element->addMultiOptions ( $options );
		$this->element->setAllowEmpty ( false );
	}

	public function getFieldValue() {
		if(!is_object($this->element)) return $this->default;
		return $this->element->getValue ();
		/*if (! $this->notAll)
			return null;
			$value = ArOn_Crud_Tools_Multiselect::getFirstOption ( $this->element->getMultiOptions () );
			$this->element->setValue ( $value );
			if (! empty ( $value ))
			return $value;
			*/
	}

	public function applyFilter(ArOn_Db_TableSelect $select) {
		if($filters = $this->getFilters()) $select->filterPath ( $this->_filterPath, $filters );
	}

	public function getFilters(){
		$values = $this->getFieldValue ();
		if ($values) {
			$filters = array();
			if(!is_array($values)) $values = array($values);
			foreach ($values as $val){
				if(empty($val) || in_array($val,$this->exceptionValues)) continue;
				$filters[] = new ArOn_Db_Filter_Field ($this->name ,$val, $this->criteria);
			}
			if(!empty($filters) && $this->enableDefaultValue) {
				$val = $this->_defaultValue;
				$filters[] = new ArOn_Db_Filter_Field ($this->name ,$val, $this->criteria);
			}
			$filter = new ArOn_Db_Filter_Compound($this->_glue,$filters);
			return $filter;
		}
		return false;
	}

	// backward compatibility
	public function getFieldWhere() {
		return null;
	}

}