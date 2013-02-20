<?php

namespace Core;

use Core\om\BaseDomain;

/**
 * Skeleton subclass for representing a row from the 'kryn_system_domain' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.kryn
 */
class Domain extends BaseDomain
{
    /**
     * We use this var to generate all absolute urls, since it's possible
     * to access the site through aliases.
     *
     * @var string
     */
    private $realDomain;

    /**
     *
     * @param string $pRealDomain
     */
    public function setRealDomain($pRealDomain)
    {
        $this->realDomain = $pRealDomain;
    }

    /**
     * @return string
     */
    public function getRealDomain()
    {
        return $this->realDomain;
    }

    /**
     * Returns the full url, with http/s, hostname and language prefix.
     *
     * @param  boolean $pSSL
     * @return string
     */
    public function getUrl($pSSL = null)
    {
        if ($pSSL === null)
            $pSSL = \Core\Kryn::$ssl;

        $url = $pSSL?'https://':'http://';

        if ($domain = $this->getRealDomain())
            $url .= $domain;
        else
            $url .= $this->getDomain();

        if ($this->getMaster() != 1)
            $url .= '/'/$this->getLang();

        return $url.'/';
    }
} // Domain
