<?php
class ArOn_Crud_Tools_EmailProcess {

	public static function send($name, $data)
	{

		$emails = ArOn_Db_Emails::getInstance();
		$template = $emails->getTemplate($name);
		
		if (!$template) return;
		if (!is_array($template)) $template = $template->toArray();
		
		$body = $template['body'];
		$to = $template['to'];
		$subject = $template['subject'];
		$from = $template['from'];
				
		$mask = array();
		
		foreach (array_keys($data) as $param) {
			$mask[] = '<#'.strtoupper($param).'#>';							
		}
		
		$body = str_replace($mask,array_values($data),$body);
		$to = str_replace($mask,array_values($data),$to);
		$from = str_replace($mask,array_values($data),$from);
		$subject = str_replace($mask,array_values($data),$subject);		

		$sent_email = new ArOn_Db_SentEmails;
		
		$hash = md5(serialize($data));
		if ($sent_email->countSameEmails($hash)) return;
		$mail = array(
			'body' => $body,
			'to' => $to,
			'hash' => $hash,
			'subject' => $subject,
			'attach' => $template['attach']? $template['attach'] : '',
			'from' => $from,
			'delay' => $template['delay'] ? $template['delay'] : 0,
			'repeat' => $template['repeat'],
		    'content_type' => $template['content_type'],
			'status' => 'delayed',
//			'created_at' =>	time()
		);
				
		$sent_email->insert($mail);
					
	}		
	
	public static function run()
	{		
		$sentEmailModel = new ArOn_Db_SentEmails;
		$select = $sentEmailModel->select()->
			where('status = ?','delayed')->
			where('created_at + delay < NOW()')->
			forUpdate();
	
		$emails = $sentEmailModel->fetchAll($select)->toArray();

		if (!$emails) return;
		$sent_ids = array();
		
		foreach ($emails as $email) {
			$mail = new ArOn_Zend_Mail( 'UTF-8' );
			$mail->setSubject( $email['subject'] );
				
			$recepients = self::getSplitedEmail( $email['to'] );
			foreach ($recepients as $emailStr=>$name) {				
				$mail->addTo($emailStr, $name );
			}
			
			if (!$recepients) {
				$recepients = self::getDirectEmail( $email['to'] );
				foreach ($recepients as $emailStr) {
					$mail->addTo($emailStr);	
				}
			}
			
			$fromArray = self::getSplitedEmail( $email['from'] );
			foreach ($fromArray as $fromEmail => $fromName) {
				$mail->setFrom( $fromEmail, $fromName );
			}
			
			if (!$fromArray) {
				$fromArray = self::getDirectEmail( $email['from'] );
				foreach ($fromArray as $fromEmail) {
					$mail->setFrom($fromEmail);	
				}
			}	
				
			$email['content_type']=='text/plain' ? 
				$mail->setBodyText($email['body'],null,Zend_Mime::TYPE_TEXT):
				$mail->setBodyHtml($email['body'],null,Zend_Mime::TYPE_HTML);

			if (!empty($email['attach']) && file_exists(UPLOAD_ATTACH_FILE_TO_EMAIL_PATH.$email['attach'])) {
				
				$content = file_get_contents(UPLOAD_ATTACH_FILE_TO_EMAIL_PATH.$email['attach']);
				$at = $mail->createAttachment($content);
				$at->filename = $email['attach'];
				
			}
			
			$sentEmailModel->update(array('status' => 'fail'),$sentEmailModel->q("id =? ",$email['id']));
			try {
				$mail->send();
				$sent_ids[] = $email['id'];
			} catch (Exception $e) {
				
			}
															
		}
		
		if (count($sent_ids)) {
			$id = $sentEmailModel->getPrimary();
			$sentEmailModel->update(array('status' => 'sent'),$id." IN (" . implode(",",$sent_ids) . ")");
		}		
		
		
	}
	
	public static function sendEmail($params){

   		$mail = new ArOn_Zend_Mail ( 'UTF-8' );
   		/*foreach($params as $key => $value){
   			$params [$key] = ArOn_Crud_Tools_String::convToUtf8($value);
   		}*/
   		//$mail->setEncodingOfHeaders(Zend_Mime::ENCODING_BASE64);  		
	    if (!empty($params['bodyText'])) $mail->setBodyText($params['bodyText'],"UTF-8",Zend_Mime::ENCODING_BASE64);
	    if (!empty($params['bodyHtml'])) $mail->setBodyHtml($params['bodyHtml'],"UTF-8",Zend_Mime::ENCODING_BASE64);
	    $mail->setFrom($params['fromEmail'], $params['fromName']);
	    $mail->addTo($params['toEmail'], $params['toName']);
	    if (!empty($params['bccEmail']) && !empty($params['bccName'])) $mail->addBcc($params['bccEmail'], $params['bccName']);
	    $mail->setSubject($params['subject']);
	    if (!empty($params['attachFile']) && file_exists($params['attachFile'])){
	        $at = $mail->addAttachment(file_get_contents($params['attachFile']));
	        $at->type        = $params['attachType'];
	        $at->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
	        $at->filename    = $params['attachName'];
	    }
    	$mail->send();
	}
	
	private function getSplitedEmail( $str ){
		$return = array ();
		$diffEmails = explode ( ',', $str );
		foreach ( $diffEmails as &$email ) {
			if(preg_match("/</",$email)){
				if (preg_match ( "/(.*)(<+)(.*)(>+)/", $email, $m )){
					$return [$m [3]] = trim ( $m [1] );
				}
			} else {
				$return [$email] = "";
			}
		}
		return $return;
	}
	
	private function getDirectEmail( $str )
	{
		$return  = array();
		$diffEmails = explode( ',', $str );
		foreach ($diffEmails as &$email) {
			if (preg_match( "/.*@*/", $email, $m))
				$return[] = trim($email);
		}
		return $return;		
	}
}

?>