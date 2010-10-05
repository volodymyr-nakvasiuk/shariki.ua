<?php
class Init_Currency {
	
	protected $_data = false;
	protected $_currencyDb = false;
	protected $_mainCurrencyId = 1;
	protected $_mainCurrencyRate = false;
	protected $_localCurrencyId = 3;
	protected $_localCurrencyRate = false;
	
	public function __construct(){
		$this->_currencyDb = Db_Currency::getInstance();
	}
	
	protected function _setData(){
		$tmp = $this->_currencyDb->fetchAll();
		$this->_data = array();
		if ($tmp){
			$tmp = $tmp->toArray();
			foreach($tmp as $curr){
				if ($curr['currency_id']==$this->_mainCurrencyId){
					$this->_mainCurrencyRate=$curr['currency_rate'];
					continue;
				}
				if ($curr['currency_id']==$this->_localCurrencyId){
					$this->_localCurrencyRate=$curr['currency_rate'];
					continue;
				}
			}
			foreach($tmp as $curr){
				if ($curr['currency_id']==$this->_localCurrencyId) continue;
				$rate = $this->_localCurrencyRate/$curr['currency_rate']/$this->_mainCurrencyRate;
				$this->_data[] = array(
					'name' =>$curr['currency_gen'],
					'rate' =>$rate,
				);
			}
		}
	}
	
	public function getData(){
		if (!$this->_data) $this->_setData();
		return $this->_data;
	}
}