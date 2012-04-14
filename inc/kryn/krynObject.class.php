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


        $obj = self::getClassObject($object_key);

        $object_id = $obj->primaryStringToArray($object_id);

        return array(
            $object_key,
            (!$object_id) ? false : $object_id,
            $params
        );
    }

    /**
     * Returns the object for the given url. Same arguments as in krynObject::get() but given by a string.
     *
     * Take a look at the krynObject::parseUrl() method for more information.
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
     * The $pObjectPrimaryValues can be mixed. Additionally to the patter of krynObject::parseUrl() we have:
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
     *  'condition'       SQL condition without WHERE or AND at the beginning
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

    public static function add($pObjectKey, $pValues){
        $obj = self::getClassObject($pObjectKey);
        return $obj->addItem($pValues);

    }

    public static function update($pObjectKey, $pPrimaryValues, $pValues){
        $obj = self::getClassObject($pObjectKey);
        return $obj->updateItem($pPrimaryValues, $pValues);

    }

    public static function removeUsages($pObjectId){

    }

    public static function removeUsage($pObjectId, $pUseObjectId){

    }

    public static function addUsage($pObjectId, $pUseObjectId){

    }

    public static function getPrimaries($pObjectId){
        $objectDefinition =& kryn::$objects[$pObjectId];

        $primaryFields = array();
        foreach ($objectDefinition['fields'] as $fieldKey => $field){
            if ($field['primaryKey'])
                $primaryFields[$fieldKey] = $field;
        }

        return $primaryFields;
    }

}

?>