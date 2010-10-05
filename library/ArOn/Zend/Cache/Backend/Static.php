<?php

class ArOn_Zend_Cache_Backend_Static extends Zend_Cache_Backend implements Zend_Cache_Backend_Interface
{

    // Available options
    protected $_options = array(
        'public_dir' => null,
        'file_extension' => '.html',
        'index_filename' => 'index',
        'file_locking' => true,
        'cache_file_umask' => 0600,
        'debug_header' => false
    );

    // Test if a cache is available for the given id and (if yes) return it
    // (false else)
    // $id should be the REQUEST_URI whose static file is to be deleted
    public function load($id, $doNotTestCacheValidity = false)
    {
        $id = $this->_decodeId($id);
        if ($doNotTestCacheValidity) {
            $this->_log("ArOn_Zend_Cache_Backend_Static::load() : \$doNotTestCacheValidity=true is unsupported by the Static backend");
        }
        $fileName = basename($id);
        if (empty($fileName)) {
            $fileName = $this->_options['index_filename'];
        }
        $pathName = $this->_options['public_dir'] . dirname($id);
        $file = $pathName . '/' . $fileName . $this->_options['file_extension'];
        if (file_exists($file)) {
            return file_get_contents($file);
        }
        return false;
    }

    // Test if a cache is available or not
    // $id should be the REQUEST_URI whose static file is to be deleted
    public function test($id)
    {
        $id = $this->_decodeId($id);
        $fileName = basename($id);
        if (empty($fileName)) {
            $fileName = $this->_options['index_filename'];
        }
        $pathName = $this->_options['public_dir'] . dirname($id);
        $file = $pathName . '/' . $fileName . $this->_options['file_extension'];
        if (file_exists($file)) {
            return true;
        }
        return false;
    }

    // Save content to a static content file in /public directory
    public function save($data, $id, $tags = array(), $specificLifetime = false)
    {
        clearstatcache();
        $fileName = basename($_SERVER['REQUEST_URI']);
        if (empty($fileName)) {
            $fileName = $this->_options['index_filename'];
        }
        $pathName = $this->_options['public_dir'] . dirname($_SERVER['REQUEST_URI']);
        if (!file_exists($pathName)) {
            mkdir($pathName, $this->_options['cache_file_umask'], true);
        }
        $dataUnserialized = unserialize($data);
        if ($this->_options['debug_header']) {
            $dataUnserialized['data'] =
                'DEBUG HEADER : This is a cached page !' . $dataUnserialized['data'];
        }
        $file = $pathName . '/' . $fileName . $this->_options['file_extension'];
        if ($this->_options['file_locking']) {
            $result = file_put_contents($file, $dataUnserialized['data'], LOCK_EX);
        } else {
            $result = file_put_contents($file, $dataUnserialized['data']);
        }
        @chmod($file, $this->_options['cache_file_umask']);
        if (count($tags) > 0) {
            $this->_log(self::TAGS_UNSUPPORTED_BY_SAVE_OF_STATIC_BACKEND);
        }
        return (bool) $result;
    }

    // Remove a cache record
    // $id should be the REQUEST_URI whose static file is to be deleted
    public function remove($id)
    {
        $id = $this->_decodeId($id);
        $fileName = basename($id);
        if (empty($fileName)) {
            $fileName = $this->_options['index_filename'];
        }
        $pathName = $this->_options['public_dir'] . dirname($id);
        $file = $pathName . '/' . $fileName . $this->_options['file_extension'];
        return unlink($file);
    }

    // Remove a cache record recursively (i.e. the file AND matching directory)
    // it ain't perfect - there may be no file matching the directory name
    // (but you get the point I'm sure!)
    // $id should be the REQUEST_URI whose static file & dir tree is to be deleted
    public function removeRecursively($id)
    {
        $id = $this->_decodeId($id);
        $fileName = basename($id);
        if (empty($fileName)) {
            $fileName = $this->_options['index_filename'];
        }
        $pathName = $this->_options['public_dir'] . dirname($id);
        $file = $pathName . '/' . $fileName . $this->_options['file_extension'];
        $directory = $pathName . '/' . $fileName;
        if (file_exists($directory)) {
            if (!is_writable($directory)) {
                return false;
            }
            foreach (new DirectoryIterator($directory) as $file) {
                if (true === $file->isFile()) {
                    if (false === unlink($file->getPathName())) {
                        return false;
                    }
                }
            }
            rmdir($directory);
        }
        if (file_exists($file)) {
            if (!is_writable($file)) {
                return false;
            }
            return unlink($file);
        }
    }

    // Clean some cache records
    // Not implemented here since we would need a backend tagging system given
    // that static files themselves cannot be tagged in the filename. The noon-tag
    // related functionality could be implemented in the future if required.
    public function clean($mode = Zend_Cache::CLEANING_MODE_ALL, $tags = array())
    {
        switch ($mode) {
            case Zend_Cache::CLEANING_MODE_ALL:
            case Zend_Cache::CLEANING_MODE_OLD:
            case Zend_Cache::CLEANING_MODE_MATCHING_TAG:
            case Zend_Cache::CLEANING_MODE_NOT_MATCHING_TAG:
            case Zend_Cache::CLEANING_MODE_MATCHING_ANY_TAG:
                $this->_log("ArOn_Zend_Cache_Backend_Static : Cleaning Modes Currently Unsupported By This Backend");
                break;
            default:
                Zend_Cache::throwException('Invalid mode for clean() method');
                break;
        }
    }

    // "Danger, Will Robinson!"
    // Ensure path is not below the configured public_dir
    // Encoded by ArOn_Zend_Cache_Backend_Static_Adapter
    protected function _decodeId($id)
    {
        $path = pack('H*', $id);
        if (!$this->_verifyPath($path)) {
            Zend_Cache::throwException('Invalid cache id: does not match expected public_dir path');
        }
        return $path;
    }

    protected function _verifyPath($path)
    {
        $path = realpath($path);
        $base = realpath($this->_options['public_dir']);
        return strncmp($path, $base, strlen($base)) !== 0;
    }

}