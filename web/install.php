<?php
/*
* This file is part of Kryn.cms.
*
* (c) Kryn.labs, MArc Schmidt <marc@kryn.org>
*
* To get the full copyright and license information, please view the
* LICENSE file, that was distributed with this source code.
*/

if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding("UTF-8");
}

chdir(__DIR__ . '/../');
error_reporting(E_ALL & ~E_NOTICE);

use Core\Kryn;
use Core\SystemFile;

$GLOBALS['krynInstaller'] = true;

$loader = include __DIR__ . '/../vendor/autoload.php';
Kryn::bootstrap($loader);

Kryn::prepareWebSymlinks();

$lang = 'en';

@ini_set('display_errors', 1);

if ($_REQUEST['step'] == 'checkConfig') {
    checkConfig();
}

if ($_REQUEST['step'] == '5') {
    define('KRYN_INSTALLER', true);
    step5Init();
}

header("Content-Type: text/html; charset=utf-8");

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de" dir="ltr">
<head>
    <title>Kryn.cms installation</title>
    <link rel="stylesheet" type="text/css" href="bundles/core/css/normalize.css"/>
    <link rel="stylesheet" type="text/css" href="bundles/core/font/SourceSansPro/Regular/fonts.css"/>
    <link rel="stylesheet" type="text/css" href="bundles/core/font/SourceSansPro/Semibold/fonts.css"/>
    <link rel="stylesheet" type="text/css" href="bundles/admin/css/ka/Button.css"/>
    <link rel="stylesheet" type="text/css" href="bundles/admin/css/ka/Input.css"/>

    <style type="text/css">
        body {
            font-size: 14px;
            line-height: 18px;
            margin: 0px;
            font-family: "SourceSansPro-Regular", Sans;
            background-color: #22628d;
            padding: 0px;
            text-align: center;
        }

        .logo {
            position: relative;
            left: -240px;
            margin-bottom: 25px;
            margin-top: 10px;
        }

        h1 {
            margin: 0px 0px 10px 0px;
            border-bottom: 1px solid #00273c;
            font-size: 12px;
            font-weight: bold;
            color: #145E84;
        }

        h2 {
            color: #145E84;
        }

        h1, h2, h3 {
            font-family: "SourceSansPro-Semibold";
        }

        td {
            vertical-align: top;
        }

        .step a, .step a:link {
            text-decoration: none;
            color: gray;
        }

        a, a:link {
            color: gray;
        }

        table {
            font-size: 14px;
            margin: 5px;
            margin-left: 10px;
            width: 400px;
            color: #555;
        }

        table td {
            padding: 5px 5px;
        }

        table th {
            color: #444;
            border-bottom: 1px solid silver;
            font-weight: normal;
            text-align: left;
        }

        table.modulelist td {
            border-bottom: 1px solid #eee;
        }

        select {
            width: 152px;
        }

        .wrapper {
            text-align: left;
            margin: auto;
            width: 700px;
            left: 60px;
            border-radius: 2px;
            border-radius: 2px;
            -moz-border-radius: 2px;
            -webkit-border-radius: 2px;
            padding: 45px 35px;
            background-color: #f1f1f1;
            position: relative;
            color: #333;
            min-height: 200px
        }

        .step a, .step a:link {
            display: block;
            text-align: left;
            padding: 12px 5px 12px 15px;
        }

        .step a.active {
            color: black;
            font-weight: bold;
        }

        .step {
            border-right: 0px;
            -moz-border-radius-topleft: 2px;
            -moz-border-radius-bottomleft: 2px;
            -webkit-border-top-left-radius: 2px;
            -webkit-border-bottom-left-radius: 2px;
            border-top-left-radius: 2px;
            border-bottom-left-radius: 2px;
            position: absolute;
            top: 27px;
            left: -150px;
            width: 150px;
            background-color: #f1f1f1;
            margin-bottom: 15px;
        }

        h2.main {
            position: absolute;
            top: 0px;
            left: 35px;
            right: 35px;
            border-bottom: 1px dashed #ddd;
            padding-bottom: 5px;
            color: #666;
        }

        #progressBar {
            -moz-border-radius: 2px;
            -webkit-border-radius: 2px;
            border-radius: 2px;
            background-color: #22628d;
            height: 25px;
            text-align: center;
            color: white;
            line-height: 25px;
            font-weight: bold;
            position: relative;
        }

        #progressError {
            white-space: pre;
            background-color: white;
            padding: 15px;
            border: 1px solid red;
        }

        #progressBarText {
            position: relative;
        }

        #progressBarIn {
            -moz-border-radius: 2px;
            -webkit-border-radius: 2px;
            border-radius: 2px;
            background-color: #22518e;
            height: 25px;
            position: absolute;
            top: 0px;
            left: 0px;
        }

        select {
            width: 141px;
        }

        .breaker {
            clear: both
        }

    </style>
    <script type="text/javascript" src="bundles/core/mootools-core.js"></script>
    <script type="text/javascript" src="bundles/core/mootools-more.js"></script>
    <link rel="SHORTCUT ICON" href="bundles/admin/images/favicon.ico"/>
