<?php
class TeamController extends Abstract_Controller_FrontendController {
	
	public function indexAction(){
		$this->view->activeMenu = 'team';
		
		$team = new Init_Team();
		$this->view->data = $team->getData();
	}

}