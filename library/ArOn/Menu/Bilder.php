<?php
class ArOn_Menu_Bilder{
	
	protected $_menu_items = array();
	
	/*
	 * @var ArOn_Menu_Db
	 */
	protected $_menu_model;
	protected $_menus;	
	protected $_menuBilder = array();
	/**
	 * @var Zend_Acl
	 */
	public static $acl;
	
	public function __construct($table = 'ArOn_Menu_Db'){
		$this->_menu_model = ArOn_Crud_Tools_Registry::singleton ( $table );
		$this->_generateMenus();
	}
	
	public function getMenu($menu_name){
		$id = ArOn_Crud_Tools_Array::arraySearchRecursive($menu_name,$this->_menuBilder);
	}
	
	public function getMenuBilderArray(){	
		return $this->_menuBuilder;
	}
	
	protected function _generateMenus(){		
		if(empty($this->_menus))$this->setMenus();
		foreach($this->_menus as $menu){			
			$this->_menuBuilder[$menu['id']] = array(
													'label' => $menu['name'],
													'title'=>$menu['name'], 
													'controller'=>'index',
													'pages' => $this->_menuGenerate(0,$menu['id'])
												);
			//$menuBuilderArray[$item['menu_id']]['pages'] = $this->_menuGenerate($menu_model,0,array());
		}
	}

	protected function _menuGenerate($parent_id, $menuid)
	{		
		$items = $this->_menu_model->getMenuItems($parent_id, $menuid);
		$menuArray = array();
		foreach($items as $item){
				
			if(self::$acl instanceof Zend_Acl){
				self::$acl->add(new Zend_Acl_Resource('menu_item:'.$item['id']));
				self::$acl->allow($item['role'],'menu_item:'.$item['id']);
			}
			$params = array();
			if($item['params'] != '' && $item['module'] != '')
			{
				if($item['params'][0] == '/') $item['params'] =  substr($item['params'], 1);
				$exploded = explode("/", $item['params']);
				$paramcnt = count($exploded);
				$x = 0;
				while($x <= $paramcnt)
				{
					$params[$exploded[$x]] = $exploded[$x+1];
					$x = $x+2;
				}
			}

			if($item['module'] == 'default' && $item['controller'] == 'page')
			{
				$tlabel = str_replace(" ","_", $item['label']);
				$params['p'] = preg_replace("/[^a-zA-Z0-9_\d]/i", "",$tlabel);
			}
			if($item['module'] != '')
			{
				$menuArray[$item['id']] = array(
					'label' 	=> $item['label'],
					'title'		=> $item['label'],
					'module'	=> $item['module'],
					'controller'=> $item['controller'],
					'action'	=> $item['action'],					
					'params'	=> $params,
					'visible'	=> $item['status'],
					//'parent' 	=> $parent_id,
					'resource'  => 'menu_item:'.$item['id'],
					'pages'		=> $this->_menuGenerate($item['id'],$menuid));

			}else{
				$menuArray[$item['id']] = array(
					'label' 	=> $item['label'],
					'title'		=> $item['title'],
					'uri'		=> $item['params'],
					'visible'	=> $item['status'],
					//'parent' 	=> $parent_id,
					'resource' 	=> 'menu_item:'.$item['id'],
					'pages' 	=> $this->_menuGenerate($item['id'],$menuid));

			}
		}
		return $menuArray;

	}
	
	protected function setMenus(){
		$this->_menus = $this->_menu_model->getMenus();
	}
}