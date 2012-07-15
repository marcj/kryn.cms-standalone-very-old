<?php


class propelHelper {

    public static $defaultOpts = array(
        '-Dproject.dir=../../../propel/'
    );

    public static $objectsToExtension = array();
    public static $classDefinition = array();

    public static function init(){

        try {
            $result = self::fullGenerator();
        } catch(Exception $e){
            self::cleanup();
            Core\Kryn::internalError('Propel initialization Error', is_array($e)?print_r($e,true):$e);
        }

        self::cleanup();
        Core\Kryn::internalMessage('Propel initialization', $result);
    }

    public static function fullGenerator(){

        self::writeXmlConfig();
        self::writeBuildPorperties();
        self::writeSchema();

        $content = '';

        $content .= self::generateClasses();
        $content .= self::generatePropelPhpConfig();
        $content .= self::updateSchema();

        self::cleanup();

        $content .= "\n\n<b style='color: green'>Done.</b>";

        return $content;
    }

    public static function generateClasses(){

        //delete old map/om folders
        foreach (Core\Kryn::$extensions as $extension){
            delDir(PATH_MODULE.$extension.'/model/map/');
            delDir(PATH_MODULE.$extension.'/model/om');
        }

        $content  = self::execute('om');

        if (is_array($content)){
            throw new Exception("Propel generateClasses failed: \n". $content[0]);
        }
        $content .= self::moveClasses();

        return $content;
    }

    public static function generatePropelPhpConfig(){

        if (!self::$classDefinition){
            die('self::$classDefinition is empty. Need propelHelper::moveClasses() first.');
        }

        $file         = 'propel/build/conf/Kryn-conf.php';
        $classMapFile = 'propel/build/conf/classmap-Kryn-conf.php';

        if (!file_exists($file)){
            self::writeXmlConfig();
            $content = self::execute('convert-conf');
            if (is_array($content))
                throw new Exception("Propel generateClasses failed: \n". $content[0]);;
        }

        $config   = file_get_contents($file);

        $classDefinition = '$conf[\'classmap\'] = '.var_export(self::$classDefinition, true).";\n";

        $line = '$conf[\'classmap\'] = include(dirname(__FILE__) . DIRECTORY_SEPARATOR . \'classmap-Kryn-conf.php\');';
        $config = str_replace($line, $classDefinition, $config);

        file_put_contents('propel-config.php', $config);

        return $content;
    }

    public static function cleanup(){

        delDir('propel');

    }

    public static function moveClasses(){

        if (!self::$objectsToExtension){
            die('self::$objectsToExtension is empty. Need propelHelper::writeSchema() first.');
        }

        $content = "\nMove class files<div style='color: gray;'>";

        foreach (self::$objectsToExtension as $name => $extension){

            if (!$name) continue;

            $files = array(
                'om/Base'.$name.'Peer.php',
                'om/Base'.$name.'.php',
                'map/'.$name.'TableMap.php',
                'om/Base'.$name.'Query.php',
                'x' => $name.'.php',
                'y' => $name.'Peer.php',
                'z' => $name.'Query.php'
            );

            foreach ($files as $key => $file){

                $target    = PATH_MODULE.$extension.'/model/'.$file;
                $targetDir = dirname(PATH_MODULE.$extension.'/model/'.$file);

                self::$classDefinition[basename($file)] = $target;

                if (!is_numeric($key) ){
                    //do not remove the class files which we can edit
                    if (file_exists($target)) continue;
                }


                if (!is_dir($targetDir)) if(!mkdir($targetDir)) die('Can not create folder '.$targetDir);

                $source = 'propel/build/classes/Kryn/'.$file;

                if (!file_exists($source)){
                    $content .= "[move][$extension] ERROR can not find $source.\n";
                } else {

                    if (!rename($source, $target)){
                        die('Can not move file '.$source.' to '.$target);
                    }
                    $content .= "[move][$extension] Class moved $file to $targetDir\n";
                }
            }

        }

        return $content."</div>";

    }


    public static function updateSchema(){

        if (!Propel::isInit()){
            $file = 'propel/build/conf/Kryn-conf.php';
            Propel::init($file);
        }

        $sql = self::getSqlDiff()."\n";

        $sql = explode(";\n", $sql);

        $result = '';

        foreach ($sql as $query){
            if (!trim($query)) continue;
            try {
                dbExec($query);
            } catch (Exception $e){
                $result .= "[error] $query -> $e\n";
            }
        }

        return $result;
    }

