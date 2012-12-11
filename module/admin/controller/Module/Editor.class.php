<?php

namespace Admin\Module;

use Core\Kryn;
use Core\SystemFile;
use Admin\Module\Manager;

class Editor {

    public function getConfig($pName){
        return Manager::loadInfo($pName);
    }

    public function setConfig($pName, $pConfig){
        Manager::prepareName($pName);

        $json = json_format(json_encode($pConfig));

        $path = "core/config.json";

        if ($pName != 'kryn')
            $path = PATH_MODULE . "$pName/config.json";


        if (!file_exists($path) && !touch($path)){
            throw new \FileNotWritableException(tf('Can not create %s for module %s.', $path ,$pName));
        }

        if (!is_writeable($path)){
            throw new \FileNotWritableException(tf('The config file %s for module %s is not writeable.', $path ,$pName));
        }

        return Kryn::fileWrite($path, $json);
    }

    public static function getWindows($pName) {
        Manager::prepareName($pName);


        $classes   = find(PATH_MODULE . $pName . '/*', true);
        $windows   = array();
        $whiteList = array('\Admin\ObjectWindow');

        $c = strlen(PATH_MODULE.$pName.'/');

        foreach ($classes as $class){

            $content = SystemFile::getContent($class);

            if (preg_match('/class[\s\t]+([a-zA-Z0-9_]+)[\s\t]+extends[\s\t]+([a-zA-Z0-9_\\\\]*)[\s\t\n]*{/', $content, $matches)){
                if (in_array($matches[2], $whiteList)){

                    $clazz = $matches[1];

                    preg_match('/namespace ([a-zA-Z0-9_\\\\]*)/', $content, $namespace);
                    $namespace = $namespace[1];
                    if ($namespace)
                        $clazz = $namespace.'\\'.$clazz;

                    $clazz = '\\'.$clazz;

                    $windows[substr($class, $c)] = $clazz;
                }
            }

        }

        return $windows;
    }


    public function getPlugins($pName) {
        Manager::prepareName($pName);

        $config = $this->getConfig($pName);
        if ($config['noConfig'])
            throw new \ModuleNotFoundException(tf('Module %s not found.', $pName));

        return $config['plugins'];
    }

    public function savePlugins($pName) {
        Manager::prepareName($pName);

        $config = $this->getConfig($pName);

        $plugins = json_decode(getArgv('plugins'), true);
        $config['plugins'] = $plugins;

        return $this->setConfig($pName, $config);
    }



    public function getObjects($pName) {
        Manager::prepareName($pName);
        $config = $this->getConfig($pName);
        return $config['objects'];
    }

    public function saveObjects($pName) {
        Manager::prepareName($pName);

        $config = $this->getConfig($pName);

        $objects = json_decode(getArgv('objects'), true);
        $config['objects'] = $objects;

        return $this->setConfig($pName, $config);
    }


    public function getModel($pName){
        Manager::prepareName($pName);

        $path = PATH_MODULE . "$pName/model.xml";

        if (!file_exists($path)){
            throw new \FileNotExistException(tf('The config file %s for %s does not exist.', $path ,$pName));
        }

        return file_get_contents($path);

    }

    public function saveModel($pName, $pModel){
        Manager::prepareName($pName);

        $path = PATH_MODULE . "$pName/model.xml";

        if (!is_writable($path)){
            throw new \FileNotWritableException(tf('The model file %s for %s is not writable.', $path ,$pName));
        }

        if (!@file_put_contents($path, $pModel)){
            throw new \FileIOErrorException(tf('Can not write model file %s for %s.', $path ,$pName));
        }

        return true;

    }

