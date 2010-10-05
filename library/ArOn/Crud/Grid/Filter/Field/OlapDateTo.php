<?php
class ArOn_Crud_Grid_Filter_Field_OlapDateTo extends ArOn_Crud_Grid_Filter_Field {

	public function updateField() {
		parent::updateField ();
		$this->element->setRequired ( true )->setValidators ( array (array ('NotEmpty', true ), 'Date' ) );
		$this->element->helper = 'GridFormCalendar';
	}

	public function getFieldWhere() {
	}

	public function applyFilterToCubeQuery(Olap_CubeQuery $cubeQuery) {
		$value = $this->getFieldValue ();
		$value = str_replace ( "-", "/", $value );
		$value = date ( 'Y-m-d', strtotime ( $value ) );
		if (empty ( $value )) {
			return;
		}
		if (isset ( $cubeQuery->filters ['date'] ) && $cubeQuery->filters ['date'] instanceof Olap_Filter_Higher) {
			$cubeQuery->filters ['date'] = new Olap_Filter_Between ( $cubeQuery->filters ['date']->value, $value );
		} else {
			$cubeQuery->filters ['date'] = new Olap_Filter_Lower ( $value, false );
		}
	}
}
