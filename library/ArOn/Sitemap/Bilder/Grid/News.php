<?php
class ArOn_Sitemap_Bilder_Grid_News extends ArOn_Sitemap_Bilder_Grid{
	
	protected function _init(){
		$this->_grid->where .= ' AND news_created_date>(NOW()-INTERVAL 3 DAY)';
		parent::_init();
	}
	
	protected function _setUrls($max){
		$i = 0;
		foreach($this->_data as $row){
			if($max !== false && $i > $max)
				break;
			$url = array ();
			$url['loc'] = $this->_generateValue($this->_loc, $row);
			if ($url['loc'] === false) continue;
			
			if($this->_lastmod !== false){
				$url['lastmod'] = array();
				if($this->_lastmod['publication']){
					$url['lastmod']['publication'] = array();
					if($this->_lastmod['publication']['name']){
						$url['lastmod']['publication']['name'] = $this->_generateValue($this->_lastmod['publication']['name'], $row);
					}
					if($this->_lastmod['publication']['language']){
						$url['lastmod']['publication']['language'] = $this->_generateValue($this->_lastmod['publication']['language'], $row);
					}
				}
				if($this->_lastmod['publication_date']){
					$url['lastmod']['publication_date'] = $this->_generateValue($this->_lastmod['publication_date'], $row);
					//$url['lastmod']['publication_date'] = date('Y-m-d\TH:i:sO', strtotime($url['lastmod']['publication_date']));
					$url['lastmod']['publication_date'] = date('c', strtotime($url['lastmod']['publication_date']));
				}
				if($this->_lastmod['title']){
					$url['lastmod']['title'] = $this->_generateValue($this->_lastmod['title'], $row);
				}
				if($this->_lastmod['keywords']){
					$url['lastmod']['keywords'] = $this->_generateValue($this->_lastmod['keywords'], $row);
					$url['lastmod']['keywords'] = str_replace(';', ',', $url['lastmod']['keywords']);
				}
			}
			if($this->_changefreq !== false){
				$url['changefreq'] = $this->_generateValue($this->_changefreq, $row);
				if ($url['changefreq'] === false) continue;
			}
			if($this->_priority !== false){
				$url['priority'] = $this->_generateValue($this->_priority, $row);
				if ($url['priority'] === false) continue;
			}
			
			$this->_urls[] = $url;
			$i++;
		}
		return true;
	}
	
}