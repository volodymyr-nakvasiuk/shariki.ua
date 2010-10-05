<?php
class ArOn_Menu_Db{
	
	/**
	 * @var ArOn_Db_Table
	 */
	protected $_menu_table;
	/**
	 * @var ArOn_Db_Table
	 */
	protected $_item_table;
	
	protected $_menus = array();
	protected $_items = array();
	
	public function __construct(){
		$this->_menu_table = ArOn_Crud_Tools_Registry::singleton ( 'Db_Menu' );
		$this->_item_table = ArOn_Crud_Tools_Registry::singleton ( 'Db_MenuItems' );
	}	
	
	public function getMenus(){
		if(empty($this->_menus)) $this->setMenus();
		return $this->_menus;
	}
	
	public function getMenuItems($parent_id,$menu_id){
		if(empty($this->_items)) $this->setItems();
		if(!array_key_exists($menu_id,$this->_items)) return array();
		return (array_key_exists($parent_id,$this->_items[$menu_id])) ? $this->_items[$menu_id][$parent_id] : array();
	}
	
	protected function setMenus(){
		$data = $this->_menu_table->fetchAll();
		foreach($data as $menu){
			$tmp_menu = array();
			$tmp_menu['id'] =  $menu['menu_id'];
			$tmp_menu['name'] =  $menu['menu_name'];
			$tmp_menu['role'] =  $menu['menu_role'];
			$this->_menus[] = $tmp_menu;
			unset($tmp_menu);
		}
	}
	
	protected function setItems(){
		$data = $this->_item_table->fetchAll();		
		foreach($data as $item){
			$tmp_item = array();
			$tmp_item['id'] =  $item['menu_item_id'];
			$tmp_item['label'] =  $item['menu_item_label'];
			$tmp_item['role'] =  $item['menu_item_role'];
			$tmp_item['module'] =  $item['menu_item_module'];
			$tmp_item['controller'] =  $item['menu_item_controller'];
			$tmp_item['action'] =  $item['menu_item_action'];
			$tmp_item['params'] =  $item['menu_item_params'];
			$tmp_item['status'] =  $item['menu_item_status'];
			if(!array_key_exists($item['menu_id'],$this->_items)) $this->_items [ $item['menu_id'] ] = array();
			if(!array_key_exists($item['parent_id'],$this->_items [ $item['menu_id'] ] )) $this->_items [ $item['menu_id'] ] [ $item['parent_id'] ] = array();
			$this->_items [ $item['menu_id'] ] [ $item['parent_id'] ] [] = $tmp_item;
			unset($tmp_item);
		}
	}
}