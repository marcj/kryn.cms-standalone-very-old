<?php

namespace Core;

use Propel\Generator\Command\ConfigConvertXmlCommand;
use Propel\Generator\Command\MigrationDiffCommand;
use Propel\Generator\Command\ModelBuildCommand;
use Propel\Generator\Manager\ModelManager;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\StreamOutput;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

/**
 * Class PropelHelper
 *
 * @package Core
 */
class PropelHelper
{
    /**
     * @var array
     */
    public static $objectsToExtension = array();
    /**
     * @var array
     */
    public static $classDefinition = array();

    /**
     * @var string
     */
    private static $tempFolder = '';

    /**
     * @return string
     */
    public static function init()
    {
        try {
            $result = self::fullGenerator();
        } catch (\Exception $e) {
            self::cleanup();
            Kryn::internalError('Propel initialization Error', is_array($e) ? print_r($e, true) : $e);
        }

        self::cleanup();

        return $result;
    }

    /**
     * @return string
     */
    public static function getTempFolder()
    {
        if (self::$tempFolder) {
            return self::$tempFolder;
        }

        self::$tempFolder = Kryn::getTempFolder();

        return self::$tempFolder;
    }

    /**
     * @param $pCmd
     *
     * @return array|bool|string
     */
    public static function callGen($pCmd)
    {
        $errors = self::checkModelXml();
        if ($errors) {
            return array('errors' => $errors);
        }

        self::writeXmlConfig();
        self::writeBuildPorperties();
        self::collectSchemas();

        switch ($pCmd) {
            case 'models':
                $result = self::generateClasses();
                break;
            case 'update':
                $result = self::updateSchema();
                break;
            case 'environment':
                return true;
        }

        self::cleanup();

        return $result;
    }

    /**
     *
     */
    public static function cleanup()
    {
        $tmp = self::getTempFolder();
        delDir($tmp . 'propel');

    }

    /**
     * @return array
     */
    public static function checkModelXml()
    {
        foreach (Kryn::$bundles as $extension) {

            if (file_exists($schema = \Core\Kryn::getBundleDir($extension) . 'Resources/config/models.xml')) {

                simplexml_load_file($schema);
                if ($errors = libxml_get_errors()) {
                    $errors[$schema] = $errors;
                }

            }
        }

        return $errors;
    }

