<?php

class Mn_Acl
{
     /**
     * Default role
     * 
     * @var string
     */
    protected static $_defaultRole = 'guest';
    
    /**
     * Instance singleton
     * 
     * @var Mn_Acl
     */
    protected static $_instance;
    
    /**
     * Persistent storage handler
     * 
     * @var Mn_Acl_Storage_Inteface
     */
    protected $_storage;
    
    /**
     * Singleton pattern implementation makes "new" unavailable
     *
     * @return void
     */
    protected function __construct()
    {}

    /**
     * Singleton pattern implementation makes "clone" unavailable
     *
     * @return void
     */
    protected function __clone()
    {}
    
    /**
     * Returns an instance of Mn_Acl
     *
     * Singleton pattern implementation
     *
     * @return Mn_Acl Provides a fluent interface
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
    
    /**
     * Returns the persistent storage handler
     *
     * Session storage is used by default unless a different storage adapter has been set.
     *
     * @return Mn_Acl_Storage_Interface
     */
    public function getStorage()
    {
        if (null === $this->_storage) {
            /**
             * @see Mn_Acl_Storage_Session
             */
            require_once 'Mn/Acl/Storage/Session.php';
            $this->setStorage(new Mn_Acl_Storage_Session());
        }

        return $this->_storage;
    }

    /**
     * Sets the persistent storage handler
     *
     * @param  Mn_Acl_Storage_Interface $storage
     * @return Mn_Acl Provides a fluent interface
     */
    public function setStorage(Mn_Acl_Storage_Interface $storage)
    {
        $this->_storage = $storage;
        return $this;
    }
    
    /**
     * Get Acl Object frome storage or an empty Acl object if storage is empty
     * 
     * @return Zend_Acl
     */
    protected function _getAcl()
    {
        if($this->getStorage()->isEmpty())
        {
            $acl = new Zend_Acl();
            $acl->addRole($this->getDefaultRole());
        }
        else {
            $acl = $this->getStorage()->read();
        }
        
        return $acl;
    }
    
    /**
     * Set Acl Object in storage
     * 
     * @param Zend_Acl
     * @return Mn_Acl Provides a fluent interface
     */
    protected function _setAcl(Zend_Acl $acl)
    {
        $this->getStorage()->write($acl);
        
        return $this;
    } 
    
    /**
     * Get default role
     * 
     * @return string
     */
    public function getDefaultRole()
    {
        return static::$_defaultRole;
    }
    
    /**
     * Adds a Role having an identifier unique to the registry
     *
     * The $parents parameter may be a reference to, or the string identifier for,
     * a Role existing in the registry, or $parents may be passed as an array of
     * these - mixing string identifiers and objects is ok - to indicate the Roles
     * from which the newly added Role will directly inherit.
     *
     * In order to resolve potential ambiguities with conflicting rules inherited
     * from different parents, the most recently added parent takes precedence over
     * parents that were previously added. In other words, the first parent added
     * will have the least priority, and the last parent added will have the
     * highest priority.
     *
     * @param  Zend_Acl_Role_Interface              $role
     * @param  Zend_Acl_Role_Interface|string|array $parents
     * @uses   Zend_Acl_Role_Registry::add()
     * @return Mn_Acl Provides a fluent interface
     */
    public function addRole($role, $parents = null)
    {
        $acl = $this->_getAcl();
        $acl->addRole($role, $parents);
        $this->_setAcl($acl);
        
        return $this;
    }
    
    /**
     * Returns the identified Role
     *
     * The $role parameter can either be a Role or Role identifier.
     *
     * @param  Zend_Acl_Role_Interface|string $role
     * @uses   Zend_Acl_Role_Registry::get()
     * @return Zend_Acl_Role_Interface
     */
    public function getRole($role)
    {
        return $this->_getAcl()->get($role);
    }

