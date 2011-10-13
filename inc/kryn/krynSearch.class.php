<?php

/**
 * krynSearch class
 * 
 * @internal
 * @author Kryn.labs <info@krynlabs.com>
 */


class krynSearch extends baseModule{
    static $indexedPages;
    static $indexedPagesName;
    static $indexBlacklistUrls;
    static $indexBlacklistUrlsName;
    public static $forceSearchIndex = false;
    
    public static $returnCodes = false;
    
    static $jsonOut = false; 
    
    static $jsonFoundPages = array();
    
    static $pageUrl;
    
    static $curDomain;
    
    public static $blacklistTimeout = 3600; //1h
    public static $minWaitTimeTillNextCrawl = 260; //for 'keep index up 2 date' adminSearchIndexer::getIndex();
    
    public static $redirectTo = '';
    
    
    public static $autoCrawlPermissionLifetime = 60; //sec
    
    public static function initSearch() {   
    	
        global $kdb;
        
        if(getArgv(1) == 'admin')
            return;
  
        
        if(isset($_REQUEST['jsonOut']))        
            self::$jsonOut = true;
            
        
        //indexing forced no matter if already indexed
        if(isset($_REQUEST['forceSearchIndex']) && $_REQUEST['forceSearchIndex']) {
            //force could only be enabled with correct search_index_key for this domain
            $validation = dbExFetch("SELECT rsn FROM %pfx%system_domains WHERE rsn = ".kryn::$domain['rsn']." AND search_index_key = '".esc($_REQUEST['forceSearchIndex'])."'", 1);
            if(!empty($validation) && $validation['rsn'] == kryn::$domain['rsn'] )
                self::$forceSearchIndex = $_REQUEST['forceSearchIndex'];
            
        }
              
       self::$curDomain = kryn::$domain['rsn'];
        
    }
    
    
    
    
    //create a new search index for this page
    public static function createPageIndex($pContent) {  
        global $cfg;
        
        if( getArgv(1) == 'admin' || kryn::$page['rsn']+0 == 0 ) return;
        
        self::$indexedPages =& kryn::getCache('systemSearchIndexedPages');
        $indexedPages =& self::$indexedPages;
        
        $indexedContent = self::stripContent( $pContent );
        $contentMd5 = md5($indexedContent);
        $hashkey = kryn::$page['rsn'].'_'.$contentMd5;
        
        $a = '/'.kryn::getRequestPageUrl(true);
        $b = $indexedPages[$hashkey]['url'];
        
        if( $indexedPages[$hashkey] && $b === "" )
            $b = '/';
        
        self::$pageUrl = $a;

        if( $indexedPages[$hashkey] && $indexedPages[$hashkey]['md5'] == $contentMd5 && $b == $a && self::$forceSearchIndex === false ){
    
            return self::exitPage('Page with this content is already indexed!', 3);
        }
        
        //check if we have additional arguments which doesnt change the content
        if( $indexedPages[$hashkey] && $indexedPages[$hashkey]['md5'] == $contentMd5 && strlen($b) < strlen($a)
            && self::$forceSearchIndex === false ){
        
            self::updateBlacklist( self::$pageUrl );
            self::$redirectTo = $b;
            return self::exitPage('Given arguments does not change the content!', 2);
                
        }
        
        //check if we are blacklistet
        
        if( $indexedPages[kryn::$domain['rsn'].'_'.$a] && $indexedPages[kryn::$domain['rsn'].'_'.$a]['blacklist']+0 > 0 ){
        
            if( time()-$indexedPages[kryn::$domain['rsn'].'_'.$a]['blacklist'] < self::$blacklistTimeout ){
            
                return self::exitPage('Page blacklisted', 8);
                
            } else if( time()-$indexedPages[kryn::$domain['rsn'].'_'.$a]['blacklist'] > self::$blacklistTimeout ){
                
                //blacklist is expired, remove from blacklistand index
                self::removeBlacklist( self::$pageUrl );
                
            }
        }
            
            
        if(kryn::$page['unsearchable']) {
            self::updateBlacklist( self::$pageUrl );
            //self::removePageFromIndexTable(self::$pageUrl);
            return self::exitPage('Page is flagged as unsearchable!', 5);
        }
        
        if( getArgv('kVersionId') || getArgv('kryn_framework_version_id') ){
            self::updateBlacklist( self::$pageUrl );
            return self::exitPage('Version indexing not allowed!', 6);
        }
        
        
            
        //check if content is empty
        if(strlen(trim($indexedContent)) < 1) {
            self::updateBlacklist( self::$pageUrl );
            return self::exitPage('No content found!. Site was not indexed!', 7);
        }
        
        
        //we now ready to index this content
        
        dbDelete('system_search', " url='".esc(self::$pageUrl)."' AND domain_rsn = '".kryn::$domain['rsn']."'");
        dbInsert('system_search', array(
            'url' => self::$pageUrl,
            'title' => kryn::$page['title'],
            'md5' => $contentMd5,
            'mdate' => time(),
            'page_rsn' => kryn::$page['rsn'],
            'domain_rsn' => kryn::$domain['rsn'],
            'page_content' => $indexedContent
        ));  
        
        self::getLinksInContent($pContent);
        self::cacheAllIndexedPages();
        return self::exitPage('Indexing successfully completed!', 1);             
       
    }
    