    public function getColumnFromField($pObject, $pFieldKey, $pField, &$pTable, &$pDatabase, &$pRefColumn = null){

        $columns = array();
        $column  = array();
        if ($pRefColumn)
            $column =& $pRefColumn;


        $object =& kryn::$objects[$pObject];


        switch(strtolower($pField['type'])){

            case 'textarea':
            case 'wysiwyg':
            case 'codemirror':
            case 'textlist':
            case 'filelist':
            case 'layoutelement':
            case 'textlist':
            case 'array':
            case 'fieldtable':
            case 'fieldcondition':
            case 'objectcondition':
            case 'filelist':

                $column['type'] = 'LONGVARCHAR';
                unset($column['size']);
                break;

            case 'text':
            case 'password':
            case 'files':

                $column['type'] = 'VARCHAR';

                if ($pField['maxlength']) $column['size'] = $pField['maxlength'];
                break;

            case 'page':
                $column['type'] = 'INTEGER';
                unset($column['size']);
                break;

            case 'file':
            case 'folder':

                $column['type'] = 'VARCHAR';
                $column['size'] = 255;
                break;

            case 'properties':
                $column['type'] = 'OBJECT';
                unset($column['size']);
                break;

            case 'select':

                if ($pField['multi']){
                    $column['type'] = 'LONGVARCHAR';
                } else {
                    $column['type'] = 'VARCHAR';
                    $column['size'] = 255;
                }

                break;

            case 'lang':

                $column['type'] = 'VARCHAR';
                $column['size'] = 3;

                break;

            case 'number':

                $column['type'] = 'INTEGER';

                if ($pField['maxlength']) $column['size'] = $pField['maxlength'];

                if ($pField['number_type'])
                    $column['type'] = $pField['number_type'];

                break;

            case 'checkbox':

                $column['type'] = 'BOOLEAN';
                break;

            case 'custom':

                if ($pField['column']){
                    foreach ($pField['column'] as $k => $v){
                        $column[$k] = $v;
                    }
                }

                break;

            case 'date':
            case 'datetime':

                if ($pField['asUnixTimestamp'] === false)
                    $column['type'] = $pField['type'] == 'date'? 'DATE':'TIMESTAMP';

                $column['type'] = 'BIGINT';

                break;

            case 'object':

                $foreignObject = kryn::$objects[$pField['object']];
                if (!$foreignObject) continue;

                if ($pField['objectRelation'] == 'n-1'){

                    $primaries = \Core\Object::getPrimaries($pField['object']);
                    if (count(primaries) > 1){
                        //define extra columns
                        foreach ($primaries as $key => $primary){
                            $columnId = $pFieldKey.ucfirst($key);
                            $columns[$columnId] = $pField;

                            $this->getColumnFromField($pObject, $columnId, $primary, $pTable, $pDatabase, $columns[$columnId]);

                        }
                        
                        return $columns;

                    } else if(count(primaries) == 1){
                        $this->getColumnFromField($pObject, key($primaries), current($primaries), $pTable, $pDatabase, $column);
                    }

                } else {

                    //n-n, we need a extra table
                                        
                    $tableName = $pField['objectRelationTable'] ? $pField['objectRelationTable'] : $pObject.'_'.$pField['object'];

                    $tablePhpName = underscore2Camelcase($tableName);
                    if ($pField['objectRelationPhpName'])
                        $tablePhpName = $pField['objectRelationPhpName'];

                    //search if we've already the table defined.
                    $tables = $pDatabase->xpath('table[@name=\''.$tableName.'\']');

                    if (!$tables) {
                        $relationTable = $pDatabase->addChild('table');
                        $relationTable['name'] = $tableName;
                        $relationTable['isCrossRef'] = "true";
                    } else {
                        $relationTable = current($tables);
                    }

                    $relationTable['phpName'] = $tablePhpName;

                    $foreignKeys = array();

                    //left columns
                    $leftPrimaries = \Core\Object::getPrimaries($pObject);
                    foreach ($leftPrimaries as $key => $primary){

                        $name = $pObject.'_'.$key;
                        $cols = $relationTable->xpath('column[@name=\''.$name.'\']');
                        $foreignKeys[$object['table']][$key] = $name;
                        if ($cols) continue;

                        $col = $relationTable->addChild('column');
                        $col['name'] = $name;
                        $this->getColumnFromField($pObject, $key, $primary, $pTable, $pDatabase, $col);
                        unset($col['autoIncrement']);
                        $col['required'] = "true";


                    }

                    //right columns
                    $leftPrimaries = \Core\Object::getPrimaries($pField['object']);
                    foreach ($leftPrimaries as $key => $primary){

                        $name = $pField['object'].'_'.$key;
                        $foreignKeys[$foreignObject['table']][$key] = $name;
                        $cols = $relationTable->xpath('column[@name=\''.$name.'\']');
                        if ($cols) continue;

                        $col = $relationTable->addChild('column');
                        $col['name'] = $name;
                        $this->getColumnFromField($pObject, $key, $primary, $pTable, $pDatabase, $col);
                        unset($col['autoIncrement']);
                        $col['required'] = "true";


                    }

                    //foreign keys
                    foreach ($foreignKeys as $table => $keys){

                        $foreigns = $relationTable->xpath('foreign-key[@foreignTable=\''.$table.'\']');
                        if ($foreigns) $foreignKey = current($foreigns);
                        else $foreignKey = $relationTable->addChild('foreign-key');

                        $foreignKey['foreignTable'] = $table;

                        if ($table == $foreignObject['table']){
                            $foreignKey['phpName'] = ucfirst($pFieldKey);
                        } else {
                            $foreignKey['phpName'] = ucfirst($pFieldKey).ucfirst($pObject);
                        }

                        foreach ($keys as $k => $v){

                            $references = $foreignKey->xpath('reference[@local=\''.$v.'\']');
                            if ($references) $reference = current($references);
                            else $reference = $foreignKey->addChild('reference');

                            $reference['local'] = $v;
                            $reference['foreign'] = $k;

                        }
                    }
                    
                    $vendors = $relationTable->xpath('vendor[@type=\'mysql\']');
                    if ($vendors) $vendor = current($vendors);
                    else $vendor = $relationTable->addChild('vendor');
                    $vendor['type'] = 'mysql';


                    $params = $vendor->xpath('parameter[@name=\'Charset\']');
                    if ($params) $param = current($params);
                    else $param = $vendor->addChild('parameter');

                    $param['name'] = 'Charset';
                    $param['value'] = 'utf8';

                    return false;

                }

                break;

            default:
                return false;

        }

        if ($pField['empty'] === 0 || $pField['empty'] === false)
            $column['required'] = "true";

        if ($pField['primaryKey']) $column['primaryKey'] = "true";
        if ($pField['autoIncrement']) $column['autoIncrement'] = "true";

        $columns[camelcase2Underscore($pFieldKey)] = $column;

        return $columns;
    }

