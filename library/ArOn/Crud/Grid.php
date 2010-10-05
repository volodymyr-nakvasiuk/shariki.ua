<?php
class ArOn_Crud_Grid extends ArOn_Crud_Cache{
	
	/*
	 * делать запрос количества записей в базе
	 */
	protected $_ifCount = true;
	
	protected $_data;

	/**
	 * @var ArOn_Db_Table
	 */
	public $table;

	public $gridTitle;

	public $fields;

	public $sort = 'id';
	protected $_sortParameter = 'sort';
	
	public $direction = 'DESC';
	protected $_directionParameter = 'sort_direction';
	
	public $link;

	public $formFilter;

	/**
	 * @var ArOn_Crud_Grid_Filter
	 */
	protected $filters;

	public $where;

	protected $rowIdName = 'id';

	public $options = array ('id' => 'grid_table', 'cellspacing' => '0', 'cellpadding' => '0' );

	protected $fieldNames = array ();

	protected $fieldData = array ();

	protected $grid_id;

	/**
	 * @var ArOn_Db_TableSelect
	 */
	protected $currentSelect;

	protected $renderTitle = true;

	protected $renderPager = true;

	protected $renderAction = true;

	protected $actions = array ('delete', 'update' );

	public $actionAjax = false;
	public $editController = false;
	public $editAction = false;
	public $ajaxClassName = 'grid-sort';

	public $gridActionName;

	public $ajaxActionName;

	public static $ajaxModuleName;

	protected $filterParams = array ();

	public $trash = false;

	public $default;

	public $formClass;

	protected $category;
	protected $category_filter;
	protected $filterPrefix;

	public $headSeparator = true;
	public $footSeparator = true;
	public $tooltip = false;
	protected $activeRow;
	protected $active_mode = true;
	protected $_params;
	protected $_width = 0;
	protected $_assoc_width = array();

	function __construct($id = null, $params = array(), $options = null, $mode = true) {
		if($id === 'cache')
			return;
		$this->filters = new ArOn_Crud_Grid_Filter ( );
		$this->filters->active_mode = $mode;
		$this->grid_id = $id;
		$this->_params = $params;
		$this->active_mode = $mode;
		if (null != $options) {
			$this->loadOptions ( $options );
		}

		$this->init ();
		$this->setup ();
	}

	function init() {

	}

	function setup() {
		if ($this->gridActionName) {
			if ($this->link === null) {
				$this->link = '/' . self::$ajaxModuleName . '/' . $this->gridActionName . '/';
			}
			if ($this->ajaxActionName === null) {
				$this->ajaxActionName = $this->gridActionName;
			}
		}

		if (! $this->formClass) {
			$this->formClass = str_replace ( "_Partner_", "_", str_replace ( "Crud_Grid_", "Crud_Form_", get_class ( $this ) ) );
		}

		if (empty ( $this->default ['limit'] )) {
			$this->default ['limit'] = '20';
		}

		if (empty ( $this->default ['p'] )) {
			$this->default ['p'] = '1';
		}

		if (! is_array ( $this->fields )) {
			return false;
		}

		$this->setTable( $this->table );
		$this->currentSelect = $this->table->select ()->columnsId ();
		$this->rowIdName = $this->table->getPrimary();
		$this->filterPrefix = $this->filters->getPrefix();
		$this->extendFields ();

		$formColumn = false;
		foreach ( $this->fields as $column => $value ) {
			if (! method_exists ( $value, 'updateColumn' ))
			continue;
			$value->setFilterPrefix($this->filterPrefix);
			$value->init ( $this, $column );
			$value->updateColumn ();
			if ($value instanceof ArOn_Crud_Grid_Column_FormColumn) {
				$this->updateFormColumn($column);
				$formColumn = true;
			}
			$this->currentSelect = $value->updateCurrentSelect ( $this->currentSelect );
			$name = $value->getName ();
			$field = $value->getField ();
			$this->fieldNames [$name] = $field;
			$this->fieldData [$name] = $value;
		}

		if (! $formColumn) {
			$pos = array_search ( "update", $this->actions );
			if ($pos !== false) {
				unset ( $this->actions [$pos] );
			}
		}
		if (isset ( $_params ['is_deleted'] ) and $_params ['is_deleted'] == '1') {
			$pos = array_search ( "delete", $this->actions );
			if ($pos !== false) {
				unset ( $this->actions [$pos] );
			}
			$this->actions [] = 'undelete';
		}
		if (isset ( $_params ['id'] )) {
			$this->activeRow = $_params ['id'];
		}

		$this->setupFilters ();
		$this->setSort ();
		$this->setDirection ();
	}
	
	protected function updateFormColumn($column){
		//edit $this->fields[$column]
	}
	
	public function setTable($table){
		if (is_string ( $table )){
			$table = ArOn_Crud_Tools_Registry::singleton ( $table );
		}
		$this->table = $table;
	}
	
	protected function setupFilters(){
		if ( empty ( $this->filters )) return false;
		$this->filters->setSortName($this->_sortParameter);
		$this->filters->setDirectionName($this->_directionParameter);
		$this->filters->table = $this->table->getTableName ();
		$this->filters->action = '/' . self::$ajaxModuleName . '/' . $this->gridActionName . '/';
		if ($this->trash) $this->filters->trash = true;
		$this->filters->default = $this->default;
		$this->filters->setParams($this->_params);
		$this->filters->setup();
		$this->filters->createForm ();

	}

	public function renderCSV() {

		$data = $this->getDataWithRenderValues ();
		if (empty ( $data ['data'] ))
		$data ['data'] = array (0 => '' );
		$csv = '';
		foreach ( $this->fieldNames as $name => $field ) {
			$filedTitle = $this->fieldData [$name]->getTitle ();
			$filedName = $this->fieldData [$name]->getName ();
			$csv .= '"' . $filedTitle . '",';
		}
		$csv .= "\n";
		foreach ( $data ['data'] as $row ) {
			foreach ( $this->fieldNames as $name => $field ) {
				if (isset ( $row [$field] )) {
					$csv .= '"' . $row [$field] . '",';
				}
					
			}
			$csv .= "\n";
		}

		return $csv;
	}
	
