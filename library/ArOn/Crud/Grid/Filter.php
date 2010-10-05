<?php
class ArOn_Crud_Grid_Filter extends Zend_Form {

	public $fields = array ();

	public $method = 'get';

	public $action = '';

	public $table;

	public $groups = array ();

	public $modelName;

	protected $_params = array ();

	protected $groupNames;

	protected $_multi;
	/**
	 * Prefix for filter form value  prefix = filter => ex. filter['id']
	 * var @string
	 */
	protected $prefix;
	public $submitButton = true;
	public $submitButtonName = 'Apply';
	public $resetButton = true;
	public $trash = false;
	public $active_mode = true;
	public $default;
	public $decorateClass = 'ArOn_Crud_Grid_Filter_Decorator_Front';

	protected $_disableLoadDefaultDecorators = true;
	protected $_translatorDisabled = true;
	
	protected $_sortParameter = 'sort';
	protected $_directionParameter = 'sort_direction';
	
	public function __construct($action = null, $options = null, $mode = true, $prefix = 'filter', $multi = false) {

		if ($action)
		$this->action = $action;
		$this->active_mode = $mode;
		$this->decorateClass = new $this->decorateClass ( );
		$this->_view = new Zend_View();
		$this->prefix = $prefix;
		parent::__construct ( $options );
		$this->init ();
		$this->setup();
	}

	public function init() {
		$this->setDisableLoadDefaultDecorators ( true );
	}

	public function setup(){
		if (empty ( $this->fields ))
		return;
		$i = 0;
		foreach ( $this->fields as $name => $field ) {
			$i++;
			if($field instanceof ArOn_Crud_Grid_Filter_Field){
				$field->setPrefix($this->prefix);
				$field->addHelper ( 'id', $this->actionId );
				$field->setFormElementName( $name );
				$field->active_mode = $this->active_mode;
				$field->setForm ( $this );
				if (is_array ( $this->default ) && isset ( $this->default [$name] ))
				$field->default = $this->default [$name];
			}
			if($field instanceof ArOn_Crud_Grid_Filter_Field_Compound){
				$field->i = $i;
				$field->init();
			}
		}
	}

	public function createForm() {
		if($this->_multi){
			if($this->prefix) $prefix = $this->prefix."[1]"; else $prefix = "[1]";
		}else {
			$prefix = $this->prefix;
		}
		if($prefix)
		$this->setElementsBelongTo ( $this->prefix );
			
		if (empty ( $this->fields ))
		return;
		foreach ( $this->fields as $name => $field ) {
			if($field instanceof ArOn_Crud_Grid_Filter_Field){
				$elements = $field->createElement ();
				if(!is_array($elements)) $elements = array($elements);
				foreach ($elements as $element){
					if(!($element instanceof Zend_Form_Element)) continue;
					$fieldNames [$name] = $element->getName ();
					if ($field->addElement)
					$this->addElement ( $element );
					$element = NULL;
				}
				$elements = NULL;
			}
		}

		$this->setAction ( $this->action )->setMethod ( $this->method );

		$this->setElementsBelongTo ( NULL );
		$this->addElement ( new Zend_Form_Element_Hidden ( $this->_sortParameter, array ('decorators' => array ('ViewHelper', array ('HtmlTag', array ('tag' => 'span' ) ) ) ) ) );
		$this->addElement ( new Zend_Form_Element_Hidden ( $this->_directionParameter, array ('decorators' => array ('ViewHelper', array ('HtmlTag', array ('tag' => 'span' ) ) ) ) ) );
		$this->addElement ( new Zend_Form_Element_Hidden ( 'limit', array ('decorators' => array ('ViewHelper', array ('HtmlTag', array ('tag' => 'span' ) ) ) ) ) );

		if ($this->trash) {
			$this->addElement ( new Zend_Form_Element_Hidden ( 'is_deleted', array ('decorators' => array ('ViewHelper', array ('HtmlTag', array ('tag' => 'span' ) ) ) ) ) );
		}

		if ($this->submitButton) {
			$submit = new Zend_Form_Element_Submit ( $this->submitButtonName );
			$submit->setDecorators ( array ('ViewHelper', array ('HtmlTag', array ('tag' => 'span' ) ) ) );
			$submit->setLabel ( $this->submitButtonName );
			$this->addElement ( $submit );
		}
		if ($this->resetButton) {
			$clear = new Zend_Form_Element_Reset ( 'Clear' );
			$clear->setDecorators ( array ('ViewHelper', array ('HtmlTag', array ('tag' => 'span' ) ) ) );
			$this->addElement ( $clear );
		}
		foreach ( $this->groups as $groupName => $fields ) {
				
			$this->groupNames [] = $groupName;
			$groupNames = array ();
			foreach ( $fields as $name ) {
				if ($name == 'Filter')
				$groupNames [] = 'Filter';
				else
				$groupNames [] = @$fieldNames [$name];
			}
			$this->decorateClass->decorateGroup ( $this, $groupNames, $groupName );
		}

		$this->updateForm ();
		$this->setFilterValue ();

	}