    /**
     * Returns true if and only if the Role exists in the registry
     *
     * The $role parameter can either be a Role or a Role identifier.
     *
     * @param  Zend_Acl_Role_Interface|string $role
     * @uses   Zend_Acl_Role_Registry::has()
     * @return boolean
     */
    public function hasRole($role)
    {
        return $this->_getAcl()->hasRole($role);
    }

    /**
     * Returns true if and only if $role inherits from $inherit
     *
     * Both parameters may be either a Role or a Role identifier. If
     * $onlyParents is true, then $role must inherit directly from
     * $inherit in order to return true. By default, this method looks
     * through the entire inheritance DAG to determine whether $role
     * inherits from $inherit through its ancestor Roles.
     *
     * @param  Zend_Acl_Role_Interface|string $role
     * @param  Zend_Acl_Role_Interface|string $inherit
     * @param  boolean                        $onlyParents
     * @uses   Zend_Acl_Role_Registry::inherits()
     * @return boolean
     */
    public function inheritsRole($role, $inherit, $onlyParents = false)
    {
        return $this->_getAcl()->inheritsRole($role, $inherit, $onlyParents);
    }
    
    /**
     * Removes the Role from the registry
     *
     * The $role parameter can either be a Role or a Role identifier.
     *
     * @param  Zend_Acl_Role_Interface|string $role
     * @uses   Zend_Acl_Role_Registry::remove()
     * @return Mn_Acl Provides a fluent interface
     */
    public function removeRole($role)
    {
        $acl = $this->_getAcl();
        $acl->removeRole($role);
        $this->_setAcl($acl);

        return $this;
    }

    /**
     * Removes all Roles from the registry
     *
     * @uses   Zend_Acl_Role_Registry::removeAll()
     * @return Mn_Acl Provides a fluent interface
     */
    public function removeRoleAll()
    {
        $acl = $this->_getAcl();
        $acl->removeRoleAll();
        $this->_setAcl($acl);

        return $this;
    }
    
    /**
     * Adds a Resource having an identifier unique to the ACL
     *
     * The $parent parameter may be a reference to, or the string identifier for,
     * the existing Resource from which the newly added Resource will inherit.
     *
     * @param  Zend_Acl_Resource_Interface|string $resource
     * @param  Zend_Acl_Resource_Interface|string $parent
     * @throws Zend_Acl_Exception
     * @return Mn_Acl Provides a fluent interface
     */
    public function addResource($resource, $parent = null)
    {
        $acl = $this->_getAcl();
        $acl->addResource($resource, $parent);
        $this->_setAcl($acl);

        return $this;
    }

    /**
     * Returns the identified Resource
     *
     * The $resource parameter can either be a Resource or a Resource identifier.
     *
     * @param  Zend_Acl_Resource_Interface|string $resource
     * @throws Zend_Acl_Exception
     * @return Zend_Acl_Resource_Interface
     */
    public function getResource($resource)
    {
        return $this->_getAcl()->get($resource);
    }

    /**
     * Returns true if and only if the Resource exists in the ACL
     *
     * The $resource parameter can either be a Resource or a Resource identifier.
     *
     * @param  Zend_Acl_Resource_Interface|string $resource
     * @return boolean
     */
    public function hasResource($resource)
    {
        return $this->_getAcl()->has($resource);
    }

    /**
     * Returns true if and only if $resource inherits from $inherit
     *
     * Both parameters may be either a Resource or a Resource identifier. If
     * $onlyParent is true, then $resource must inherit directly from
     * $inherit in order to return true. By default, this method looks
     * through the entire inheritance tree to determine whether $resource
     * inherits from $inherit through its ancestor Resources.
     *
     * @param  Zend_Acl_Resource_Interface|string $resource
     * @param  Zend_Acl_Resource_Interface|string $inherit
     * @param  boolean                            $onlyParent
     * @throws Zend_Acl_Resource_Registry_Exception
     * @return boolean
     */
    public function inheritsResource($resource, $inherit, $onlyParent = false)
    {
        return $this->_getAcl()->inherits($resource, $inherit, $onlyParent);
    }

