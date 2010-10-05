<?php
class Init_Directory {
	
	/**
	 *
	 * @var ArOn_Db_Table
	 */
	protected $_model;
	protected $_searchKeyParams;
	protected $_data;
	protected $_cache;
	
	public function __construct(){
		
		$this->setup();
	}
	
	protected function setup(){
		$this->_setModel();
		$this->_setData();
		$this->setSearchKeyParams();
		$this->_initCache();
	}
	
	protected function _setModel(){
		$this->_model = Db_Directory::getInstance();
		return $this;
	}
	
	protected function _setData(){
		//$this->_data = $this->_model->fetchAll()->toArray();
		$this->_data = array();
		return $this;
	}
	
	protected function _initCache(){
		$this->_cache = array();
		foreach ($this->_data as $adata){
			if(!array_key_exists($adata ['directory_alt_name'], $this->_cache))
				$this->_cache [$adata ['directory_alt_name']] = array();
			if(!empty($adata ['directory_parent_id']))
				$this->_cache[$adata ['directory_alt_name']] [$adata ['directory_index']] = $adata ;
		}
		return $this;
	}
	
	public function getDirectoryParams($searchParams){
			
		$result = array();
		if(empty($searchParams)) return $result;
		foreach ($this->_searchKeyParams as $key){
			$values = ArOn_Crud_Tools_Array::arraySearchRecursive($key,$searchParams,false,array(),true);
			if(!is_array($values)){
				$values = array($values);
			}
			foreach ($values as $value){
				if(empty($value)) continue;
				if(array_key_exists($key,$this->_cache) && ($directory_id = $this->_cache [$key] [$value] ['directory_id'])){					
					$data = $this->_cache [$key] [$value] ;
					$data['id'] = $directory_id;
					$result[$directory_id] = $data;
				}
			}
		}
		return $result;
	}
	
	/**
	 * Возвращает ключи всех выбранных категорий 
	 * @param $result
	 */
	public function getDirectoryParamsIds(array $result){
		$ids = array();
		if(empty($result)) return $ids;
		foreach($result as $adata){
			$ids[] =  $adata ['directory_id']; 
		}
		return $ids;
	}
	
	/**
	 * Возвращает ключи всех родителей 
	 * @param $result
	 */
	public function getDirectoryParentIds(array $result){
		$ids = array();
		if(empty($result)) return $ids;
		foreach($result as $adata){
			$ids[ $adata ['directory_parent_id'] ] =  $adata ['directory_parent_id']; 
		}
		return $ids;
	}
	
	/**
	 * Возвращает хеши всех возможных вариантов значений по категориям 
	 * @param $result
	 */
	public function getDirectoryHashByCategoryVariants(array $result, $sub_variants = false){
		if(empty($result)) return $result;
		$by_category = $this->getDirectoryIdsByCategory($result);
		$arrays = array();
		foreach($by_category as  $ids){
			$arrays [] = $ids;
		}		
		$all_variants = ArOn_Crud_Tools_Array::getAllValuesVariantsFromArrays($arrays, $sub_variants);
		$hashes = array();
		foreach($all_variants as $adata){
			$hashes [] = sha1(implode('-',$adata));
		}
		return $hashes;
	}
	
	/**
	 * Возвращает массив всех значений разбитых и отсортированных по категориям
	 * @param $result
	 */
	public function getDirectoryIdsByCategory(array $result){
		$by_category = array();
		if(empty($result)) return $by_category;		 
		foreach($result as $adata){
			$key = $adata ['directory_alt_name'];
			if($key == 'model')
				$key = 'mark';
			if(!array_key_exists($key ,$by_category))
				$by_category[ $key ] = array();
				$by_category[ $key ] [] =  $adata ['directory_id']; 
		}
		foreach($by_category as $k => $ids){
			sort($by_category [$k]);
		}
		ksort($by_category);
		return $by_category;
	}
	
	
	public function setSearchKeyParams($params = false){
		if(!is_array($params) && $params === false){
			$params = array('mark','model','region','country','function');
		}
		$this->_searchKeyParams = $params;
		return $this;
	}
}
?>