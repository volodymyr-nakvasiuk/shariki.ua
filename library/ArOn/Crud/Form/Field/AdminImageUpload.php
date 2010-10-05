<?php
class ArOn_Crud_Form_Field_AdminImageUpload extends ArOn_Crud_Form_Field_ImageUpload {

	protected $_db_just_filename;
	protected $del_img_source = false;
	protected $waterMark = array('file'=>false, 'position'=>array(4), 'mode'=>7); //mode: big(+1),middle(+2),small(+4)
	protected $resize = array('big'=>'620x465','middle'=>'240x180','small'=>'140x105');

	function __construct($name, $uploadDirectory, $fileName = '{sha}', $db_just_filename = true, $title = null, $description = null, $size = '1024000' /* limit to 100K*/,$required = null, $notEdit = false, $width = 150, $del_img_source = false, $resize=false, $waterMark = false) {
		parent::__construct($name, $uploadDirectory, $fileName, $title, $description, $size,$required, $notEdit);
		$this->_db_just_filename = $db_just_filename;
		$this->del_img_source = $del_img_source;
		if ($waterMark)	$this->waterMark = $waterMark;
		if ($resize) $this->resize = $resize;
	}

	protected function sha1($id = 0) {
		return md5(uniqid($id, true));
	}

	public function postSaveAction($data = null) {
		if (! empty ( $_FILES ) && isset ( $_FILES [$this->name] ) && empty ( $_FILES [$this->name] ['error'] )) {
			$this->fileFullName = $this->fileName;
			$formData = $this->form->getData ();
			$model = $this->form->getModel ();
				
			if(strpos($this->fileFullName,'{sha}') !== false){
				$file_hash_name = ($this->sha1) ? $this->sha1( $this->form->actionId ) : $this->form->actionId;
				$this->fileFullName = str_replace ( '{sha}', $file_hash_name, $this->fileFullName );
			}
			while (($start = strpos($this->fileFullName,'{')) !== false && ($end = strpos($this->fileFullName,'}',$start)) !== false){
				$key = substr($this->fileFullName,$start+1,$end-$start-1);
				$value = (key_exists($key, $formData)) ? $formData [$key] : $this->form->actionId;
				$this->fileFullName = str_replace ( "{".$key."}", $value, $this->fileFullName );
			}
			if ($this->fileFullName === false){
				$file = explode ( ".", $_FILES [$this->name] ['name'] );
				$this->fileFullName = $file [0];
			}
				
			$file_type = strtolower(end ( explode ( ".", $_FILES [$this->name] ['name'] ) ));
			$this->fileName = $this->fileFullName.'.'.$file_type;
			$zend_upload_dir = $this->uploadDirectory;
			$this->fileFullName = $this->uploadDirectory.'/'.$this->fileName;
			$pathinfo = pathinfo($this->fileFullName);
			$pathinfo['basename'] = preg_replace('|(.*)\.(.*)$|', '$1.jpg', $pathinfo['basename']);
			$this->uploadDirectory = $pathinfo['dirname'];
			$file_resize_small  = $this->uploadDirectory.'/small/' .$pathinfo['basename'];
			$file_resize_middle = $this->uploadDirectory.'/middle/'.$pathinfo['basename'];
			$file_resize_big    = $this->uploadDirectory.'/big/'   .$pathinfo['basename'];
			
			if ($this->_db_just_filename) $this->fileName = $pathinfo['basename'];
			else $this->fileName = preg_replace('|(.*)\.(.*)$|', '$1.jpg', $this->fileName);
			/* Debuger: */
			/*
			 echo '$file_type = '.$file_type."\n";
			 echo '$this->fileName = '.$this->fileName."\n";
			 echo '$this->uploadDirectory = '.$this->uploadDirectory."\n";
			 echo '$this->fileFullName = '.$this->fileFullName."\n";
			 echo '$file_resize_small = '.$file_resize_small."\n";
			 echo '$file_resize_middle = '.$file_resize_middle."\n";
			 echo '$file_resize_big = '.$file_resize_big."\n";
			 exit;
			 */
			$dirs = array('big','middle','small');
			ArOn_Crud_Tools_File::sarmdir($this->uploadDirectory, $dirs, 0777, true);
			
			if (file_exists ( $this->fileFullName )) unlink ( $this->fileFullName );
			if (is_uploaded_file($_FILES [$this->name]['tmp_name'])) {
				move_uploaded_file($_FILES [$this->name]['tmp_name'], $this->fileFullName);
			}
			elseif (is_file($zend_upload_dir.'/'.$_FILES [$this->name] ['name'])) {
				rename($zend_upload_dir.'/'.$_FILES [$this->name] ['name'], $this->fileFullName);
			}
			if (file_exists ( $file_resize_small ))  unlink ( $file_resize_small );
			if (file_exists ( $file_resize_middle )) unlink ( $file_resize_middle );
			if (file_exists ( $file_resize_big ))    unlink ( $file_resize_big );
			
			$mode = substr(base_convert((string)$this->waterMark['mode'], 10, 2), -3);
			if ($mode{2} && $this->waterMark['file']) $smallWM = $this->waterMark['file'].'_'.$this->resize['small'].'.png';
			else $smallWM = false;
			if ($mode{1} && $this->waterMark['file']) $middleWM = $this->waterMark['file'].'_'.$this->resize['middle'].'.png';
			else $middleWM = false;
			if ($mode{0} && $this->waterMark['file']) $bigWM = $this->waterMark['file'].'_'.$this->resize['big'].'.png';
			else $bigWM = false;
			
			list($w, $h) = explode('x', $this->resize['big']);
			ArOn_Crud_Tools_Image::Resize ( $this->fileFullName, $file_resize_big, $w, $h, true, $bigWM, 1, $this->waterMark['position']);
			list($w, $h) = explode('x', $this->resize['middle']);
			ArOn_Crud_Tools_Image::Resize ( $this->fileFullName, $file_resize_middle, $w, $h, true, $middleWM, 1, $this->waterMark['position']);
			list($w, $h) = explode('x', $this->resize['small']);
			ArOn_Crud_Tools_Image::Resize ( $this->fileFullName, $file_resize_small, $w, $h, true, $smallWM, 1, $this->waterMark['position']);
			
			if ($this->del_img_source){
				unlink($this->fileFullName);
			}
				
			$def_data = array ($this->name => $this->fileName);
			$model->update ( $def_data, $model->getAdapter ()->quoteInto ( $model->getPrimary () . " = ?", $this->form->actionId ) );
		}
	}

}