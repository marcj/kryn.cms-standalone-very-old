<?php

/*
* This file is part of Kryn.cms.
*
* (c) Kryn.labs, MArc Schmidt <marc@kryn.org>
*
* To get the full copyright and license information, please view the
* LICENSE file, that was distributed with this source code.
*/

header("Content-Type: text/html; charset=utf-8");

$GLOBALS['krynInstaller'] = true;
define('PATH', dirname(__FILE__).'/');
define('PATH_CORE', 'core/');
define('PATH_MODULE', 'module/');
define('PATH_MEDIA', 'media/');

@set_include_path( '.' . PATH_SEPARATOR . PATH . 'lib/pear/' . PATH_SEPARATOR . get_include_path());

# Load very important classes.
include(PATH_CORE . 'Kryn.class.php');
include('lib/propel/runtime/lib/Propel.php');

require('core/bootstrap.autoloading.php');

include(PATH_CORE.'misc.global.php');
include(PATH_CORE.'database.global.php');
include(PATH_CORE.'template.global.php');
include(PATH_CORE.'internal.global.php');
include(PATH_CORE.'framework.global.php');
$lang = 'en';
$cfg = array();


@ini_set('display_errors', 1);
error_reporting(E_ALL ^ E_NOTICE);

if( $_REQUEST['step'] == 'checkDb' )
    checkDb();

