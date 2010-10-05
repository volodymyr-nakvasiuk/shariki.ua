<?php
class ArOn_Crud_Grid_Column_Delete extends ArOn_Crud_Grid_Column_Action {

	public function render($row) {
		$value = $this->row_id;
		$params = array ();
		foreach ( $_GET as $name => $param ) {
			if ($param != '')
			$params [] = "$name=$param";
		}
		$this->params = implode ( "&", $params );
		$this->link_action = 'onclick="if (confirm(\'Are you really want to delete this element?\')) {this.disabled = true;} else {return false}"';
		$html = parent::render ( $value );
		return $html;
	}
}