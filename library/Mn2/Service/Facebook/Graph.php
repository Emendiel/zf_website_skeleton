<?php

class Mn2_Service_Facebook_Graph extends Zend_Service_Abstract
{
    const GRAPH_URL = 'https://graph.facebook.com';

    protected $_client = null;
    
    protected $_accessToken = null;

    public function __construct()
    {
        $this->_client = new Zend_Rest_Client();
        $this->_client->setUri(GRAPH_URL);
    }
    
    public function setAccessToken($token = null)
    {
        $this->_accessToken = $token;
    }
    
    protected function authenticate($params)
    {
        if(!empty($this->_accessToken))
        {
            $params['access_token'] = $this->_accessToken;
        }
        
        return $params;
    }

    public function get($path, array $params = array())
    {
        $params['metadata'] = 1;
        
        $params = authenticate($params);
        
        $response = $this->_client->restGet($path, $params);
    }

    public function post($path, array $params = array())
    {
        $params = authenticate($params);
        $response = $this->_client->restPost($path, $params);
    }

    public function delete($path)
    {
        $response = $this->_client->restDelete($path);
    }
     
    public function search($type, $query)
    {
        $params         = array();
        $params['type'] = $type;
        $params['q']    = $query;
        
        $this->get('/search', $params);
    }

    
}