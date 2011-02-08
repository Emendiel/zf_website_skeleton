<?php

class Mn_Controller_Action_Helper_Auth extends Zend_Controller_Action_Helper_Abstract
{
    public function preDispatch()
    {
        $oResourceHelper = new Mn_Controller_Action_Helper_Resource();
        $oResourceHelper->setActionController($this->getActionController());

        ////////////////////////////////////////////////////////////////////////
        /////////////////////////  Check facebook auth  ////////////////////////
        ////////////////////////////////////////////////////////////////////////
        $Facebook = $oResourceHelper->direct('mnFacebook');
        
        if($Facebook->getSession()){
            if($Facebook->getUser()){
                $FbUserInfo = $Facebook->getUserInfo();
                
                // Set website locale with facebook locale
                Zend_Registry::get('Zend_Translate')->setLocale($FbUserInfo['locale']);
            }
        }
    }
}
