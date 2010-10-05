<?php
class ArOn_Sitemap_Bilder{
	
	protected $_structure;
	
	protected $_urlObjects;
	
	
	public function __construct(array $structure,$cache = false){
		if(empty($structure))
			return false;
		$this->_structure = $structure;
		$this->setup();
	}
	
	public function getUrlValues(){
		$results = array();
		foreach ($this->_urlObjects as $urlObject){
			$urlValues = $urlObject->getUrls();
			$results = array_merge($results,$urlValues);
		}
		return $results;
	}
	
	protected function setup(){
		$this->_parseStructure();
	}
	
	protected function _parseStructure(){
		foreach($this->_structure as $part){
			$urlObjectType = array();
			$loc = false;$lastmod = false;$changefreq = false; $priority = false;
			foreach ($part as $type => $value){
				$type = strtolower($type);
				//$value = strtolower($value);

				if($type == 'data'){
					$urlObjectType ['type'] = 'Url';					
					$urlObjectType ['data'] = $value;
				}
				if($type == 'table'){
					$urlObjectType ['type'] = 'Table';					
					$urlObjectType ['data'] = $this->_initTable($value);
				}
				if($type == 'grid'){
					$urlObjectType ['type'] = 'Grid';					
					$urlObjectType ['data'] = $this->_initGrid($value);
				}
				if($type == 'news'){
					$urlObjectType ['type'] = 'Grid_News';					
					$urlObjectType ['data'] = $this->_initGrid($value);
				}
				elseif($type == 'loc'){
					$loc = $value;
				}
				elseif($type == 'lastmod'){
					$lastmod = $value;
				}
				elseif($type == 'changefreq'){
					$changefreq = $value;
				}
				elseif($type == 'priority'){
					$priority = $value;
				}
			}
			$className = "ArOn_Sitemap_Bilder_" . $urlObjectType ['type'];
			$this->_urlObjects[] = new $className ($urlObjectType ['data'], $loc, $lastmod, $changefreq, $priority );
		}
	}
	
	protected function _initTable($tableClassName){
		if($tableClassName instanceof ArOn_Db_Table)
			return $tableClassName;
		return ArOn_Db_Table::getInstance($tableClassName);
	}
	
	protected function _initGRid($gridClassName){
		if($gridClassName instanceof ArOn_Crud_Grid)
			return $gridClassName;
		return new $gridClassName;
	}
}