    /**
     * Removes a Resource and all of its children
     *
     * The $resource parameter can either be a Resource or a Resource identifier.
     *
     * @param  Zend_Acl_Resource_Interface|string $resource
     * @throws Zend_Acl_Exception
     * @return Mn_Acl Provides a fluent interface
     */
    public function removeResource($resource)
    {
        $acl = $this->_getAcl();
        $acl->remove($resource);
        $this->_setAcl($acl);

        return $this;
    }

    /**
     * Removes all Resources
     *
     * @return Mn_Acl Provides a fluent interface
     */
    public function removeResourceAll()
    {
        $acl = $this->_getAcl();
        $acl->removeAll();
        $this->_setAcl($acl);

        return $this;
    }
    
    /**
     * Adds an "allow" rule to the ACL
     *
     * @param  Zend_Acl_Role_Interface|string|array     $roles
     * @param  Zend_Acl_Resource_Interface|string|array $resources
     * @param  string|array                             $privileges
     * @param  Zend_Acl_Assert_Interface                $assert
     * @uses   Zend_Acl::setRule()
     * @return Mn_Acl Provides a fluent interface
     */
    public function allow($roles = null, $resources = null, $privileges = null, Zend_Acl_Assert_Interface $assert = null)
    {
        $acl = $this->_getAcl();
        $acl->allow($roles, $resources, $privileges, $assert);
        $this->_setAcl($acl);

        return $this;
    }

    /**
     * Adds a "deny" rule to the ACL
     *
     * @param  Zend_Acl_Role_Interface|string|array     $roles
     * @param  Zend_Acl_Resource_Interface|string|array $resources
     * @param  string|array                             $privileges
     * @param  Zend_Acl_Assert_Interface                $assert
     * @uses   Zend_Acl::setRule()
     * @return Mn_Acl Provides a fluent interface
     */
    public function deny($roles = null, $resources = null, $privileges = null, Zend_Acl_Assert_Interface $assert = null)
    {
        $acl = $this->_getAcl();
        $acl->deny($roles, $resources, $privileges, $assert);
        $this->_setAcl($acl);

        return $this;
    }

    /**
     * Removes "allow" permissions from the ACL
     *
     * @param  Zend_Acl_Role_Interface|string|array     $roles
     * @param  Zend_Acl_Resource_Interface|string|array $resources
     * @param  string|array                             $privileges
     * @uses   Zend_Acl::setRule()
     * @return Mn_Acl Provides a fluent interface
     */
    public function removeAllow($roles = null, $resources = null, $privileges = null)
    {
        $acl = $this->_getAcl();
        $acl->removeAllow($roles, $resources, $privileges);
        $this->_setAcl($acl);

        return $this;
    }

    /**
     * Removes "deny" restrictions from the ACL
     *
     * @param  Zend_Acl_Role_Interface|string|array     $roles
     * @param  Zend_Acl_Resource_Interface|string|array $resources
     * @param  string|array                             $privileges
     * @uses   Zend_Acl::setRule()
     * @return Mn_Acl Provides a fluent interface
     */
    public function removeDeny($roles = null, $resources = null, $privileges = null)
    {
        $acl = $this->_getAcl();
        $acl->removeDeny($roles, $resources, $privileges);
        $this->_setAcl($acl);

        return $this;
    }
    
