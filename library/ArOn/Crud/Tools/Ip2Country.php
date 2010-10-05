<?php

define('IP_STR',1);
define('IP_VALUE',2);
define('IP_RANGE_NUMERICAL',3);
define('IP_RANGE',4);
define('IP_REGISTRY',5);
define('IP_ASSIGNED_UNIXTIME',6);
define('IP_COUNTRY_ISO',7);
define('IP_COUNTRY_CODE',8);
define('IP_COUNTRY_NAME',9);
define('IP_INFO',10);

class ArOn_Crud_Tools_Ip2Country {

	/**
	 * @param string $ip
	 * @param ip $method
	 */
	function __construct($ip,$dbConfig=array()){
		if(!$this->checkIpAddr($ip)){
			die('Bad IP address! Should be in xxx.xxx.xxx.xxx format!');
		}else{
			$this->_ip = $ip;
		}
		
		if(!is_array($dbConfig)){
			die('Error! Database configuration not set! #1');
		}else{
			$this->dbConfig = $dbConfig;
		}
		
		$this->_setData();
				
		if(!$this->ipInfoArr){
			die ('Error during reciving informations about IP address!');
		}else{
			$this->ipInfoArr['IP_STR'] = $this->_ip;
			$this->ipInfoArr['IP_VALUE'] = $this->ipValue;
			$this->ipInfoArr['IP_FROM_STR'] = $this->getIpFromValue($this->ipInfoArr['IP_FROM']);
			$this->ipInfoArr['IP_TO_STR'] = $this->getIpFromValue($this->ipInfoArr['IP_TO']);
		}
	}
	
	function __destruct(){
		/*if($this->db)
			mysql_close($this->db);*/
	}
	
	/**
	 * IP address
	 *
	 * @var string
	 */
	private $_ip = '';
	
	/**
	 * Numerical representation of IP address
	 *       Example: (from Right to Left)
	 *       1.2.3.4 = 4 + (3 * 256) + (2 * 256 * 256) + (1 * 256 * 256 * 256)
	 *       is 4 + 768 + 13,1072 + 16,777,216 = 16,909,060
	 * @var integer
	 */
	private $ipValue = NULL;
	
	/**
	 * database conection configuration
	 * feel free to replece our db conection and use Your favorite database abstraction layer (ie. ADOdb)
	 *
	 * @var array
	 */
	public $dbConfig = array();
	
	/**
	 * database conection object
	 * feel free to replece our db conection and use Your favorite database abstraction layer (ie. ADOdb)
	 *
	 * @var object
	 */
	public $db = false;
	
	/**
	 * IP address in form of array of integer values
	 *
	 * @var string
	 */
	private $ipArr = array();
	
	/**
	 * IP address information array
	 *
	 * @var string
	 */
	private $ipInfoArr = false;
	
	/**
	 * returns information about IP adrress
	 *
	 * @param integer $mode
	 * @return mixed
	 */
	public function getInfo($mode=IP_INFO){
		if(!in_array($mode,array( IP_STR , IP_VALUE, IP_RANGE_NUMERICAL, IP_RANGE, IP_REGISTRY, IP_ASSIGNED_UNIXTIME , IP_COUNTRY_ISO, IP_COUNTRY_CODE, IP_COUNTRY_NAME, IP_INFO, ))){
			die('Error! Bad getInfo() mode!');
		}else switch($mode){
			case IP_STR:
				return $this->ipInfoArr['IP_STR'];
				break;
			case IP_VALUE:
				return $this->ipInfoArr['IP_VALUE'];
				break;
			case IP_RANGE_NUMERICAL:
				return array(
					 'FROM' => $this->ipInfoArr['IP_FROM'],
					 'TO' => $this->ipInfoArr['IP_TO']
				);
				break;
			case IP_RANGE:
				return array(
					 'FROM' => $this->ipInfoArr['IP_FROM_STR'],
					 'TO' => $this->ipInfoArr['IP_TO_STR']
				);
				break;
			case IP_REGISTRY:
				return $this->ipInfoArr['REGISTRY'];
				break;
			case IP_ASSIGNED_UNIXTIME:
				return $this->ipInfoArr['ASSIGNED'];
				break;
			case IP_COUNTRY_ISO:
				return $this->ipInfoArr['CTRY'];
				break;
			case IP_COUNTRY_CODE:
				return $this->ipInfoArr['CNTRY'];
				break;
			case IP_COUNTRY_NAME:
				return $this->ipInfoArr['COUNTRY'];
				break;
			case IP_INFO:
			default:
				return $this->ipInfoArr;
				break;
		}
	}
	
	private function _setData(){
		
		$this->ipArr = $this->getIpArr();
		$this->ipValue = $this->getIpValue();
		if(function_exists('geoip_country_code_by_name')){
			$this->ipInfoArr = array();
			//IP_FROM 	IP_TO 	REGISTRY 	ASSIGNED 	CTRY 	CNTRY 	COUNTRY
			$this->ipInfoArr['IP_FROM'] = $this->ipValue;
			$this->ipInfoArr['IP_TO'] = $this->ipValue;
			$this->ipInfoArr['CTRY'] = geoip_country_code_by_name ($this->_ip);
			$this->ipInfoArr['CNTRY'] = geoip_country_code3_by_name ($this->_ip);
			$this->ipInfoArr['COUNTRY'] = geoip_country_name_by_name ($this->_ip);
			//$this->ipInfoArr['REGISTRY'] = geoip_isp_by_name ($this->_ip);
			$this->ipInfoArr['REGISTRY'] = null;
			$this->ipInfoArr['ASSIGNED'] = null;
		}else{
			$this->ipInfoArr = $this->dbGetRow();
		}
	}
	
