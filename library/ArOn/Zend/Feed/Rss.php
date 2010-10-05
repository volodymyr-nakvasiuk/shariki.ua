<?php
class ArOn_Zend_Feed_Rss extends Zend_Feed_Rss
{    
	
	protected $_setAtom = false;
	
	/**
     * Generate the header of the feed when working in write mode
     *
     * @param  array $array the data to use
     * @return DOMElement root node
     */
    protected function _mapFeedHeaders($array)
    {
        $channel = $this->_element->createElement('channel');

        $title = $this->_element->createElement('title');
        $title->appendChild($this->_element->createCDATASection($array->title));
        $channel->appendChild($title);

        $link = $this->_element->createElement('link', $array->link);
        $channel->appendChild($link);

        $desc = isset($array->description) ? $array->description : '';
        $description = $this->_element->createElement('description');
        $description->appendChild($this->_element->createCDATASection($desc));
        $channel->appendChild($description);
		
        if (isset($array->lastUpdate)){
        	$pubdate = ($array->lastUpdate === true) ?  time() : $array->lastUpdate;
        	$pubdate = $this->_element->createElement('pubDate', date('r', $pubdate));
        	$channel->appendChild($pubdate);
        }
        
    	if (isset($array->published)){
        	$lastBuildDate = ($array->published === true) ?  time() : $array->published;
        	$lastBuildDate = $this->_element->createElement('lastBuildDate', date('r', $lastBuildDate));
        	$channel->appendChild($lastBuildDate);
        }
        
        $editor = '';
        if (!empty($array->email)) {
            $editor .= $array->email;
        }
        if (!empty($array->author)) {
            $editor .= ' (' . $array->author . ')';
        }
        if (!empty($editor)) {
            $author = $this->_element->createElement('managingEditor', ltrim($editor));
            $channel->appendChild($author);
        }
        if (isset($array->webmaster)) {
            $channel->appendChild($this->_element->createElement('webMaster', $array->webmaster));
        }

        if (!empty($array->copyright)) {
            $copyright = $this->_element->createElement('copyright', $array->copyright);
            $channel->appendChild($copyright);
        }

        if (isset($array->category)) {
            $category = $this->_element->createElement('category', $array->category);
            $channel->appendChild($category);
        }

        if (!empty($array->image)) {
            $image = $this->_element->createElement('image');
            $url = $this->_element->createElement('url', $array->image);
            $image->appendChild($url);
            $imagetitle = $this->_element->createElement('title');
            $imagetitle->appendChild($this->_element->createCDATASection($array->title));
            $image->appendChild($imagetitle);
            $imagelink = $this->_element->createElement('link', $array->link);
            $image->appendChild($imagelink);

            $channel->appendChild($image);
        }

        $generator = !empty($array->generator) ? $array->generator : 'Zend_Feed';
        $generator = $this->_element->createElement('generator', $generator);
        $channel->appendChild($generator);

        if (!empty($array->atom)) {
        	$this->_setAtom = true;
        	foreach ($array->atom as $name=>$atom){ 
            	$node = $this->_element->createElement('atom:'.$name);
            	foreach ($atom as $attrName=>$attrValue){
            		$node->setAttribute($attrName, $attrValue);
            	}
            	$channel->appendChild($node);
        	}
        }
        
    	if (!empty($array->language)) {
            $language = $this->_element->createElement('language', $array->language);
            $channel->appendChild($language);
        }

        $doc = $this->_element->createElement('docs', 'http://blogs.law.harvard.edu/tech/rss');
        $channel->appendChild($doc);

        if (isset($array->cloud)) {
            $cloud = $this->_element->createElement('cloud');
            $cloud->setAttribute('domain', $array->cloud['uri']->getHost());
            $cloud->setAttribute('port', $array->cloud['uri']->getPort());
            $cloud->setAttribute('path', $array->cloud['uri']->getPath());
            $cloud->setAttribute('registerProcedure', $array->cloud['procedure']);
            $cloud->setAttribute('protocol', $array->cloud['protocol']);
            $channel->appendChild($cloud);
        }

        if (isset($array->ttl)) {
            $ttl = $this->_element->createElement('ttl', $array->ttl);
            $channel->appendChild($ttl);
        }

        if (isset($array->rating)) {
            $rating = $this->_element->createElement('rating', $array->rating);
            $channel->appendChild($rating);
        }

        if (isset($array->textInput)) {
            $textinput = $this->_element->createElement('textInput');
            $textinput->appendChild($this->_element->createElement('title', $array->textInput['title']));
            $textinput->appendChild($this->_element->createElement('description', $array->textInput['description']));
            $textinput->appendChild($this->_element->createElement('name', $array->textInput['name']));
            $textinput->appendChild($this->_element->createElement('link', $array->textInput['link']));
            $channel->appendChild($textinput);
        }

        if (isset($array->skipHours)) {
            $skipHours = $this->_element->createElement('skipHours');
            foreach ($array->skipHours as $hour) {
                $skipHours->appendChild($this->_element->createElement('hour', $hour));
            }
            $channel->appendChild($skipHours);
        }

        if (isset($array->skipDays)) {
            $skipDays = $this->_element->createElement('skipDays');
            foreach ($array->skipDays as $day) {
                $skipDays->appendChild($this->_element->createElement('day', $day));
            }
            $channel->appendChild($skipDays);
        }

        if (isset($array->itunes)) {
            $this->_buildiTunes($channel, $array);
        }

        return $channel;
    }
	
