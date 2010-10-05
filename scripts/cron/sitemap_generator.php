<?php
ini_set('memory_limit','800M');
include 'zend_cron_init.php';
$time_start = microtime(true);

$basePathFiles = scandir(DOCUMENT_ROOT);
foreach ($basePathFiles as $file){
	$pathinfo = pathinfo($file);
	if ($pathinfo['extension']=='gz' && strpos($file, '.xml.gz')!==false) unlink(DOCUMENT_ROOT.'/'.$file);
}
$k=count($xmlFiles);

$sitemap_struture = array();
$staticPages = Db_SiteSitemapStatic::getInstance();
$staticPages->where = "static_status = 1";
$sitemap_struture[] = array('table' => $staticPages,
							'loc' => '[static_loc]',
							'lastmod' => date('c'),
							'changefreq' => '[static_changefreq]',
							'priority' => '[static_priority]'
					);
$sitemap_struture[] = array('table' => 'Db_Emark',
							'loc' => '{{HOST_NAME}}/catalog/{mark_link}/',
							'lastmod' => date('c'),
							'changefreq' => 'monthly',
							'priority' => '0.9'
					);
$sitemap_struture[] = array('grid' => 'Crud_Grid_Emodel',
							'loc' => '{{HOST_NAME}}/catalog/{mark_link}/{model_link}/',
							'lastmod' => date('c'),
							'changefreq' => 'monthly',
							'priority' => '0.9'
					);
$sitemap_struture[] = array('grid' => 'Crud_Grid_Cartest',
							'loc' => '{{HOST_NAME}}/catalog/{mark_link}/{model_link}/test/{tests_id}/',
							'lastmod' => date('c'),
							'changefreq' => 'monthly',
							'priority' => '0.7'
					);
$sitemap_struture[] = array('grid' => 'Crud_Grid_ArticleView',
							'loc' => '{{HOST_NAME}}/catalog/{mark_link}/{model_link}/mod/{article_id}/',
							'lastmod' => date('c'),
							'changefreq' => 'monthly',
							'priority' => '0.7'
					);								
$sitemap_struture[] = array('table' => 'Db_Mark',
							'loc' => '{{HOST_NAME}}/buy/old/{mark_link}/',
							'lastmod' => date('c'),
							'changefreq' => 'hourly',
							'priority' => '0.8'
					);
$sitemap_struture[] = array('grid' => 'Crud_Grid_ExtJs_Pmodel',
							'loc' => '{{HOST_NAME}}/buy/old/{mark_link}/{model_link}/',
							'lastmod' => date('c'),
							'changefreq' => 'hourly',
							'priority' => '0.7'
					);
$sitemap_struture[] = array('table' => 'Db_Mark',
							'loc' => '{{HOST_NAME}}/buy/new/{mark_link}/',
							'lastmod' => date('c'),
							'changefreq' => 'hourly',
							'priority' => '0.8'
					);
$sitemap_struture[] = array('grid' => 'Crud_Grid_ExtJs_Pmodel',
							'loc' => '{{HOST_NAME}}/buy/new/{mark_link}/{model_link}/',
							'lastmod' => date('c'),
							'changefreq' => 'hourly',
							'priority' => '0.7'
					);
$sitemap_struture[] = array('grid' => 'Crud_Grid_ExtJs_Nprice',
                            'loc' => '{{HOST_NAME}}/buy/new/{mark_link}/{model_link}/{price_id}/',
                            'lastmod' => date('c'),
                            'changefreq' => 'hourly',
                            'priority' => '0.7'
                    );
$sitemap_struture[] = array('grid' => 'Crud_Grid_ExtJs_SiteMapPrice',
                            'loc' => '{{HOST_NAME}}/buy/old/{mark_link}/{model_link}/{price_id}/',
                            'lastmod' => date('c'),
                            'changefreq' => 'hourly',
                            'priority' => '0.7'
                    );
foreach ($sitemap_struture as $sitemap_struture_one){
echo "Creating Site map for:<br/>/n/r";
print_r($sitemap_struture_one);
echo "/n/r";
$time = explode(" ",microtime());
$time = $time[1];
	
$sitemapBilder = new ArOn_Sitemap_Bilder(array($sitemap_struture_one));

//$time_end = microtime(true);
//$time = $time_end - $time_start;
//$urls = $sitemapBilder->getUrlValues();

        // create object
        $sitemap = new ArOn_Sitemap_Generator(HOST_NAME."/", DOCUMENT_ROOT);
        // will create also compressed (gzipped) sitemap
        $sitemap->createGZipFile = true;
        // determine how many urls should be put into one file
        $sitemap->maxURLsPerSitemap = 50000;
        // sitemap file name
        $sitemap->sitemapFileName = "sitemap.xml";
        // sitemap index file name
        //$sitemap->sitemapIndexFileName = "sitemap-index.xml";
        // robots file name
        $sitemap->robotsFileName = "robots.txt";

        //$urls = $sitemapBilder->getUrlValues();
        
        // add many URLs at one time
        $sitemap->addUrls($sitemapBilder->getUrlValues());
        
        unset($sitemapBilder);

        try {
            // create sitemap
            $sitemap->createSitemap(true);

            // write sitemap as file
            $sitemap->writeSitemap();

            // update robots.txt file
            $sitemap->updateRobots();

            // submit sitemaps to search engines
            //$result = $sitemap->submitSitemap("yahooAppId");
            // shows each search engine submitting status
            /*echo "<pre>";
            print_r($result);
            echo "</pre>";*/
            
        }
        catch (Exception $exc) {
            echo $exc->getTraceAsString();
        }
        
        unset($sitemap);

        echo "Memory peak usage: ".number_format(memory_get_peak_usage()/(1024*1024),2)."MB<br/>\n\r";
        $time2 = explode(" ",microtime());
        $time2 = $time2[1];
        echo "Execution time: ".number_format($time2-$time)."s<br/>\n\r<br/>\n\r";
}
?>