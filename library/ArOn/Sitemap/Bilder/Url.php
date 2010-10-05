<?php
class ArOn_Sitemap_Bilder_Url{
	
	protected $_data;
	protected $_urls = array();
	
	protected $_loc;
	protected $_lastmod;
	protected $_changefreq;
	protected $_priority;
	
	public function __construct(array $data, $loc, $lastmod = false, $changefreq = false, $priority = false){
		$this->_data = $data;
		$this->_loc = $loc;
		$this->_lastmod = $lastmod;
		$this->_changefreq = $changefreq;
		$this->_priority = $priority;
		
		$this->_init();
	}
	
	protected function _init(){}
	
	public function getUrls($max = false){
		if(empty($this->_urls))
			$this->_setUrls($max);
		return $this->_urls;
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
				$url['lastmod'] = $this->_generateValue($this->_lastmod, $row);
				if ($url['lastmod'] === false) continue;
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
	
	protected function _generateValue($template,$data){
		$result = $template;
		$start = 0;
		while (($start = strpos($result,'[',$start)) !== false && ($end = strpos($result,']',$start)) !== false){
			$key = substr($result,$start+1,$end-$start-1);
			$value = (array_key_exists($key, $data)) ? $data [$key] : "";
			if (!$value && $value!==0 && $value!=="0") return false;
			$result = str_replace ( "[".$key."]", $value, $result );
		}
		$start = 0;
		while (($start = strpos($result,'{{',$start)) !== false && ($end = strpos($result,'}}',$start)) !== false){
			$CONST = substr($result,$start+2,$end-$start-2);
			$value = (defined($CONST )) ? constant($CONST ) : "";
			if (!$value && $value!==0 && $value!=="0") return false;
			$result = str_replace ( "{{".$CONST."}}", $value, $result );
		}
		$start = 0;
		while (($start = strpos($result,'{',$start)) !== false && ($end = strpos($result,'}',$start)) !== false){
			$key = substr($result,$start+1,$end-$start-1);
			$value = (key_exists($key, $data)) ? $data [$key] : "";
			if (!$value && $value!==0 && $value!=="0") return false;
			$result = str_replace ( "{".$key."}", $value, $result );
		}
		return $result;
	}
	
}