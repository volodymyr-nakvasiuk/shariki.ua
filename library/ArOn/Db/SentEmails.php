<?php
class ArOn_Db_SentEmails extends ArOn_Db_Table {

    protected $_name = 'site_sent_emails';
    
    public function countSameEmails($hash)
    {
		$select = $this->select();
		$select->reset();
		$select->from($this->_name,array('num' => 'count(*)'))->
			where('hash = ?', md5($hash));
		$result = $this->fetchAll($select)->toArray();
		return $result[0]['num'];								
										   		
    }
	
    
}