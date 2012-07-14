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

    public function setFullUrl($pFullUrl){
        $this->fullUrl = $pFullUrl;
    }

    public function getFullUrl(){
        return $this->fullUrl;
    }
} // SystemPage
