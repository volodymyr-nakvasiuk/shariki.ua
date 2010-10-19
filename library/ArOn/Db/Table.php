<?php
class ArOn_Db_Table extends Zend_Db_Table {

	const ID = 'ID';
	const NAME = 'NAME';

	protected $_primary = 'id';
	protected $_name_expr = 'id';
	protected $_order_expr = null;
	protected $_order_asc = true;
	protected $_is_deleted;
	protected $_is_deleted_value = 0;
	
	protected $_type = 'table';
	protected $_tableExtentions = array('view');
	
	protected $_aclWhere;
	protected $_aclPath;
	
	protected $_duplicate_extension = array();
	
	/**
	 * 
	 * @var string or array
	 * 	Acl columns in $_aclPath table that implodes 
	 * 	with sql "OR" separator and with self::$authId value 
	 */
	protected $_aclColumn;

	public static $aclPlugins = array ();
	public static $authId;

	protected $_where;

	public static $processedModels = array ();
	
	/**
	 * 
	 * @var ArOn_Cache_Adapter
	 */
	protected static $_cacheAdapter;
	protected static $_cache = false;
	
	/**
	 * Options for table metadata cache object, setup in getInstance method 
	 * @var array
	 */
	protected static $_metadataCacheOptions = null;
	/**
	 * metadata cache object
	 * @var Zend_Cache_Core
	 */
	protected static $_metadataCacher = null;
	
	
	protected static $_instance = array();
	
	static function getInstance($className = false,$debug = false){
		if($className === false) $className = get_called_class();
		if (array_key_exists($className,self::$_instance) === false)
		{	 //var_dump($className);die;
			if(!class_exists($className)) {
				throw new Zend_Controller_Exception("Class " . $className ." does not exists!");
			}
			$class = new $className (array(ArOn_Db_Table::METADATA_CACHE => self::_getMetadataCacher()));
			if(self::$_cache === true){
				self::$_instance [$className] = self::$_cacheAdapter->getCacher($class);
			}else{
				self::$_instance [$className] = $class;
			}
		}
		
		return self::$_instance [$className];
	}
	
	protected static function _getMetadataCacher(){
		if( self::$_metadataCacheOptions === null)
			return null;
				
		if(self::$_metadataCacher === null)
			self::setMetadataCacher();
			
		return self::$_metadataCacher;
	}
	
	public static function setCacheAdapter(ArOn_Cache_Adapter $adapter){
		self::$_cache = true;
		self::$_cacheAdapter = $adapter;
		return true;
	}
	
	public static function isCached(){
		return self::$_cache;
	}
	
	public static function getDefaultAdapter($cache = true)
	{	
		if(self::$_cache && $cache === true){
			return self::$_cacheAdapter->getCacher(self::$_defaultDb);
		}else{
			return self::$_defaultDb;
		}
	}
	
	public static function setMetadataCacheOptions($frontend, $backend, $frontendOptions = array(), $backendOptions = array(), $customFrontendNaming = false, $customBackendNaming = false, $autoload = false){
		self::$_metadataCacheOptions = array(
			'frontend' => $frontend,
			'backend'  => $backend,
			'frontendOptions' => $frontendOptions,
			'backendOptions' => $frontendOptions,
			'customFrontendNaming' => $customFrontendNaming,
			'customBackendNaming' => $customBackendNaming,
			'autoload' => $autoload
		);
		self::setMetadataCacher();
	}
	
	public static function setMetadataCacher($cacheObject = null){
		if($cacheObject instanceof Zend_Cache_Core){
			self::$_metadataCacher = $cacheObject;
		}elseif ($cacheObject === false || self::$_metadataCacheOptions === null){
			self::$_metadataCacher = null;
		}else{
			self::$_metadataCacher = Zend_Cache::factory ( 	
						self::$_metadataCacheOptions['frontend'], 
						self::$_metadataCacheOptions['backend'], 
						self::$_metadataCacheOptions['frontendOptions'], 
						self::$_metadataCacheOptions['backendOptions'], 
						self::$_metadataCacheOptions['customFrontendNaming'], 
						self::$_metadataCacheOptions['customBackendNaming'], 
						self::$_metadataCacheOptions['autoload']
					);
		}
	}
	
	public function getAdapter($cache = true)
	{
		if(self::$_cache && $cache === true){
			return self::$_cacheAdapter->getCacher($this->_db);
		}else{
			return $this->_db;
		}
	}
	
