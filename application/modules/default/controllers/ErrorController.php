<?php

class ErrorController extends Abstract_Controller_FrontendController {
	
	public function errorAction()
	{
		$errors = $this->_getParam('error_handler');
		if(!($errors instanceof ArrayObject))
			return;
		switch ($errors->type) {
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
			case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:
				// 404 error -- controller or action not found
				//$this->getResponse()->setHttpResponseCode(404);
				//$this->view->message = 'Page not found';
				$exception = $errors->exception;
				$log = new Zend_Log(
					new Zend_Log_Writer_Stream(
						LOG_ROOT.'/application404Exception.log'
					)
				);
				$log->debug($exception->getMessage() . "\n" .
				$exception->getTraceAsString());
				break;
			default:
				// application error
				$exception = $errors->exception;
				$log = new Zend_Log(
					new Zend_Log_Writer_Stream(
						LOG_ROOT.'/applicationException.log'
					)
				);
				$log->debug($exception->getMessage() . "\n" .
				$exception->getTraceAsString());
				break;
		}
		//$this->view->exception = $errors->exception;
		//$this->view->request   = $errors->request;
		
	}
	
}
