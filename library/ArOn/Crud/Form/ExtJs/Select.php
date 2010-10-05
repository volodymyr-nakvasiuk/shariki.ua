<?php
class ArOn_Crud_Form_ExtJs_Select extends ArOn_Crud_Form_ExtJs_Element {

	/**
	 * Enter description here...
	 *
	 * @var Zend_Form_Select
	 */
	protected $_element;

	protected $_options = array();
	protected $_store;
	protected $_group = false;

	public function init(){}

	protected function setup(){
		parent::setup();
		$this->_options = $this->_element->getMultiOptions();
		$this->setOptionsStore();
	}

	public function render(){
		$html = " new Ext.form.ComboBox( {
    		fieldLabel: '" . $this->_fieldLabel . "',
    		boxLabel: '" . $this->_boxLabel . "',
            hiddenName: '" . $this->_name . "',
            allowBlank: " . $this->_allowBlank . ",
            width: " . $this->_width . ",
            store: new Ext.data.ArrayStore({
                " . $this->_store . "
            }),
            valueField:'optionValue',
            displayField:'displayText',
            value: '" . $this->_value . "',
            typeAhead: true,
            mode: 'local',
            triggerAction: 'all',
            emptyText:'-',
            selectOnFocus:true,
            editable: true
            ". 
		(($this->_group)
		?",
			tpl: new Ext.XTemplate(
			        '<tpl for=\".\">',
			        '<tpl if=\"this.group != values.group\">',
			        '<tpl exec=\"this.group = values.group\"></tpl>',
			        '<h1>{group}</h1>',
			        '</tpl>',
			        '<div class=\"x-combo-list-item\">{displayText}</div>',
			        '</tpl>'
		    )":"")
		.
		(($this->_onchange)
		?",
			listeners:{
		         'select': ".$this->_onchange."
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
				$this->_group = true;
				$group = $name;
				foreach ($value as $name=>$val){
					$val = addslashes($val);
					$option = "['" . $name . "', '" . $val . "', '" . $group . "']";
					$options[] = $option;
				}
			}else{
				$value = addslashes($value);
				$option = "['" . $name . "', '" . $value . "']";
				$options[] = $option;
			}
		}
		$html = "fields: ['optionValue', 'displayText'" .(($this->_group)?", 'group'":""). "],
                data: [" . implode(", ",$options) . "]";
		$this->_store = $html;
	}
}