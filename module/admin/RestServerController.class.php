<?php

class RestServerController {

    /**
     * The client
     *
     * @var RestServerClient
     */
    private $client;

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
        return $this->client;
    }

    /**
     * Sends a 'Bad Request' response to the client.
     *
     * @param $pCode
     * @param $pMessage
     */
    public function sendBadRequest($pCode, $pMessage){
        $msg = array('error' => $pCode, 'message' => $pMessage);
        if (!$this->getClient()) return $this->raiseError($pCode, $pMessage);
        $this->getClient()->sendResponse('400', $msg);
    }


    /**
     * Sends a 'Internal Server Error' response to the client.
     * @param $pCode
     * @param $pMessage
     * @throws \Exception
     */
    public function sendError($pCode, $pMessage){
        $msg = array('error' => $pCode, 'message' => $pMessage);
        if (!$this->getClient()) throw new \Exception('client_not_found_in_RestServerController');
        $this->getClient()->sendResponse('500', $msg);
    }


}


