<?php

/*
 * This file is part of Kryn.cms.
 *
 * (c) Kryn.labs, MArc Schmidt <marc@kryn.org>
 *
 * To get the full copyright and license informations, please view the
 * LICENSE file, that was distributed with this source code.
 *
 */

namespace Admin;

use \Core\Kryn;

class AdminController {

    /**
     * Checks the access to the administration URLs and redirect to administration login if no access.
     * 
     * @internal
     * @static
     */
    public static function checkAccess($pUrl) {

        return true;

        if (substr($pUrl, 0, 9) == 'admin/ui/'){
            return true;
        }

        if ($pUrl == 'admin/login'){
            return true;
        }

        //todo, use Permission class

        //if (Kryn::checkUrlAccess($pUrl))
        //    throw new \AccessDeniedException(tf('Access denied.'));
    }

    public function exceptionHandler($pException){
        if (get_class($pException) != 'AccessDeniedException')
            \Core\Utils::exceptionHandler($pException);
    }


    public function run() {

        @header('Expires:');

        if (Kryn::$config['displayDetailedRestErrors']){
            $exceptionHandler = array($this, 'exceptionHandler');
            $debugMode = true;
        }

        $url = Kryn::getRequestedPath();

        //checkAccess
        $this->checkAccess($url);

        
        $entryPoint = Utils::getEntryPoint(substr($url, strlen('admin/')));
        
        if ($entryPoint) {

            //is window entry point?
            $objectWindowTypes = array('list', 'edit', 'add', 'combine');

            if (in_array($entryPoint['type'], $objectWindowTypes)){
                $epc = new ObjectWindowController('admin');
                $epc->setExceptionHandler($exceptionHandler);
                $epc->setDebugMode($debugMode);
                $epc->run($entryPoint);
            }

            //is store?
            //todo
        }

        if (Kryn::isActiveModule(getArgv(2)) && getArgv(2) != 'admin'){

            $clazz = '\\'.ucfirst(getArgv(2)).'\\AdminController';

            if (get_parent_class($clazz) == 'RestService\Server'){
                $obj = new $clazz('admin/'.getArgv(2));
                $obj->setExceptionHandler($exceptionHandler);
                $obj->setDebugMode($debugMode);
            } else {
                $obj = new $clazz();
            }

            die($obj->run());

        } else {

            \RestService\Server::create('admin', $this)

                ->setCheckAccess(array($this, 'checkAccess'))
                ->setExceptionHandler($exceptionHandler)
                ->setDebugMode($debugMode)

                ->addGetRoute('', 'showLogin')

                ->addGetRoute('css/style.css', 'loadCss')
                ->addGetRoute('login', 'loginUser')
                ->addGetRoute('logged-in', 'loggedIn')
                ->addGetRoute('logout', 'logoutUser')

                ->addSubController('ui', '\Admin\UIAssets')
                    ->addGetRoute('possibleLangs', 'getPossibleLangs')
                    ->addGetRoute('languagePluralForm', 'getLanguagePluralForm')
                    ->addGetRoute('language', 'getLanguage')
                ->done()

                //admin/backend
                ->addSubController('backend', '\Admin\Backend')
                    ->addGetRoute('js/script.js', 'loadJs')

                    ->addGetRoute('settings', 'getSettings')

                    ->addGetRoute('desktop', 'getDesktop')
                    ->addPostRoute('desktop', 'saveDesktop')

                    ->addGetRoute('widgets', 'getWidgets')
                    ->addPostRoute('widgets', 'saveWidgets')

                    ->addGetRoute('menus', 'getMenus')
                    ->addGetRoute('custom-js', 'getCustomJs')
                    ->addPostRoute('user-settings', 'saveUserSettings')

                    ->addDeleteRoute('cache', 'clearCache')

                    ->addGetRoute('search', 'getSearch')

                ->done()



                //admin/object
                ->addSubController('object', '\Admin\Object\Controller')
                    ->addGetRoute('([a-zA-Z-_\.\\\\]+)/([^/]+)', 'getItem')
                    ->addPostRoute('([a-zA-Z-_\.\\\\]+)/([^/]+)', 'postItem')
                    ->addDeleteRoute('([a-zA-Z-_\.\\\\]+)/([^/]+)', 'deleteItem')
                    ->addPutRoute('([a-zA-Z-_\.\\\\]+)', 'putItem')
                    ->addGetRoute('([a-zA-Z-_\.\\\\]+)', 'getItems')
                ->done()

                //admin/object-branch
                ->addSubController('object-tree', '\Admin\Object\Controller')
                  ->addGetRoute('([a-zA-Z-_\.\\\\]+)', 'getTree')
                  ->addGetRoute('([a-zA-Z-_\.\\\\]+)/([^/]+)', 'getTreeBranch')
                ->done()

                //admin/object-count
                ->addSubController('object-count', '\Admin\Object\Controller')
                  ->addGetRoute('([a-zA-Z-_\.\\\\]+)', 'getCount')
                ->done()

                //admin/object-root
                ->addSubController('object-root', '\Admin\Object\Controller')
                    ->addGetRoute('([a-zA-Z-_\.\\\\]+)', 'getTreeRoot')
                ->done()

                //admin/object-roots
                ->addSubController('object-roots', '\Admin\Object\Controller')
                    ->addGetRoute('([a-zA-Z-_\.\\\\]+)', 'getTreeRoots')
                ->done()

                //admin/object-parents
                ->addSubController('object-parents', '\Admin\Object\Controller')
                    ->addGetRoute('([a-zA-Z-_\.\\\\]+)/([^/]+)', 'getParents')
                ->done()

                //admin/object-parent
                ->addSubController('object-parent', '\Admin\Object\Controller')
                    ->addGetRoute('([a-zA-Z-_\.\\\\]+)/([^/]+)', 'getParent')
                ->done()

                //admin/object-move
                ->addSubController('object-move', '\Admin\Object\Controller')
                 ->addPostRoute('([a-zA-Z-_\.\\\\]+)/([^/]+)', 'moveItem')
                ->done()

                ->addSubController('', '\Admin\Object\Controller')

                    ->addGetRoute('objects', 'getItemsByUri')
                    ->addGetRoute('object', 'getItemPerUri')
                    ->addGetRoute('object-version', 'getVersionsPerUri')

                    /*
                    ->addGetRoute('field-object/([a-zA-Z-_]+)/([^/]+)', 'getFieldItem')
                    ->addGetRoute('field-object-count/([a-zA-Z-_]+)', 'getFieldItemsCount')
                    ->addGetRoute('field-object/([a-zA-Z-_]+)', 'getFieldItems')
                    */

                    ->addGetRoute('browser-object/([a-zA-Z-_\.\\\\]+)', 'getBrowserItems')
                ->done()

                //admin/system
                ->addSubController('system', '\Admin\System')

                    ->addGetRoute('', 'getSystemInformation')

                    ->addSubController('config', '\Admin\Config')
                        ->addGetRoute('', 'getConfig')
                        ->addGetRoute('labels', 'getLabels')
                        ->addPostRoute('', 'saveConfig')
                    ->done()

                    //admin/system/module/manager
                    ->addSubController('module/manager', '\Admin\Module\Manager')
                        ->addGetRoute('install/pre', 'installPre')
                        ->addGetRoute('install/extract', 'installExtract')
                        ->addGetRoute('install/database', 'installDatabase')
                        ->addGetRoute('install/post', 'installPost')
                        ->addGetRoute('check-updates', 'check4updates')
                        ->addGetRoute('local', 'getLocal')
                        ->addGetRoute('installed', 'getInstalled')
                        ->addGetRoute('activate', 'activate')
                        ->addGetRoute('deactivate', 'deactivate')
                    ->done()



                    ->addSubController('languages', '\Admin\Languages')
                    ->done()


                    //admin/system/orm
                    ->addSubController('orm', '\Admin\ORM')
                        ->addGetRoute('environment', 'buildEnvironment')
                        ->addGetRoute('models', 'writeModels')
                        ->addGetRoute('update', 'updateScheme')
                        ->addGetRoute('check', 'checkScheme')
                    ->done()


                    //admin/system/module/editor
                    ->addSubController('module/editor', '\Admin\Module\Editor')
                        ->addGetRoute('config', 'getConfig')

                        ->addGetRoute('windows', 'getWindows')
                        ->addGetRoute('window', 'getWindowDefinition')
                        ->addPostRoute('window', 'saveWindowDefinition')
                        ->addPutRoute('window', 'newWindow')

                        ->addGetRoute('objects', 'getObjects')
                        ->addPostRoute('objects', 'saveObjects')

                        ->addGetRoute('plugins', 'getPlugins')
                        ->addPostRoute('plugins', 'savePlugins')

                        ->addPostRoute('model/from-object', 'setModelFromObject')
                        ->addPostRoute('model/from-objects', 'setModelFromObjects')

                        ->addPostRoute('model', 'saveModel')
                        ->addGetRoute('model', 'getModel')

                        ->addPostRoute('language', 'saveLanguage')
                        ->addGetRoute('language', 'getLanguage')
                        ->addGetRoute('language/extract', 'getExtractedLanguage')

                        ->addPostRoute('general', 'saveGeneral')
                        ->addPostRoute('entryPoints', 'saveEntryPoints')


                    ->done()

                ->done()

                ->addSubController('file', '\Admin\File')
                    ->addGetRoute('', 'getFiles')
                ->addGetRoute('single', 'getFile')
                ->addGetRoute('thumbnail', 'getThumbnail')
                ->done()

            ->run();

            exit;

        }
    }

