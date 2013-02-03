<?php

namespace Core;

class Object {

    /**
     * Array of instances of the object classes
     *
     * @var array
     */
    public static $instances = array();

    /**
     * Translates the internal url to the real path.
     *
     * Example: getUrl('file://45') => '/myImageFolder/Picture1.png'
     *          getUrl('news://4') => '/newspage/detail/my-news-title'
     *          getUrl('user://1') => '/userdetail/admini-strator'
     *
     * @link http://docu.kryn.org/developer/extensions/internal-url
     *
     * Can return additionally 'http(s)://myDomain/' at the beginning if the target
     * is on a different domain.
     *
     * @static
     * @param string $pInternalUrl
     * @param int    $pPluginContentElementId
     *
     * @return string|bool
     */
    public static function getUrl($pInternalUrl, $pPluginContentElementId){

        $pos = strpos($pInternalUrl,'://');
        $objectIds = substr($pInternalUrl, 0, $pos);
        $params = explode('/', substr($pInternalUrl, $pos+2));

        $objectDefinition = self::getDefinition($objectIds);
        if (!$objectDefinition) return false;

        if (method_exists($objectDefinition['_extension'], $objectDefinition['urlGetter'])){
            return call_user_func(array($objectDefinition['_extension'], $objectDefinition['urlGetter']), $params);

        } else return false;
    }


    /**
     * Clears the instances cache.
     *
     */
    public static function cleanup(){
        self::$instances = array();
    }

    /**
     * Parse the internal object url scheme and return the information as array.
     *
     * Pattern:
     *    object://<object_key>[/<primay_values_url_encoded-1>][/<primay_values_url_encoded-n>][/?<options_as_querystring>]
     *
     * Examples:
     *
     * 1. object://news/1
     *   => returns the object news with primary value equal 1
     *
     * 2. object://news/id=1
     *   => equal as 1.
     *
     * 3. object://news/1/2
     *   => returns a list of the objects with primary value equal 1 or 2
     *
     * 4. object://news/id=1/id=2
     *   => equal as 3.
     *
     * 5. object://object_with_multiple_primary/2,54
     *   => returns the object with the first primary field equal 2 and second primary field equal 54
     *
     * 6. object://object_with_multiple_primary/2,54/34,55
     *   => returns a list of the objects
     *
     * 7. object://object_with_multiple_primary/id=2,parent_id=54/id=34,parent_id=55
     *   => equal as 6 if the first defined primary is 'id' and the second 'parent_id'
     *
     * 8. object://news/1?fields=title
     *   => equal as 1. but returns only the field title
     *
     * 9. object://news/1?fields=title,category_id
     *   => equal as 1. but returns only the field title and category_id
     *
     * 10. object://news?fields=title
     *   => returns all objects from type news
     *
     * 11. object://news?fields=title&limit=5
     *   => returns first 5 objects from type news
     *
     *
     * @static
     * @param string $pInternalUrl
     * @return array [object_key, object_id/s, queryParams]
     */
    public static function parseUrl($pInternalUrl){

        $pInternalUrl = trim($pInternalUrl);

        $list = false;

        $catch = 'object://';
        if (substr(strtolower($pInternalUrl),0,strlen($catch)) == $catch){
            $pInternalUrl = substr($pInternalUrl, strlen($catch));
        }

        $catch = 'objects://';
        if (substr(strtolower($pInternalUrl),0,strlen($catch)) == $catch){
            $list = true;
            $pInternalUrl = substr($pInternalUrl, strlen($catch));
        }

        $pos = strpos($pInternalUrl, '/');
        $questionPos = strpos($pInternalUrl, '?');

        if ($pos === false && $questionPos === false){
            return array(
                $pInternalUrl,
                false,
                array(),
                $list
            );
        }
        if ($pos === false && $questionPos != false)
            $objectKey = substr($pInternalUrl, 0, $questionPos);
        else
            $objectKey = substr($pInternalUrl, 0, $pos);

        if (strpos($objectKey, '%'))
            $objectKey = rawurldecode($objectKey);

        $params = array();

        if ($questionPos !== false){
            parse_str(substr($pInternalUrl, $questionPos+1), $params);

            if ($pos !== false)
                $objectIds = substr($pInternalUrl, $pos+1, $questionPos-($pos+1));

        } else if ($pos !== false)
            $objectIds = substr($pInternalUrl, $pos+1);

        $objectIds = self::parsePk($objectKey, $objectIds);

        if ($params && $params['condition']){
            $params['condition'] = json_decode($params['condition'], true);
        }

        return array(
            $objectKey,
            (!$objectIds) ? false : $objectIds,
            $params,
            $list
        );
    }

