<?php

class Mn_Controller_Action_Helper_Auth extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Auth Adapter object
     *
     * @var Zend_Auth_Adapter_Interface
     */
    protected $_authAdapter;

    /**
     * SDK Client
     *
     * @var Mn_Sdk
     */
    protected $_sdk;

    /**
     * Facebook Client
     *
     * @var Mn_Facebook_Extended
     */
    protected $_facebook;

    /**
     * Logs Manager
     *
     * @var Zend_Log
     */
    protected $_log;

    /**
     * Constructor
     */
    public function init()
    {
        $this->_auth = Zend_Auth::getInstance();

        $oResourceHelper = new Mn_Controller_Action_Helper_Resource();
        $oResourceHelper->setActionController($this->getActionController());

        $this->_sdk      = $oResourceHelper->direct('mnSdk');
        $this->_facebook = $oResourceHelper->direct('mnFacebook');
        $this->_log      = $oResourceHelper->direct('log');
        
        $this->_authAdapter = new Mn_Auth_Adapter_Sdkfb($this->_sdk, $this->_facebook);
    }

    public function preDispatch()
    {
        if (Zend_Auth::getInstance()->hasIdentity())
        {
            if($this->_authAdapter->isIdentityValid())
            {
                $this->_log->info("User has an identity");
                return;
            }
            
            $this->_log->info("Invalid identity");
            $this->logout();
        }

        $this->logIn();
    }

    /**
     * Log in user
     */
    public function logIn()
    {
        $this->_log->info("Authenticate");

        $oResult  = Zend_Auth::getInstance()->authenticate($this->_authAdapter);

        if($oResult->isValid())
        {
            $this->_log->info("Auth success");
            return;
        }
        $this->_log->info("Auth fail");
    }

    /**
     * Log out user
     */
    public function logOut()
    {
        $this->_log->info("log out");
        $this->_authAdapter->logOut();
    }
}
