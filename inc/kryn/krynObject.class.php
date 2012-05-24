<?php

class krynObject {


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
     * 2. object://news/rsn=1
     *   => equal as 1.
     *
     * 3. object://news/1/2
     *   => returns a list of the objects with primary value equal 1 or 2
     *
     * 4. object://news/rsn=1/rsn=2
     *   => equal as 3.
     *
     * 5. object://object_with_multiple_primary/2,54
     *   => returns the object with the first primary field equal 2 and second priamry field equal 54
     *
     * 6. object://object_with_multiple_primary/2,54/34,55
     *   => returns a list of the objects
     *
     * 7. object://object_with_multiple_primary/rsn=2,parent_rsn=54/rsn=34,parent_rsn=55
     *   => equal as 6 if the first defined primary is 'rsn' and the second 'parent_rsn'
     *
     * 8. object://news/1?fields=title
     *   => equal as 1. but returns only the field title
     *
     * 9. object://news/1?fields=title,category_rsn
     *   => equal as 1. but returns only the field title and category_rsn
     *
     * 10. object://news?fields=title
     *   => returns all objects from type news
     *
     * 11. object://news?fields=title&limit=5
     *   => returns first 5 objects from type news
     *
     *
     * @static
     * @param $pInternalUri
     * @return array [object_key, object_id/s, queryParams]
     */
    public static function parseUri($pInternalUri){

        $pInternalUri = trim($pInternalUri);

        $catch = 'object://';
        if (substr(strtolower($pInternalUri),0,strlen($catch)) == $catch){
            $pInternalUri = substr($pInternalUri, strlen($catch));
        }

        $pos = strpos($pInternalUri, '/');
        $questionPos = strpos($pInternalUri, '?');

        if ($pos === false && $questionPos === false){
            return array(
                $pInternalUri,
                false,
                array()
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

        $obj = self::getClassObject($object_key);

        $object_id = $obj->primaryStringToArray($object_id);

        if ($params && $params['condition']){
            $params['condition'] = json_decode($params['condition'], true);
        }

        return array(
            $object_key,
            (!$object_id) ? false : $object_id,
            $params
        );
    }

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
     * Returns the object for the given url. Same arguments as in krynObject::get() but given by a string.
     *
     * Take a look at the krynObject::parseUri() method for more information.
     *
     * @static
     * @param $pInternalUri
     * @return object
     */
    public static function getFromUri($pInternalUri){

        list($object_key, $object_id, $params) = self::parseUri($pInternalUri);

        return self::get($object_key, $object_id, $params);
    }


    /**
     * Returns the single row or a list of objects.
     *
     * The $pObjectPrimaryValues can be mixed. Additionally to the patter of krynObject::parseUri() we have:
     *
     * Returns single row:
     * array(1) => returns one item with the first primary=1
     * array('rsn' => 1) => equal as above (if the primary=rsn)
     * array('primary_1' => 2, 'primary_2' => 3)
     *
     * Returns multiple row (list):
     * array(1,2) => returns three items whereas the primary for the first object is 1, for the second object 2, etc
     * array( array('rsn'=>1), array('rsn'=>2) ) equal as above
     * array( array('primary_1'=>2, 'primary_2'=>3), array('primary_1'=>3, 'primary_2'=>5) )
     *
     * If you need more complex filterin use $pOptions['condition'] which is a SQL condition (check for SQL injection!)
     *
     * !IMPORTANT!:
     * Don't forget to use esc() function, if you use $pOptions['condition'], to escape the values (because in this
     * function we do _NOT_ escape this value)
     *
     * $pOptions is a array which can contain following options. All options are optional.
     *
     *  'fields'          Limit the columns selection. Use a array or a comma separated list (like in SQL SELECT)
     *                    If empty all columns will be selected.
     *  'condition'       Condition as array. Take a look at dbConditionArrayToSql to get information about the structure.
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
     * @static
     * @param string $pObjectKey
     * @param mixed  $pObjectPrimaryValues string or array
     * @param array  $pOptions
     * @param bool   $pRawData
     * @return array|bool
     */
    public static function get($pObjectKey, $pObjectPrimaryValues = false, $pOptions = array(), $pRawData = false){

        $definition = kryn::$objects[$pObjectKey];
        if (!$definition) return false;

        $obj = self::getClassObject($pObjectKey);

        if (!$pOptions['fields'])
            $pOptions['fields'] = '*';

        if (!$pOptions['foreignKeys'])
            $pOptions['foreignKeys'] = '*';

        if (
            (is_array($pObjectPrimaryValues) && array_key_exists(0, $pObjectPrimaryValues))
        ){

            return $obj->getItems($pObjectPrimaryValues, $pOptions['offset'], $pOptions['limit'], $pOptions['fields'],
                $pOptions['foreignKeys'], $pOptions['orderBy'], $pOptions['orderDirection'], $pRawData);

        }


        if ($pObjectPrimaryValues !== false){

            $item = $obj->getItem($pObjectPrimaryValues, $pOptions['fields'], $pOptions['foreignKeys'], $pRawData);

            return $item;

        } else {

            return $obj->getItems(null, $pOptions['offset'], $pOptions['limit'], $pOptions['fields'],
                                  $pOptions['foreignKeys'], $pOptions['orderBy'], $pOptions['orderDirection'], $pRawData);
        }
    }

    /**
     * Returns the class object for $pObjectKey
     *
     * @static
     * @param $pObjectKey
     * @return bool
     * @throws Exception
     */
    public static function getClassObject($pObjectKey){


        $definition =& kryn::$objects[$pObjectKey];
        if (!$definition) return false;

        if (!self::$instances[$pObjectKey]){
            if ($definition['class']){

                $path = (substr($definition['class'], 0, 5) == 'kryn/'?'inc/':'inc/module/'.$definition['_extension'].'/').$definition['class'].'.class.php';

                if (!file_exists($path))
                    throw new Exception('Create object instance error: Class file for '.$pObjectKey.' ('.$definition['class'].', '.$path.') not found');

                require_once($path);

                $p = explode('/', $definition['class']);
                $className = $p[count($p)-1];

                if ($className && class_exists($className)){
                    self::$instances[$pObjectKey] = new $className($pObjectKey, $definition);
                } else throw new Exception('Create object instance error: Class '.$className.' not found');

            } else if ($definition['table']){

                require_once('inc/kryn/krynObject/krynObjectTable.class.php');
                self::$instances[$pObjectKey] = new krynObjectTable($pObjectKey, $definition);
            } else {
                klog('krynObject', 'No class or table defined for object '.$pObjectKey);
                throw new Exception('No class or table defined for object '.$pObjectKey);
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
    public static function countFromUri($pInternalUri){
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

        $obj = self::getClassObject($pObjectUri);

        if (!$obj) return array('error'=>'object_not_found');

        return $obj->getCount($pAdditionalCondition);

    }

    public static function add($pObjectUri, $pValues){
        list($object_key, $object_id, $params) = self::parseUri($pObjectUri);
        $obj = self::getClassObject($object_key);
        return $obj->add($pValues);

    }

    public static function update($pObjectUri, $pValues){
        list($object_key, $object_id, $params) = self::parseUri($pObjectUri);
        $obj = self::getClassObject($object_key);
        return $obj->update($object_id, $pValues);

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
    public static function getTree($pParentObjectUri, $pDepth = 1, $pExtraFields = ''){


        list($object_key, $object_id, $params) = self::parseUri($pParentObjectUri);
        $obj = self::getClassObject($object_key);

        return $obj->getTree($object_id[0], $pDepth, $params['rootId'], $pExtraFields);

    }

    public static function getTreeRoot($pParentObjectUri, $pRootId){

        list($object_key, $object_id, $params) = self::parseUri($pParentObjectUri);

        $definition = kryn::$objects[$object_key];

        if (!$definition['chooserBrowserTreeRootAsObject']) return false;
        if (!$definition['chooserBrowserTreeRootObject']) return false;

        $obj = self::getClassObject($definition['chooserBrowserTreeRootObject']);

        $fields = $obj->primaryKeys;
        $fields[] = $definition['chooserBrowserTreeRootObjectLabel'];

        if ($definition['chooserBrowserTreeRootObjectExtraFields']){
            $extraFields = explode(',', trim(str_replace(' ', '', $definition['chooserBrowserTreeRootObjectExtraFields'])));
            foreach ($extraFields as $field)
                $fields[] = $field;
        }


        list($rootKey, $rootId, $params) = self::parseUri($definition['chooserBrowserTreeRootObject'].'/'.$pRootId);

        return $obj->getItem($rootId[0], $fields);

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

    public static function getParentId($pObjectUri){

        list($object_key, $object_id, $params) = self::parseUri($pObjectUri);

        $obj = self::getClassObject($object_key);

        return $obj->getParentId($object_id[0]);
    }

    public static function getParent($pObjectUri){
        list($object_key, $object_id, $params) = self::parseUri($pObjectUri);

        $obj = self::getClassObject($object_key);

        return $obj->getParent($object_id[0]);
    }

    public static function getParents($pObjectUri){
        list($object_key, $object_id, $params) = self::parseUri($pObjectUri);

        $obj = self::getClassObject($object_key);

        return $obj->getParents($object_id[0]);
    }

    public static function move($pSourceObjectUri, $pTargetObjectUri, $pMode){

        list($object_key, $object_id, $params) = self::parseUri($pSourceObjectUri);
        $target = self::parseUri($pTargetObjectUri);

        $obj = self::getClassObject($object_key);

        $targetId = $target[1][0];
        $pTargetObjectKey = false;

        if ($target[0] != $object_key){
            $pMode = 'into';
            $pTargetObjectKey = $target[0];
        }

        return $obj->move($object_id[0], $targetId, $pMode, $pTargetObjectKey);
    }

}

?>