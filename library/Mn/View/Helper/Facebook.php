<?php

/**
 * Facebook
 * 
 * @category   Mn
 * @package    Mn_Controller
 * @copyright  Copyright (c) 2004-2010 Mimesis Republic
 *
 */

/**
 * Mn_View_Helper_Facebook
 *
 * @category   Mn
 * @package    Mn_facebook
 * @copyright  Copyright (c) 2004-2010 Mimesis Republic
 */
class Mn_View_Helper_Facebook extends Zend_View_Helper_Abstract
{

	/**
	 * Format javascript to load facebook connect button
	 * TODO: Add config parameter to manage facebook connect url
	 * 
	 * @return string javascript
	 */
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
      </script>
      
      <p><fb:login-button /></p>';
			
		return $sScript;
	}

  /**
   * Format a long local string with short local string
   * If $p_sLocale lenght is more than 2, don't format and return initial parameter
   * TODO: Find better way to retrieve long locale
   * 
   * @param $p_sLocale string : Short local
   * @return string : Long locale
   */
	protected function getCompleteLocal($p_sLocale){
		if(strlen($p_sLocale) > 2){
			return $p_sLocale;
		}
		
		return strtolower($p_sLocale) . '_' . strtoupper($p_sLocale);
	}

}