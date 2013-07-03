<?php

require_once 'Response.php';
require_once '/../curl.class.php';
/**
 * YQL class for convenient work with Yahoo Query Language
 * @author Martin Bažík
 * 
 * @property-read string $query
 */
class YQL 
{
    public static $defaultTablesDefinitionUrl = 'http://datatables.org/alltables.env';
    private static $curlInst;
    private $yqlUrl = 'http://query.yahooapis.com/v1/public/yql?q=';
	private $query;
	private $tablesDefinitionUrl;
    public static $instance;
    /**
    * @param string $tablesDefinitionUrl Url of the tables definitions file 
    */
    public function __construct($tablesDefinitionUrl = null) 
    {
    	if($tablesDefinitionUrl == null) 
    	{
    	    $tablesDefinitionUrl = self::$defaultTablesDefinitionUrl;
    	}
    	$this->tablesDefinitionUrl = $tablesDefinitionUrl;

         $this->curlInst = curlClass::getInstance();
    }
    public static function getInstance()
    {
        if(!isset(self::$instance))   
        {
            self::$instance = new YQL();
        }
        return self::$instance;
    }
    /**
    * Gets the query to use
    * @return string 
    */
    public function getQuery() 
    {
	   return $this->query;
    }

    /**
    * Sets the query to use
    * @param string $query
    * @return YQL 
    */
    public function setQuery($query) 
    {
    	$this->query = $query;
    	return $this;
    }

    private function prepareRequest($query, $type = "json")
    {
    	return $this->yqlUrl
    		.urlencode($query)
    		."&format=".$type
    		.'&env='
    		.urlencode($this->tablesDefinitionUrl);
    		;
    }

    private function executeRequest($request)
    {
        $result = $this->curlInst->sendGet($request);
        if($result)
        {
            return $result["content"];
        }
    }

    private function parseResponse($data)
    {
    	$jsonObj = json_decode($data);
    	if(isset($jsonObj->error))
    	{
    	    throw new Exception($jsonObj->error->description);
    	}

    	$response = new Response($data, $jsonObj->query->count, $jsonObj->query->created, $jsonObj->query->lang, $jsonObj->query->results);
    	return $response;
    }

    /**
    * Executes the YQL query
    * @param string $query
    * @throws Exception
    * @return Response 
    */
    public function execute($query = null, $type = "json")
    {
    	if($query == null) 
    	{
    	    $query = $this->query;
    	}
    	if($query == null) 
    	{
    	    throw new Exception('No query set');
    	}
    	$request = $this->prepareRequest($query, $type);
    	$response = $this->executeRequest($request);
    	return $this->parseResponse($response);
    }

    public static function query($query)
    {
    	$yql = new static();
    	return $yql->execute($query);
    }
}