    public static function updateBlacklist( $pUrl, $pDomainRsn = false ){
        global $kcache;
    
        if(!$pDomainRsn)
            $pDomainRsn = kryn::$domain['rsn'];
    
        $url = esc( $pUrl );
        
        if( !self::$indexedPages[$pDomainRsn.'_'.$pUrl]['blacklist'] ||
            self::$indexedPages[$pDomainRsn.'_'.$pUrl]['blacklist'] == 0 ){
            
            dbUpdate('system_search', "url = '$url' AND domain_rsn = $pDomainRsn", array(
                'blacklist' => time()
            ));
        
        } 
        self::$indexedPages[$pDomainRsn.'_'.$pUrl]['blacklist'] = time();
        kryn::setCache('systemSearchIndexedPages', self::$indexedPages);
    }
    
    public static function removeBlacklist( $pUrl, $pDomainRsn = false ){
        global $kcache;
    
        if(!$pDomainRsn)
            $pDomainRsn = kryn::$domain['rsn'];
    
        $url = esc( $pUrl );
        dbUpdate('system_search', "url = '$url' AND domain_rsn = $pDomainRsn", array(
            'blacklist' => 0
        ));
        self::$indexedPages[$pDomainRsn.'_'.$pUrl]['blacklist'] = 0;
        kryn::setCache('systemSearchIndexedPages', self::$indexedPages);
    }
    
    public static function toBlacklist(){
        self::updateBlacklist( kryn::$pageUrl );
    }
    
    public static function stripContent( $pContent ){
        
        $arSearch = array('@<script[^>]*>.*</script>@Uis',  // javascript
                       '@<style[^>]*>.*</style>@Uis',    //  style tags
                       '@<\!--unsearchable-begin-->.*<\!--unsearchable-end-->@Uis', //unsearchable html comment
                       '@<!--.*-->@Uis',         // comments
                       '@style="(.*)"@Uis',                   // css inline styling
                       '@class="(.*)"@Uis',                   //css class
                       '@id="(.*)"@Uis',
                      
        );
        $pContent = preg_replace($arSearch, '', $pContent);
        return kryn::compress(strip_tags($pContent, '<p><br><br /><h1><h2><h3><h4><h5><h6>'));
    }
    
    
    public static function cacheAllIndexedPages(){
        $res = dbExec('SELECT url, page_rsn, domain_rsn, md5, blacklist FROM %pfx%system_search');
        $cache = array();
        while( $row = dbFetch($res) ){
            $cache[$row['page_rsn'].'_'.$row['md5']] = $row;           
            $cache[$row['domain_rsn'].'_'.$row['url']] = array('blacklist' => $row['blacklist']);
        }
        self::$indexedPages = $cache;
        kryn::setCache('systemSearchIndexedPages', $cache);
    }
    
