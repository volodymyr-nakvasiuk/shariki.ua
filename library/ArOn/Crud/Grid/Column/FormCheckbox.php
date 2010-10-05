<?php
class ArOn_Crud_Grid_Column_FormCheckbox extends ArOn_Crud_Grid_Column {

	/*function __construct($title)
	 {
		//parent::__construct($title, false);
		}*/

	public function render($row) {
		$value = $this->row_id;
		$attr = '';
		if (@$this->class)
		$attr .= ' class="' . $this->class . '" ';
		if (@$this->id)
		$attr .= ' id="' . $this->id . '" ';
		if (@$this->gridTitleField)
		$attr .= ' title="' . $row [$this->gridTitleField] . '" ';
		if (@$this->action)
		$attr .= ' action="' . $this->action . '" ';
		$attr = str_replace ( '{value}', $value, $attr );

		$html = '<input type="checkbox"  value="' . $value . '" ' . $attr . ' />';

		return $html;
	}
}