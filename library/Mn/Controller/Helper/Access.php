<?php
/**
 *
 * @category   Mn
 * @package    Mn_Controller
 */

//TODO: study improvement to update Controller implementing Zend_Resouce_Interface and role Zend_Role_Interface

/**
 * @category   Mn
 * @package    Mn_Controller
 */
class Mn_Controller_Action_Helper_Access extends Zend_Controller_Action_Helper_Abstract
{
    /**
     * Resource name per default the current controller name
     * 
     * @var null|string
     */
    public $resourceId = null;   
    
    /**
     * List of the permissions control disabled for the current controller, empty per default
     * 
     * @var array
     */
    public  $permissionDisabled = array();
    
    /**
     * Map between action name and permission, per default the action name is taken
     * 
     * @var array [actionName => permission]
     */
    public $permissionMap = array();
    
    /**
     * Control authorization prior to run an action
     *
     */
    public function preDispatch()
    {
        $resourceId = !empty($this->resourceId) ? $this->resourceId : $this->getRequest()->getControllerName();

        $permissionName = isset($this->permissionMap[$this->getRequest()->getActionName()]) ? 
                        $this->permissionMap[$this->getRequest()->getActionName()] : $this->getRequest()->getActionName();
                        
        //if acl is disabled for this $request authorize the user
        if(in_array('all', $this->permissionDisabled) || in_array($permissionName, $this->permissionDisabled)) {
            parent::preDispatch();
            return;
        }
        
        //test if the current user is allowed to made a specific action on this resource
        if(!Mn_Acl::getInstance()->isAllowed($this->_getCurrentRoles(), $resourceId, $actionName))
        {
            //TODO: improve redirect for forbidden access, exception ?
            $this->_actionController->getHelper('redirector')->gotoUrlAndExit('/index/forbidden');
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