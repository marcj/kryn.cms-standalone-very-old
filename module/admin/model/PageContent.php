<?php



/**
 * Skeleton subclass for representing a row from the 'kryn_system_content' table.
 *
 * 
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.kryn
 */
class PageContent extends BasePageContent {

    protected function createSlug()
    {
        $slug = $this->createRawSlug();
        $slug = $this->limitSlugSize($slug);

        return $slug;
    }
} // PageContent
