<?php

namespace Core\Models;

use Core\Kryn;
use Core\Models\Base\Node as BaseNode;

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
class Node extends BaseNode
{
    /**
     * Same as getChildren but returns only visible pages and non-folder nodes
     *
     * @param  boolean                 $pWithFolders
     *
     * @return \PropelObjectCollection
     */
    public function getLinks($pWithFolders = false)
    {
        if ($this->collNestedGetLinks === null) {

            $types = $pWithFolders ? array(0, 1, 2) : array(0, 1);
            $this->collNestedGetLinks = NodeQuery::create()
                ->childrenOf($this)
                ->filterByVisible(1)
                ->filterByType($types)
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
    public function hasLinks()
    {
        $links = $this->getLinks();

        return count($links) !== 0;

    }

    /**
     * Returns all parents.
     *
     * @return mixed
     */
    public function getParents()
    {
        if (!$this->parents_cached) {

            $this->parents_cached = array();

            $ancestors = $this->getAncestors();
            foreach ($ancestors as $parent) {

                if ($parent->getType() !== null && $parent->getType() < 2) { //exclude root node
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
    public function getPath($pDelimiter = ' » ')
    {
        $parents = $this->getParents();

        $path = $this->getDomain()->getDomain();
        foreach ($parents as &$parent) {
            $path .= $pDelimiter . $parent->getTitle();
        }

        $path .= $pDelimiter . $this->getTitle();

        return $path;
    }

    /**
     * Returns the URL of the given page.
     *
     * If the current domain differs from page's domain,
     * then we'll add the protocol and domain name as well (means with http://<domain>[/<language>]/<urn>).
     *
     * @param integer|Node $pPage
     * @param boolean      $pForceFullUrl No matter if the page' domain differs from the current domain, we always
     *                                    return the full URL (means with http://<domain>[/<language>]/<urn>)
     *
     * @return string|void
     * @static
     */
    public static function getUrl($pPage, $pForceFullUrl = false)
    {
        $id = $pPage instanceof Node ? $pPage->getId() : $pPage + 0;
        $domainId = $pPage instanceof Node ? $pPage->getDomainId() : Kryn::getDomainOfPage($pPage + 0);

        $urls =& Kryn::getCachedPageToUrl($domainId);
        $url = $urls[$id];

        if ($pForceFullUrl || !Kryn::$domain || $domainId != Kryn::$domain->getId()) {
            $domain = Kryn::$domain ? : Kryn::getDomain($domainId);

            $domainName = $domain->getRealDomain();
            if ($domain->getMaster() != 1) {
                $url = $domainName . $domain->getPath() . $domain->getLang() . '/' . $url;
            } else {
                $url = $domainName . $domain->getPath() . $url;
            }

            $url = 'http' . (Kryn::$ssl ? 's' : '') . '://' . $url;
        }

        //crop last /
        if (substr($url, -1) == '/') {
            $url = substr($url, 0, -1);
        }

        if ($url == '/') {
            $url = '.';
        }

        return $url;
    }

    /**
     * If this page is the current page or one of the parents of the current.
     * Useful for navigation highlighting.
     *
     * @return bool
     */
    public function isActive()
    {
        if ($this->getId() == \Core\Kryn::$page->getId()) {
            return true;
        }

        $url = self::getUrl(\Core\Kryn::$page);
        $purl = self::getUrl($this);

        if ($url && $purl) {
            $pos = strpos($url, $purl);
            if ($url == '/' || $pos != 0 || $pos === false) {
                return false;
            } else {
                return true;
            }
        }

        return false;
    }

} // Node