</head>
<body>
<img class="logo" src="bundles/core/images/logo_white.png"/>

<div class="wrapper">
<h2 class="main">Installation</h2>
<?php

$step = 1;
if (!empty($_REQUEST['step'])) {
    $step = $_REQUEST['step'];
}
?>

<div class="step">
    <a href="javascript:;" <?php if ($step == 1) {
        echo 'class="active"';
    } ?>>1. Start</a>
    <a href="javascript:;" <?php if ($step == 2) {
        echo 'class="active"';
    } ?>>2. Requirements</a>
    <a href="javascript:;" <?php if ($step == 3) {
        echo 'class="active"';
    } ?>>3. Configuration</a>
    <a href="javascript:;" <?php if ($step == 4) {
        echo 'class="active"';
    } ?>>4. Package</a>
    <a href="javascript:;" <?php if ($step == 5) {
        echo 'class="active"';
    } ?>>5. Installation</a>

    <div class="breaker"></div>
</div>

<?php

switch ($step) {
    case '5':
        step5();
        break;
    case '4':
        step4();
        break;
    case '3':
        step3();
        break;
    case '2':
        step2();
        break;
    case '1':
        welcome();
}

function checkConfig()
{
    if ((file_exists('app/config/config.xml') && !is_writable('app/config/config.xml'))
        || !file_exists('app/config/config.xml') && !is_writable('app/config/')
    ) {
        $res['res'] = false;
        $res['error'] = 'app/config/config.xml is not writable.';
        die(json_encode($res));
    }

    $cfg = array(
        "db_server" => $_REQUEST['server'],
        "db_user" => $_REQUEST['username'],
        "db_password" => $_REQUEST['password'],
        "db_name" => $_REQUEST['db'],
        "db_prefix" => $_REQUEST['prefix'],
        "db_type" => $_REQUEST['type'],
        "db_pdo" => $_REQUEST['pdo']
    );

    $res = array('res' => true);

    $dsn = $cfg['db_type'] . ':dbname=' . $cfg['db_name'] . ';host=' . $cfg['db_server'];

    try {
        $connection = new PDO($dsn, $cfg['db_user'], $cfg['db_password']);
    } catch (PDOException $e) {
        $res['res'] = false;
        $res['error'] = "`$dsn` [" . $e->getMessage() . ']';
    }

    if ($res['res'] == true) {
        $database = Kryn::$config->getDatabase(true);

        $connection = $database->getMainConnection();
        $connection->setServer($_REQUEST['server']);
        $connection->setUsername($_REQUEST['username']);
        $connection->setPassword($_REQUEST['password']);
        $connection->setName($_REQUEST['db']);
        $connection->setType($_REQUEST['type']);
        $connection->setPersistent($_REQUEST['persistent'] == 1);

        $database->setPrefix($_REQUEST['prefix']);

        $timezone = $_REQUEST['timezone'];
        if (!$timezone) {
            $timezone = 'Europe/Berlin';
        }

        Kryn::$config->setTimezone($timezone);

        $systemTitle = $_REQUEST['systemTitle'];
        if (!$systemTitle) {
            $systemTitle = "Fresh Installation";
        }

        Kryn::$config->setSystemTitle($systemTitle);

        if ($_REQUEST['id']) {
            Kryn::$config->setId($_REQUEST['id']);
        }

        Kryn::$config->setTempDir($_REQUEST['fileTemp']);

        $filePermission = Kryn::$config->getFile(true);
        $filePermission->setDisableModeChange($_REQUEST['fileNoChangeMode'] == 1);
        $filePermission->setGroupPermission($_REQUEST['fileGroupPermission']);
        $filePermission->setEveryonePermission($_REQUEST['fileEveryonePermission']);
        $filePermission->setGroupOwner($_REQUEST['fileGroupName']);

        $errors = Kryn::$config->getErrors(true);
        $errors->setDisplay($_REQUEST['displayErrors'] == 1);
        $errors->setStackTrace($_REQUEST['displayBeautyErrors'] == 1);
        $errors->setDisplayRest($_REQUEST['displayDetailedRestErrors'] == 1);

        if (!SystemFile::createFolder($temp = Kryn::getTempFolder())) {
            $res['error'] = sprintf('Can not create the temp folder ``.', $temp);
            $res['res'] = false;
        }

        if (!Kryn::$config->save('app/config/config.xml', true)) {
            $res['error'] = 'Can not open file app/config/config.xml - please change the permissions.';
            $res['res'] = false;
        }

        delDir(Kryn::getTempFolder() . 'propel');
    }

    die(json_encode($res));
}

