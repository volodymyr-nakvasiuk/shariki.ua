<?php
class ArOn_Crud_Grid_Column_Checkbox extends ArOn_Crud_Grid_Column {

	protected $type = 'int';

	public function render($row) {
		$value = $row [$this->name];
		if ($value) {
			$html = '<img src="/images/icon_checkbox_active.gif" alt="enabled" />';
		} else {
			$html = '<img src="/images/icon_checkbox_inactive.gif" alt="disabled" />';
		}

		return $html;
	}
}