    public function setModelFromObjects($pName){

        Manager::prepareName($pName);
        $config = $this->getConfig($pName);

        $result = array();
        foreach ($config['objects'] as $objectKey => $def){
            $result[$objectKey] = $this->setModelFromObject($pName, $objectKey);
        }

        return $result;
    }

    public function setModelFromObject($pName, $pObject){

        Manager::prepareName($pName);
        $config = $this->getConfig($pName);

        if (!$object = $config['objects'][$pObject])
            throw new \Exception(tf('Object %s in %s does not exist.', $pObject, $pName));

        if ($config['objects'][$pObject]['dataModel'] != 'propel') return false;

        $path = PATH_MODULE . "$pName/model.xml";

        if (!file_exists($path) && !touch($path))
            throw new \FileNotWritableException(tf('The module folder of module %s is not writable.', $pName));

        if (!is_writable($path))
            throw new \FileNotWritableException(tf('The model file %s for %s is not writable.', $path ,$pName));
        

        if (file_exists($path) && filesize($path)){
            $xml = @simplexml_load_file($path);

            if ($xml === false){
                $errors = libxml_get_errors();
                throw new \Exception(tf('Parse error in %s: %s', $path, json_format($errors)));
            }
        } else {
            $xml = simplexml_load_string('<database></database>');
        }

        //search if we've already the table defined.
        $tables = $xml->xpath('table[@name=\''.$object['table'].'\']');

        if (!$tables) $objectTable = $xml->addChild('table');
        else $objectTable = current($tables);

        if (!$object['table']) throw new \Exception(tf('The object %s has no table defined.', $pObject));

        $objectTable['name'] = $object['table'];
        $objectTable['phpName'] = $object['propelClassName'] ?: ucfirst($pObject);

        $columnsDefined = array();

        foreach ($object['fields'] as $fieldKey => $field){

            $columns = $this->getColumnFromField($pObject, $fieldKey, $field, $objectTable, $xml);

            if (!$columns) continue;

            foreach ($columns as $key => $column){
                //column exist?
                $eColumns = $objectTable->xpath('column[@name =\''.$key.'\']');

                if ($eColumns) {

                    $newCol = current($eColumns);
                    if ($newCol['custom'] == true) continue;

                } else $newCol = $objectTable->addChild('column');

                $newCol['name'] = $key;
                $columnsDefined[] = $key;

                foreach ($column as $k => $v){
                    $newCol[$k] = $v;
                }
            }

        }


        $vendors = $objectTable->xpath('vendor[@type=\'mysql\']');
        if ($vendors) $vendor = current($vendors);
        else $vendor = $objectTable->addChild('vendor');
        $vendor['type'] = 'mysql';

        $params = $vendor->xpath('parameter[@name=\'Charset\']');
        if ($params) $param = current($params);
        else $param = $vendor->addChild('parameter');

        $param['name'] = 'Charset';
        $param['value'] = 'utf8';


        $dom = new \DOMDocument;
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($xml->asXML());
        $dom->formatOutput = true;
        SystemFile::setContent($path, $dom->saveXml());

        return true;

    }


