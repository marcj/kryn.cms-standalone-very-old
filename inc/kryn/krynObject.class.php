<?php

class krynObject {

    /**
     * Translates the internal url to the real path.
     *
     * Example: getUrl('file://45') => '/myImageFolder/Picture1.png'
     *          getUrl('publication_news://4/<contentPluginId>') => '/newspage/detail/my-news-title'
     *          getUrl('users://1/<contentPluginId>') => '/userdetail/admini-strator'
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
     * Returns the object for the given url
     *
     * @static
     * @param $pInternalUrl
     * @return object
     */
    public static function get($pInternalUrl){

        //TODO, not done here

        $pos = strpos($pInternalUrl,'://');
        $object_id = substr($pInternalUrl, 0, $pos);
        $params = explode('/', substr($pInternalUrl, $pos+2));

        $objectDefinition = kryn::$objects[$object_id];
        if (!$objectDefinition) return false;

        if (method_exists($objectDefinition['_extension'], $objectDefinition['getter'])){
            return call_user_func(array($objectDefinition['_extension'], $objectDefinition['getter']), $params);
        } else return false;
    }

    /**
     * Sets the object for the given url
     *
     * @static
     * @param  $pInternalUrl
     * @param  $pObject
     * @return bool
     */
    public static function set($pInternalUrl, $pObject){

        //TODO, not done here

        $pos = strpos($pInternalUrl,'://');
        $object_id = substr($pInternalUrl, 0, $pos);
        $params = explode('/', substr($pInternalUrl, $pos+2));

        $objectDefinition = kryn::$objects[$object_id];
        if (!$objectDefinition) return false;

        if (method_exists($objectDefinition['_extension'], $objectDefinition['setter'])){
            return call_user_func(array($objectDefinition['_extension'], $objectDefinition['setter']), $params, $pObject);
        } else return false;
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

}

?>