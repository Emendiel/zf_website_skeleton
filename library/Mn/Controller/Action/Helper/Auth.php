<?php

class Mn_Controller_Action_Helper_Auth extends Zend_Controller_Action_Helper_Abstract
{
    public function preDispatch()
    {
        $oResourceHelper = new Mn_Controller_Action_Helper_Resource();
        $oResourceHelper->setActionController($this->getActionController());
        
        $auth = Zend_Auth::getInstance();
    
        if ($auth->hasIdentity())
        {
            $oResourceHelper->direct('Log')->info("User has identity");
        }
        else{
            $oResourceHelper->direct('Log')->info("User has not identity");
            $oSdk = $oResourceHelper->direct('mnSdk');
            $Facebook = $oResourceHelper->direct('mnFacebook');
            $oAdapter = new Mn_Auth_Adapter_Sdkfb($oSdk, $Facebook);
            $oResult = $auth->authenticate($oAdapter);

            if($oResult->isValid()){
                $oResourceHelper->direct('Log')->info("Auth success");
            }
            else
            {
                $oResourceHelper->direct('Log')->info("Auth fail");
            }
        }
    }
}
