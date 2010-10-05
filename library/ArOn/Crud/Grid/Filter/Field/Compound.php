<?php
class ArOn_Crud_Grid_Filter_Field_Compound extends ArOn_Crud_Grid_Filter_Field {

	protected $_type = 'text';
	protected $elements;
	protected $_fields;
	public $i = 0;

	function __construct($name, $title, $fields) {
		parent::__construct ( $name, $title );
		$this->_fields = $fields;
		/*if (! is_array ( reset ( $this->_fields ) )) {
			$this->_fields = array (array ('path' => null, 'filters' => $this->_fields ) );
			}*/
	}

	public function init(){
		foreach ( $this->_fields as $name => $field ) {
			if($field instanceof ArOn_Crud_Grid_Filter_Field){
				$field->setPrefix($this->prefix);
				if (isset($this->actionId)) $field->addHelper ( 'id', $this->actionId );
				$field->setFormElementName($name);
				$field->active_mode = $this->active_mode;
				$field->setForm ( $this->form );
				if (is_array ( $this->default ) && isset ( $this->default [$name] ))
				$field->default = $this->default [$name];
			}
		}
	}

	public function createElement() {
		$this->loadHelper ();
		foreach ( $this->_fields as $field ) {
			$element = $field->createElement();
			$element->setBelongsTo($this->name."[". $this->i ."]");
			$this->elements[] = $element;
			unset($element);
		}
		return $this->elements;
	}

	public function applyFilter(ArOn_Db_TableSelect $select) {
		if(empty($this->default)) return false;
		$filters = array();
		foreach ($this->default as $values){
			if(empty($values)) continue;
			$tmp_filters = array();
			$tmp_from = array();
			foreach ($this->_fields as $name => $field){
				if(empty($values[$name])) continue;
				$field->getElement()->setValue($values[$name]);
				$tmp_select = new ArOn_Db_TableSelect($select->getTable());
				$tmp_select->reset(Zend_Db_Select::WHERE);
				$field->applyFilter($tmp_select);
				$tmp_filters[] = implode(' AND ',$tmp_select->getPart(ArOn_Db_TableSelect::WHERE));
				$tmp_from[] = $tmp_select->getPart(ArOn_Db_TableSelect::FROM);
				unset($tmp_select);
				//$tmp_filters[] = $field->getFilters();
			}
			$filters[] = new ArOn_Db_Filter_Compound('AND',$tmp_filters);
		}
		$filter = new ArOn_Db_Filter_Compound('OR',$filters);
		$select->filterPath ( null, $filter );
		return true;
	}

	// backward compatibility
	public function getFieldWhere() {
		return null;
	}

}
