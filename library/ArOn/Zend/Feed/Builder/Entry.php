<?php

class ArOn_Zend_Feed_Builder_Entry extends Zend_Feed_Builder_Entry
{    
	public function setText($text)
    {
        $this->offsetSet('text', $text);
        return $this;
    }

}
