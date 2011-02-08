<?php
/**
 * Mn Library
 *
 *
 * @category   Mn
 * @package    Mn_Sdk
 * @copyright  Copyright (c) 2004-2010 Mimesis Republic
 * @version    $Id: Sdk.php 21015 2010-02-11 01:56:02Z freak $
 */

/**
 * Sdk
 *
 * @category   Mn
 * @package    Mn_sdk
 * @copyright  Copyright (c) 2004-2010 Mimesis Republic
 */
class Mn_Sdk
{
    protected $_options = null;

    public function __construct($options = array()){
        $this->setOptions($options);
    }
    
    public function getOptions(){
        return $this->_options;
    }
    
    public function setOptions($options){
        if(is_array($options)){
            $options = new Zend_Config($options);
        }
        elseif(!($options instanceof Zend_Config)){
            throw new Exception ('Configuration options must be an array or a Zend_Config object');
        }
        
        $this->_options = $options;
        
        return $this;
    }
    
    public function getUri(){
        if(empty($this->_options->uri) || empty($this->_options->username) || empty($this->_options->password)){
            throw new Exception('Config options missing : uri, user, password needed');
        }
        
        $uri = sprintf("http://%s:%s@%s", $this->_options->username, $this->_options->password, $this->_options->uri);
        
        return $uri;
    }
    
    public function request($path, $method = 'GET', $params = array()){
        // Add string /v2 because Zend_Rest drop this part of uri
        $path = '/v2' . $path;
        
        $oZend_Rest_Client = new Zend_Rest_Client();
        $oZend_Rest_Client->setUri($this->getUri());

        switch(strtoupper($method)){
            case 'GET':
                $response = $oZend_Rest_Client->restGet($path, $params);
            break;
            case 'POST':
                $response = $oZend_Rest_Client->restPost($path, $params);
            break;
            case 'PUT':
                $response = $oZend_Rest_Client->restPut($path, $params);
            break;
            case 'DELETE':
                $response = $oZend_Rest_Client->restDelete($path);
            break;
            default:
                throw new Exception("Invalid method");
        }
        
        return $response;
    }
}
