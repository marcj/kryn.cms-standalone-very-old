<?php

/**
 * RestServer - A REST server class for RESTful APIs.
 */

class RestServer {

    /**
     * Current routes.
     *
     * structure:
     *  array(
     *    '<uri>' => array('<methodName>', array(<requiredParams>), array(<optionalParams>));
     *  )
     *
     * <uri> with no starting or trailing slash!
     *
     * array(
     *   'book/(.*)/(.*)' => array('book')
     *   //calls book($method, $1, $2)
     *   
     *   'house/(.*)' => array('book', array('sort'))
     *   //calls book($method, $1, getArgv('sort'))
     *   
     *   'label/flatten' => array('getLabel', array('uri'))
     *   //Calls getLabel($method, getArgv('uri'))
     *
     *
     *   'get:foo/bar' => array('getLabel', array('uri'), array('optionalSort'))
     *   //Calls getLabel(getArgv('uri'), getArgv('optionalSort'))
     *   
     *   'post:foo/bar' => array('saveLabel', array('uri'))
     *   //Calls saveLabel(getArgv('uri'), getArgv('optionalSort'))
     * )
     *
     * @var array
     */
    private $routes = array();


    /**
     * Blacklisted http get arguments.
     *
     * @var array
     */
    private $blacklistedGetParameters = array('method', 'suppress_status_code');


    /**
     * Current URL that triggers the controller.
     *
     * @var string
     */
    private $triggerUrl = '';


    /**
     * Contains the controller object.
     *
     * @var string
     */
    private $controller = '';


    /**
     * List of sub controllers.
     *
     * @var array
     */
    private $controllers = array();


    /**
     * Parent controller.
     *
     * @var RestServer
     */
    private $parentController;


    /**
     * The client
     *
     * @var RestServerClient
     */
    private $client;


    /**
     * From the rewrite rule: RewriteRule ^(.+)$ index.php?__url=$1&%{query_string}
     * @var string
     */
    private $rewrittenRuleKey = '__url';


    /**
     * List of excluded methods.
     *
     * @var array|string array('methodOne', 'methodTwo') or * for all methods
     */
    private $collectRoutesExclude = array('__construct');


    /**
     * List of possible methods.
     * @var array
     */
    public $methods = array('get', 'post', 'put', 'delete');


    /**
     * Constructor
     *
     * @param string        $pTriggerUrl
     * @param string|object $pControllerClass
     * @param string        $pRewrittenRuleKey From the rewrite rule: RewriteRule ^(.+)$ index.php?__url=$1&%{query_string}
     * @param RestServer $pParentController
     */
    public function __construct($pTriggerUrl, $pControllerClass = null, $pRewrittenRuleKey = '__url',
                                $pParentController = null){

        $this->normalizeUrl($pTriggerUrl);
        $this->setRewrittenRuleKey($pRewrittenRuleKey);

        if ($pParentController){
            $this->parentController = $pParentController;
            $this->setClient($pParentController->getClient());
        } else {
            $this->setClient(new RestServerClient($this));
        }

        $this->setClass($pControllerClass);
        $this->setTriggerUrl($pTriggerUrl);
    }


    /**
     * Factory.
     *
     * @param string $pTriggerUrl
     * @param string $pControllerClass
     * @param string $pRewrittenRuleKey From the rewrite rule: RewriteRule ^(.+)$ index.php?__url=$1&%{query_string}
     *
     * @return RestServer
     */
    public static function create($pTriggerUrl, $pControllerClass = '', $pRewrittenRuleKey = '__url'){
        $clazz = get_called_class();
        return new $clazz($pTriggerUrl, $pControllerClass, $pRewrittenRuleKey);
    }


    /**
     * Returns the rewritten rule key.
     *
     * @return string
     */
    public function getRewrittenRuleKey(){
        return $this->rewrittenRuleKey;
    }


    /**
     * Sets the rewritten rule key.
     * @param string $pRewrittenRuleKey
     *
     * @return RestServer
     */
    public function setRewrittenRuleKey($pRewrittenRuleKey){
        $this->rewrittenRuleKey = $pRewrittenRuleKey;
        return $this;
    }