	public function renderRSS(	$feedArray = array( 'title','link','description','image','language','atom','text', 'pubDate', 'published'), 
								$feedFields = array('title','link','description','image','pubDate','enclosure','text'),
								$limit = 10,
								$format = 'rss'		) {
		$this->setLimit($limit);
		$data = $this->getData ();
		if (empty ( $data ['data'] ))
		$data ['data'] = array (0 => '' );
		$csv = '';
		foreach ( $this->fieldNames as $name => $field ) {
			$filedTitle = $this->fieldData [$name]->getTitle ();
			$filedName = $this->fieldData [$name]->getName ();
			$csv .= '"' . $filedTitle . '",';
		}
		
	 	// Название ленты
        $title = (empty($feedArray['title'])) ? $this->gridTitle : $feedArray['title'];

        // Ссылка на ленту
        $urlPrefix = $feedArray ['link'];
        $link = $urlPrefix;
        
        // Описание
        $description = $feedArray ['description'];        
        
        // Содержание
        //$text = $feedArray ['text'];  
        
        // Логотип
        $image = $feedArray ['image'];
        //if (!is_array($image)) $image = array('url'=>$image);
        
        // Кодировка
        $charset = 'UTF-8';

        //Язык
        $language = $feedArray ['language'];
        
        //Атомы
        $atom = $feedArray ['atom'];
        if (!is_array($atom)) $atom = array($atom);
        
        // Массив для ленты
        $feedRss = array(
            'title'       => $title,
            'link'        => $link,
            'charset'     => $charset,
        	'language'    => $language,
        	'atom'        => $atom,
            'entries'     => array(),
        );
        
		if(!empty($description)){
            $feedRss['description'] = $description;
        }
        
        if(!empty($image)){
            $feedRss['image'] = $image;
        }
        
        if (isset($feedArray ['pubDate'])){
        	$feedRss['lastUpdate'] = $feedArray ['pubDate'];
        }
        
		if (isset($feedArray ['published'])){
        	$feedRss['published'] = $feedArray ['published'];
        }
        
        $link_f = false; $pudDate_f = false;$pudDate_c='';$guid_f = false; $enclosure_f = false; $image_f = false;
        if(!empty($feedFields['link'])){
        	$link_f = true;        
	        if(strpos($feedFields['link'],'http') === false){
		        if($feedFields['link'][0] == '/') $feedFields['link'] = substr($feedFields['link'],1);
		        if($urlPrefix[-1] == '/') $urlPrefix = substr($urlPrefix,0,-1);
		        $feedFields['link'] = $urlPrefix . '/' . $feedFields['link'];		       
	        }
        }
		if(!empty($feedFields['guid'])){
        	$guid_f = true;        
	        if(strpos($feedFields['guid'],'http') === false){
		        if($feedFields['guid'][0] == '/') $feedFields['guid'] = substr($feedFields['guid'],1);
		        if($urlPrefix[-1] == '/') $urlPrefix = substr($urlPrefix,0,-1);
		        $feedFields['guid'] = $urlPrefix . '/' . $feedFields['guid'];		       
	        }
        }
        if(!empty($feedFields['pubDate'])) $pudDate_f = true;
        if(!empty($feedFields['pubDateCorrection'])) $pudDate_c = $feedFields['pubDateCorrection'];
        if(!empty($feedFields['enclosure'])) {
        	$enclosure_f = true;
        }
		if(!empty($feedFields['image'])){
        	$image_f = true;        
	        if(strpos($feedFields['image'],'http') === false){
		        if($feedFields['image'][0] == '/') $feedFields['image'] = substr($feedFields['image'],1);
		        if($urlPrefix[-1] == '/') $urlPrefix = substr($urlPrefix,0,-1);
		        $feedFields['image'] = $urlPrefix . '/' . $feedFields['image'];		       
	        }
        }
        
        // Добавляем статьи в массив        
        foreach ($data['data'] as $item) {
        	foreach ($item as &$value){
        		$value = str_replace('&nbsp;', ' ', $value);
		        preg_match_all("/\{\{([^}]*)\}\}/", $value, $matches);
		 		foreach ($matches[0] as $key=>$match){
		 			$value = str_replace($match, '', $value);
		 		}
        	}
        	
            $feed = array();
            $feed ['title'] = (empty($feedFields['title'])) ? $item[ArOn_Db_Table::NAME] : $item[$feedFields['title']];
            $feed ['title'] = trim($feed ['title'], " \t\r\n.\x0B\0");
            if($link_f === true){
            	$feed ['link'] = $this->_replaceInStringFields($feedFields['link'],$item);
            }
            if($guid_f === true){
            	$feed ['guid'] = array('value'=>$this->_replaceInStringFields($feedFields['guid'],$item), 'isPermaLink'=>true);
            }
            $image = empty($feedFields['image'])?'':
            	'<center><img alt="'.$feed ['title'].'" src="'.$this->_replaceInStringFields($feedFields['image'], $item).'"/></center><br/>';
            $feed ['description'] = $image.$item [$feedFields['description']];
            if(array_key_exists($feedFields['text'],$item) && !empty($item [$feedFields['text']])){
            	$feed ['text'] = $image.$item [$feedFields['text']];
            }
            if($pudDate_f === true)
                $feed ['lastUpdate']  = strtotime($item [$feedFields['pubDate']].' '.$pudDate_c);
                
          	if($enclosure_f === true){          		
          		$enclosure = $feedFields['enclosure'];
          		if(array_key_exists('path',$enclosure)) {
          			$path = $this->_replaceInStringFields($enclosure['path'], $item);
          			unset($enclosure['path']);
          			
          			$length = (file_exists($path)) ? filesize($path) : 0;
          			$enclosure['length'] = $length;          			
          		}
          		$enclosure['url'] = $this->_replaceInStringFields($enclosure['url'], $item);
                $feed ['enclosure']  = array($enclosure);
          	}    
                
            $feedRss['entries'][] = $feed;
        }
          // Импортируем массив в ленту
        $feed = ArOn_Zend_Feed::importArray($feedRss, $format);
        // Отправляем нужные заголовки браузеры и получаем нашу ленту
        $feed->send();
        
	}
	
	public function renderXML($xmlFields = false, $withFieldTitle = false, $version = '1.0', $encoding = 'utf-8') {
		$data = $this->getDataWithRenderValues();
		if(!$xmlFields){
			$xmlFields = array();
			foreach ( $this->fieldNames as $fieldName => $fieldTitle ) {
				$xmlFields [$fieldName] = ($withFieldTitle) ? $fieldTitle : $fieldName;
			}
		}
		$mixed  = $this->_getXmlMixedData($data ['data'],$xmlFields);

		$xml = new ArOn_Crud_Tools_Xml('1.0', 'utf-8');
        $xml->fromMixed($mixed);
        return $xml->saveXML();
	}
	
