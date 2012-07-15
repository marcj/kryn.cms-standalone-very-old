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
     * Updates the full url
     */
    public function updateFullUrl(){
        if ($this->getId() && $this->getUrl() && $this->getDomainId()){

            $url = '';
            $cachedUrls =& \Core\Kryn::getCache('systemUrls-' . $this->getDomainId());
            if (!($url = $cachedUrls['id']['id=' . $this->getId()]) || !$cachedUrls || !$cachedUrls['id']) {
                $cachedUrls = adminPages::updateUrlCache($this->getDomainId());
            }

            if ($this->getDomainId() != \Core\Kryn::$domain->getId()){
                if ($this->getDomainId() != \Core\Kryn::$domain->getId())
                    $domain = $this->getDomain();
                else
                    $domain = \Core\Kryn::$domain;

                $domainName = $domain->getRealDomain();
                if ($domain->getMaster() != 1) {
                    $url = $domainName . $domain->getPath() . $domain->getLang() . '/' . $url;
                } else {
                    $url = $domainName . $domain->getPath() . $url;
                }

                $url = 'http' . (\Core\Kryn::$ssl ? 's' : '') . '://' . $url;
            }

            if (substr($url, -1) == '/')
                $url = substr($url, 0, -1);

            if ($url == '/')
                $url = '.';

            if ($url == '/')
                $url = '.';

            $this->fullUrl = $url;
        } else {
            $this->fullUrl = false;
        }
    }

    public function preSave(PropelPDO $con = null){

        $url = '';
        $parent = $this;
        while ($parent->getParentId() > 0 && $parent = $parent->getParent()){

            if ($parent->getType() < 2){//only pages and links
                $url = $parent->getUrl().'/';
            }

        }

        $url .= $this->getUrl();
        $this->setFullUrl($url);

        return true;
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
