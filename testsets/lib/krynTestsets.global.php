<?php



function krnTestSetInstallExtensionFromArray($pExtensionKey, $pConfig){

    if (!$pExtensionKey) return;

    @mkdirr(PATH . 'inc/module/' . $pExtensionKey);
    @mkdirr(PATH . 'inc/template/' . $pExtensionKey);

}

function krynTestSetsDeinstallExtension($pExtensionKey){

    if (!$pExtensionKey) return;

    @rmdir(PATH . 'inc/module/' . $pExtensionKey);
    @rmdir(PATH . 'inc/template/' . $pExtensionKey);

}

function krynTestSetExit($pError, $pFile, $pLine){

    $fp = fopen('php://stderr', 'r+');
    fputs($fp, "$pError\n");
    fclose($fp);

    exit(1);
}


?>