    /**
     * Get object's definition.
     *
     * @param string $pObjectKey `Core\Language` or `Core.Language`.
     * @return array
     */
    public static function getDefinition($pObjectKey){
        $pObjectKey = str_replace('.', '\\', $pObjectKey);
        $temp   = explode('\\', $pObjectKey);
        $module = strtolower($temp[0]);
        $name   = $temp[1];

        if (Kryn::$configs[$module] && ($res = Kryn::$configs[$module]['objects'][$name])){
            $res['_module'] = $module;
            return $res;
        }
    }

    /**
     * Cuts of the namespace name of a object key.
     * Core\Node => Node.
     *
     * @param string $pObjectKey
     * @return string
     */
    public static function getName($pObjectKey){
        $pObjectKey = str_replace('.', '\\', $pObjectKey);
        if (strpos($pObjectKey, '\\') === false) return $pObjectKey;
        $temp   = explode('\\', $pObjectKey);
        $name   = $temp[1];
        return $name;
    }

    /**
     * Returns true of the object is nested.
     *
     * @param string $pObjectKey
     * @return mixed
     */
    public static function isNested($pObjectKey){
        static $nested;
        if ($nested && $nested[$pObjectKey]) return $nested[$pObjectKey];
        $def = self::getDefinition($pObjectKey);
        $nested[$pObjectKey] = ($def['nested']) ? true : false;
        return $nested[$pObjectKey];
    }

    /**
     * Returns the table name behind a object.
     * Not all objects has a table. If the object is based on propel's orm, then it has one.
     *
     * @param string $pObjectKey
     * @return string
     */
    public static function getTable($pObjectKey){
        static $tableName;
        if ($tableName && $tableName[$pObjectKey]) return pfx.$tableName[$pObjectKey];
        $def = self::getDefinition($pObjectKey);
        $tableName[$pObjectKey] = $def['table'];
        return pfx.$tableName[$pObjectKey];
    }

    /**
     * Converts the primary key statement of a url to normalized structure.
     * Generates a array for the usage of Core\Object:get()
     *
     * 1,2,3 => array( array(id =>1),array(id =>2),array(id =>3) )
     * 1 => array(array(id => 1))
     * idFooBar => array( id => "idFooBar")
     * idFoo-Bar => array(array(id => idFoo, id2 => "Bar"))
     * 1-45, 2-45 => array(array(id => 1, pid = 45), array(id => 2, pid=>45))
     *
     *
     * @static
     * @param string $pObjectKey
     * @param string $pPrimaryKey
     * @return array|mixed
     */
    public static function parsePk($pObjectKey, $pPrimaryKey){

        $obj = self::getClass($pObjectKey);

        $objectIds = $obj->primaryStringToArray($pPrimaryKey);

        return $objectIds;
    }

    /**
     * Returns the object key (not id) from an object url.
     *
     * @param string $pUrl
     * @return string
     */
    public static function getObjectKey($pUrl){

        if (strpos($pUrl, '/') == 0)
            $pUrl = substr($pUrl, 9);

        $idx = strpos($pUrl, '/');
        if ($idx == -1) return $pUrl;

        return substr($pUrl, 0, $idx);
    }

    /**
     * This just cut off object://<objectName>/ and returns the primary key part as plain text.
     *
     * @param string $pUrl
     * @return string
     */
    public static function getCroppedObjectId($pUrl){

        if (strpos($pUrl, 'object://') === 0)
            $pUrl = substr($pUrl, 9);

        $idx = strpos($pUrl, '/');
        return substr($pUrl, $idx+1);
    }

    /**
     * Returns the id of an object item for the usage in urls (internal url's) - rawurlencoded.
     *
     * @param string $pObjectKey
     * @param array $pPk
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function getObjectUrlId($pObjectKey, $pPk){

        $pPk = self::normalizePk($pObjectKey, $pPk);
        $pks = self::getPrimaryList($pObjectKey);

        if (count($pks) == 0 ) throw new \InvalidArgumentException($pObjectKey.' does not have primary keys.');

        $withFieldNames = !is_numeric(key($pPk));

        if (count($pks) == 1 && is_array($pPk)){
            return rawurlencode($pPk[ $withFieldNames ? $pks[0] : 0 ]);
        } else {
            $c = 0;
            $urlId = array();
            foreach ($pks as $pk){
                $urlId[] = rawurlencode($pPk[ $withFieldNames ? $pk : $c ]);
                $c++;
            }
            return implode(',', $urlId);
        }

    }

    /**
     * Checks if a field in a object exists.
     *
     * @param string $pObjectKey
     * @param string $pField
     * @return bool
     */
    public static function checkField($pObjectKey, $pField){
        $definition = self::getDefinition($pObjectKey);
        if (!$definition['fields'][$pField])
            return false;
        return true;
    }

