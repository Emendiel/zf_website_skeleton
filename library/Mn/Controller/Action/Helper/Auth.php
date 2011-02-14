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
     * @var Zend_logger
     */
    protected $_logger;

    /**
     * Init
     */
    public function init()
    {
        $this->_sdk      = Zend_Registry::get('Mn_Sdk');
        $this->_facebook = Zend_Registry::get('Mn_Facebook');
        $this->_logger   = Zend_Registry::get('Mn_Log');

        $this->_authAdapter = new Mn_Auth_Adapter_Sdkfb();
    }

    public function preDispatch()
    {
        if (Zend_Auth::getInstance()->hasIdentity())
        {
            if($this->_authAdapter->isIdentityValid())
            {        
                $this->_logger->info("User has an identity");
                return;
            }

            $this->_logger->info("Invalid identity");
            $this->logOut();
        }

        $this->logIn();
    }

    /**
     * Log in user
     */
    public function logIn()
    {
        $this->_logger->info("Authenticate");

        $oResult  = Zend_Auth::getInstance()->authenticate($this->_authAdapter);

        if($oResult->isValid())
        {
            $this->_logger->info("Auth success");
            return;
        }

        //log the error
        $logPriority = ($oResult->getCode() === Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND || $oResult->getCode() === Zend_Auth_Result::FAILURE_UNCATEGORIZED) ? Zend_Log::ERR : Zend_Log::INFO;
        $this->_logger->log("Auth fail, " . $oResult->getCode(). ': ' . implode(' / ', $oResult->getMessages()), $logPriority);
    }

    /**
     * Log out user
     */
    public function logOut()
    {
        $this->_logger->info("log out");
        $this->_authAdapter->logOut();
    }
}
