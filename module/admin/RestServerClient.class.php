<?php


class RestServerClient {

    /**
     * Current output format.
     *
     * @var string
     */
    private $outputFormat = 'xml';


    /**
     * List of possible output formats.
     *
     * @var array
     */
    private $outputFormats = array(
        'json' => 'asJSON',
        'xml' => 'asXML'
    );

    /**
     * @var RestServerController
     *
     */
    private $controller;

    public function __construct($pRestServerController){
        $this->controller = $pRestServerController;

        $this->setupFormats();
    }

    /**
     * Sends the actual response.
     *
     * @internal
     * @param string $pHttpCode
     * @param $pMessage
     */
    public function sendResponse($pHttpCode = '200', $pMessage){

        if ($_GET['suppress_status_code'] || php_sapi_name() === 'cli'){
            $pMessage['status'] = $pHttpCode;
        } else {
            $httpMap = array(
                '200' => '200 OK',
                '500' => '500 Internal Server Error',
                '400' => '400 Bad Request',

            );
            header('HTTP/1.0 '.$httpMap[$pHttpCode]?$httpMap[$pHttpCode]:$pHttpCode);
        }

        $method = $this->outputFormats[$this->outputFormat];
        print $this->$method($pMessage);
        exit;

    }


    /**
     * Converts $pMessage to pretty json.
     *
     * @param $pMessage
     * @return string
     */
    public function asJSON($pMessage){
        return json_format(json_encode($pMessage));
    }


    /**
     * Converts $pMessage to xml.
     *
     * @param $pMessage
     * @return string
     */
    public function asXML($pMessage){
        $xml = new SimpleXMLElement('<response/>');
        $pMessage = array_flip($pMessage);
        array_walk_recursive($pMessage, array($xml, 'addChild'));
        return $xml->asXML();
    }


    /**
     * Add a additional output format.
     *
     * @param string $pCode
     * @param string $pMethod
     * @return RestServerClient $this
     */
    public function addOutputFormat($pCode, $pMethod){
        $this->outputFormats[$pCode] = $pMethod;
        return $this;
    }


    /**
     * Set the current output format.
     *
     * @param string $pFormat a key of $outputForms
     * @return RestController
     */
    public function setFormat($pFormat){
        $this->outputFormat = $pFormat;
        return $this;
    }

    public function getController(){
        return $this->controller;
    }


    /**
     * Setup formats.
     */
    public function setupFormats(){

        //through HTTP_ACCEPT
        foreach ($this->outputFormats as $formatCode => $formatMethod){
            if (strpos($_SERVER['HTTP_ACCEPT'], $formatCode) !== false){
                $this->outputFormat = $formatCode;
                break;
            }
        }

        //through uri suffix
        if (preg_match('/\.(\w+)$/i', $this->getController()->getUrl(), $matches)) {
            $this->outputFormat = $matches[1];
        }

        return $this;
    }

}