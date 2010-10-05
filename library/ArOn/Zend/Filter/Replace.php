<?php

/**
 * ArOn_Zend_Filter_Replace
 * 
 * Фильтр создающий строку для употребления в качестве мета тэга keywords. 
 * 
 * @author 
 * @version 0.2
 *
 */
 
class ArOn_Zend_Filter_Replace implements Zend_Filter_Interface 
{
    /**
     * Массив символов, которые будут удаленны из строки
     *
     * @var array
     */
 
    protected $_searchSymbols = array();
 
    /**
     * Массив символов, которые будут заменены в строке
     *
     * @var array
     */
 
    protected $_replaceWords = array();
 
 
    /**
     * Конструктор класса
     *
     */
 
    public function __construct($searchWords, $replaceSymbols)
    {
        if (!is_array($searchWords)) {
        	$searchWords = array($searchWords);
        }
 
        if (!is_array($replaceSymbols)) {
        	$replaceSymbols = array($replaceSymbols);
        }
        $this->_searchSymbols = $searchWords;
        $this->_replaceWords   = $replaceSymbols;
    }
 
    /**
     * Основной метод фильтрующий строку
     *
     * @param string $value строка для обработки
     * @return string результат
     */
 
    public function filter($value) 
    {
        return str_replace($this->_searchSymbols, $this->_replaceWords, $value);
    }
}