	public function setFilterValue() {
		if ($this->active_mode) {
			if (! is_array ( $this->default ))
			$this->default = array ();
			$hidden = array ($this->_sortParameter, $this->__directionParameter, 'limit', 'is_deleted', 'p' );
			foreach ( $hidden as $name ) {
				if (isset ( $this->_params [$name] ))
				//$this->default [$name] = $this->_params [$name];
				$element = $this->getElement($name);
				if(isset($element) && ($element instanceof Zend_Form_Element)) $element->setValue($this->_params [$name]);
			}
			if($this->prefix) {
				$params = (isset($this->_params[$this->prefix]))? $this->_params[$this->prefix] : $this->_params;
			}else{
				$params = $this->_params;
			}
				
			if (isset ( $params ) && is_array ( $params )){
				//$this->_removeEmpty($params);
				$this->_setDefaultValue($params);
			}
		}
	}

	protected function _setDefaultValue($params){
		if(!empty($params)) $this->default = $params;
		return $this;
	}

	protected function _removeEmpty(&$params){
		foreach ( $params as $name => $value )
		if(is_array($value)) $this->_removeEmpty($value);
		if(empty($value)) unset($params[$name]);
	}

	public function applyFilters(ArOn_Db_TableSelect $select){
		$wheres = array ();
		if($this->_multi){
			foreach ($this->default as $filter){
				if(is_array($filter)) $wheres[] = $this->_applyFilters($filter,$select->getTable());
			}
		}else{
			$wheres[] = $this->_applyFilters($this->default,$select->getTable());
		}
		foreach ($wheres as $i=>$where){
			if(empty($where)) unset($wheres[$i]);
		}
		if(!empty($wheres)) $select->where(implode(" OR ", $wheres));
		return true;
	}

	protected function _applyFilters(array $params,$table){
		
		$this->_setValue($params);
		$this->isValid($params);
		$wheres = array ();
		foreach ( $this->fields as $_name => $_filter ) {
			if($_filter instanceof ArOn_Crud_Grid_Filter_Field){
				$tmp_select = new ArOn_Db_TableSelect($table);
				$tmp_select->reset(Zend_Db_Select::WHERE);		
				$_filter->applyFilter($tmp_select);
				$where = $tmp_select->getPart(Zend_Db_Select::WHERE);
				if(!empty($where)) $wheres[] = "(" . implode(' AND ',$where ) . ")";
				unset($tmp_select);
			}
			else {
				//require_once 'Zend/Form/Exception.php';
				throw new Zend_Form_Exception(sprintf('Filter "%s" not instanceof Crud_Grid_Filter_Field', $_name));
			}
		}
		return (empty($wheres)) ? false : "(" . implode(' AND ',$wheres) . ")";
	}

	public function getFilterWhere() {

		$where = array ();

		foreach ( $this->fields as $field ) {
			if(!($field instanceof ArOn_Crud_Grid_Filter_Field))
			continue;
			$field->table = $this->table;
			$value = $field->getFieldWhere ();
			if (! empty ( $value ))
			$where [] = $value;
		}
		return $where;
	}

	protected function updateForm() {

		$this->setAttrib ( 'accept-charset', 'UTF-8' );
		$this->decorateClass->decorateForm ( $this );
	}

	public function changeDecoratorClass($className) {
		$this->decorateClass = new $className ( );
	}

	public function setPrefix($prefix){
		$this->prefix = $prefix;
		return $this;
	}

	public function getPrefix(){
		return $this->prefix;
	}
	public function setParams($params){
		$this->_params = $params;
		return $this;
	}

	protected function _setValue($values=null){
		if(empty($values)) return $this;
		foreach ($this->fields as $name=>$field){
			//if(!isset($this->default[$name]) && empty($this->default[$name])) continue;
			if (isset($values[$name])){
				$field->setValue($values[$name]);
			}
		}
	}

	public function getDefaultValues(){
		return $this->default;
	}

	public function setMultiFiltering($flag = true){
		$this->_multi = ($flag) ? true : false;
		return $this;
	}
	
	public function setSortName($name){
		$this->_sortParameter = $name;
		return $this;
	}
	
	public function setDirectionName($name){
		$this->_directionParameter = $name;
		return $this;
	}
}