<?php

namespace Core;

class PropelHelper {

    public static $objectsToExtension = array();
    public static $classDefinition = array();

    private static $tempFolder = '';

    public static function init(){

        try {
            $result = self::fullGenerator();
        } catch(\Exception $e){
            self::cleanup();
            Kryn::internalError('Propel initialization Error', is_array($e)?print_r($e,true):$e);
            throw $e;
        }

        self::cleanup();

        return $result;
    }

    public static function getTempFolder(){

        if (self::$tempFolder) return self::$tempFolder;

        self::$tempFolder = Kryn::getTempFolder();

        return self::$tempFolder;
    }

    public static function callGen($pCmd){

        $errors = self::checkModelXml();
        if ($errors)
            return array('errors' => $errors);

        self::writeXmlConfig();
        self::writeBuildPorperties();
        self::collectSchemas();

        switch($pCmd){
            case 'models':
                $result = self::generateClasses(); break;
            case 'update':
                $result = self::updateSchema(); break;
            case 'environment': return true;
        }

        self::cleanup();

        return $result;
    }

    public static function cleanup(){

        $tmp = self::getTempFolder();
        delDir($tmp . 'propel');

    }

    public static function checkModelXml(){
        foreach (Kryn::$extensions as $extension){

            if ($extension == 'kryn') continue;

            if (file_exists($schema = \Core\Kryn::getModuleDir($extension).'model.xml')){

                simplexml_load_file($schema);
                if ($errors = libxml_get_errors())
                    $errors[$schema] = $errors;

            }
        }

        return $errors;
    }

    public static function fullGenerator(){

        self::writeXmlConfig();
        self::writeBuildPorperties();
        self::collectSchemas();

        $content = '';

        $content .= self::generateClasses();
        $content .= self::updateSchema();

        self::cleanup();

        $content .= "\n\n<b style='color: green'>Done.</b>";

        return $content;
    }

    public static function generateClasses(){

        $tmp = self::getTempFolder();

        if (!file_exists($tmp . 'propel/runtime-conf.xml')){
            self::writeXmlConfig();
            self::writeBuildPorperties();
            self::collectSchemas();
        }

        $content = self::execute('om');


        if (is_array($content)){
            throw new \Exception("Propel generateClasses failed: \n". $content[0]);
        }
        $content .= self::moveClasses();

        return $content;
    }

    /*
    public static function collectObjectToExtension(){


        self::$objectsToExtension = array();
        foreach (Kryn::$extensions as $extension){

            if ($extension == 'kryn') continue;

            if (file_exists($schema = \Core\Kryn::getModuleDir($extension).'model.xml')){

                $tables = simplexml_load_file ($schema);

                foreach ($tables->table as $table){
                    $attributes = $table->attributes();
                    $clazz = (string)$attributes['phpName'];

                    self::$objectsToExtension[$clazz] = $extension;

                }

            }
        }
    }
*/

    public static function moveClasses(){

        $tmp = self::getTempFolder();

        $result = '';

        Kryn::$extensions = array_unique(Kryn::$extensions);

        foreach (Kryn::$extensions as $extension){

            $targetDir = $tmp.'propel-classes/'.ucfirst($extension);
            $source = $tmp.'propel/build/classes/'.ucfirst($extension);

            //$result .= " CHECK $targetDir \n";
            $files = find($source.'/*.php');
            if ($files && !mkdirr($targetDir))
                throw new \FileNotWritableException(tf('Can not create folder %s', $source));

            //$result .= ' $FILES COUNT '.count($files)."\n";
            //$result .= " WHAT ".(is_dir($targetDir)+0)."\n";
            foreach ($files as $file){

                $target  = \Core\Kryn::getModuleDir($extension).'model/'.basename($file);

                $result .= "$file => ".(file_exists($target)+0)."\n";
                if (file_exists($target)) continue;

                if (!is_dir($targetDir) && !mkdirr($targetDir))
                    throw new \FileNotWritableException(tf('Can not create folder %s', $targetDir));

                if (!copy($file, $target))
                    throw new \FileNotWritableException(tf('Can not move file %s to %s', $source, $target));

                $result .= "[move][$extension] Class moved ".basename($file)." to $targetDir\n";

            }

            $file = $source.'/om';
            if (is_dir($file)){
                $target = $tmp.'propel-classes/'.ucfirst($extension).'/om';
                copyr($file, $target);
                $result .= "[move][$extension] OM folder moved ".basename($file)." to $target\n";
            }

            $file = $source.'/map';
            if (is_dir($file)){
                $target = $tmp.'propel-classes/'.ucfirst($extension).'/map';
                copyr($file, $target);
                $result .= "[move][$extension] MAP folder moved ".basename($file)." to $target\n";
            }


        }

        return $result;

    }