	public function _getXmlMixedData($data,$xmlFields){
		$mixed = array();
		if (empty ( $data ))
			$mixed = array ( 0 => '' );
		foreach ( $data as $row ) {
			foreach ( $xmlFields as $fieldName => $fieldXML ) {
				if (isset ( $row [$fieldName] )) {
					$mixed[$fieldXML] =  htmlspecialchars( $row [ $fieldName ] );
				}
			}
		}
		return $mixed;
	}
	
	protected function _replaceInStringFields($string,$item){
		$start = 0;
		while(($start = strpos($string,'{',$start)) !== false){
			$start = strpos($string,'{');
	        $end = strpos($string,'}');
	        $name = substr($string,($start+1),$end-$start-1);
	        $string = str_replace('{'.$name.'}',$item[$name],$string);
	        $start++;
		}
		return $string;
	}
	
	function extendFields() {
	}

	public function render() {

		$html = '';

		$this->preRender ();

		$html .= '<div class="title">';
		$html .= $this->renderLinks ();
		$html .= '</div>';

		$html .= $this->renderFilters ();
		//					if(empty($data['data'])) $data['data'] = array(0 => '');


		$html .= $this->renderGridTable ();
		//Zend_Paginator


		return $html;
	}

	public function preRender() {
		$params = $this->filters->getDefaultValues();
		$this->setFilterParams($this->_params);
	}

	public function getFilterParams(){
		if(empty($this->filterParams)) {
			$params = $this->filters->getDefaultValues();
			$this->setFilterParams($params);
		}
		return $this->filterParams;
	}



	public function renderLinks() {

		$html = '';
		$htmls = array ();

		$ajax_attr = '';
		if ($this->actionAjax and $this->grid_id) {
			$ajax_attr = ' class="' . $this->ajaxClassName . '" action="' . $this->ajaxActionName . '-add" id="' . $this->grid_id . '" ';
		}

		$form = '';
		if (! empty ( $this->filterParams ))
		$form = "?" . implode ( "&", $this->filterParams );
			
		/*$html .= '<span style="margin:2px">
		 <a href="'.$this->link.'add'.$form.'"'.$ajax_attr.'" module="'.self::$ajaxModuleName.'">Create New</a>
		 </span>';*/
		$html .= '<span style="margin:2px">
								<input type="button" 
								onclick="window.location.href=\'' . $this->link . 'add' . $form . '\'" ' . $ajax_attr . '"
								module="' . self::$ajaxModuleName . '" value="Create New">
							</span>';

		if (! empty ( $this->title_links )) {
			$html .= '<span style="margin:2px">' . $this->title_links . '</span>';
		}
		if ($this->active_mode && $this->trash && (@$_params ['is_deleted'] != '1') && ! isset ( $_params ['ajax'] )) {
				
			/*$html .= '<span style="margin:5px"><a href="'.$this->link.$form.((empty($form))?'?is_deleted=1':'&is_deleted=1').'"'.$ajax_attr.'>
			 '.'Show Recycle Bin'.
			 //<img border="0" src="/res/img/icons/trash.gif" width="20" height="20" alt="deleted">
			 '</a></span>';	*/
			$html .= '<span style="margin:5px">
						<input type="button" 
						onclick="window.location.href=\'' . $this->link . $form . ((empty ( $form )) ? '?is_deleted=1' : '&is_deleted=1') . '\'"' . $ajax_attr . '					
						' . ' value="Show Recycle Bin">' . //<img border="0" src="/res/img/icons/trash.gif" width="20" height="20" alt="deleted">
			'</span>';
		}

		if ($this->active_mode && $this->trash && (@$_params ['is_deleted'] == '1') && ! isset ( $_params ['ajax'] )) {
				
			/*$html .= '<span style="margin:5px"><a href="'.$this->link.$form.'"'.$ajax_attr.'>
			 '.'Go back'.
			 //<img border="0" src="/res/img/icons/trash.gif" width="20" height="20" alt="deleted">
			 '</a></span>';*/
			$html .= '<span style="margin:5px">
						<input type="button" 
						onclick="window.location.href=\'' . $this->link . $form . '\'" ' . $ajax_attr . ' value="Go back">' . '</span>';
		}

		return $html;
	}

	public function renderGridTable() {
		$html = '<table ';
		if (! empty ( $this->options ) && is_array ( $this->options )) {
			foreach ( $this->options as $name => $value ) {
				$html .= $name . '="' . $value . '" ';
			}
		}
		$html .= '>';
		$pagination = $this->renderFoot ();

		$html .= '<thead>';
		if ($this->headSeparator) {
			$html .= '<tr><td colspan=99><table cellspacing="0" cellpadding="0" border="0" width="100%"><tbody><tr><td nowrap="nowrap" height="0" bgcolor="#999999"><spacer height="1" type="block"/></td></tr></tbody></table>
        					  <table cellspacing="0" cellpadding="0" border="0" width="100%"><tbody><tr><td nowrap="nowrap" height="1" bgcolor="#dddddd"><spacer height="1" type="block"/></td></tr></tbody></table>
        					  </td></tr>';
		}
		$html .= $pagination;
		if ($this->headSeparator) {
			$html .= '<tr><td colspan=99><table cellspacing="0" cellpadding="0" border="0" width="100%"><tbody><tr><td nowrap="nowrap" height="1" bgcolor="#999999"><spacer height="1" type="block"/></td></tr></tbody></table>
        					  <table cellspacing="0" cellpadding="0" border="0" width="100%"><tbody><tr><td nowrap="nowrap" height="1" bgcolor="#dddddd"><spacer height="1" type="block"/></td></tr></tbody></table>
        					  </td></tr>';
		}
		$html .= $this->renderHead ();
		$html .= '</thead>';
		//					if($this->headSeparator) {
		//						$html .= '<tr><th colspan="99"><hr></th></tr>';
		//					}
		$html .= '<tbody class="records">';
		if ($this->headSeparator) {
			$html .= '<tr><td colspan=99><table cellspacing="0" cellpadding="0" border="0" width="100%"><tbody><tr><td nowrap="nowrap" height="1" bgcolor="#999999"><spacer height="1" type="block"/></td></tr></tbody></table>
        					  <table cellspacing="0" cellpadding="0" border="0" width="100%"><tbody><tr><td nowrap="nowrap" height="1" bgcolor="#dddddd"><spacer height="1" type="block"/></td></tr></tbody></table>
        					  </td></tr>';
		}
		$html .= $this->renderBody ();
		$html .= '</tbody>';
		$html .= '<tfoot>';
		if ($this->headSeparator) {
			$html .= '<tr><td colspan=99><table cellspacing="0" cellpadding="0" border="0" width="100%"><tbody><tr><td nowrap="nowrap" height="0" bgcolor="#999999"><spacer height="1" type="block"/></td></tr></tbody></table>
        					  <table cellspacing="0" cellpadding="0" border="0" width="100%"><tbody><tr><td nowrap="nowrap" height="1" bgcolor="#dddddd"><spacer height="1" type="block"/></td></tr></tbody></table>
        					  </td></tr>';
		}
		$html .= $pagination . '</tfoot>';

		$html .= '</table>';

		return $html;
	}

