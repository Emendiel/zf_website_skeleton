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
        $Sdk = $this->_helper->resource('mnSdk');
        
        $user = $Sdk->request('/users/1', 'get');
    
        print_r($user);
    
//        echo Zend_Registry::get('Zend_Locale');
    
        /*echo 'index';
        
        $Facebook = $this->_helper->resource('mnFacebook');
        
        $option = $Facebook->getOptions();
        print_r($option);
        
        $session = $Facebook->getSession();
        print_r($session);
        
        $loginUrl = $Facebook->getLoginUrl(
            array(
            'canvas'    => 1,
            'fbconnect' => 0,
            'req_perms' => 'email,publish_stream,status_update,user_birthday, user_location,user_work_history'
            )
        );
 
        $fbme = null;
 
        if (!$session) {
            echo "<script type='text/javascript'>top.location.href = '$loginUrl';</script>";
            exit;
        }
        else {
            try {
                $uid      =   $Facebook->getUser();
                $fbme     =   $Facebook->api('/me');
 
            } catch (FacebookApiException $e) {
                echo "<script type='text/javascript'>top.location.href = '$loginUrl';</script>";
                exit;
            }
        }*/
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