function welcome()
{
    ?>

    <h2>Thanks for choosing Kryn.cms!</h2>
    <br/>
    Your installation folder is <strong style="color: gray;"><?php echo getcwd(); ?></strong>
    <br/>
    <br/>
    <b>Kryn.cms license</b><br/>
    <br/>
    <div style="height: 350px; background-color: white; padding: 5px; overflow: auto; white-space: pre;">
<?php $f = fopen("LICENSE", "r");
        if ($f) {
            while (!feof($f)) print fgets($f, 4096);
} ?>
    </div>
    <br/><br/>
    <b style="color: gray">Kryn.cms comes with amazing additional third party software.</b><br/>
    Please respect all of their licenses too:<br/>
    <br/>
    <table style="width: 100%" cellpadding="3">
        <tr>
            <th width="160">Name</th>
            <th width="250">Author/Link</th>
            <th>License</th>
        </tr>
        <tr>
            <td width="160">Propel</td>
            <td width="250"><a href="http://propelorm.org//">propelorm.org/</a></td>
            <td>&raquo; <a href="http://www.propelorm.org/download#license">MIT license</a></td>
        </tr>
        <tr>
            <td width="160">Mootools</td>
            <td width="250"><a href="http://mootools.net/">mootools.net</a></td>
            <td>&raquo; <a href="http://www.opensource.org/licenses/mit-license.php">MIT license</a></td>
        </tr>
        <tr>
            <td>Smarty</td>
            <td><a href="http://www.smarty.net/">www.smarty.net</a></td>
            <td>&raquo; <a href="http://www.gnu.org/licenses/lgpl.html">LGPL</a></td>
        </tr>
        <tr>
            <td>Codemirror</td>
            <td><a href="http://codemirror.net/">codemirror.net</a></td>
            <td>&raquo; <a href="../lib/codemirror/LICENSE">MIT-style license</a></td>
        </tr>
        <tr>
            <td>normalize.css</td>
            <td><a href="http://necolas.github.com/normalize.css/">necolas.github.com/normalize.css</a></td>
            <td>&raquo; <a href="../lib/codemirror/LICENSE">MIT License</a></td>
        </tr>

        <tr>
            <td>Silk icon set 1.3</td>
            <td><a href="http://www.famfamfam.com/lab/icons/silk/">www.famfamfam.com/lab/icons/silk/</a></td>
            <td>&raquo; <a href="http://creativecommons.org/licenses/by/2.5/">Creative Commons Attribution 2.5
                    License.</a></td>
        </tr>
        <tr>
            <td width="160">[Mootools plugin] Stylesheet</td>
            <td width="250"><a href="http://mifjs.net">Anton Samoylov</a></td>
            <td>&raquo; <a href="http://mifjs.net/license.txt">MIT-Style License</a></td>
        </tr>

        <tr>
            <td colspan="3">IconSet:
            </td>
        </tr>
        <tr>
            <td colspan="3" style="white-space: pre; background-color: white;"><?php print file_get_contents(
                    'src/Admin/Resources/public/icons/license.txt'
                ); ?>
            </td>
        </tr>

    </table>
    <a href="?step=2" class="ka-Button">Accept</a>

<?php
}

function step5Failed($pError)
{
    $msg = array('error' => $pError->getMessage(), 'exception' => (string)$pError);

    if ($pError instanceof \PDOException) {
        //$msg['sql'] = ;
    }
    print json_encode($msg);
    exit;
}

function step5Done($pMsg)
{
    $msg = array('data' => $pMsg);
    print json_encode($msg);
    exit;
}

//prepare
function step5_1()
{
    try {

        \Core\TempFile::createFolder('./');
        \Core\WebFile::createFolder('cache/');

        \Core\PropelHelper::cleanup();

        \Core\Kryn::$bundles = array();

        \Core\PropelHelper::updateSchema();
        \Core\PropelHelper::cleanup();

    } catch (Exception $e) {
        step5Failed($e);
    }

    step5Done(true);
}

//Install core modules
function step5_2()
{
    $manager = new Admin\Module\Manager;

    try {
        $manager->install('Core\\CoreBundle');
        $manager->install('Admin\\AdminBundle');
        $manager->install('Users\\UsersBundle');
    } catch (Exception $e) {
        step5Failed($e);
    }
    step5Done(true);
}

//Update ORM
function step5_3()
{
    \Core\TempFile::remove('propel');

    \Core\Kryn::$bundles = array('Core\\CoreBundle', 'Admin\\AdminBundle', 'Users\\UsersBundle');

    try {
        \Core\PropelHelper::updateSchema();
        \Core\PropelHelper::generateClasses();
    } catch (Exception $e) {
        step5Failed($e);
    }
    step5Done(true);
}

