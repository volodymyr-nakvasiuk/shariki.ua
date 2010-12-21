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
			$feedback = new Tools_Feedback('allura@inbox.ru');
			$this->view->faq_error = $feedback->send($_POST);
		}
		$this->view->page = $this->_request->getParam('p', 1);
		$this->view->pageUrl = HOST_NAME.'/feedback/?p='.$this->view->page;
		$feedback = new Init_Feedback($this->view->page);
		$this->view->data = $feedback->getData();
		$id = $this->_request->getParam('id');
		$this->view->selectedId = $id?$id:$this->view->data['data']['0']['feedback_id'];
		
		$this->view->layouts['left']["left_menu"] = array('inc/menu/feedback', 100);
		$this->view->layouts['bottom']["search_pages"] = array('inc/bottom/pages', 10);
		$this->view->layouts['bottom']["feedback_form"] = array('inc/bottom/feedback', 50);
	}

}