<?php
class Cms_JsonController extends Abstract_Controller_CmsController {

	protected $parent_ids = array(
		'siteController' => 'controller_module_id',
		'siteActs' => 'action_controller_id',
	);
	
	public function init(){
		parent::init();
		$this->_helper->viewRenderer->setNoRender();
	}
	
	public function __call($function,$params){
		$count = preg_match('/(.*?)Action/',$function,$matches);
		if ($count) {
			$parent_id = $this->_request->getParam('parent_id');
			if(empty($parent_id )){
				echo "{succes: true,rows: []}";
				return true;
			}
			$classname = 'Db_'.ucfirst($matches[1]);
			if(strpos($classname,'Acl_') !== false){
				$name = ucfirst($matches[1]);
				$t = $matches[1] [3];
				
				$name = str_replace('Acl_'.$t,'Acl_'.ucfirst($t),$name);
				$classname = 'Crud_Grid_ExtJs_'.ucfirst($name);
				$grid = new $classname (null,array('parent' => $parent_id));
				$grid->setLimit(1000);
				$data = $grid->getData();
				$model = $grid->getModel();
				echo $this->generateJson($data['data'],$model);
				return true;
			}
			if(!class_exists($classname)) return false;
			$model = new $classname();
			$select = $model->select();
			$parent_key = (array_key_exists($matches[1], $this->parent_ids)) ? $this->parent_ids [$matches[1]] : 'parent_id';
			$select->where($parent_key . ' = ?',$parent_id);
			$order_exp = $model->getOrderExpr();
			$order_asc = $model->getOrderAsc();
			foreach ($order_exp as $i => &$key){
				if ($order_asc[$i]) $direction = 'ASC';
				else $direction = 'DESC';
				$key = $model->getTableName().'.'.$key.' '.$direction;
			}
			$select->reset(ArOn_Db_TableSelect::ORDER)->order($order_exp);
			//echo $select->__toString();exit;
			$data = $model->fetchAll($select);
			echo $this->generateJson($data,$model);
			return true;
		}
	}
	
	public function carAction(){
		
			$parent_id = $this->_request->getParam('client_id');
			if(empty($parent_id )){
				echo "{succes: true,rows: []}";
				return true;
			}
			$model = Db_Car::getInstance();
			
			/**
			 * @var ArOn_Db_TableSelect
			 */
			$select = $model->select();
			$select->where('price_client_id =?',$parent_id);
			$select->columnsJoinOne('Db_Mark','mark_name');
			$select->columnsJoinOne('Db_Model','model_name');
			$select->from(null,array('price_name' => "CONCAT_WS(' ',mark_name,model_name)", 'price_id'));
			$select->order($model->getOrderExpr());
			$data = $model->fetchAll($select);
			echo $this->generateJson($data,$model);
			return true;
	}
	
	public function catalogAction(){
		$parent_id = $this->_request->getParam('parent_id');
		if(empty($parent_id )){
			echo "{succes: true,rows: []}";
			return true;
		}
		
		$generation = Db_Generation::getInstance();
		$generations = $generation->fetchAll("parent_id = ".$parent_id)->toArray();
		$ids = array();
		$gens = array();
		foreach ($generations as $generation){
			$ids[] = $generation['generation_id'];
			$gens[$generation['generation_id']] = $generation;
		}
		
		$model = new Db_Catalog();
		$select = $model->select();
		$select->where('parent_id IN ('.implode(',',$ids).')');
		$select->group('parent_id');
		//$select->order(array('article_year_beg', 'article_name'));
		$data = $model->fetchAll($select);
		
		foreach ($data as $key=>$row) $data[$key]['article_name'] = $gens[$row['parent_id']]['generation_name'].' '.$row['article_name'].' <span style="color:green;">'.$row['article_year_beg'].'..'.$row['article_year_end'].'</span>';
		
		echo $this->generateJson($data,$model);
		return true;
	}
	
	protected function generateJson($data,$model){
			$json = "{success: true,rows: [";
			$options = array();
			$key = $model->getPrimary();
			$name = $model->getNameExpr();
			foreach ($data as $row){
				$option = "{optionValue:'" . $row[$key] . "', displayText:'" . addslashes($row[$name]) . "'}";
				$options[] = $option;
			}
			$json .= implode(', ',$options) ."]}";
			return $json;
	}

}