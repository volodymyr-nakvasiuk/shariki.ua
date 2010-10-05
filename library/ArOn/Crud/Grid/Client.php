<?php
class ArOn_Crud_Grid_Client extends ArOn_Crud_Grid {
	
	public function render(){
		$this->preRender ();
		$data = $this->getData ();
		$row_html = '';
		
		foreach ( $data ['data'] as &$row ) {
			$row_id = @$row [$this->rowIdName];
			foreach ( $this->fields as $name => $field ) {
				$field->row_id = $row_id;
				if ($field instanceof ArOn_Crud_Grid_Column) {
					$row[$name] = $field->render($row);
				}	
			}
		}
		
		return $data;
	}
	
	protected function updateFormColumn($column){
		$this->fields[$column]->setElementView(false);
	}
}