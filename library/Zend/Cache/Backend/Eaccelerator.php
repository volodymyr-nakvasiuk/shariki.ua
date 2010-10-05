<?php
/**
* @author Aco <aco dot best at gmail dot com> | WebITeam
*/

/**
* @see Zend_Cache_Backend_Interface
*/
//require_once'Zend/Cache/Backend/ExtendedInterface.php';

/**
* @see Zend_Cache_Backend
*/
//require_once'Zend/Cache/Backend.php';

/**
* @package Zend_Cache
* @subpackage Zend_Cache_Backend
*/
class Zend_Cache_Backend_Eaccelerator extends Zend_Cache_Backend implements Zend_Cache_Backend_ExtendedInterface
{
	/**
	* Log message
	*/
	const TAGS_UNSUPPORTED_BY_CLEAN_OF_EA_BACKEND = 'Zend_Cache_Backend_Eaccelerator::clean() : tags are unsupported by the eaccelerator backend';
	const TAGS_UNSUPPORTED_BY_SAVE_OF_EA_BACKEND = 'Zend_Cache_Backend_Eaccelerator::save() : tags are unsupported by the eaccelerator backend';
	
	protected $_options = array();

	public function __construct(array $options = array()){
		
		if (!extension_loaded('eAccelerator'))	{
			Zend_Cache::throwException('The eaccelerator extension must be loaded for using this backend !');
		}
		
		$funcs = get_extension_funcs('eAccelerator');	
		if(!in_array("eaccelerator_get",$funcs) || !in_array("eaccelerator_put",$funcs))	{
			Zend_Cache::throwException('The eaccelerator extension must be loaded completely for using this backend !');
		}
		
		parent::__construct($options);
	}
	/**
	* Save some string datas into a cache record
	*
	* Note : $data is always "string" (serialization is done by the
	* core not by the backend)
	*
	* @param string $data datas to cache
	* @param string $id cache id
	* @param array $tags array of strings, the cache record will be tagged by each string entry
	* @param int $specificLifetime if != false, set a specific lifetime for this cache record (null => infinite lifetime)
	* @return boolean true if no problem
	*/
	public function save($data, $id, $tags = array(), $specificLifetime = false)
	{
		$lifetime = $this->getLifetime($specificLifetime);
		if (count($tags) > 0){
			$this->_log(self::TAGS_UNSUPPORTED_BY_SAVE_OF_EA_BACKEND);
		}
		return eaccelerator_put($id, $data, $lifetime);
	}
	/**
	* Test if a cache is available for the given id and (if yes) return it (false else)
	*
	* WARNING $doNotTestCacheValidity=true is unsupported by the Eaccelerator backend
	*
	* @param string $id cache id
	* @param boolean $doNotTestCacheValidity if set to true, the cache validity won't be tested
	* @return string cached datas (or false)
	*/
	public function load($id, $doNotTestCacheValidity = false){
		return eaccelerator_get($id);
	}
	
	public function test($id){
		$tmp = eaccelerator_get($id);
		if ($tmp) {
			return $tmp;
		}
		return false;
	}
	
	/**
	* Remove a cache record
	*
	* @param string $id cache id
	* @return boolean true if no problem
	*/
	public function remove($id){
		return eaccelerator_rm($id);
	}
	