    //search for links in parsed html content
    public static function getLinksInContent($pContent) {
        global $cfg;
        
        kryn::replacePageIds($pContent);       
        $searchPattern = '#<a[^>]+href[^>]*=[^>]*\"([^\"]+)\"[^>]*>(.*)<\/a>#Uis';          
        preg_match_all($searchPattern, $pContent, $matches, PREG_SET_ORDER);
        
        $arInserted = array();
        foreach($matches as $value) {
                      
            $linkBackup = $value[1];
            $value[1] = strtolower($value[1]); 
            //check if link is valid
            //kick all anchors, javascript btns, admin and downloadcenter links
            if(strlen($value[1]) < 2 || strpos($value[1], '.') !== false || strpos($value[1], '#') !== false || strpos($value[1], 'mailto:') !== false || strpos($value[1], 'action_select') !== false
                || strpos($value[1], 'javascript:') === 0 || strpos($value[1], 'downloadfile') !== false
                || strpos($value[1], '/admin') === 0 || strpos($value[1], 'admin') === 0 || strpos($value[1], 'users-logout:') !== false
                || (strpos($value[1], 'http://'.kryn::$domain['domain']) === false && (strpos($value[1], 'http://') === 0) || strpos($value[1], 'https://') === 0)
                || strpos($value[1], 'user:logout') !== false
            )
                continue;
            

           
                
                
           //restore case-sensitivity     
           $value[1] = $linkBackup;
           
           if( strpos($value[1], kryn::$domain['path']) === 0 ){
               $value[1] = substr($value[1], strlen(kryn::$domain['path']));
           }
           
           if( $value[1] == '' )
               $value[1] = '/';
           
           //add slash     
           if(strpos($value[1], 'http://') !== 0 && strpos($value[1], 'https://') !== 0 && strpos($value[1], '/') !== 0)
                $value[1] = '/'.$value[1];
                
           //remove last slash 
           if(strrpos($value[1], '/') == strlen($value[1])-1)
                $value[1] = substr($value[1], 0, strlen($value[1])-1);
            
           //if absolute link transform to relative
           if(strpos($value[1], 'http://') === 0 || strpos($value[1], 'https://') === 0) {              
               $value[1] = substr($value[1], stripos($value[1], kryn::$domain['domain'].$cfg['path'])+strlen(kryn::$domain['domain'].$cfg['path'])-1);
           }     
        
           $value[1] = str_replace('//', '/', $value[1]);
           $value[1] = str_replace('//', '/', $value[1]);
        
           if( substr($value[1], -1) == '/' )
               $value[1] = substr($value[1], 0, -1);
            
            
            
           
           if( count(self::$indexedPages) > 1 && !isset($arInserted[kryn::$domain['rsn'].'_'.$value[1]]) &&!isset(self::$indexedPages[kryn::$domain['rsn'].'_'.$value[1]]) && !isset($arInserted[kryn::$domain['rsn'].'_'.$value[1]]) && strlen($value[1]) > 0 ){
+               $arInserted[kryn::$domain['rsn'].'_'.$value[1]] = true;
           	    self::disposePageForIndex($value[1], 'LINK '.esc($value[1]), kryn::$domain['rsn']);
                self::$jsonFoundPages[] = $value[1];
           }
        }        
      
    }   
    

    
    public static function getSearchIndexOverview($pPageRsn) {
        $indexes = dbExFetch("SELECT url, title , mdate, md5 FROM %pfx%system_search WHERE page_rsn =".esc($pPageRsn)." AND mdate > 0 ORDER BY url, mdate DESC", -1);
        $arIndexes = array();
        foreach($indexes as $page) {
            $arIndexes[] = array($page['url'], $page['title'], date('d.m.Y H:i', $page['mdate']), $page['md5']);
        }
        
        return $arIndexes;
    }
    
    
    //insert a page into the searchtable for further indexing
    public static function disposePageForIndex($pUrl, $pTitle, $pDomainRsn, $pPageRsn='0') {
        global $cfg;
        
        $url = esc($pUrl);
        $pPageRsn += 0;
  #      dbDelete('system_search', "domain_rsn = $pDomainRsn AND url = '$url'");
        return dbInsert('system_search', array(
            'url' => $pUrl,
            'title' => $pTitle,
            'mdate' => 0,
            'domain_rsn' => $pDomainRsn+0,
            'page_rsn' => $pPageRsn
        ));
    }
       
    //clear complete search index
    public static function clearSearchIndex() {
        //cache files first
        $arOldFiles = glob('inc/cache/indexedPages_*');
        if(!empty($arOldFiles)) {
            foreach( $arOldFiles as $file ) {
                unlink($file);    
            }
        }

        dbExec("DELETE FROM %pfx%system_search");
        kryn::removeCache('systemSearchIndexedPages');
        return array('state' => true);
        
    }
    
    
    //void page output while in searchmode
    public static function exitPage($pMsg, $pCode = false) {
        if( self::$returnCodes == true )
            return $pCode;
        
        if(self::$jsonOut) {
    		$hasPermission = false;
    		if( getArgv('crawlerId') ){
    			include_once('inc/modules/admin/adminSearchIndexer.class.php');
                json(array(
                    'msg' => $pMsg,
                    'foundPages' => self::$jsonFoundPages,
                    'access' => adminSearchIndexer::hasPermission()
                ));
    		}
        }else{
            @ob_end_clean();
            header("HTTP/1.0 404 Not Found");
            exit();
        }
            
    }

}

?>
