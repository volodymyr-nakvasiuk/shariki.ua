<?php
class ArOn_Crud_Form_ExtJs_MultiSelect extends ArOn_Crud_Form_ExtJs_Element {

	/**
	 * Enter description here...
	 *
	 * @var Zend_Form_Select
	 */
	protected $_element;

	protected $_options;
	protected $_store;
	protected $_group = false;
	protected $_clearAllRender = false;
	protected $_clearAllText = 'Сбросить';

	public function init(){}

	protected function setup(){
		parent::setup();
		$this->_options = $this->_element->getMultiOptions();
		$this->setOptionsStore();
		$this->setValue();
	}

	public function render(){
		if(is_array($this->_value)) $this->_value = implode(',',$this->_value);
		$html = " new Ext.form.MultiSelectField( {
            fieldLabel: '" . $this->_fieldLabel . "',
            boxLabel: '" . $this->_boxLabel . "',
            hiddenName: '" . $this->_name . "',
            id: '".$this->_formActionName."-" . $this->_name . "-id-".$this->_element->getAttrib('actionId')."',
            allowBlank: " . $this->_allowBlank . ",
            valueField:'optionValue',
            displayField:'displayText',
            ". 
			(($this->_group)
			?"groupField: 'group',":"") 
			."selectOnFocus:true,
            ".((!empty($this->_value)) ? "value: '" . $this->_value . "',":"")."
            ".(($this->_clearAllRender)?"clearAllRender: true, cleaAllText: '".$this->_clearAllText."',":"")."
            mode: 'local',
            triggerAction: 'all',
            emptyText:'-',
            width:" . $this->_width . ",
            store: new Ext.data.ArrayStore({
                " . $this->_store . "
            })".
		(($this->_onchange)
		?",
			listeners:{
		         'change': ".$this->_onchange."
		    }
			":"")
		.
		    "
        })";
		return $html;
	}


	public function setOptionsStore($options = false){
		if($options) $this->_options = $options;
		$options = array();
		foreach ($this->_options as $name => $value){
			if(is_array($value)) {
				if(count($this->_options) > 1 || !empty($name)) $this->_group = true;
				$group = $name;
				foreach ($value as $name=>$val){
					$val = addslashes($val);
					if (!$val || $val == '-' || $val == -1){
						$this->_clearAllRender = true;
						$this->_clearAllText = $name;
					}
					$option = "['" . $name . "', '" . $val . "', '" . $group . "']";
					$options[] = $option;
				}
			}else{
				$value = addslashes($value);
				if (!$value || $value == '-' || $value == -1){
					$this->_clearAllRender = true;
					$this->_clearAllText = $name;
				}
				$option = "['" . $name . "', '" . $value . "']";
				$options[] = $option;
			}
		}
		$html = "fields: ['optionValue', 'displayText'" .(($this->_group)?", 'group'":""). "],
                data: [" . implode(", ",$options) . "]";
		$this->_store = $html;
	}
	

	protected function setValue(){
		if(!is_array($this->_value)) return true;
		$values = array();
		foreach( $this->_value as $value){
			if($value !== 0 && empty($value)) continue;			
			$values[] = addslashes($value);
		}
		$this->_value = implode(",",$values);
		return true;	
	}
	
}