    /**
     * Converts given object key and the object item to the internal url.
     *
     * @static
     * @param string $pObjectKey
     * @param mixed $pPrimaryValues
     * @return string
     */
    public static function toUrl($pObjectKey, $pPrimaryValues){
        $url = 'object://'.$pObjectKey.'/';
        if (is_array($pPrimaryValues)){
            foreach ($pPrimaryValues as $key => $val){
                $url .= rawurlencode($val).',';
            }
        } else {
            return $url . rawurlencode($pPrimaryValues);
        }
        return substr($url, 0, -1);
    }

    /**
     * Returns the object for the given url. Same arguments as in krynObjects::get() but given by a string.
     *
     * Take a look at the krynObjects::parseUrl() method for more information.
     *
     * @static
     * @param $pInternalUrl
     * @return object
     */
    public static function getFromUrl($pInternalUrl){

        list($objectKey, $objectIds, $params, $asList) = self::parseUrl($pInternalUrl);

        return $asList?self::getList($objectKey, $objectIds, $params):self::get($objectKey, $objectIds, $params);
    }


    /**
     * Returns the single row of a object.
     * $pOptions is a array which can contain following options. All options are optional.
     *
     *  'fields'          Limit the columns selection. Use a array or a comma separated list (like in SQL SELECT)
     *                    If empty all columns will be selected.
     *  'offset'          Offset of the result set (in SQL OFFSET)
     *  'limit'           Limits the result set (in SQL LIMIT)
     *  'order'           The column to order. Example:
     *                    array(
     *                      array('field' => 'category', 'direction' => 'asc'),
     *                      array('field' => 'title',    'direction' => 'asc')
     *                    )
     *
     *  'foreignKeys'     Define which column should be resolved. If empty all columns will be resolved.
     *                    Use a array or a comma separated list (like in SQL SELECT)
     *
     *  'permissionCheck' Defines whether we check against the ACL or not. true or false. default false
     *
     * @static
     * @param string $pObjectKey
     * @param mixed  $pPk
     * @param array  $pOptions
     * @return array|null
     */
    public static function get($pObjectKey, $pPk, $pOptions = array()){

        $obj = self::getClass($pObjectKey);
        $primaryKey = $obj->normalizePrimaryKey($pPk);
        $pks = $obj->getPrimaryKeys();

        if (!$pOptions['fields']){
            if ($obj->definition['defaultSelection'])
                $pOptions['fields'] = $obj->definition['defaultSelection'];
            else
                $pOptions['fields'] = '*';
        }

        if ($pOptions['fields'] != '*' && $obj->definition['limitDataSets']){

            if (is_string($pOptions['fields']))
                $pOptions['fields'] = explode(',', trim(str_replace(' ', '', $pOptions['fields'])));

            $extraFields = dbExtractConditionFields($obj->definition['limitDataSets']);
            $deleteFieldValues = array();

            foreach ($extraFields as $field){
                if ($obj->definition['fields'][$field]){
                    if (array_search($field, $pOptions['fields']) === false && array_search($field, $pks) === false){
                        $pOptions['fields'][] = $field;
                        $deleteFieldValues[] = $field;
                    }
                }
            }
        }

        $item = $obj->getItem($primaryKey, $pOptions);

        if (!$item) return null;

        if ($pOptions['permissionCheck'] && $aclCondition = Permission::getListingCondition($pObjectKey)){
            if (!self::satisfy($item, $aclCondition)) return false;
        }

        if ($obj->definition['limitDataSets']){
            if (!self::satisfy($item, $obj->definition['limitDataSets'])) return false;
        }

        if ($deleteFieldValues){
            foreach ($deleteFieldValues as $field)
                unset($item[$field]);
        }

        return $item;

    }

