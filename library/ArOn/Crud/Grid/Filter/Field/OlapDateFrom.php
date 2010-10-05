<?php
class ArOn_Crud_Grid_Filter_Field_OlapDateFrom extends ArOn_Crud_Grid_Filter_Field {

	public function updateField() {
		parent::updateField ();
		$this->element->setRequired ( true )->setValidators ( array (array ('NotEmpty', true ), 'Date' ) );
		$this->element->helper = 'GridFormCalendar';
	}

	public function getFieldWhere() {
	}

	public function applyFilterToCubeQuery(Olap_CubeQuery $cubeQuery) {
		$value = $this->getFieldValue ();
		$value = date ( 'Y-m-d', strtotime ( $value ) );
		if (empty ( $value )) {
			return;
		}
		if (isset ( $cubeQuery->filters ['date'] ) && $cubeQuery->filters ['date'] instanceof Olap_Filter_Lower) {
			$cubeQuery->filters ['date'] = new Olap_Filter_Between ( $value, $cubeQuery->filters ['date']->value );
		} else {
			$cubeQuery->filters ['date'] = new Olap_Filter_Higher ( $value, false );
		}
	}
}
