<?php

/*
* This file is part of Kryn.cms.
*
* (c) Kryn.labs, MArc Schmidt <marc@kryn.org>
*
* To get the full copyright and license information, please view the
* LICENSE file, that was distributed with this source code.
*/

if (function_exists('mb_internal_encoding'))
  mb_internal_encoding("UTF-8");

error_reporting(E_ALL ^ E_NOTICE);

use Core\Kryn;
use Core\File;
use Core\SystemFile;

$GLOBALS['krynInstaller'] = true;
define('PATH', dirname(__FILE__).'/');
define('PATH_CORE', 'core/');
define('PATH_MODULE', 'module/');
define('PATH_MEDIA', 'media/');
define('PATH_MEDIA_CACHE', 'media/cache/');

@set_include_path( '.' . PATH_SEPARATOR . PATH . 'lib/pear/' . PATH_SEPARATOR . get_include_path());

# Load very important classes.
include(PATH_CORE . 'Kryn.class.php');
include('lib/propel/runtime/lib/Propel.php');

if (file_exists('config.php'))
  Kryn::$config = require('config.php');

if (!is_array(Kryn::$config)) Kryn::$config = array();

require('core/bootstrap.autoloading.php');

include(PATH_CORE.'global/misc.global.php');
include(PATH_CORE.'global/database.global.php');
include(PATH_CORE.'global/template.global.php');
include(PATH_CORE.'global/internal.global.php');
include(PATH_CORE.'global/framework.global.php');
include(PATH_CORE.'global/exceptions.global.php');

$lang = 'en';
$cfg = array();

@ini_set('display_errors', 1);

if( $_REQUEST['step'] == 'checkConfig' )
    checkConfig();

if( $_REQUEST['step'] == '5' ){

    spl_autoload_register(function ($pClass) {
      if (substr($pClass, 0, 1) == '\\')
        $pClass = substr($pClass, 1);
      foreach (Kryn::$extensions as $extension){
        if (file_exists($clazz = 'module/'.$extension.'/model/'.$pClass.'.php')){
            include $clazz;
            return true;
        }
      }
    });
    $modules = array('admin', 'users');
    step5Init();
}


