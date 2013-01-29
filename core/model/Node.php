<?php

namespace Core;

use Core\om\BaseNode;


/**
 * Skeleton subclass for representing a row from the 'kryn_system_node' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.Core
 */
class Node extends BaseNode {

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
     * Same as getChildren but returns only visible pages and non-folder nodes
     *
     * @return array
     */
    public function getLinks(){

        if ($this->collNestedGetLinks === null){
            $this->collNestedGetLinks = NodeQuery::create()
                ->childrenOf($this)
                ->filterByVisible(1)
                ->filterByType(array(0,1))
                ->orderByBranch()
                ->find();
        }
        return $this->collNestedGetLinks;
    }


    /**
     * Does the current node has (valid) sub links?
     *
     * @return bool
     */
    public function hasLinks(){

        $links = $this->getLinks();
        return count($links)!==0;

    }

    /**
     * Returns all parents.
     *
     * @return mixed
     */
    public function getParents(){

        if (!$this->parents_cached){

            $this->parents_cached = array();

            $ancestors = $this->getAncestors();
            foreach ($ancestors as $parent){

                if ($parent->getType() !== null && $parent->getType() < 2){ //exclude root node
                    $this->parents_cached[] = $parent;
                }

            }
        }

        return $this->parents_cached;
    }


    /**
     * Generates a path to the current page.
     *
     * level 1 -> level 2 -> page
     *
     * where ' -> ' is a $pDelimiter
     *
     * @param string $pDelimiter
     *
     * @return string
     */
    public function getPath($pDelimiter = ' » '){

        $parents = $this->getParents();

        $path = $this->getDomain()->getDomain();
        foreach ($parents as &$parent) {
            $path .= $pDelimiter . $parent->getTitle();
        }

        $path .= $pDelimiter . $this->getTitle();

        return $path;
    }

    /**
     * Returns the full url to the given page object.
     *
     * If the page belongs to another domain than the current,
     * then the url contains http://<otherDomain>/<fullUrl>
     *
     * @param bool $pWithoutContextCheck Does or does not check whether the page belongs to the current domain and
     *                                   therefore add the domain name.
     * @return string
     */
    public function getFullUrl($pWithoutContextCheck = false){

        if (!$this->full_url && $this->getId()){

            $this->full_url = $this->generateFullUrl();

            //we save the result in the database
            $c1 = new \Criteria();
            $c1->add(NodePeer::ID, $this->getId());

            $c2 = new \Criteria();
            $c2->add(NodePeer::FULL_URL, $this->full_url);

            \BasePeer::doUpdate($c1, $c2, \Propel::getConnection());

        }

        if (!$pWithoutContextCheck)
            return $this->full_url;

        if (!$this->full_url_context){
            $this->full_url_context = Kryn::fullUrl($this);
        }

        return $this->full_url_context;

    }

    /**
     * If this page is the current page or one of the parents of the current.
     * Useful for navigation highlighting.
     *
     * @return bool
     */
    public function isActive(){

        if( $this->getId() == \Core\Kryn::$page->getId() ) return true;

        $url = \Core\Kryn::$page->getFullUrl();
        $purl = $this->getFullUrl();

        if ($url && $purl){
            $pos = strpos( $url, $purl );
            if( $url == '/' || $pos != 0  || $pos === false){
                return false;
            } else {
                return true;
            }
        }
        return false;
    }


} // Node