	public function renderFilters() {
		//$this->filters->createForm();
		return $this->filters->fields ? $this->filters->render () : '';
	}

	public function getFilters() {
		return $this->filters;
	}

	public function renderHead() {
		$html = '';

		if ($this->renderTitle) {
			$html .= '<tr>';
			$html .= $this->renderTitle ();
			$html .= '</tr>';
		}
		return $html;
	}

	public function renderBody() {
		$html = '';
		$active_row = false;
		$data = $this->getData ();
		$field_count = count ( $this->fieldNames );

		$i = 1;
		$last_category = null;
		$record_odd = "<tr class=\"record odd\">";
		$record_even = "<tr class=\"record even\">";
		foreach ( $data ['data'] as $row ) {
			$row_html = '';
			$category = $this->category ? $row ["_category_id"] : null;
			$row_id = @$row [$this->rowIdName];
			if ($last_category !== $category) {
				$last_category = $category;
				$catlink = ($this->category_filter) ? '<a href="?'. (($this->filterPrefix)?$this->filterPrefix.'['.$this->category_filter.']':$this->category_filter) . '=' . $row ["_category_id"] . '">' . $row ["_category"] . "</a>" : $row ["_category"];
				$row_html .= '<tr class="category"><td colspan="' . $field_count . '">' . $catlink . '</td></tr>';
				$record_odd = "<tr class=\"record odd cat_$category\">";
				$record_even = "<tr class=\"record even cat_$category\">";
				if (! $row_id) {
					$html .= $row_html;
					continue;
				}
				$i = 1;
			}
			if (! empty ( $row ['_class'] )) {
				$row_html .= "<tr class=\"{$row['_class']}\">";
			} else {
				$row_html .= ($i % 2) ? $record_odd : $record_even;
			}
				
			foreach ( $this->fields as $name => $field ) {
				$field->row_id = $row_id;
				if ($field instanceof ArOn_Crud_Grid_Column) {
					$row_html .= (! empty ( $field->rowClass )) ? '<td class="' . $field->rowClass . '">' : '<td>';
					$row_html .= $field->render ( $row ) . "</td>";
				} else {
					print "<script> alert('Bad field with name: \'$name\'.');</script>";
				}
					
			}
				
			//						$html .= $this->renderRow($row);
			$row_html .= '</tr>';
			if ($row_id == $this->activeRow) {
				$active_row = str_replace ( '<tr class="record', '<tr class="active', $row_html );
				$html .= $active_row;
			} else {
				$html .= $row_html;
			}
			$i ++;
		}
		if ($active_row) {
			//						$active_row = str_replace('<tr class="record','<tr class="active',$active_row);
			//						$html = $active_row.$html;
		} elseif ($this->activeRow) {
			$row_html = '';
			$select = clone $this->currentSelect;
			$select->reset ( 'where' );
			$select->reset ( 'order' );
			$select->limit ( 0 );
			$select->filterId ( $this->activeRow );
				
			$row = $this->table->fetchRow ( $select );
			if ($row !== null) {
				$row = $row->toArray ();
				$row_id = @$row [$this->rowIdName];

				$row_html .= '<tr class="record active">';
				foreach ( $this->fields as $field ) {
					$field->row_id = $row_id;
					$row_html .= "<td>" . $field->render ( $row ) . "</td>";
				}
				$row_html .= '</tr>';
				$html = $row_html . $html;
			}
		}

		if ($this->headSeparator) {
			//$html = '<tr><th colspan="'.$field_count.'"><hr></th></tr>'.$html.'<tr><th colspan="'.$field_count.'"><hr></th></tr>';
		}
		return $html;
	}

	public function renderFoot() {

		if (! $this->renderAction && ! $this->renderPager)
		return '';

		$data = $this->getData ();

		$html = '<tr><td colspan="99">';
		if ($this->renderAction && ! empty ( $data ['data'] [0] )) {
			$html .= '<p class="p-actions">';
			$html .= $this->renderAction ();
			$html .= '</p>';
		}

		if ($this->renderPager && ! empty ( $data ['data'] [0] )) {
			$html .= '<p class="p-listing">Results per page : ';
			$html .= $this->renderPaginator ();
			$html .= '</p>';
		}

		$html .= '</td></tr>';
		return $html;
	}