    /**
     * Returns a array of propel config's value. We do not save it as .php file, instead
     * we create it dynamicaly out of our own config.php.
     * 
     * @return array The config array for Propel::init() (only in kryn's version of propel, no official)
     */
    public static function getConfig(){
        
        $adapter = Kryn::$config['database']['type'];
        if ($adapter == 'postgresql') $adapter = 'pgsql';

        $dsn = $adapter.':host='.Kryn::$config['database']['server'].';dbname='.Kryn::$config['database']['name'];

        $persistent = Kryn::$config['database']['persistent'] ? true:false;

        $emulatePrepares = Kryn::$config['database']['type'] == 'mysql';

        $config = array();
        $config['datasources']['kryn'] = array(
            'adapter' => $adapter,
            'connection' => array(
                'dsn' => $dsn,
                'user' => Kryn::$config['database']['user'],
                'password' => Kryn::$config['database']['password'],
                'options' => array(
                    'ATTR_PERSISTENT' => array('value' => $persistent)
                ),
                'settings' => array(
                    'charset' => array('value' => 'utf8')
                ),
                'attributes' => array(
                    'ATTR_EMULATE_PREPARES' => array('value' => $emulatePrepares)
                )
            )
        );
        $config['datasources']['default'] = 'kryn';


        return $config;
    }


    public static function writeXmlConfig(){

        $tmp = self::getTempFolder();

        if (!mkdirr($folder = $tmp.'propel/build/conf/'))
            throw new \Exception('Can not create propel folder in '.$folder);

        $adapter = Kryn::$config['database']['type'];
        if ($adapter == 'postgresql') $adapter = 'pgsql';

        $dsn = $adapter.':host='.Kryn::$config['database']['server'].';dbname='.Kryn::$config['database']['name'];

        $persistent = Kryn::$config['database']['persistent'] ? true:false;

        $xml = '<?xml version="1.0"?>
<config>
    <propel>
        <datasources default="kryn">
            <datasource id="kryn">
                <adapter>'.$adapter.'</adapter>
                <connection>
                    <classname>PropelPDO</classname>
                    <dsn>'.$dsn.'</dsn>
                    <user>'.Kryn::$config['database']['user'].'</user>
                    <password>'.Kryn::$config['database']['password'].'</password>
                    <options>
                        <option id="ATTR_PERSISTENT">'.$persistent.'</option>
                    </options>';

        if (Kryn::$config['database']['type'] == 'mysql'){
            $xml .= '
                    <attributes>
                        <option id="ATTR_EMULATE_PREPARES">true</option>
                    </attributes>
                    ';
        }

        $xml .= '
                    <settings>
                        <setting id="charset">utf8</setting>
                    </settings>
                </connection>
            </datasource>
        </datasources>
    </propel>
</config>';
    
        file_put_contents($tmp . 'propel/runtime-conf.xml', $xml);
        file_put_contents($tmp . 'propel/buildtime-conf.xml', $xml);
        return true;
    }

    /**
     * Updates database's Schema.
     *
     * This function creates whatever is needed to do the job.
     * (means, calls writeXmlConfig() etc if necessary).
     *
     * This function inits the Propel class.
     *
     * @param bool $pWithDrop
     * @return string
     * @throws \Exception
     */
    public static function updateSchema($pWithDrop = false){

        $sql = self::getSqlDiff($pWithDrop);

        if (is_array($sql)){
            throw new \Exception("Propel updateSchema failed: \n". $sql[0]);
        }

        if (!$sql){
            return "Schema up 2 date.";
        }

        $GLOBALS['sql'] = $sql;

        $sql = explode(";\n", $sql);

        dbBegin();
        try {
            foreach ($sql as $query)
                dbExec($query);
        } catch (\PDOException $e){
            dbRollback();
            throw new \PDOException($e->getMessage().' in SQL: '.$query);
        }
        dbCommit();

        return 'ok';
    }


    public static function collectSchemas(){

        $tmp = self::getTempFolder();

        $currentSchemas = find($tmp.'propel/*.schema.xml');
        foreach ($currentSchemas as $file){
            unlink($file);
        }

        $schemeData = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n  <database name=\"kryn\" basePeer=\"\\Core\\PropelBasePeer\" defaultIdMethod=\"native\"\n";

        foreach (Kryn::$extensions as $extension){

            if (file_exists($schema = \Core\Kryn::getModuleDir($extension).'model.xml')){

                $tables = simplexml_load_file($schema);
                $newSchema = $schemeData.' namespace="'.ucfirst($extension).'">';

                foreach ($tables->table as $table){
                    $newSchema .= $table->asXML()."\n    ";
                }


                $newSchema .= "</database>";

                $file = $extension.'.schema.xml';
                file_put_contents($tmp . 'propel/'.$file, $newSchema);
            }

        }

        file_put_contents($tmp . 'propel/schema.xml', $schemeData."></database>");

        return true;
    }


