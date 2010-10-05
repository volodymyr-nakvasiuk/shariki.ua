<?php
interface ArOn_Crud_Grid_Column_Interface {
	public function getName();

	public function getField();

	public function getTitle();

	public function isSorted();

	public function getAction();

	public function updateCurrentSelect($currentSelect, $table);
}