<?php
/**
* This class provides some functionality for
* retrieving page stats from Google, Yandex, Alexa, Yahoo!
* 
* @package Fooup
* @subpackage Rating
* @name PageStats
* @version 0.0.0.1
* @copyright (C) 2009-2010 illusive (http://web-blog.org.ua/)
*/
class ArOn_Crud_Tools_RatingPage
{
        
    protected $hosts = array(
    'www.google.com',
    'toolbarqueries.google.com',
    'toolbarqueries.l.google.com'
    );
    
    /**
     * Connects to the specified host and extracts PR from the response
     *
     * This function gets a checksum for an URL, attempts a connection to the specified Google host, 
     * searches the response for PageRank, and returns said result. 
     *
     * @param       string $url     URL of the domain to check
     * @param       string $host    IP or domain name of the Google host to query
     * @return      integer, returns -1 if no page rank was found
    */
    public function getGooglePR($url, $host=null) {
        //by default
        $pagerank = -1;
        
        if(is_null($host)){ // random host
            $hostNumber=rand(0, 2);
            $host=$this->hosts[$hostNumber];
        }
        
        // Open domain socket connection to specified host
        $fp = fsockopen($host, 80, $errno, $errstr, 3);
                
        if($fp) {
            // Get URL checksum
            $hash = $this->_getHash($url);
            $ch = $this->_getCh($hash);

            // Build the domain info request
            $out = "GET /search?client=navclient-auto&ch=" . $ch .  "&features=Rank&q=info:" . $url . " HTTP/1.1\r\n" ;
            $out .= "Host: " . $host . "\r\n";
            $out .= "Connection: Close\r\n\r\n";
            
            // Write the request to our connection 
            fwrite($fp, $out);
            
            while (!feof($fp)) {
                // Check the response for a ranking
                $data = fgets($fp, 128);
                $pos = strpos($data, "Rank_");
                if($pos !== false) {
                    // Get the rank from our response and return
                    $pagerank = intval(substr($data, $pos + 9));
                    return $pagerank;
                }
            }
            
            // Close the connection
            fclose($fp);
            
            return $pagerank;
        } else return array($errno=>$errstr); // error
    }
    
    
    /**
    * Convert string to a 32-bit integer
    * 
    * @param string $string
    * @param int $check
    * @param hex $gmagic - Google's magic
    */
    protected function _strToInt($string, $check, $gmagic) {
        $integer32 = 4294967296;
    
        $length = strlen($string);
        for ($i = 0; $i < $length; $i++) {
            $check *= $gmagic;     
            if ($check >= $integer32) {
                $check = ($check - $integer32 * (int) ($check / $integer32));
                $check = ($check < -2147483648) ? ($check + $integer32) : $check;
            }
            $check += ord($string{$i}); 
        }
        
        return $check;
    }
    
    /**
    * Generate an URL hash to feed the checksum
    * 
    * @param string $url - URL for generating a hash
    * @return int
    */
    protected function _getHash($url) {
        $check1 = $this->_strToInt($url, 0x1505, 0x21);
        $check2 = $this->_strToInt($url, 0, 0x1003F);
    
        $check1 >>= 2;     
        $check1 = (($check1 >> 4) & 0x3FFFFC0 ) | ($check1 & 0x3F);
        $check1 = (($check1 >> 4) & 0x3FFC00 ) | ($check1 & 0x3FF);
        $check1 = (($check1 >> 4) & 0x3C000 ) | ($check1 & 0x3FFF);    
        
        $t1 = (((($check1 & 0x3C0) << 4) | ($check1 & 0x3C)) <<2 ) | ($check2 & 0xF0F );
        $t2 = (((($check1 & 0xFFFFC000) << 4) | ($check1 & 0x3C00)) << 0xA) | ($check2 & 0xF0F0000 );
        
        return ($t1 | $t2);
    }

    /**
    * Get a Google checksum for provided hash
    * 
    * @param $hash - URL hash
    * @return string
    */
    protected function _getCh($hash) {
        $checkByte = 0;
        $flag = 0;

        $string = sprintf('%u', $hash) ;
        $length = strlen($string);
        
        for ($i = $length - 1;  $i >= 0;  $i --) {
            $Re = $string{$i};
            if (1 === ($flag % 2)) {              
                $Re += $Re;     
                $Re = (int)($Re / 10) + ($Re % 10);
            }
            $checkByte += $Re;
            $flag ++;    
        }

        $checkByte %= 10;
        if (0 !== $checkByte) {
            $checkByte = 10 - $checkByte;
            if (1 === ($flag % 2) ) {
                if (1 === ($checkByte % 2)) {
                    $checkByte += 9;
                }
                $checkByte >>= 1;
            }
        }

        return '7'.$checkByte.$string;
    }
    
    /**
    * Get Google Back Links for specified url
    * 
    * @param string $url
    * @return integer
    */
    public function getGoogleBL($url) {
        $data="";
        $fp = fsockopen("www.google.com", 80, $errno, $errstr, 5);
        if ($fp) {
            $out = "GET /search?hl=en&lr=&ie=UTF-8&q=link%3A".$url." HTTP/1.1\r\n" ;
            $out .= "Host: www.google.com\r\n" ;
            $out .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7\r\n";
            $out .= "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*\/*;q=0.5\r\n";
            $out .= "Accept-Language: en-us,en;q=0.5\r\n";
            $out .= "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n";
            $out .= "Connection: Close\r\n\r\n" ;
            fwrite($fp, $out);
            while (!feof($fp)) $data .= fgets($fp, 4096);
            fclose($fp);
        } else return array($errno=>$errstr); // error
            
        if($data) {
            if(preg_match("#of(?: about)? <b>([^<]+)</b> linking to#im", $data, $match)) return $match[1];
            else return null;
        } return null;
    }
    
