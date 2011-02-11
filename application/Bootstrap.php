<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initHelpers()
    {
        Zend_Controller_Action_HelperBroker::addHelper(new Mn_Controller_Action_Helper_P3p());
        Zend_Controller_Action_HelperBroker::addHelper(new Mn_Controller_Action_Helper_Auth());
    }
}

