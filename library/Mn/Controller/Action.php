<?php
/**
 *
 * @category   Mn
 * @package    Mn_Controller
 */

//TODO: refact in Zend_Controller_Action_Helper (http://julien-pauli.developpez.com/tutoriels/zend-framework/atelier/aclmvc/)

/**
 * @category   Mn
 * @package    Mn_Controller
 */
abstract class Mn_Controller_Action extends Zend_Controller_Action
{
    protected $aclDisabled = array();
    
    /**
     * Verify authorization prior to run an action
     *
     */
    public function preDispatch()
    {
        //if acl is disabled for this $request
        if(in_array('all', $this->aclDisabled) || in_array($this->getRequest()->getActionName(), $this->aclDisabled)) {
            parent::preDispatch();
            return;
        }
        
        if(!Mn_Acl::getInstance()->isAllowed($this->_getCurrentRoles(), $this->getRequest()->getControllerName(), $this->getRequest()->getActionName()))
        {
            //TODO: improve redirect for forbidden access
            $this->getHelper('redirector')->gotoUrlAndExit('/index/forbidden');
        }
        
        parent::preDispatch();
    }
    
     /**
     * Get the current roles of the user
     * 
     * @return array roles
     */
    protected function _getCurrentRoles()
    {
        $auth = Zend_Auth::getInstance();
        
        if (!$auth->hasIdentity()) 
        {
            $roles[] = Mn_Acl::getInstance()->getDefaultRole();
            return $roles;
        }
        
        //TODO: create Zend_Auth user object
        return $auth->getIdentity()->getRoles();
    }
}