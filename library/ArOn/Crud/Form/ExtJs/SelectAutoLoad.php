<?php
class ArOn_Crud_Form_ExtJs_SelectAutoLoad extends ArOn_Crud_Form_ExtJs_Element {

	/**
	 * Enter description here...
	 *
	 * @var Zend_Form_Select
	 */
	protected $_element;
	protected $_actionUrl = false;
	protected $_parent_id = false;
	protected $_options = array();
	protected $_store;

	public function init(){}

	protected function setup(){
		parent::setup();
		$this->_actionUrl = $this->_element->getAttrib('actionUrl');
		$this->_parent_id = $this->_element->getAttrib('parent_id');
		$this->_options = $this->_element->getMultiOptions();
		$this->setOptionsStore();
	}

	public function render(){
		$html = " new Ext.form.ComboBox( {
            fieldLabel: '" . $this->_fieldLabel . "',
            boxLabel: '" . $this->_boxLabel . "',
            hiddenName: '" . $this->_name . "',
            id: '".$this->_formActionName."-" . $this->_name . "-id-".$this->_element->getAttrib('actionId')."',
            allowBlank: " . $this->_allowBlank . ",
            width: " . $this->_width . ",
            store: new Ext.data.Store({
                isLoaded: false,
                url: '/". ArOn_Crud_Form::$ajaxModuleName  ."/json/" . (($this->_actionUrl) ? $this->_actionUrl : $this->_name) . "',
                ".(($this->_parent_id)?
                "autoLoad: false,
                baseParams: {
                    parent_id: " . $this->_parent_id . " 
                },":"")."
                reader: new Ext.data.JsonReader({
                    root:'rows'
                }, [
                    {name: 'optionValue', type: 'int'},
                    {name: 'displayText', type: 'string'},
                ])
            }),
            listeners: {
                ".(($this->_onchange)?"'select': ".$this->_onchange.",":"")."
                'afterrender': function() {
                    var store = this.getStore();
                    store.load({
                        callback: function(){
                            this.setValue('" . $this->_value . "');
                        },
                        scope: this
                    });
                }
            },
            valueField:'optionValue',
            displayField:'displayText',
            typeAhead: true,
            mode: 'local',
            triggerAction: 'all',
            emptyText:'-',
            selectOnFocus:true,
            editable: true
        })";
		return $html;
	}

	public function setOptionsStore($options = false){
		if($options) $this->_options = $options;
		$options = array();
		foreach ($this->_options as $name => $value){
			$option = "['" . $name . "', '" . $value . "']";
			$options[] = $option;
		}

		$html = "fields: ['optionValue', 'displayText'],
 		data: [" . implode(", ",$options) . "]";
		$this->_store = $html;
	}
	
}