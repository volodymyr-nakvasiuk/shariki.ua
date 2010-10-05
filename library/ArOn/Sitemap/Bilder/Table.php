<?php
class ArOn_Sitemap_Bilder_Table extends ArOn_Sitemap_Bilder_Url{
	
	/**
	 * 
	 * @var ArOn_Db_Table
	 */
	protected $_table;
	
	public function __construct(ArOn_Db_Table $table, $loc, $lastmod = false, $changefreq = false, $priority = false){
		$this->_table = $table;
		parent::__construct(array(), $loc, $lastmod, $changefreq, $priority);
	}
	
	protected function _init(){
		$data = array();
		$result = $this->_table->fetchAll();
		if($result instanceof Zend_Db_Table_Rowset_Abstract){
			$data = $result->toArray();
		}
		$this->_data = $data;
	}
}