<?php
class FeedbackController extends Abstract_Controller_FrontendController {
	
	public function indexAction(){
		$this->_forward('detail');
	}
	
	public function detailAction(){
		$this->view->activeMenu = 'feedback';
		
		$feedback = new Init_Feedback();
		$this->view->data = $feedback->getData();
		$id = $this->_request->getParam('id');
		$this->view->selectedId = $id?$id:$this->view->data['0']['feedback_id'];
		
		$this->view->layouts['left']["left_menu"] = array('inc/menu/feedback', 100);
	}

}