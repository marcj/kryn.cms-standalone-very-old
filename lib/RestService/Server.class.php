<?php

namespace RestService;

/**
 * RestService\Server - A REST server class for RESTful APIs.
 */

class Server {

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
     * @var RestService\Server
     */
    private $parentController;


    /**
     * The client
     *
     * @var RestService\Server
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
    public $methods = array('get', 'post', 'put', 'delete', 'head', 'options');


    /**
     * Check access function/method. Will be fired after the route has been found.
     * Arguments: (url, route)
     * 
     * @var callable
     */
    private $checkAccessFn;

    /**
     * Send exception function/method. Will be fired if a route-method throws a exception.
     * Please die/exit in your function then.
     * Arguments: (exception)
     * 
     * @var callable
     */
    private $sendExceptionFn;

    /**
     * If this is true, we send file, line and backtrace if an exception has been thrown.
     * 
     * @var boolean
     */
    private $debugMode = false;


    /**
     * Sets whether the service should serve route descriptions
     * through the OPTIONS method.
     * 
     * @var boolean
     */
    private $describeRoutes = true;


    /**
     * Constructor
     *
     * @param string        $pTriggerUrl
     * @param string|object $pControllerClass
     * @param string        $pRewrittenRuleKey From the rewrite rule: RewriteRule ^(.+)$ index.php?__url=$1&%{query_string}
     * @param RestService\Server $pParentController
     */
    public function __construct($pTriggerUrl, $pControllerClass = null, $pRewrittenRuleKey = '__url',
                                $pParentController = null){

        $this->normalizeUrl($pTriggerUrl);
        $this->setRewrittenRuleKey($pRewrittenRuleKey);

        if ($pParentController){
            $this->parentController = $pParentController;
            $this->setClient($pParentController->getClient());

            if ($pParentController->getCheckAccess())
                $this->setCheckAccess($pParentController->getCheckAccess());

            if ($pParentController->getExceptionHandler())
                $this->setExceptionHandler($pParentController->getExceptionHandler());

            if ($pParentController->getDebugMode())
                $this->setDebugMode($pParentController->getDebugMode());

            if ($pParentController->getDescribeRoutes())
                $this->setDescribeRoutes($pParentController->getDescribeRoutes());

        } else {
            $this->setClient(new Client($this));
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
     * @return Server $this
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
     * @return Server $this
     */
    public function setRewrittenRuleKey($pRewrittenRuleKey){
        $this->rewrittenRuleKey = $pRewrittenRuleKey;
        return $this;
    }

    /**
     * Set the check access function/method.
     * Will fired with arguments: (url, route)
     * 
     * @param callable $pFn 
     * @return Server $this
     */
    public function setCheckAccess($pFn){
        $this->checkAccessFn = $pFn;
        return $this;
    }

    /**
     * Getter for checkAccess
     * @return callable
     */
    public function getCheckAccess(){
        return $this->checkAccessFn;
    }


    /**
     * Sets whether the service should serve route descriptions
     * through the OPTIONS method.
     * 
     * @param boolean $pDescribeRoutes 
     * @return Server $this
     */
    public function setDescribeRoutes($pDescribeRoutes){
        $this->describeRoutes = $pDescribeRoutes;
    }

    /**
     * Getter for describeRoutes.
     * 
     * @return boolean
     */
    public function getDescribeRoutes($pFn){
        return $this->describeRoutes;
    }

    /**
     * Send exception function/method. Will be fired if a route-method throws a exception.
     * Please die/exit in your function then.
     * Arguments: (exception)
     * 
     * @param callable $pFn 
     * @return Server $this
     */
    public function setExceptionHandler($pFn){
        $this->sendExceptionFn = $pFn;
        return $this;
    }

    /**
     * Getter for checkAccess
     * @return callable
     */
    public function getExceptionHandler(){
        return $this->sendExceptionFn;
    }

    /**
     * If this is true, we send file, line and backtrace if an exception has been thrown.
     * 
     * @param boolean $pDebugMode 
     * @return Server $this
     */
    public function setDebugMode($pDebugMode){
        $this->debugMode = $pDebugMode;
        return $this;
    }

    /**
     * Getter for checkAccess
     * @return boolean
     */
    public function getDebugMode(){
        return $this->debugMode;
    }


    /**
     * Alias for getParent()
     *
     * @return Server
     */
    public function done(){
        return $this->getParent();
    }


    /**
     * Returns the parent controller
     *
     * @return Server $this
     */
    public function getParent(){
        return $this->parentController;
    }


    /**
     * Set the URL that triggers the controller.
     *
     * @param $pTriggerUrl
     * @return Server
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
     * @param Client $pClient
     * @return Server $this
     */
    public function setClient($pClient){
        $this->client = $pClient;
        $this->client->setupFormats();

        return $this;
    }


    /**
     * Get the current client.
     *
     * @return Client
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
        if (!$this->getClient()) throw new \Exception('client_not_found_in_ServerController');
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
        if (!$this->getClient()) throw new \Exception('client_not_found_in_ServerController');
        $this->getClient()->sendResponse('500', $msg);
    }

    /**
     * Sends a exception response to the client.
     * @param $pCode
     * @param $pMessage
     * @throws \Exception
     */
    public function sendException($pException){

        if ($this->sendExceptionFn){
            call_user_func_array($this->sendExceptionFn, array($pException));
        }
        
        $message = $pException->getMessage();
        if (is_object($message) && $message->xdebug_message) $message = $message->xdebug_message;

        $msg = array('error' => get_class($pException), 'message' => $message);

        if ($this->debugMode){
            $msg['file'] = $pException->getFile();
            $msg['line'] = $pException->getLine();
            $msg['trace'] = $pException->getTraceAsString();
        }

        if (!$this->getClient()) throw new \Exception('client_not_found_in_ServerController');
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
     * @return Server
     */
    public function addRoute($pUri, $pMethod, $pHttpMethod = '_all_'){
        $this->routes[$pUri][ $pHttpMethod ] = $pMethod;
        return $this;
    }


    /**
     * Same as addRoute, but limits to GET.
     *
     * @param string $pUri
     * @param string $pMethod
     * @return Server
     */
    public function addGetRoute($pUri, $pMethod){
        $this->addRoute($pUri, $pMethod, 'get');
        return $this;
    }


    /**
     * Same as addRoute, but limits to POST.
     *
     * @param string $pUri
     * @param string $pMethod
     * @return Server
     */
    public function addPostRoute($pUri, $pMethod){
        $this->addRoute($pUri, $pMethod, 'post');
        return $this;
    }


    /**
     * Same as addRoute, but limits to PUT.
     *
     * @param string $pUri
     * @param string $pMethod
     * @return Server
     */
    public function addPutRoute($pUri, $pMethod){
        $this->addRoute($pUri, $pMethod, 'put');
        return $this;
    }

    /**
     * Same as addRoute, but limits to HEAD.
     *
     * @param string $pUri
     * @param string $pMethod
     * @return Server
     */
    public function addHeadRoute($pUri, $pMethod){
        $this->addRoute($pUri, $pMethod, 'head');
        return $this;
    }


    /**
     * Same as addRoute, but limits to OPTIONS.
     *
     * @param string $pUri
     * @param string $pMethod
     * @param array  $pArguments Required arguments. Throws an exception if one of these is missing.
     * @param array  $pOptionalArguments
     * @return Server
     */
    public function addOptionsRoute($pUri, $pMethod){
        $this->addRoute($pUri, $pMethod, 'options');
        return $this;
    }


    /**
     * Same as addRoute, but limits to DELETE.
     *
     * @param string $pUri
     * @param string $pMethod
     * @param array  $pArguments Required arguments. Throws an exception if one of these is missing.
     * @param array  $pOptionalArguments
     * @return Server
     */
    public function addDeleteRoute($pUri, $pMethod){
        $this->addRoute($pUri, $pMethod, 'delete');
        return $this;
    }


    /**
     * Removes a route.
     *
     * @param string $pUri
     * @return Server
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
    private function createControllerClass($pClassName){
        if ($pClassName != ''){
            try {
                $this->controller = new $pClassName();
                if (get_parent_class($this->controller) == 'RestService\Server'){
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
     * @return Server new created Server
     */
    public function addSubController($pTriggerUrl, $pControllerClass = '', $pRewrittenRuleKey = '__url'){

        $this->normalizeUrl($pTriggerUrl);

        $controller = new Server($this->triggerUrl.'/'.$pTriggerUrl, $pControllerClass,
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
     * @return Server
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

        //does the requested uri exist?
        list($methodName, $regexArguments, $trigger, $method) = $this->findRoute($uri, $this->getClient()->getMethod());

        if (!$methodName){
            if (!$this->getParent()){
                $this->sendBadRequest('rest_route_not_found', "There is no route for '$uri'.");
            } else {
                return false;
            }
        }

        if ($method != '_all_'){
            $arguments[] = $method;

        }

        if (is_array($regexArguments)){
            $arguments = array_merge($arguments, $regexArguments);
        }


        //open class and scan method
        $ref = new \ReflectionClass($this->controller);
        $method = $ref->getMethod($methodName);
        $params = $method->getParameters();

        if ($method == '_all_'){
            //first parameter is $pMethod
            array_shift($params);
        }

        foreach ($params as $param){
            if (!$param->isOptional() && $_REQUEST[$param->getName()] === null){
                $this->sendBadRequest('rest_required_argument_not_found', tf("Argument '%s' is missing.", $param->getName()));
            }
        }
        var_dump('ok'); exit;

        if ($this->checkAccessFn){
            $args[] = $this->getClient()->getUrl();
            $args[] = $route;
            $args[] = $arguments;
            try {
                call_user_func_array($this->checkAccessFn, $args);
            } catch(\Exception $e){
                $this->sendException($e);
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
        } catch(\Exception $e){
            $this->sendException($e);
        }

    }

    public function collectArguments(&$pArguments, $pNeedArguments, $pRequired){

        if (is_array($pNeedArguments)){
            $black = $this->getRewrittenRuleKey();

            foreach ($pNeedArguments as $argument){
                if ($_REQUEST[$argument] === null && $pRequired)
                    $this->sendBadRequest('rest_required_argument_not_found', "Argument '$argument' is missing.");

                if ($argument == '_'){
                    //we collect all arguments that begins with _
                    $arguments = array();
                    foreach ($_REQUEST as $k => $v){
                        if ($black != $k && substr($k, 0, 1) == '_'){
                            $arguments[substr($k, 1)] = $v; 
                        }
                    }
                    $pArguments[] = $arguments;
                } else {
                    $pArguments[] = $_REQUEST[$argument];
                }
            }
        }
    }

    /**
     * Find and return the route for $pUri.
     *
     * @param string $pUri
     * @param string $pMethod limit to method
     * @return array|boolean
     */
    public function findRoute($pUri, $pMethod = '_all_'){

        if ($method = $this->routes[$pUri][$pMethod]){
            return array($method, null, $pUri, $pMethod);
        } else if ($pMethod != '_all_' && $method = $this->routes[$pUri]['_all_']){
            return array($method, null, $pUri, $pMethod);
        } else {

            //maybe we have a regex uri
            foreach ($this->routes as $routeUri => $routeMethods){

                if (preg_match('|^'.$routeUri.'$|', $pUri, $matches)){

                    if ($routeMethod != $routeMethods[$pMethod]){
                        if ($routeMethod != $routeMethods['_all_'])
                            continue;
                    }
                    array_shift($matches);
                    foreach ($matches as $match){
                        $arguments[] = $match;
                    }

                    return array($routeMethod, $arguments, $pUri, $pMethod);
                }

            }
            }

        return false;
    }

}