    public function saveGeneral($pName) {
        Manager::prepareName($pName);

        $config = $this->getConfig($pName);

        if (getArgv('owner') > 0)
            $config['owner'] = getArgv('owner');

        $config['title'] = getArgv('title');
        $config['desc'] = getArgv('desc');
        $config['tags'] = getArgv('tags');

        $config['version'] = getArgv('version');
        $config['community'] = getArgv('community');
        $config['writableFiles'] = getArgv('writableFiles');
        $config['category'] = getArgv('category');
        $config['depends'] = getArgv('depends');

        return $this->setConfig($pName, $config);
    }


    public function saveEntryPoints($pName, $pEntryPoints){
        Manager::prepareName($pName);

        $config = $this->getConfig($pName);
        $config['admin'] = json_decode($pEntryPoints, true);

        return $this->setConfig($pName, $config);
    }

    public function saveWindowDefinition($pClass){

        if (substr($pClass, 0, 1) != '\\')
            $pClass = '\\'.$pClass;

        $actualPath = str_replace('\\', '/', lcfirst(substr($pClass, 1))).'.class.php';
        $fSlash = strpos($actualPath, '/');
        $actualPath = 'module/'.substr($actualPath, 0, $fSlash).'/controller/'.substr($actualPath, $fSlash+1);

        $general = getArgv('general');
        $path = $general['file'];

        $sourcecode = "<?php\n\n";

        $lSlash = strrpos($pClass, '\\');
        $className = $lSlash !== -1 ? substr($pClass, $lSlash+1) : $pClass;

        $parentClass = '\Admin\ObjectWindow';

        $namespace = substr(substr($pClass, 1), 0, $lSlash);
        if (substr($namespace, -1) == '\\')
            $namespace = substr($namespace, 0, -1);

        $sourcecode .= "namespace $namespace;\n \n";

        $sourcecode .= 'class '.$className.' extends '.$parentClass." {\n\n";

        if (count($fields = getArgv('fields')) > 0)
            $this->addVar($sourcecode, 'fields', $fields);

        $listing = getArgv('list');
        foreach ($listing as $listVarName => $listVar){
            $this->addVar($sourcecode, $listVarName, $listVar);
        }

        $general = getArgv('general');
        $blacklist = array('class', 'file');
        foreach ($general as $varName => $var){
            if (array_search($varName, $blacklist) !== false) continue;
            $this->addVar($sourcecode, $varName, $var);
        }

        $methods = getArgv('methods');
        foreach ($methods as $name => $source){
            $this->addMethod($sourcecode, $source);
        }

        $sourcecode .= "\n}\n";

        $sourcecode = str_replace("\r", '', $sourcecode);

        error_log($path);

        SystemFile::setContent($path, $sourcecode);

        return true;

    }

    public function addMethod(&$pSourceCode, $pSource){

        $pSourceCode .= substr($pSource, 6, -4)."\n";

    }

    public function addVar(&$pSourceCode, $pName, $pVar, $pVisibility = 'public', $pStatic = false){

        $pSourceCode .=
            "    "
            .$pVisibility.($pStatic?' static':'').' $'.$pName.' = '.var_export(self::toVar($pVar), true)
            .";\n\n";

    }

    public function toVar($pValue){
        if ($pValue == 'true')   return true;
        if ($pValue == 'false')  return false;
        if (is_numeric($pValue)) return $pValue+0;
        return $pValue;
    }


