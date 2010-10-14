<?php
class Tools_Ip2Country{
	
	protected $_config;
	protected $_class;
	protected $_ip;
	protected $_country_model;
	
	protected $_country = null;
	
	public function __construct($ip){
		$this->_ip = $ip;
		$config = ArOn_Db_Table::getDefaultAdapter()->getConfig();
		
		$this->_config = array(
			'host' => $config['host'], //example host name
			'port' => $config['port']?$config['port']:3306, //3306 -default mysql port number
			'dbName' => $config['dbname'], //example db name
			'dbUserName' => $config['username'], //example user name
			'dbUserPassword' => $config['password'], //example user password
			'tableName' => 'ip_to_country' //example table name
		);
		$this->setup();
	}
	
	
	protected function setup(){
		
		$this->_class = new ArOn_Crud_Tools_Ip2Country($this->_ip,$this->_config);
		$this->_country_model= Db_Country::getInstance();
	}
	
	public function getName(){
		if(null === $this->_country)
			$this->_setCountry();
		return $this->_country;
	}
	
	public function getIsoCode(){
		return $this->_class->getInfo(IP_COUNTRY_ISO);
	}
	
	public function getId(){
		$code = $this->getIsoCode();
		$row = $this->_country_model->getRowByFieldValue('country_ctry',$code);
		return $row?$row['country_id']:false;
	}
	
	protected function _setCountry(){
		$this->_country = $this->_class->getInfo(IP_COUNTRY_NAME);
	}
}