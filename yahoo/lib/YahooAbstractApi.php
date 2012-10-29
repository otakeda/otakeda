<?php

/**
 * Abstract API Class for Yahoo! JAPAN API
 **/
abstract class YahooAbstractApi{

    protected $guid;
    protected $session;
    protected $client;
    protected $result = NULL;

	/**
	* constructor
	**/
	public function __construct(YahooSession $session){
        $this->_init( $session );
    }

	/**
	* initialize session
    *
	* @param YahooSession $session
	**/
    private function _init( $session ) {
		$this->session = $session;
		$this->client = $session->getClient();
		$this->guid = $session->getGuid();
    }

	/**
	* get API response
    *
	* @param Array $parameters 
    * @return some response
	**/
    abstract public function getResponse($parameters);

	/**
	* execute HTTP GET request
    *
	* @param String $entrypoint
	* @param Array $parameters 
	**/
    protected function httpGet( $entrypoint, $parameters = array() ) {
        $this->result = $this->client->get($entrypoint, $parameters);
    }

	/**
	* execute HTTP POST request
    *
	* @param String $entrypoint
	* @param Array $parameters 
	**/
    protected function httpPost( $entrypoint, $parameters = array() ) {
        $this->result = $this->client->post($entrypoint, $parameters);
    }

	/**
	* get HTTP response body
    *
	* @return String  
	**/
    protected function getResponseBody() {
        return $this->result['responseBody'];
    }

	/**
	* get HTTP response headers
    *
	* @return Array
	**/
    protected function getResponseHeaders() {
        return $this->result['responseHeaders'];
    }

	/**
	* get HTTP request body
    *
	* @return String  
	**/
    protected function getRequestBody() {
        return $this->result['responseBody'];
    }

	/**
	* get HTTP request headers
    *
	* @return Array
	**/
    protected function getRequestHeaders() {
        return $this->result['responseHeaders'];
    }

}
