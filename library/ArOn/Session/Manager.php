<?php
class ArOn_Session_Manager extends Zend_Session_SaveHandler_DbTable {

	public function __construct() {

		$config = array ('name' => 'session', //table name as per Zend_Db_Table
						'primary' => array ('session_id', //the sessionID given by PHP
											'save_path', //session.save_path
											'name' 
											)//session name
											, 
						'primaryAssignment' => array (	//you must tell the save handler which columns you
														//are using as the primary key. ORDER IS IMPORTANT
														'sessionId', //first column of the primary key is of the sessionID
														'sessionSavePath', //second column of the primary key is the save path
														'sessionName' )//third column of the primary key is the session name
														,
						'modifiedColumn' => 'modified', //time the session should expire
						'dataColumn' => 'session_data', //serialized data
						'lifetimeColumn' => 'lifetime' )//end of life for a specific record
		;
		parent::__construct ( $config );
	}

}
?>