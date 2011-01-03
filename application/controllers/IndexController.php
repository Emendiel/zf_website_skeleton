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
        $this->_helper->access->permissionDisabled = array('all');
    }

    public function indexAction()
    {        
        $facebook = $this->getInvokeArg('bootstrap')->getResource('mnFacebook');
        
        $this->view->fbAppId = $facebook->getAppId();        
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

