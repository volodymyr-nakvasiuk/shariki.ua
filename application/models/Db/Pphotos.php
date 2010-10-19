<?php
class Db_Pphotos extends ArOn_Db_Table {
	protected $_primary = 'photos_id';
	protected $_name = 'pphotos';
	protected $_name_expr = 'photos_name';
	//protected $_is_deleted = "is_deleted";
	protected $_order_expr = array('photos_main','photos_order');
	protected $_order_asc = array(false,true);
	
	protected $_referenceMap    = array(
	);

	public function update(array $data, $where){
			if(array_key_exists('photos_main',$data) && $data['photos_main'] == 1){
				$old = $this->fetchRow($where);
				parent::update(array('photos_main' => NULL),"photos_parent_id = '".$old['photos_parent_id']."'");
			}
			return parent::update($data, $where);
	}

	public function delete($where) {
		$rows = $this->fetchAll($where);
		$updatedParents = array();
		if ($rows){
			$rows = $rows->toArray();
			foreach ($rows as $row){
				$updatedParents[$row['photos_parent_id']] = array();
				if ($row['photos_main'] == 1){
					$updatedParents[$row['photos_parent_id']]['del'] = true;
				}
			}
		}
		$result = parent::delete($where);
		foreach ($updatedParents as $parent_id=>$opt){
			if ($opt['del']){
				$row = $this->fetchRow('photos_parent_id = '.$parent_id);
				if (!empty($row['photos_id'])){
					$this->update(array('photos_main' => 1), 'photos_id = '.$row['photos_id']);
				}
			}
		}
		return $result;
	}

	public function insert(array $data){
		if (!$data['photos_main'] && $data['photos_parent_id']){
			$row = $this->fetchRow('photos_parent_id = '.$data['photos_parent_id'].' AND photos_main=1');
			if (empty($row['photos_id'])){
				$data['photos_main'] = 1;
			}
		}
		return parent::insert($data);
	}
}