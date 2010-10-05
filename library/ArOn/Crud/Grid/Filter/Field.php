<?php
class ArOn_Crud_Grid_Filter_Field implements ArOn_Crud_Grid_Filter_Field_Interface {

	protected $_type = 'text';

	public $helper = array ();

	protected $name;
	protected $formElementName;
	protected $title;
	protected $prefix;
	protected $elementClassName;
	protected $exceptionValues = array('от','до');
	protected $criteria;
	protected $enableDefaultValue = false;
	/**
	 * @var Zend_Form_Element
	 */
	protected $element;

	public $table;

	public $default = false;
	protected $_defaultValue = 0;
	public $addElement = true;
	public $active_mode;

	/**
	 *
	 * @var ArOn_Crud_Grid_Filter
	 */
	protected $form;
	protected $isArray = true;

	function __construct($name, $title = null, $criteria = ArOn_Db_Filter_Field::EQ, $options = null) {
		$this->elementClassName = $this->getMyPluginLoader ()->load ( $this->_type );
		$this->name = $name;
		$this->title = $title;
		$this->criteria = $criteria;
		if (is_array ( $options )) {
			foreach ( $options as $atr => $value ) {
				$this->$atr = $value;
			}
		}
	}

	public function init() {
	}

	public function createElement() {
		$this->loadHelper ();
		$this->element = new $this->elementClassName ( $this->formElementName, array ('disableLoadDefaultDecorators' => true ) );
		$this->element->setLabel ( $this->title );
		if (@ $this->default)
		$this->element->setValue ( $this->default );
		$this->updateField ();

		return $this->element;
	}

	public function updateField() {
		$this->element->setIsArray($this->isArray);
		$this->form->decorateClass->decorateField ( $this );
	}

	private function getMyPluginLoader() {
		$_loader = new Zend_Loader_PluginLoader ( array ('Zend_Form_Element_' => 'Zend/Form/Element/' ) );

		return $_loader;
	}

	public function addHelper($key, $value) {
		$this->helper [$key] = $value;
	}

	public function loadHelper() {
		foreach ( $this->helper as $var => $value ) {
			$this->$var = $value;
		}
	}

	public function applyFilter(ArOn_Db_TableSelect $select) {
		if($filters = $this->getFilters()) $select->filter ( $filters );
	}

	public function getFilters(){
		$values = $this->getFieldValue ();
		if ($values) {
			$filters = array();
			if(!is_array($values)) $values = array($values);
			foreach ($values as $val){
				if(empty($val) || in_array($val,$this->exceptionValues)) continue;
				$filters[] = new ArOn_Db_Filter_Field ($this->name ,$val , $this->criteria);
			}
			if(!empty($filters) && $this->enableDefaultValue) {
				$val = $this->_defaultValue;
				$filters[] = new ArOn_Db_Filter_Field ($this->name ,$val , $this->criteria);
			}
			$filter = new ArOn_Db_Filter_Compound('OR',$filters);
			return $filter;
		}
		return false;
	}

	public function getFieldWhere() {
		return null;
	}

	public function applyFilterToCubeQuery(Olap_CubeQuery $cubeQuery) {
	}

	public function setFilterWhere(Zend_Db_Select $select) {
		$value = $this->getFieldValue ();
		if (@ $value and $this->element->isValid ( $value )) {
			return $select->where ( "`$this->table`.`$this->field` = ?", $value );
		}
	}

	public function getFieldValue() {

		if(!is_object($this->element)) return $this->default;
		return $this->element->getValue ();
		/*$type = $this->element->getType ();
			if ($type == 'Zend_Form_Element_Select') {
			$options = $this->element->getMultiOptions ();
			foreach ( $options as $key => $name ) {
			$value = $key;
			break;
			}
			$this->element->setValue ( $value );
			}*/
	}

	public function getName() {
		return $this->name;
	}

	public function setForm(ArOn_Crud_Grid_Filter $form) {
		$this->form = $form;
	}

	public function getElement() {
		return $this->element;
	}

	public function setPrefix($prefix){
		$this->prefix = $prefix;
		return $this;
	}

	public function setFormElementName($name){
		$this->formElementName = $name;
		return $this;
	}

	public function setValue($value){
		$this->default = $value;
		return $this;
	}
	public function setEnableDefaultValue($flag = true){
		$this->enableDefaultValue = $flag;
		return $this;
	}
	public function setDefaultValue($value){
		$this->_defaultValue = $value;
		return $this;
	}
	public function setExceptionValues($values){
		if(is_array($values)) $this->exceptionValues = $values;
		else $this->exceptionValues[] = $values;
		return $this;
	}
}