    /**
     * @return string
     */
    public static function fullGenerator()
    {
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

    /**
     * @return string
     * @throws \Exception
     */
    public static function generateClasses()
    {
        $tmp = self::getTempFolder();

        if (!file_exists($tmp . 'propel/runtime-conf.xml')) {
            self::writeXmlConfig();
            self::writeBuildPorperties();
            self::collectSchemas();
        }

        $input = new ArrayInput(array(
             '--input-dir' => $tmp . 'propel/',
             '--output-dir' => $tmp . 'propel/build/classes/',
             '--verbose' => 'vvv'
        ));
        $command = new ModelBuildCommand();
        $command->getDefinition()->addOption(
            new InputOption('--verbose', '-v|vv|vvv', InputOption::VALUE_NONE, '') //because migrationDiffCommand access this option
        );

        $output = new StreamOutput(fopen('php://memory', 'rw'));
        $command->run($input, $output);

//        $generatorConfig = new GeneratorConfig(array_merge(array(
//            'propel.platform.class'                     => $input->getOption('platform'),
//            'propel.builder.object.class'               => $input->getOption('object-class'),
//            'propel.builder.objectstub.class'           => $input->getOption('object-stub-class'),
//            'propel.builder.objectmultiextend.class'    => $input->getOption('object-multiextend-class'),
//            'propel.builder.query.class'                => $input->getOption('query-class'),
//            'propel.builder.querystub.class'            => $input->getOption('query-stub-class'),
//            'propel.builder.queryinheritance.class'     => $input->getOption('query-inheritance-class'),
//            'propel.builder.queryinheritancestub.class' => $input->getOption('query-inheritance-stub-class'),
//            'propel.builder.tablemap.class'             => $input->getOption('tablemap-class'),
//            'propel.builder.pluralizer.class'           => $input->getOption('pluralizer-class'),
//            'propel.disableIdentifierQuoting'           => !$input->getOption('enable-identifier-quoting'),
//            'propel.targetPackage'                      => $input->getOption('target-package'),
//            'propel.packageObjectModel'                 => $input->getOption('enable-package-object-model'),
//            'propel.namespace.autoPackage'              => !$input->getOption('disable-namespace-auto-package'),
//            'propel.addGenericAccessors'                => true,
//            'propel.addGenericMutators'                 => true,
//            'propel.addSaveMethod'                      => true,
//            'propel.addTimeStamp'                       => false,
//            'propel.addValidateMethod'                  => true,
//            'propel.addHooks'                           => true,
//            'propel.namespace.map'                      => 'Map',
//            'propel.useLeftJoinsInDoJoinMethods'        => true,
//            'propel.emulateForeignKeyConstraints'       => false,
//            'propel.schema.autoPrefix'                  => false,
//            'propel.dateTimeClass'                      => '\DateTime',
//            // MySQL specific
//            'propel.mysql.tableType'                    => $input->getOption('mysql-engine'),
//            'propel.mysql.tableEngineKeyword'           => 'ENGINE',
//       ), $this->getBuildProperties($input->getOption('input-dir') . '/build.properties')));
//
//        $manager = new ModelManager();
//        $manager->setFilesystem(new Filesystem());
//        $manager->setGeneratorConfig($generatorConfig);
//        $manager->setSchemas($schemas);
//
//        $manager->setLoggerClosure(function($message){
//            echo $message;
//        });
//        $manager->setWorkingDirectory($tmp . 'propel/');

//        var_dump($manager->build());

//        $content = self::execute('om');

//        if (is_array($content)) {
//            throw new \Exception("Propel generateClasses failed: \n" . $content[0]);
//        }

        $content = stream_get_contents($output->getStream());
        $content .= self::moveClasses();

        return $content;
    }

    /**
     * @return string
     * @throws \FileNotWritableException
     */
    public static function moveClasses()
    {
        $tmp = self::getTempFolder();

        $result = '';

        copyr($tmp . 'propel/build/classes/', $tmp . 'propel-classes/');

        foreach (Kryn::$bundles as $extension) {

            //$extension = Kryn::getBundleName($extension);
            $bundle = Kryn::getBundle($extension);
            $source = $tmp . 'propel-classes/' . ucfirst($bundle->getNamespace()) . '/Models';

            //$result .= " CHECK $targetDir \n";
            $files = find($source . '/*.php');

            foreach ($files as $file) {
                $target = $bundle->getPath() . 'Models/' . basename($file);

                $result .= "$file => " . (file_exists($target) + 0) . "\n";

                if (!file_exists($target)) {
                    if (!copy($file, $target)) {
                        throw new \FileNotWritableException(tf('Can not move file %s to %s', $source, $target));
                    }
                }
                unlink($file);
            }
        }

        return $result;

    }

//    /**
//     * Returns a array of propel config's value. We do not save it as .php file, instead
//     * we create it dynamicaly out of our own config.php.
//     *
//     * @return array The config array for Propel::init() (only in kryn's version of propel, no official)
//     */
//    public static function getConfig()
//    {
//        $adapter = Kryn::$config['database']['type'];
//        if ($adapter == 'postgresql') {
//            $adapter = 'pgsql';
//        }
//
//        $persistent = Kryn::$config['database']['persistent'] ? true : false;
//
//        $emulatePrepares = Kryn::$config['database']['type'] == 'mysql';
//
//        $config = array();
//        $config['datasources']['kryn'] = array(
//            'adapter' => $adapter,
//            'connection' => array(
//                'dsn' => self::getDsn($adapter),
//                'user' => Kryn::$config['database']['user'],
//                'password' => Kryn::$config['database']['password'],
//                'options' => array(
//                    'ATTR_PERSISTENT' => array('value' => $persistent)
//                ),
//                'settings' => array(
//                    'charset' => array('value' => 'utf8')
//                ),
//                'attributes' => array(
//                    'ATTR_EMULATE_PREPARES' => array('value' => $emulatePrepares)
//                )
//            )
//        );
//        $config['datasources']['default'] = 'kryn';
//
//        return $config;
//    }

    /**
     * @param $pDriver
     *
     * @return string
     */
    public static function getDsn($pDriver)
    {
        $dsn = strtolower($pDriver);

        if ($dsn == 'sqlite') {
            $file = Kryn::$config['database']['name'];
            if (substr($file, 0, 1) != '/') {
                $file = PATH . $file;
            }
            $dsn .= ':' . $file;
        } else {
            $dsn .= ':host=' . Kryn::$config['database']['server'];
            $dsn .= ';dbname=' . Kryn::$config['database']['name'];
        }

        return $dsn;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public static function writeXmlConfig()
    {
        $tmp = self::getTempFolder();

        if (!mkdirr($folder = $tmp . 'propel/')) {
            throw new \Exception('Can not create propel folder in ' . $folder);
        }

        $adapter = Kryn::$config['database']['type'];
        if ($adapter == 'postgresql') {
            $adapter = 'pgsql';
        }

        $persistent = Kryn::$config['database']['persistent'] ? 'true' : 'false';

        $xml = '<?xml version="1.0"?>
<config>
    <propel>
        <datasources default="kryn">
            <datasource id="kryn">
                <adapter>' . $adapter . '</adapter>
                <connection>
                    <dsn>' . self::getDsn($adapter) . '</dsn>
                    <user>' . Kryn::$config['database']['user'] . '</user>
                    <password>' . Kryn::$config['database']['password'] . '</password>
                    <options>
                        <option id="ATTR_PERSISTENT">' . $persistent . '</option>
                    </options>';

        if (Kryn::$config['database']['type'] == 'mysql') {
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

        $input = new ArrayInput(array(
             '--input-dir' => $tmp . 'propel/',
             '--output-dir' => $tmp . 'propel/',
             '--verbose' => 'vvv'
        ));
        $command = new ConfigConvertXmlCommand();
        $command->getDefinition()->addOption(
            new InputOption('--verbose', '-v|vv|vvv', InputOption::VALUE_NONE, '') //because migrationDiffCommand access this option
        );

        $output = new StreamOutput(fopen('php://memory', 'rw'));
        $command->run($input, $output);


        mkdirr($tmp . 'propel-classes/');

        $source = $tmp . 'propel/config.php';
        rename($source, $tmp . 'propel-classes/config.php');

        return true;
    }


    public static function loadConfig()
    {
        $tmp = self::getTempFolder();
        if (file_exists($file = $tmp . 'propel-classes/config.php')) {
            include($file);
            return true;
        }

        return false;
    }

    /**
     * Updates database's Schema.
     *
     * This function creates whatever is needed to do the job.
     * (means, calls writeXmlConfig() etc if necessary).
     *
     * This function inits the Propel class.
     *
     * @param  bool $pWithDrop
     *
     * @return string
     * @throws \Exception
     */
    public static function updateSchema($pWithDrop = false)
    {
        $sql = self::getSqlDiff($pWithDrop);

        if (is_array($sql)) {
            throw new \Exception("Propel updateSchema failed: \n" . $sql[0]);
        }

        if (!$sql) {
            return "Schema up 2 date.";
        }

        $GLOBALS['sql'] = $sql;

        $sql = explode(";\n", $sql);

        dbBegin();
        try {
            foreach ($sql as $query) {
                dbExec($query);
            }
        } catch (\PDOException $e) {
            dbRollback();
            throw new \PDOException($e->getMessage() . ' in SQL: ' . $query);
        }
        dbCommit();

        return 'ok';
    }


    /**
     * @return bool
     */
    public static function collectSchemas()
    {
        $tmp = self::getTempFolder();

        $currentSchemas = find($tmp . 'propel/*.schema.xml');
        foreach ($currentSchemas as $file) {
            unlink($file);
        }

        $schemeData = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n  <database name=\"kryn\" basePeer=\"\\Core\\PropelBasePeer\" defaultIdMethod=\"native\"\n";

        foreach (Kryn::$bundles as $bundleName) {

            if (!($bundle = Kryn::getBundle($bundleName))) {
                continue;
            }

            if (file_exists($schema = $bundle->getPath() . 'Resources/config/models.xml')) {

                $extension = $bundle->getNamespace();
                $tables = simplexml_load_file($schema);
                $newSchema = $schemeData . ' namespace="' . ucfirst($extension) . '\\Models">';

                foreach ($tables->table as $table) {
                    $newSchema .= $table->asXML() . "\n    ";
                }

                $newSchema .= "</database>";

                $file = str_replace('/', '.', $extension) . '.schema.xml';
                file_put_contents($tmp . 'propel/' . $file, $newSchema);
            }

        }
        file_put_contents($tmp . 'propel/schema.xml', $schemeData . "></database>");

        return true;
    }

    /**
     * @return array|string
     */
    public static function getSqlDiff()
    {
        $tmp = self::getTempFolder();

        if (!file_exists($tmp . 'propel/runtime-conf.xml')) {
            self::writeXmlConfig();
            self::writeBuildPorperties();
            self::collectSchemas();
        }

        //remove all migration files
        $files = find($tmp . 'propel/generated-migrations/PropelMigration_*.php');
        if ($files[0]) {
            unlink($files[0]);
        }
        $input = new ArrayInput(array(
             '--input-dir' => $tmp . 'propel/',
             '--output-dir' => $tmp . 'propel/build/',
             '--verbose' => 'vvv'
        ));
        $command = new MigrationDiffCommand();
        $command->getDefinition()->addOption(
            new InputOption('--verbose', '-v|vv|vvv', InputOption::VALUE_NONE, '') //because migrationDiffCommand access this option
        );

        $output = new StreamOutput(fopen('php://memory', 'rw'));
        $command->run($input, $output);

        $files = find($tmp . 'propel/build/PropelMigration_*.php');
        $lastMigrationFile = $files[0];

        if (!$lastMigrationFile) {
            return '';
        }

        preg_match('/(.*)\/PropelMigration_([0-9]*)\.php/', $lastMigrationFile, $matches);
        $clazz = 'PropelMigration_' . $matches[2];
        $uid = str_replace('.', '_', uniqid('', true));
        $newClazz = 'PropelMigration__' . $uid;

        $content = file_get_contents($lastMigrationFile);
        $content = str_replace('class ' . $clazz, 'class PropelMigration__' . $uid, $content);
        file_put_contents($lastMigrationFile, $content);

        include($lastMigrationFile);
        $obj = new $newClazz;

        $sql = $obj->getUpSQL();

        $sql = $sql['kryn'];
        unlink($lastMigrationFile);

        if (is_array($protectTables = \Core\Kryn::$config['database']['protectTables'])) {
            foreach ($protectTables as $table) {
                $table = str_replace('%pfx%', pfx, $table);
                $sql = preg_replace('/^DROP TABLE (IF EXISTS|) ' . $table . '(\n|\s)(.*)\n+/im', '', $sql);
            }
        }
        $sql = preg_replace('/^#.*$/im', '', $sql);

        return trim($sql);
    }

    /**
     * @return array|string
     * @throws \Exception
     */
    public static function execute()
    {
        $chdir = getcwd();
        chdir('vendor/propel/propel1/generator/');

        $oldIncludePath = get_include_path();
        set_include_path(PATH . 'vendor/phing/phing/classes/' . PATH_SEPARATOR . get_include_path());

        $argv = array('propel-gen');

        foreach (func_get_args() as $cmd) {
            $argv[] = $cmd;
        }

        $tmp = self::getTempFolder();
        $tmp .= 'propel/';

        $argv[] = '-Dproject.dir=' . $tmp;

        var_dump($tmp);

        require_once 'phing/Phing.php';

        $outStreamS = fopen("php://memory", "w+");
        $outStream = new \OutputStream($outStreamS);
        $cmd = implode(' ', $argv);
        $outStream->write("\n\nExecute command: " . $cmd . "\n\n");

        try {
            /* Setup Phing environment */
            \Phing::setOutputStream($outStream);
            \Phing::setErrorStream($outStream);

            \Phing::startup();

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

        if (strpos($content, "BUILD FINISHED") !== false && strpos($content, "Aborting.") === false) {
            preg_match_all('/\[((propel[a-zA-Z-_]*)|phingcall)\] .*/', $content, $matches);
            $result = "\nCommand successfully: $cmd\n";
            $result .= '<div style="color: gray;">';
            foreach ($matches[0] as $match) {
                $result .= $match . "\n";
            }

            return $result . '</div>';
        } else {
            return array($content);
        }
    }

    /**
     * @throws Exception
     */
    public static function writeBuildPorperties()
    {
        $tmp = self::getTempFolder();

        if (!mkdirr($folder = $tmp . 'propel/')) {
            throw new Exception('Can not create propel folder in ' . $folder);
        }

        $adapter = Kryn::$config['database']['type'];
        if ($adapter == 'postgresql') {
            $adapter = 'pgsql';
        }

        $properties = '
propel.mysql.tableType = InnoDB

propel.database = ' . $adapter . '
propel.database.url = ' . self::getDsn($adapter) . '
propel.database.user = ' . Kryn::$config['database']['user'] . '
propel.database.password = ' . Kryn::$config['database']['password'] . '
propel.tablePrefix = ' . Kryn::$config['database']['prefix'] . '
propel.database.encoding = utf8
propel.project = kryn

propel.namespace.autoPackage = true
propel.packageObjectModel = true
propel.behavior.workspace.class = lib.WorkspaceBehavior

';

        if ($adapter == 'pgsql') {
            $properties .= "propel.disableIdentifierQuoting=true";
        }

        file_put_contents($tmp . 'propel/build.properties', $properties) ? true : false;

    }

}