	protected function renderTitle() {
		$html = '';
		$ajax_attr = '';
		if ($this->actionAjax) {
			$ajax_attr = ' class="' . $this->ajaxClassName . '" action="' . $this->ajaxActionName . '" ' . '" module="' . self::$ajaxModuleName . '" ';
		}

		foreach ( $this->fieldNames as $name => $field ) {
			$fieldTitle = $this->fieldData [$name]->getTitle ();
			$fieldName = $this->fieldData [$name]->getName ();
			$html .= '<th ';
			$html .= ($this->sort == $name) ? ' class="filter sorted ' . (($this->direction === 'ASC') ? 'asc' : 'desc') . '"' : '';
			$html .= ' id="' . $name . '"';
			if (isset ( $this->metrics ) && in_array ( $field, $this->metrics )) {
				$html .= ' class="metric-column " ';
			}
			$html .= '>';
			if ($this->fieldData [$name]->isSorted () == 1) {
				$url = array ();
				$url [] = $this->_sortParameter . '=' . $fieldName;
				$url [] = $this->_directionParameter . '=' . (($this->direction === 'DESC') ? 'ASC' : 'DESC');

				$page = $this->getPage ();
				if (! empty ( $page )) {
					$url [] = 'p=' . $this->getPage ();
				}

				$limit = $this->getLimit ();
				if (! empty ( $limit )) {
					$url [] = 'limit=' . $limit;
				}

				$delete = $this->getDelete ();
				if (! empty ( $delete )) {
					$url [] = 'is_deleted=' . $delete;
				}

				if (! empty ( $this->filterParams )) {
					$url [] = implode ( "&", $this->filterParams );
				}

				$url = (! empty ( $url )) ? "?" . implode ( '&', $url ) : '';

				$attr = '';
				if (! empty ( $this->fieldData [$name]->class ) and ! $this->fieldData [$name]->noOwnAttr and ! empty ( $ajax_attr )) {
					$attr .= ' class="' . $this->fieldData [$name]->class . '" ';
				}
				//if(!empty($this->fieldData[$name]->id) and !$this->fieldData[$name]->noOwnAttr) $attr .= ' id="'.$this->fieldData[$name]->id.'" ';
				//$attr = str_replace('{value}',$this->row_id,$attr);


				$link_action = $this->fieldData [$name]->getAction ();
				$link_action = str_replace ( '{link}', $url, $link_action );
				$link_action = str_replace ( '{value}', $this->grid_id, $link_action );

				$html .= '<a href="' . $url . '" ' . $link_action . $attr . $ajax_attr . ' ' . ((! empty ( $this->grid_id )) ? "id=\"$this->grid_id\"" : "") . '>' . $fieldTitle . '</a>';
				//$html .= '<a href="#" '.$link_action.' >'.$filedTitle.'</a>';
			} elseif (! empty ( $fieldTitle )) {
				$html .= "<span>" . $fieldTitle . "</span>";
			} else {
				$html .= '&nbsp;';
			}
				
			if (! empty ( $fieldTitle ) and $this->tooltip) {
				$html .= '<sup><a href="/' . self::$ajaxModuleName . '/help/add/?form[table_name]=' . $this->table->getTableName () . '&form[place]=Grid&form[field]=' . $fieldName . '"
						 				 onmouseover="showTooltip(this,\'Grid\',\'' . $this->table->getTableName () . '\',\'' . $fieldName . '\')"
						 				 style="color:white; background: none; text-decoration: none" class="tooltip" title="wait..."
						 				 module="' . self::$ajaxModuleName . '">?</a></sup>';
			}
				
			$html .= '</th>';
		}

		return $html;
	}

	public function renderAction() {
		if (empty ( $this->actions ) or ! $this->ajaxActionName)
		return;

		foreach ( $this->actions as $key => $name ) {
			if (is_array ( $name )) {
				$i = 0;
				foreach ( $name as $sub_key => $value ) {
					/*if(is_numeric($sub_key)) {
					 $sub_key = $value;
					 }*/
					$i ++;
					$actions [] = '<input type="button" class="column-action" name="' . $key . '" value="' . $value . '" id="' . $this->grid_id . '" action="' . $this->ajaxActionName . '" module="' . self::$ajaxModuleName . '" index="' . $i . '">
													  <input type="hidden" value="' . $sub_key . '" name="' . $key . '" id="' . $key . '_hidden_' . $i . '">';

				}
			} else {
				if (is_numeric ( $key )) {
					$key = $name;
				}
				$actions [] = '<input type="button" class="column-action" name="' . $key . '" value="' . $name . '" id="' . $this->grid_id . '" action="' . $this->ajaxActionName . '" module="' . self::$ajaxModuleName . '">
												  <input type="hidden" value="' . $name . '" name="' . $key . '" id="' . $key . '_hidden">';
			}
		}

		$html = implode ( '&nbsp;', $actions );

		return $html;
	}

	public function renderPaginator() {
		if (empty ( $this->_data ['array_pages'] ))
		return;

		$html = '';
		$url = array ();
		$ajax_attr = '';
		if ($this->actionAjax) {
			$ajax_attr = ' class="' . $this->ajaxClassName . '" action="' . $this->ajaxActionName . '"' . '" module="' . self::$ajaxModuleName . '" ';
			if (! empty ( $this->grid_id ))
			$ajax_attr .= 'id="' . $this->grid_id . '" ';
		}

		if (! empty ( $this->filterParams )) {
			$url [] = implode ( "&", $this->filterParams );
		}

		if ($this->active_mode && ! empty ( $_params [$this->_sortParameter] )) {
			//if(!empty($url)) $url .= "&";
			$url [] = $this->_sortParameter . "=" . $_params [$this->_sortParameter];
			$url [] = $this->_directionParameter . "=" . $_params [$this->_directionParameter];
		}

		$limit_url = $url;
		$limit = $this->getLimit ();
		$url [] = 'limit=' . $this->getLimit ();

		$delete = $this->getDelete ();
		if (! empty ( $delete )) {
			$url [] = 'is_deleted=' . $delete;
		}
		$url = (! empty ( $url )) ? implode ( '&', $url ) : '';
		$limit_url = (! empty ( $limit_url )) ? implode ( '&', $limit_url ) : '';

		if ($this->actionAjax) {
			$ajax_attr = ' class="limit-per-page" action="' . $this->ajaxActionName . '"' . '" module="' . self::$ajaxModuleName . '" ';
			if (! empty ( $this->grid_id ))
			$ajax_attr .= 'id="' . $this->grid_id . '" ';
		} else {
			$ajax_attr = 'class="limit-per-page-2"';
		}
		$limit_select = array ('10' => '10', '20' => '20', '50' => '50', '100' => '100', '200' => '200' );
		if ($limit == 'all') {
			$limit_select ['all'] = 'All';
		}
		$html .= '<select name="limit" ' . $ajax_attr . ' params="?' . $limit_url . '">';
		if ($this->actionAjax) {
			$ajax_attr = '';
		}
		foreach ( $limit_select as $key => $value ) {
			$selected = ($key == $this->getLimit ()) ? 'selected' : '';
			$html .= '<option value= ' . $key . ' ' . $selected . '>' . $value . '</option>';
		}
		$html .= '</select> | ';

		if ($limit == 'all') {
			$html .= ' Total: ' . count ( $this->_data ['data'] );
			return $html;
		}

		if (! empty ( $url )) {
			$url = "&" . $url;
		}

		foreach ( $this->_data ['array_pages'] as $page => $exist ) {
				
			if ($page == 'prev' && $exist != 0) {
				// /'.$module.'/'.$this->controller.'/'.$this->action.'/
				$html .= '<a href="?p=' . $exist . $url . '"' . $ajax_attr . '>&lt Previous</a>  | ';
			} elseif ($page == 'next' && $exist != 0) {
				$html .= '<a href="?p=' . $exist . $url . '"' . $ajax_attr . '>Next &gt</a> ';
			} elseif ($exist === 'now') {
				$html .= $page . ' | ';
			} elseif ($exist != 0 && $page != 'last' && $page != 'first') {
				$html .= '<a href="?p=' . $page . $url . '"' . $ajax_attr . '>' . $page . '</a>  | ';
			}

		}
		if (! empty ( $limit_url )) {
			$limit_url = "&" . $limit_url;
		}
		if (! empty ( $delete )) {
			$limit_url .= '&is_deleted=' . $delete;
		}
		$html .= '  <a href="?p=all' . $limit_url . '"' . $ajax_attr . '>' . 'All</a> ';

		return $html;
	}

