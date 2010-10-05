<?php
class ArOn_Crud_Grid_ExtJs_View_GalleryForm extends ArOn_Crud_Grid_ExtJs_View{
	
	protected $_imagesHost='/cms/images/uploader';
	protected $_uploadUrl='/upload.php';
	protected $_uploadFileName='image';
	protected $_maxFileSize=0;
	protected $_fileTypes=array('Картинки (*.jpg, *.jpeg, *.gif, *.png)'=>'*.jpg;*.jpeg;*.gif;*.png');
	protected $_uploadParams = array();
	
	protected $viewTemplate = 'galleryform.phtml';
	protected $_width = 380;
	protected $_height = 295;
	protected $_minimizable = 'false';
	protected $_maximizable = 'false';
	protected $_resizable   = 'false';
	
	protected function renderButtons(){
		$html = '';
		$html .=  "
					    buttons: []
					";
		return $html;
	}
	
	protected function createFlashArray($arr){
		if (!is_array($arr)) $arr = array('param'=>$arr);
		$returnArr = array();
		foreach($arr as $name=>$value){
			$returnArr[] = $name.'='.$value;
		}
		return implode('||',$returnArr);
	}
	
	public function init() {
		$this->_data['imagesHost']=$this->_imagesHost;
		$this->_data['uploadUrl']=$this->_uploadUrl;
		$this->_data['uploadFileName']=$this->_uploadFileName;
		$this->_data['maxFileSize']=$this->_maxFileSize;
		
		$this->_data['fileTypes']=$this->createFlashArray($this->_fileTypes);
		$this->_uploadParams['PHPSESSID'] = $_COOKIE['PHPSESSID'];
		$this->_data['uploadParams']=$this->createFlashArray($this->_uploadParams);
	}
}