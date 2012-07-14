<?php



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
class Domain extends BaseDomain {

    /**
     * We use this var to generate all absolute urls, since it's possible
     * to access the site through aliases.
     *
     * @var string
     */
    private $realDomain;

    public function setRealDomain($pRealDomain){
        $this->realDomain = $pRealDomain;
    }

    public function getRealDomain(){
        return $this->realDomain;
    }

} // SystemDomain