	public function getReferenceEx($rule) {
		if ($rule instanceof ArOn_Db_Table)
		$rule = get_class ( $rule );
		if ($rule instanceof ArOn_Cache_Type_Table)
			$rule = $rule->getClass();
		// get direct reference from reference map
		if (isset ( $this->_referenceMap [$rule] )) {
			$ref = &$this->_referenceMap [$rule];
			if (! isset ( $ref ['refTable'] ))
			$ref ['refTable'] = ArOn_Crud_Tools_Registry::singleton ( $ref ['refTableClass'] );
			return $ref;
		}
		// get reverse reference from dependent tables
		if (in_array ( $rule, $this->_dependentTables )) {
			$refTable = ArOn_Crud_Tools_Registry::singleton ( $rule );
			$thisClass = get_class ( $this );
			foreach ( $refTable->_referenceMap as &$ref ) {
				if ($ref ['refTableClass'] !== $thisClass)
				continue;
				$newRef ["refTableClass"] = $rule;
				$newRef ["refTable"] = $refTable;
				$newRef ["refColumns"] = $ref ["columns"];
				$newRef ["columns"] = $ref ["refColumns"];
				$newRef ["reverse"] = true;
				return $newRef;
			}
		}
		// try to find reference from reference map by table name
		foreach ( $this->_referenceMap as &$ref ) {
			if ($ref ['refTableClass'] !== $rule && !(is_array($ref ['refTableClass']) && in_array($rule,$ref ['refTableClass'])))
				continue;
			if (! isset ( $ref ['refTable'] ))
			$ref ['refTable'] = ArOn_Crud_Tools_Registry::singleton ( $rule );
			return $ref;
			
		}
		// try to find reference from references of dependent tables by ref name
		foreach ( $this->_dependentTables as $tableClass ) {
			$refTable = ArOn_Crud_Tools_Registry::singleton ( $tableClass );
			if (isset ( $refTable->_referenceMap [$rule] )) {
				$ref = &$refTable->_referenceMap [$rule];
				$newRef ["refTableClass"] = $tableClass;
				$newRef ["refTable"] = $refTable;
				$newRef ["refColumns"] = $ref ["columns"];
				$newRef ["columns"] = $ref ["refColumns"];
				$newRef ["reverse"] = true;
				return $newRef;
			}
		}
		throw new Exception ( "Could not find join rule '$rule' for table '" . get_class ( $this ) . "'" );
	}

	public function getJoinPath($rules) {
		if (! $rules)
		return array ();
		if (! is_array ( $rules ))
		$rules = array ($rules );

		$rule = array_shift ( $rules );
		if ($rule instanceof Zend_Db_Table)
			$rule = get_class ( $rule );
		if ($rule instanceof ArOn_Cache_Type_Table)
			$rule = $rule->getClass();
		$ref = $this->getReferenceEx ( $rule );
		$result [$rule] = $ref;
		$refTable = $ref ['refTable'];
		$tail = $refTable->getJoinPath ( $rules );
		$result = (is_array($tail)) ? array_merge ( $result, $tail ) : $result;
		return $result;
	}

	public function select($alias = null) {
		$select = new ArOn_Db_TableSelect ( $this, $alias );
		$alias = $select->getAlias ();
		if ($this->_where) {
			$select->where ( $this->applyAlias ( $this->_where, $alias ) );
		}
		$select = $this->_setAcl($select);
		if ($this->_order_expr){
			if(!is_array($this->_order_expr)) $this->_order_expr = array($this->_order_expr);
			if(!is_array($this->_order_asc)) $this->_order_asc = array($this->_order_asc);
			foreach ($this->_order_expr as $i => $key){
				if ($this->_order_asc[$i]) $direction = 'ASC';
				else $direction = 'DESC';
				$this->_order($select, $key.' '.$direction);
			}
		}
		return $select;
	}

	public function getWhere() {
		return $this->_where;
	}

	public function getTableName() {
		return $this->_name;
	}
	
	public function getTableType() {
		return $this->_type;
	}
	
	public function getPrimary() {
		return is_array ( $this->_primary ) ? $this->_primary [1] : $this->_primary;
	}

	public function applyAlias($expr, $alias) {
		if ($expr instanceof Zend_Db_Expr)
		$expr = $expr->__toString ();
		if (strpos ( $expr, "`$this->_name`" ) === false) {
			return new Zend_Db_Expr ( "`$alias`.`$expr`" );
		}
		$expr = str_replace ( "`$this->_name`", "`$alias`", $expr );
		return new Zend_Db_Expr ( $expr );
	}

