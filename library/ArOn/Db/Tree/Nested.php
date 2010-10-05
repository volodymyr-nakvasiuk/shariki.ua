<?php
// SAMPLE DB TABLE STRUCTURE:

/* CREATE TABLE directory_nested (
   nested_id	INT UNSIGNED NOT NULL AUTO_INCREMENT,
   directory_id INT UNSIGNED NOT NULL,
   left_key	INT UNSIGNED NOT NULL,
   right_key	INT UNSIGNED NOT NULL,
   nested_level	INT UNSIGNED NOT NULL,
   PRIMARY KEY(nested_id),
   KEY(left_key, right_key, nested_level),
   INDEX(directory_id)
 );
 
 Where `directory_id` is key from directory table with name and options of node
*/

// This is believed to be the optimal Nested Sets use case. Use `one-to-one`
// relations on `cat_id` field between this `structure` table and 
// another `data` table in your database.
//
// Don't forget to make a single call to clear() 
// to set up the Root node in an empty table.
//
//****************************************************************************
// NOTE: Although you may use this library to retrieve data from the table,
//		 it is recommended to write your own queries for doing that.
//		 The main purpose of the library is to provide a simpler way to 
//		 create, update and delete records. Not to SELECT them.
//****************************************************************************
//
// IMPORTANT! DO NOT create either UNIQUE or PRIMARY keys on the set of
//            fields (`cat_left`, `cat_right` and `cat_level`)!
//            Unique keys will destroy the Nested Sets structure!
//
//****************************************************************************

//
//****************************************************************************
// Note: For best viewing of the code Tab size 4 is recommended
//****************************************************************************

class ArOn_Db_Tree_Nested extends ArOn_Db_Table {
	
	// These 3 variables are names of fields which are needed to implement 
	// Nested Sets. All 3 fields should exist in your table! 
	// However, you may want to change their names here to avoid name collisions.
	protected $left = 'left_key';
	protected $right = 'right_key';
	protected $level = 'level';