    /**
     * Generate the entries of the feed when working in write mode
     *
     * The following nodes are constructed for each feed entry
     * <item>
     *    <title>entry title</title>
     *    <link>url to feed entry</link>
     *    <guid>url to feed entry</guid>
     *    <description>short text</description>
     *    <content:encoded>long version, can contain html</content:encoded>
     * </item>
     *
     * @param  DOMElement $root the root node to use
     * @param  array $array the data to use
     * @return void
     */
    protected function _mapFeedEntries(DOMElement $root, $array)
    {
        Zend_Feed::registerNamespace('content', 'http://purl.org/rss/1.0/modules/content/');

        foreach ($array as $dataentry) {
            $item = $this->_element->createElement('item');

            $title = $this->_element->createElement('title');
            $title->appendChild($this->_element->createCDATASection($dataentry->title));
            $item->appendChild($title);

            if (isset($dataentry->author)) {
                $author = $this->_element->createElement('author', $dataentry->author);
                $item->appendChild($author);
            }

            $link = $this->_element->createElement('link', $dataentry->link);
            $item->appendChild($link);

            if (isset($dataentry->guid)) {
                $guid = $this->_element->createElement('guid', $dataentry->guid['value']);
                if ($dataentry->guid['isPermaLink']){
                	$isPermaLink="true";
                }
                else {
                	$isPermaLink="false";
                }
                $guid->setAttribute('isPermaLink', $isPermaLink );
                $item->appendChild($guid);
            }

            $description = $this->_element->createElement('description');
            $description->appendChild($this->_element->createCDATASection($dataentry->description));
            $item->appendChild($description);
			
	        if (isset($dataentry->text)) {
		        $text = $this->_element->createElement('full-text');
		        $text->appendChild($this->_element->createCDATASection(ArOn_Crud_Tools_String::realStripTags($dataentry->text)));
		        $item->appendChild($text);
	        }	        
            
            if (isset($dataentry->content)) {
                $content = $this->_element->createElement('content:encoded');
                $content->appendChild($this->_element->createCDATASection($dataentry->content));
                $item->appendChild($content);
            }

            $pubdate = isset($dataentry->lastUpdate) ? $dataentry->lastUpdate : time();
            $pubdate = $this->_element->createElement('pubDate', gmdate('r', $pubdate));
            $item->appendChild($pubdate);

            if (isset($dataentry->category)) {
                foreach ($dataentry->category as $category) {
                    $node = $this->_element->createElement('category', $category['term']);
                    if (isset($category['scheme'])) {
                        $node->setAttribute('domain', $category['scheme']);
                    }
                    $item->appendChild($node);
                }
            }

            if (isset($dataentry->source)) {
                $source = $this->_element->createElement('source', $dataentry->source['title']);
                $source->setAttribute('url', $dataentry->source['url']);
                $item->appendChild($source);
            }

            if (isset($dataentry->comments)) {
                $comments = $this->_element->createElement('comments', $dataentry->comments);
                $item->appendChild($comments);
            }
            if (isset($dataentry->commentRss)) {
                $comments = $this->_element->createElementNS('http://wellformedweb.org/CommentAPI/',
                                                             'wfw:commentRss',
                                                             $dataentry->commentRss);
                $item->appendChild($comments);
            }


            if (isset($dataentry->enclosure)) {
                foreach ($dataentry->enclosure as $enclosure) {
                    $node = $this->_element->createElement('enclosure');
                    $node->setAttribute('url', $enclosure['url']);
                    if (isset($enclosure['type'])) {
                        $node->setAttribute('type', $enclosure['type']);
                    }
                    if (isset($enclosure['length'])) {
                        $node->setAttribute('length', $enclosure['length']);
                    }
                    $item->appendChild($node);
                }
            }

            $root->appendChild($item);
        }
    }
	
 	/**
     * Override Zend_Feed_Element to include <rss> root node
     *
     * @return string
     */
    public function saveXml()
    {
        // Return a complete document including XML prologue.
        $doc = new DOMDocument($this->_element->ownerDocument->version,
                               $this->_element->ownerDocument->actualEncoding);
        $root = $doc->createElement('rss');

        // Use rss version 2.0
        $root->setAttribute('version', '2.0');

        // Content namespace
        if ($this->_setAtom){
        	$root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:atom', 'http://www.w3.org/2005/Atom');
        }
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:dc', 'http://purl.org/dc/elements/1.1/');
        $root->setAttributeNS('http://www.w3.org/2000/xmlns/', 'xmlns:content', 'http://purl.org/rss/1.0/modules/content/');
        
        $root->appendChild($doc->importNode($this->_element, true));

        // Append root node
        $doc->appendChild($root);

        // Format output
        $doc->formatOutput = true;

        return $doc->saveXML();
    }
}
