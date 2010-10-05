<?php
class ArOn_Crud_Tools_Registry extends Zend_Registry {
	static public function singleton($className) {
		/*if (self::isRegistered($className)) {
			return self::get($className);
		}*/		
		if(class_exists($className) === false) return false;
		if(method_exists($className, 'getInstance')){
			$fn = $className ."::" . "getInstance";
			$class = call_user_func($fn,$className);
		}else{
			$class = new $className;
		}
		self::set($className, $class);
		return $class;
	}
}