    /**
     * Alias for getParent()
     *
     * @return RestServer
     */
    public function done(){
        return $this->getParent();
    }


    /**
     * Returns the parent controller
     *
     * @return RestServer
     */
    public function getParent(){
        return $this->parentController;
    }


    /**
     * Set the URL that triggers the controller.
     *
     * @param $pTriggerUrl
     * @return RestServer
     */
    public function setTriggerUrl($pTriggerUrl){
        $this->triggerUrl = $pTriggerUrl;
        return $this;
    }


    /**
     * Gets the current trigger url.
     *
     * @return string
     */
    public function getTriggerUrl(){
        return $this->triggerUrl;
    }


    /**
     * Sets the client.
     *
     * @param RestServerClient $pClient
     * @return RestServer $this
     */
    public function setClient($pClient){
        $this->client = $pClient;
        $this->client->setupFormats();

        return $this;
    }


    /**
     * Get the current client.
     *
     * @return RestServerClient
     */
    public function getClient(){
        return $this->client?$this->client:$this;
    }


    /**
     * Throws the given arguments/error codes as exception,
     * if no real client has been set.
     *
     * @param $pCode
     * @param $pMessage
     * @throws \Exception
     *
     */
    public function sendResponse($pCode, $pMessage){
        throw new Exception($pCode.': '.print_r($pMessage, true));
    }


    /**
     * Sends a 'Bad Request' response to the client.
     *
     * @param $pCode
     * @param $pMessage
     * @throws \Exception
     */
    public function sendBadRequest($pCode, $pMessage){
        if (is_object($pMessage) && $pMessage->xdebug_message) $pMessage = $pMessage->xdebug_message;
        $msg = array('error' => $pCode, 'message' => $pMessage);
        if (!$this->getClient()) throw new \Exception('client_not_found_in_RestServerController');
        $this->getClient()->sendResponse('400', $msg);
    }


    /**
     * Sends a 'Internal Server Error' response to the client.
     * @param $pCode
     * @param $pMessage
     * @throws \Exception
     */
    public function sendError($pCode, $pMessage){
        if (is_object($pMessage) && $pMessage->xdebug_message) $pMessage = $pMessage->xdebug_message;
        $msg = array('error' => $pCode, 'message' => $pMessage);
        if (!$this->getClient()) throw new \Exception('client_not_found_in_RestServerController');
        $this->getClient()->sendResponse('500', $msg);
    }


    /**
     * Adds a new route.
     *
     * $pUri can be prefixed with the http methods to limit it:
     *
     *  'get:<uri>', 'post:<uri>', 'put:<uri>', 'delete:<uri>'
     *
     * Examples:
     *
     *   addRoute('get:object', 'getObject');       //calls ->getObject($method);
     *   addRoute('post:object', 'addObject');      //calls ->addObject($method);
     *   addRoute('put:object', 'setObject');       //calls ->setObject($method);
     *   addRoute('delete:object', 'removeObject'); //calls ->removeObject($method);
     *
     *   addRoute('object', 'object', array('id'))  //calls ->object($method, $_GET['id']);
     *   addRoute('object/([0-9]*)', 'object')      //calls ->object($method, $1);
     *
     *   addRoute('get:dogs', 'getDogs', null, array('limit', 'offset')
     *                                              //calls ->getDogs($method, $_GET['limit'], $_GET['limit']);
     *
     *   addRoute('get:dog', 'getDog', array('id')  //calls ->getDogs($method, $_GET['id']);
     *
     * @param string $pUri
     * @param string $pMethod
     * @param array  $pArguments Required arguments. Throws an exception if one of these is missing.
     * @param array  $pOptionalArguments
     * @return RestServer
     */
    public function addRoute($pUri, $pMethod, $pArguments = array(), $pOptionalArguments = array()){
        $this->routes[$pUri] = array($pMethod, $pArguments, $pOptionalArguments);
        return $this;
    }


