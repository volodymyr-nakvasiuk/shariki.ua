<?php

class ArOn_Zend_Feed_Builder extends Zend_Feed_Builder{

	/**
     * The data of the feed
     *
     * @var $_data array
     */
    protected $_data;

    /**
     * Header of the feed
     *
     * @var $_header Zend_Feed_Builder_Header
     */
    protected $_header;

    /**
     * List of the entries of the feed
     *
     * @var $_entries array
     */
    protected $_entries = array();
    
	public function __construct(array $data)
    {
        $this->_data = $data;
        $this->_createHeader($data);
        if (isset($data['entries'])) {
            $this->_createEntries($data['entries']);
        }
    }
	
 	/**
     * Returns an instance of Zend_Feed_Builder_Header
     * describing the header of the feed
     *
     * @return Zend_Feed_Builder_Header
     */
    public function getHeader()
    {
        return $this->_header;
    }

    /**
     * Returns an array of Zend_Feed_Builder_Entry instances
     * describing the entries of the feed
     *
     * @return array of Zend_Feed_Builder_Entry
     */
    public function getEntries()
    {
        return $this->_entries;
    }
    
	/**
     * Create the Zend_Feed_Builder_Header instance
     *
     * @param  array $data
     * @throws Zend_Feed_Builder_Exception
     * @return void
     */
    protected function _createHeader(array $data)
    {
        $mandatories = array('title', 'link', 'charset');
        foreach ($mandatories as $mandatory) {
            if (!isset($data[$mandatory])) {
                /**
                 * @see Zend_Feed_Builder_Exception
                 */
                //require_once'Zend/Feed/Builder/Exception.php';
                throw new Zend_Feed_Builder_Exception("$mandatory key is missing");
            }
        }
        $this->_header = new ArOn_Zend_Feed_Builder_Header($data['title'], $data['link'], $data['charset']);
        if (isset($data['lastUpdate'])) {
            $this->_header->setLastUpdate($data['lastUpdate']);
        }
        if (isset($data['published'])) {
            $this->_header->setPublishedDate($data['published']);
        }
        if (isset($data['description'])) {
            $this->_header->setDescription($data['description']);
        }
        if (isset($data['author'])) {
            $this->_header->setAuthor($data['author']);
        }
        if (isset($data['email'])) {
            $this->_header->setEmail($data['email']);
        }
        if (isset($data['webmaster'])) {
            $this->_header->setWebmaster($data['webmaster']);
        }
        if (isset($data['copyright'])) {
            $this->_header->setCopyright($data['copyright']);
        }
        if (isset($data['image'])) {
            $this->_header->setImage($data['image']);
        }
        if (isset($data['generator'])) {
            $this->_header->setGenerator($data['generator']);
        }
   		if (isset($data['atom'])) {
            $this->_header->setAtom($data['atom']);
        }
        if (isset($data['language'])) {
            $this->_header->setLanguage($data['language']);
        }
        if (isset($data['ttl'])) {
            $this->_header->setTtl($data['ttl']);
        }
        if (isset($data['rating'])) {
            $this->_header->setRating($data['rating']);
        }
        if (isset($data['cloud'])) {
            $mandatories = array('domain', 'path', 'registerProcedure', 'protocol');
            foreach ($mandatories as $mandatory) {
                if (!isset($data['cloud'][$mandatory])) {
                    /**
                     * @see Zend_Feed_Builder_Exception
                     */
                    //require_once'Zend/Feed/Builder/Exception.php';
                    throw new Zend_Feed_Builder_Exception("you have to define $mandatory property of your cloud");
                }
            }
            $uri_str = 'http://' . $data['cloud']['domain'] . $data['cloud']['path'];
            $this->_header->setCloud($uri_str, $data['cloud']['registerProcedure'], $data['cloud']['protocol']);
        }
        if (isset($data['textInput'])) {
            $mandatories = array('title', 'description', 'name', 'link');
            foreach ($mandatories as $mandatory) {
                if (!isset($data['textInput'][$mandatory])) {
                    /**
                     * @see Zend_Feed_Builder_Exception
                     */
                    //require_once'Zend/Feed/Builder/Exception.php';
                    throw new Zend_Feed_Builder_Exception("you have to define $mandatory property of your textInput");
                }
            }
            $this->_header->setTextInput($data['textInput']['title'],
                                         $data['textInput']['description'],
                                         $data['textInput']['name'],
                                         $data['textInput']['link']);
        }
        if (isset($data['skipHours'])) {
            $this->_header->setSkipHours($data['skipHours']);
        }
        if (isset($data['skipDays'])) {
            $this->_header->setSkipDays($data['skipDays']);
        }
        if (isset($data['itunes'])) {
            $itunes = new Zend_Feed_Builder_Header_Itunes($data['itunes']['category']);
            if (isset($data['itunes']['author'])) {
                $itunes->setAuthor($data['itunes']['author']);
            }
            if (isset($data['itunes']['owner'])) {
                $name = isset($data['itunes']['owner']['name']) ? $data['itunes']['owner']['name'] : '';
                $email = isset($data['itunes']['owner']['email']) ? $data['itunes']['owner']['email'] : '';
                $itunes->setOwner($name, $email);
            }
            if (isset($data['itunes']['image'])) {
                $itunes->setImage($data['itunes']['image']);
            }
            if (isset($data['itunes']['subtitle'])) {
                $itunes->setSubtitle($data['itunes']['subtitle']);
            }
            if (isset($data['itunes']['summary'])) {
                $itunes->setSummary($data['itunes']['summary']);
            }
            if (isset($data['itunes']['block'])) {
                $itunes->setBlock($data['itunes']['block']);
            }
            if (isset($data['itunes']['explicit'])) {
                $itunes->setExplicit($data['itunes']['explicit']);
            }
            if (isset($data['itunes']['keywords'])) {
                $itunes->setKeywords($data['itunes']['keywords']);
            }
            if (isset($data['itunes']['new-feed-url'])) {
                $itunes->setNewFeedUrl($data['itunes']['new-feed-url']);
            }

            $this->_header->setITunes($itunes);
        }
    }
    