//execute installDatabase
function step5_4()
{

    $manager = new Admin\Module\Manager;

    try {
        $manager->installDatabase('Core\\CoreBundle');
        $manager->installDatabase('Admin\\AdminBundle');
        $manager->installDatabase('Users\\UsersBundle');
    } catch (Exception $e) {
        step5Failed($e);
    }
    step5Done(true);
}

//Install modules
function step5_5()
{
    $manager = new \Admin\Module\Manager;

    foreach (Kryn::$bundles as $module) {
        if ($module == 'Admin\\AdminBundle' || $module == 'Users\\UsersBundle' || $module == 'Core\\CoreBundle') {
            continue;
        }
        try {
            $manager->install($module);
        } catch (Exception $e) {
            step5Failed($e);
        }
    }
    step5Done(true);
}

//Update ORM
function step5_6()
{
    \Core\TempFile::remove('propel');

    try {
        \Core\PropelHelper::generateClasses();
        \Core\PropelHelper::updateSchema();
        \Core\PropelHelper::cleanup();
    } catch (Exception $e) {
        step5Failed($e);
    }
    step5Done(true);
}

//Execute module database-scripts
function step5_7()
{
    global $modules;

    $manager = new \Admin\Module\Manager;

    foreach (Kryn::$bundles as $module) {
        if ($module == 'Admin\\AdminBundle' || $module == 'Users\\UsersBundle' || $module == 'Core\\CoreBundle') {
            continue;
        }
        try {
            $manager->installDatabase($module);
        } catch (Exception $e) {
            step5Failed($e);
        }
    }
    step5Done(true);
}

//cleanup
function step5_8()
{
    \Core\PropelHelper::cleanup();

    //load all configs
    \Core\Kryn::loadModuleConfigs();

    \Admin\Utils::clearCache();

    //todo
    if (Kryn::$config['rename']) {
        rename(
            'install.php',
            '../install.doNotRemoveIt.' . rand(123, 5123) . rand(585, 2319293) . rand(9384394, 313213133)
        );
    }

    step5Done(true);
}

function step5Init()
{
    $subStep = $_GET['substep'] + 0;

    define('pfx', Kryn::$config['database']['prefix']);

    if ($subStep == 0 && $_REQUEST['modules']) {
        Kryn::$config->setBundles(array_keys($_REQUEST['modules']));
        Kryn::$config->save('app/config/config.xml', true);
    }
    Kryn::loadActiveModules();

    $temp = Kryn::getTempFolder();
    if (!SystemFile::exists($temp) && !SystemFile::createFolder($temp)) {
        die('Temp folder is not accessible: '. $temp);
    }

    if ($subStep >= 1) {
        \Core\PropelHelper::loadConfig();
    }

    if ($subStep > 0) {
        $fn = 'step5_' . $subStep;
        $fn();
        exit;
    }
}