header("Content-Type: text/html; charset=utf-8");


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de" dir="ltr">
  <head>
    <title>Kryn.cms installation</title>
      <link rel="stylesheet" type="text/css" href="media/core/css/normalize.css"  />
      <link rel="stylesheet" type="text/css" href="media/admin/css/ui/ka.Button.css"  />
      <link rel="stylesheet" type="text/css" href="media/admin/css/ui/ka.Input.css"  />

      <style type="text/css">
      body {
          line-height: 150%;
          font-size: 13px;
          margin: 0px;
          font-family: Verdana, Sans;
          background-color: #22628d;
          padding: 0px;
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
      
      td {
        vertical-align: top;
      }

      a, a:link {
        text-decoration: none;
        color: gray;
      }

      body {
        text-align: center;
        font-size: 11px;
        font-family: Verdana,Arial,sans-serif;
      }

      table {
        font-size: 11px;
        margin: 5px;
        margin-left: 10px;
        width: 400px;
        color: #555;
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
        -moz-border-radius: 10px;
        -webkit-border-radius: 10px;
        padding: 45px 35px;
        background-color: #f1f1f1;
        position: relative;
        color: #333;
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
        -moz-border-radius-topleft: 10px;
        -moz-border-radius-bottomleft: 10px;
        -webkit-border-top-left-radius: 10px;
        -webkit-border-bottom-left-radius: 10px;
        border-top-left-radius: 3px;
        border-bottom-left-radius: 3px;
        position: absolute;
        top: 27px;
        left: -150px;
        width: 150px;
        background-color: #f1f1f1;
        margin-bottom: 15px;
      }
      
      h2.main {
      	font-size: 12px;
      	line-height:13px;
      	position: absolute;
      	top: 0px;
      	left: 35px;
      	right: 35px;
      	border-bottom: 1px dashed #ddd;
      	padding-bottom: 5px;
      	color: gray;
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

      .breaker { clear: both }

    </style>
    <script type="text/javascript" src="media/core/mootools-core.js"></script>
    <script type="text/javascript" src="media/core/mootools-more.js"></script>
    <link rel="SHORTCUT ICON" href="media/admin/images/favicon.ico" />
  </head>
  <body>
    <img class="logo" src="media/core/images/logo_white.png" />
    <div class="wrapper">
    <h2 class="main">Installation</h2>
<?php

$step = 1;
if( !empty($_REQUEST['step']) )
    $step = $_REQUEST['step'];
?>

<div class="step">
    <a href="javascript:;" <?php if( $step == 1 ) echo 'class="active"'; ?>>1. Start</a>
    <a href="javascript:;" <?php if( $step == 2 ) echo 'class="active"'; ?>>2. Requirements</a>
    <a href="javascript:;" <?php if( $step == 3 ) echo 'class="active"'; ?>>3. Configuration</a>
    <a href="javascript:;" <?php if( $step == 4 ) echo 'class="active"'; ?>>4. Package</a>
    <a href="javascript:;" <?php if( $step == 5 ) echo 'class="active"'; ?>>5. Installation</a>
    <div class="breaker"></div>
</div>

<?php

switch( $step ){
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

function checkConfig(){
	global $cfg;
	
	$type = $_REQUEST['db_type'];


  if ((file_exists('config.php') && !is_writable('config.php'))
      || !file_exists('config.php') && !is_writable('.')
     ){
      $res['res'] = false;
      $res['error'] = './config.php is not writable.';
      die(json_encode($res));
  }
	
	$cfg = array(
		  "db_server"	  => $_REQUEST['server'],
	    "db_user"		  => $_REQUEST['username'],
	    "db_password"   => $_REQUEST['password'],
	    "db_name"     => $_REQUEST['db'],
	    "db_prefix"   => $_REQUEST['prefix'],
	    "db_type"     => $_REQUEST['type'],
	    "db_pdo"      => $_REQUEST['pdo']
	);

	$res = array('res' => true);

  $dsn = $cfg['db_type'].':dbname='.$cfg['db_name'].';host='.$cfg['db_server'];

  try {
      $connection = new PDO($dsn, $cfg['db_user'], $cfg['db_password']);
  } catch (PDOException $e) {
      $res['res'] = false;
      $res['error'] = $dsn.': '.$e->getMessage();
  }

  if ($res['res'] == true){


      $timezone = $_REQUEST['timezone'];
      if (!$timezone) $timezone = 'Europe/Berlin';

      $systemTitle = $_REQUEST['systemTitle'];
      if (!$systemTitle) $systemTitle = "Fresh install";

      $cfg = array(
          'id' => $_REQUEST['id'],
          'database' => array(
            'server' => $_REQUEST['server'],
            'user'   => $_REQUEST['username'],
            'password' => $_REQUEST['password'],
            'name'   => $_REQUEST['db'],
            'prefix' => $_REQUEST['prefix'],
            'type'   => $_REQUEST['type'],
            'persistent'   => $_REQUEST['persistent'],
          ),

          'fileGroupPermission'      => $_REQUEST['fileGroupPermission'],
          'fileGroupName'      => $_REQUEST['fileGroupName'],
          'fileEveryonePermission'   => $_REQUEST['fileEveryonePermission'],

          'cache' => array(
            'class' => '\Core\Cache\Files'
          ),

          "passwordHashCompat" => 0,
          "passwordHashKey"    => Core\Client\ClientAbstract::getSalt(32),

          "displayErrors"        => $_REQUEST['displayErrors'],
          "displayDetailedRestErrors"    => $_REQUEST['displayDetailedRestErrors'],
          "logErrors"            => 0,
          "systemTitle"          => $systemTitle,
          "client"                 => array(
            "class"              => "\Core\Client\KrynUsers",
            "config"             => array(
              "emailLogin"       => false,
              "store"            => array(
                "class"          => "database",
                "config"         => array()
              )

            )
          ),
          "timezone"            => $timezone
      );
      $config = '<?php return '. var_export($cfg,true) .'; ?>';

      Kryn::$config = $cfg;

      delDir(Kryn::getTempFolder().'propel');

      $f = \Core\SystemFile::setContent('config.php', $config);

      if (!$f){
          $res['error'] = 'Can not open file config.php - please change the permissions.';
          $res['res'] = false;
      }
  }

  die(json_encode($res));
}

function welcome(){
?>

<h2>Thanks for choosing Kryn.cms!</h2>
<br />
Your installation folder is <strong style="color: gray;"><?php echo getcwd(); ?></strong>
<br />
<br />
<b>Kryn.cms license</b><br />
<br />
<div style="height: 350px; background-color: white; padding: 5px; overflow: auto; white-space: pre;">
    <?php $f = fopen("LICENSE", "r"); if($f) while (!feof($f)) print fgets($f, 4096) ?>
</div>
<br /><br />
<b style="color: gray">Kryn.cms comes with amazing additional third party software.</b><br />
      Please respect all of their licenses too:<br />
<br />
<table style="width: 100%" cellpadding="3">
    <tr>
    <th width="160">Name</th>
    <th width="250">Author/Link</th>
    <th>License</th>
</tr>
<tr>
    <td  width="160">Mootools</td>
    <td  width="250"><a href="http://mootools.net/">mootools.net</a></td>
    <td>&raquo; <a href="http://www.opensource.org/licenses/mit-license.php">MIT license</a></td>
</tr>
<tr>
    <td  width="160">Mooeditable fork</td>
    <td  width="250"><a href="https://github.com/MArcJ/mooeditable">https://github.com/MArcJ/mooeditable</a></td>
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
    <td>&raquo; <a href="lib/codemirror/LICENSE">MIT-style license</a></td>
</tr>
<tr>
    <td>normalize.css</td>
    <td><a href="http://necolas.github.com/normalize.css/">necolas.github.com/normalize.css</a></td>
    <td>&raquo; <a href="lib/codemirror/LICENSE">MIT License</a></td>
</tr>

<tr>
    <td>Silk icon set 1.3</td>
    <td><a href="http://www.famfamfam.com/lab/icons/silk/">www.famfamfam.com/lab/icons/silk/</a></td>
    <td>&raquo; <a href="http://creativecommons.org/licenses/by/2.5/">Creative Commons Attribution 2.5 License.</a></td>
</tr>


<tr>
    <td>[PEAR] JSON</td>
    <td><a href="http://pear.php.net/package/Services_JSON">PEAR/Services_JSON</a></td>
    <td>&raquo; <a href="http://www.opensource.org/licenses/bsd-license.php">BSD</a></td>
</tr>

<tr>
    <td>[PEAR] Archive</td>
    <td><a href="http://pear.php.net/package/File_Archive/">PEAR/File_Archive</a></td>
    <td>&raquo; <a href="http://www.gnu.org/licenses/lgpl.html">LGPL</a></td>
</tr>

<tr>
    <td>[PEAR] MIME</td>
    <td><a href="http://pear.php.net/package/MIME_Type">PEAR/MIME_Type</a></td>
    <td>&raquo; <a href="http://www.php.net/license/3_0.txt">PHP License 3.0</a></td>
</tr>

<tr>
    <td>[PEAR] Structures_Graph</td>
    <td><a href="http://pear.php.net/package/MIME_Type">PEAR/Structures_Graph</a></td>
    <td>&raquo; <a href="http://www.gnu.org/licenses/lgpl.html">LGPL</a></td>
</tr>
<tr>
    <td  width="160">[Mootools plugin] Stylesheet</td>
    <td  width="250"><a href="http://mifjs.net">Anton Samoylov</a></td>
    <td>&raquo; <a href="http://mifjs.net/license.txt">MIT-Style License</a></td>
</tr>


<tr>
    <td colspan="3">IconSet:
    </td>
</tr>
<tr>
    <td colspan="3" style="white-space: pre; background-color: white;"><?php print file_get_contents('media/admin/icons/license.txt'); ?>
    </td>
</tr>

</table>
<a href="?step=2" class="ka-Button" >Accept</a>

<?php
}

function step5Failed($pError){
    $msg = array('error' => $pError);
    print json_encode($msg);
    exit;
}

function step5Done($pMsg){
    $msg = array('data' => $pMsg);
    print json_encode($msg);
    exit;
}


    //fire pre scripts
    function step5_1(){
        global $modules;

        $manager = new Admin\Module\Manager;

        foreach ($modules as $module){
            try {
                $manager->installPre($module);
            } catch (Exception $e){
                step5Failed($e->getMessage());
            } catch (RestException $e){
                step5Failed($e->getMessage());
            } catch (PDOException $e){
                step5Failed($e->getMessage());
            }
        }
        step5Done(true);
    }

    //fire extract scripts
    function step5_2(){
        global $modules;
        $manager = new Admin\Module\Manager;

        //fire extract scripts, since we've already all files at place
        foreach ($modules as $module){
            try {
                $manager->installPre($module);
            } catch (Exception $e){
                step5Failed($e->getMessage());
            } catch (RestException $e){
                step5Failed($e->getMessage());
            } catch (PDOException $e){
                step5Failed($e->getMessage());
            }
        }
        step5Done(true);
    }

    //write propel build environment
    function step5_3(){

        \Core\SystemFile::delete('propel');

        try {
            //create the propel config
            \Core\PropelHelper::writeXmlConfig();
            \Core\PropelHelper::writeBuildPorperties();
            \Core\PropelHelper::collectSchemas();
        } catch (Exception $e){
            step5Failed($e->getMessage());
        }
        step5Done(true);
    }


    //Write propel model classes
    function step5_4(){

        try {
            $res = \Core\PropelHelper::generateClasses();
        } catch (Exception $e){
            step5Failed($e->getMessage());
        }
        step5Done($res);
    }

    //Update database schema
    function step5_5(){

        try {
            $res = \Core\PropelHelper::updateSchema();
        } catch (Exception $e){
            step5Failed($e->getMessage());
        }
        step5Done($res);
    }

    //Fire package-database scripts
    function step5_6(){
        global $modules;

        $manager = new \Admin\Module\Manager;

        foreach ($modules as $module){
            try {
                $manager->installDatabase($module);
            } catch (Exception $e){
                step5Failed($e->getMessage());
            } catch (RestException $e){
                step5Failed($e->getMessage());
            } catch (PDOException $e){
                step5Failed($e->getMessage());
            }
        }
        step5Done(true);
    }

    //Fire package-post scripts
    function step5_7(){
        global $modules;

        $manager = new \Admin\Module\Manager;

        foreach ($modules as $module){
            try {
                $manager->installPost($module);
            } catch (Exception $e){
                step5Failed($e->getMessage());
            } catch (RestException $e){
                step5Failed($e->getMessage());
            } catch (PDOException $e){
                step5Failed($e->getMessage());
            }
        }
        step5Done(true);
    }

    //cleanup
    function step5_8(){

        \Core\PropelHelper::cleanup();

        //load all configs
        \Core\Kryn::loadConfigs();

        \Admin\Utils::clearCache();
        
        step5Done(true);
    }

    function step5Init(){

        $subStep = $_GET['substep']+0;
        Kryn::$config = require('config.php');

        define('pfx', Kryn::$config['database']['prefix']);

        if ($subStep == 0 && $_POST['modules']){

            $dir = opendir( PATH_MODULE );
            if(! $dir ) return;
            while (($file = readdir($dir)) !== false){
                if( $file != '..' && $file != '.' && $file != 'admin' && $file != 'users' ){
                    if ($_POST['modules'][$file])
                        $modules[] = $file;
                }
            }

            Kryn::$config['activeModules'] = $modules;

            \Core\SystemFile::setContent('config.php', "<?php\nreturn ".var_export(Kryn::$config, true).";\n?>");
        }


        if (file_exists($file = 'propel-config.php')){
            $propelConfig = include($file);
            Core\Kryn::$propelClassMap = $propelConfig['classmap'];
        }

        if ($subStep >= 6){

            \Propel::init(\Core\PropelHelper::getConfig());
        }

        if ($subStep > 0){
            $fn = 'step5_'.$subStep;
            $fn();
            exit;
        }

    }


function step5(){
?>

<br />
<h2>Installation</h2>
<?php
    
//    if( !rename( 'install.php', 'install.doNotRemoveIt.'.rand(123,5123).rand(585,2319293).rand(9384394,313213133) ) ){
//        print '<div style="margin: 25px; border: 2px solid red; padding: 10px; padding-left: 25px;">
//        	Can not rename install.php - please remove or rename the file for security reasons!
//        	</div>';
//    }
?>
    <div id="progress">
        <div id="progressMessage">Pending ...</div>
        <div id="progressBar">
            <div id="progressBarIn" style="width: 0px"></div>
            <div id="progressBarText">0%</div>
        </div>
        <div id="progressError" style="display: none;"></div>
    </div>
    <div id="installDone" style="display: none;">
        <h3 style="color: green;">Installation successful.</h3>
        Login: admin<br/>
        Password: admin<br/>
        <br/>
        Please change your password as fast as possible!<br />
        <br/>
        Go to:
        <a href="./">Frontend</a> |
        <a href="./admin">Administration</a>
    </div>

    <script type="text/javascript">
        window.addEvent('domready', function(){

            var steps = [
                'Execute module pre-scripts ...',
                'Execute module extract-scripts ...',
                'Write propel build environment ...',
                'Write propel model classes ...',
                'Update database schema ...',
                'Execute module database-scripts ...',
                'Execute module post-scripts ...',
                'Cleanup ...'
            ];

            var currentStep = 0;
            var handleNextStep;

            document.id('progressBarIn').set('tween', {duration: 250});

            handleNextStep = function(){
                currentStep++;

                document.id('progressMessage').set('text', steps[currentStep-1]);

                new Request.JSON({
                    url: 'install.php?step=5&substep='+currentStep,
                    noCache: 1,
                    onComplete: function(pResult){

                        if (pResult.error){
                            document.id('progressError').setStyle('display', 'block');
                            document.id('progressError').set('html', pResult.error);
                            new Element('h2', {
                                style: 'color: red',
                                text: 'Error'
                            }).inject(document.id('progressError'), 'top');
                        } else {

                            width = (currentStep / (steps.length/700));
                            percent = (currentStep / (steps.length/100));
                            document.id('progressBarText').set('text', percent.toFixed(0)+'%');
                            document.id('progressBarIn').tween('width', width);

                            if (steps.length == currentStep){
                                document.id('progress').setStyle('display', 'none');
                                document.id('installDone').setStyle('display', 'block');
                            } else {
                                handleNextStep.delay(250);
                            }
                        }
                    },
                    onError: function(pResult){

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

function step4(){
?>

<br />
Your installation file contains following modules:<br />
<br />
<br />
<form action="?step=5" method="post" id="form.modules">

<table style="width: 98%" class="modulelist" cellpadding="4">
<?php

    $systemModules = array('core','admin','users');
    buildModInfo( $systemModules );

    $dir = opendir( PATH_MODULE."" );
    $modules = array();
    if(! $dir ) return;
    while (($file = readdir($dir)) !== false){
        if( $file != '..' && $file != '.' && $file != '.svn' && (array_search($file, $systemModules) === false) ){
            $modules[] = $file;
        }
    }
    buildModInfo( $modules );
?>
</table>
</form>
<b style="color: red;">All database tables we install will be dropped in the next step!</b><br /><br/>
<a href="?step=3" class="ka-Button" >Back</a>
<a href="javascript: document.id('form.modules').submit();" class="ka-Button" >Install!</a>
<?php
}

function buildModInfo( $modules ) {
    global $lang;
    foreach( $modules as $module ){
         $config = \Admin\Module\Manager::loadInfo( $module );
         $version = $config['version'];
         $title = $config['title'];
         $desc = $config['desc'];

         $checkbox = '<input name="modules['.$module.']" checked type="checkbox" value="1" />';
         if( $config['system'] == "1"){
             $checkbox = '<input name="modules['.$module.']" checked disabled type="checkbox" value="1" />';
         }
        ?>
        <tr>
        	<td valign="top" width="30"><?php print $checkbox ?></td>
        	<td valign="top" width="150"><b><?php print $title ?></b></td>
        	<td valign="top" width="90"><div style="color: gray; margin-bottom: 11px;">#<?php print $module ?></div></td>
        	<td valign="top" >
        	<?php print $desc ?>
        	</td>
        </tr>
        <?php
    }

}

function step2(){
    $anyThingOk = true;
    Kryn::initConfig(); 
?>

<h2>Checking requirements</h2>
<br />
<ol>

    <li><b>PHP Version</b>
    <?php

        $t = explode("-", PHP_VERSION);
        $v = ( $t[0] ) ? $t[0] : PHP_VERSION;
        //5.3.2 because flock()
        if(! version_compare($v, "5.3.2") < 0 ){
            print '<div style="color: red">PHP version tot old.<br />';
            print "You need PHP version 5.3.0 or greater.<br />";
            print "Installed version: $v (".PHP_VERSION.")<br/></div>";
            $anyThingOk = false;
        } else {
            print '<div style="color: green">OK</div>';
        }
        ?>
    </li>


    <li><b>PHP Extensions</b>
    <?php

        if(!extension_loaded('mbstring')){
            $anyThingOk = false;
            print '<div style="color: red">mbstring required.</div>';
        } else {
            print '<div style="color: green">mbstring OK</div>';
        }

        if(!extension_loaded('gd')){
            $anyThingOk = false;
            print '<div style="color: red">libgd required.</div>';
        } else {
            print '<div style="color: green">libgd OK</div>';
        }

        ?>
    </li>

    <li><b>Database PDO driver</b>
        <?php

        $drivers = array('mysql' => 'MySQL', 'pgsql' => 'PostgreSQL', 'sqlite' => 'SQLite', 'sqlsrv' => 'MSSQL');

        $oneFound = false;
        foreach ($drivers as $driver => $label){
            $color = extension_loaded('pdo_'.$driver)?'green':'red';
            if ($color == 'green')
                $oneFound = true;
            print '<div style="color: '.$color.'">'.$label.'</div>';
        }
        if (!$oneFound){
            print '<br /><div>There is no available pdo driver.</div>';
            $anyThingOk = false;
        }

?>
    </li>

    <li><b>Public cache directory</b>
        <?php

        $dir = 'media/cache';

        if (!file_exists($dir))
            if (!mkdir($dir)){
                print "<div style='color: red'>Can not create media/cache folder.</div>";
            }

        if (is_writable($dir)){
          print "<div style='color: green'>$dir is writeable.</div>";
        } else {
          $anyThingOk = false;
          print "<div style='color: red'>$dir is not writeable.</div>";
        }

?>
    </li>


    <li><b>Temp directory</b><br/>
      <div style="color: gray; padding-bottom: 6px;">
        This directory is used for file caches, smarty compiled templates, propel build<br/>
        and other temporarily files. To set a different folder, you can overwrite the<br/>
        environment var in this priority:<br/>
        TMP, TEMP, TMPDIR or TEMPDIR
      </div>
<?php
    
    $tempFolder = Kryn::getTempFolder(false);

    if (is_writable($tempFolder)){
      print "<div style='color: green'>$tempFolder is writeable.</div>";
      ?>
      <div style="color: gray; padding-bottom: 6px;">
        In the next step, you can set a installation id.<br/>
        This is then be used as sub folder in temp to make these files unique. 
      </div>
      <?php
    } else {
      $anyThingOk = false;
      print "<div style='color: red'>$tempFolder is not writeable.</div>";
    }

    ?>

    </li>

    <li><b>config.php permission</b><br/>
<?php
    
    $configFile = 'config.php';

    if (!file_exists($configFile))
      if (!@touch($configFile)){
        print "<div style='color: red'>Can not create $configFile.</div>";
      }

    if (is_writable($configFile)){
      print "<div style='color: green'>$configFile exists and is writeable.</div>";
    } else {
      $anyThingOk = false;
      print "<div style='color: red'>$configFile is not writeable.</div>";
    }

    ?>
    </li>



</ol>
    <br />
    <a href="?step=1" class="ka-Button" >Back</a>
    <?php

    if ($anyThingOk) {
        print '<a href="?step=3" class="ka-Button" >Next</a>'; 
    } else {
        print '<a href="?step=2" class="ka-Button" >Re-Check</a>';
    }

}

function step3(){
    ?>

<script type="text/javascript">
    window.checkConfigEntries = function(){
        var ok = true;
        
        if( document.id('db_server').value == '' ){ document.id('db_server').highlight(); ok = false; }
        if( document.id('db_prefix').value == '' ){ document.id('db_prefix').highlight(); ok = false; }
        if( document.id('db_username').value == '' ){ document.id('db_username').highlight(); ok = false; }
        if( document.id('db_name').value == '' ){ document.id('db_name').highlight(); ok = false; }

        if( ok ){
            document.id( 'status' ).set('html', '<span style="color:green;">Check data ...</span>');
            var req = document.id('form').toQueryString().parseQueryString();

            if (window.lcer) window.lcer.cancel();

            window.lcer = new Request.JSON({url: 'install.php?step=checkConfig', onComplete: function(stat){
                if( stat != null && stat.res == true )
                   location = '?step=4';
                else if( stat != null )
                    document.id('status').set('html', '<span style="color:red;">Failed:<br />'+stat.error+'</span>');
                else
                    document.id('status').set('html', '<span style="color:red;">Fatal Error. Please take a look in server logs.</span>');
            },
            onError: function(res){
                document.id('status').set('html', '<span style="color:red;">Fatal Error. Please take a look in server logs.</span> Maybe this helps: <br />'+res);
            }}).post(req);
        }
    }

    window.addSlave = function(){

      var tr = new Element('tr');

      var names = ['host', 'port', 'username', 'password', 'database', 'prefix'];
      for (var i=0; i<6;i++)
        new Element('input', {name: 'slaves['+names[i]+'][]', required: i==1?null:true, 'class': 'ka-Input', style: 'width: 100%'}).inject(new Element('td').inject(tr));

      var remove = new Element('a', {text: 'X', href: 'javascript:;'}).inject(new Element('td').inject(tr));

      remove.addEvent('click', function(){
        tr.destroy();
      })

      tr.inject($('slaves'));

    }
</script>

<form id="form" autocomplete="off" onsubmit="checkConfigEntries(); return false;">

  These settings are only the minimum settings to run Kryn.cms.<br/>
  To set more detailed settings later, just login to the administration and open the settings window.

<h3>System</h3>

<table style="width: 100%" cellpadding="3">
    <tr>
        <td width="250">Title</td>
        <td>
            <input type="text" class="ka-Input" required name="systemTitle" value="Fresh installation">
        </td>
    </tr>
    <tr>
        <td width="250">Installation id</td>
        <td>
            <input type="text" class="ka-Input" required name="id" value="<?php echo dechex((time() / 1000) / mt_rand(10, 100)); ?>">
        </td>
    </tr>
    <tr>
        <td width="250">Timezone</td>
        <td>
            <select name="timezone">
                <?php
                    $zones = timezone_identifiers_list();
                    foreach ($zones as $zone){
                        echo "<option ".($zone=='Europe/Berlin'?'selected="selected"':'').">$zone</option>"; 
                    }
                ?>
            </select>
        </td>
    </tr>

    <tr>
        <td>
            Display errors
        </td>
        <td><input type="checkbox" checked="checked" name="displayErrors" value="1" /></td>
    </tr>
    <tr>
        <td>
            Display detailed RESTful API Error information.
            <div style="color: gray">Means file name, line number and backstrace.</div>
        </td>
        <td><input type="checkbox" checked="checked" name="displayDetailedRestErrors" value="1" /></td>
    </tr>
</table>


<h3>Local Filesystem</h3>

<table style="width: 100%" cellpadding="3">
    <tr>
        <td width="250">Default group name
            <div style="color: silver">Let it empty, to change no group.</div>
        </td>
        <td>
            <input type="text" class="ka-Input" name="fileGroupName" value="">
        </td>
    </tr>
    <tr>
        <td width="250">Default group permission</td>
        <td>
            <select name="fileGroupPermission">
                <option value="rw">Read and Write</option>
                <option value="r">Read</option>
                <option value="-">None</option>
            </select>
        </td>
    </tr>
    <tr>
        <td width="250">Default everyone permission</td>
        <td>
            <select name="fileEveryonePermission">
                <option value="-">None</option>
                <option value="r">Read</option>
                <option value="rw">Read and Write</option>
            </select>
        </td>
    </tr>
</table>

<h3>Database</h3>



<table style="width: 100%" cellpadding="3">
 	<tr>
        <td width="250">Database PDO driver</td>
        <td><select required name="type" id="db_type">
<?php

            $drivers = array('mysql' => 'MySQL', 'pgsql' => 'PostgreSQL', 'sqlite' => 'SQLite', 'sqlsrv' => 'MSSQL');

            foreach ($drivers as $driver => $label){
                $enabled = extension_loaded('pdo_'.$driver)?'':'disabled="disabled"';
                print "<option $enabled value=\"$driver\">$label</option>";
            }
?>
        </select></td>
    </tr>

    <tr>
        <td>
          Persistent connections
        </td>
        <td><input type="checkbox" checked="checked" name="persistent" value="1" /></td>
    </tr>
    <tr>
        <td>
          Host
        </td>
        <td><input required type="text" class="ka-Input" name="server" id="db_server" value="localhost" /></td>
    </tr>
    <tr>
        <td>
          Port
          <div style="color: silver">Empty for default</div>
        </td>
        <td><input type="text" class="ka-Input" style="width:50px" name="port" value="" /></td>
    </tr>
    <tr id="ui_username">
        <td>Username</td>
        <td><input required type="text" class="ka-Input" name="username" id="db_username" /></td>
    </tr>
    <tr id="ui_password">
        <td>Password</td>
        <td><input type="password" name="password" id="db_password" class="ka-Input" /></td>
    </tr>
    <tr id="ui_db">
        <td>
        	Database name
        </td>
        <td><input required type="text" class="ka-Input" name="db" id="db_name" /></td>
    </tr>
    <tr>
        <td>Table Prefix
	        <div style="color: silver">
	        	Please use only a lowercase string.
	        </div></td>
        <td><input required style="width: 80px" type="text" class="ka-Input" name="prefix" id="db_prefix" value="kryn_" /></td>
    </tr>
    <tr>
        <td colspan="2">
            <br />
            More settings are available after the installation in the administration area.
        </td>
    </tr>
</table>
<div id="status" style="padding: 4px;"></div>
<br />
<a href="?step=2" class="ka-Button" >Back</a>
<input type="submit" class="ka-Button" value="Test connection and write config.php" />
</form>


<?php
}

?>
    <script type="text/javascript" src="media/core/js/bgNoise.js"></script>
    </div>
  </body>
</html>
