<?php



/**
 * Skeleton subclass for representing a row from the 'kryn_system_page' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.kryn
 */
class Page extends BasePage {

    /**
     * Contains the full path to the url (without domain path and domain name)
     * @var string
     */
    private $fullUrl;

    /**
     * @param string $pFullUrl
     * return \Page $this
     */
    public function setFullUrl($pFullUrl){
        $this->fullUrl = $pFullUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getFullUrl(){
        return $this->fullUrl;
    }


    public function isActive(){

        if( $this->getId() == \Core\Kryn::$page->getId() ) return true;

        $url = \Core\Kryn::pageUrl( \Core\Kryn::$page->getId(), false, true );
        $purl = \Core\Kryn::pageUrl( $this->getId(), false, true );

        $pos = strpos( $url, $purl );
        if( $url == '/' || $pos != 0  || $pos === false){
            return false;
        } else {
            return true;
        }
    }


} // SystemPage
