<?php
class ArOn_Crud_Form_Field_SwfImageUpload extends ArOn_Crud_Form_Field_AdminImageUpload {

	public function postSaveAction($data = null) {
		echo $this->saveImageFile($data[$this->name]);
		parent::postSaveAction($data);
		exit;
	}

	protected function saveImageFile($fileData = null){
		if ($fileData){
			$targetPath = str_replace('//','/',$this->uploadDirectory.'/');
			if (!is_dir($targetPath)) mkdir($targetPath, 0755, true);
			$targetFile = $targetPath . md5($fileData).'.jpg';
			if ($handle = fopen($targetFile, "wb")) {
				$char_arr = explode('|', $fileData);
				$size = 0;
				foreach ($char_arr as $char){
					if ($s = fwrite($handle, chr($char))){
						$size += $s;
					}
				}
				if (!is_array($_FILES)) $_FILES = array();
				$_FILES[$this->name] = array(
					"name" => basename($targetFile),
					"type" => 'application/octet-stream',
					"tmp_name" => $targetFile,
					"error" => 0,
					"size" => $size,
				);
				return "1";
			}
			return 0;
		}
	}
}