<?php

class IndexController extends Mn_Controller_Action
{
    protected $aclDisabled = array('all');

    public function init()
    {
        /* Initialize action controller here */
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

