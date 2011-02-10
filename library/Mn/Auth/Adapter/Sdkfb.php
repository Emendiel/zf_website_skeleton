<?php

class Mn_Auth_Adapter_Sdkfb implements Zend_Auth_Adapter_Interface
{
    protected $_Mn_Sdk;

    public function __construct($oMn_Sdk){
        $this->_Mn_Sdk = $oMn_Sdk;
    }
    
    public function authenticate(){
        $oZend_Auth_Result = new Zend_Auth_Result();
        
        if(){
            $user = $this->_Mn_Sdk->request('/users/me', 'get');
        }
        
        return $oZend_Auth_Result;
    }
}
