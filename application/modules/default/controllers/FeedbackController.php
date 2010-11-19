<?php
class FeedbackController extends Abstract_Controller_FrontendController {
	
	public function init(){
		parent::init();
		$this->view->activeMenu = 'feedback';
	}
	
	public function indexAction(){
		$this->_forward('detail');
	}
	
	public function detailAction(){
		if (isset($_POST) && $_POST){
			$feedback = new Tools_Feedback('djtheme@gmail.com');
			$this->view->faq_error = $feedback->send($_POST);
		}

		$feedback = new Init_Feedback();
		$this->view->data = $feedback->getData();
		$id = $this->_request->getParam('id');
		$this->view->selectedId = $id?$id:$this->view->data['0']['feedback_id'];
		
		$this->view->layouts['left']["left_menu"] = array('inc/menu/feedback', 100);
	}

}