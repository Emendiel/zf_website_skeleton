<?php
class Mn_Auth_Adapter_Sdkfb implements Zend_Auth_Adapter_Interface
{
    /**
     * SDK Client
     *
     * @var Mn_Sdk
     */
    protected $_sdk;

    /**
     * Facebook client
     *
     * @var Mn_Facebook_extended
     */
    protected $_facebook;
    
    /**
     * Logs Manager
     *
     * @var Zend_Log
     */
    protected $_log;
    
    /**
     * Identity
     *
     * @var Mn_Auth_Identity
     */
    protected $_identity;

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->_sdk      = Zend_Registry::get('Mn_Sdk');
        $this->_facebook = Zend_Registry::get('Mn_Facebook');
        $this->_log      = Zend_Registry::get('Mn_Log');
        
        $this->_identity  = new Mn_Auth_Identity('facebook');
    }

    /**
     * Authenticate
     *
     */
    public function authenticate()
    {
        
        $aSignedRequest = $this->_facebook->getSignedRequest();
        
        $this->_log->debug('signed_request: ' . print_r($aSignedRequest, true));

        // Test if user has not a facebook session
        if(!$this->_facebook->getSession()){
            
            //1. user is not logged on Facebook
            if(empty($aSignedRequest))
            {
                return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, $this->_identity, array('not logged on Facebook'));
            }
            
            //2. user is logged on facebook but has not accepted the application
            //we know its country, locale and age range
            $this->setUserLocale();
            $this->_identity->setData(array('facebook_session' => $aSignedRequest['user']));
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID, $this->_identity, array('not accepted the application'));
        }

        //user is logged on Facebook, log it in app
        $this->setUserLocale();
        $this->signedSdkRequest();

        //get user information from SDK
        try{
            $oUserSdk = $this->_sdk->request('/users/find_by', 'GET', array('facebook_id' => $this->_facebook->getUser()));

            $oUserSdk['facebook_session'] = $aSignedRequest['user'];
            $this->_identity->setData($oUserSdk);

            return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $this->_identity, array());
        }
        catch(Exception $e)
        {
            if($e->getCode() == '404')
            {
                return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, $this->_identity, array('user does not exist in SDK'));
            }

            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_UNCATEGORIZED, $this->_identity, array('error during SDK request', $e->getCode(), $e->getMessage()));
        }
    }

    /**
     * Sign SDK Request
     */
    protected function signedSdkRequest($set = true)
    {
        $options = $this->_sdk->getOptions()->toArray();

        if($set)
        {
            $tmp =  $this->_facebook->getSignedRequest();
            $options['signedRequest'] = $tmp['oauth_token'];
        }
        else
        {
            unset($options['signedRequest']);
        }

        $this->_sdk->setOptions($options);
    }

    /**
     * Set User Local from Facebook
     */
    public function setUserLocale()
    {
        $signedRequest = $this->_facebook->getSignedRequest();

        if(isset($signedRequest['locale']))
        {
            Zend_Registry::get('Zend_Translate')->setLocale($signedRequest['locale']);
        }
    }

    /**
     * Is identity valid
     *
     * @return boolean
     */
    public function isIdentityValid()
    {
        if(!(($this->_facebook->getSession())
        && ($this->_facebook->getUser() == Zend_Auth::getInstance()->getIdentity()->get('facebook_id'))))
        {
            return false;
        }

        //Hack if user disconnect from Facebook and go just after on the application logged out (cookie fb can't be automaticly destroyed by facebook in the application)
        try
        {
            $userinfo = $this->_facebook->getUserInfo();
        }
        catch(Exception $e)
        {
            return false;
        }
        return true;
    }

    /**
     * Log out
     */
    public function logOut()
    {
        $this->signedSdkRequest(false);
        Zend_Auth::getInstance()->clearIdentity();
    }
}