	public function addColumn($name, $options) {
		$this->fieldData [] = new ArOn_Crud_Grid_Column_Default ( $name, $options );
	}

	protected function setData() {
		$this->prepareCurrentSelect ();
		$result = $this->table->fetchAll ( $this->currentSelect );
		$this->_data ['data'] = ($result !== null) ? $result->toArray() : array(); 
		$this->paginate ();
	}
	
	public function setAlternativeData($data){
		$this->_data = $data;
		return $this;
	}
	
	public function getCurrentSelect(){
		$this->prepareCurrentSelect();
		return $this->currentSelect;
	}

	protected function prepareCurrentSelect() {

		$sort_fields = $this->sort;
		if (!is_array($sort_fields)) {
			$sort_fields = array($sort_fields);
		}

		$page = $this->getPage();
		$limit = $this->getLimit();
		if($limit !== false && $limit !== 'all') 
			$this->currentSelect->limitPage($page,$limit);

		foreach ($sort_fields as $sort_field){
			$order[] = $sort_field . ' ' . (($this->direction === 'DESC') ? 'DESC' : 'ASC');
		}

		$filtersApplied = false;

		/*if (isset($_params['id'])) {
		 	
		$this->currentSelect->filterId($_params['id']);
		$filtersApplied = true;
			
		} else {*/

		// to preserve compatibility with existing code
		$filtersApplied = $this->filters->applyFilters($this->currentSelect);
		$filter = $this->filters->getFilterWhere ();
		if (! empty ( $this->where )) {
			$filter [] = $this->where;
		}
		if ($this->trash) {
			$this->trashFilter ();
		}

		foreach ( $filter as $key => $val ) {
			if (is_int ( $key )) {
				$this->currentSelect->where ( $val );
			} else {
				$this->currentSelect->where ( $key, $val );
			}
			$filtersApplied = true;
		}


		$this->currentSelect->removeDuplicateColumns ();

		if ($this->category) {
			if (is_string ( $this->category )) {
				$this->category = ArOn_Crud_Tools_Registry::singleton ( $this->category );
			}
				
			$categorySelect = $this->category->select ()->columnsId ( "_category_id" )->columnsName ( "_category" );
				
			if (! $filtersApplied) {
				$this->currentSelect = $categorySelect;
			} else {
				$ref = $this->category->getReferenceEx ( $this->table->getClass() );
				$this->currentSelect->columns ( $ref ['refColumns'] );
				$categorySelect->joinLeft ( array ("t" => $this->currentSelect ), "t." . $ref ['refColumns'] . "=" . $categorySelect->getAlias () . "." . $ref ['columns'] );
				$this->currentSelect = $categorySelect;
			}
		}
		
		if ($this->category) {
			$this->currentSelect->reset(Zend_Db_Select::ORDER);
			$this->currentSelect->order ( "_category" );
		}elseif(!empty($order)){
			$this->currentSelect->reset(Zend_Db_Select::ORDER);			
			$this->currentSelect->order ( $order );
		}
		$this->currentSelect = $this->updateCurrentSelect ( $this->currentSelect );
	}

	function paginate() {

		$page = $this->getPage ();
		if (empty ( $page )) {
			$page = 1;
		}
		$limit = $this->getLimit ();
		
		if ($page != 'all' && $limit !== 'all' && $limit !== false) {
			$id_page = $page;			
				
			$mess_num = $limit;
			if ($mess_num == NULL)
			$mess_num = 10;
			if (! isset ( $page_num ))
			$page_num = 11;
			if ($page_num % 2 == 0)
			$page_num = $page_num + 1;

			$lines = ($this->_ifCount) ? $this->currentSelect->getRowCount() : $this->getLimit();
			//$lines = count ( $this->_data ['data'] );
			$pages = ( int ) (($lines + $mess_num - 1) / $mess_num);
				
			if (! isset ( $id_page )) {
				$start = 0;
				$pn2 = 1;
			} else {
				$pn2 = $id_page;
				if (is_numeric ( $pn2 ) && $pn2 <= $pages && $pn2 >= 1) {
					$start = $pn2 - 1;
				} else {
					$start = 0;
					$pn2 = 1;
				}
			}
			$start = $mess_num * ($start - 1) + $mess_num;
				
			if ($page_num >= $pages) {
				$st1 = 1;
				$st2 = $pages;
			} elseif (($pn2 > (($page_num - 1) / 2)) && ($pn2 < $pages - (($page_num - 1) / 2))) {
				$st1 = $pn2 - (($page_num - 1) / 2);
				$st2 = $pn2 + (($page_num - 1) / 2);
			} elseif (($pn2 >= $pages - (($page_num - 1) / 2)) && ($pn2 <= $pages)) {
				$st1 = $pages - ($page_num - 1);
				$st2 = $pages;
			} else {
				$st1 = 1;
				$st2 = $page_num;
			}
				
			if (($pn2 - 1) >= 1) {
				$prev = ($pn2) - 1;
			} else {
				$prev = 1;
			}
				
			if (($pn2 + 1) <= $pages) {
				$next = ($pn2) + 1;
			} else {
				$next = $pages;
			}
				
			$page_count = "";
			$array_pages = array ();
				
			$array_pages ['first'] = ($prev > 1) ? 1 : 0;
				
			if ($pn2 != $prev) {
				$array_pages ['prev'] = $prev;
			} elseif ($lines != 0) {
				$array_pages ['prev'] = 0;
			}
				
			for($n = $st1; $n <= $st2; $n ++) {
				$array_pages [$n] = ($pn2 == $n) ? 'now' : 1;
			}
				
			$array_pages ['next'] = ($next != $pn2 and $lines != 0) ? $next : 0;
			$array_pages ['last'] = ($next != $pages) ? $pages : 0;
				
			if ($pages == $pn2) {
				$mess_now = $lines - ($pages - 1) * $mess_num;
			} elseif ($lines == 0)
			$mess_now = 0;
			else {
				$mess_now = $mess_num;
			}
				
			//$this->_data ['data'] = array_slice ( $this->_data ['data'], $start, $mess_num, false );
			$this->_data ['array_pages'] = $array_pages;
			$this->_data ['mess_count'] = $mess_num;
			$this->_data ['mess_now'] = $mess_now;
			$this->_data ['page_count'] = $page_num;
			$this->_data ['all_count'] = $lines;
			$this->_data ['all_page'] = $pages;
			$this->_data ['page_link'] = $page_count;
			$this->_data ['page_now'] = $pn2;

		}else{
			$this->_data ['all_count'] = count($this->_data ['data']);
		}		
			

	}
	