	protected $qryParams = '';
	protected $qryFields = '';
	protected $qryTables = '';
	protected $qryWhere = '';
	protected $qryGroupBy = '';
	protected $qryHaving = '';
	protected $qryOrderBy = '';
	protected $qryLimit = '';
	protected $sqlNeedReset = true;
	protected $sql;	// Last SQL query

//************************************************************************
// Returns a Left and Right IDs and Level of an element or false on error
// $ID : an ID of the element
	function getElementInfo($ID) { return $this->getNodeInfo($ID); }
	function getNodeInfo($ID) {
		$this->sql = 'SELECT '.$this->left.','.$this->right.','.$this->level.' FROM '.$this->_name.' WHERE '.$this->_primary.'=\''.$ID.'\'';
		$Data = $this->fetchAll($this->sql)->toArray();
		if(!empty($Data) && count($Data) == 1)
			return array((int)$Data[$this->left], (int)$Data[$this->right], (int)$Data[$this->level]); 
		else trigger_error("phpDbTree error: ".$this->_db->error(), E_USER_ERROR);
	}

//************************************************************************
// Clears table and creates 'root' node
// $data : optional argument with data for the root node
	function clear($data=array()) {
		
		// clearing table
		if((!$this->_db->query('TRUNCATE '.$this->_name)) && (!$this->_db->query('DELETE FROM '.$this->_name))) trigger_error("phpDbTree error: ".$this->_db->error(), E_USER_ERROR);

		// preparing data to be inserted
		if(sizeof($data)) {
			$fld_names = implode(',', array_keys($data)).',';
			if(sizeof($data)) $fld_values = '\''.implode('\',\'', array_values($data)).'\',';
		}
		$fld_names .= $this->left.','.$this->right.','.$this->level;
		$fld_values .= '1,2,0';

		// inserting new record
		$this->sql = 'INSERT INTO '.$this->_name.'('.$fld_names.') VALUES('.$fld_values.')';
		if(!($this->_db->query($this->sql))) trigger_error("phpDbTree error: ".$this->_db->error(), E_USER_ERROR);

		return $this->_db->insert_id();
	}

//************************************************************************
// Updates a record
// $ID : element ID
// $data : array with data to update: array(<field_name> => <fields_value>)
	function update($ID, $data) {
		$sql_set = '';
		foreach($data as $k=>$v) $sql_set .= ','.$k.'=\''.addslashes($v).'\'';
		return $this->_db->query('UPDATE '.$this->_name.' SET '.substr($sql_set,1).' WHERE '.$this->_primary.'=\''.$ID.'\'');
	}

//************************************************************************
// Inserts a record into the table with nested sets
// $ID : an ID of the parent element
// $data : array with data to be inserted: array(<field_name> => <field_value>)
// Returns : true on success, or false on error
	function insert($ID, $data) {
		if(!(list($leftId, $rightId, $level) = $this->getNodeInfo($ID))) trigger_error("phpDbTree error: ".$this->_db->error(), E_USER_ERROR);

		// preparing data to be inserted
		if(sizeof($data)) {
			$fld_names = implode(',', array_keys($data)).',';
			$fld_values = '\''.implode('\',\'', array_values($data)).'\',';
		}
		$fld_names .= $this->left.','.$this->right.','.$this->level;
		$fld_values .= ($rightId).','.($rightId+1).','.($level+1);

		// creating a place for the record being inserted
		if($ID) {
			$this->sql = 'UPDATE '.$this->_name.' SET '
				. $this->left.'=IF('.$this->left.'>'.$rightId.','.$this->left.'+2,'.$this->left.'),'
				. $this->right.'=IF('.$this->right.'>='.$rightId.','.$this->right.'+2,'.$this->right.')'
				. 'WHERE '.$this->right.'>='.$rightId;
			if(!($this->_db->query($this->sql))) trigger_error("phpDbTree error: ".$this->_db->error(), E_USER_ERROR);
		}

		// inserting new record
		$this->sql = 'INSERT INTO '.$this->_name.'('.$fld_names.') VALUES('.$fld_values.')';
		if(!($this->_db->query($this->sql))) trigger_error("phpDbTree error: ".$this->_db->error(), E_USER_ERROR);

		return $this->_db->lastInsertId();
	}

//************************************************************************
// Inserts a record into the table with nested sets
// $ID : ID of the element after which (i.e. at the same level) the new element 
//		 is to be inserted
// $data : array with data to be inserted: array(<field_name> => <field_value>)
// Returns : true on success, or false on error
	function insertNear($ID, $data) {
		if(!(list($leftId, $rightId, $level) = $this->getNodeInfo($ID)))
			trigger_error("phpDbTree error: ".$this->_db->error(), E_USER_ERROR);

		// preparing data to be inserted
		if(sizeof($data)) {
			$fld_names = implode(',', array_keys($data)).',';
			$fld_values = '\''.implode('\',\'', array_values($data)).'\',';
		}
		$fld_names .= $this->left.','.$this->right.','.$this->level;
		$fld_values .= ($rightId+1).','.($rightId+2).','.($level);

		// creating a place for the record being inserted
		if($ID) {
			$this->sql = 'UPDATE '.$this->_name.' SET '
			.$this->left.'=IF('.$this->left.'>'.$rightId.','.$this->left.'+2,'.$this->left.'),'
			.$this->right.'=IF('.$this->right.'>'.$rightId.','.$this->right.'+2,'.$this->right.')'
                               . 'WHERE '.$this->right.'>'.$rightId;
			if(!($this->_db->query($this->sql))) trigger_error("phpDbTree error:".$this->_db->error(), E_USER_ERROR);
		}

		// inserting new record
		$this->sql = 'INSERT INTO '.$this->_name.'('.$fld_names.') VALUES('.$fld_values.')';
		if(!($this->_db->query($this->sql))) trigger_error("phpDbTree error:".$this->_db->error(), E_USER_ERROR);

		return $this->_db->lastInsertId();
	}


//************************************************************************ 
// Assigns a node with all its children to another parent 
// $ID : node ID 
// $newParentID : ID of new parent node 
// Returns : false on error 
   function moveAll($ID, $newParentId) { 
      if(!(list($leftId, $rightId, $level) = $this->getNodeInfo($ID))) trigger_error("phpDbTree error: ".$this->_db->error(), E_USER_ERROR); 
      if(!(list($leftIdP, $rightIdP, $levelP) = $this->getNodeInfo($newParentId))) trigger_error("phpDbTree error: ".$this->_db->error(), E_USER_ERROR); 
      if($ID == $newParentId || $leftId == $leftIdP || ($leftIdP >= $leftId && $leftIdP <= $rightId)) return false; 

      // whether it is being moved upwards along the path
      if ($leftIdP < $leftId && $rightIdP > $rightId && $levelP < $level - 1 ) { 
         $this->sql = 'UPDATE '.$this->_name.' SET ' 
            . $this->level.'=IF('.$this->left.' BETWEEN '.$leftId.' AND '.$rightId.', '.$this->level.sprintf('%+d', -($level-1)+$levelP).', '.$this->level.'), ' 
            . $this->right.'=IF('.$this->right.' BETWEEN '.($rightId+1).' AND '.($rightIdP-1).', '.$this->right.'-'.($rightId-$leftId+1).', ' 
                           .'IF('.$this->left.' BETWEEN '.($leftId).' AND '.($rightId).', '.$this->right.'+'.((($rightIdP-$rightId-$level+$levelP)/2)*2 + $level - $levelP - 1).', '.$this->right.')),  ' 
            . $this->left.'=IF('.$this->left.' BETWEEN '.($rightId+1).' AND '.($rightIdP-1).', '.$this->left.'-'.($rightId-$leftId+1).', ' 
                           .'IF('.$this->left.' BETWEEN '.$leftId.' AND '.($rightId).', '.$this->left.'+'.((($rightIdP-$rightId-$level+$levelP)/2)*2 + $level - $levelP - 1).', '.$this->left. ')) ' 
            . 'WHERE '.$this->left.' BETWEEN '.($leftIdP+1).' AND '.($rightIdP-1) 
         ; 
      } elseif($leftIdP < $leftId) { 
         $this->sql = 'UPDATE '.$this->_name.' SET ' 
            . $this->level.'=IF('.$this->left.' BETWEEN '.$leftId.' AND '.$rightId.', '.$this->level.sprintf('%+d', -($level-1)+$levelP).', '.$this->level.'), ' 
            . $this->left.'=IF('.$this->left.' BETWEEN '.$rightIdP.' AND '.($leftId-1).', '.$this->left.'+'.($rightId-$leftId+1).', ' 
               . 'IF('.$this->left.' BETWEEN '.$leftId.' AND '.$rightId.', '.$this->left.'-'.($leftId-$rightIdP).', '.$this->left.') ' 
            . '), ' 
            . $this->right.'=IF('.$this->right.' BETWEEN '.$rightIdP.' AND '.$leftId.', '.$this->right.'+'.($rightId-$leftId+1).', ' 
               . 'IF('.$this->right.' BETWEEN '.$leftId.' AND '.$rightId.', '.$this->right.'-'.($leftId-$rightIdP).', '.$this->right.') ' 
            . ') ' 
            . 'WHERE '.$this->left.' BETWEEN '.$leftIdP.' AND '.$rightId 
            // !!! added this line (Maxim Matyukhin) 
            .' OR '.$this->right.' BETWEEN '.$leftIdP.' AND '.$rightId 
         ; 
      } else { 
         $this->sql = 'UPDATE '.$this->_name.' SET ' 
            . $this->level.'=IF('.$this->left.' BETWEEN '.$leftId.' AND '.$rightId.', '.$this->level.sprintf('%+d', -($level-1)+$levelP).', '.$this->level.'), ' 
            . $this->left.'=IF('.$this->left.' BETWEEN '.$rightId.' AND '.$rightIdP.', '.$this->left.'-'.($rightId-$leftId+1).', ' 
               . 'IF('.$this->left.' BETWEEN '.$leftId.' AND '.$rightId.', '.$this->left.'+'.($rightIdP-1-$rightId).', '.$this->left.')' 
            . '), ' 
            . $this->right.'=IF('.$this->right.' BETWEEN '.($rightId+1).' AND '.($rightIdP-1).', '.$this->right.'-'.($rightId-$leftId+1).', ' 
               . 'IF('.$this->right.' BETWEEN '.$leftId.' AND '.$rightId.', '.$this->right.'+'.($rightIdP-1-$rightId).', '.$this->right.') ' 
            . ') ' 
            . 'WHERE '.$this->left.' BETWEEN '.$leftId.' AND '.$rightIdP 
            // !!! added this line (Maxim Matyukhin) 
            . ' OR '.$this->right.' BETWEEN '.$leftId.' AND '.$rightIdP 
         ; 
      } 
      return $this->_db->query($this->sql) or trigger_error("phpDbTree error: ".$this->_db->error(), E_USER_ERROR); 
   } 

//************************************************************************
// Deletes a record wihtout deleting its children
// $ID : an ID of the element to be deleted
// Returns : true on success, or false on error
	function delete($ID) {
		if(!(list($leftId, $rightId, $level) = $this->getNodeInfo($ID))) trigger_error("phpDbTree error: ".$this->_db->error(), E_USER_ERROR);

		// Deleting record
		$this->sql = 'DELETE FROM '.$this->_name.' WHERE '.$this->_primary.'=\''.$ID.'\'';
		if(!$this->_db->query($this->sql)) trigger_error("phpDbTree error: ".$this->_db->error(), E_USER_ERROR);

		// Clearing blank spaces in a tree
		$this->sql = 'UPDATE '.$this->_name.' SET '
			. $this->left.'=IF('.$this->left.' BETWEEN '.$leftId.' AND '.$rightId.','.$this->left.'-1,'.$this->left.'),'
			. $this->right.'=IF('.$this->right.' BETWEEN '.$leftId.' AND '.$rightId.','.$this->right.'-1,'.$this->right.'),'
			. $this->level.'=IF('.$this->left.' BETWEEN '.$leftId.' AND '.$rightId.','.$this->level.'-1,'.$this->level.'),'
			. $this->left.'=IF('.$this->left.'>'.$rightId.','.$this->left.'-2,'.$this->left.'),'
			. $this->right.'=IF('.$this->right.'>'.$rightId.','.$this->right.'-2,'.$this->right.') '
			. 'WHERE '.$this->right.'>'.$leftId
		;
		if(!$this->_db->query($this->sql)) trigger_error("phpDbTree error: ".$this->_db->error(), E_USER_ERROR);

		return true;
	}

//************************************************************************
// Deletes a record with all its children
// $ID : an ID of the element to be deleted
// Returns : true on success, or false on error
	function deleteAll($ID) {
		if(!(list($leftId, $rightId, $level) = $this->getNodeInfo($ID))) trigger_error("phpDbTree error: ".$this->_db->error(), E_USER_ERROR);

		// Deleteing record(s)
		$this->sql = 'DELETE FROM '.$this->_name.' WHERE '.$this->left.' BETWEEN '.$leftId.' AND '.$rightId;
		if(!$this->_db->query($this->sql)) trigger_error("phpDbTree error: ".$this->_db->error(), E_USER_ERROR);

		// Clearing blank spaces in a tree
		$deltaId = ($rightId - $leftId)+1;
		$this->sql = 'UPDATE '.$this->_name.' SET '
			. $this->left.'=IF('.$this->left.'>'.$leftId.','.$this->left.'-'.$deltaId.','.$this->left.'),'
			. $this->right.'=IF('.$this->right.'>'.$leftId.','.$this->right.'-'.$deltaId.','.$this->right.') '
			. 'WHERE '.$this->right.'>'.$rightId
		;
		if(!$this->_db->query($this->sql)) trigger_error("phpDbTree error: ".$this->_db->error(), E_USER_ERROR);

		return true;
	}

//************************************************************************
// Enumerates children of an element 
// $ID : an ID of an element which children to be enumerated
// $start_level : relative level from which start to enumerate children
// $end_level : the last relative level at which enumerate children
//   1. If $end_level isn't given, only children of 
//      $start_level levels are enumerated
//   2. Level values should always be greater than zero.
//      Level 1 means direct children of the element
// Returns : a result id for using with other DB functions
	function enumChildrenAll($ID) { return $this->enumChildren($ID, 1, 0); }
	function enumChildren($ID, $start_level=1, $end_level=1) {
		if($start_level < 0) trigger_error("phpDbTree error: ".$this->_db->error(), E_USER_ERROR);

		// We could use sprintf() here, but it'd be too slow
		$whereSql1 = ' AND '.$this->_name.'.'.$this->level;
		$whereSql2 = '_'.$this->_name.'.'.$this->level.'+';

		if(!$end_level) $whereSql = $whereSql1.'>='.$whereSql2.(int)$start_level;
		else {
			$whereSql = ($end_level <= $start_level) 
				? $whereSql1.'='.$whereSql2.(int)$start_level
				: ' AND '.$this->_name.'.'.$this->level.' BETWEEN _'.$this->_name.'.'.$this->level.'+'.(int)$start_level
					.' AND _'.$this->_name.'.'.$this->level.'+'.(int)$end_level;
		}

		$this->sql = $this->sqlComposeSelect(array(
			'', // Params
			'', // Fields
			$this->_name.' _'.$this->_name.', '.$this->_name, // Tables
			'_'.$this->_name.'.'.$this->_primary.'=\''.$ID.'\''
				.' AND '.$this->_name.'.'.$this->left.' BETWEEN _'.$this->_name.'.'.$this->left.' AND _'.$this->_name.'.'.$this->right
				.$whereSql
		));

		return $this->_db->query($this->sql);
	}

//************************************************************************
// Enumerates the PATH from an element to its top level parent
// $ID : an ID of an element
// $showRoot : whether to show root node in a path
// Returns : a result id for using with other DB functions
	function enumPath($ID, $showRoot=false) {
		$this->sql = $this->sqlComposeSelect(array(
			'', // Params
			'', // Fields
			$this->_name.' _'.$this->_name.', '.$this->_name, // Tables
			'_'.$this->_name.'.'.$this->_primary.'=\''.$ID.'\''
				.' AND _'.$this->_name.'.'.$this->left.' BETWEEN '.$this->_name.'.'.$this->left.' AND '.$this->_name.'.'.$this->right
				.(($showRoot) ? '' : ' AND '.$this->_name.'.'.$this->level.'>0'), // Where
			'', // GroupBy
			'', // Having
			$this->_name.'.'.$this->left // OrderBy
		));

		return $this->_db->query($this->sql);
	}

//************************************************************************
// Returns query result to fetch data of the element's parent
// $ID : an ID of an element which parent to be retrieved
// $level : Relative level of parent
// Returns : a result id for using with other DB functions
	function getParent($ID, $level=1) {
		if($level < 1) trigger_error("phpDbTree error: ".$this->_db->error(), E_USER_ERROR);

		$this->sql = $this->sqlComposeSelect(array(
			'', // Params
			'', // Fields
			$this->_name.' _'.$this->_name.', '.$this->_name, // Tables
			'_'.$this->_name.'.'.$this->_primary.'=\''.$ID.'\''
				.' AND _'.$this->_name.'.'.$this->left.' BETWEEN '.$this->_name.'.'.$this->left.' AND '.$this->_name.'.'.$this->right
				.' AND '.$this->_name.'.'.$this->level.'=_'.$this->_name.'.'.$this->level.'-'.(int)$level // Where
		));

		return $this->_db->query($this->sql);
	}

//************************************************************************
	function sqlReset() {
		$this->qryParams = ''; $this->qryFields = ''; $this->qryTables = ''; 
		$this->qryWhere = ''; $this->qryGroupBy = ''; $this->qryHaving = ''; 
		$this->qryOrderBy = ''; $this->qryLimit = '';
		return true;
	}

//************************************************************************
	function sqlSetReset($resetMode) { $this->sqlNeedReset = ($resetMode) ? true : false; }

//************************************************************************
	function sqlParams($param='') { return (empty($param)) ? $this->qryParams : $this->qryParams = $param; }
	function sqlFields($param='') { return (empty($param)) ? $this->qryFields : $this->qryFields = $param; }
	function sqlSelect($param='') { return $this->sqlFields($param); }
	function sqlTables($param='') { return (empty($param)) ? $this->qryTables : $this->qryTables = $param; }
	function sqlFrom($param='') { return $this->sqlTables($param); }
	function sqlWhere($param='') { return (empty($param)) ? $this->qryWhere : $this->qryWhere = $param; }
	function sqlGroupBy($param='') { return (empty($param)) ? $this->qryGroupBy : $this->qryGroupBy = $param; }
	function sqlHaving($param='') { return (empty($param)) ? $this->qryHaving : $this->qryHaving = $param; }
	function sqlOrderBy($param='') { return (empty($param)) ? $this->qryOrderBy : $this->qryOrderBy = $param; }
	function sqlLimit($param='') { return (empty($param)) ? $this->qryLimit : $this->qryLimit = $param; }

//************************************************************************
	function sqlComposeSelect($arSql) {
		$joinTypes = array('join'=>1, 'cross'=>1, 'inner'=>1, 'straight'=>1, 'left'=>1, 'natural'=>1, 'right'=>1);

		$this->sql = 'SELECT '.$arSql[0].' ';
		if(!empty($this->qryParams)) $this->sql .= $this->sqlParams.' ';

		if(empty($arSql[1]) && empty($this->qryFields)) $this->sql .= $this->_name.'.'.$this->_primary;
		else {
			if(!empty($arSql[1])) $this->sql .= $arSql[1];
			if(!empty($this->qryFields)) $this->sql .= ((empty($arSql[1])) ? '' : ',') . $this->qryFields;
		}
		$this->sql .= ' FROM ';
		$isJoin = ($tblAr=explode(' ',trim($this->qryTables))) && ($joinTypes[strtolower($tblAr[0])]);
		if(empty($arSql[2]) && empty($this->qryTables)) $this->sql .= $this->_name;
		else {
			if(!empty($arSql[2])) $this->sql .= $arSql[2];
			if(!empty($this->qryTables)) {
				if(!empty($arSql[2])) $this->sql .= (($isJoin)?' ':',');
				elseif($isJoin) $this->sql .= $this->_name.' ';
				$this->sql .= $this->qryTables;
			}
		}
		if((!empty($arSql[3])) || (!empty($this->qryWhere))) {
			$this->sql .= ' WHERE ' . $arSql[3] . ' ';
			if(!empty($this->qryWhere)) $this->sql .= (empty($arSql[3])) ? $this->qryWhere : 'AND('.$this->qryWhere.')';
		}
		if((!empty($arSql[4])) || (!empty($this->qryGroupBy))) {
			$this->sql .= ' GROUP BY ' . $arSql[4] . ' ';
			if(!empty($this->qryGroupBy)) $this->sql .= (empty($arSql[4])) ? $this->qryGroupBy : ','.$this->qryGroupBy;
		}
		if((!empty($arSql[5])) || (!empty($this->qryHaving))) {
			$this->sql .= ' HAVING ' . $arSql[5] . ' ';
			if(!empty($this->qryHaving)) $this->sql .= (empty($arSql[5])) ? $this->qryHaving : 'AND('.$this->qryHaving.')';
		}
		if((!empty($arSql[6])) || (!empty($this->qryOrderBy))) {
			$this->sql .= ' ORDER BY ' . $arSql[6] . ' ';
			if(!empty($this->qryOrderBy)) $this->sql .= (empty($arSql[6])) ? $this->qryOrderBy : ','.$this->qryOrderBy;
		}
		if(!empty($arSql[7])) $this->sql .= ' LIMIT '.$arSql[7];
		elseif(!empty($this->qryLimit)) $this->sql .= ' LIMIT '.$this->qryLimit;

		if($this->sqlNeedReset) $this->sqlReset();

		return $this->sql;
	}
//************************************************************************
}