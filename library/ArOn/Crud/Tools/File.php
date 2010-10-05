<?php
class ArOn_Crud_Tools_File {
	
	static function sarmdir($root, array $dirs, $mode = 0777, $recursive = true) {
			$root = str_replace("\\","/",$root);
			$root = ($root[-1] == '/') ? substr($root, 0, -1) : $root;
			foreach ($dirs as $dir){
				$path = $root . '/' . $dir;
				if(!is_dir($path))
					self::rmkdir($path, $mode, $recursive);
			}
	}
	
	static function armdir(array $paths, $mode = 0777, $recursive = true) {
		foreach ($paths as $path){
			if(!is_dir($path))
				self::rmkdir($path, $mode, $recursive);
		}
	}
	
	static function rmkdir($path, $mode = 0777, $recursive = true) {
		$path = rtrim(preg_replace(array("/\\\\/", "/\/{2,}/"), "/", $path), "/");
		if (!$recursive){
			$e = explode("/", ltrim($path, "/"));
			if(substr($path, 0, 1) == "/") {
				$e[0] = "/".$e[0];
			}
			$c = count($e);
			$cp = $e[0];
			for($i = 1; $i < $c; $i++) {
				if(!is_dir($cp) && !@mkdir($cp, $mode, $recursive)) {
					return false;
				}
				$cp .= "/".$e[$i];
			}
		}
		return @mkdir($path, $mode, $recursive);
	}
}
