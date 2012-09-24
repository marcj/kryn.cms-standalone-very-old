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
     * @var array
     */
    public static $cache = array();


    /**
     * Translates the internal url to the real path.
     *
     * Example: getUri('file://45') => '/myImageFolder/Picture1.png'
     *          getUri('news://4') => '/newspage/detail/my-news-title'
     *          getUri('user://1') => '/userdetail/admini-strator'
     *
     * @link http://docu.kryn.org/developer/extensions/internal-url
     *
     * Can return additionally 'http(s)://myDomain/' at the beginning if the target
     * is on a different domain.
     *
     * @static
     * @param string $pInternalUri
     * @param int    $pPluginContentElementId
     *
     * @return string|bool
     */
    public static function getUri($pInternalUri, $pPluginContentElementId){

        //TODO, not done here

        $pos = strpos($pInternalUri,'://');
        $object_id = substr($pInternalUri, 0, $pos);
        $params = explode('/', substr($pInternalUri, $pos+2));

        $objectDefinition = kryn::$objects[$object_id];
        if (!$objectDefinition) return false;

        if (method_exists($objectDefinition['_extension'], $objectDefinition['urlGetter'])){
            return call_user_func(array($objectDefinition['_extension'], $objectDefinition['urlGetter']), $params);

        } else return false;
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
     * @param string $pInternalUri
     * @return array [object_key, object_id/s, queryParams]
     */
    public static function parseUri($pInternalUri){

        $pInternalUri = trim($pInternalUri);

        $list = false;

        $catch = 'object://';
        if (substr(strtolower($pInternalUri),0,strlen($catch)) == $catch){
            $pInternalUri = substr($pInternalUri, strlen($catch));
        }

        $catch = 'objects://';
        if (substr(strtolower($pInternalUri),0,strlen($catch)) == $catch){
            $list = true;
            $pInternalUri = substr($pInternalUri, strlen($catch));
        }

        $pos = strpos($pInternalUri, '/');
        $questionPos = strpos($pInternalUri, '?');

        if ($pos === false && $questionPos === false){
            return array(
                $pInternalUri,
                false,
                array(),
                $list
            );
        }
        if ($pos === false && $questionPos != false)
            $object_key = substr($pInternalUri, 0, $questionPos);
        else
            $object_key = substr($pInternalUri, 0, $pos);

        $params = array();

        if ($questionPos !== false){
            parse_str(substr($pInternalUri, $questionPos+1), $params);

            if ($pos !== false)
                $object_id = substr($pInternalUri, $pos+1, $questionPos-($pos+1));

        } else if ($pos !== false)
            $object_id = substr($pInternalUri, $pos+1);

        $object_id = self::parsePk($object_key, $object_id);

        if ($params && $params['condition']){
            $params['condition'] = json_decode($params['condition'], true);
        }

        return array(
            $object_key,
            (!$object_id) ? false : $object_id,
            $params,
            $list
        );
    }

    /**
     * Converts the primary key statement of a uri to better structure.
     * Generates a array for the usage of Core\Object:get()
     *
     * 1/2/3 => array(1,2,3)
     * 1 => 1
     * idFooBar => idFooBar
     * idFoo/Bar => array(idFoo, Bar)
     *
     *
     * @static
     * @param string $pObjectKey
     * @param string $pPrimaryKey
     * @return array|mixed
     */
    public static function parsePk($pObjectKey, $pPrimaryKey){

        $obj = self::getClass($pObjectKey);

        $object_id = $obj->primaryStringToArray($pPrimaryKey);

        return $object_id;
    }


    public static function checkField($pObjectKey, $pField){
        if (!Kryn::$objects[$pObjectKey]['fields'][$pField])
            throw new \FieldNotFoundException(tf('Field %s in object %s not found.', $pField, $pObjectKey));
        return true;
    }

    /**
     * Converts given params to the internal uri.
     *
     * @static
     * @param string $pObjectKey
     * @param mixed $pPrimaryValues
     * @return string
     */
    public static function toUri($pObjectKey, $pPrimaryValues){
        $url = 'object://'.$pObjectKey.'/';
        if (is_array($pPrimaryValues)){
            foreach ($pPrimaryValues as $key => $val){
                $url .= $key.'='.rawurlencode($val).',';
            }
        } else {
            return $url . rawurlencode($pPrimaryValues);
        }
        return substr($url, 0, -1);
    }

    /**
     * Returns the object for the given url. Same arguments as in krynObjects::get() but given by a string.
     *
     * Take a look at the krynObjects::parseUri() method for more information.
     *
     * @static
     * @param $pInternalUri
     * @return object
     */
    public static function getFromUri($pInternalUri){

        list($object_key, $object_id, $params, $asList) = self::parseUri($pInternalUri);

        return $asList?self::getList($object_key, $object_id, $params):self::get($object_key, $object_id, $params);
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
     * @param mixed  $pPrimaryKey
     * @param array  $pOptions
     * @return array|bool
     */
    public static function get($pObjectKey, $pPrimaryKey, $pOptions = array()){

        $obj = self::getClass($pObjectKey);

        $primaryKey = $obj->normalizePrimaryKey($pPrimaryKey);

        if (!$pOptions['fields'])
            $pOptions['fields'] = '*';

        if (!$pOptions['foreignKeys'])
            $pOptions['foreignKeys'] = '*';

        $item = $obj->getItem($primaryKey, $pOptions);

        if ($pOptions['permissionCheck'] && $aclCondition = \Core\Acl::getListingCondition($pObjectKey)){
            if (!self::satisfy($item, $aclCondition)) return false;
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

        if (!$pOptions['fields']) $pOptions['fields'] = '*';

        if (!$pOptions['foreignKeys'])
            $pOptions['foreignKeys'] = '*';

        if ($pCondition !== false && $pCondition !== null && !is_array($pCondition)){
            $pCondition = array($pCondition);
        }

        if ($pOptions['permissionCheck'] && $aclCondition = \Core\Acl::getListingCondition($pObjectKey)){
            if ($pCondition)
                $pCondition = array($aclCondition, 'AND', $pCondition);
            else
                $pCondition = $aclCondition;
        }
 
        return $obj->getItems($pCondition, $pOptions);

        if ($pCondition['noCache']) return $obj->getItems($pCondition, $pOptions);

        $cacheId = 'ObjectGetList_'.md5($pObjectKey.'-'.json_encode($pCondition).json_encode($pOptions));
        if ($items = Kryn::getCache($cacheId)){
            return $items;
        }
        
        
        $items = $obj->getItems($pCondition, $pOptions);
        Kryn::setCache($cacheId, $items);
        return $items;
        

    }

    /**
     * Returns the class object for $pObjectKey
     *
     * @static
     * @param $pObjectKey
     * @return bool
     * @throws Exception
     */
    public static function &getClass($pObjectKey){

        $definition =& \Core\Kryn::$objects[$pObjectKey];
        if (!$definition) throw new \ObjectNotFoundException(tf('Can not get class for object %s', $pObjectKey));

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
     * Counts the items of $pInternalUri
     *
     * @static
     * @param $pInternalUri
     * @return array
     */
    public static function getCountFromUri($pInternalUri){
        list($object_key, $object_id, $params) = self::parseUri($pInternalUri);

        return self::getCount($object_key, $params['condition']);
    }


    /**
     *
     * Counts the items of $pObjectKey filtered by $pCondition
     *
     * @static
     * @param $pObjectUri
     * @param string $pAdditionalCondition
     * @return array
     */
    public static function getCount($pObjectUri, $pAdditionalCondition = ''){

        $obj = self::getClass($pObjectUri);

        if (!$obj) return array('error'=>'object_not_found');

        return $obj->getCount($pAdditionalCondition);

    }

    public static function test(){

    }

    public static function add($pObjectKey, $pValues, $pBranchPk = false, $pPosition = 'into', $pScopeId = false,
                               $pOptions = array()){

        $obj = self::getClass($pObjectKey);
    
        if ($pOptions['permissionCheck']){
            foreach ($pValues as $fieldName => $value){
                if (!Acl::checkAdd($pObjectKey, $pBranchPk, $fieldName)){
                    throw new \NoFieldWritePermission(tf("No update permission to field '%s' in item '%s' from object '%s'", $fieldName, $pBranchPk, $pObjectKey));
                }
            }
        }

        return $obj->add($pValues, $pBranchPk, $pPosition, $pScopeId);

    }


    public static function updateFromUri($pObjectUri, $pValues){
        list($object_key, $object_id, $params) = self::parseUri($pObjectUri);
        return self::update($object_key, $object_id[0], $pValues, $params);
    }

    /**
     * Updates an item.
     * 
     * @param  string $pObjectKey
     * @param  array $pPk
     * @param  array $pValues
     * @param  array $pOptions
     * @return boolean
     */
    public static function update($pObjectKey, $pPk, $pValues, $pOptions){

        if ($pOptions['permissionCheck']){
            foreach ($pValues as $fieldName => $value){
                if (!Acl::checkUpdate($pObjectKey, $pPk, $fieldName)){
                    throw new \NoFieldWritePermission(tf("No update permission to field '%s' in item '%s' from object '%s'", $fieldName, $pPk, $pObjectKey));
                }
            }
        }

        $obj = self::getClass($pObjectKey);
        return $obj->update($pPk, $pValues);
    }

    public static function removeFromUri($pObjectUri){
    }

    public static function remove($pObjectUri){
    }

    public static function removeUsages($pObjectUri){

    }

    public static function removeUsage($pObjectUri, $pUseObjectId){

    }

    public static function addUsage($pObjectUri, $pUseObjectId){

    }

    /**
     *
     *
     * @param $pParentObjectUri
     * @param int $pDepth  0 returns only the root. 1 returns with one level of children, 2 with two levels etc
     * @param string|array $pExtraFields
     * @return array|bool
     */
    public static function getTreeFromUri($pParentObjectUri, $pDepth = 1, $pExtraFields = ''){

        list($object_key, $object_id, $params) = self::parseUri($pParentObjectUri);
        $obj = self::getClass($object_key);

        return $obj->getTree($object_id[0], false, $pDepth, $params['scopeId'], $pExtraFields);

    }


    public static function getTreeRoot($pObjectKey, $pScope, $pOptions = false){

        $definition = kryn::$objects[$pObjectKey];
        if (!$definition['nestedRootAsObject'] && $pScope === null) throw new \Exception('No scope defined.');

        $pOptions['fields'] = $definition['nestedRootObjectLabel'];

        return self::get($definition['nestedRootObject'], $pScope, $pOptions);
    }

    /**
     * @static
     * @param $pObjectKey
     * @param null $pParentPrimaryKey
     * @param null $pCondition
     * @param int $pDepth
     * @param bool $pScope
     * @param bool $pOptions
     * @return mixed
     * @throws \Exception
     */
    public static function getTree($pObjectKey, $pParentPrimaryKey = null, $pCondition = null, $pDepth = 0, $pScope = false, $pOptions = false){

        $obj = self::getClass($pObjectKey);
        $definition = kryn::$objects[$pObjectKey];

        if ($pParentPrimaryKey)
            $parentPrimaryKey = $obj->normalizePrimaryKey($pParentPrimaryKey);

        if (!$definition['nestedRootAsObject'] && $pScope === false) throw new \Exception('No scope defined.');

        if (!$pOptions['fields']){
            $fields[] = $definition['nestedRootObjectLabel'];

            if ($definition['nestedRootObjectExtraFields']){
                $extraFields = explode(',', trim(str_replace(' ', '', $definition['nestedRootObjectExtraFields'])));
                foreach ($extraFields as $field)
                    $fields[] = $field;
            }
            $pOptions['fields'] = implode(',',$fields);
        }

        if ($pCondition)
            $pCondition = dbPrimaryKeyToCondition($pCondition, $pObjectKey);


        if ($pOptions['permissionCheck'] && $aclCondition = \Core\Acl::getListingCondition($pObjectKey)){
            if ($pCondition)
                $pCondition = array($aclCondition, 'AND', $pCondition);
            else
                $pCondition = $aclCondition;
        }

        return $obj->getTree($parentPrimaryKey, $pCondition, $pDepth, $pScope, $pOptions);

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
        $objectDefinition =& kryn::$objects[$pObjectId];

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
        $objectDefinition =& kryn::$objects[$pObjectId];

        $primaryFields = array();
        foreach ($objectDefinition['fields'] as $fieldKey => $field){
            if ($field['primaryKey'])
                $primaryFields[] = $fieldKey;
        }

        return $primaryFields;
    }




    public static function getParentId($pObjectKey, $pObjectId){

        $obj = self::getClass($pObjectKey);

        return $obj->getParentId($pObjectId);
    }

    public static function getParentIdFromUri($pObjectUri){

        list($object_key, $object_id, $params) = self::parseUri($pObjectUri);

        return self::getParentId($object_key, $object_id[0]);
    }


    public static function getParent($pObjectKey, $pObjectId){
        $obj = self::getClass($pObjectKey);
        return $obj->getParent($pObjectId);
    }

    public static function getParentFromUri($pObjectUri){
        list($object_key, $object_id, $params) = self::parseUri($pObjectUri);
        return self::getParent($object_key, $object_id[0]);
    }



    public static function getParents($pObjectKey, $pObjectId){
        $obj = self::getClass($pObjectKey);
        return $obj->getParents($pObjectId);
    }

    public static function getParentsFromUri($pObjectUri){
        list($object_key, $object_id, $params) = self::parseUri($pObjectUri);
        return self::getParents($object_key, $object_id[0]);
    }

    public static function move($pSourceObjectUri, $pTargetObjectUri, $pMode){

        list($object_key, $object_id, $params) = self::parseUri($pSourceObjectUri);
        $target = self::parseUri($pTargetObjectUri);

        $obj = self::getClass($object_key);

        $targetId = $target[1][0];
        $pTargetObjectKey = false;

        if ($target[0] != $object_key){
            $pMode = 'into';
            $pTargetObjectKey = $target[0];
        }

        return $obj->move($object_id[0], $targetId, $pMode, $pTargetObjectKey);
    }


    public static function satisfyFromUri($pObjectUri, $pCondition){

        $object = krynObjects::getFromUri($pObjectUri);
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
            else
                $complied = $lastOperator == 'and' ? $complied && $res : $complied || $res;

        }

        return $complied;
    }

    public static function checkRule(&$pObjectItem, $pCondition){
        global $client;

        $field = $pCondition[0];
        $operator = $pCondition[1];
        $value = $pCondition[2];

        $ovalue = $pObjectItem[$field];

        //'<', '>', '<=', '>=', '=', 'LIKE', 'IN', 'REGEXP'
        switch(strtoupper($operator)){

            case '!=':
                return ($ovalue != $value);

            case 'LIKE':
                $value = preg_quote($value, '/');
                $value = str_replace('%', '.*', $value);
                $value = str_replace('_', '.', $value);
                return preg_match('/'.$value.'/', $ovalue);

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

}