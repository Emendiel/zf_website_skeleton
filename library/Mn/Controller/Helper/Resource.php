<?php

class Mn_Controller_Action_Helper_Resource extends Zend_Controller_Action_Helper_Abstract
{
	public function direct($p_sResource){
		$oBootstrap = $this->_actionController->getInvokeArg('bootstrap');
		
		if (!$oBootstrap->hasResource($p_sResource)) {
			throw new Zend_Exception(sprintf('Resource %s doesn\'t exist', $p_sResource));
		}
		
		return $oBootstrap->getResource($p_sResource);
	}
}