<?php
/*
// Мой любимый набор стоп-слов
$stopWords = 'без более бы был была были было быть вам вас весь во вот все всего всех вы где да даже для
 до его ее если  есть ещё же за здесь из из-за или им их как как-то ко когда кто ли либо
 мне может мы на надо наш не него неё нет ни них но ну об однако он она они оно от очень по под
 при со так также такой там те тем то того тоже той только том ты уже хотя  чего чей чем
 что чтобы чьё чья эта эти это а б в г д е ё ж з и к л м н о п р с т у ф х ц ч ш щ э ь ы ю я';
 
// Не менее любимые стоп-символы
$stopSymbols = 'x27 x22 x60 \t \n \r \' , . / « » # ; : @ ~ [ ] { } = - + ) ( * & ^ % $ < > ? !';
 
// Выводим десять самых часто-используемых слов
$filter = new Vlasov_Filter_Keywords(10, $stopWords, $stopSymbols);
echo $filter->filter('Проснувшись однажды утром после беспокойного сна, Грегор Замза обнаружил, что он у себя
 в постели превратился в страшное насекомое. Лежа на панцирнотвердой спине, он видел,
 стоило ему приподнять голову, свой коричневый, выпуклый, разделенный дугообразными
 чешуйками живот, на верхушке которого еле держалось готовое вот-вот окончательно сползти одеяло.
 Его многочисленные, убого тонкие по сравнению с остальным телом ножки беспомощно
 копошились у него перед глазами.');
 */



/**
 * ArOn_Zend_Filter_Keywords
 * 
 * Фильтр создающий строку для употребления в качестве мета тэга keywords. 
 * 
 * @author Кирилл "Кирпич" Власов http://techforweb.ru/
 * @version 0.2
 *
 */
 
class ArOn_Zend_Filter_Keywords implements Zend_Filter_Interface 
{
    /**
     * Массив стоп-символов, которые будут удаленны из строки
     *
     * @var array
     */
 
    protected $_stopSymbols = array();
 
    /**
     * Массив стоп-слов, которые будут удаленны из строки
     *
     * @var array
     */
 
    protected $_stopWords = array();
 
    /**
     * Лимит ключевых слов (если 0 значит не лимитированно)
     *
     * @var integer
     */
 
    protected $_limit = 0;
 
    /**
     * Разделитель ключевых слов
     *
     * @var string|null
     */
 
    protected $_separator = null;
 
    /**
     * Конструктор класса
     *
     * @param integer $limit лимит ключевых слов
     * @param array|string $stopWords стоп-слова
     * @param array|string $stopSymbols стоп-символы
     * @param string|null $separator разделитель ключевых слов
     */
 
    public function __construct($limit = 0, $stopWords = array(), $stopSymbols = array(), $separator = ',')
    {
        if (!is_array($stopWords)) {
        	$stopWords = explode(' ', strval($stopWords));
        }
 
        if (!is_array($stopSymbols)) {
        	$stopSymbols = explode(' ', strval($stopSymbols));
        }
 
        $this->_limit       = intval($limit);
        $this->_separator   = $separator;
        $this->_stopSymbols = $stopSymbols;
        $this->_stopWords   = $stopWords;
    }
 
    /**
     * Основной метод фильтрующий строку
     *
     * @param string $value строка для обработки
     * @return string результат
     */
 
    public function filter($value) 
    {   
        $keywords = array();
        $value    = str_replace($this->_stopSymbols, null, $value);
 
        foreach (explode(' ', $value) as $word) {
            if (strlen($word) && !in_array(strtolower($word), $this->_stopWords)) {
            	$keywords[] = trim($word);
            }
        }
 
        $keywords = array_count_values($keywords);
        arsort($keywords);
 
        $keywords = array_keys($keywords);
 
        if ($this->_limit) {
        	$keywords = array_slice($keywords, 0, $this->_limit);
        }
 
        return join($this->_separator . ' ', $keywords);
    }
}
