<?php

namespace Core\ORM\Sync;

use Admin\Exceptions\BuildException;
use Core\Bundle;
use Core\Config\Object;
use Core\SystemFile;

class Propel implements SyncInterface {
    /**
     * @param  string $pObject
     * @param  string $pFieldKey
     * @param  array $pField
     * @param  xml $pTable
     * @param  xml $pDatabase
     * @param  xml $pRefColumn
     * @param  Bundle $bundle
     *
     * @return array|bool
     */
    public function getColumnFromField(
        $pObject,
        $pFieldKey,
        $pField,
        &$pTable,
        &$pDatabase,
        &$pRefColumn = null,
        Bundle $bundle = null
    )
    {

        $columns = array();
        $column = array();
        if ($pRefColumn) {
            $column =& $pRefColumn;
        }

        $object = \Core\Object::getDefinition($pObject);

        switch (strtolower($pField['type'])) {

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

                if ($pField['maxlength']) {
                    $column['size'] = $pField['maxlength'];
                }
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

                if ($pField['multi']) {
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

                if ($pField['maxlength']) {
                    $column['size'] = $pField['maxlength'];
                }

                if ($pField['number_type']) {
                    $column['type'] = $pField['number_type'];
                }

                break;

            case 'checkbox':

                $column['type'] = 'BOOLEAN';
                break;

            case 'custom':

                if ($pField['column']) {
                    foreach ($pField['column'] as $k => $v) {
                        $column[$k] = $v;
                    }
                }

                break;

            case 'date':
            case 'datetime':

                if ($pField['asUnixTimestamp'] === false) {
                    $column['type'] = $pField['type'] == 'date' ? 'DATE' : 'TIMESTAMP';
                }

                $column['type'] = 'BIGINT';

                break;

            case 'object':

                $foreignObject = \Core\Object::getDefinition($pField['object']);
                if (!$foreignObject) {
                    continue;
                }

                $relationName = $pField['objectRelationName'] ?: $foreignObject->getId();

                if ($pField['objectRelation'] == 'nTo1') {

                    $leftPrimaries = \Core\Object::getPrimaryList($pObject);
                    $rightPrimaries = \Core\Object::getPrimaries($pField['object']);

                    $foreignObject = \Core\Object::getDefinition($pField['object']);

                    if (!$foreignObject['table']) {
                        continue;
                    }

                    $foreigns = $pTable->xpath('foreign-key[@foreignTable=\'' . $foreignObject['table'] . '\']');
                    if ($foreigns) {
                        $foreignKey = current($foreigns);
                    } else {
                        $foreignKey = $pTable->addChild('foreign-key');
                    }

                    $foreignKey['phpName'] = $relationName;
                    $foreignKey['foreignTable'] = $foreignObject['table'];

                    if (count($rightPrimaries) == 1) {

                        $references = $foreignKey->xpath('reference[@local=\'' . camelcase2Underscore($pFieldKey) . '\']');
                        if ($references) {
                            $reference = current($references);
                        } else {
                            $reference = $foreignKey->addChild('reference');
                        }

                        $reference['local'] = camelcase2Underscore($pFieldKey);
                        $reference['foreign'] = key($rightPrimaries);

                    } else {

                        //add left primary keys
                        foreach ($rightPrimaries as $key => $def) {
                            $references = $pTable->xpath('reference[@local=\'' . $pFieldKey . '_' . $key . '\']');
                            if ($references) {
                                $reference = current($references);
                            } else {
                                $reference = $foreignKey->addChild('reference');
                            }

                            $reference['local'] = camelcase2Underscore($pFieldKey) . '_' . $key;
                            $reference['foreign'] = $key;

                            //create additional fields
                            $this->getColumnFromField(
                                $pObject,
                                underscore2Camelcase($pFieldKey . '_' . $key),
                                $def,
                                $pTable,
                                $pDatabase,
                                $bundle
                            );
                        }
                    }

                } else {

                    //n-n, we need a extra table

                    $probablyName = $bundle->getName() . '_' . camelcase2Underscore(
                            \Core\Object::getName($pObject)
                        ) . '_' . camelcase2Underscore($pFieldKey) . '_relation';

                    $tableName = $pField['objectRelationTable'] ? $pField['objectRelationTable'] : $probablyName;

                    //search if we've already the table defined.
                    $tables = $pDatabase->xpath('table[@name=\'' . $tableName . '\']');

                    if (!$tables) {
                        $relationTable = $pDatabase->addChild('table');
                        $relationTable['name'] = $tableName;
                        $relationTable['isCrossRef'] = "true";
                    } else {
                        $relationTable = current($tables);
                    }

                    $relationTable['phpName'] = $relationName;

                    $foreignKeys = array();

                    //left columns
                    $leftPrimaries = \Core\Object::getPrimaries($pObject);
                    foreach ($leftPrimaries as $key => $primary) {

                        $name = strtolower(\Core\Object::getName($pObject)) . '_' . $key;
                        $cols = $relationTable->xpath('column[@name=\'' . $name . '\']');
                        $foreignKeys[$object['table']][$key] = $name;
                        if ($cols) {
                            continue;
                        }

                        $col = $relationTable->addChild('column');
                        $col['name'] = $name;
                        $this->getColumnFromField($pObject, $key, $primary, $pTable, $pDatabase, $col, $bundle);
                        unset($col['autoIncrement']);
                        $col['required'] = "true";

                    }

                    //right columns
                    $rightPrimaries = \Core\Object::getPrimaries($pField['object']);
                    foreach ($rightPrimaries as $key => $primary) {

                        $name = camelcase2Underscore(\Core\Object::getName($pField['object'])) . '_' . $key;
                        $foreignKeys[$foreignObject['table']][$key] = $name;
                        $cols = $relationTable->xpath('column[@name=\'' . $name . '\']');
                        if ($cols) {
                            continue;
                        }

                        $col = $relationTable->addChild('column');
                        $col['name'] = $name;
                        $this->getColumnFromField($pObject, $key, $primary, $pTable, $pDatabase, $col, $bundle);
                        unset($col['autoIncrement']);
                        $col['required'] = "true";

                    }

                    //foreign keys
                    foreach ($foreignKeys as $table => $keys) {

                        $foreigns = $relationTable->xpath('foreign-key[@foreignTable=\'' . $table . '\']');
                        if ($foreigns) {
                            $foreignKey = current($foreigns);
                        } else {
                            $foreignKey = $relationTable->addChild('foreign-key');
                        }

                        $foreignKey['foreignTable'] = $table;

                        if ($table == $foreignObject['table']) {
                            $foreignKey['phpName'] = ucfirst($pFieldKey);
                        } else {
                            $foreignKey['phpName'] = ucfirst($pFieldKey) . \Core\Object::getName($pObject);
                        }

                        if ($object['workspace']) {
                            $references = $foreignKey->xpath('reference[@local=\'workspace_id\']');
                            if ($references) {
                                $reference = current($references);
                            } else {
                                $reference = $foreignKey->addChild('reference');
                            }
                            $reference['local'] = 'workspace_id';
                            $reference['foreign'] = 'workspace_id';
                        }

                        foreach ($keys as $k => $v) {

                            $references = $foreignKey->xpath('reference[@local=\'' . $v . '\']');
                            if ($references) {
                                $reference = current($references);
                            } else {
                                $reference = $foreignKey->addChild('reference');
                            }

                            $reference['local'] = $v;
                            $reference['foreign'] = $k;

                        }
                    }

                    //workspace behavior if $pObject is workspaced
                    if ($object['workspace']) {

                        $behaviors = $relationTable->xpath('behavior[@name=\'workspace\']');
                        if ($behaviors) {
                            $behavior = current($behaviors);
                        } else {
                            $behavior = $relationTable->addChild('behavior');
                        }
                        $behavior['name'] = 'workspace';
                    }

                    $vendors = $relationTable->xpath('vendor[@type=\'mysql\']');
                    if ($vendors) {
                        $vendor = current($vendors);
                    } else {
                        $vendor = $relationTable->addChild('vendor');
                    }
                    $vendor['type'] = 'mysql';

                    $params = $vendor->xpath('parameter[@name=\'Charset\']');
                    if ($params) {
                        $param = current($params);
                    } else {
                        $param = $vendor->addChild('parameter');
                    }

                    $param['name'] = 'Charset';
                    $param['value'] = 'utf8';

                    return false;

                }

                break;

            default:
                return false;

        }

        if ($pField['empty'] === 0 || $pField['empty'] === false) {
            $column['required'] = "true";
        }

        if ($pField['primaryKey']) {
            $column['primaryKey'] = "true";
        }
        if ($pField['autoIncrement']) {
            $column['autoIncrement'] = "true";
        }

        $columns[camelcase2Underscore($pFieldKey)] = $column;

        return $columns;
    }



    public function syncObject(Bundle $bundle, Object $object) {
        $path = $bundle->getPath() . 'Resources/config/models.xml';
        if (!file_exists($path) && !touch($path)) {
            throw new BuildException(tf('File `%s` is not writeable.', $path));
        }

        if (file_exists($path) && filesize($path)) {
            $xml = @simplexml_load_file($path);

            if ($xml === false) {
                $errors = libxml_get_errors();
                throw new BuildException(tf('Parse error in %s: %s', $path, json_format($errors)));
            }
        } else {
            $xml = simplexml_load_string('<database></database>');
        }
        $xml['namespace'] = ucfirst($bundle->getNamespace());

        //search if we've already the table defined.
        $tables = $xml->xpath('table[@name=\'' . $object['table'] . '\']');

        if (!$tables) {
            $objectTable = $xml->addChild('table');
        } else {
            $objectTable = current($tables);
        }

        if (!$object['table']) {
            throw new BuildException(tf('The object `%s` has no table defined.', $object->getId()));
        }

        $objectTable['name'] = $object['table'];
        $objectTable['phpName'] = $object['propelClassName'] ? : ucfirst($object->getId());

        $columnsDefined = array();

        foreach ($object->getFields() as $field) {

            $columns = $this->getColumnFromField(
                ucfirst($bundle->getNamespace()) . '\\' . $object->getId(),
                $field->getId(),
                $field,
                $objectTable,
                $xml,
                $null,
                $bundle
            );

            if (!$columns) {
                continue;
            }

            foreach ($columns as $key => $column) {
                //column exist?
                $eColumns = $objectTable->xpath('column[@name =\'' . $key . '\']');

                if ($eColumns) {

                    $newCol = current($eColumns);
                    if ($newCol['custom'] == true) {
                        continue;
                    }

                } else {
                    $newCol = $objectTable->addChild('column');
                }

                $newCol['name'] = $key;
                $columnsDefined[] = $key;

                foreach ($column as $k => $v) {
                    $newCol[$k] = $v;
                }
            }

        }

        if ($object['workspace']) {
            $behaviors = $objectTable->xpath('behavior[@name=\'workspace\']');
            if ($behaviors) {
                $behavior = current($behaviors);
            } else {
                $behavior = $objectTable->addChild('behavior');
            }
            $behavior['name'] = 'workspace';
        }

        $vendors = $objectTable->xpath('vendor[@type=\'mysql\']');
        if ($vendors) {
            $vendor = current($vendors);
        } else {
            $vendor = $objectTable->addChild('vendor');
        }
        $vendor['type'] = 'mysql';

        $params = $vendor->xpath('parameter[@name=\'Charset\']');
        if ($params) {
            $param = current($params);
        } else {
            $param = $vendor->addChild('parameter');
        }

        $param['name'] = 'Charset';
        $param['value'] = 'utf8';

        $dom = new \DOMDocument;
        $dom->preserveWhiteSpace = false;
        $dom->loadXML($xml->asXML());
        $dom->formatOutput = true;
        SystemFile::setContent($path, $dom->saveXml());

        return true;
    }
}