    /**
     * Returns the list of objects.
     *
     *
     * $pOptions is a array which can contain following options. All options are optional.
     *
     *  'fields'          Limit the columns selection. Use a array or a comma separated list (like in SQL SELECT)
     *                    If empty all columns will be selected.
     *  'offset'          Offset of the result set (in SQL OFFSET)
     *  'limit'           Limits the result set (in SQL LIMIT)
     *  'order'           The column to order. Example:
     *                    array(
     *                      array('field' => 'category', 'direction' => 'asc'),
     *                      array('field' => 'title',    'direction' => 'asc')
     *                    )
     *
     *  'foreignKeys'     Defines which column should be resolved. If empty all columns will be resolved.
     *                    Use a array or a comma separated list (like in SQL SELECT). 'field1, field2, field3'
     *
     *  'permissionCheck' Defines whether we check against the ACL or not. true or false. default false
     *
     * @static
     * @param string $pObjectKey
     * @param mixed  $pCondition Condition object from the structure of dbPrimaryKeyToConditionToSql() or dbConditionToSql()
     * @param array  $pOptions
     * @see \dbConditionToSql
     * @return array|bool
     */
    public static function getList($pObjectKey, $pCondition = false, $pOptions = array()){

        $obj = self::getClass($pObjectKey);

        if (!$pOptions['fields']){
            if ($obj->definition['defaultSelection'])
                $pOptions['fields'] = $obj->definition['defaultSelection'];
            else
                $pOptions['fields'] = '*';
        }

        if ($pCondition)
            $pCondition = dbPrimaryKeyToCondition($pCondition, $pObjectKey);


        if ($obj->definition['limitDataSets'])
            $pCondition = $pCondition ? array($pCondition, 'AND', $obj->definition['limitDataSets']) : $obj->definition['limitDataSets'];

        if ($pOptions['permissionCheck'] && $aclCondition = Permission::getListingCondition($pObjectKey)){
            if ($pCondition)
                $pCondition = array($aclCondition, 'AND', $pCondition);
            else
                $pCondition = $aclCondition;
        }

        return $obj->getItems($pCondition, $pOptions);

    }

    /**
     * Returns the class object for $pObjectKey
     *
     * @param $pObjectKey
     * @return bool
     * @throws \ObjectNotFoundException
     * @throws \Exception
     */
    public static function &getClass($pObjectKey){

        $pObjectKey = str_replace('.', '\\', $pObjectKey);
        $definition = self::getDefinition($pObjectKey);

        if (!$definition) throw new \ObjectNotFoundException(tf('Object not found %s', $pObjectKey));

        if (!self::$instances[$pObjectKey]){

            if ($definition['dataModel'] != 'custom'){

                //propel
                if ($propelClass = $definition['propelClass'])
                    self::$instances[$pObjectKey] = new $propelClass($pObjectKey, $definition);
                else
                    self::$instances[$pObjectKey] = new \Core\ORM\Propel($pObjectKey, $definition);

            } else {

                //custom
                if (!class_exists($className = $definition['class']))
                    throw new \Exception(tf('Class for %s (%s) not found.', $pObjectKey. $definition['class']));

                self::$instances[$pObjectKey] = new $className($pObjectKey, $definition);

            }
        }

        return self::$instances[$pObjectKey];

    }

    /**
     * Counts the items of $pInternalUrl
     *
     * @param $pInternalUrl
     * @return array
     */
    public static function getCountFromUrl($pInternalUrl){
        list($objectKey, $objectIds, $params) = self::parseUrl($pInternalUrl);

        return self::getCount($objectKey, $params['condition']);
    }


    /**
     * Removes all items.
     *
     * @param string $pObjectKey
     */
    public static function clear($pObjectKey){
        $obj = self::getClass($pObjectKey);
        return $obj->clear();
    }

    /**
     * Counts the items of $pObjectKey filtered by $pCondition
     *
     * @param string $pObjectKey
     * @param array  $pCondition
     * @param array  $pOptions
     * @return array
     */
    public static function getCount($pObjectKey, $pCondition = null, $pOptions = null){

        $obj = self::getClass($pObjectKey);

        if ($pCondition)
            $pCondition = dbPrimaryKeyToCondition($pCondition, $pObjectKey);

        if ($obj->definition['limitDataSets'])
            $pCondition = $pCondition ? array($pCondition, 'AND', $obj->definition['limitDataSets']) : $obj->definition['limitDataSets'];

        if ($pOptions['permissionCheck'] && $aclCondition = Permission::getListingCondition($pObjectKey)){
            if ($pCondition)
                $pCondition = array($aclCondition, 'AND', $pCondition);
            else
                $pCondition = $aclCondition;
        }

        return $obj->getCount($pCondition);

    }

    /**
     * Counts the items of $pObjectKey filtered by $pCondition
     *
     * @param string $pObjectKey
     * @param mixed $pPk
     * @param array $pCondition
     * @param mixed $pScope
     * @param array $pOptions
     * @return array
     */
    public static function getBranchChildrenCount($pObjectKey, $pPk = null, $pCondition = null, $pScope = null, $pOptions = null){

        $obj = self::getClass($pObjectKey);

        if ($pPk)
            $pPk = $obj->normalizePrimaryKey($pPk);

        if ($pCondition)
            $pCondition = dbPrimaryKeyToCondition($pCondition, $pObjectKey);

        if ($obj->definition['limitDataSets'])
            $pCondition = $pCondition ? array($pCondition, 'AND', $obj->definition['limitDataSets']) : $obj->definition['limitDataSets'];

        if ($pOptions['permissionCheck'] && $aclCondition = Permission::getListingCondition($pObjectKey)){
            if ($pCondition)
                $pCondition = array($aclCondition, 'AND', $pCondition);
            else
                $pCondition = $aclCondition;
        }

        return $obj->getBranchChildrenCount($pPk, $pCondition, $pScope, $pOptions);

    }