    /**
     * Performs operations on ACL rules
     *
     * The $operation parameter may be either OP_ADD or OP_REMOVE, depending on whether the
     * user wants to add or remove a rule, respectively:
     *
     * OP_ADD specifics:
     *
     *      A rule is added that would allow one or more Roles access to [certain $privileges
     *      upon] the specified Resource(s).
     *
     * OP_REMOVE specifics:
     *
     *      The rule is removed only in the context of the given Roles, Resources, and privileges.
     *      Existing rules to which the remove operation does not apply would remain in the
     *      ACL.
     *
     * The $type parameter may be either TYPE_ALLOW or TYPE_DENY, depending on whether the
     * rule is intended to allow or deny permission, respectively.
     *
     * The $roles and $resources parameters may be references to, or the string identifiers for,
     * existing Resources/Roles, or they may be passed as arrays of these - mixing string identifiers
     * and objects is ok - to indicate the Resources and Roles to which the rule applies. If either
     * $roles or $resources is null, then the rule applies to all Roles or all Resources, respectively.
     * Both may be null in order to work with the default rule of the ACL.
     *
     * The $privileges parameter may be used to further specify that the rule applies only
     * to certain privileges upon the Resource(s) in question. This may be specified to be a single
     * privilege with a string, and multiple privileges may be specified as an array of strings.
     *
     * If $assert is provided, then its assert() method must return true in order for
     * the rule to apply. If $assert is provided with $roles, $resources, and $privileges all
     * equal to null, then a rule having a type of:
     *
     *      TYPE_ALLOW will imply a type of TYPE_DENY, and
     *
     *      TYPE_DENY will imply a type of TYPE_ALLOW
     *
     * when the rule's assertion fails. This is because the ACL needs to provide expected
     * behavior when an assertion upon the default ACL rule fails.
     *
     * @param  string                                   $operation
     * @param  string                                   $type
     * @param  Zend_Acl_Role_Interface|string|array     $roles
     * @param  Zend_Acl_Resource_Interface|string|array $resources
     * @param  string|array                             $privileges
     * @param  Zend_Acl_Assert_Interface                $assert
     * @throws Zend_Acl_Exception
     * @uses   Zend_Acl_Role_Registry::get()
     * @uses   Zend_Acl::get()
     * @return Mn_Acl Provides a fluent interface
     */
    public function setRule($operation, $type, $roles = null, $resources = null, $privileges = null,
                            Zend_Acl_Assert_Interface $assert = null)
    {
        $acl = $this->_getAcl();
        $acl->setRule($operation, $type, $roles, $resources, $privileges, $assert);
        $this->_setAcl($acl);

        return $this;
    }

    /**
     * Returns true if and only if the Role has access to the Resource
     *
     * The $role and $resource parameters may be references to, or the string identifiers for,
     * an existing Resource and Role combination.
     *
     * If either $role or $resource is null, then the query applies to all Roles or all Resources,
     * respectively. Both may be null to query whether the ACL has a "blacklist" rule
     * (allow everything to all). By default, Zend_Acl creates a "whitelist" rule (deny
     * everything to all), and this method would return false unless this default has
     * been overridden (i.e., by executing $acl->allow()).
     *
     * If a $privilege is not provided, then this method returns false if and only if the
     * Role is denied access to at least one privilege upon the Resource. In other words, this
     * method returns true if and only if the Role is allowed all privileges on the Resource.
     *
     * This method checks Role inheritance using a depth-first traversal of the Role registry.
     * The highest priority parent (i.e., the parent most recently added) is checked first,
     * and its respective parents are checked similarly before the lower-priority parents of
     * the Role are checked.
     *
     * @param  Zend_Acl_Role_Interface|array|string     $role
     * @param  Zend_Acl_Resource_Interface|string $resource
     * @param  string                             $privilege
     * @uses   Zend_Acl::get()
     * @uses   Zend_Acl_Role_Registry::get()
     * @return boolean
     */
    public function isAllowed($roles = null, $resource = null, $privilege = null)
    {
        if(is_array($roles)){
            try {
                foreach($roles as $role)
                {
                    if($this->_getAcl()->isAllowed($role, $resource, $privilege))
                    {
                        return true;
                    }
                }
            }
            catch(Zend_Acl_Exception $e)
            {
                //TODO: log acl exception
            }
            
            return false;
        }
        
        return $this->_getAcl()->isAllowed($roles, $resource, $privilege);
    }
    
    /**
     * @return array of registered roles
     */
    public function getRoles()
    {
        return $this->_getAcl()->getRoles();
    }

    /**
     * @return array of registered resources
     */
    public function getResources()
    {
        return $this->_getAcl()->getResources();
    }
}