    /**
     * Same as addRoute, but limits to GET.
     *
     * @param string $pUri
     * @param string $pMethod
     * @param array  $pArguments Required arguments. Throws an exception if one of these is missing.
     * @param array  $pOptionalArguments
     * @return RestServer
     */
    public function addGetRoute($pUri, $pMethod, $pArguments = array(), $pOptionalArguments = array()){
        $this->routes['get:'.$pUri] = array($pMethod, $pArguments, $pOptionalArguments);
        return $this;
    }


    /**
     * Same as addRoute, but limits to POST.
     *
     * @param string $pUri
     * @param string $pMethod
     * @param array  $pArguments Required arguments. Throws an exception if one of these is missing.
     * @param array  $pOptionalArguments
     * @return RestServer
     */
    public function addPostRoute($pUri, $pMethod, $pArguments = array(), $pOptionalArguments = array()){
        $this->routes['post:'.$pUri] = array($pMethod, $pArguments, $pOptionalArguments);
        return $this;
    }


    /**
     * Same as addRoute, but limits to PUT.
     *
     * @param string $pUri
     * @param string $pMethod
     * @param array  $pArguments Required arguments. Throws an exception if one of these is missing.
     * @param array  $pOptionalArguments
     * @return RestServer
     */
    public function addPutRoute($pUri, $pMethod, $pArguments = array(), $pOptionalArguments = array()){
        $this->routes['put:'.$pUri] = array($pMethod, $pArguments, $pOptionalArguments);
        return $this;
    }


    /**
     * Same as addRoute, but limits to DELETE.
     *
     * @param string $pUri
     * @param string $pMethod
     * @param array  $pArguments Required arguments. Throws an exception if one of these is missing.
     * @param array  $pOptionalArguments
     * @return RestServer
     */
    public function addDeleteRoute($pUri, $pMethod, $pArguments = array(), $pOptionalArguments = array()){
        $this->routes['delete:'.$pUri] = array($pMethod, $pArguments, $pOptionalArguments);
        return $this;
    }


    /**
     * Removes a route.
     *
     * @param string $pUri
     * @return RestServer
     */
    public function removeRoute($pUri){
        unset($this->routes[$pUri]);
        return $this;
    }


    /**
     * Sets the controller class.
     *
     * @param string|object $pClass
     */
    public function setClass($pClass){
        if (is_string($pClass)){
            $this->createControllerClass($pClass);
        } else if(is_object($pClass)){
            $this->controller = $pClass;
        } else {
            $this->controller = $this;
        }
    }


    /**
     * Setup the controller class.
     *
     * @param string $pClassName
     * @throws Exception
     */
    public function createControllerClass($pClassName){
        if ($pClassName != ''){
            try {
                $this->controller = new $pClassName();
                if (get_parent_class($this->controller) == 'RestServerController'){
                    $this->controller->setClient($this->getClient());
                }
            } catch (Exception $e) {
                throw new Exception('Error during initialisation of '.$pClassName.': '.$e);
            }
        } else {
            $this->controller = $this;
        }
    }

    /**
     * Constructor
     *
     * @param string $pTriggerUrl
     * @param string $pControllerClass
     * @param string $pRewrittenRuleKey From the rewrite rule: RewriteRule ^(.+)$ index.php?__url=$1&%{query_string}
     *
     * @return RestServer new created RestServer
     */
    public function addSubController($pTriggerUrl, $pControllerClass = '', $pRewrittenRuleKey = '__url'){

        $this->normalizeUrl($pTriggerUrl);

        $controller = new RestServer($this->triggerUrl.'/'.$pTriggerUrl, $pControllerClass,
            $pRewrittenRuleKey?$pRewrittenRuleKey:$this->rewrittenRuleKey, $this);

        $this->controllers[] = $controller;

        return $controller;
    }

    /**
     * Normalize $pUrl
     *
     * @param $pUrl Ref
     */
    public function normalizeUrl(&$pUrl){
        if (substr($pUrl, -1) == '/') $pUrl = substr($pUrl, 0, -1);
        if (substr($pUrl, 0, 1) == '/') $pUrl = substr($pUrl, 1);
    }