	/**
	 * validate IP address
	 *
	 * @param string $ip
	 * @return boolean
	 */
	private function checkIpAddr($ip=''){
		
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
		
		
		//return eregi('^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$',$ip);
	}
	
	/**
	 * returns IP address in array of integer values
	 *
	 * @return array
	 */
	private function getIpArr(){
		$vars = explode('.',$this->_ip);
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
	private function getIpValue(){
		return $this->ipArr[3] + ( $this->ipArr[2] * 256 ) + ( $this->ipArr[1] * 256 * 256 ) + ( $this->ipArr[0] * 256 * 256 * 256 );
	}
	
	/**
	 * returns IP numer from numerical representation.
	 *       Example: (from Right to Left)
	 *       1.2.3.4 = 4 + (3 * 256) + (2 * 256 * 256) + (1 * 256 * 256 * 256)
	 *       is 4 + 768 + 13,1072 + 16,777,216 = 16,909,060
	 *
	 * @param integer $value
	 * @param boolean $returnAsStr
	 * @return mixed
	 */
	private function getIpFromValue($value=0,$returnAsStr=true){
		$ip[0] = floor( intval($value) / (256*256*256) );
		$ip[1] = floor( ( intval($value) - $ip[0]*256*256*256 ) / (256*256) );
		$ip[2] = floor( ( intval($value) -$ip[0]*256*256*256 -$ip[1]*256*256 ) / 256 );
		$ip[3] = intval($value) - $ip[0]*256*256*256 - $ip[1]*256*256 - $ip[2]*256;
		if($returnAsStr){
			return $ip[0].'.'.$ip[1].'.'.$ip[2].'.'.$ip[3];
		}else{
			return $ip;
		}
	}
	
	/**
	 * returns SQL used to get iformation from ip2country database
	 *
	 * @return string
	 */
	private function getIpSelectSQL(){
		if(empty($this->dbConfig['tableName'])){
			$this->dbConfig['tableName'] = 'ip_to_country'; //setting default mysql port name
			echo "phpPp2Country table name not selected! traying default value: '".$this->dbConfig['tableName']."'";
		}
		return 'SELECT * FROM '.$this->dbConfig['tableName'].' WHERE IP_FROM <= '.$this->ipValue.' AND IP_TO >= '.$this->ipValue;
	}
	
	/**
	 * connect to database
	 * feel free to replece our function and use here Your favorite(s) database abstraction layer (ie. ADOdb)
	 *
	 * @return object - database conection resource
	 */
	private function dbConnect(){
		if(is_array($this->dbConfig)){
			if(empty($this->dbConfig['host'])){
				$this->dbConfig['host'] = 'localhost'; //setting default mysql port name
				echo "Database connection host not selected! traying default value: '".$this->dbConfig['port']."'";
			}
			if(intval($this->dbConfig['port']==0)){
				$this->dbConfig['port'] = 3306; //setting default mysql port name
				echo "Database connection port not selected! traying default value: '".$this->dbConfig['port']."'";
			}
			if(empty($this->dbConfig['dbUserName'])){
				$this->dbConfig['dbUserName'] = 'ip_to_country'; //setting default mysql port name
				echo "Database connection host not selected! traying default value: '".$this->dbConfig['dbUserName']."'";
			}
			if(empty($this->dbConfig['dbUserPassword'])){
				$this->dbConfig['dbUserPassword'] = 'xxx'; //setting default mysql port name
				echo "Database connection host not selected! traying default value: '".$this->dbConfig['dbUserPassword']."'";
			}
			$this->db = mysql_connect($this->dbConfig['host'].':'.$this->dbConfig['port'], $this->dbConfig['dbUserName'], $this->dbConfig['dbUserPassword']);
			if (!$this->db) {
			    die('Database connection error: ' . mysql_error());
			}else{
				if(empty($this->dbConfig['dbName'])){
					$this->dbConfig['dbName'] = 'ip_to_country'; //setting default mysql port name
					echo "Database connection host not selected! traying default value: '".$this->dbConfig['dbName']."'";
				}
				if( !mysql_select_db( $this->dbConfig['dbName'] , $this->db ) ){
					die("Error during selecting database '".$this->dbConfig['dbName']."' : ". mysql_error());
				}else{
					return true;
				}
			}
		}else{
			die('Error! Database configuration not set! #2');
		}
	}
	
	/**
	 * executes given SQL querry and returns one row
	 * feel free to replece our function and use here Your favorite(s) database abstraction layer (ie. ADOdb)
	 *
	 * @param string $sql
	 * @return array
	 */
	private function dbGetRow(){
		$this->dbConnect();
		$sql = $this->getIpSelectSQL();
		$result = mysql_query($sql);
		if($result){
			$row = mysql_fetch_assoc($result);
			if($row){
				return $row;
			}
			else {
				$result = mysql_query(str_replace($this->ipValue, '2130706433', $sql));
				return mysql_fetch_assoc($result);
			}
		}
		die("Error during database querry:" . mysql_error());
	}
}