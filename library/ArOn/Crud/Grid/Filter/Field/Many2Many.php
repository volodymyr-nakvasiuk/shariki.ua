<?php
class ArOn_Crud_Grid_Filter_Field_Many2Many extends ArOn_Crud_Grid_Filter_Field_Select {

	public $parent_key;

	public $join_key;

	public $join_table;

	public $link_key;


	public function getFieldWhere(){
		 
		$value = $this->getFieldValue();
		if(!empty($value)){
			$where = "$this->table.$this->parent_key IN (Select $this->join_key From $this->join_table Where $this->link_key = ".ArOn_Crud_Tools_String::quote($value).")";
			return $where;
		}
	}

}
