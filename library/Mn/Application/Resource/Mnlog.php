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
require_once 'Zend/Application/Resource/Log.php';

/**
 * Resource for setting up Mn Log
 *
 * @uses       Zend_Application_Resource_Log
 * @category   Mn
 * @package    Mn_Application
 * @subpackage Resource
 * @copyright  Copyright (c) 2004-2010 Mimesis Republic
 */
class Mn_Application_Resource_Mnlog
extends Zend_Application_Resource_Log
{
    const DEFAULT_REGISTRY_KEY = 'Mn_Log';
    /**
     * Defined by Zend_Application_Resource_Resource
     *
     * @return Zend_Log
     */
    public function init()
    {
        $options = $this->getOptions();
        $key = (isset($options['registry_key']) && !is_numeric($options['registry_key']))
        ? $options['registry_key']
        : self::DEFAULT_REGISTRY_KEY;
        unset($options['registry_key']);
        
        $log = $this->getLog();

        Zend_Registry::set($key, $log);

        return $log;
    }
}