    /**
     * Adds a item.
     *
     * @param string $pObjectKey
     * @param array  $pValues
     * @param mixed  $pPk              Full PK as array or as primary key string (url).
     * @param string $pPosition        If nested set. `first` (child), `last` (last child), `prev` (sibling), `next` (sibling)
     * @param bool   $pTargetObjectKey If this object key differs from $pObjectKey then we'll use $pPk as `scope`. Also
     *                                 it is then only possible to have position `first` and `last`.
     * @param array  $pOptions
     * @return mixed
     *
     * @throws \NoFieldWritePermission
     */
    public static function add($pObjectKey, $pValues, $pPk = null, $pPosition = 'first', $pTargetObjectKey = null,
                               $pOptions = array()){

        $pPk = self::normalizePk($pObjectKey, $pPk);

        $obj = self::getClass($pObjectKey);
    
        if ($pOptions['permissionCheck']){

            foreach ($pValues as $fieldName => $value){

                //todo, what if $pTargetObjectKey differs from $pObjectKey

                if (!Permission::checkAdd($pObjectKey, $pPk, $fieldName)){
                    throw new \NoFieldWritePermission(tf("No update permission to field '%s' in item '%s' from object '%s'", $fieldName, $pPk, $pObjectKey));
                }
            }
        }

        if ($pTargetObjectKey && $pTargetObjectKey != $pObjectKey){
            if ($pPosition == 'prev' || $pPosition == 'next'){
                throw \InvalidArgumentException('Its not possible to use `prev` or `next` to add a new entry with a different object key.');
            }

            $pPk = self::normalizePk($pTargetObjectKey, $pPk);

            //since propel's nested set behaviour only allows a single value as scope, we need to use the first pk
            $scope = current($pPk);
            return $obj->add($pValues, null, $pPosition, $scope);
        }

        return $obj->add($pValues, $pPk, $pPosition);

    }

    /**
     * Updates a item per url.
     *
     * @param string $pObjectUrl
     * @param array $pValues
     * @return bool
     */
    public static function updateFromUrl($pObjectUrl, $pValues){
        list($objectKey, $objectIds, $params) = self::parseUrl($pObjectUrl);
        return self::update($objectKey, $objectIds[0], $pValues, $params);
    }

    /**
     * Updates a item.
     * 
     * @param  string $pObjectKey
     * @param  mixed  $pPk
     * @param  array $pValues
     * @param  array $pOptions
     * @return bool
     */
    public static function update($pObjectKey, $pPk, $pValues, $pOptions = null){

        if ($pOptions['permissionCheck']){
            foreach ($pValues as $fieldName => $value){
                //if (!Permission::checkUpdate($pObjectKey, $pPk, $fieldName)){
                //    throw new \NoFieldWritePermission(tf("No update permission to field '%s' in item '%s' from object '%s'", $fieldName, $pPk, $pObjectKey));
                //}
            }
        }

        $obj = self::getClass($pObjectKey);
        $primaryKey = $obj->normalizePrimaryKey($pPk);
        return $obj->update($primaryKey, $pValues);
    }

    /**
     * Removes a object item per url.
     *
     * @param string $pObjectUrl
     * @return bool
     */
    public static function removeFromUrl($pObjectUrl){
        list($objectKey, $objectIds, $params) = self::parseUrl($pObjectUrl);
        $obj = self::getClass($objectKey);
        return $obj->remove($objectIds[0]);
    }

    /**
     * Removes a object item.
     *
     * @param string $pObjectKey
     * @param mixed $pPk
     * @return boolean
     */
    public static function remove($pObjectKey, $pPk){
        $obj = self::getClass($pObjectKey);
        $primaryKey = $obj->normalizePrimaryKey($pPk);
        return $obj->remove($primaryKey);
    }

    /*
    public static function removeUsages($pObjectUrl){

    }

    public static function removeUsage($pObjectUrl, $pUseObjectId){

    }

    public static function addUsage($pObjectUrl, $pUseObjectId){

    }
    */


