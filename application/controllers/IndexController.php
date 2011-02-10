<?php
/**
 * Index controller
 * 
 * @category application
 * @package default
 * @copyright Mimesis Republic
 */

/**
 * IndexController
 * 
 * @category application
 * @package default
 */
class IndexController extends Zend_Controller_Action
{
    public function init()
    {

    }

    public function indexAction()
    {
        $auth = Zend_Auth::getInstance();
    
        if ($auth->hasIdentity())
        {
            echo 'g une session';exit;
            $this->_helper->resource('Log')->info("User has identity");
            $oIdentity = $auth->getIdentity();
        }
        else{
            $this->_helper->resource('Log')->info("User has not identity");
            $oSdk = $this->_helper->resource('mnSdk');
            $oAdapter = new Mn_Auth_Adapter_Sdkfb($oSdk);
            $oResult = $auth->authenticate($oAdapter);
            if($oResult->isValid()){
                echo 'isvalid';exit;
                $this->_helper->resource('Log')->info("Auth success");
                $oIdentity = $auth->getIdentity();
            }
            else
            {
                $this->_helper->resource('Log')->info("Auth fail");
            }
        }

        //print_r($user);
    }
    
    public function registerAction()
    {

    }
    
    public function phpinfoAction()
    {
        phpinfo();
        exit;
    }

    public function forbiddenAction()
    {
        echo "forbidden";
        exit;
    }
}