    public function getWindowDefinition($pClass){

        if (substr($pClass, 0, 1) != '\\')
            $pClass = '\\'.$pClass;

        if (!class_exists($pClass)) throw new \ClassNotFoundException(tf('Class %s not found.', $pClass));

        $reflection = new \ReflectionClass($pClass);
        $path = substr($reflection->getFileName(), strlen(PATH));

        $content = explode("\n", SystemFile::getContent($path));

        $actualPath = str_replace('\\', '/', lcfirst(substr($pClass, 1))).'.class.php';
        $fSlash = strpos($actualPath, '/');
        $actualPath = 'module/'.substr($actualPath, 0, $fSlash).'/controller/'.substr($actualPath, $fSlash+1);

        $res = array(
            'class' => $pClass,
            'file' => $path,
            'actualFile' => $actualPath,
            'properties' => array(
                '__file__' => $path
            )
        );

        $obj = new $pClass(null, true);
        foreach ($obj as $k => $v)
            $res['properties'][$k] = $v;

        $parent = $reflection->getParentClass();
        $parentClass = $parent->name;

        $methods = $reflection->getMethods();

        foreach ($methods as $method){
            if ($method->class == $pClass){

                $code = '';
                if ($code) $code = "    $code\n";
                for ($i = $method->getStartLine()-1; $i < $method->getEndLine(); $i++){
                    $code .= $content[$i]."\n";
                }

                if ($doc = $method->getDocComment())
                    $code = "    $doc\n$code";

                $res['methods'][$method->name] = str_replace("\r", '', $code);
            }
        }

        if (getArgv('parentClass')){
            $parentClass = getArgv('parentClass', 2);
        }

        self::extractParentClassInformation($parentClass, $res['parentMethods']);

        unset($res['properties']['_fields']);

        return $res;
    }

    /**
     * Extracts parent's class information.
     *
     * @internal
     * @param $pParentClass
     * @param $pMethods
     * @throws \ClassNotFoundException
     */
    public static function extractParentClassInformation($pParentClass, &$pMethods){

        if (!class_exists($pParentClass)) throw new \ClassNotFoundException();

        $reflection = new \ReflectionClass($pParentClass);
        $parentPath = substr($reflection->getFileName(), strlen(PATH));

        $parentContent = explode("\n", SystemFile::getContent($parentPath));
        $parentReflection = new \ReflectionClass($pParentClass);

        $methods = $parentReflection->getMethods();
        foreach ($methods as $method){
            if ($pMethods[$method->name]) continue;

            if ($method->class == $pParentClass){

                $code = '';
                for ($i = $method->getStartLine()-1; $i < $method->getEndLine(); $i++){

                    $code .= $parentContent[$i]."\n";
                    if (strpos($parentContent[$i], '{'))
                        break;

                }

                if ($doc = $method->getDocComment())
                    $code = "    $doc\n$code";

                $pMethods[$method->name] = str_replace("\r", '', $code);
            }
        }

        $parent = $parentReflection->getParentClass();

        if ($parent){
            self::extractParentClassInformation($parent->name, $pMethods);
        }

    }


    /**
     * Creates a new CRUD object window.
     *
     * @param string $pClass
     * @param string $pModule Name of the module
     * @param bool   $pForce
     *
     * @return bool
     * @throws \FileAlreadyExistException
     */
    public function newWindow($pClass, $pModule, $pForce = false){

        if (class_exists($pClass) && !$pForce){
            $reflection = new \ReflectionClass($pClass);
            throw new \FileAlreadyExistException(tf('Class already exist in %s', $reflection->getFileName()));
        }

        $actualPath = str_replace('\\', '/', lcfirst(substr($pClass, 1))).'.class.php';
        $fSlash = strpos($actualPath, '/');
        $actualPath = 'module/'.$pModule.'/controller/'.substr($actualPath, $fSlash+1);

        if (file_exists($actualPath) && !$pForce){
            throw new \FileAlreadyExistException(tf('File already exist, %s', $actualPath));
        }

        $sourcecode = "<?php\n\n";

        $lSlash = strrpos($pClass, '\\');
        $className = $lSlash !== -1 ? substr($pClass, $lSlash+1) : $pClass;

        $parentClass = '\Admin\ObjectWindow';

        $namespace = substr(substr($pClass, 1), 0, $lSlash);
        if (substr($namespace, -1) == '\\')
            $namespace = substr($namespace, 0, -1);

        $sourcecode .= "namespace $namespace;\n \n";

        $sourcecode .= 'class '.$className.' extends '.$parentClass." {\n\n";

        $sourcecode .= "}\n";

        error_log($actualPath);

        return SystemFile::setContent($actualPath, $sourcecode);
    }





}