    public static function getSqlDiff(){

        //remove all migration files
        $files = find('propel/build/migrations/PropelMigration_*.php');
        if ($files[0]) unlink($files[0]);

        $content = self::execute('diff');

        $files = find('propel/build/migrations/PropelMigration_*.php');
        $lastMigrationFile = $files[0];

        preg_match('/(.*)\/PropelMigration_([0-9]*)\.php/', $lastMigrationFile, $matches);
        $clazz = 'PropelMigration_'.$matches[2];

        if (!$lastMigrationFile) return $content;

        require($lastMigrationFile);
        $obj = new $clazz;

        $sql = $obj->getUpSQL();

        $sql = $sql['Kryn'];
        unlink($lastMigrationFile);

        $sql = preg_replace('/^DROP TABLE .*$/im', '', $sql);

        return trim($sql);

    }

    public static function writeSchema(){

        $newSchema = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n  <database name=\"Kryn\" defaultIdMethod=\"native\">\n     ";

        foreach (Core\Kryn::$extensions as $extension){

            if ($extension == 'Kryn') continue;

            if (file_exists($schema = PATH_MODULE.$extension.'/model/schema.xml')){

                $tables = simplexml_load_file ($schema);

                foreach ($tables->table as $table){
                    $attributes = $table->attributes();

                    self::$objectsToExtension[(string)$attributes['phpName']] = $extension;

                    $newSchema .= $table->asXML()."\n    ";

                }

            }

        }

        $newSchema .= "\n</database>";

        file_put_contents('propel/schema.xml', $newSchema);

        return true;
    }

    public static function execute(){

        $chdir = getcwd();
        chdir('lib/propel/generator/');

        $oldIncludePath = get_include_path();
        set_include_path("./lib" . PATH_SEPARATOR . get_include_path());

        $argv = array('propel-gen');

        foreach (func_get_args() as $cmd)
            $argv[] = $cmd;

        foreach (self::$defaultOpts as $cmd)
            $argv[] = $cmd;

        require_once 'phing/Phing.php';

        $outStreamS = fopen("php://memory", "w+");
        $outStream = new OutputStream($outStreamS);
        $cmd = implode(' ', $argv);
        $outStream->write("\n\nExecute command: ".$cmd."\n\n");


        try {
            /* Setup Phing environment */
            Phing::startup();

            error_reporting(E_ALL ^ E_NOTICE);

            Phing::setOutputStream($outStream);
            Phing::setErrorStream($outStream);

            // Set phing.home property to the value from environment
            // (this may be NULL, but that's not a big problem.)
            Phing::setProperty('phing.home', getenv('PHING_HOME'));

            // Grab and clean up the CLI arguments
            $args = isset($argv) ? $argv : $_SERVER['argv']; // $_SERVER['argv'] seems to not work (sometimes?) when argv is registered
            array_shift($args); // 1st arg is script name, so drop it
            // Invoke the commandline entry point
            Phing::fire($args);

            // Invoke any shutdown routines.
            Phing::shutdown();
        } catch (Exception $x) {
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

        if (!mkdirr($folder = 'propel/'))
            throw new Exception('Can not create propel folder in '.$folder);

        $adapter = Core\Kryn::$config['db_type'];
        if ($adapter == 'postgresql') $adapter = 'pgsql';

        $dsn = $adapter.':host='.Core\Kryn::$config['db_server'].';dbname='.Core\Kryn::$config['db_name'].';';

        $properties = '
propel.database = '.$adapter.'
propel.database.url = '.$dsn.'
propel.database.user = '.Core\Kryn::$config['db_user'].'
propel.database.password = '.Core\Kryn::$config['db_passwd'].'
propel.tablePrefix = '.Core\Kryn::$config['db_prefix'].'
propel.project = Kryn';

        return file_put_contents('propel/build.properties', $properties)?true:false;
    }

    public static function writeXmlConfig(){

        if (!mkdirr($folder = 'propel/build/conf/'))
            throw new Exception('Can not create propel folder in '.$folder);

        $adapter = Core\Kryn::$config['db_type'];
        if ($adapter == 'postgresql') $adapter = 'pgsql';

        $dsn = $adapter.':host='.Core\Kryn::$config['db_server'].';dbname='.Core\Kryn::$config['db_name'].';';

        $xml = '<?xml version="1.0"?>
<config>
    <propel>
        <datasources default="Kryn">
            <datasource id="Kryn">
                <adapter>'.$adapter.'</adapter>
                <connection>
                    <classname>PropelPDO</classname>
                    <dsn>'.$dsn.'</dsn>
                    <user>'.Core\Kryn::$config['db_user'].'</user>
                    <password>'.Core\Kryn::$config['db_passwd'].'</password>
                    <options>
                        <option id="ATTR_PERSISTENT">true</option>
                    </options>';

        if (Core\Kryn::$config['db_type'] == 'mysql'){
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

        file_put_contents('propel/runtime-conf.xml', $xml);
        file_put_contents('propel/buildtime-conf.xml', $xml);
        return true;
    }







}