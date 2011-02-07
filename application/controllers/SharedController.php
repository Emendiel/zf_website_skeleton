<?php
/**
 * Shared controller
 * 
 * @category application
 * @package default
 * @copyright Mimesis Republic
 */

/**
 * SharedController
 * 
 * @category application
 * @package default
 */
class SharedController extends Zend_Controller_Action
{
    public function init()
    {

    }

    public function indexAction()
    {        
        
    }
    
    public function redirectAction()
    {
        if($this->getRequest()->getParam('redirectTo'))
        {
            $this->_helper->getHelper('Redirector')->gotoUrl($this->getRequest()->getParam('redirectTo'));
        }
    }
    
    public function jsConfigAction()
    {                                       
       $configFb = $this->_helper->resource('mnFacebook')->getOptions();
       unset($configFb['secret']);
       
       $config = array(
                       'user' => 'toto', 
                       'fb'   => $configFb);
       
       $this->_helper->layout->disableLayout();
       $this->getResponse()->setHeader('content-type', 'text/javascript');
       $this->view->jsonConfig = Zend_Json::encode($config);
    }
}