	/**
	* Clean some cache records
	*
	* Available modes are :
	* 'all' (default) => remove all cache entries ($tags is not used)
	* 'old' => unsupported
	* 'matchingTag' => unsupported
	* 'notMatchingTag' => unsupported
	* 'matchingAnyTag' => unsupported
	*
	* @param string $mode clean mode
	* @param array $tags array of tags
	* @throws Zend_Cache_Exception
	* @return boolean true if no problem
	*/
	public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array())
	{
		switch ($mode)	{
			case Zend_Cache::CLEANING_MODE_ALL:
				return eaccelerator_clear() && eaccelerator_clean();
				break;
			case Zend_Cache::CLEANING_MODE_OLD:
				return eaccelerator_gc();
				break;
			case Zend_Cache::CLEANING_MODE_MATCHING_TAG:
			case Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG:
			case Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG:
				$this->_log(self::TAGS_UNSUPPORTED_BY_CLEAN_OF_EA_BACKEND);
				break;
			default:
				Zend_Cache::throwException('Invalid mode for clean() method');
				break;
		}
	}
	
	/**
	* Return true if the automatic cleaning is available for the backend
	*
	* DEPRECATED : use getCapabilities() instead
	* 
	* @deprecated 
	* @return boolean
	*/
	public function isAutomaticCleaningAvailable(){
		return false;
	}
	
	/**
	* Return the filling percentage of the backend storage
	* 
	* @throws Zend_Cache_Exception
	* @return int integer between 0 and 100
	*/
	public function getFillingPercentage(){
		$eaInfo = eaccelerator_info();
		if ($eaInfo["memorySize"] == 0) {
			Zend_Cache::throwException("can't get eaccelerator memory size");
		}
		if ($eaInfo["memoryAllocated"] > $eaInfo["memorySize"]) {
			return 100;
		}
		return ((int) (100. * ($eaInfo["memoryAllocated"] / $eaInfo["memorySize"])));
	}
	
	/**
	* Return an array of stored tags
	*
	* @return array array of stored tags (string)
	*/
	public function getTags(){ 
		$this->_log(self::TAGS_UNSUPPORTED_BY_SAVE_OF_EA_BACKEND);
		return array();
	}
	
	/**
	* Return an array of stored cache ids which match given tags
	* 
	* In case of multiple tags, a logical AND is made between tags
	*
	* @param array $tags array of tags
	* @return array array of matching cache ids (string)
	*/
	public function getIdsMatchingTags($tags = array()){
		$this->_log(self::TAGS_UNSUPPORTED_BY_SAVE_OF_EA_BACKEND);
		return array(); 
	}
	
	/**
	* Return an array of stored cache ids which don't match given tags
	* 
	* In case of multiple tags, a logical OR is made between tags
	*
	* @param array $tags array of tags
	* @return array array of not matching cache ids (string)
	*/ 
	public function getIdsNotMatchingTags($tags = array()){
		$this->_log(self::TAGS_UNSUPPORTED_BY_SAVE_OF_EA_BACKEND);
		return array(); 
	}
	
	/**
	* Return an array of stored cache ids which match any given tags
	* 
	* In case of multiple tags, a logical AND is made between tags
	*
	* @param array $tags array of tags
	* @return array array of any matching cache ids (string)
	*/
	public function getIdsMatchingAnyTags($tags = array()){
		$this->_log(self::TAGS_UNSUPPORTED_BY_SAVE_OF_EA_BACKEND);
		return array(); 
	}
	
	/**
	* Return an array of stored cache ids
	* 
	* @return array array of stored cache ids (string)
	*/
	public function getIds(){
		return eaccelerator_list_keys();
	}
	
	/**
	* Return an array of metadatas for the given cache id
	*
	* The array must include these keys :
	* - expire : the expire timestamp
	* - tags : a string array of tags
	* - mtime : timestamp of last modification time
	* 
	* @param string $id cache id
	* @return array array of metadatas (false if the cache id is not found)
	*/
	public function getMetadatas($id)
	{
		return false; 
	}
	
	/**
	* Give (if possible) an extra lifetime to the given cache id
	*
	* @param string $id cache id
	* @param int $extraLifetime
	* @return boolean true if ok
	*/
	public function touch($id, $extraLifetime)	{	
		$tmp = eaccelerator_get($id);
		if ($tmp !== null){
			eaccelerator_put($id, $tmp, $extraLifetime);
			return true;
		}
		return false;
	}
	
	/**
	* Return an associative array of capabilities (booleans) of the backend
	* 
	* The array must include these keys :
	* - automatic_cleaning (is automating cleaning necessary)
	* - tags (are tags supported)
	* - expired_read (is it possible to read expired cache records
	* (for doNotTestCacheValidity option for example))
	* - priority does the backend deal with priority when saving
	* - infinite_lifetime (is infinite lifetime can work with this backend)
	* - get_list (is it possible to get the list of cache ids and the complete list of tags)
	* 
	* @return array associative of with capabilities
	*/
	public function getCapabilities()	{
		return array(
			'automatic_cleaning' => false,
			'tags' => false,
			'expired_read' => false,
			'priority' => false,
			'infinite_lifetime' => false,
			'get_list' => true
		);
	}
}
?>