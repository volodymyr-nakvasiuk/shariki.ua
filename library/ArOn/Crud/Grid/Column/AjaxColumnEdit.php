<?php
class ArOn_Crud_Grid_Column_AjaxColumnEdit extends ArOn_Crud_Grid_Column_FormColumn {

	public function render($row) {

		$attribs ['rowId'] = $this->row_id;

		if ($this->element instanceof Zend_Form_Element_Checkbox) {
			$attribs ['checked'] = $row [$this->key];
		}
		$attribs ['id'] = 'input_value_' . $this->row_id;
		$fieldName = $this->form->getFieldNameByColumnName ( $this->name );
		$value = $this->form->fields [$fieldName]->setValue ( $row [$this->key] )->getValue ();
		$helper = $this->_element_helper;

		$html = '<span id="row_edit_' . $this->row_id . '" rowid="' . $this->row_id . '" class="row-edit" rel="#tooltip_edit_' . $this->row_id . '" >' . $value . '</span>';
		$html .= '<span id="input_edit_' . $this->row_id . '" style="display:none">';
		$html .= $this->_element_view->$helper ( $this->_element_name, $value, $attribs, $this->_element_options );
		$html .= '<a href="javascript:saveColumn(' . $this->row_id . ')">Save</a>';
		$html .= '</span>';
		$html .= '<div style="display:none" class="tooltip-help" id="tooltip_edit_' . $this->row_id . '">
				<a href="javascript:editColumn(' . $this->row_id . ')" onclick="$(\'#cluetip\').hide()">Edit</a>
				</div>';
		$html = '<span class="span-edit" id="' . $this->row_id . '"
								
				>' . $html . '</span>';
		return $html;
	}
}