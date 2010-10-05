<?php
class ArOn_Db_Emails  extends ArOn_Db_Table{

    protected $_name = 'site_emails';
	
    public static function getTemplate($name)
    {
    	$email = self::getInstance();
    	$select = $email->
    		select()->    		
    		where('name = ?',$name)->    		
    		limit(1);
    	$result = $email->fetchRow($select);	
		return ($result !== null) ? $result->toArray() : false;		
    }	

}