	public function getFieldsTitle(){
		$titles = array();
	
		foreach ( $this->fieldNames as $name => $field ) {
			$fieldTitle = $this->fieldData [$name]->getTitle ();
			$fieldName = $this->fieldData [$name]->getName ();
			$titles [ $fieldName ] = $fieldTitle;
		}
		return $titles;	
	}
	
	public function getData() {
		if (empty ( $this->_data )) {
			$this->setData ();
		}
		return $this->_data;
	}
	
	public function getDataWithRenderValues(){
		if (empty ( $this->_data )) {
			$this->setData ();
		}
		$data = array();
		$data = $this->_data;
		foreach ( $data ['data'] as $i => $row ) {
			//unset($data ['data'][$i]);
			//$data ['data'][$i] = array();
			foreach ( $this->fields as $name => $field ) {
				$field->row_id = $row['id'];
				if ($field instanceof ArOn_Crud_Grid_Column) {
					$data ['data'][$i][$name] = $field->render ( $row );
				}
			}
		}
		return $data;
	}
	
	public function getCount() {

		if (empty ( $this->_data ) || empty($this->_data ['all_count'])) {
			$this->prepareCurrentSelect ();
			$this->paginate ();
		}
		return $this->_data ['all_count'];

	}

	public function clearData() {
		$this->_data = null;
	}

	public function getTitleColumns() {
		return $this->fieldNames;
	}

	protected function getPage() {
		if ($this->active_mode && isset ( $this->_params ['p'] ))
		return $this->_params ['p'];
		else
		return @$this->default ['p'];
	}
	
	protected function setPage($page = 1) {
		if ($this->active_mode && isset ( $this->_params ['p'] ))
		$this->_params ['p'] = $page;
		else
		$this->default ['p'] = $page;
	}

	protected function getSort() {
		$this->setSort();
		return $this->sort;
	}

	protected function getDirection(){
		$this->setDirection();
		return $this->direction;
	}

	protected function getLimit() {
		if ($this->active_mode && ! empty ( $this->_params ['limit'] ))
		return $this->_params ['limit'];
		else
		return @$this->default ['limit'];
	}

	protected function getDelete() {
		if ($this->active_mode && isset ( $_params ['is_deleted'] ) && $_params ['is_deleted'] != '')
		return $_params ['is_deleted'];
		else
		return @$this->default ['is_deleted'];
	}

	protected function setSort() {
		if ($this->active_mode && isset ( $this->_params [$this->_sortParameter] ) and $this->_params [$this->_sortParameter] != '')
		$this->sort = $this->_params [$this->_sortParameter];
		elseif (! empty ( $this->default [$this->_sortParameter] ))
		$this->sort = $this->default [$this->_sortParameter];
	}

	protected function setDirection() {
		if ($this->active_mode && isset ( $this->_params [$this->_directionParameter] ) and $this->_params [$this->_directionParameter] != '')
		$this->direction = $this->_params [$this->_directionParameter];
		elseif (! empty ( $this->default [$this->_directionParameter] ))
		$this->direction = $this->default [$this->_directionParameter];
	}


	protected function setFilterParams($params){
		if ( empty ( $params )) return false;

		if (! is_array ( $this->filterParams ))
		$this->filterParams = array ($this->filterParams );
		/*foreach ( $params as $f_key => $f_value ) {
			if(!empty($f_value) && !in_array($f_key,$constFilter))
			$this->filterParams [$f_key] = ($this->filterPrefix) ? "$this->filterPrefix[$f_key]=$f_value" : "$f_key=$f_value";
			}*/
		$this->filterParams = ($this->filterPrefix) ? array($this->filterPrefix => $params) : $params;
		$params = $this->filterParams;
		$this->filterParams = array();
		$this->getParameters($params);
		$this->deep = 0;
	}
	protected $deep = 0;
	protected function getParameters(array $params,$param='') {
		$exeptions = array('от','до');
		foreach($params as $key=>$value) {
			if(is_array($value)) {
				$key = ($this->deep == 0) ? $key : "[$key]";
				$this->deep++;
				$this->getParameters($value,$param.$key);
				$this->deep--;
			} elseif(!empty($value) && !in_array($value,$exeptions)) {
				if($this->deep != 0)
				$key = (!is_numeric($key))?"[".$key."]":"[]";
				$constFilter = array($this->_sortParameter,$this->_directionParameter,'limit','p','is_deleted','Apply','Clear');
				$this->filterParams[] = $param.$key."=".$value;
				//$this->deep = 0;
				if($this->deep == 0) $param = '';
			}
		}
	}
	public function getAllFilterParams($params = false){
		$all_params = array();
		if($params !== false) $this->filterParams = "";
		$params = $this->getFilterParams($params);
		foreach ($params as $param){
			$param_array = explode('=',$param);
			$all_params[] = array( 'name' => $param_array[0] , 'value' => $param_array[1]);
		}
		$all_params[] = array( 'name' => $this->_sortParameter , 'value' => (is_array($sort = $this->getSort())) ? $sort[0] : $sort);
		$all_params[] = array( 'name' => $this->_directionParameter , 'value' => $this->getDirection());
		$all_params[] = array( 'name' => 'limit' , 'value' => $this->getLimit());
		$all_params[] = array( 'name' => 'p' , 'value' => $this->getPage());
		return $all_params;
	}

