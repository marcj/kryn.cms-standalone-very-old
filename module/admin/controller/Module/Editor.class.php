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

        if (!is_writeable($path)){
            throw new \FileNotWritableException(tf('The config file %s for %s is not writeable.', $path ,$pName));
        }

        return Kryn::fileWrite($path, $json);
    }

    public static function getWindows($pName) {
        Manager::prepareName($pName);

        $classes   = find(PATH_MODULE . $pName . '/*.class.php');
        $windows   = array();
        $whiteList = array('windowlist', 'windowadd', 'windowedit', 'windowcombine');

        foreach ($classes as $class){

            $content = Kryn::fileRead($class);

            if (preg_match('/class ([a-zA-Z0-9_]*) extends (admin|)([a-zA-Z0-9_]*)\s*{/', $content, $matches)){
                if (in_array(strtolower($matches[3]), $whiteList))
                    $windows[] = $matches[1];
            }

        }

        return $windows;
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
                break;

            case 'text':
            case 'password':
            case 'files':

                $column['type'] = 'VARCHAR';

                if ($pField['maxlength']) $column['size'] = $pField['maxlength'];
                break;

            case 'page':
                $column['type'] = 'INTEGER';
                break;

            case 'file':
            case 'folder':

                $column['type'] = 'VARCHAR';
                $column['size'] = 255;
                break;

            case 'properties':
                $column['type'] = 'OBJECT';

            case 'select':

                if ($pField['multi']){
                    $column['type'] = 'LONGVARCHAR';
                } else {
                    $column['type'] = 'VARCHAR';
                    $column['size'] = 255;
                }


            case 'lang':

                $column['type'] = 'VARCHAR';
                $column['size'] = 3;

            case 'number':

                $column['type'] = 'INTEGER';

                if ($pField['maxlength']) $column['size'] = $pField['maxlength'];

                if ($pField['number_type'])
                    $column['type'] = $pField['number_type'];

                break;

            case 'checkbox':

                $column['type'] = '';
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

            case 'object':

                $foreignObject = kryn::$objects[$pField['object']];
                if (!$foreignObject) continue;

                if ($pField['objectRelation'] == 'n-1'){

                    $primaries = \Core\Object::getPrimaries($pField['object']);
                    if (count(primaries) > 1){
                        //define extra columns
                        foreach ($primaries as $key => $primary){
                            $columnId = $pFieldKey.'_'.$key;
                            $columns[$columnId] = $pField;

                            $this->getColumnFromField($columnId, $primary, $pTable, $pDatabase, $columns[$columnId]);

                        }
                        
                        return $columns;

                    } else if(count(primaries) == 1){
                        $this->getColumnFromField($pObject, key($primaries), current($primaries), $pTable, $pDatabase, $column);
                    }

                } else {

                    //n-n, we need a extra table
                                        
                    $tableName = $pField['objectRelationTable'] ? $pField['objectRelationTable'] : $pObject.'_'.$pField['object'];

                    //search if we've already the table defined.
                    $tables = $pDatabase->xpath('table[@name=\''.$tableName.'\']');
                    $object = kryn::$objects[$pObject];

                    if (!$tables) {
                        $relationTable = $pDatabase->addChild('table');
                        $relationTable['name'] = $tableName;
                        $relationTable['isCrossRef'] = "true";
                    } else {
                        $relationTable = current($tables);
                    }

                    if ($pField['objectRelationPhpName'])
                        $relationTable['phpName'] = $pField['objectRelationPhpName'];

                    $foreignKeys = array();

                    //left columns
                    $leftPrimaries = \Core\Object::getPrimaries($pObject);
                    foreach ($leftPrimaries as $key => $primary){

                        $name = $pObject.'_'.$key;
                        $cols = $relationTable->xpath('column[@name=\''.$name.'\']');
                        if ($cols) continue;

                        $col = $relationTable->addChild('column');
                        $col['name'] = $name;
                        $this->getColumnFromField($pObject, $key, $primary, $pTable, $pDatabase, $col);
                        unset($col['autoIncrement']);
                        $col['required'] = "true";

                        $foreignKeys[$object['table']][$key] = $col['name'];

                    }

                    //right columns
                    $leftPrimaries = \Core\Object::getPrimaries($pField['object']);
                    foreach ($leftPrimaries as $key => $primary){

                        $name = $pField['object'].'_'.$key;
                        $cols = $relationTable->xpath('column[@name=\''.$name.'\']');
                        if ($cols) continue;

                        $col = $relationTable->addChild('column');
                        $col['name'] = $name;
                        $this->getColumnFromField($pObject, $key, $primary, $pTable, $pDatabase, $col);
                        unset($col['autoIncrement']);
                        $col['required'] = "true";

                        $foreignKeys[$foreignObject['table']][$key] = $col['name'];

                    }

                    //foreign keys
                    foreach ($foreignKeys as $table => $keys){

                        $foreigns = $relationTable->xpath('foreign-key[@foreignTable=\''.$table.'\']');
                        if ($foreigns) $foreignKey = current($foreigns);
                        else $foreignKey = $relationTable->addChild('foreign-key');

                        $foreignKey['foreignTable'] = $table;

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

        $columns[$pFieldKey] = $column;

        return $columns;
    }

    public function setModelFromObject($pName, $pObject){

        Manager::prepareName($pName);
        $config = $this->getConfig($pName);

        if (!$object = $config['objects'][$pObject])
            throw new \Exception(tf('Object %s in %s does not exist.', $pObject, $pName));

        $path = PATH_MODULE . "$pName/model.xml";

        if (!is_writable($path)){
            throw new \FileNotWritableException(tf('The model file %s for %s is not writable.', $path ,$pName));
        }

        if (file_exists($path)){
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
        $objectTable['phpName'] = $object['phpClass'];

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
}