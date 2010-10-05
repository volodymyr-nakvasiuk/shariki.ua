<?php

class ArOn_Db_Filter_Cache extends ArOn_Db_Filter_Search {

	protected $_cache;
	protected $_criteria;
	
	public function __construct($expr , $field, $cache, $criteria = self::LIKE) {
		
		parent::__construct($field, $expr);
		
		if(empty($cache)) $this->_cache = false;
		elseif(is_array($cache)) $this->_cache = $cache;
		else $this->_cache = Zend_Registry::get($cache);
		$this->_criteria = $criteria;
		$this->setup();
	}

	public function filterWhere(ArOn_Db_TableSelect $select, /*ArOn_Db_Table */ $table, $alias) {
		if (! $this->_cache)
		return parent::filterWhere ( $select, $table, $alias );		
		
		if(count($this->_expr) == 0) return false;
		if(count($this->_expr) == 1){
			$this->_expr = $this->_expr[0];
			return parent::filterWhere ( $select, $table, $alias );
		}
		
		$where = implode ( ",", $this->_expr );
		$this->_fields = array_keys($this->_fields);
		return $this->_fields [0]." IN ($where)";
	}
	
	public function isCached(){
		if(count($this->_expr) == 0) return false;
		else return true;
	}
	
	protected function setup(){
		$this->_setExpr();
	}
	
	protected function _setExpr(){
		if ($this->_criteria === self::EQ) {
			$this->_expr = array_search($this->_expr,$this->_cache);
		} elseif ($this->_criteria === self::LIKE) {
			$this->_expr = $this->_arraySearch($this->_expr,$this->_cache);
		} elseif ($this->_criteria === self::BEGINS) {
			$this->_expr = $this->_arraySearch($this->_expr,$this->_cache,true);
		}
	}
	
	protected function _arraySearch($needle  , array $haystack, $type = NULL){
		$keys = array();
		$needle = strtolower($needle);
		foreach($haystack as $key => $value){
			$value = strtolower($value);
			if(strlen($needle) > strlen($value)) continue;
			elseif(strlen($needle) == strlen($value)){
				if($needle == $value){
					$keys[] = $key;
				}
			}
			elseif($type === true){
				$value = substr($value,0,strlen($needle));
				if($needle == $value) $keys[] = $key;
			}
			elseif($type === false){
				$value = substr($value,-strlen($needle));
				if($needle == $value) $keys[] = $key;
			}			
			elseif(strpos($value,$needle) !== false){
				$keys[] = $key; 
			}
		}
		return $keys;
	}
}
