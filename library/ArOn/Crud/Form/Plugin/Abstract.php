<?php

abstract class ArOn_Crud_Form_Plugin_Abstract {
	
        protected $name = null;
        
        abstract public function run($obj, $data, $args);
        
        public function __construct($data = null) {

                if($this->name === null){
                        throw new Exception('Name must be defined');
                }
                $this->init($data);
        }
        
        protected function init($data){

        }
        
        public function toString(){
                return $this->name;
        }
}