<?php
class ArOn_Crud_Form_ExtJs_MultiSelectAutoLoad extends ArOn_Crud_Form_ExtJs_MultiSelect {
	
	protected $_actionUrl = false;
	protected $_parent_id = false;	
	
	protected function setup(){
		$this->_actionUrl = $this->_element->getAttrib('actionUrl');
		$this->_parent_id = $this->_element->getAttrib('parent_id');
		parent::setup();		
	}
	
	public function render(){
		$html = " new Ext.form.MultiSelectField( {
            fieldLabel: '" . $this->_fieldLabel . "',
            boxLabel: '" . $this->_boxLabel . "',
            hiddenName: '" . $this->_name . "',
            id: '".$this->_formActionName."-" . $this->_name . "-id-".$this->_element->getAttrib('actionId')."',
            allowBlank: " . $this->_allowBlank . ",
            width: " . $this->_width . ",
            selectOnFocus:true,
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
        				scope: this,
						callback: function(){
							this.renderValues();
							" . ((!empty($this->_value)) ? "this.setValue('" . $this->_value . "');" : "")."
						}
					});
                }
            },
            valueField:'optionValue',
            ". 
			(($this->_group)
			?"groupField: 'group',":"") 
			."
            displayField:'displayText',
            typeAhead: true,
            ".(($this->_clearAllRender)?"clearAllRender: true, cleaAllText: '".$this->_clearAllText."',":"")."
            mode: 'local',
            triggerAction: 'all',
            emptyText:'-',
            selectOnFocus:true,
            editable: false
        })";
		return $html;
	}
	
}