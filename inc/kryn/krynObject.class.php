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
     * Example: getUrl('file://45') => '/myImageFolder/Picture1.png'
     *          getUrl('news://4/<contentPluginId>') => '/newspage/detail/my-news-title'
     *          getUrl('user://1/<contentPluginId>') => '/userdetail/admini-strator'
     *
     * @link http://docu.kryn.org/developer/extensions/internal-url
     *
     * Can return additionally 'http(s)://myDomain/' at the beginning if the target
     * is on a different domain.
     *
     * @static
     * @param string $pInternalUrl
     *
     * @return string|bool
     */
    public static function getUrl($pInternalUrl){

        //TODO, not done here

        $pos = strpos($pInternalUrl,'://');
        $object_id = substr($pInternalUrl, 0, $pos);
        $params = explode('/', substr($pInternalUrl, $pos+2));

        $objectDefinition = kryn::$objects[$object_id];
        if (!$objectDefinition) return false;

        if (method_exists($objectDefinition['_extension'], $objectDefinition['urlGetter'])){
            return call_user_func(array($objectDefinition['_extension'], $objectDefinition['urlGetter']), $params);

        } else return false;
    }

    /**
     * @static
     * @param $pInternalUrl
     * @return array [object_key, object_id/s, queryParams]
     */
    public static function parseUrl($pInternalUrl){

        $pInternalUrl = str_replace(' ', '', trim($pInternalUrl));

        $catch = 'object://';
        if (substr(strtolower($pInternalUrl),0,strlen($catch)) == $catch){
            $pInternalUrl = substr($pInternalUrl, strlen($catch));
        }

        $pos = strpos($pInternalUrl, '/');
        $questionPos = strpos($pInternalUrl, '?');

        if ($pos === false && $questionPos === false){
            return array(
                $pInternalUrl,
                false,
                array()
            );
        }

        if ($pos === false && $questionPos != false)
            $object_key = substr($pInternalUrl, 0, $questionPos);
        else
            $object_key = substr($pInternalUrl, 0, $pos);

        $params = array();

        if ($questionPos !== false){
            parse_str(substr($pInternalUrl, $questionPos+1), $params);

            if ($pos !== false)
                $object_id = substr($pInternalUrl, $pos+1, $questionPos-($pos+1));

        } else if ($pos !== false)
            $object_id = substr($pInternalUrl, $pos+1);

        if (strpos($object_id, ',') !== false){
            $object_id = explode(',', $object_id);
        }

        return array(
            $object_key,
            $object_id==""?false:$object_id,
            $params
        );
    }

    /**
     * Returns the object for the given url. Same arguments as in krynObject::get() but given by a string.
     *
     * The string consists of the object key, the object primary key and options URL encoded.
     *
     * Examples:
     *
     *    system_user/2 => returns the whole object system_user with the primary value of 2
     *
     *    system_user/2?fields=name => returns the object system_user with the primary value is 2 and only with the
     *                                 column "name"
     *
     *    system_user/2?fields=name,title,bar  => returns the object system_user with the primary value is 2 and
     *                                             only with the column "name", "title" and "bar"
     *
     *    system_users/?limit=30 => returns the first 30 objects of system_user
     *
     *    system_users/?limit=5&offset=3 => returns the first 5 objects of system_user starting at the third.
     *
     *    system_users/?condition=rsn&gt;3 => returns all objects of system_user where rsn is bigger than 3
     *
     * Note: If you use getFromUrl() you need to have "condition" url encoded.
     *       (So > and < doesnt work! Use &gt; etc instead)
     *
     * @static
     * @param $pInternalUrl
     * @return object
     */
    public static function getFromUrl($pInternalUrl){

        list($object_key, $object_id, $params) = self::parseUrl($pInternalUrl);

        return self::get($object_key, $object_id, $params);
    }


    /**
     * Returns the single row or a list of objects.
     *
     * The $pObjectPrimaryValues can be mixed. Following some examples:
     *
     * Returns single row:
     * "1" => returns one item with the primary=1
     * array(1) => equal as above
     * array('rsn' => 1) => equal as above (if the primary=rsn)
     * array('primary_1' => 2, 'primary_2' => 3)
     *
     * Returns multiple row (list):
     * "1,2,3" => returns three items whereas the primary from the first is 1, from tht second 2, etc
     * array(1,2) => equal as above
     * array( array('rsn'=>1), array('rsn'=>2) )
     * array( array('primary_1'=>2, 'primary_2'=>3), array('primary_1'=>3, 'primary_2'=>5) )
     *
     * If you need more complex filterin use $pOptions['condition'] which is a SQL condition (check for SQL injection!)
     *
     * Use esc() function, if you use $pOptions['condition']
     *
     * $pOptions is a array which can contain following options. All options are optional.
     *
     *  'fields'          Limit the columns selection. Use a array or a comma separated list (like in SQL SELECT)
     *                    If empty all columns will be selected.
     *  'condition'       SQL condition without WHERE or AND at the beginning
     *  'offset'          Offset of the result set (in SQL OFFSET)
     *  'limit'           Limits the result set (in SQL LIMIT)
     *  'orderBy'         The column to order
     *  'orderDirection'  The order direction
     *  'foreignKeys'     Define which column should be resolved. If empty all columns will be resolved.
     *                    Use a array or a comma separated list (like in SQL SELECT)
     *
     * @static
     * @param string $pObjectKey
     * @param mixed  $pObjectPrimaryValues
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
            (is_array($pObjectPrimaryValues) && array_key_exists(0, $pObjectPrimaryValues)) ||
            (is_string($pObjectPrimaryValues) && strpos($pObjectPrimaryValues, ',') !== false)
        ){

            return $obj->getItems($pObjectPrimaryValues, $pOptions['offset'], $pOptions['limit'], $pOptions['condition'], $pOptions['fields'],
                $pOptions['foreignKeys'], $pOptions['orderBy'], $pOptions['orderDirection'], $pRawData);

        }


        if ($pObjectPrimaryValues !== false){

            $item = $obj->getItem($pObjectPrimaryValues, $pOptions['fields'], $pOptions['foreignKeys'], $pRawData);

            return $item;

        } else {

            return $obj->getItems(null, $pOptions['offset'], $pOptions['limit'], $pOptions['condition'], $pOptions['fields'],
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


        $definition = kryn::$objects[$pObjectKey];
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
                    self::$instances[$pObjectKey] = new $className($definition, $pObjectKey);
                } else throw new Exception('Create object instance error: Class '.$className.' not found');

            } else if ($definition['table']){

                @require_once('inc/kryn/krynObject/krynObjectTable.class.php');
                self::$instances[$pObjectKey] = new krynObjectTable($definition, $pObjectKey);
            }
        }

        return self::$instances[$pObjectKey];

    }

    /**
     * Sets the object values for the given url
     *
     * @static
     * @param  $pInternalUrl
     * @param  $pObject
     * @return bool
     */
    public static function set($pInternalUrl, $pObject){

        //TODO, not done here

        list($object_key, $object_id, $params) = self::parseUrl($pInternalUrl);

        $objectDefinition = kryn::$objects[$object_key];
        if (!$objectDefinition) return false;

        if (method_exists($objectDefinition['_extension'], $objectDefinition['setter'])){
            return call_user_func(array($objectDefinition['_extension'], $objectDefinition['setter']), $params, $pObject);
        } else return false;

    }


    /**
     * Counts the items of $pInternalUrl
     *
     * @static
     * @param $pInternalUrl
     * @return array
     */
    public static function countFromUrl($pInternalUrl){
        list($object_key, $object_id, $params) = self::parseUrl($pInternalUrl);

        return self::count($object_key, $params['condition']);
    }


    /**
     *
     * Counts the items of $pObjectKey filtered by $pCondition
     *
     * @static
     * @param $pObjectKey
     * @param string $pCondition
     * @return array
     */
    public static function count($pObjectKey, $pCondition = ''){

        $obj = self::getClassObject($pObjectKey);

        if (!$obj) return array('error'=>'object_not_found');

        return $obj->getCount($pCondition);

    }

    /**
     * TBD
     *
     * @static
     * @param $pObjectId
     * @param $pObject
     */
    public static function add($pObjectId, $pObject){
        //TODO
    }

    public static function removeUsages($pObjectId){

    }

    public static function removeUsage($pObjectId, $pUseObjectId){

    }

    public static function addUsage($pObjectId, $pUseObjectId){

    }

}

?>