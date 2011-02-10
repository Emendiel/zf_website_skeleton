<?php
class Mn_Auth_Adapter_Sdkfb implements Zend_Auth_Adapter_Interface
{
    protected $_sdk;

    public function __construct($oSdk){
        $this->_sdk = $oSdk;
    }
    
    public function authenticate(){
        $oAuthUser  = new Mn_Auth_Adapter_Sdkfb_User();
    
        try{
            $user = $this->_sdk->request('/users/1', 'get');
            
            /*if(){
            }*/
            
            return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $oAuthUser, array());
        }
        catch(Exception $e){
        
        }
    }
}