    public static function getSqlDiff(){

        $tmp = self::getTempFolder();

        if (!file_exists($tmp . 'propel/runtime-conf.xml')){
            self::writeXmlConfig();
            self::writeBuildPorperties();
            self::collectSchemas();
        }

        if (!\Propel::isInit()){
            \Propel::setConfiguration(self::getConfig());
            \Propel::initialize();
        }

        $tmp = self::getTempFolder();

        //remove all migration files
        $files = find($tmp . 'propel/build/migrations/PropelMigration_*.php');
        if ($files[0]) unlink($files[0]);

        $content = self::execute('sql-diff');

        if (is_array($content)) return $content;
        if (strpos($content, '"sql-diff" failed'))
            return array($content);

        $files = find($tmp . 'propel/build/migrations/PropelMigration_*.php');
        $lastMigrationFile = $files[0];

        if (!$lastMigrationFile) return '';

        preg_match('/(.*)\/PropelMigration_([0-9]*)\.php/', $lastMigrationFile, $matches);
        $clazz = 'PropelMigration_'.$matches[2];
        $uid = str_replace('.', '_', uniqid('', true));
        $newClazz = 'PropelMigration__'.$uid;

        $content = file_get_contents($lastMigrationFile);
        $content = str_replace('class '.$clazz, 'class PropelMigration__'.$uid, $content);
        file_put_contents($lastMigrationFile, $content);

        include($lastMigrationFile);
        $obj = new $newClazz;

        $sql = $obj->getUpSQL();

        $sql = $sql['kryn'];
        unlink($lastMigrationFile);

        if (is_array($protectTables = \Core\Kryn::$config['database']['protectTables'])){
            foreach ($protectTables as $table){
                $table = str_replace('%pfx%', pfx, $table);
                $sql = preg_replace('/^DROP TABLE (IF EXISTS|) '.$table.'(\n|\s)(.*)\n+/im', '', $sql);
            }
        }
        $sql = preg_replace('/^#.*$/im', '', $sql);

        return trim($sql);

    }

    public static function execute(){

        $chdir = getcwd();
        chdir('vendor/propel/propel1/generator/');

        $oldIncludePath = get_include_path();
        set_include_path(PATH.'vendor/phing/phing/classes/' . PATH_SEPARATOR . get_include_path());

        $argv = array('propel-gen');

        foreach (func_get_args() as $cmd)
            $argv[] = $cmd;

        $tmp = self::getTempFolder();
        $tmp .= 'propel/';

        $argv[] = '-Dproject.dir='.$tmp;

        require_once 'phing/Phing.php';

        $outStreamS = fopen("php://memory", "w+");
        $outStream = new \OutputStream($outStreamS);
        $cmd = implode(' ', $argv);
        $outStream->write("\n\nExecute command: ".$cmd."\n\n");


        try {
            /* Setup Phing environment */
            \Phing::startup();

            \Phing::setOutputStream($outStream);
            \Phing::setErrorStream($outStream);

            // Set phing.home property to the value from environment
            // (this may be NULL, but that's not a big problem.)
            \Phing::setProperty('phing.home', getenv('PHING_HOME'));

            // Grab and clean up the CLI arguments
            $args = isset($argv) ? $argv : $_SERVER['argv']; // $_SERVER['argv'] seems to not work (sometimes?) when argv is registered
            array_shift($args); // 1st arg is script name, so drop it
            // Invoke the commandline entry point
            \Phing::fire($args);

            // Invoke any shutdown routines.
            \Phing::shutdown();
        } catch (\Exception $x) {
            chdir($chdir);
            set_include_path($oldIncludePath);
            throw $x;
        }
        chdir($chdir);
        set_include_path($oldIncludePath);

        rewind($outStreamS);
        $content = stream_get_contents($outStreamS);

        if (strpos($content, "BUILD FINISHED") !== false && strpos($content, "Aborting.") === false){
            preg_match_all('/\[((propel[a-zA-Z-_]*)|phingcall)\] .*/', $content, $matches);
            $result  = "\nCommand successfully: $cmd\n";
            $result .= '<div style="color: gray;">';
            foreach ($matches[0] as $match){
                $result .= $match."\n";
            }

            return $result.'</div>';
        } else {
            return array($content);
        }
    }

    public static function writeBuildPorperties(){

        $tmp = self::getTempFolder();

        if (!mkdirr($folder = $tmp . 'propel/'))
            throw new Exception('Can not create propel folder in '.$folder);

        $adapter = Kryn::$config['database']['type'];
        if ($adapter == 'postgresql') $adapter = 'pgsql';

        $dsn = $adapter.':host='.Kryn::$config['database']['server'].';dbname='.Kryn::$config['database']['name'].';';

        $properties = '
propel.mysql.tableType = InnoDB

propel.database = '.$adapter.'
propel.database.url = '.$dsn.'
propel.database.user = '.Kryn::$config['database']['user'].'
propel.database.password = '.Kryn::$config['database']['password'].'
propel.tablePrefix = '.Kryn::$config['database']['prefix'].'
propel.database.encoding = utf8
propel.project = kryn

propel.namespace.autoPackage = true
propel.packageObjectModel = true
propel.behavior.workspace.class = lib.WorkspaceBehavior

';

        if ($adapter == 'pgsql')
            $properties .= "propel.disableIdentifierQuoting=true";

        file_put_contents($tmp . 'propel/build.properties', $properties)?true:false;

    }




}