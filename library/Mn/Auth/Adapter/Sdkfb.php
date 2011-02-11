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
     * Constructor
     *
     * @param n_Sdk $oSdk
     * @param mixed $oFacebook
     */
    public function __construct($oSdk, $oFacebook)
    {
        $this->_sdk      = $oSdk;
        $this->_facebook = $oFacebook;
    }

    /**
     * Authenticate
     *
     */
    public function authenticate()
    {

        $this->setUserLocale();
        $oAuthUser  = new Mn_Auth_Adapter_Sdkfb_User();

        // Test if user has a facebook session
        if(!$this->_facebook->getSession()){
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_UNCATEGORIZED, null, array('no facebook session'));
        }

        // Test is we can get user from facebook (user has accepted the application
        if(!$this->_facebook->getUser()){
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_UNCATEGORIZED, null, array('no user send by facebook'));
        }

        $this->signedSdkRequest();

        try{
            $oUserSdk = $this->_sdk->request('/users/find_by', 'GET', array('facebook_id' => $this->_facebook->getUser()));

            $oAuthUser->setData($oUserSdk);

            return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $oAuthUser, array());
        }
        catch(Exception $e)
        {
            error_log($e);
            if($e->getCode() == '404')
            {
                return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND, null, array('user does not exist'));
            }

            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE_UNCATEGORIZED, null, array('error during SDK request', $e->getCode(), $e->getMessage()));
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