if( $_REQUEST['step'] == '5' ){
    $modules = array('admin', 'users');
    step5Init();
}

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="de" lang="de" dir="ltr">
  <head>
    <title>Kryn.cms installation</title>
      <link rel="stylesheet" type="text/css" href="media/admin/css/ka.Button.css"  />

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

      input.text {
        border: 1px solid silver;
        width: 250px;
        text-indent: 4px;
      }

      .wrapper {
        text-align: left;
        margin: auto;
        width: 700px;
        left: 60px;
        -moz-border-radius: 10px;
        -webkit-border-radius: 10px;
        padding: 45px 35px;
        background-color: #f6f6f6;
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
        background-color: #f6f6f6;
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
    <script type="text/javascript" src="media/kryn/mootools-core.js"></script>
    <link rel="SHORTCUT ICON" href="media/admin/images/favicon.ico" />
  </head>
  <body>
    <img class="logo" src="media/kryn/images/logo_white.png" />
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
    <a href="javascript:;" <?php if( $step == 3 ) echo 'class="active"'; ?>>3. Database</a>
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

function checkDb(){
	global $cfg;
	
	$type = $_REQUEST['type'];
	
	$cfg = array(
		"db_server"		=> $_REQUEST['server'],
	    "db_user"		=> $_REQUEST['username'],
	    "db_passwd"		=> $_REQUEST['passwd'],
	    "db_name"		=> $_REQUEST['db'],
	    "db_prefix"		=> $_REQUEST['prefix'],
	    "db_type"		=> $_REQUEST['type'],
	    "db_pdo"		=> $_REQUEST['pdo']
	);

	$res = array('res' => true);


    $path = dirname($_SERVER['REQUEST_URI']);
    if( $path == '\\' ) $path = '/';
    if( substr($path, 0, -1) != '/' )
        $path .= '/';
    $path = str_replace('//', '/', $path);

    $timezone = @date_default_timezone_get();
    if( !$timezone )
        $timezone = 'Europe/Berlin';

    $dsn = $cfg['db_type'].':dbname='.$cfg['db_name'].';host='.$cfg['db_server'];

    try {
        $connection = new PDO($dsn, $cfg['db_user'], $cfg['db_passwd']);
    } catch (PDOException $e) {
        $res['res'] = false;
        $res['error'] = $e->getMessage();
    }

    if( $res['res'] == true ){
        $cfg = array(
        
            'db_server' => $_REQUEST['server'],
            'db_user'   => $_REQUEST['username'],
            'db_passwd' => $_REQUEST['passwd'],
            'db_name'   => $_REQUEST['db'],
            'db_prefix' => $_REQUEST['prefix'],
            'db_type'   => $_REQUEST['type'],
            "cache_type"   => "files",
            "media_cache"    => "cache/media/",
            "display_errors" => "0",
            "log_errors"     => "0",
            "systemtitle"    => "Fresh install",
            "rewrite"        => false,
            "locale"         => "de_DE.UTF-8",
            "path"			 => $path,
            "passwd_hash_compatibility" => "0",
            "passwd_hash_key"           => Core\Auth::getSalt(32),
            "timezone"       => $timezone
        );
        $config = '<?php $cfg = '. var_export($cfg,true) .'; ?>';

        $f = @fopen( 'config.php', 'w+' );
        if( !$f ){
            $res['error'] = 'Can not open file config.php - please change the permissions.';
            $res['res'] = false;
        } else {
            fwrite( $f, $config ); 
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

        try {
            //create the propel config
            propelHelper::writeXmlConfig();
            propelHelper::writeBuildPorperties();
            propelHelper::writeSchema();
        } catch (Exception $e){
            step5Failed($e->getMessage());
        }
        step5Done(true);
    }


    //Write propel model classes
    function step5_4(){

        try {
            $res = propelHelper::generateClasses();
        } catch (Exception $e){
            step5Failed($e->getMessage());
        }
        step5Done($res);
    }


    //Write main ./propel-config.php
    function step5_5(){
        try {
            $res = propelHelper::generatePropelPhpConfig();
        } catch (Exception $e){
            step5Failed($e->getMessage());
        }
        step5Done($res);
    }

    //Update database schema
    function step5_6(){

        try {
            $res = propelHelper::updateSchema();
        } catch (Exception $e){
            step5Failed($e->getMessage());
        }
        step5Done($res);
    }

    //Fire package-database scripts
    function step5_7(){
        global $modules;

        $manager = new Admin\Module\Manager;

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
    function step5_8(){
        global $modules;

        $manager = new Admin\Module\Manager;

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
    function step5_9(){

        propelHelper::cleanup();
        step5Done(true);
    }

    function step5Init(){

        global $cfg, $modules;

        $subStep = $_GET['substep']+0;
        require( 'config.php' );

        if ($subStep == 0 && !$cfg['activeExtensions']){

            $dir = opendir( PATH_MODULE );
            if(! $dir ) return;
            while (($file = readdir($dir)) !== false){
                if( $file != '..' && $file != '.' && $file != '.svn' && $file != 'admin' && $file != 'users' ){
                    if ($_POST['modules'][$file])
                        $modules[] = $file;
                }
            }

            $cfg['activeExtensions'] = $modules;
            array_shift($cfg['activeExtensions']);//admin
            array_shift($cfg['activeExtensions']);//users
            file_put_contents('config.php', "<?php\n\$cfg = ".var_export($cfg, true).";\n?>");
        } else {
            if ($cfg['activeExtensions'])
                $modules = array_merge($modules, $cfg['activeExtensions']);
        }

        Core\Kryn::$config = $cfg;
        Core\Kryn::$config['db_error_print_sql'] = 1;

        if (file_exists($file = 'propel-config.php')){
            $propelConfig = include($file);
            Core\Kryn::$propelClassMap = $propelConfig['classmap'];
        }

        if ($subStep > 6){

            $file = 'propel-config.php';
            \Propel::init($file);

            $propelConfig = include($file);
            Core\Kryn::$propelClassMap = $propelConfig['classmap'];
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

    @mkdir( 'cache/' );
    @mkdir( 'cache/media' );
    @mkdir( 'cache/object' );
    @mkdir( 'cache/smarty_compile' );

    delDir('propel');

    @mkdir( PATH_MEDIA.'trash' );
    @mkdir( PATH_MEDIA.'css' );
    @mkdir( PATH_MEDIA.'js' );
    
    @mkdir( 'data', 0777 );
    @mkdir( 'data/upload', 0777 );
    @mkdir( 'data/packages', 0777 );
    @mkdir( 'data/upload/modules', 0777 );

    
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
                'Write main ./propel-config.php ...',
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
Your installation file contains following extensions:<br />
<br />
<br />
<form action="?step=5" method="post" id="form.modules">

<table style="width: 98%" class="modulelist" cellpadding="4">
<?php

    $systemModules = array('kryn','admin','users');
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
<a href="javascript: $('form.modules').submit();" class="ka-Button" >Install!</a>
<?php
}

function buildModInfo( $modules ) {
    global $lang;
    foreach( $modules as $module ){
         $config = adminModule::loadInfo( $module );
         $version = $config['version'];
         $title = $config['title'][$lang];
         $desc = $config['desc'][$lang];

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
?>

<h2>Checking requirements</h2>
<br />
<ol>

    <li><b>PHP Version</b>
    <?php

        $t = explode("-", PHP_VERSION);
        $v = ( $t[0] ) ? $t[0] : PHP_VERSION;
        if(! version_compare($v, "5.3.0") < 0 ){
            print '<div style="color: red">PHP version tot old.<br />';
            print "You need PHP version 5.3.0 or greater.<br />";
            print "Installed version: $v (".PHP_VERSION.")<br/></div>";
            $anyThingOk = false;
        } else {
            print '<div style="color: green">OK</div>';
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
            print '<br /><div>There is not available pdo driver.</div>';
            $anyThingOk = false;
        }

?>
    </li>


    <li><b>File permissions</b><br/>
<?php

    global $count;
    $count = 0;

    function checkFile( $pDir, $pFile ){
        global $count;

        $res = '';
        $file = $pDir . ($pFile?'/'.$pFile:'');
        if (!is_dir($file)) {
            $fh = @fopen( $file, 'a+' );
            if( !$fh ){
                $res .=  "<br />$file";
                $count++;
            }
        } else {
            //folder
            if (!is_writeable($file) || opendir($file) === false){
                $res .= "<br />$file";
                $count++;
            }
            $res .= checkDir( $file );
        }
        return $res;
    }

    function checkDir( $pDir ){
        $pDir .= "";
        $res = '';
        $dir = opendir( $pDir );
        if (!$dir) return;
        while (($file = readdir($dir)) !== false){
            if( $file != '..' && $file != '.' && $file != '.git' ){
                $res .= checkFile($pDir, $file);
            }
        }
        return $res;
    }

    $id = posix_getuid();
    $gid = posix_getegid();
    $info = posix_getpwuid($id);
    $ginfo = posix_getgrgid($id);
    $user = $info['name'];
    $group = $ginfo['name'];

    $filesLocked = '';
    $files = array('core', 'data', 'lib', 'media', 'module', 'scripts', '.htaccess', 'index.php', 'install.php');
    if (!is_writeable('.') || opendir('.') === false){
        $filesLocked .= "<br />./";
        $count++;
    }
    foreach ($files as $file){
        $filesLocked  .= checkFile('.', $file);
    }

    if( $filesLocked != "" ){
        $anyThingOk = false;
        print '<div style="color: red;">'.tpf('%s file', '%s files', $count).' are not writeable.</div>Please set write permissions to owner of the web-server or to everyone.<br/>
               <br />
               Use your FTP client to adjust these permissions or directly through ssh:
               <div style="border: 1px solid silver;  font-family: monospace; background-color: white; padding: 5px; margin: 5px;">
               chown -R <i>'.$user.'</i> '.getcwd().'; <br /><b>or</b><br />
               chown -R <i>:'.$group.'</i> '.getcwd().'; <br /<br />
               chmod -R g+w '.getcwd().'; <br /><b>or</b><br />
               chmod -R 777 '.getcwd().' (strongly not recommended)</div>';
        print '<div style="border: 1px solid silver; overflow: auto; font-family: monospace; height: 150px; overflow: auto;  background-color: white; margin: 5px;">'.$filesLocked.'</div>';
    } else {
        print '<div style="color: green;">OK</div>';
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

Please enter your database credentials.<br />
<br/>
    Please note: All tables that already exists will be deleted!
<br/>
<script type="text/javascript">
    window.checkDBEntries = function(){
        var ok = true;
        
        if( $('db_server').value == '' ){ $('db_server').highlight(); ok = false; }
        if( $('db_prefix').value == '' ){ $('db_prefix').highlight(); ok = false; }
        if( ok ){
            $( 'status' ).set('html', '<span style="color:green;">Check data ...</span>');
            var req = {};
            req.type = $('db_type').value;
            req.server = $('db_server').value;
            req.db = $('db_db').value;
            req.prefix = $('db_prefix').value;
            req.username = $('db_username').value;
            req.passwd = $('db_passwd').value;
            //req.pdo = $('db_pdo').checked?1:0;

            new Request.JSON({url: 'install.php?step=checkDb', onComplete: function(stat){
                if( stat != null && stat.res == true )
                   location = '?step=4';
                else if( stat != null )
                    $( 'status' ).set('html', '<span style="color:red;">Login failed:<br />'+stat.error+'</span>');
                else
                    $( 'status' ).set('html', '<span style="color:red;">Fatal Error. Please take a look in server logs.</span>');
            },
            onError: function(res){
                $( 'status' ).set('html', '<span style="color:red;">Fatal Error. Please take a look in server logs.</span> Maybe this helps: <br />'+res);
            }}).post(req);
        }
    }
</script>
<form id="db_form">
<table style="width: 100%" cellpadding="3">
 	<tr>
        <td width="250">Database PDO driver</td>
        <td><select name="db_type" id="db_type">
<?php

            $drivers = array('mysql' => 'MySQL', 'pgsql' => 'PostgreSQL', 'sqlite' => 'SQLite', 'sqlsrv' => 'MSSQL');

            foreach ($drivers as $driver => $label){
                $enabled = extension_loaded('pdo_'.$driver)?'':'disabled="disabled"';
                print "<option $enabled value=\"$driver\">$label</option>";
            }
?>
        </select></td>
    </tr>
    <!--<td>PDO driver</td>
    <td>
        <input type="checkbox" id="db_pdo" />
    </td>
    </tr>
    -->
    <tr>
        <td>
        	Host
        </td>
        <td><input class="text" type="text" name="server" id="db_server" value="localhost" /></td>
    </tr>
    <tr id="ui_username">
        <td>Username</td>
        <td><input class="text" type="text" name="username" id="db_username" /></td>
    </tr>
    <tr id="ui_passwd">
        <td>Password</td>
        <td><input class="text" type="password" name="passwd" id="db_passwd" /></td>
    </tr>
    <tr id="ui_db">
        <td>
        	Database name
        </td>
        <td><input class="text" type="text" name="db" id="db_db" /></td>
    </tr>
    <tr>
        <td>Prefix
	        <div style="color: silver">
	        	Please use only a lowercase string.
	        </div></td>
        <td><input class="text" type="text" name="prefix" id="db_prefix" value="kryn_" /></td>
    </tr>
</table>
</form>
<div id="status" style="padding: 4px;"></div>
<br />
<br />
<a href="?step=2" class="ka-Button" >Back</a>
<a href="javascript: checkDBEntries();" class="ka-Button" >Next</a>

<?php
}

?>
    <script type="text/javascript" src="media/kryn/js/bgNoise.js"></script>
    </div>
  </body>
</html>