function step5()
{
    ?>

    <br/>
    <h2>Installing ....</h2>
<?php

?>
    <div id="progress">
        <div id="progressMessage">Pending ...</div>
        <div id="progressBar">
            <div id="progressBarIn" style="width: 0px"></div>
            <div id="progressBarText">0%</div>
        </div>
        <div id="progressError" style="display: none; overflow-x: auto;"></div>
    </div>
    <div id="installDone" style="display: none;">
        <br/>

        <h3 style="color: green;">Installation successful!</h3>
        <br/>
        <b>Administration login</b><br/>
        <table width="50%">
            <tr>
                <td>Login:</td>
                <td>admin</td>
            </tr>
            <tr>
                <td>Password:</td>
                <td>admin</td>
            </tr>

        </table>
        !Please change your password as fast as possible!<br/>
        <br/>
        <br/>
        &raquo; Go to:
        <a target="_blank" href="../">Frontend</a> or <a target="_blank" href="./kryn">Administration</a>
        <br/>
        <br/>

        <div
            style="color: gray;  border: 1px solid silver; border-top: 1px dashed #888; padding: 15px; margin: 15px 0; border-radius: 5px;">
            Developer information:<br/><br/>
            If you have chosen a temp directory outside of your project dir, you should include the following folder to your project to get the
            correct auto-completion in your IDE, since there are all propel model classes.<br/>
            <pre><?php echo Kryn::getTempFolder() . 'propel-classes/' ?></pre>
        </div>
    </div>

    <script type="text/javascript">
        window.addEvent('domready', function () {

            var steps = [
                'Preparing and cleaning database ....',
                'Install core modules ...',
                'Update ORM ...',
                'Execute database-scripts ...',
                'Install modules ...',
                'Update ORM ...',
                'Execute module database-scripts ...',
                'Cleanup ...'
            ];

            var currentStep = 0;
            var handleNextStep;

            document.id('progressBarIn').set('tween', {duration: 250});

            handleNextStep = function () {
                currentStep++;

                document.id('progressMessage').set('text', steps[currentStep - 1]);

                new Request.JSON({
                    url: 'install.php?step=5&substep=' + currentStep,
                    noCache: 1,
                    onComplete: function (pResult) {

                        if (pResult.error) {
                            document.id('progressError').setStyle('display', 'block');
                            document.id('progressError').set('html', pResult.error);
                            if (pResult.sql) {
                                new Element('div', {
                                    text: pResult.sql
                                }).inject(document.id('progressError'));
                            }
                            if (pResult.exception) {
                                new Element('div', {
                                    text: pResult.exception
                                }).inject(document.id('progressError'));
                            }
                            new Element('h2', {
                                style: 'color: red',
                                text: 'Error'
                            }).inject(document.id('progressError'), 'top');
                        } else {

                            width = (currentStep / (steps.length / 700));
                            percent = (currentStep / (steps.length / 100));
                            document.id('progressBarText').set('text', percent.toFixed(0) + '%');
                            document.id('progressBarIn').tween('width', width);

                            if (steps.length == currentStep) {
                                document.id('progress').setStyle('display', 'none');
                                document.id('installDone').setStyle('display', 'block');
                            } else {
                                handleNextStep.delay(250);
                            }
                        }
                    },
                    onError: function (pResult) {

                        document.id('progressError').setStyle('display', 'block');
                        document.id('progressError').set('html', pResult);
                        new Element('h2', {
                            style: 'color: red',
                            text: 'Error'
                        }).inject(document.id('progressError'), 'top');
                    }
                }).get();

            }

            handleNextStep();

        });
    </script>

<?php
}

function step4()
{
    ?>

    <br/>
    Your package contains following modules:<br/>
    <br/>
    <br/>
    <form action="?step=5" method="post" id="form.modules">
        <table style="width: 98%" class="modulelist" cellpadding="4">
            <?php
            buildModInfo();
            ?>
        </table>
    </form>
    <b style="color: red;">All database tables related to these bundles and all tables where the name starts with
        `<?php echo Kryn::getSystemConfig()->getDatabase(true)->getPrefix(); ?>`
        will be dropped in the next step!</b><br/><br/>
    <a href="?step=3" class="ka-Button">Back</a>
    <a href="javascript: document.id('form.modules').submit();" class="ka-Button">Install!</a>
<?php
}

function buildModInfo()
{
    $finder = new \Symfony\Component\Finder\Finder();
    $finder
        ->files()
        ->name('*Bundle.php')
        ->notPath('/Tests/')
        ->notPath('/Test/')
        ->in(__DIR__ . '/../vendor')
        ->in(__DIR__ . '/../tests/bundles')
        ->in(__DIR__ . '/../src');

    $bundles = array();
    /** @var \Symfony\Component\Finder\SplFileInfo $file */
    foreach ($finder as $file) {

        $file = $file->getRealPath();
        $content = file_get_contents($file);
        preg_match('/^\s*\t*class ([a-z0-9_]+)/mi', $content, $className);
        if (isset($className[1]) && $className[1]){
            preg_match('/\s*\t*namespace ([a-zA-Z0-9_\\\\]+)/', $content, $namespace);
            $class = (count($namespace) > 1 ? $namespace[1] . '\\' : '' ) . $className[1];

            if ('Bundle' === $className[1]) {
                continue;
            }

            $bundles[] = $class;
        }
    }

    $bundles = array_unique($bundles);
    $systemBundles = array('Admin\\AdminBundle', 'Core\\CoreBundle', 'Users\\UsersBundle');

    foreach ($bundles as $bundleClass) {
        $bundle = new $bundleClass();
        if (in_array($bundleClass, $systemBundles) || !($bundle instanceof \Core\Bundle)) {
            continue;
        }

        $composer = $bundle->getComposer();
        $checked = in_array($bundleClass, Kryn::$config->getBundles()) ? 'checked="checked"' : '';

        ?>

        <tr>
            <td valign="top" width="30">
                <div style="padding-top: 5px;">
                    <input name="modules[<?php echo $bundleClass; ?>]" <?php echo $checked; ?> type="checkbox" value="1" />
                </div>
            </td>
            <td valign="top"><b><?php echo $composer['name'] ?></b></td>
            <td valign="top">
                <div style="color: gray; margin-bottom: 11px;">#<?php echo $bundleClass ?></div>
            </td>
        </tr>
        <tr>
            <td valign="top" colspan="3" style="border-bottom: 1px solid silver;">
                <div style="padding-left: 40px;">
                    <?php echo $composer['description'] ?>
                </div>
            </td>
        </tr>

        <?php
    }

}

