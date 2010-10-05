<?php
class ArOn_Crud_Form_Field {

	protected $_type = 'text';

	public $helper = array ();

	protected $name;

	protected $elementFormName = false;

	protected $title;

	protected $description;

	protected $elementClassName;

	/**
	 * @var Zend_Form_Element
	 */
	protected $_covertType = false;
	protected $element;
	protected $elementHelper;
	protected $_value = null;
	protected $prefixFilter = 'filter';
	protected $required;
	protected $_element_attribs = array();
	protected $tableName;
	protected $exploide = false;
	public $disabled = false;
	public $hidden = false;
	public $default = null;
	public $rowClass = 'field_input';
	
	/**
	 * @var ArOn_Db_Table
	 */
	public $formModel;

	/**
	 * @var ArOn_Crud_Form
	 */
	public $form;
	public $notEdit = false;
	protected $saveInDataBase = true;
	protected $filterPrefix;
	protected $_formActionName;
	protected $_width;
	
	function __construct($name = false, $title = null, $description = null, $required = null, $notEdit = false, $width = 200) {
		
		$this->name = $name;
		$this->title = $title;
		$this->description = $description;
		if ($required !== null)
		$this->required = $required;
		$this->notEdit = $notEdit;
		$this->_width = $width;
		$this->init ();
	}

	public function init() {
	}

	public function setForm($form) {
		$this->form = $form;
		$this->formModel = $form->getModel ();
	}

	public function createElement() {
		$this->loadHelper ();
		$name = ($this->elementFormName === false) ? $this->name : $this->elementFormName;		
		$this->elementClassName = self::getMyPluginLoader ()->load ( $this->_type );		
		$this->element = new $this->elementClassName ( $name, array ('disableLoadDefaultDecorators' => true ) );
		$this->element->addPrefixPath ( 'Crud_Form_Decorator', 'Crud/Form/Decorator/', 'decorator' );
		if ($this->_type == 'radio') {
			//		   /echo $this->element->helper;die;
		}
		if($this->elementHelper)
		$this->element->helper = $this->elementHelper;
		if ($this->_value !== null) {
			$this->element->setValue ( $this->_value );
		} elseif ($this->default !== null) {
			$this->element->setValue ( $this->default );
		}
		//$this->element->setValue($this->_value);
		$this->updateField ();
		if ($this->hidden) {
			$this->element->helper = 'formHidden';
			return $this->element;
		}
		$this->element->setLabel ( $this->title )->setDescription ( $this->description );
		$this->element->setAttrib ( 'formActionName', $this->_formActionName );
		$this->element->setAttrib ( 'width', $this->_width );
		if ($this->form instanceof Zend_Form) {
			$this->element->setAttrib ( 'form_id', $this->form->getAttrib ( 'id' ) );
			$this->setFilters();
		}
		$getData = ($this->filterPrefix) ?  $_GET [$this->filterPrefix] : $_GET;
		if ($this->notEdit && (! empty ( $getData [$this->name] ) || $this->form->actionId)) {
			$this->element->helper = 'formNotEdit';
		}
		return $this->element;
	}

	public function updateField() {

		if ($this->hidden) {
			$this->rowClass = 'hidden';
		}
		$this->decorator ();
		if ($this->required) {
			$this->element->setRequired ( TRUE );
		}
		if ($this->disabled == true) {
			$this->element->setAttrib ( 'disabled', 'disabled' );
		}

		if (isset ( $this->class )) {
			$this->element->setAttrib ( 'class', $this->class );
		}
		$this->setElementAttribs();
	}

	protected function decorator() {
		if($this->form->isDecorate()) $this->form->getDecoratorClass()->decorateField ( $this );
	}

	public function updateCurrentSelect(ArOn_Db_TableSelect $currentSelect) {
		$this->loadHelper ();
		return $currentSelect;
	}

	public function setTableName($table_name) {
		$this->tableName = $table_name;
	}

	protected static $_loader;

	static function getMyPluginLoader() {
		if (self::$_loader)
		return self::$_loader;

		self::$_loader = new Zend_Loader_PluginLoader ( array ('Zend_Form_Element_' => 'Zend/Form/Element/' ) );

		return self::$_loader;
	}

	public function addHelper($key, $value) {
		$this->helper [$key] = $value;
	}

	protected function loadHelper() {
		foreach ( $this->helper as $var => $value ) {
			$this->$var = $value;
		}
	}

	public function getInsertData() {
		if (! $this->saveInDataBase)
		return false;
		$data = array ();
		$data ['model'] = 'default';
		$data ['data'] = array ('key' => $this->getName (), 'value' => $this->getValue () );

		return $data;
	}

	public function preSaveAction($data = null) {

	}

	public function postSaveAction($data = null) {

	}

	public function setValue($value) {
		$this->_value = $value;
		if ($this->element instanceof Zend_Form_Element) {
			$this->element->setValue ( $this->_value );
		}
		return $this;
		//$this->form->getElement($this->name)->setValue($value);
	}

	public function setPrefixFilter($prefix){
		$this->prefixFilter = $prefix;
		return $this;
	}
	
	public function setFilters(){
		$filters = $this->getFilters();
		if(empty($filters))
			return false;		
		$this->element->setFilters($filters);
	}
	
	public function getFilters(){
		$filters = array();
		$filters[] = new ArOn_Zend_Filter_Replace(chr(226).chr(128).chr(139), '');
		return $filters;
	}
	
	public function covertValueType($value){
		if ($this->_covertType){
			if (is_array($value)) {
				foreach ($value as &$val) settype($val, $this->_covertType);
			}
			else {
				settype($value, $this->_covertType);
			}
		}
		
		return $value;
	}
	
	public function getValue() {
		$value = ($this->element instanceof Zend_Form_Element) ? $this->element->getValue() : $this->_value;
		return $this->covertValueType(($this->explode)?explode($this->explode, $value):$value);
	}
	
	public function getRenderValue(){
		return $this->getValue();
	}
	
	public function getName() {
		return $this->name;
	}

	public function noSave($notSaveInDataBase=true) {
		$this->saveInDataBase = !$notSaveInDataBase;
	}

	public function getElement() {
		return $this->element;
	}
	public function getTableName() {
		return $this->tableName;
	}
	public function isRequire() {
		return $this->required;
	}
	public function setFilterPrefix($prefix){
		$this->filterPrefix = $prefix;
		return $this;
	}
	public function setElementFormName($name){
		$this->elementFormName = $name;
		if($this->name === false) $this->name = $this->elementFormName;
		return $this;
	}
	public function getElementFormName(){
		return ($this->elementFormName === false) ? $this->name : $this->elementFormName;
	}
	public function setElementHelper($helper){
		$this->elementHelper = $helper;
		if($this->element instanceof Zend_Form_Element){
			$this->element->helper = $helper;
		}
		return $this;
	}

	public function setExplode($separator){
		$this->explode = $separator;
		return $this;
	}

	protected function setElementAttribs(){
		if (($this->element instanceof Zend_Form_Element) && count($this->_element_attribs)>0) {
			foreach ($this->_element_attribs as $name => $value){
				$this->element->setAttrib($name,$value);
			}
		}
	}

	public function addAttrib($name,$value){
		$this->_element_attribs[$name] = $value;
		return $this;
	}

	public function getTitle() {
		return $this->title;
	}
	
	public function setAjaxActionName($name){
		$this->_formActionName = $name;
		return $this;
	}
	
	public function setElementType($type){
		$this->_type = $type;
		return $this;
	}
	
}