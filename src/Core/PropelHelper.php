<?php

namespace Core;

use Core\Config\Connection;
use Core\Exceptions\BundleNotFoundException;
use Propel\Generator\Command\ConfigConvertXmlCommand;
use Propel\Generator\Command\MigrationDiffCommand;
use Propel\Generator\Command\ModelBuildCommand;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\StreamOutput;
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
     * @param $cmd
     *
     * @return array|bool|string
     */
    public static function callGen($cmd)
    {
        $errors = self::checkModelXml();
        if ($errors) {
            return array('errors' => $errors);
        }

        self::writeXmlConfig();
        self::writeBuildProperties();
        self::collectSchemas();

        switch ($cmd) {
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
        self::writeBuildProperties();
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
            self::writeBuildProperties();
            self::collectSchemas();
        }

        $platform = Kryn::getSystemConfig()->getDatabase()->getMainConnection()->getType();
        $platform = ucfirst($platform) . 'Platform';

        $input = new ArrayInput(array(
             '--input-dir' => $tmp . 'propel/',
             '--output-dir' => $tmp . 'propel/build/classes/',
             '--platform' => $platform,
             '--verbose' => 'vvv'
        ));
        $command = new ModelBuildCommand();
        $command->getDefinition()->addOption(
            new InputOption('--verbose', '-v|vv|vvv', InputOption::VALUE_NONE, '') //because migrationDiffCommand access this option
        );

        $output = new StreamOutput(fopen('php://memory', 'rw'));
        $command->run($input, $output);
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
        TempFile::copy('propel/build/classes', 'propel-classes');

        foreach (Kryn::$bundles as $bundleName) {

            $bundle = Kryn::getBundle($bundleName);
            if (!$bundle) {
                throw new BundleNotFoundException(tf('Bundle `%s` not found.', $bundleName));
            }
            $source = $tmp . 'propel-classes/' . ucfirst($bundle->getNamespace()) . '/Models';

            $files = find($source . '/*.php');

            foreach ($files as $file) {
                $target = $bundle->getPath() . 'Models/' . basename($file);

                $result .= "$file => " . (file_exists($target) + 0) . "\n";

                if (!file_exists($target)) {
                    SystemFile::createFolder(dirname($target));
                    if (!copy($file, $target)) {
                        throw new \FileNotWritableException(tf('Can not move file %s to %s', $source, $target));
                    }
                }
                unlink($file);
            }
        }

        return $result;

    }

    /**
     * @param Connection $connection
     *
     * @return string
     */
    public static function getConnectionXml(Connection $connection)
    {
        $type = strtolower($connection->getType());
        $dsn  = $type;

        if ('sqlite' === $dsn) {
            $file = $connection->getServer();
            if (substr($file, 0, 1) != '/') {
                $file = PATH . $file;
            }
            $dsn .= ':' . $file;
        } else {
            $dsn .= ':host=' . $connection->getServer();
            $dsn .= ';dbname=' . $connection->getName();
        }

        $user = htmlspecialchars($connection->getUsername(), ENT_XML1);
        $password = htmlspecialchars($connection->getPassword(), ENT_XML1);
        $dsn = htmlspecialchars($dsn, ENT_XML1);

        $persistent = $connection->getPersistent() ? 'true' : 'false';

        $xml = "
    <connection>
        <dsn>$dsn</dsn>
        <user>$user</user>
        <password>$password</password>

        <options>
            <option id=\"ATTR_PERSISTENT\">$persistent</option>
        </options>";

//        if ('mysql' === $type) {
//            $xml .= '
//        <attributes>
//            <option id="ATTR_EMULATE_PREPARES">true</option>
//        </attributes>
//            ';
//        }

        $xml .= '
        <settings>
            <setting id="charset">utf8</setting>
        </settings>
    </connection>';

        return $xml;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public static function writeXmlConfig()
    {
        $tmp = self::getTempFolder();

        if (!TempFile::createFolder($folder = 'propel/')) {
            throw new \Exception('Can not create propel folder in ' . $tmp . $folder);
        }

        $adapter = Kryn::getSystemConfig()->getDatabase()->getMainConnection()->getType();

        $xml = '<?xml version="1.0"?>
<config>
    <propel>
        <datasources default="kryn">
            <datasource id="kryn">
                <adapter>' . $adapter . '</adapter>
                ';

        foreach (Kryn::getSystemConfig()->getDatabase()->getConnections() as $connection) {
            if (!$connection->isSlave()) {
                $xml .= self::getConnectionXml($connection) . "\n";
            }
        }

        $slaves = '';
        foreach (Kryn::getSystemConfig()->getDatabase()->getConnections() as $connection) {
            if ($connection->isSlave()) {
                $slaves .= self::getConnectionXml($connection) . "\n";
            }
        }

        if ($slaves) {
            $xml .= "<slaves>$slaves</slaves>";
        }

        $xml .= '
            </datasource>
        </datasources>
    </propel>
</config>';

        TempFile::setContent('propel/runtime-conf.xml', $xml);
        TempFile::setContent('propel/buildtime-conf.xml', $xml);

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

        TempFile::createFolder('propel-classes');

        TempFile::move('propel/config.php', 'propel-classes/config.php');

        include($tmp . 'propel-classes/config.php');

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
     * @param  bool $withDrop
     *
     * @return string
     * @throws \Exception
     */
    public static function updateSchema($withDrop = false)
    {
        $sql = self::getSqlDiff($withDrop);

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
            $lastStatus = '';
            if ('mysql' == Kryn::getSystemConfig()->getDatabase()->getMainConnection()->getType()) {
                $lastStatus = dbExFetch('SHOW ENGINE INNODB STATUS');
                $lastStatus = "\n" . $lastStatus['Status'];
            }
            throw new \PDOException($e->getMessage() . ' in SQL: ' . $query.$lastStatus);
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

        $schemeData = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n  <database name=\"kryn\" defaultIdMethod=\"native\"\n";

        $krynBehavior = '<behavior name="\\Core\\KrynBehavior" />';

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

                $newSchema .= "$krynBehavior</database>";

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
            self::writeBuildProperties();
            self::collectSchemas();
        }

        //remove all migration files
        $files = find($tmp . 'propel/generated-migrations/PropelMigration_*.php');
        if ($files[0]) {
            unlink($files[0]);
        }

        $platform = Kryn::getSystemConfig()->getDatabase()->getMainConnection()->getType();
        $platform = ucfirst($platform) . 'Platform';

        $input = new ArrayInput(array(
             '--input-dir' => $tmp . 'propel/',
             '--output-dir' => $tmp . 'propel/build/',
             '--platform' => $platform,
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
     * @throws Exception
     */
    public static function writeBuildProperties()
    {
        $tmp = self::getTempFolder();

        if (!TempFile::createFolder('propel/')) {
            throw new \Exception('Can not create propel folder in ' . $tmp . 'propel/');
        }

        $platform = Kryn::getSystemConfig()->getDatabase()->getMainConnection()->getType();
        $platform = ucfirst($platform) . 'Platform';

        $properties = '
propel.mysql.tableType = InnoDB

propel.tablePrefix = ' . Kryn::getSystemConfig()->getDatabase()->getPrefix() . '
propel.platform = ' . $platform . '
propel.database.encoding = utf8
propel.project = kryn

propel.namespace.autoPackage = true
propel.packageObjectModel = true
propel.behavior.workspace.class = lib.WorkspaceBehavior
';
        return TempFile::setContent('propel/build.properties', $properties);

    }

}