function step2()
{
    $anyThingOk = true;
    ?>

    <h2>Checking requirements</h2>
    <br/>
    <ol>

        <li><b>PHP Version</b>
            <?php

            $t = explode("-", PHP_VERSION);
            $v = ($t[0]) ? $t[0] : PHP_VERSION;
            //5.3.2 because flock()
            if (!version_compare($v, "5.4.0") < 0) {
                print '<div style="color: red">PHP version too old.<br />';
                print "You need PHP version 5.4.0 or greater.<br />";
                print "Installed version: $v (" . PHP_VERSION . ")<br/></div>";
                $anyThingOk = false;
            } else {
                print '<div style="color: green">OK</div>';
            }
            ?>
        </li>

        <li><b>PHP Extensions</b>
            <?php

            if (!extension_loaded('mbstring')) {
                $anyThingOk = false;
                print '<div style="color: red">mbstring required.</div>';
            } else {
                print '<div style="color: green">mbstring OK</div>';
            }

            if (!extension_loaded('gd')) {
                $anyThingOk = false;
                print '<div style="color: red">libgd required.</div>';
            } else {
                print '<div style="color: green">libgd OK</div>';
            }

            if (!extension_loaded('zip')) {
                $anyThingOk = false;
                print '<div style="color: red">zip required.</div>';
            } else {
                print '<div style="color: green">zip OK</div>';
            }

            ?>
        </li>

        <li><b>Database PDO driver</b>
            <?php

            $drivers = array('mysql' => 'MySQL', 'pgsql' => 'PostgreSQL', 'sqlite' => 'SQLite', 'sqlsrv' => 'MSSQL');

            $oneFound = false;
            foreach ($drivers as $driver => $label) {
                $color = extension_loaded('pdo_' . $driver) ? 'green' : 'red';
                if ($color == 'green') {
                    $oneFound = true;
                }
                print '<div style="color: ' . $color . '">' . $label . '</div>';
            }
            if (!$oneFound) {
                print '<br /><div>There is no available pdo driver.</div>';
                $anyThingOk = false;
            }

            ?>
        </li>

    </ol>
    <br/>
    <a href="?step=1" class="ka-Button">Back</a>
    <?php

    if ($anyThingOk) {
        print '<a href="?step=3" class="ka-Button" >Next</a>';
    } else {
        print '<a href="?step=2" class="ka-Button" >Re-Check</a>';
    }

}