    public function loginUser($pUsername, $pPassword){
        $status = Kryn::getAdminClient()->login($pUsername, $pPassword);
        return !$status ? false :
            array(
                'token' => Kryn::getAdminClient()->getToken(),
                'userId' => Kryn::getAdminClient()->getUserId(),
                'lastLogin' => Kryn::getAdminClient()->getUser()->getLastLogin()
            );
    }

    public function loggedIn(){
        return Kryn::getAdminClient()->getUserId() > 0;
    }

    public function logoutUser(){
        Kryn::getClient()->logout();
        return true;
    }


    public function searchAdmin($pQuery) {

        $res = array();

        $lang = getArgv('lang');

        //pages
        $nodes = \Core\NodeQuery::create()->filterByTitle('%'.$pQuery.'%', \Criteria::LIKE)->find();

        if (count($nodes) > 0) {
            foreach ($nodes as $node)
                $respages[] =
                    array($node->getTitle(), 'admin/pages', array('id' => $node->getId(), 'lang' => $node->getDomain()->getLang()));
            $res[t('Pages')] = $respages;
        }

        //help
        $helps = array();
        foreach (Kryn::$configs as $key => $mod) {
            $helpFile = PATH_MODULE . "$key/lang/help_$lang.json";
            if (!file_exists($helpFile)) continue;
            if (count($helps) > 10) continue;

            $json = json_decode(Kryn::fileRead($helpFile), 1);
            if (is_array($json) && count($json) > 0) {
                foreach ($json as $help) {

                    if (count($helps) > 10) continue;
                    $found = false;

                    if (preg_match("/$pQuery/i", $help['title']))
                        $found = true;

                    if (preg_match("/$pQuery/i", $help['tags']))
                        $found = true;

                    if (preg_match("/$pQuery/i", $help['help']))
                        $found = true;

                    if ($found)
                        $helps[] = array($help['title'], 'admin/help', array('id' => $key . '/' . $help['id']));
                }
            }
        }
        if (count($helps) > 0) {
            $res[t('Help')] = $helps;
        }

        return $res;
    }


    public function loadCss() {
        return Utils::loadCss();
    }

    public static function showLogin() {

        $language = Kryn::$adminClient->getSession()->getLanguage();
        if (!$language) $language = 'en';

        if (getArgv('setLang') != '')
            $language = getArgv('setLang', 2);

        tAssign('adminLanguage', $language);

        print tFetch('admin/index.tpl');
        exit;
    }

}
