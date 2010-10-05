<?php

class ArOn_Zend_Feed_Builder_Header extends Zend_Feed_Builder_Header {    

	public function __construct($title, $link, $charset = 'utf-8')
    {
        $this->offsetSet('title', $title);
        $this->offsetSet('link', $link);
        $this->offsetSet('charset', $charset);
        /*
         $this->setLastUpdate(time())
             ->setGenerator('Zend_Feed');
        */
    }

}