	public function getFilterParam($param_name){
		$param_value = false;
		$params = $this->getFilterParams($params);
		foreach ($params as $param){
			$param_array = explode('=',$param);
			if ($param_array[0] == $param_name){
				$param_value = $param_array[1];
				break;
			}
		}
		return $param_value;
	}

	static function decodeParams2Array($params){
		/*if(is_string($params)) $params = explode('&',$params);
		foreach ($params as $param){
			list($name,$value) = explode('=',$param);
			$name = str_replace(']','',$name);
			$name = explode('[',$name);
			$tmp_array = array();
			if(!isset($decode[$name[0]])) $decode[$name[0]] = array();
			$tmp_array = &$decode[$name[0]];
			for($i=1;$i<count($name);$i++){
				if(!isset($tmp_array[$name[$i]])) $tmp_array[$name[$i]] = array();
				$tmp_array = &$tmp_array[$name[$i]];
			}
			$tmp_array = $value;
			unset($tmp_array);
		}*/
		if(is_array($params)) $params = implode('&',$params);
		parse_str($params,$decode);
		return $decode;
	}

	protected function trashFilter() {
		if (isset ( $_params ['is_deleted'] ) && $_params ['is_deleted'] != '') {
			if ($this->currentSelect instanceof ArOn_Db_TableSelect) {
				$this->currentSelect->filterDeleted ( true );
			} else {
				$this->currentSelect->where ( $this->table->getTableName () . ".is_deleted=?", trim ( $_params ['is_deleted'] ) );
			}
		} else {
			$this->currentSelect->where ( $this->table->getTableName () . ".is_deleted=0" );
		}
	}

	protected function updateCurrentSelect($select) {

		return $select;
	}

	public function addHelper($key, $value) {
		$this->helper [$key] = $value;
	}

	protected function loadHelper() {
		foreach ( $this->helper as $var => $value ) {
			$this->$var = $value;
		}
	}

	protected function loadOptions($options) {
		foreach ( $options as $var => $value ) {
			$this->$var = $value;
		}
	}

	public function clearFilters() {
		$this->filters = NULL;
	}

	public function setNoTitle() {
		$this->renderTitle = false;
	}
	
	public function setNoPager() {
		$this->renderPager = false;
	}
	
	public function setRowIdName($spec) {
		$this->rowIdName = $spec;
	}
	
	public function setNotCountQuery(){
		$this->_ifCount = false;
	}
	
	public function getFilterFields() {
		return $this->filters->fields;
	}

	public function getFieldByName($name) {
		if (array_key_exists($name, $this->fields))
		return $this->fields[$name];
		else
		return false;
	}
	
	public function getModel() {
		return $this->table;
	}
	
	public function setWhere($where) {
		$this->where = $where;
		return $this;
	}

	public function setParams(array $params){
		$this->_params = $params;
		return $this;
	}
	
	public function setLimit($limit){
		$this->_params['limit'] = $limit;
		return $this;
	}
	
	public function setDefaultParams(array $params){
		$this->default = $params;
		return $this;
	}

	public function setAttrib($name, $value) {
		$name = ( string ) $name;

		if (null === $value) {
			unset ( $this->options [$name] );
		} else {
			$this->options [$name] = $value;
		}

		return $this;
	}

	public function setAttribs(array $attribs) {
		foreach ( $attribs as $key => $value ) {
			$this->setAttrib ( $key, $value );
		}

		return $this;
	}

	public function setFilterValue($name, $value) {
		$this->filters->getElement ( $name )->setValue ( $value );
		if($this->filterPrefix)
		$_params [$this->filterPrefix] [$name] = $value;
		else
		$_params [$name] = $value;
		return $this;
	}
	/**
	 *  Action functions
	 */

	public function deleteAction($ids) {

		$idkey = $this->table->getPrimary ();

		if (is_array ( $ids )) {
			$results = true;
			foreach ( $ids as $id ) {
				$result = $this->table->delete ( "`$idkey` = '$id'" );
				if (! $result)
				$results = $result;
			}
			return $results;
		} else {
			return $this->table->delete ( "`$idkey` = '$ids'" );
		}
		return $this->table->delete ( $where );
	}
	public function statusAction($ids, $param) {

		$idkey = $this->table->getPrimary ();
		$data = array ('status' => "$param" );
		if (is_array ( $ids )) {
			$results = true;
			foreach ( $ids as $id ) {
				$result = $this->table->update ( $data, "`$idkey` = '$id'" );
				if (! $result)
				$results = $result;
			}
			return $results;
		} else {
			return $this->table->update ( $data, "`$idkey` = '$ids'" );
		}
	}
	public function is_activeAction($ids, $param) {

		$idkey = $this->table->getPrimary ();
		$data = array ('is_active' => "$param" );
		if (is_array ( $ids )) {
			$results = true;
			foreach ( $ids as $id ) {
				$result = $this->table->update ( $data, "`$idkey` = '$id'" );
				if (! $result)
				$results = $result;
			}
			return $results;
		} else {
			return $this->table->update ( $data, "`$idkey` = '$ids'" );
		}
	}
	public function undeleteAction($ids) {

		$idkey = $this->table->getPrimary ();
		$column = $this->table->getIsDeleted ();
		$data = array ($column => "0" );
		if (is_array ( $ids )) {
			$results = true;
			foreach ( $ids as $id ) {
				$result = $this->table->update ( $data, "`$idkey` = '$id'" );
				if (! $result)
				$results = $result;
			}
			return $results;
		} else {
			return $this->table->update ( $data, "`$idkey` = '$ids'" );
		}
		return $this->table->delete ( $where );
	}
	
	public function getGridWidth(){		
		if(empty($this->_width)) $this->setGridWidth();
		return $this->_width;
	}
	
	public function getGridWidthAssoc(){
		if(empty($this->_assoc_width)) $this->setGridWidth();
		return $this->_assoc_width;
	}
	
	protected function setGridWidth(){
		foreach ( $this->fieldNames as $name => $field ){
				$width =  $this->fieldData [$name]->getWidth();
				$this->_width += $width; 
				$this->_assoc_width [$name] = $width; 
		}
	}
	
}