    /**
     * Returns a single root item. Only for nested objects.
     *
     * @param string $pObjectKey
     * @param mixed $pScope
     * @param bool $pOptions
     * @return array
     * @throws \Exception
     */
    public static function getRoot($pObjectKey, $pScope, $pOptions = false){

        $definition = self::getDefinition($pObjectKey);

        if ($definition['nestedRootAsObject'] && $pScope === null) throw new \Exception('No `scope` defined.');

        $pOptions['fields'] = $definition['nestedRootObjectLabelField'];

        return self::get($definition['nestedRootObject'], $pScope, $pOptions);
    }

    /**
     * Returns all roots. Only for nested objects.
     *
     * @param string $pObjectKey
     * @param array $pCondition
     * @param options $pOptions
     * @return array
     * @throws \Exception
     */
    public static function getRoots($pObjectKey, $pCondition = null, $pOptions = null){

        $definition = self::getDefinition($pObjectKey);

        if (!$definition['nested']) throw new \Exception('Object is not a nested set.');

        if ($definition['nestedRootObjectLabelField'] && !$pOptions['fields'])
            $pOptions['fields'] = $definition['nestedRootObjectLabelField'];

        if ($definition['nestedRootAsObject']){

            return self::getList($definition['nestedRootObject'], null, $pOptions);
        } else {
            $obj = self::getClass($pObjectKey);

            if ($pOptions['permissionCheck'] && $aclCondition = Permission::getListingCondition($pObjectKey)){
                if ($pCondition)
                    $pCondition = array($aclCondition, 'AND', $pCondition);
                else
                    $pCondition = $aclCondition;
            }

            return $obj->getRoots($pCondition, $pOptions);

        }
    }

    /**
     * @static
     * @param $pObjectKey
     * @param null $pPk
     * @param null $pCondition
     * @param int $pDepth
     * @param bool $pScope
     * @param bool $pOptions
     * @return mixed
     * @throws \Exception
     */
    public static function getBranch($pObjectKey, $pPk = null, $pCondition = null, $pDepth = 1, $pScope = false, $pOptions = false){

        $obj = self::getClass($pObjectKey);
        $definition = self::getDefinition($pObjectKey);

        if ($pPk)
            $pPk = $obj->normalizePrimaryKey($pPk);

        if (!$definition['nestedRootAsObject'] && $pScope === false) throw new \Exception('No scope defined.');

        if (!$pOptions['fields']){
            $fields[] = $definition['nestedRootObjectLabelField'];

            if ($definition['nestedRootObjectExtraFields']){
                $extraFields = explode(',', trim(str_replace(' ', '', $definition['nestedRootObjectExtraFields'])));
                foreach ($extraFields as $field)
                    $fields[] = $field;
            }
            $pOptions['fields'] = implode(',',$fields);
        }

        if ($pCondition)
            $pCondition = dbPrimaryKeyToCondition($pCondition, $pObjectKey);

        if ($pOptions['permissionCheck'] && $aclCondition = Permission::getListingCondition($pObjectKey)){
            if ($pCondition)
                $pCondition = array($aclCondition, 'AND', $pCondition);
            else
                $pCondition = $aclCondition;
        }

        return $obj->getBranch($pPk, $pCondition, $pDepth, $pScope, $pOptions);

    }

    /**
     * Returns a hash of all primary fields.
     *
     * Returns array('<keyOne>' => <arrayDefinition>, '<keyTwo>' => <arrayDefinition>, ...)
     *
     * @static
     * @param $pObjectId
     * @return array
     */
    public static function getPrimaries($pObjectId){
        $objectDefinition = self::getDefinition($pObjectId);

        $primaryFields = array();
        foreach ($objectDefinition['fields'] as $fieldKey => $field){
            if ($field['primaryKey'])
                $primaryFields[$fieldKey] = $field;
        }

        return $primaryFields;
    }

    /**
     * Return a list of all primary keys.
     *
     * Returns array('<keyOne>', '<keyTwo>', ...);
     *
     * @static
     * @param $pObjectId
     * @return array
     */
    public static function getPrimaryList($pObjectId){
        $objectDefinition = self::getDefinition($pObjectId);

        $primaryFields = array();
        foreach ($objectDefinition['fields'] as $fieldKey => $field){
            if ($field['primaryKey'])
                $primaryFields[] = $fieldKey;
        }

        return $primaryFields;
    }

    /**
     * Returns the parent pk.
     *
     * @param string $pObjectKey
     * @param mixed $pPk
     * @return array
     */
    public static function getParentPk($pObjectKey, $pPk){
        $obj = self::getClass($pObjectKey);
        $pk = $obj->normalizePrimaryKey($pPk);
        return $obj->getParentId($pk);
    }