    /**
     * Create the array of article entries
     *
     * @param  array $data
     * @throws Zend_Feed_Builder_Exception
     * @return void
     */
    protected function _createEntries(array $data)
    {
        foreach ($data as $row) {
            $mandatories = array('title', 'link', 'description');
            foreach ($mandatories as $mandatory) {
                if (!isset($row[$mandatory])) {
                    /**
                     * @see Zend_Feed_Builder_Exception
                     */
                    //require_once'Zend/Feed/Builder/Exception.php';
                    throw new Zend_Feed_Builder_Exception("$mandatory key is missing");
                }
            }
            $entry = new ArOn_Zend_Feed_Builder_Entry($row['title'], $row['link'], $row['description']);
            if (isset($row['author'])) {
                $entry->setAuthor($row['author']);
            }
            if (isset($row['guid'])) {
                $entry->setId($row['guid']);
            }
        	if (isset($row['text'])) {
                $entry->setText($row['text']);
            }
            if (isset($row['content'])) {
                $entry->setContent($row['content']);
            }
            if (isset($row['lastUpdate'])) {
                $entry->setLastUpdate($row['lastUpdate']);
            }
            if (isset($row['comments'])) {
                $entry->setCommentsUrl($row['comments']);
            }
            if (isset($row['commentRss'])) {
                $entry->setCommentsRssUrl($row['commentRss']);
            }
            if (isset($row['source'])) {
                $mandatories = array('title', 'url');
                foreach ($mandatories as $mandatory) {
                    if (!isset($row['source'][$mandatory])) {
                        /**
                         * @see Zend_Feed_Builder_Exception
                         */
                        //require_once'Zend/Feed/Builder/Exception.php';
                        throw new Zend_Feed_Builder_Exception("$mandatory key of source property is missing");
                    }
                }
                $entry->setSource($row['source']['title'], $row['source']['url']);
            }
            if (isset($row['category'])) {
                $entry->setCategories($row['category']);
            }
            if (isset($row['enclosure'])) {
                $entry->setEnclosures($row['enclosure']);
            }

            $this->_entries[] = $entry;
        }
    }
}