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
     * Generates the full url pasend on all parents.
     *
     * @param null|PropelPDO $con
     * @return string
     */
    public function generateFullUrl(PropelPDO $con = null){

        $url = '';

        $parents = $this->getAncestors();
        foreach ($parents as $parent){

            if ($parent->getUrl() && $parent->getType() !== null && $parent->getType() < 2){ //only pages and links
                $url .= $parent->getUrl().'/';
            }

        }

        $url .= $this->getUrl();
        return $url;

    }

    /**
     * @return string
     */
    public function getFullUrl(){

        if (!$this->full_url && $this->getId()){

            $this->full_url = $this->generateFullUrl();

            //we save the result in the database
            $c1 = new Criteria();
            $c1->add(PagePeer::ID, $this->getId());

            $c2 = new Criteria();
            $c2->add(PagePeer::FULL_URL, $this->full_url);

            BasePeer::doUpdate($c1, $c2, Propel::getConnection());

        }

        return $this->full_url;

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
