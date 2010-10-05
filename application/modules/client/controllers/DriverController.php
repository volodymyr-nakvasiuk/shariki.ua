<?php
class Client_DriverController extends Client_AjaxController  {
	
	public $resultJSON = array('success'=>false);
	
	public function __call($function, $params){
		$count = preg_match('/(.*?)Action/',$function,$matches);
		
		if ($count){
			$this->resultJSON['success'] = true;
			//$action = $this->_request->getParam('grid_action');
			$names = explode('Z',$matches[1]);
			$this->resultJSON['data'] = array();
			foreach($names as $name){
				$action = '_load'.ucfirst($name).'Data';
				$this->resultJSON['data'][$name] = method_exists($this, $action)?$this->$action():array();
			}
		}
	}
	
	protected function _loadLogedData(){
		$return = array();
		if ($this->auth){
			$return['is'] = true;
			$return['email'] = $this->userData['user']['email'];
			$return['name'] = $this->userData['user']['name'];
			$return['tel'] = $this->userData['user']['tel'];
		}
		else {
			$return['is'] = false;
		}
		
		return $return;
	}
	
	protected function _loadBookmarksData(){
		$return = array();
		$url = $this->_request->getParam('link');
		if ($url){
			$url = explode('/',$url);
			$l = count($url)-1;
			if (!$url[$l]) $l--;
			$return['in'] = (boolean)Tools_Price::inBookmarks($url[$l]);
		}
		return $return;
	}
	
	protected function _loadClientBookmarksData(){
		$return = array();
		$return = Tools_Price::getClientBookmarks();
		//return array(1, 2, 3);
		$return = Zend_Json::encode($return);
		return $return;
	}
	
	protected function _loadStatisticsData(){
		$statistics = new Init_Statistics();
		
		return array(
			'today'=>$statistics->getTodayOldCount(),
			'all'  =>$statistics->getAllOldCount(),
		);
	}

	protected function _loadNewsCounterData(){
		$params = $this->_request->getParams();	
		preg_match("/\/(\d*)\/$/", $params['link'], $arr);
		$model = new Db_View_News();
		$model->update(array('news_viewed' => new Zend_Db_Expr('`news_viewed`+1')),'`news_id`='.$arr[1]);
		return '{success:true}';
	}
}