    /**
     * Sends data to the client with 200 http code.
     *
     * @param $pData
     */
    public function send($pData){
        $this->getClient()->sendResponse(200, array('data' => $pData));
    }

    /**
     * Setup automatic routes.
     *
     * @return RestServer
     */
    public function collectRoutes(){

        if ($this->collectRoutesExclude == '*') return $this;

        $methods = get_class_methods($this->controller);
        foreach ($methods as $method){
            if (in_array($method, $this->collectRoutesExclude)) continue;

            $uri = strtolower(preg_replace('/([a-z])([A-Z])/', '$1/$2', $method));
            $r = new ReflectionMethod($this->controller, $method);
            if ($r->isPrivate()) continue;

            $params = $r->getParameters();
            $optionalArguments = array();
            $arguments = array();
            foreach ($params as $param){
                $name = lcfirst(substr($param->getName(), 1));
                if ($param->isOptional())
                    $optionalArguments[] = $name;
                else
                    $arguments[] = $name;
            }
            $this->routes[$uri] = array(
                $method,
                count($arguments)==0?null:$arguments.
                    count($optionalArguments)==0?null:$optionalArguments
            );
        }

        return $this;
    }


    /**
     * Fire the magic!
     *
     * Searches the method and sends the data to the client.
     *
     * @return mixed
     */
    public function run(){

        //check sub controller
        foreach ($this->controllers as $controller)
            $controller->run();

        //check if its in our area
        if (strpos($this->getClient()->getUrl().'/', $this->triggerUrl.'/') !== 0) return;

        $uri = substr($this->getClient()->getUrl(), strlen($this->triggerUrl));

        $this->normalizeUrl($uri);

        $route = false;
        $arguments = array();

        //add method
        $arguments[] = $this->getClient()->getMethod();

        //does the requested uri exist?
        if (!list($route, $regexArguments, $trigger) = $this->findRoute($uri)){
            if (!$this->getParent()){
                $this->sendError('rest_route_not_found', "There is no route for '$uri'.");
            } else {
                return false;
            }

        } else {
            if ($pos = strpos($trigger, ':'))
                if (array_search(substr($trigger, 0, $pos), $this->methods) !== false)
                    array_shift($arguments);
            $arguments = array_merge($arguments, $regexArguments);
        }

        //map required arguments
        if (is_array($route[1])){
            foreach ($route[1] as $argument){
                if ($_REQUEST[$argument] === null)
                    $this->sendBadRequest('rest_required_argument_not_found', "Argument '$argument' is missing.");

                $arguments[] = $_REQUEST[$argument];
            }
        }

        //map optional arguments
        if (is_array($route[2])){
            foreach ($route[2] as $argument){

                if ($_GET[$argument])
                    $arguments[] = $_GET[$argument];
            }
        }

        //fire method
        $method = $route[0];
        $object = $this->controller;

        if (!method_exists($object, $method)){
            $this->sendError('rest_method_not_found', tf('Method %s in class %s not found.', $method, get_class($object)));
        }

        try {
            $data = call_user_func_array(array($object, $method), $arguments);
            $this->send($data);
        } catch(Exception $e){
            $this->sendError(get_class($e), $e->getMessage());
        }

    }

    /**
     * Find and return the route for $pUri.
     *
     * @param string $pUri
     * @return array|boolean
     */
    public function findRoute($pUri){

        $def = false;

        $methods[] = '';
        $arguments = array();

        $methods[] = $this->getClient()->getMethod().':';

        foreach ($methods as $method){

            $uri = $method.$pUri;
            if (!$this->routes[$uri]){

                //maybe we have a regex uri
                foreach ($this->routes as $routeUri => $routeDef){
                    if (preg_match('|^'.$routeUri.'$|', $uri, $matches)){
                        $def = $routeDef;
                        array_shift($matches);
                        foreach ($matches as $match){
                            $arguments[] = $match;
                        }
                        return array($def, $arguments, $routeUri);
                        break;
                    }
                }

            } else {
                $def = $this->routes[$uri];
                return array($def, $arguments, $uri);
            }
        }

        return false;
    }

}