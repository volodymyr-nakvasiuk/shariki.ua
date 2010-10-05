<?php
interface ArOn_Crud_Grid_Filter_Field_Interface
{
	public function createElement();

	public function updateField();

	public function getFieldWhere();

}