	public function getNameExpr($alias = null) {
		if(!$this->_name_expr) return null;
		if(!is_array($this->_name_expr)) return $alias ? $this->applyAlias ( $this->_name_expr, $alias ) : $this->_name_expr;
		$columns = array();
		foreach($this->_name_expr['columns'] as $column){
			if($column == ' ') $columns[] = "'".$column."'";
			elseif(!empty($column)) $columns[] = ($alias) ? $this->applyAlias ( $column, $alias ) : $column;
		}
		$name_expr = (array_key_exists('function',$this->_name_expr) && !empty($this->_name_expr['function']))
			? $this->_name_expr['function'] ."(" . implode(', ',$columns) . ")"
			: implode(', ',$columns);
			
		return new Zend_Db_Expr ($name_expr);
	}

	public function getOrderExpr() {
		return $this->_order_expr;
	}

	public function getOrderAsc() {
		return $this->_order_asc;
	}
	/*
	 protected function _setupMetadata()
	 {
	 $this->_cols = is_array($this->_primary) ? $this->_primary : array($this->_primary);
	 if (!is_array($this->_primary)) {
	 $this->_metadata[$this->_primary]['DATA_TYPE'] = null;
	 }
	 return true;
	 }
	 */
	
	/**
	 * Fix value in data array before update or insert.
	 * 
	 *  @return array
	 */
	public function fixData($data){
		if(empty($data))
			return $data;
		if(!is_array($data))
			return $data;
		foreach ($data as $key => $val) {
			if($val === null || $val === false || strtolower($val) === 'null') {
				$data [$key] = new Zend_Db_Expr('NULL');
			}
		}
		
		return $data;
	}
	
	public function insert(array $data){
		$data = $this->updateData($data);
		$_data = array();
		foreach ($this->_getCols() as $column){
			if(key_exists($column,$data)) $_data[$column] = $data[$column];
		}
		$_data = $this->fixData($_data);
		return parent::insert($_data);
	}
	
	public function update(array $data, $where){
		$_data = array();
		$where = $this->updateWhere($where);
		foreach ($this->_getCols() as $column){
			if(array_key_exists($column,$data)) $_data[$column] = $data[$column];
		}
		
		if (!empty($_data)){
			$_data = $this->fixData($_data);
			return parent::update($_data, $where);
		}
		else {
			return 0;
		}
	}
	
	public function duplicate($ids,$extension = null){
		if(!is_array($ids)) {
			$ids = array($ids);
		}
		if(!is_array($this->_duplicate_extension)) {
			$this->_duplicate_extension = array($this->_duplicate_extension);
		}
		$cols = $this->_getCols();
		$select = $this->select();
		$select->where( $this->getPrimary() . ' IN (' . implode(',', $ids ) . ')');
		$extension = (null !== $extension) ? array_merge($extension,$this->_duplicate_extension) : $this->_duplicate_extension;
		$extension[] = $this->getPrimary();
		$cols = array_diff($cols,$extension);
		$select->columns($cols);
		$data = $this->fetchAll($select);
		foreach ($data as $row){
			$insert_data = $row->toArray();
			$this->insert($insert_data);
		}
		return true;
	}
	
	public function delete($where) {
		$where = $this->updateWhere($where);
		self::$processedModels [] = get_class ( $this );

		if ($this->_is_deleted) {
			return $this->_db->update ( $this->_name, array ($this->_name . "." . $this->_is_deleted => 1 ), $where );
		}
		// Deleting child records in dependent tables
		if (! empty ( $this->_dependentTables )) {
			$dependModels = array();
			$rows = $this->fetchAll ( $where );
			$primary = $this->_primary;
			$ids = array ();
			foreach ( $rows as $row ) {
				$ids [] = $row->{$primary [1]};
			}			
			foreach ( $this->_dependentTables as $dependModelName ) {

				//Checking if $dependModelName is not processed before
				if (in_array ( $dependModelName, self::$processedModels )) {
					continue;
				}

				$dependModel = ArOn_Crud_Tools_Registry::singleton ( $dependModelName );
				$depName = $dependModel->info('name');
				if(in_array($depName,$dependModels))
					continue;
				$dependModels[] = $depName;
				
				if (! $dependModel instanceof ArOn_Db_Table && ! $dependModel instanceof ArOn_Cache_Type_Table) {
					throw new Exception ( 'Dependent Model Invalid' );
				}
				$depType = $dependModel->getTableType();
				if(in_array($depType,$this->_tableExtentions))
					continue;
				if (count ( $ids )) {
					$refTables = $dependModel->getReferenceEx ( $this );
					$new_where = $refTables ['columns'] . ' in (' . implode ( ', ', $ids ) . ')';
					//$res = $dependModel->delete ( $new_where );
				}
					
			}
		}

		return parent::delete ( $where );

	}
	
