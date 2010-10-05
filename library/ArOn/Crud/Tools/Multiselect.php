<?php
class ArOn_Crud_Tools_Multiselect {
	const EMPTY_CATEGORY = "n/a";
	const EMPTY_ITEM = "n/a";

	static function prepareOptions(ArOn_Db_TableSelect $select, $name = null, $category = null, $categoryName = null, $where = null, $emptyCategory = "n/a", $emptyItem = "n/a", $multiselect = false, &$fields = false, $category_order = null) {
		if ($emptyCategory === null)
		$emptyCategory = self::EMPTY_CATEGORY;
		if ($emptyItem === null)
		$emptyItem = self::EMPTY_ITEM;
		$model = $select->getTable();
		if ($where) {
			if (! is_array ( $where )) {
				$where = array ($where );
			}
			foreach ( $where as $whereItem ) {
				$select->where ( $whereItem );
			}
		}

		if(null !== $name) $select->columnsId () -> setColumn ('name',$name);
		else $select->columnsId ()->columnsName ();

		if ($category) {
			if(!is_array($category)) $category = array($category);
			foreach($category as $i => $cat){
				if($i == 0){
					$select->columnsJoinOne ( $cat, 'category' );
					if (null == $category_order) {
						$select->order ( array ('category', 'name' ) );
					} else {
						$select->order ( $category_order );
					}
				}else{
					$column = is_array($cat) ? $cat [-1] : $cat;
					$select->columnsJoinOne ( $cat, $column );
				}
			}
				
			$data = (($result = $model->fetchAll ( $select )) === null) ? array() : $result->toArray();
			$options = array ();
			foreach ( $data as $item ) {
				if ($item ['id'] === 0)
				continue;
				$category = trim ( $item ['category'] ) ? $item ['category'] : $emptyCategory;
				$options [$category] [$item ['id']] = trim ( $item ['name'] ) ? $item ['name'] : $emptyItem;
			}
		} else {
			$select->orderNatural ();
			//$select->order ( 'name' );
			$data = (($result = $model->fetchAll ( $select )) === null) ? array() : $result->toArray();
			$options = array ();
			foreach ( $data as $item ) {
				if ($item ['id'] === 0)
				continue;
				$options [$item ['id']] = trim ( $item ['name'] ) ? $item ['name'] : $emptyItem;
			}
			if ($multiselect) {
				$options = array (0 => $options );
			}
		}
		return $options;
	}

	static function prepareOptionsAll(ArOn_Db_TableSelect $select, $name = null, $category = null, $categoryName = null, $where = null, $emptyCategory = "n/a", $emptyItem = "n/a", $multiselect = false, &$fields = null, $category_order = null) {
		if ($emptyCategory === null)
		$emptyCategory = self::EMPTY_CATEGORY;
		if ($emptyItem === null)
		$emptyItem = self::EMPTY_ITEM;
		$model = $select->getTable();
		if ($where) {
			if (! is_array ( $where )) {
				$where = array ($where );
			}
			foreach ( $where as $whereItem ) {
				$select->where ( $whereItem );
			}
		}

		$select->columnsId ()->columnsAll ();
		if (null !== $fields) {
			if (is_array ( $fields )) {
				foreach ( $fields as $field => $value ) {
					if (is_array ( $value )) {
						if (isset ( $value ['case'] )) {
							$case = self::selectCase ( $field, $value ['case'], $select->getAlias () );
							$select->from ( null, array ($field . "_case" => new Zend_Db_Expr ( $case ) ) );
							unset ( $fields [$field] );
							$fields [$field . "_case"] = $value ['title'];
						}
					}
				}
			}
		}

		if ($category) {
			$select->columnsJoinOne ( $category, 'category' );
			if (null == $category_order) {
				$select->order ( array ('category', 'name' ) );
			} else {
				$select->order ( $category_order );
			}
			$data = (($result = $model->fetchAll ( $select )) === null) ? array() : $result->toArray();
			$options = array ();
			foreach ( $data as $item ) {
				if ($item ['id'] === 0)
				continue;
				$category = trim ( $item ['category'] ) ? $item ['category'] : $emptyCategory;
				$options [$category] [$item ['id']] = $item;
			}
		} else {
			$select->orderNatural ();
			$select->order ( 'name' );
			$data = (($result = $model->fetchAll ( $select )) === null) ? array() : $result->toArray();
			$options = array ();
			foreach ( $data as $item ) {
				if ($item ['id'] === 0)
				continue;
				$options [$category] [$item ['id']] = $item;
			}
			if ($multiselect) {
				$options = array (0 => $options );
			}
		}
		return $options;
	}

	static function getFirstOption(&$multiOptions) {
		$value = null;
		foreach ( $multiOptions as $key => $item ) {
			if (is_array ( $item )) {
				if (! empty ( $item )) {
					$value = reset ( array_keys ( $item ) );
					break;
				}
			} else {
				$value = $key;
				break;
			}
		}
		return $value;
	}

	static function getNameById(&$multiOptions, $id = null, $default = 'n/a') {
		if ($id == null)
		return $default;
		if ($multiOptions == null)
		return $default;
		foreach ( $multiOptions as $key => $value ) {
			if (is_array ( $value )) {
				if (isset ( $value [$id] )) {
					return $value [$id];
				}
			} else {
				if ($key == $id) {
					return $value;
				}
			}
		}
		return $default;
	}

	static function selectCase($field, $options, $table_name = "") {

		if (null !== $table_name) {
			$field = $table_name . "." . $field;
		}
		$select = "(CASE " . $field . " ";
		foreach ( $options as $key => $value ) {
			$select .= "WHEN '" . $key . "' THEN '" . $value . "' ";
		}
		$select .= " ELSE " . $field;
		$select .= " END)";

		return $select;
	}
}