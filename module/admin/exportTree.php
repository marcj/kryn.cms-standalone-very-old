<?php
function exportTree( $pParentRsn, $pDomain )
{
    $pParentRsn += 0;
    $pDomain += 0;

    $result['pages'] = _exportTree( array('id' => $pParentRsn, 'domain_id' => $pDomain) );

    return $result;
}

function _exportTree( $pPage )
{
    $pParentRsn = $pPage['id']+0;
    $pDomain = $pPage['domain_id']+0;
    $pagesRes = dbExec("SELECT * FROM %pfx%system_page WHERE pid = $pParentRsn AND domain_id = $pDomain ");

    $childs = array();
    while ( $row = dbFetch($pagesRes) ) {

        $contentRes = dbExec("SELECT c.* FROM %pfx%system_contents c, %pfx%system_page_version v
                WHERE
                c.page_id = ".$row['id']."
                AND v.active = 1
                AND c.version_id = v.id");

        while ( $contentRow = dbFetch($contentRes) ) {

            unset($contentRow['id']);
            unset($contentRow['page_id']);
            $row['contents'][] = $contentRow;

        }

        $row['childs'] = _exportTree($row);

        unset($row['id']);
        unset($row['domain_id']);
        unset($row['pid']);

        $childs[] = $row;
    }

    return $childs;
}

function importTree( $pJson, $pParentRsn, $pDomain )
{
    $obj = json_decode( $pJson, true );

    _importTree( $obj['pages'], $pParentRsn, $pDomain );
}

function _importTree( $pChilds, $pParent, $pDomain )
{
    global $user;

    if( !is_array($pChilds) || count($pChilds) == 08 ) return;

    foreach ($pChilds as $page) {
        $page['pid'] = $pParent;
        $page['domain_id'] = $pDomain;

        print "Adding page: ".$page['title']."\n";
        $lastRsn = dbInsert('system_page', $page);
        if ($page['contents']) {

            $newVersion = dbInsert('system_page_version', array(
                'created' => time(),
                'modified'=>time(),
                'owner_id' => $user->user_id,
                'page_id' => $lastRsn,
                'active' => 1
            ));

            foreach ($page['contents'] as $content) {

                $content['page_id'] = $lastRsn;
                $content['version_id'] = $newVersion;
                $content['cdate'] = time();
                dbInsert( 'system_contents', $content );

            }
        }

        _importTree( $page['childs'], $lastRsn, $pDomain );
    }

}

//$result = exportTree(0, 2);
//json( $result );

//$json = file_get_contents("media/exportTree.php");

//print $json;

//print "<pre>";
//json( $result );
//$json = json_encode($result);
//importTree( $json, 6, 1);