	public function restore($where) {
		$where = $this->updateWhere($where);
		if ($this->_is_deleted) {
			return $this->_db->update ( $this->_name, array ($this->_name . "." . $this->_is_deleted => 0 ), $where );
		}
		return false;
	}

	public function getIsDeleted() {
		return $this->_is_deleted;
	}
	public function getIsDeletedValue(){
		return $this->_is_deleted_value;
	} 
	// backward compatibility functions
	public function getRowById($id) {
		$is_deleted = $this->_is_deleted;
		$this->_is_deleted = false;
		$res = call_user_func_array(array($this, "find"), is_array($id)?$id:array($id))->current ();
		//$res = $this->find ( $id )->current ();
		$this->_is_deleted = $is_deleted;
		return ($res) ? $res->toArray () : array ();
	}

	// backward compatibility functions
	public function getRowByParam($where = null) {
		$res = $this->fetchRow ( $where );
		return ($res) ? $res->toArray () : array ();
	}
	
	// backward compatibility functions
	public function getRowByFieldValue($fieldName, $value) {
		$where = $this->q("`$fieldName` = ?", $value);
		$row = $this->getRowByParam ( $where );
		return $row;
	}
	
	
	// backward compatibility functions
	public function getFieldByParam($fieldName, $paramName, $paramValue) {
		$where = $this->q("`$paramName` = ?",$paramValue);
		$row = $this->getRowByParam ( $where );
		return isset ( $row [$fieldName] ) ? $row [$fieldName] : null;
	}

	// backward compatibility functions
	function exists($where) {
		$result = $this->fetchRow ( $where );
		return $result !== null;
	}
	
	// set if exist acl rules for table
	protected function _setAcl(ArOn_Db_TableSelect $select) {
		if (! $this->_aclWhere)
		return $select;

		$params = false;
		$plugins = self::$aclPlugins;
		if (is_object ( $plugins )) {
			$params = $plugins->getList ();
			$aclPath = $plugins->getPath ();
			$aclColumn = $plugins->getColumn ();
		} elseif (! empty ( self::$authId )) {
			$params = array (self::$authId );
			$aclPath = $this->_aclPath;
			if(!empty($this->_aclColumn)){
				$aclColumn = (is_array($this->_aclColumn)) ? $this->_aclColumn : array($this->_aclColumn);
			}else{
				$aclColumn = array($this->_primary);
			}
		}

		//if (!$params) return false;
		if ($params === false) {
			return $select;
		} elseif (empty ( $params )) {
			$params = array (- 1 );
		}
		if(!empty($aclPath)){
			$result = $this->getJoinPath($aclPath);
			$rule = array_pop($result);
			$select->columnsJoinOne ( $aclPath, $aclColumn );
		}else{
			$refTableClass = $this->_name;
		}
		foreach ( $params as $value ) {
			foreach($aclColumn as $column){
				$sql[] = "`$refTableClass`.$column = '$value'";
			}
			$sql = "(" . implode ( ' OR ', $sql ) . ")";
			$where [] = "(" . $sql . ")";
		}
		$aclWhere = "(" . implode ( ' OR ', $where ) . ")";
		$select->where(str_replace ( "%s", $aclWhere, $this->_aclWhere ));
		return $select;
	}

	public function q($text, $value, $type = null, $count = null){
		return $this->getAdapter()->quoteInto($text, $value, $type, $count);
	}
	
	protected function updateData($data){
		if (! empty ( self::$authId ) && ! empty ( $this->_aclColumn ) && ( empty($this->_aclPath) || $this->_aclPath == $this->_name)) {
			$params = array (self::$authId);
			$data [$this->_aclColumn] = self::$authId;
		}
		return $data;
	}
	
	protected function updateWhere($where){
		if (! empty ( self::$authId ) && ! empty ( $this->_aclColumn ) && ( empty($this->_aclPath) || $this->_aclPath == $this->_name)) {
			$params = array (self::$authId);
			$where = trim($where);
			if(empty($where)){
				$where  = $this->_aclColumn ." = ".self::$authId;
			}else{
				$where  .= " AND " . $this->_aclColumn ." = ".self::$authId;
			}
		}
		return $where;
	}
	
	public function getClass(){
		return get_class ( $this );
	}
	
}
