<?php
class ArOn_Crud_Form_ExtJs_Element {

	/**
	 * Enter description here...
	 *
	 * @var Zend_Form_Element
	 */
	protected $_element;

	protected $_validators;
	protected $_filters;

	protected $actionUrl = false;
	protected $_xtype = 'textfield';
	protected $_inputType = 'text';
	protected $_name;
	protected $_fieldLabel;
	protected $_boxLabel;
	protected $_value;
	protected $_id;
	protected $_allowBlank = 'true';
	protected $_onchange = false;
	protected $_width = 615;
	protected $_formActionName;
	
	public function __construct(Zend_Form_Element $element){
		$this->_element = $element;
		$this->init();
		$this->setup();
	}

	public function init(){}

	protected function setup(){
		$this->_validators = $this->_element->getValidators();
		$this->_filters = $this->_element->getFilters();
		$this->_name = $this->_element->getName();
		$this->_fieldLabel = $this->_element->getLabel();
		$this->_boxLabel = $this->_element->getDescription();
		$this->_onchange = $this->_element->getAttrib('onchange');		
		$this->_width = $this->_element->getAttrib('width');
		$this->_formActionName = $this->_element->getAttrib('formActionName');
		$this->_value = preg_replace("/(\r|\n)/",'',$this->_element->getValue());
		$this->_id = $this->_element->getId();
		$this->actionUrl = $this->_element->getAttrib('actionUrl');
		if($this->_element->isRequired()) $this->_allowBlank = 'false';
	}

	public function render(){
		$html = " new Ext.form.TextField( {
			inputType: '" . $this->_inputType . "',
    		fieldLabel: '" . $this->_fieldLabel . "',
    		boxLabel: '" . $this->_boxLabel . "',
            name: '" . $this->_name . "',
            //id: '" . $this->_id . "',
            allowBlank: " . $this->_allowBlank . ",
            value: '" . addslashes($this->_value) . "',
            width:" . $this->_width . "
		})";
		return $html;
	}

}