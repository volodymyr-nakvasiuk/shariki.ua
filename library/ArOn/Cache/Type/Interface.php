<?php
interface ArOn_Cache_Type_Interface {
	
	public function getObject();
	
	public function getBackendClassName();
	
	public function getCacheCoreClassName();
	
}