<?php
class ArOn_Sitemap_Bilder_Grid extends ArOn_Sitemap_Bilder_Url{
	
	/**
	 * 
	 * @var ArOn_Crud_Grid
	 */
	protected $_grid;
	protected $_limit = 'all';
	
	public function __construct(ArOn_Crud_Grid $grid, $loc, $lastmod = false, $changefreq = false, $priority = false){
		$this->_grid = $grid;
		parent::__construct(array(), $loc, $lastmod, $changefreq, $priority);
	}
	
	protected function _init(){
		$this->_grid->setLimit($this->_limit);
		$result = $this->_grid->getDataWithRenderValues();
		$this->_data = $result['data'];
	}
}