    /**
     * Returns the parent pk from a url.
     *
     * @param string $pObjectUrl
     * @return array
     */
    public static function getParentPkFromUrl($pObjectUrl){

        list($objectKey, $objectIds, $params) = self::parseUrl($pObjectUrl);

        return self::getParentId($objectKey, $objectIds[0]);
    }

    /**
     * Returns the parent item per url. Only if the object is nested.
     *
     * @param string $pObjectKey
     * @param mixed $pPk
     * @param null $pOptions
     * @return mixed
     */
    public static function getParent($pObjectKey, $pPk, $pOptions = null){
        $obj = self::getClass($pObjectKey);
        $pk = $obj->normalizePrimaryKey($pPk);
        return $obj->getParent($pk, $pOptions);
    }

    /**
     * Returns the parent item. Only if the object is nested.
     *
     * @param string $pObjectUrl
     * @return array
     */
    public static function getParentFromUrl($pObjectUrl){
        list($objectKey, $objectIds, $params) = self::parseUrl($pObjectUrl);
        return self::getParent($objectKey, $objectIds[0]);
    }

    /**
     * @param string $pObjectUrl
     * @param array  $pOptions
     * @return array
     */
    public static function getVersionsFromUrl($pObjectUrl, $pOptions = null){
        list($objectKey, $objectId) = Object::parseUrl($pObjectUrl);
        return self::getVersions($objectKey, $objectId[0], $pOptions);
    }

    /**
     * @param string $pObjectKey
     * @param mixed  $pPk
     * @param array  $pOptions
     * @return array
     */
    public static function getVersions($pObjectKey, $pPk, $pOptions = null){
        $obj = self::getClass($pObjectKey);
        $pk = $obj->normalizePrimaryKey($pPk);
        return $obj->getVersions($pk);

    }

    /**
     * Returns always a array with primary key and value pairs from a single pk.
     *
     * $pPk can be
     *  - 24
     *  - array(24)
     *  - array('id' => 24)
     *
     * result:
     *  array(
     *    'id' => 24
     * );
     *
     * @param string $pObjectKey
     * @param mixed $pPk
     * @return array A single primary key as array. Example: array('id' => 1).
     */
    public static function normalizePk($pObjectKey, $pPk){
        $obj = self::getClass($pObjectKey);
        return $obj->normalizePrimaryKey($pPk);
    }

    /**
     * Parses a whole (can be multiple) primary key that is a represented as string and returns the first primary key.
     *
     * Example:
     *
     *  1/2/3 => array( array(id =>1),array(id =>2),array(id =>3) )
     *  1 => array(array(id => 1))
     *  idFooBar => array( id => "idFooBar")
     *  idFoo/Bar => array(array(id => idFoo), array(id2 => "Bar"))
     *  1,45/2,45 => array(array(id => 1, pid = 45), array(id => 2, pid=>45))
     *
     * @param $pObjectKey
     * @param $pPkString
     * @return array Example array('id' => 4)
     */
    public static function normalizePkString($pObjectKey, $pPkString){

        $obj = self::getClass($pObjectKey);
        $objectIds = $obj->primaryStringToArray($pPkString);

        return $objectIds[0];
    }

    /**
     * Returns all parents, incl. the root object (if root is an object, it returns this object entry as well)
     *
     * @param string $pObjectKey
     * @param mixed  $pPk
     * @param array  $pOptions
     * @return mixed
     */
    public static function getParents($pObjectKey, $pPk, $pOptions = null){
        $obj = self::getClass($pObjectKey);
        $pk = $obj->normalizePrimaryKey($pPk);
        return $obj->getParents($pk, $pOptions);
    }

    /**
     * Returns all parents per url, incl. the root object (if root is an object, it returns this object entry as well)
     *
     * @param string $pObjectUrl
     * @return mixed
     */
    public static function getParentsFromUrl($pObjectUrl){
        list($objectKey, $objectIds, $params) = self::parseUrl($pObjectUrl);
        return self::getParents($objectKey, $objectIds[0]);
    }

    /**
     * Moves a item to a new position.
     *
     * @param string $pObjectKey
     * @param array  $pPk
     * @param array  $pTargetPk
     * @param string $pPosition `first` (child), `last` (last child), `prev` (sibling), `next` (sibling)
     * @param string $pTargetObjectKey
     * @param array $pOptions
     * @return mixed
     */
    public static function move($pObjectKey, $pPk, $pTargetPk, $pPosition = 'first', $pTargetObjectKey = null, $pOptions = null){

        $obj = self::getClass($pObjectKey);

        $pk = $obj->normalizePrimaryKey($pPk);
        $pTargetPk = self::normalizePk($pTargetObjectKey ? $pTargetObjectKey : $pObjectKey, $pTargetPk);

        //todo check access


        return $obj->move($pk, $pTargetPk, $pPosition, $pTargetObjectKey);
    }

