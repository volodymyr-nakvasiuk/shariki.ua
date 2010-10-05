<?php
class ArOn_Sitemap_Generator_News extends ArOn_Sitemap_Generator{
	
	protected $_newsNameSpace = "http://www.google.com/schemas/sitemap-news/0.9";
	
	public $sitemapHeader = '<urlset
                                xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                                xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9
                                http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd"
                                xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
                                xmlns:n="http://www.google.com/schemas/sitemap-news/0.9">
                         </urlset>';
	
	protected function addXMLUrl($xml, $url){
    	$row = $xml->addChild('url');
        $row->addChild('loc',htmlspecialchars($url['loc'],ENT_QUOTES,'UTF-8'));
        if (isset($url['lastmod'])){
        	$lastmod = $row->addChild('n:news', null, $this->_newsNameSpace);
        	if($url['lastmod']['publication']){
				$publication = $lastmod->addChild('n:publication', null, $this->_newsNameSpace);
				if($url['lastmod']['publication']['name']){
					$publication->addChild('n:name',$url['lastmod']['publication']['name'], $this->_newsNameSpace);
				}
				if($url['lastmod']['publication']['language']){
					$publication->addChild('n:language',$url['lastmod']['publication']['language'], $this->_newsNameSpace);
				}
			}
			if($url['lastmod']['publication_date']){
				$lastmod->addChild('n:publication_date',$url['lastmod']['publication_date'], $this->_newsNameSpace);
			}
			if($url['lastmod']['title']){
				$lastmod->addChild('n:title',$url['lastmod']['title'], $this->_newsNameSpace);
			}
			if($url['lastmod']['keywords']){
				$lastmod->addChild('n:keywords',$url['lastmod']['keywords'], $this->_newsNameSpace);
			}
        }
        //$row->addChild('lastmod', $url['lastmod']);
        
        if (isset($url['changefreq'])) $row->addChild('changefreq',$url['changefreq']);
        if (isset($url['priority'])) $row->addChild('priority',$url['priority']);
    }
    
}