function step3()
{
    ?>

    <script type="text/javascript">

        window.addEvent('domready', function () {
            var driver = document.id('db_type');
            var server = document.id('db_server');
            var info = document.id('db_name_sqlite_info');
            var user = document.id('db_username');
            var passwd = document.id('db_password');
//            var port = document.id('db_port');
            var persis = document.id('db_persistent');

            driver.addEvent('change', function () {
                if (driver.value == 'sqlite') {
                    server.required = false;
                    user.required = false;
                    [server, passwd, user, persis].each(function (item) {
                        item.getParent('tr').setStyle('display', 'none');
                    });
                    info.setStyle('display', 'block');
                } else {
                    server.required = true;
                    user.required = true;
                    [server, passwd, user, persis].each(function (item) {
                        item.getParent('tr').setStyle('display', 'table-row');
                    });
                    info.setStyle('display', 'none');
                }
            });

            driver.fireEvent('change');

        });

        window.checkConfigEntries = function () {
            var ok = true;
            var driver = document.id('db_type');

            if (driver.value != 'sqlite') {
                if (document.id('db_server').value == '') {
                    document.id('db_server').highlight();
                    ok = false;
                }
                if (document.id('db_username').value == '') {
                    document.id('db_username').highlight();
                    ok = false;
                }
                if (document.id('db_name').value == '') {
                    document.id('db_name').highlight();
                    ok = false;
                }
            }

            if (ok) {
                document.id('status').set('html', '<span style="color:green;">Check data ...</span>');
                var req = document.id('form').toQueryString().parseQueryString();

                if (window.lcer) {
                    window.lcer.cancel();
                }

                window.lcer = new Request.JSON({url: 'install.php?step=checkConfig', onComplete: function (stat) {
                    if (stat != null && stat.res == true) {
                        location = '?step=4';
                    }
                    else if (stat != null) {
                        document.id('status').set('html',
                            '<span style="color:red;">Failed:<br />' + stat.error + '</span>');
                    }
                    else {
                        document.id('status').set('html',
                            '<span style="color:red;">Fatal Error. Please take a look in server logs.</span>');
                    }
                },
                onError: function (res) {
                    document.id('status').set('html',
                        '<span style="color:red;">Fatal Error. Please take a look in server logs.</span> Maybe this helps: <br />' +
                            res);
                }}).get(req);
            }
        }

        window.addSlave = function () {

            var tr = new Element('tr');

            var names = ['host', 'username', 'password', 'database', 'prefix'];
            for (var i = 0; i < 6; i++) {
                new Element('input', {name: 'slaves[' + names[i] + '][]', required: i == 1 ? null :
                    true, 'class': 'ka-Input', style: 'width: 100%'}).inject(new Element('td').inject(tr));
            }

            var remove = new Element('a', {text: 'X', href: 'javascript:;'}).inject(new Element('td').inject(tr));

            remove.addEvent('click', function () {
                tr.destroy();
            })

            tr.inject($('slaves'));

        }
    </script>

    <form id="form" autocomplete="off" onsubmit="checkConfigEntries(); return false;">

    <div style="padding: 15px 0">
        These settings are only the minimum settings to run Kryn.cms.<br/>
        To set more detailed settings, just login to the administration and open the settings window after the installation.
    </div>

    <h3>System</h3>

    <table style="width: 100%" cellpadding="3">
        <tr>
            <td width="450">Title*</td>
            <td>
                <input type="text" class="ka-Input-text" required name="systemTitle" value="<?php echo Kryn::$config->getSystemTitle() ?: 'Fresh installation'; ?>">
            </td>
        </tr>
        <tr>
            <td>Installation id*
                <div style="color: #aaa">No special characters.</div>
            </td>
            <td>
                <input type="text" class="ka-Input-text" required name="id"
                       value="<?php echo Kryn::$config->getId() ?: dechex((time() / 1000) / mt_rand(10, 100)); ?>">
            </td>
        </tr>
        <tr>
            <td>Timezone</td>
            <td>
                <select name="timezone">
                    <?php
                    $value = Kryn::$config->getTimezone() ?: 'Europe/Berlin';
                    $zones = timezone_identifiers_list();
                    foreach ($zones as $zone) {
                        echo "<option " . ($zone == $value ? 'selected="selected"' : '') . ">$zone</option>";
                    }
                    ?>
                </select>
            </td>
        </tr>

        <tr>
            <td>
                Display errors
                <div style="color: #aaa">Activates regular PHP error reporting.</div>
            </td>
            <td><input type="checkbox" <?php if (Kryn::$config->getErrors(true)->getDisplay()) echo 'checked="checked"'; ?> name="displayErrors" value="1"/></td>
        </tr>
        <tr>
            <td>
                Display beauty errors
                <div style="color: #aaa">A fancy error view with highlighted code view of the debug trace.</div>
            </td>
            <td><input type="checkbox" <?php if (Kryn::$config->getErrors()->getStackTrace()) echo 'checked="checked"'; ?> name="displayBeautyErrors" value="1"/></td>
        </tr>

        <tr>
            <td>
                Display detailed RESTful API Error information.
                <div style="color: #aaa">Means file name, line number and backstrace.</div>
            </td>
            <td><input type="checkbox" <?php if (Kryn::$config->getErrors()->getDisplayRest()) echo 'checked="checked"'; ?> name="displayDetailedRestErrors" value="1"/></td>
        </tr>
    </table>

    <h3>Local Filesystem</h3>

    <table style="width: 100%" cellpadding="3">

        <tr>
            <td colspan="2">
                <div style="color: #aaa;">Following settings are used only for modified files and new files created by
                    Kryn.cms.
                </div>
            </td>
        </tr>
        <tr>
            <td width="450">Default group owner
            </td>
            <td>
                <input type="text" class="ka-Input-text" name="fileGroupName" value="<?php echo Kryn::$config->getFile(true)->getGroupOwner(); ?>">
            </td>
        </tr>
        <tr>
            <td>Default group permission</td>
            <td>
                <select name="fileGroupPermission">
                    <option <?php if (Kryn::$config->getFile()->getGroupPermission() == 'rw') echo 'selected="selected"'; ?> value="rw">Read and Write</option>
                    <option <?php if (Kryn::$config->getFile()->getGroupPermission() == 'r') echo 'selected="selected"'; ?>value="r">Read</option>
                    <option <?php if (Kryn::$config->getFile()->getGroupPermission() == '') echo 'selected="selected"'; ?> value="">None</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Default everyone permission</td>
            <td>
                <select name="fileEveryonePermission">
                    <option <?php if (Kryn::$config->getFile()->getEveryonePermission() == '') echo 'selected="selected"'; ?> value="">None</option>
                    <option <?php if (Kryn::$config->getFile()->getEveryonePermission() == 'r') echo 'selected="selected"'; ?> value="r">Read</option>
                    <option <?php if (Kryn::$config->getFile()->getEveryonePermission() == 'rw') echo 'selected="selected"'; ?> value="rw">Read and Write</option>
                </select>
            </td>
        </tr>
        <tr>
            <td>Do not change file permission</td>
            <td>
                <input type="checkbox" <?php if (Kryn::$config->getFile()->getDisableModeChange()) echo 'checked="checked"'; ?> value="1" name="fileNoChangeMode"/>
            </td>
        </tr>
        <tr>
            <td>Temp directory
                <div style="color: #aaa">A path to a directory for temporary, local caching, propel build and smarty
                    compiling files. If empty, we search in the environment in this priority:<br/>
                    TMP, TEMP, TMPDIR or TEMPDIR. If enything is empty, we use php's function sys_get_temp_dir().
                </div>
            </td>
            <td>
                <input type="text" class="ka-Input-text" name="fileTemp" value="<?php echo Kryn::$config->getTempDir() ?>">
            </td>
        </tr>
    </table>

    <h3>Database</h3>

    <div style="color: #aaa">This is the main connection. You can define later more slave connections in the administration.</div>

    <table style="width: 100%" cellpadding="3">
        <tr>
            <td width="450">Database PDO driver *</td>
            <td><select required name="type" id="db_type">
                    <?php

                    $drivers = array(
                        'mysql' => 'MySQL',
                        'pgsql' => 'PostgreSQL',
                        'sqlite' => 'SQLite',
                        'sqlsrv' => 'MSSQL'
                    );

                    foreach ($drivers as $driver => $label) {
                        $enabled = extension_loaded('pdo_' . $driver) ? '' : 'disabled="disabled"';
                        $selected = $driver == Kryn::$config->getDatabase(true)->getMainConnection()->getType() ? 'selected="selected"' : '';
                        print "<option $enabled $selected value=\"$driver\">$label</option>";
                    }
                    ?>
                </select></td>
        </tr>

        <tr>
            <td>

                Persistent connections
                <div style="color: #aaa">You should probably deactivate this on the most low-cost and free web hoster.
                </div>
            </td>
            <td><input type="checkbox" <?php if (Kryn::$config->getDatabase(true)->getMainConnection()->getPersistent()) echo 'checked="checked"'; ?> id="db_persistent" name="persistent" value="1"/></td>
        </tr>
        <tr>
            <td>
                Host *
            </td>
            <td><input required type="text" class="ka-Input-text" name="server" id="db_server" value="<?php echo Kryn::$config->getDatabase()->getMainConnection()->getServer() ?>"/></td>
        </tr>
        <tr id="ui_username">
            <td>Username *</td>
            <td><input required type="text" class="ka-Input-text" name="username" id="db_username" value="<?php echo Kryn::$config->getDatabase()->getMainConnection()->getUsername() ?>"/></td>
        </tr>
        <tr id="ui_password">
            <td>Password</td>
            <td><input type="password" name="password" id="db_password" class="ka-Input-text" value="<?php echo Kryn::$config->getDatabase()->getMainConnection()->getPassword(); ?>"/></td>
        </tr>
        <tr id="ui_db">
            <td>
                Database/Schema name *
                <div id="db_name_sqlite_info" style="display: none; color: #aaa">For SQLite enter here the file name
                </div>
            </td>
            <td><input required type="text" class="ka-Input-text" name="db" id="db_name" value="<?php echo Kryn::$config->getDatabase()->getMainConnection()->getName() ?>"/></td>
        </tr>
        <tr>
            <td>Table Prefix
                <div style="color: #aaa">
                    Please use only a lowercase string.<br />
                    In the next step all tables with a name that starts with this prefix WILL BE DROPPED!<br/>
                    If you don't enter a prefix ALL TABLES WILL BE REMOVED in this database/schema.

                </div>
            </td>
            <td><input style="width: 80px" type="text" class="ka-Input-text" name="prefix" id="db_prefix"
                       value="<?php echo Kryn::$config->getDatabase()->getPrefix() ?: 'kryn_' ?>"/></td>
        </tr>
        <tr>
            <td colspan="2">
                <br/>
                * Required fields.
            </td>
        </tr>
    </table>

    <h3>Other</h3>
    <table style="width: 100%" cellpadding="3">
        <tr>
            <td width="450">
                Rename install.php to random name at the end of the installation.
                <div style="color: #aaa">Highly recommended for security reasons.</div>
            </td>
            <td><input type="checkbox" checked="checked" name="rename" value="1"/></td>
        </tr>
    </table>
    <div id="status" style="padding: 4px;"></div>
    <br/>
    <a href="?step=2" class="ka-Button">Back</a>
    <input type="submit" class="ka-Button" value="Test connection and write config.php"/>
    </form>

<?php
}

?>
</div>
</body>
</html>
