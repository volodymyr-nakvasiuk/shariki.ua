<?php
class ArOn_Zend_Feed_Yandex extends ArOn_Zend_Feed_Rss
{    

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
            $title->appendChild($this->_element->createCDATASection(trim(preg_replace('#\[.*?\]#is', '', $dataentry->title))));
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
                $text = $this->_element->createElement('yandex:full-text',ArOn_Crud_Tools_String::realStripTags($dataentry->text));
                $item->appendChild($text);
            }
	        
            if (isset($dataentry->content)) {
                $content = $this->_element->createElement('content:encoded');
                $content->appendChild($this->_element->createCDATASection($dataentry->content));
                $item->appendChild($content);
            }

            $pubdate = isset($dataentry->lastUpdate) ? $dataentry->lastUpdate : time();
            $pubdate = $this->_element->createElement('pubDate', date('r', $pubdate));
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

}