    /**
     * Moves a item around by a url.
     *
     * @param string $pSourceObjectUrl
     * @param string $pTargetObjectUrl
     * @param string $pPosition
     * @param array  $pOptions
     * @return mixed
     */
    public static function moveFromUrl($pSourceObjectUrl, $pTargetObjectUrl, $pPosition = 'first', $pOptions = null){

        list($objectKey, $objectIds, $params) = self::parseUrl($pSourceObjectUrl);
        list($targetObjectKey, $targetObjectIds, $targetParams) = self::parseUrl($pTargetObjectUrl);

        return self::move($objectKey, $objectIds[0], $targetObjectIds[0], $targetObjectKey, $pPosition, $pOptions);
    }

    /**
     * Checks whether the conditions in $pCondition are complied with the given object url.
     *
     * @param string $pObjectUrl
     * @param array  $pCondition
     * @return bool
     */
    public static function satisfyFromUrl($pObjectUrl, $pCondition){

        $object = self::getFromUrl($pObjectUrl);
        return self::satisfy($object, $pCondition);

    }

    /**
     * Checks whether the conditions in $pCondition are complied with the given object item.
     *
     * $pCondition is a structure as of dbConditionToSql();
     *
     * @static
     * @param $pObjectItem
     * @param $pCondition
     *
     * @return bool
     */
    public static function satisfy(&$pObjectItem, $pCondition){

        $complied = null;
        $lastOperator = 'and';

        if (is_array($pCondition) && is_string($pCondition[0])){
            return self::checkRule($pObjectItem, $pCondition);
        }

        foreach ($pCondition as $condition){

            if (is_string($condition)){
                $lastOperator = strtolower($condition);
                continue;
            }

            if (is_array($condition) && is_array($condition[0])){
                //group
                $res = self::satisfy($pObjectItem, $condition);
            } else {
                $res = self::checkRule($pObjectItem, $condition);
            }

            if (is_null($complied))
                $complied = $res;
            else {
                if ($lastOperator == 'and')
                    $complied = $complied && $res;

                if ($lastOperator == 'and not')
                    $complied = $complied && !$res;

                if ($lastOperator == 'or')
                    $complied = $complied || $res;
            }

        }

        return $complied === null ? true : ($complied ? true : false);
    }

    /**
     * Checks a single condition.
     *
     * @param array $pObjectItem
     * @param array $pCondition
     * @return bool|int
     */
    public static function checkRule(&$pObjectItem, $pCondition){
        global $client;

        $field = $pCondition[0];
        $operator = $pCondition[1];
        $value = $pCondition[2];

        if (is_numeric($field)){
            $ovalue = $field;
        } else {
            $ovalue = $pObjectItem[$field];
        }

        //'<', '>', '<=', '>=', '=', 'LIKE', 'IN', 'REGEXP'
        switch(strtoupper($operator)){

            case '!=':
                return ($ovalue != $value);

            case 'LIKE':
                $value = preg_quote($value, '/');
                $value = str_replace('%', '.*', $value);
                $value = str_replace('_', '.', $value);
                return preg_match('/^'.$value.'$/', $ovalue);

            case 'REGEXP':
                return preg_match('/'.preg_quote($value, '/').'/', $ovalue);

            case 'IN':
                return strpos(','.$value.',', ','.$ovalue.',') !== false;

            case '<';
                return ($ovalue < $value);
            case '>';
                return ($ovalue > $value);
            case '<=';
            case '=<';
                return ($ovalue <= $value);
            case '>=';
            case '=>';
                return ($ovalue >= $value);

            case '= CURRENT_USER':
                return $ovalue == $client->user_id;

            case '!= CURRENT_USER':
                return $ovalue != $client->user_id;

            case '=':
            default:
                return ($ovalue == $value);
        }

    }

    /**
     * Returns the public URL.
     *
     * @param string $pObjectKey
     * @param string $pPk
     * @param array  $pPlugin
     * @return string
     */
    public static function getPublicUrl($pObjectKey, $pPk, $pPlugin = null){

        $definition = self::getDefinition($pObjectKey);

        if ($definition && $definition['publicUrlGenerator']){
            $pk = self::normalizePkString($pObjectKey, $pPk);
            return call_user_func_array($definition['publicUrlGenerator'], array($pObjectKey, $pk, $pPlugin));
        }

        return null;

    }

}
