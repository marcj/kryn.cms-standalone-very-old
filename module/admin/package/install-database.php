<?php


if (!defined('KRYN_MANAGER')) return false;


dbDelete('system_domain');
dbDelete('system_page');
dbDelete('system_page_content');

//setup pages

$domainName = '127.0.0.1';
if ($_SERVER['SERVER_NAME'])
    $domainName = $_SERVER['SERVER_NAME'];

$domain = new Domain();
$domain->setDomain($domainName);

if ($_SERVER['REQUEST_URI']){
    $path = dirname($_SERVER['REQUEST_URI']);
    if( substr($path, 0, -1) != '/' ) $path .= '/';
    $path = str_replace("//", "/", $path);
    $path = str_replace('\\', '', $path);
} else {
    $path = $_GET['path']?$_GET['path']:'/';
}

$domain->setPath($path);
$domain->setTitleFormat('%title | Page title');
$domain->setMaster(1);
$domain->setLang('en');
$domain->setResourcecompression(1);
$domain->setSearchIndexKey(md5($_SERVER['SERVER_NAME'].'-'.@time().'-'.rand()));
$domain->save();

$root = new Page();
$root->setDomainId($domain->getId());
$root->makeRoot();
$root->setTitle('root');
$root->save();

$defaultLayout = 'th_krynDemo/layout_default.tpl';
$defaultContentTemplate = 'th_krynDemo/content_default.tpl';
$pages = array(

    array(0, 'Blog', $defaultLayout, 'home', '',
        array(
            '1' => array(
                array('text', 'Kryn.cms has been installed!', $defaultContentTemplate, '<p>Kryn.cms has been installed correctly.</p><p>&nbsp;</p><p><a href="http://www.kryn.org">Kryn CMS Website</a></p><p>&nbsp;</p><p>&nbsp;</p><p>Go to <a href="admin">administration</a> to manage your new website.</p><p>&nbsp;</p><p><strong>Default login:</strong></p><p><strong><br /></strong></p><p style="padding-left: 10px;">Username: admin</p><p style="padding-left: 10px;">Password: admin</p>'),
                array('plugin', '', $defaultContentTemplate, 'publication::newsList::{"itemsPerPage":"","maxPages":"","detailPage":"16","template":"default","category_rsn":["1"]}')
            ),
            '2' => array(
                array('plugin', '» CATEGORIES', $defaultContentTemplate, 'publication::categoryList::{"listPage":"1","template":"default","category_rsn":[]}'),
            )
        ),
        array(
            array(1, 'Article', $defaultLayout, 'article', array(), array(), 0)
        )
    ),

    array(0, 'Links', $defaultLayout, 'links', '',
        array(
            '1' => array(
                array('text', 'Links', $defaultContentTemplate, 'Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim. Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt. Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis, feugiat a, tellus. Phasellus viverra nulla ut metus varius laoreet. Quisque rutrum. Aenean imperdiet. Etiam ultricies nisi vel augue. Curabitur ullamcorper ultricies nisi.')
            ),
            '2' => array(
                array('text', '» About', $defaultContentTemplate, 'hoho'),
            )
        ),
        array(
            array(1, 'Kryn.cms Official Website', $defaultLayout, 'www-kryn-org', 'http://www.kryn.org/'),
            array(1, 'Kryn.cms Documentation', $defaultLayout, 'docu-kryn-org', 'http://docu.kryn.org/'),
            array(1, 'Kryn.cms Extensions', $defaultLayout, 'www-kryn-org-extensions', 'http://www.kryn.org/extensions')
        )
    ),

);

/*
* 0: type
* 1: Title
* 2: layout
* 3: url
* 4: link target
* 5: contents
* 6: children
* 7: visible
*/
foreach ($pages as $page){

    $oPage = new Page();

    $oPage->setDomainId($domain->getId());
    $oPage->setType($page[0]);
    $oPage->setTitle($page[1]);
    $oPage->setLayout($page[2]);
    $oPage->setUrl($page[3]);
    $oPage->insertAsLastChildOf($root);
    if ($page[7])
        $oPage->setVisible($page[7]);
    $oPage->save();

    if ($page[4])
        $oPage->setLink($page[4]);

    if ($page[5])
        installPageContents($oPage, $page[5]);

    if ($page[6]){
        installPages($oPage, $page[6]);
    }
}

$startPage = PageQuery::create()->filterByDomainId($domain->getId())->findOneByLft(2);
$domain->setStartpageId($startPage->getId());
$domain->save();

dbDelete('system_langs');
$h = fopen(PATH_MODULE . 'admin/ISO_639-1_codes.csv', 'r');
if ($h) {
    while (($data = fgetcsv($h, 1000, ",")) !== FALSE) {
        dbInsert('system_langs', array('code' => $data[0], 'title' => $data[1], 'langtitle' => $data[2]));
    }
}

dbUpdate('system_langs', array('code' => 'en'), array('visible' => 1));




/**
 * @static
 * @param Page $pPage
 * @param array $pChildren
 */
function installPages($pPage, $pChildren){

    /*
    * 0: type
    * 1: Title
    * 2: layout
    * 3: url
    * 4: link target
    * 5: contents
    * 6: children
    * 7: visible
    */
    foreach ($pChildren as $page){
        $oPage = new Page();
        $oPage->setDomainId($pPage->getDomainId());
        $oPage->setType($page[0]);
        $oPage->setTitle($page[1]);
        $oPage->setLayout($page[2]);
        $oPage->setUrl($page[3]);
        $oPage->setParentId($pPage->getId());
        $oPage->insertAsLastChildOf($pPage);

        if ($page[7])
            $oPage->setVisible($page[7]);

        $oPage->save();

        if ($page[4])
            $oPage->setLink($page[4]);

        if ($page[5])
            self::installPageContents($oPage, $page[5]);

        if ($page[6]){
            self::installPages($oPage, $page[6]);
        }
    }

}


/**
 * @static
 * @param Page $pPage
 * @param array $pBoxedContents
 */
function installPageContents($pPage, $pBoxedContents){

    if (!is_array($pBoxedContents)) return;

    /**
     * 0: type
     * 1: title
     * 2: template
     * 3: content
     *
     */
    foreach ($pBoxedContents as $boxId => $contents){
        foreach ($contents as $content){

            $oContent = new PageContent();

            $oContent->setPageId($pPage->getId());
            $oContent->setBoxId($boxId);
            $oContent->setType($content[0]);
            $oContent->setTitle($content[1]);
            $oContent->setTemplate($content[2]);
            $oContent->setContent($content[3]);
            $oContent->save();

        }
    }

}
