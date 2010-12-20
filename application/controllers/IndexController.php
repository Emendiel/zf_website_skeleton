<?php

class IndexController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->access->permissionDisabled = array('all');
    }

    public function indexAction()
    {
        //var_dump(Zend_Registry::get('Zend_Log'));
        // action body
        $this->getInvokeArg('bootstrap')->getResource('log')->log('test log', Zend_Log::WARN);
        
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

