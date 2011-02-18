<?php
/**
 * Mn Library
 *
 *
 * @category   Mn
 * @package    Mn_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2004-2010 Mimesis Republic
 * @version    $Id: Facebook.php 21015 2010-02-11 01:56:02Z freak $
 */

/**
 * @see Zend_Application_Resource_ResourceAbstract
 */
require_once 'Zend/Application/Resource/Multidb.php';

/**
 * Resource for setting up Mn Multidb
 *
 * @uses       Zend_Application_Resource_Multidb
 * @category   Mn
 * @package    Mn_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2004-2010 Mimesis Republic
 */
class Mn_Application_Resource_Mnmultidb
extends Zend_Application_Resource_Multidb
{
    const DEFAULT_REGISTRY_KEY = 'Mn_Multidb';
    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return Zn_Application_Resource_Mnmultidb
     */
    public function init()
    {
        parent::init();
        //set in registry
        Zend_Registry::set($this->_registryKey, $this);

        return $this;
    }
}
