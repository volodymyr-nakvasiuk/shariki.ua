<?php
class Tools_Feedback {

	protected $_email = false;

	public function __construct($email){
		$this->_email = $email;
	}
	
	public function send($params){
		$error = $this->_checkParams($params);
		if(!$error){
			$this->_sendByEmail($params);
		}
		return $error;
	}

	protected function _checkParams($data) {
		$error = '';
		if (!$data['name']) $error .= "<br/>Пожалуйста представтесь!";
		if (!$data['contact']) $error .= "<br/>Нам нужны Ваши контакты для ответа!";
		if (!$data['text']) $error .= "<br/>Невозможно отправить пустой отзыв!";

		return $error;
	}

	protected function _sendByEmail($data) {
		$title = "Пришел новый отзыв с сайта SHARIKI.UA";

		$message= '
		<html>
			<head>
				<title>'.$title.'</title>
			</head>
			<body>
				<div style="margin-top: 15px; border-bottom: dashed 1px #999999; padding-bottom: 10px;">
		<table width="650px" border="1" cellpadding="5" cellspacing="0">
			<tr>
				<td style="padding:5px !important;"><center><b>ФИО:</b></center>'.$data['name'].'</td>
			</tr>
			<tr>
				<td style="padding:5px !important;"><center><b>Контакты:</b></center>'.$data['contact'].'</td>
			</tr>
			<tr>
				<td style="padding:5px !important;"><center><b>Текст отзыва:</b></center>'.$data['text'].'</td>
			</tr>
		</table>
	</div>
			</body>
		</html>';
		
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= 'To: Manager <'.$this->_email.'>' . "\r\n";
		$headers .= 'From: Info Messager <info@shariki.ua>' . "\r\n";


		mail($this->_email, $title, $message, $headers);
	}
}