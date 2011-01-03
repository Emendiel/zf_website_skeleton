<?php

class Mn_View_Helper_Facebook extends Zend_View_Helper_Abstract
{

	public function facebook(){
			
		$sLocale = $this->getCompleteLocal(Zend_Registry::get('Zend_Locale'));

		$oZend_Config_Yaml = new Zend_Config_Yaml(		 
		  APPLICATION_PATH . '/configs/application.yaml',
		  APPLICATION_ENV
		);

		$sFbAppId = $oZend_Config_Yaml->resources->mnFacebook->appId;
		$sCookie = $oZend_Config_Yaml->resources->mnFacebook->cookie;
			
		$sScript = '
      <div id="fb-root"></div>
      <script src="http://connect.facebook.net/' . $sLocale . '/all.js"></script>
      <script>
         FB.init({ 
            appId:"' . $sFbAppId . '", cookie: ' . $sCookie . ', status: true, xfbml:true 
         });
      </script>';
			
		return $sScript;
	}


	protected function getCompleteLocal($p_sLocale){
		return strtolower($p_sLocale) . '_' . strtoupper($p_sLocale);
	}

}