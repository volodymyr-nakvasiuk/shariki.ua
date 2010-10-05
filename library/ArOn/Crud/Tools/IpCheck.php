<?php
class ArOn_Crud_Tools_IpCheck{
	
	protected $_request;
	protected $_model;
	protected $_errors;
	protected $_db_flag;
	
	protected $_ip;
	protected $_ipArr;
	protected $_ipValue;
	
	protected $_data = array();
	protected $_list = array();
	
	protected $_count = 0;
	
	public function __construct(Zend_Controller_Request_Http $request, $model, $fromdb = false){
		$this->_request = $request;
		$this->_model 	= $model;
		$this->_db_flag = $fromdb;
		$this->setup();
	}
	
	public function exists($ip = false){
		if($ip){
			if(!$this->_checkIpAddr($ip)){
				$this->_errors = 'Bad IP address! Should be in xxx.xxx.xxx.xxx format!';
				return false;
			}else{
				$this->_ip = $ip;
			}
			$this->_ipArr = $this->_getIpArr($this->_ip);
			$this->_ipValue = $this->_getIpValue($this->_ipArr);
		}
		return $this->_checkIpInList();
	}
	
	protected function setup(){
		$this->_initIP();
		if($this->_db_flag)
			$this->_initDb();
		else
			$this->_initFile();
	}
	
	protected function _initIP(){
		$ip = ArOn_Crud_Tools_IpCheck::getClientIp();
		if(!$this->_checkIpAddr($ip)){
			$this->_errors = 'Bad IP address! Should be in xxx.xxx.xxx.xxx format!';
			return false;
		}else{
			$this->_ip = $ip;
		}
		
		$this->_ipArr = $this->_getIpArr($this->_ip);
		$this->_ipValue = $this->_getIpValue($this->_ipArr);
		return true;
	}
	
	protected function _initFile(){
		if(!file_exists($this->_model))
			return $this;
		$this->_data = file($this->_model);
		$this->_setIpList();
		return $this;		
	}
	
	protected function _setIpList(){
		$this->_list = array();
		foreach($this->_data as $line){
			$item = array( 'eq' => false, 'from' => false , 'to' => false);
			$line = trim($line);
			$line = str_replace(" ","",$line);
			if(strpos($line,"-") !== false){
				$interval = explode("-",$line);
				if(!$this->_checkIpAddr($interval[0]) || !$this->_checkIpAddr($interval[1]))
					continue;
				$item ['from'] = $this->_getIpValue( $this->_getIpArr($interval[0]) );
				$item ['to'] = $this->_getIpValue( $this->_getIpArr($interval[1]) );
			}else{
				if(!$this->_checkIpAddr($line)){
					continue;
				}
				$item ['eq'] = $this->_getIpValue( $this->_getIpArr($line) );				
			}
			$this->_list [] = $item;
		}
		return $this;
	}
	
	protected function _checkIpInList(){
		$result = false; 
		foreach ($this->_list as $item){
			if($item ['eq'] !== false){
				$result = $this->_checkEqual($this->_ipValue,$item ['eq']);
			}elseif(($item ['from'] !== false) && ($item ['to'] !== false)){
				$result = $this->_checkInterval($this->_ipValue,$item ['from'],$item ['to']);
			}
			if($result === true) return true;
		}
		return false;
	}
	
	private function _checkEqual($ip,$value){
		return ($ip == $value) ? true : false;
	}
	
	private function _checkInterval($ip,$from,$to){
		return ($ip >= $from && $to >= $ip) ? true : false;
	}
	
	private function _checkIpAddr($ip){
		//first of all the format of the ip address is matched
		  if(preg_match("/^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})$/",$ip))
		  {
		    //now all the intger values are separated
		    $parts=explode(".",$ip);
		    //now we need to check each part can range from 0-255
		    foreach($parts as $ip_parts)
		    {
		      if(intval($ip_parts)>255 || intval($ip_parts)<0)
		      return false; //if number is not within range of 0-255
		    }
		    return true;
		  }
		  else
		    return false; //if format of ip address doesn't matches
	}
	
	/**
	 * returns IP address in array of integer values
	 *
	 * @return array
	 */
	private function _getIpArr($ip){
		$vars = explode('.',$ip);
		return array(
			intval($vars[0]),
			intval($vars[1]),
			intval($vars[2]),
			intval($vars[3])
		);
	}
	
	/**
	 * returns numerical representation of IP address.
	 *       Example: (from Right to Left)
	 *       1.2.3.4 = 4 + (3 * 256) + (2 * 256 * 256) + (1 * 256 * 256 * 256)
	 *       is 4 + 768 + 13,1072 + 16,777,216 = 16,909,060
	 *
	 * @return integer
	 */
	private function _getIpValue($ipArr){
		return $ipArr[3] + ( $ipArr[2] * 256 ) + ( $ipArr[1] * 256 * 256 ) + ( $ipArr[0] * 256 * 256 * 256 );
	}
	
	public static function getClientIp($checkProxy = true){
		if ($checkProxy && isset($_SERVER['HTTP_X_REAL_IP']) && $_SERVER['HTTP_X_REAL_IP'] != null) {
			$ip = $_SERVER['HTTP_X_REAL_IP'];
		}
		else if ($checkProxy && isset($_SERVER['HTTP_CLIENT_IP']) && $_SERVER['HTTP_CLIENT_IP'] != null) {
			$ip = $_SERVER['HTTP_CLIENT_IP'];
		}
		else if ($checkProxy && isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != null) {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else {
			$ip = $_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
}