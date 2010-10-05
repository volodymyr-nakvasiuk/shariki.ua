<?php
class ArOn_Crud_Grid_Column_FormColumnExtJs extends ArOn_Crud_Grid_Column_FormColumn {
	public function render($row){
		if ($this->element instanceof Zend_Form_Element_Checkbox) {
			$this->attibutes['onchange'] = 'javascript:if(this.checked){var val = 1;}else{var val = 0;} Ext.Ajax.request({url: \'/'. ArOn_Crud_Form::$ajaxModuleName .'/grid/'.$this->grid->ajaxActionName.'/updatevalue\',params: {\'id\':'.$this->row_id.', \'name\' : \''.$this->name.'\', \'value\': val}});';
		}
		else {
			$this->attibutes['onchange'] = 'javascript:Ext.Ajax.request({url: \'/'. ArOn_Crud_Form::$ajaxModuleName .'/grid/'.$this->grid->ajaxActionName.'/updatevalue\',params: {\'id\':'.$this->row_id.', \'name\' : \''.$this->name.'\', \'value\': this.value}});';
		}
		$this->attibutes['style'] = 'width: 95%;';
		return	parent::render ($row);
	}
}
?>