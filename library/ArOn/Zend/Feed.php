<?php

class ArOn_Zend_Feed extends Zend_Feed
{

    /**
     * Construct a new Zend_Feed_Abstract object from a custom array
     *
     * @param  array  $data
     * @param  string $format (rss|atom) the requested output format
     * @return Zend_Feed_Abstract
     */
    public static function importArray(array $data, $format = 'atom')
    {
        $obj = 'ArOn_Zend_Feed_' . ucfirst(strtolower($format));
        if (!class_exists($obj)) {
            //require_once'Zend/Loader.php';
            Zend_Loader::loadClass($obj);
        }

        /**
         * @see ArOn_Zend_Feed_Builder
         */
        //require_once'Zend/Feed/Builder.php';
        return new $obj(null, null, new ArOn_Zend_Feed_Builder($data));
    }

    /**
     * Construct a new Zend_Feed_Abstract object from a Zend_Feed_Builder_Interface data source
     *
     * @param  Zend_Feed_Builder_Interface $builder this object will be used to extract the data of the feed
     * @param  string                      $format (rss|atom) the requested output format
     * @return Zend_Feed_Abstract
     */
    public static function importBuilder(Zend_Feed_Builder_Interface $builder, $format = 'atom')
    {
        $obj = 'ArOn_Zend_Feed_' . ucfirst(strtolower($format));
        if (!class_exists($obj)) {
            //require_once'Zend/Loader.php';
            Zend_Loader::loadClass($obj);
        }
        return new $obj(null, null, $builder);
    }
}
