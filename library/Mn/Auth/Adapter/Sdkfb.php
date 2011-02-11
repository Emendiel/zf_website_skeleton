<?php
class Mn_Auth_Adapter_Sdkfb implements Zend_Auth_Adapter_Interface
{
    protected $_sdk;
    protected $_facebook;

    public function __construct($oSdk, $oFacebook){
        $this->_sdk = $oSdk;
        $this->_facebook = $oFacebook;
    }
    
    public function authenticate(){
        if($this->_facebook->getSession()){
            if($this->_facebook->getUser()){
                $options = $this->_sdk->getOptions()->toArray();
                $options['signedRequest'] = $this->_facebook->getSignedRequest();
                $this->_sdk->setOptions($options);
                
                $FbUserInfo = $this->_facebook->getUserInfo();

                // Set website locale with facebook locale
                Zend_Registry::get('Zend_Translate')->setLocale($FbUserInfo['locale']);
    
                try{
                    $oStdClass = $this->_sdk->request('/users/1', 'get');
                    $oAuthUser  = new Mn_Auth_Adapter_Sdkfb_User($oStdClass);
                    
                    return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $oAuthUser, array());
                }
                catch(Exception $e){
                    error_log($e->getMessage());
                }
            }
        }
        else{
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_UNCATEGORIZED, $oAuthUser, array());
        }
    }
}