    /**
    * Get Yahoo's Back Links for specified url
    * 
    * @param string $url
    * @return integer 
    */
    public function getYahooBL($url) {
        $data="";
        $fp = fsockopen("siteexplorer.search.yahoo.com", 80, $errno, $errstr, 5);
            if ($fp) {
                $out = "GET /search?ei=UTF-8&p=".$url."&bwm=i&bwmf=s HTTP/1.1\r\n" ;
                $out .= "Host: siteexplorer.search.yahoo.com\r\n" ;
                $out .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7\r\n";
                $out .= "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*\/*;q=0.5\r\n";
                $out .= "Accept-Language: en-us,en;q=0.5\r\n";
                $out .= "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n";
                $out .= "Connection: Close\r\n\r\n" ;
                fwrite($fp, $out);
                while (!feof($fp)) $data .= fgets($fp, 4096);
                fclose($fp);
            } else return array($errno=>$errstr);
        if($data) {
            if(preg_match("|>Inlinks \(([^\)]*?)\)<|im", $data, $match)) return $match[1];
            else return null;
        } else return null;
    }
    
    /**
    * Get Alexa Rang
    * 
    * @param string $url
    * @return integer
    */
    public function getAlexaRang($url) {
        $page="";
        $fp = fsockopen("data.alexa.com", 80, $errno, $errstr, 5);
        if ($fp) {
            $out = "GET /data?cli=10&dat=snbamz&url=".$url." HTTP/1.1\r\n" ;
            $out .= "Host: data.alexa.com\r\n" ;
            $out .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7\r\n";
            $out .= "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*\/*;q=0.5\r\n";
            $out .= "Accept-Language: en-us,en;q=0.5\r\n";
            $out .= "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n";
            $out .= "Connection: Close\r\n\r\n" ;
            fwrite($fp, $out);
            while (!feof($fp)) 
            { 
               $page .= fgets($fp, 4096);
             }
            fclose($fp);
        } else return array($errno=>$errstr);
        
        // parse a page and retrieve a page rang
        if (preg_match('/<POPULARITY URL=".+?" TEXT="(\d+)"\/>/is', $page, $ar)) return $ar[1]; 
        else return null;
    }
    
    /**
    * Retrieve Yandex "ТИЦ" for specified URL
    * 
    * @param string $url
    * @return integer
    */
    public function getYandexRang($url){
        if (!preg_match('/^(http:\/\/)(.*)/i', $url)) $url='http://'.$url;
        
        $content = file_get_contents('http://bar-navig.yandex.ru/u?ver=2&url='.$url.'&target=_No__Name:5&show=1&thc=0');
        
        $create = xml_parser_create();
        xml_parse_into_struct($create, $content, $array);
        xml_parser_free($create);
        
        return isset($array[3]['attributes']['VALUE'])?@$array[3]['attributes']['VALUE']:null;
    }

    /**
    * Is specified url exists in Google's Cache?
    * 
    * @param string $url
    * @return bool
    */
    public function isInGoogleCache($url){
        $data=array();
        $url = rawurlencode($url);// encode url
        $googleDataCenter=array("72.14.203.104");
        $cachedPage="";
        $out = "GET /search?sourceid=navclient-ff&ie=UTF-8&q=cache:".$url." HTTP/1.1\r\n"; 
        $out .= "Host: ".$googleDataCenter[0]."\r\n"; 
        $out .= "User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7\r\n";
        $out .= "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*\/*;q=0.5\r\n";
        $out .= "Accept-Language: en-us,en;q=0.5\r\n";
        $out .= "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7\r\n";
        $out .= "Connection: close\r\n";
        $out .= "Cache-Control: max-age=0\r\n\r\n"; 
        $fp = fsockopen($googleDataCenter[0], 80, $errno, $errstr, 10);  
        if (!$fp) { 
            return array($errno=>$errstr);    
        } else { 
            fwrite($fp, $out); 
            while (!feof($fp)) $cachedPage .= fgets($fp, 4096);
        }
        fclose($fp);

        // if page exists in the Google's cache
        if(preg_match("/on ([^\.]*?)\./is", $cachedPage, $matches)) {
            $data[0] = $PageIsCached = 1; // exists in cache
            $data[1] = $PageCachedTime=$matches[1];// Last cache date in the format 20 Oct 2009 20:35:47 GMT
        } else{
            $data[0] = $PageIsCached = 0;// do not exists in the cache
        }
        
        return ($data[0] == true); // returns true/false
    }
    
    public function countOfOutboundLinks($url){
        $domain = parse_url($url);
        if (array_key_exists('host',$domain))$domain = $domain['host'];
        else {
            $domain = $url;
            $url = 'http://'.$url.'/';
        }
        
        $page = @file_get_contents($url);
        $outboundLinks=array();
        
        preg_match_all("|<\s*a[^>]*href\s*=([^>]+)>|Us", $page, $matches, PREG_SET_ORDER);
        foreach ($matches as $value) {
            $repl[0]='|^"|i';
            $repl[1]='|"$|i';
            $repl[2]='|^\'|i';
            $repl[3]='|\'$|i';
            $value[1]=trim($value[1]);
            $value[1]=trim(preg_replace($repl, "", $value[1]));
            //if the link starts with http or https
            if(preg_match("|^https?://|i", $value[1])){
                //if the link contans the hostname
                if(!preg_match("|^https?://(www\.)?".$domain."|iU", $value[1])) $outboundLinks[]=$value[1];
            }
        }
        
        return count($outboundLinks);//number of outboundlinks
    }
}